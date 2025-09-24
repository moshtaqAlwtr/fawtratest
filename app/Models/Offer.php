<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Offer extends Model
{
    use SoftDeletes;

    /**
     * الحقول التي يمكن تعبئتها (Mass Assignment).
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'valid_from',
        'valid_to',
        'type',
        'quantity',
        'discount_type',
        'discount_value',
        'category',
        'status',
        'client_id',
        'is_active',
        'unit_type',
        'product_id',
        'category_id',
    ];

    /**
     * الحقول التي يجب إخفاؤها عند التحويل إلى JSON.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    /**
     * الحقول التي يجب تحويلها إلى أنواع بيانات محددة.
     *
     * @var array
     */
    protected $casts = [
        'valid_from' => 'date',
        'valid_to' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * العلاقة مع العميل (Client).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client()
    {
        return $this->belongsTo(Client::class, 'client_id');
    }

    /**
     * العلاقة مع المنتج (Product).
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

   
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

 // في App\Models\Offer
public function clients()
{
    return $this->belongsToMany(Client::class, 'offer_clients');
}

public function categories()
{
    return $this->belongsToMany(Category::class, 'offer_categories');
}

public function products()
{
    return $this->belongsToMany(Product::class, 'offer_products');
}
}
