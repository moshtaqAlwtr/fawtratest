<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'year',
        'initial_balance',
        'used_balance',
        'remaining_balance',
        'carried_forward',
        'additional_balance',
        'notes'
    ];

    protected $casts = [
        'year' => 'integer',
        'initial_balance' => 'integer',
        'used_balance' => 'integer',
        'remaining_balance' => 'integer',
        'carried_forward' => 'integer',
        'additional_balance' => 'integer'
    ];

    // العلاقات
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    // Scopes
    public function scopeCurrentYear($query)
    {
        return $query->where('year', now()->year);
    }

    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    public function scopeForLeaveType($query, $leaveTypeId)
    {
        return $query->where('leave_type_id', $leaveTypeId);
    }

    // Helper methods
    public function getTotalAvailableBalance()
    {
        return $this->initial_balance + $this->carried_forward + $this->additional_balance;
    }

    public function getActualRemainingBalance()
    {
        return max(0, $this->getTotalAvailableBalance() - $this->used_balance);
    }

    public function canDeduct($days)
    {
        return $this->getActualRemainingBalance() >= $days;
    }

    public function deductDays($days, $reason = null)
    {
        if (!$this->canDeduct($days)) {
            return false;
        }

        $this->used_balance += $days;
        $this->remaining_balance = $this->getActualRemainingBalance();

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') .
                          now()->format('Y-m-d H:i:s') . ': خصم ' . $days . ' أيام - ' . $reason;
        }

        return $this->save();
    }

    public function addDays($days, $reason = null)
    {
        $this->used_balance = max(0, $this->used_balance - $days);
        $this->remaining_balance = $this->getActualRemainingBalance();

        if ($reason) {
            $this->notes = ($this->notes ? $this->notes . "\n" : '') .
                          now()->format('Y-m-d H:i:s') . ': إضافة ' . $days . ' أيام - ' . $reason;
        }

        return $this->save();
    }
}
