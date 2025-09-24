<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManufacturingGeneralSetting extends Model
{
    protected $table = 'manufacturing_general_settings';
    protected $fillable = ['quantity_exceeded'];
    
}
