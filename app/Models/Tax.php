<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tax extends Model
{
    protected $primaryKey = 'tax_id';
    protected $fillable = ['tax_status'];
}
