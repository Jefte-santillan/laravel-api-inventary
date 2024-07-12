<?php

namespace App\Flow\Logics\ModelQuery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Container\BindingResolutionException;
use App\Kernel\Logics\LogicBusinessTrait;

class IndexLogic
{
    use LogicBusinessTrait;

    public function run(array $input): array | bool
    {
        $queryBuilder = $this->makeQueryBuilder($input['model']);
        if (!$queryBuilder) {
            return false;
        }
        $queryBuilder = $this->applyJoin($queryBuilder, $input);
        $queryBuilder = $this->applyQuery($queryBuilder, $input);
        $queryBuilder = $this->applyFilter($queryBuilder, $input);
        $queryBuilder = $this->applySort($queryBuilder, $input);
        $queryBuilder = $this->applySelect($queryBuilder, $input);
        $queryBuilder = $this->applyScope($queryBuilder, $input);
        $result = $this->runPagination($queryBuilder, $input);
        if (!$result) {
            return false;
        }

        return $this->makeResult($result);
    }

    public function makeResult(&$result): array
    {
        if (!method_exists($result, 'total')) {
            return [
                'total' => $result->count(),
                'data' => $result->toArray()
            ];
        }
        if ($result->total() === 0) {
            return [
                'total' => 0,
                'data' => []
            ];
        }
        return [
            'total' => $result->total(),
            'data' => $result->toArray()['data']
        ];
    }

    public function applyQuery(&$query, &$input): Builder | Model
    {
        if (!isset($input['query']) && !isset($input['query']['property']) && !isset($input['query']['value'])) {
            return $query;
        }
        $property = $input['query']['property'];
        $value = $input['query']['value'];
        $operator = $input['query']['operator'] ?? 'like';

        return $query->where($property, $operator, $value);
    }

    public function applySelect(&$query, $selects): Builder | Model
    {
        if (!isset($selects['select'])) {
            return $query;
        }
        $resultSelects = [];
        foreach ($selects['select'] as $select) {
            $resultSelects[] = DB::raw($select);
        }
        return $query->select($resultSelects);
    }

    public function applyScope(&$query, $input): Builder | Model
    {
        if (!isset($input['scope'])) {
            return $query;
        }
        return $query->{$input['scope']}($input);
    }

    public function applyJoin(&$query, &$joins): Builder | Model
    {
        if (!isset($joins['join'])) {
            return $query;
        }
        foreach ($joins['join'] as $join) {
            if (isset($join['active']) && !$join['active']) {
                continue;
            }
            $operator = '=';
            if (isset($join['operator'])) {
                $operator = $join['operator'];
            }
            if (!isset($join['type'])) {
                $query = $query->join(
                    DB::raw($join['table']),
                    DB::raw($join['foreign_column']),
                    $operator,
                    DB::raw($join['primary_column'])
                );
                continue;
            }
            if ($join['type'] === 'left') {
                $query = $query->leftJoin(
                    DB::raw($join['table']),
                    DB::raw($join['foreign_column']),
                    $operator,
                    DB::raw($join['primary_column'])
                );
            } elseif ($join['type'] === 'right') {
                $query = $query->rightJoin(
                    DB::raw($join['table']),
                    DB::raw($join['foreign_column']),
                    $operator,
                    DB::raw($join['primary_column'])
                );
            } else {
                $query = $query->crossJoin(DB::raw($join['foreign_column']));
            }
        }
        return $query;
    }

    public function applyFilter(&$query, $filters): Builder | Model
    {
        if (!isset($filters['filter'])) {
            return $query;
        }
        if (is_string($filters)) {
            $filters = json_decode($filters, true);
        }
        foreach ($filters['filter'] as $filter) {
            $operator = '=';

            $value = $filter['value'];

            if (isset($filter['active']) && !$filter['active']) {
                continue;
            }
            if (isset($filter['operator'])) {
                $operator = $filter['operator'];
            }
            if ($operator === 'like') {
                $value = '%' . $value . '%';
            } elseif ($operator === 'likeLeft') {
                $value = '%' . $value;
            } elseif ($operator === 'likeRight') {
                $value = $value . '%';
            }
            $type = isset($filter['type']) ? $filter['type'] : 'where';
            if (in_array($type, [
                'whereIn',
            ])) {
                $query = $query->{$type}($filter['property'], $value);
            } elseif (in_array($type, [
                'whereNotIn',
                'orWhereIn',
                'orWhereNotIn',
                'whereDate',
                'whereMonth',
                'whereDay',
                'whereYear',
                'whereTime',
                'orWhere',
                'whereBetween',
                'orWhereBetween',
                'whereNotBetween',
                'orWhereNotBetween',
                'whereRaw',
                'orWhereRaw'
            ])) {
                $query = $query->{$type}($filter['property'], $operator, $value);
            } elseif (in_array($type, [
                'whereNull',
                'whereNotNull',
                'orWhereNull',
                'orWhereNotNull'
            ])) {
                $query = $query->{$type}($filter['property']);
            } else {
                $query = $query->{$type}(
                    $filter['property'],
                    $operator,
                    $value
                );
            }
        }
        return $query;
    }

    public function applySort(&$query, $sortFields): Builder | Model
    {
        if (!isset($sortFields['sort'])) {
            return $query;
        }

        foreach ($sortFields['sort'] as $sort) {

            if (isset($sort['active']) && !$sort['active']) {
                continue;
            }
            $direction = 'asc';
            if (isset($sort['direction'])) {
                $direction = $sort['direction'];
            }

            $property = $sort['property'];
            if (isset($sort['as']) && !empty($sort['as'])) {
                $property = $sort['as'];
            }
            $query = $query->orderBy($property, $direction);
        }
        return $query;
    }

    public function runPagination(&$query, $input): LengthAwarePaginator | bool
    {
        //dd($input);
        try {
            $page = isset($input['page']) ? (int)$input['page'] : 1;
            $limit = isset($input['limit']) ? (int)$input['limit'] : 15;
            return $query->paginate($limit, ['*'], 'page', $page);
        } catch (\Throwable $ex) {
            return $this->errorCode('flow.modelQuery.index.queryFail', [
                'message' => $ex->getMessage()
            ]);
        }
    }

    public function makeQueryBuilder($nameClass)
    {
        try {
            return app($nameClass);
        } catch (BindingResolutionException $ex) {
            return $this->errorCode('flow.modelQuery.index.makeModel', [
                'message' => $ex->getMessage()
            ]);
        }
    }
}
