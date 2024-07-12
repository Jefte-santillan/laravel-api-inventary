<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Kernel\Models\SoftDeletesUnix;

class Identities extends Model
{
    use HasFactory;
    use SoftDeletesUnix;
    protected $fillable = [
        'name',
        'created_at',
        'updated_at'
    ];
    public $timestamps = false;

    public function getIdentity($name)
    {
        return $this->where('name', $name)->first();
    }

    public function scopeSearch($query, $input)
    {
        return $query->where('name', 'LIKE', '%' . $input['value'] . '%');
    }
}
