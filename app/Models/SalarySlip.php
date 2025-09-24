<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalarySlip extends Model
{
    protected $fillable = ['employee_id', 'slip_date', 'from_date', 'to_date', 'currency', 'total_salary', 'total_deductions', 'net_salary', 'notes', 'attachments'];

    protected $casts = [
        'slip_date' => 'date',
        'from_date' => 'date',
        'to_date' => 'date',
        'total_salary' => 'decimal:2',
        'total_deductions' => 'decimal:2',
        'net_salary' => 'decimal:2',
    ];

    /**
     * علاقة مع الموظف
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * حساب صافي الراتب
     */
    public function calculateNetSalary(): void
    {
        $this->net_salary = $this->total_salary - $this->total_deductions;
    }

    /**
     * تحميل المرفقات
     */
    public function uploadAttachment($file): void
    {
        if ($file) {
            $path = $file->store('salary_slips/attachments', 'public');
            $this->attachments = $path;
            $this->save();
        }
    }

    /**
     * Scope للبحث عن قسائم موظف معين
     */
    public function scopeForEmployee($query, $employeeId)
    {
        return $query->where('employee_id', $employeeId);
    }

    /**
     * Scope للبحث حسب الفترة
     */
    public function scopeForPeriod($query, $fromDate, $toDate)
    {
        return $query->whereBetween('slip_date', [$fromDate, $toDate]);
    }
    public function salaryItem()
    {
        return $this->hasMany(SalaryItem::class, 'salary_slips_id', );
    }
}
