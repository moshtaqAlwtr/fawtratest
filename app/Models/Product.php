<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Product extends Model
{
    use HasFactory;
    protected $table = 'products';

    protected $fillable = ['id', 'name','notify_before_days','expiry_date', 'description', 'category_id', 'serial_number', 'brand', 'supplier_id', 'low_stock_thershold', 'barcode', 'sales_cost_account', 'sales_account', 'available_online', 'featured_product', 'track_inventory', 'inventory_type', 'low_stock_alert', 'Internal_notes', 'tags', 'images', 'status', 'purchase_price', 'sale_price', 'tax1', 'tax2', 'min_sale_price', 'discount', 'discount_type', 'profit_margin', 'type', 'created_by', 'created_at', 'updated_at'];
    public function product_details()
    {
        return $this->hasOne(ProductDetails::class, 'product_id');
    }

    public function totalQuantity()
    {
        return $this->product_details()->sum('quantity');
    }
    public function productDetails(){
return $this->hasMany(ProductDetails::class,'product_id');
    }


    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function price_list()
    {
        return $this->belongsToMany(PriceList::class, 'price_list_items');
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function invoice_items()
    {
        return $this->hasMany(InvoiceItem::class, 'product_id');
    }

    public function totalSold()
    {
        return $this->invoice_items()->sum('quantity');
    }

    public function totalSoldLast28Days()
    {
        return $this->invoice_items()
            ->where('created_at', '>=', Carbon::now()->subDays(28))
            ->sum('quantity');
    }

    public function totalSoldLast7Days()
    {
        return $this->invoice_items()
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->sum('quantity');
    }
    public function supplier()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function warehousePermitsProducts()
    {
        return $this->hasMany(WarehousePermitsProducts::class, 'product_id');
    }

    public function averageCost()
    {
        return $this->warehousePermitsProducts()->avg('unit_price');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }


    public function invoices()
    {
        return $this->hasManyThrough(
            Invoice::class,
            InvoiceItem::class,
            'product_id', // Foreign key on invoice_items table
            'id', // Local key on invoices table
            'id', // Local key on products table
            'invoice_id' // Foreign key on invoice_items table
        );
    }
}
