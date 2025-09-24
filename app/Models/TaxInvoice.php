<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaxInvoice extends Model
{
     protected $table = 'taxs_invoice';

    /**
     * الأعمدة التي يمكن تعبئتها (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'invoice_id',
        'type',
        'rate',
        'value',
        'type_invoice',
        'product_id',
    ];
}
