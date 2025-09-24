<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalaryTemplate extends Model
{
    use HasFactory;

    // اسم الجدول
    protected $table = 'salary_template';

    // الأعمدة القابلة للتعبئة
    protected $fillable = [
        'salary_item_id',
        'name',
        'description',
        'status',
        'receiving_cycle',
        'amount',
    ];

    /**
     * العلاقة مع جدول salary_items
     */
    public function salaryItem()
    {
        return $this->hasMany(SalaryItem::class, 'salary_template_id');
    }
}
