<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDetails extends Model
{
    use HasFactory;
    protected $table = 'product_details';
    protected $fillable = ['id', 'quantity', 'unit_price', 'date', 'time', 'type_of_operation', 'comments', 'attachments', 'subaccount', 'product_id', 'type', 'store_house_id', 'purchase_order_id', 'purchase_quotation_id', 'created_at', 'updated_at'];
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }

    public function purchaseQuotation()
    {
        return $this->belongsTo(PurchaseQuotation::class, 'purchase_quotation_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function storeHouse()
    {
        return $this->belongsTo(StoreHouse::class, 'store_house_id');
    }

}
