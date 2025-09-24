<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Contract extends Model
{
    protected $fillable = ['employee_id', 'job_title_id', 'job_level_id', 'salary_temp_id', 'code', 'description', 'parent_contract_id', 'start_date', 'end_date', 'type_contract', 'duration_unit', 'duration', 'amount', 'join_date', 'probation_end_date', 'contract_date', 'receiving_cycle', 'currency', 'attachments'];

    protected $dates = ['start_date', 'end_date', 'join_date', 'probation_end_date', 'contract_date'];

    // ثوابت لأنواع العقود
    const TYPE_PERIOD = 1;
    const TYPE_END_DATE = 2;

    // ثوابت لوحدات المدة
    const DURATION_DAY = 1;
    const DURATION_MONTH = 2;
    const DURATION_YEAR = 3;

    // ثوابت لدورة القبض
    const CYCLE_MONTHLY = 1;
    const CYCLE_WEEKLY = 2;
    const CYCLE_YEARLY = 3;
    const CYCLE_QUARTERLY = 4;
    const CYCLE_ONCE_WEEK = 5;

    // العلاقة مع الموظف
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    // العلاقة مع المسمى الوظيفي
    public function jobTitle(): BelongsTo
    {
        return $this->belongsTo(JopTitle::class, 'job_title_id');
    }

    // العلاقة مع المستوى الوظيفي
    public function jobLevel(): BelongsTo
    {
        return $this->belongsTo(FunctionalLevels::class, 'job_level_id');
    }

    // العلاقة مع قالب الراتب
    public function salaryTemplate(): BelongsTo
    {
        return $this->belongsTo(SalaryTemplate::class, 'salary_temp_id');
    }

    // العلاقة مع العقد الأساسي
    public function parentContract(): BelongsTo
    {
        return $this->belongsTo(Contract::class, 'parent_contract_id');
    }

    // العقود الفرعية
    public function childContracts()
    {
        return $this->hasMany(Contract::class, 'parent_contract_id');
    }


public function salaryItems()
{
    return $this->hasMany(SalaryItem::class, 'contracts_id');
}
public function creator()
{
    return $this->belongsTo(User::class, 'created_by');
}
    // Accessors & Mutators
    public function getTypeContractTextAttribute()
    {
        return match ($this->type_contract) {
            self::TYPE_PERIOD => 'مدة محددة',
            self::TYPE_END_DATE => 'تاريخ انتهاء محدد',
            default => 'غير محدد',
        };
    }

    public function getDurationUnitTextAttribute()
    {
        return match ($this->duration_unit) {
            self::DURATION_DAY => 'يوم',
            self::DURATION_MONTH => 'شهر',
            self::DURATION_YEAR => 'سنة',
            default => 'غير محدد',
        };
    }

    public function getReceivingCycleTextAttribute()
    {
        return match ($this->receiving_cycle) {
            self::CYCLE_MONTHLY => 'شهري',
            self::CYCLE_WEEKLY => 'أسبوعي',
            self::CYCLE_YEARLY => 'سنوي',
            self::CYCLE_QUARTERLY => 'ربع سنوي',
            self::CYCLE_ONCE_WEEK => 'مرة في الأسبوع',
            default => 'غير محدد',
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('end_date')->orWhere('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('end_date', '<', now());
    }

    // Methods
    public function isActive(): bool
    {
        return is_null($this->end_date) || $this->end_date >= now();
    }

    public function isExpired(): bool
    {
        return !is_null($this->end_date) && $this->end_date < now();
    }

    public function getDurationText(): string
    {
        if ($this->type_contract === self::TYPE_END_DATE) {
            return 'حتى ' . $this->end_date->format('Y-m-d');
        }

        return $this->duration . ' ' . $this->duration_unit_text;
    }
}
