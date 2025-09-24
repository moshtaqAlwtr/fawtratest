<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'client_id',
        'phone',
        'password',
        'role',
        'branch_id',
        'employee_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Get the region groups for the user.
     */


    /**
     * الحصول على العملاء المستهدفين للموظف
     */


    public function isManager()
    {
        return $this->role === 'manager';
    }
    public function currentBranch()
    {
        return Branch::find($this->branch_id);
    }
    // في نموذج User
public function clientVisits()
{
    return $this->hasMany(EmployeeClientVisit::class, 'employee_id');
}
public function invoices()
{
    return $this->hasMany(Invoice::class, 'created_by');
}
    public function target()
{
    return $this->hasOne(EmployeeTarget::class);
}
public function receipts()
{
    return $this->hasMany(Receipt::class, 'created_by');
}
public function employeeClients()
{
    return $this->hasMany(ClientEmployee::class, 'employee_id');
}

    public function isEmployee()
    {
        return $this->role === 'employee';
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

// داخل App\Models\User
public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}


    public function notifications()
{
    return $this->hasMany(Notification::class);
}
// app/Models/User.php
public function regionGroups()
{
        // The foreign key in the 'employee_group' pivot table should be 'user_id' to link to the 'users' table.
    // This relationship links a User to their Region_groubs via the employee_group pivot table.
    // It correctly specifies that the 'employee_id' column on the 'users' table (parentKey)
    // should be used to join with the 'employee_id' column on the 'employee_group' table (foreignPivotKey).
    return $this->belongsToMany(Region_groub::class, 'employee_group', 'employee_id', 'group_id', 'employee_id')
                ->using(EmployeeGroup::class)
                ->withPivot(['direction_id', 'expires_at']);
}
public function locations()
{
    return $this->hasMany(Location::class, 'employee_id');
}
public function job_role()
{
    return $this->hasMany(JobRole::class);
}

}