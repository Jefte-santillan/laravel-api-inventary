<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Kernel\Models\SoftDeletesUnix;

class Operations extends Model
{
    use HasFactory;
    use SoftDeletesUnix;
    protected $fillable = [
        'product_id',
        'type_operation',
        'quantity',
        'number_serie',
        'created_identity_id',
        'operation_type_id',
        'out_identity_id',
        'out',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function getOperation(int $id)
    {
        return $this->where('id', $id)
            ->where('deleted_At', 0)->first();
    }

    public function scopeSearch($query, $input)
    {
        return $query->where('number_serie', 'LIKE', '%' . $input['value'] . '%');
    }

    public function getOperationWidthProduct(int $id)
    {
        $table = $this->getTable();
        return $this->select(
            "$table.*",
            'p.name',
            'i.name as identity_name',
            'io.name as out_identity_name',
            \DB::raw("IF($table.out = 0, 0, FROM_UNIXTIME($table.out)) as date_out"),

        )
            ->leftJoin('products as p', "$table.product_id", 'p.id')
            ->leftJoin('identities as i', "$table.created_identity_id", 'i.id')
            ->leftJoin('identities as io', "$table.out_identity_id", 'io.id')
            ->where("$table.id", $id)
            ->first();
    }
}
