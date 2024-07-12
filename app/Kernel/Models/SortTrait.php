<?php

namespace App\Kernel\Models;

/**
 * 
 *
 * @author Luis Josafat Heredia Contreras
 */
trait SortTrait
{
    
    public function applySort($model, array $input = [], array $alias = [])
    {        
        if (!isset($input['sort'])) {            
            return $model;            
        }
        $sorter = json_decode($input['sort']);        
        foreach($sorter as $sort) {
            $property = isset($alias[$sort->property]) ?
                $alias[$sort->property] : $sort->property;
            $model = $model->orderBy($property, $sort->direction);            
        }        
        return $model;        
    }
    
}
