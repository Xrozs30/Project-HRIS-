<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LeavePermission extends Model
{
    use HasFactory;

    protected $table = 'leave_permissions';
    protected $primaryKey = 'leave_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'leave_create_at';
    const UPDATED_AT = 'leave_update_at';

    protected $fillable = [
        'leave_id',
        'employee_id',
        'leave_type',
        'leave_start_date',
        'leave_end_date',
        'leave_duration',
        'leave_reason',
        'leave_sick_proof',
        'leave_status',
        'leave_approve_by',
        'leave_rejection_reason',
        'leave_approve_at',
    ];

    protected $casts = [
        'leave_start_date' => 'date',
        'leave_end_date' => 'date',
        'leave_approve_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $latest = static::orderBy('leave_id', 'desc')->first();
                if (!$latest) {
                    $model->{$model->getKeyName()} = 'LP01';
                } else {
                    $num = preg_replace("/[^0-9\.]/", '', $latest->leave_id);
                    $model->{$model->getKeyName()} = 'LP' . sprintf('%02d', $num + 1);
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'leave_approve_by', 'employee_id');
    }
}

