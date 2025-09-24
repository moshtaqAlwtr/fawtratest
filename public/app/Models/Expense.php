<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'expenses';

    // الحقول القابلة للتعبئة
    protected $fillable = ['id', 'code', 'amount', 'description', 'date', 'unit_id','created_by', 'expenses_category_id',
        'supplier_id', 'seller', 'treasury_id', 'account_id','is_recurring', 'recurring_frequency', 'end_date',
        'tax1', 'tax2', 'tax1_amount','treasury_id', 'tax2_amount', 'attachments', 'cost_centers_enabled', 'created_at', 'updated_at'];

    // العلاقات
    public function expenses_category()
    {
        return $this->belongsTo(ExpensesCategory::class, 'expenses_category_id');
    }
    public static function generateCode()
    {
        // جلب آخر كود مستخدم
        $lastCode = self::orderBy('id', 'desc')->value('code');

        // إذا لم يكن هناك كود، نبدأ من 1
        if (!$lastCode) {
            return 'EXP-0001';
        }

        // استخراج الرقم من الكود
        $number = intval(substr($lastCode, 4)) + 1;

        // إضافة الأصفار لجعل الرقم مكون من 4 خانات
        $newCode = 'EXP-' . str_pad($number, 4, '0', STR_PAD_LEFT);

        return $newCode;
    }

    public function treasury()
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    // public function employee(){
    //     return $this->belongsTo(Employee::class, 'employee_id');
    // }
    public function branch(){
        return $this->belongsTo(Branch::class, 'branch_id');
    }
    public function client(){
        return $this->belongsTo(Client::class, 'client_id');
    }
    public function account(){
        return $this->belongsTo(Account::class, 'account_id');
    }
    public function Supplier(){
        return $this->belongsTo(Supplier::class,'supplier_id');
    }
public function createdBy(){
    return $this->belongsTo(User::class, 'created_by');
}
}
