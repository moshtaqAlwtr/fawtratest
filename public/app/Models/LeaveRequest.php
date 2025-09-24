<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    /**
     * الحقول التي يمكن تعبئتها
     *
     * @var array
     */
    protected $fillable = ['employee_id', 'request_type', 'leave_type', 'start_date', 'end_date', 'days', 'description', 'status', 'approved_by', 'approved_at', 'rejection_reason', 'attachments'];

    /**
     * الحقول التي يجب أن تكون من نوع تاريخ
     *
     * @var array
     */
    protected $dates = ['start_date', 'end_date', 'approved_at', 'created_at', 'updated_at'];

    /**
     * العلاقة مع الموظف
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    /**
     * العلاقة مع الموافق على الطلب
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * الحصول على نوع الطلب كسلسلة نصية مقروءة
     *
     * @return string
     */
    public function getRequestTypeTextAttribute()
    {
        return [
            'leave' => 'إجازة',
            'emergency' => 'إجازة طارئة',
            'sick' => 'إجازة مرضية',
        ][$this->request_type] ?? $this->request_type;
    }

    /**
     * الحصول على نوع الإجازة كسلسلة نصية مقروءة
     *
     * @return string
     */
    public function getLeaveTypeTextAttribute()
    {
        return [
            'annual' => 'إجازة اعتيادية',
            'casual' => 'إجازة عرضية',
            'sick' => 'إجازة مرضية',
            'unpaid' => 'إجازة بدون راتب',
        ][$this->leave_type] ?? $this->leave_type;
    }

    /**
     * الحصول على حالة الطلب كسلسلة نصية مقروءة
     *
     * @return string
     */
    public function getStatusTextAttribute()
    {
        return [
            'pending' => 'قيد الانتظار',
            'approved' => 'موافق عليها',
            'rejected' => 'مرفوضة',
        ][$this->status] ?? $this->status;
    }

    /**
     * الحصول على المرفقات كمصفوفة
     *
     * @return array
     */
    public function getAttachmentsArrayAttribute()
    {
        return $this->attachments ? json_decode($this->attachments, true) : [];
    }

    /**
     * نطاق الاستعلام للطلبات المعلقة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * نطاق الاستعلام للطلبات المقبولة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * نطاق الاستعلام للطلبات المرفوضة
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * التحقق مما إذا كان الطلب معلقاً
     *
     * @return bool
     */
    public function isPending()
    {
        return $this->status === 'pending';
    }

    /**
     * التحقق مما إذا كان الطلب مقبولاً
     *
     * @return bool
     */
    public function isApproved()
    {
        return $this->status === 'approved';
    }

    /**
     * التحقق مما إذا كان الطلب مرفوضاً
     *
     * @return bool
     */
    public function isRejected()
    {
        return $this->status === 'rejected';
    }
public function leaveType(){
    return $this->belongsTo(LeaveType::class);
}
}
