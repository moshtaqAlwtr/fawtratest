<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BranchSettingBranch extends Model
{
    protected $table = 'branch_setting_branch'; // تحديد اسم الجدول الوسيط

    protected $fillable = ['branch_id', 'branch_setting_id', 'status'];

    public $timestamps = false; // إذا لم يكن هناك created_at و updated_at

    // علاقة مع الفروع
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    // علاقة مع ضبط الفروع
    public function setting()
    {
        return $this->belongsTo(BranchSetting::class, 'branch_setting_id');
    }
}
