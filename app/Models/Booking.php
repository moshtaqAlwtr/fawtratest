<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = ['product_id', 'employee_id', 'appointment_date', 'appointment_time', 'client_id','start_time',
    'end_time','status'];

    // العلاقات
    public function product() {
        return $this->belongsTo(Product::class);
    }

    public function employee() {
        return $this->belongsTo(User::class);
    }

    public function client() {
        return $this->belongsTo(Client::class);
    }
}
