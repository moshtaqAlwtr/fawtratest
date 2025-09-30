<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientRelation extends Model
{
    use HasFactory;

    protected $fillable = ['client_id', 'status','quotation_id', 'process', 'time', 'date', 'employee_id', 'description', 'location_id','type', 'deposit_count', 'employee_view_status', 'site_type','invoice_id', 'competitor_documents', 'additional_data'];

    protected $casts = [
        'additional_data' => 'array',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function location()
    {
        return $this->hasOne(Location::class, 'client_relation_id');
    }

    public function employee()
    {
        return $this->belongsTo(User::class);
    }

    // دالة للحصول على نص نوع الموقع
    public function getSiteTypeTextAttribute()
    {
        $types = [
            'independent_booth' => 'بسطة مستقلة',
            'grocery' => 'بقالة',
            'supplies' => 'تموينات',
            'markets' => 'أسواق',
            'station' => 'محطة',
        ];

        return $types[$this->site_type] ?? 'غير محدد';
    }
}
