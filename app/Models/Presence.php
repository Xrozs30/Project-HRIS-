<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presence extends Model
{
    use HasFactory;

    protected $table = 'presence';
    protected $primaryKey = 'presence_id';

    const CREATED_AT = 'presence_create_at';
    const UPDATED_AT = 'presence_update_at';

    protected $fillable = [
        'employee_id',
        'presence_date',
        'presence_time_in',
        'presence_time_out',
        'presence_photo_in',
        'presence_photo_out',
        'presence_lat',
        'presence_long',
        'presence_status',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'employee_id');
    }
}
