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
        return $this->select("$table.*", 'p.name', 'i.name as identity_name')
            ->leftJoin('products as p', "$table.product_id", 'p.id')
            ->leftJoin('identities as i', "$table.created_identity_id", 'i.id')
            ->first($id);
    }
}
