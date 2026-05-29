<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $table = 'payrolls';
    protected $primaryKey = 'payroll_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'payroll_create_at';
    const UPDATED_AT = 'payroll_update_at';

    protected $guarded = [];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function transactional()
    {
        return $this->belongsTo(Transactional::class, 'transactional_id', 'transactional_id');
    }
}
