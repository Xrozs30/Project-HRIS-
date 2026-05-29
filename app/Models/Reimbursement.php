<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reimbursement extends Model
{
    use HasFactory;

    protected $table = 'reimburse';
    protected $primaryKey = 'reimburse_id';

    const CREATED_AT = 'reimburse_create_at';
    const UPDATED_AT = 'reimburse_update_at';

    protected $fillable = [
        'employee_id',
        'reimburse_date',
        'reimburse_total',
        'reimburse_description',
        'reimburse_proof',
        'reimburse_status',
        'reimburse_notes',
    ];


    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
