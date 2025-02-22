<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TypeOperations extends Model
{
    const IN_KEY = 'in';
    const OUT_KEY = 'out';

    protected $fillable = ['key', 'description'];

    public $timestamps = false;

    public function getByKey(string $key)
    {
        return $this->where('key', $key)->first();
    }
}
