<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Kernel\Models\SoftDeletesUnix;

class ProductStatus extends Model
{
    use HasFactory;
    const STATUS_ACTIVE = 'active';
    public $timestamps = false;
    protected $table = 'product_status';
    protected $fillable = [
        'key',
        'description',
    ];

    public function getByKey(string $key)
    {
        return $this->where('key', $key)->first();
    }
}
