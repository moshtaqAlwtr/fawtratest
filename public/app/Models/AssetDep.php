<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssetDep extends Model
{
    protected $table = 'asset_dep';

    protected $fillable = [
        'asset_id',
        'salvage_value',
        'dep_method',
        'dep_rate',
        'duration',
        'period',
        'unit_name',
        'total_units',
        'acc_dep',
        'book_value',
        'last_dep_date',
        'end_date',
        'dep_account_id',
        'acc_dep_account_id'
    ];

    protected $casts = [
        'salvage_value' => 'decimal:2',
        'dep_rate' => 'decimal:2',
        'acc_dep' => 'decimal:2',
        'book_value' => 'decimal:2',
        'last_dep_date' => 'date',
        'end_date' => 'date',
        'dep_method' => 'integer',
        'period' => 'integer',
        'duration' => 'integer',
        'total_units' => 'integer'
    ];

    // العلاقة مع الأصل
    public function asset()
    {
        return $this->belongsTo(AssetDepreciation::class, 'asset_id');
    }

    // العلاقة مع حساب مصروف الإهلاك
    public function depAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'dep_account_id');
    }

    // العلاقة مع حساب مجمع الإهلاك
    public function accDepAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'acc_dep_account_id');
    }

    // ثوابت لطريقة الإهلاك
    const METHOD_STRAIGHT_LINE = 1;  // القسط الثابت
    const METHOD_DECLINING_BALANCE = 2;  // القسط المتناقص
    const METHOD_UNITS_OF_PRODUCTION = 3;  // وحدات الإنتاج
    const METHOD_NO_DEPRECIATION = 4;  // بدون إهلاك

    // ثوابت للفترات
    const PERIOD_DAILY = 1;    // يومي
    const PERIOD_MONTHLY = 2;  // شهري
    const PERIOD_YEARLY = 3;   // سنوي

    // دالة لحساب القيمة الدفترية
    public function calculateBookValue()
    {
        return $this->asset->purchase_value - $this->acc_dep;
    }

    // دالة لحساب قيمة الإهلاك للفترة
    public function calculateDepreciationAmount()
    {
        switch ($this->dep_method) {
            case self::METHOD_STRAIGHT_LINE:
                return $this->calculateStraightLineDepreciation();
            
            case self::METHOD_DECLINING_BALANCE:
                return $this->calculateDecliningBalanceDepreciation();
            
            case self::METHOD_UNITS_OF_PRODUCTION:
                return $this->calculateUnitsOfProductionDepreciation();
            
            default:
                return 0;
        }
    }

    // حساب القسط الثابت
    private function calculateStraightLineDepreciation()
    {
        $depreciableAmount = $this->asset->purchase_value - $this->salvage_value;
        return $this->dep_rate;
    }

    // حساب القسط المتناقص
    private function calculateDecliningBalanceDepreciation()
    {
        return $this->book_value * ($this->dep_rate / 100);
    }

    // حساب وحدات الإنتاج
    private function calculateUnitsOfProductionDepreciation()
    {
        // يحتاج إلى عدد الوحدات المنتجة في الفترة
        return 0;
    }

    // دالة للتحقق من انتهاء فترة الإهلاك
    public function isDepreciationComplete()
    {
        if ($this->end_date && now()->gte($this->end_date)) {
            return true;
        }

        if ($this->book_value <= $this->salvage_value) {
            return true;
        }

        return false;
    }
}
