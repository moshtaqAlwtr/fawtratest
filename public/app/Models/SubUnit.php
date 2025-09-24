<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubUnit extends Model
{
    use HasFactory;

    protected $table = 'sub_units';

    protected $fillable = ['larger_unit_name', 'conversion_factor', 'sub_discrimination','template_unit_id'];

    public function template_unit()
    {
        return $this->belongsTo(TemplateUnit::class, 'template_unit_id');
    }
    
}
