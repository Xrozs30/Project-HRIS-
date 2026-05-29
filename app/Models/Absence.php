<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absence extends Model
{
    use HasFactory;

    protected $fillable = ['employee_id', 'date', 'penalty_amount', 'notes'];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}
