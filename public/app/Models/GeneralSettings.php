<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralSettings extends Model
{
    use HasFactory;

    protected $table = 'general_settings';
    protected $fillable = ['sub_account', 'storehouse_id', 'price_list_id', 'enable_negative_stock', 'advanced_pricing_options', 'enable_stock_requests', 'enable_sales_stock_authorization', 'enable_purchase_stock_authorization', 'track_products_by_serial_or_batch', 'allow_negative_tracking_elements', 'enable_multi_units_system', 'inventory_quantity_by_date', 'enable_assembly_and_compound_units', 'show_available_quantity_in_warehouse'];

}
