<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetDepreciation extends Model
{
    use HasFactory;

    /**
     * الحقول القابلة للتعبئة
     * @var array
     */
    protected $fillable = [
        'code',
        'status',             // كود الأصل
        'name',              // اسم الأصل
        'date_price',        // تاريخ الشراء
        'date_service',      // تاريخ بداية الخدمة
        'account_id',        // معرف الحساب المرتبط
        'place',             // مكان الأصل
        'region_age',        // العمر الانتاجي
        'quantity',          // الكمية
        'description',       // وصف الأصل
        'purchase_value',    // قيمة الشراء
        'currency',          // العملة (1=ريال, 2=دولار)
        'cash_account',      // حساب النقدية
        'tax1',              // الضريبة الأولى
        'tax2',              // الضريبة الثانية
        'employee_id',       // معرف الموظف
        'client_id',         // معرف العميل
        'attachments',       // مسار المرفقات
        'depreciation_method', // طريقة الإهلاك (1=القسط الثابت, 2=القسط المتناقص, 3=وحدات الإنتاج, null=بدون إهلاك)
                    // مسار الصورة
    ];

    /**
     * الحقول التي تعامل كتواريخ
     * @var array
     */
    protected $dates = [
        'date_price',      // تاريخ الشراء
        'date_service',    // تاريخ بدء الخدمة
        'created_at',      // تاريخ الإنشاء
        'updated_at',      // تاريخ التحديث
    ];

    /**
     * علاقة مع جدول الحسابات
     */
    public function account()
    {
        return $this->belongsTo(Account::class,'asset_account');
    }

    /**
     * علاقة مع جدول الموظفين
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * علاقة مع جدول العملاء
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * علاقة مع جدول الإهلاك
     */
    public function depreciation()
    {
        return $this->hasOne(AssetDep::class, 'asset_id');
    }

    /**
     * دالة للحصول على النص العربي لطريقة الإهلاك
     */
    public function getDepreciationMethodTextAttribute()
    {
        $methods = [
            1 => 'طريقة القسط الثابت',
            2 => 'طريقة القسط المتناقص',
            3 => 'وحدات الانتاج',
            4 => 'بدون الاهلاك'
        ];

        return $methods[$this->depreciation_method] ?? '';
    }

    /**
     * دالة للحصول على النص العربي للضريبة
     */
    public function getTaxTextAttribute($value)
    {
        $types = [
            1 => 'القيمة المضافة',
            2 => 'صفرية',
            3 => 'قيمة مضافة'
        ];

        return $types[$value] ?? '';
    }

    /**
     * دالة للحصول على نوع العملة
     */
    public function getCurrencyTextAttribute()
    {
        $currencies = [
            1 => 'ريال',
            2 => 'دولار'
        ];

        return $currencies[$this->currency] ?? '';
    }
}
