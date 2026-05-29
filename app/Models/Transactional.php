<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactional extends Model
{
    use HasFactory;

    protected $table = 'transactional';
    protected $primaryKey = 'transactional_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'transactional_create_at';
    const UPDATED_AT = 'transactional_update_at';

    protected $fillable = [
        'transactional_id',
        'employee_id',
        'transactional_month',
        'transactional_thr',
        'transactional_bonus',
        'transactional_overtime',
        'transactional_bpjs',
        'transactional_total',
    ];

    /**
     * Boot function from Laravel.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                // Generate next TR ID
                $latest = static::orderBy('transactional_id', 'desc')->first();
                if (!$latest) {
                    $model->{$model->getKeyName()} = 'TR01';
                } else {
                    $string = preg_replace("/[^0-9\.]/", '', $latest->transactional_id);
                    $model->{$model->getKeyName()} = 'TR' . sprintf('%02d', $string + 1);
                }
            }
        });
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
