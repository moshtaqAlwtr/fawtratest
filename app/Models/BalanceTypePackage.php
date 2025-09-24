<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BalanceTypePackage extends Model
{
    use HasFactory;

    protected $table = 'balance_type_package'; // اسم الجدول

    protected $fillable = [
        'balance_type_id',
        'package_id',
        'balance_value',
    ];

    // علاقة مع نموذج BalanceType
    public function balanceType()
    {
        return $this->belongsTo(BalanceType::class, 'balance_type_id');
    }

    // علاقة مع نموذج Package
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_id');
    }
}
