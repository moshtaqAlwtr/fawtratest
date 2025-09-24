<?php

namespace App\Models;

use App\Http\Requests\AttendanceSheetsRequest;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Department;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Employee extends Model
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        # معلومات الموظف
        'first_name',
        'middle_name',
        'nickname',
        'employee_photo',
        'notes',
        'email',
        'employee_type',
        'status',
        'allow_system_access',
        'send_credentials',
        'language',
        'Job_role_id',
        'access_branches_id',
        #معلومات شخصية
        'date_of_birth',
        'gender',
        'nationality_status',
        'country',
        #معلومات تواصل
        'mobile_number',
        'phone_number',
        'personal_email',
        #العنوان الحالي
        'current_address_1',
        'current_address_2',
        'city',
        'region',
        'postal_code',
        #معلومات وظيفة
        'job_title_id',
        'department_id',
        'job_level_id',
        'job_type_id',
        'branch_id',
        'direct_manager_id',
        'hire_date',
        'shift_id',
        'custom_financial_month',
        'custom_financial_day',
        'group_id',
        'created_by',
    ];

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');

    }
 public function user() {
    return $this->hasOne(User::class);
}


    public function job_role()
    {
        return $this->belongsTo(JobRole::class, 'Job_role_id');
    }

    public function job_level()
    {
        return $this->belongsTo(FunctionalLevels::class, 'job_level_id');
    }

    public function job_type()
    {
        return $this->belongsTo(TypesJobs::class, 'job_type_id');
    }

    public function job_title()
    {
        return $this->belongsTo(JopTitle::class, 'job_title_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function direct_manager()
    {
        return $this->belongsTo(Employee::class, 'direct_manager_id');
    }

    /*
     * طريقة للحصول على الاسم الكامل للموظف.
     */
    public function getFullNameAttribute()
    {
        return trim("{$this->first_name} {$this->middle_name} {$this->nickname}");
    }
    public function supplyOrders()
    {
        return $this->hasMany(SupplyOrder::class, 'employee_id');
    }
    public function clients()
    {
        return $this->belongsToMany(Client::class, 'client_employee', 'employee_id', 'client_id')->withTimestamps();
    }

    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Region_groub::class, 'employee_group')
                    ->withPivot('expires_at')
                    ->withTimestamps();
    }
public  function  expenses()
{
    return $this->hasMany(Expense::class);

}
public function shift()
{
    return $this->hasMany(AttendanceSheetsEmployees::class, 'employee_id');
}
public function leaveRequests()
{
    return $this->hasMany(LeaveRequest::class);
}
}
