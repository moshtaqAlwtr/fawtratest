<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditNotification extends Model
{
    use HasFactory;

    // تحديد اسم الجدول المرتبط بهذا المودل
    protected $table = 'credit_notifications';

    // الحقول القابلة للتعبئة
    protected $fillable = ['client_id', 'created_by', 'credit_date', 'release_date', 'credit_number', 'subtotal', 'status', 'total_discount', 'total_tax', 'shipping_cost', 'next_payment', 'grand_total', 'notes', 'discount_type', 'discount_amount', 'tax_type'];

    /**
     * العلاقة مع جدول العملاء (Clients)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * العلاقة مع جدول المستخدمين (Users) للمسؤول الذي أنشأ الاشعار
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * العلاقة مع جدول الموظفين (Employees)
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * دالة للحصول على حالة الاشعار كنص
     * @return string
     */
    public function getStatusTextAttribute()
    {
        $statuses = [
            1 => 'Draft',
            2 => 'Pending',
            3 => 'Approved',
            4 => 'Converted to Invoice',
            5 => 'Cancelled',
        ];

        return $statuses[$this->status] ?? 'Unknown';
    }
    public function items()
{
    return $this->hasMany(InvoiceItem::class, 'credit_note_id');

}
}
