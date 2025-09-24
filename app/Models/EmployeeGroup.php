<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class EmployeeGroup extends Pivot
{
    protected $table = 'employee_group';

    protected $fillable = [
        'employee_id',
        'group_id',
        'direction_id',
        'expires_at',
    ];

    protected $dates = ['expires_at'];

    // تقدر تضيف دوال خاصة هنا لو حاب
    public function employee()
    {
        return $this->belongsTo(User::class);
    }


}
