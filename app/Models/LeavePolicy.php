<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeavePolicy extends Model
{
    use HasFactory;
    protected $table = 'leave_policies';
    protected $guarded = [];
    public function leaveType()
    {
        return $this->belongsToMany(LeaveType::class, 'leave_type_leave_types');
    }

    public function leavePolicyCustomize()
    {
        return $this->hasOne(LeavePolicyCustomize::class, 'leave_policy_id');
    }

}
