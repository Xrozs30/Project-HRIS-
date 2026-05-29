<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Overtime extends Model
{
    protected $primaryKey = 'overtime_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'overtime_create_at';
    const UPDATED_AT = 'overtime_update_at';

    protected $fillable = [
        'overtime_id',
        'employee_id',
        'overtime_date',
        'overtime_start',
        'overtime_finish',
        'overtime_duration',
        'overtime_status',
        'overtime_description',
        'overtime_approve_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $latest = static::orderBy('overtime_id', 'desc')->first();
                if (!$latest) {
                    $model->{$model->getKeyName()} = 'OT01';
                } else {
                    $num = preg_replace("/[^0-9\.]/", '', $latest->overtime_id);
                    $model->{$model->getKeyName()} = 'OT' . sprintf('%02d', $num + 1);
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }

    public function approver()
    {
        return $this->belongsTo(Employee::class, 'overtime_approve_by', 'employee_id');
    }
}
