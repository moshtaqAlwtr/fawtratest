<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptCategory extends Model
{
    use HasFactory;
    protected $table = 'receipt_categories';
    protected $fillable = ['id', 'name', 'status', 'description', 'created_at', 'updated_at'];
}
