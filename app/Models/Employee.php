<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Employee extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'employees';
    protected $primaryKey = 'employee_id';
    public $incrementing = false;
    protected $keyType = 'string';

    const CREATED_AT = 'employee_create_at';
    const UPDATED_AT = 'employee_update_at';

    protected $fillable = [
        'employee_id',
        'employee_name',
        'employee_email',
        'employee_password',
        'employee_role',
        'employee_face_descriptor',
        'employee_nik',
        'employee_address',
        'employee_phone',
        'position_id',
        'tax_id',
        'employee_basic_salary',
        'employee_bank_number',
        'employee_bank_name',
        'employee_bpjs_number',
        'employee_gender',
        'employee_birth_date',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'employee_password',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'employee_password' => 'hashed',
        ];
    }

    public function getAuthPassword()
    {
        return $this->employee_password;
    }

    public function getAuthPasswordName()
    {
        return 'employee_password';
    }

    /**
     * Get the leave requests for the user.
     */
    public function leavePermissions()
    {
        return $this->hasMany(LeavePermission::class, 'employee_id');
    }

    /**
     * Get the overtime requests for the user.
     */
    public function overtimes()
    {
        return $this->hasMany(Overtime::class, 'employee_id');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class, 'employee_id');
    }

    public function reimbursements()
    {
        return $this->hasMany(Reimbursement::class, 'employee_id');
    }

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'employee_id');
    }

    public function transactionals()
    {
        return $this->hasMany(Transactional::class, 'employee_id', 'employee_id');
    }

    public function position()
    {
        return $this->belongsTo(Position::class, 'position_id', 'position_id');
    }

    public function tax()
    {
        return $this->belongsTo(Tax::class, 'tax_id', 'tax_id');
    }

    /**
     * Check if the user is HR.
     */
    public function isHr()
    {
        return $this->employee_role === 'hr';
    }

    /**
     * Check if the user is Owner.
     */
    public function isOwner()
    {
        return $this->employee_role === 'owner';
    }
}
