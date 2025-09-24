<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class SupplyOrder extends Model
{

    protected $fillable = ['name', 'order_number', 'start_date', 'end_date', 'description', 'client_id', 'employee_id', 'product_details', 'shipping_address', 'tracking_number', 'shipping_policy_file', 'tag', 'budget', 'currency', 'custom_fields', 'status','show_employee'];

    protected $casts = [
        'custom_fields' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class,'client_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

}
