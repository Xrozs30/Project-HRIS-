<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allowance extends Model
{
    protected $primaryKey = 'dummy_ignore_id';
    protected $fillable = ['dummy_ignore_type'];

    public function transactionals()
    {
        return $this->hasMany(Transactional::class, 'dummy_ignore_id', 'dummy_ignore_id');
    }
}
