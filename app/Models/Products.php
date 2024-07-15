<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Kernel\Models\SoftDeletesUnix;

class Products extends Model
{
    use HasFactory;
    use SoftDeletesUnix;
    protected $fillable = [
        'name',
        'product_type_id',
        'created_identity_id',
        'quantity',
        'created_at',
        'updated_at',
        'deleted_at',
        'product_status_id',
    ];

    public function getByName($name)
    {
        return $this->where('name', $name)
            ->where('deleted_at', 0)
            ->first();
    }

    public function scopeSearch($query, $input)
    {
        return $query->where('name', 'LIKE', '%' . $input['value'] . '%');
    }
}
