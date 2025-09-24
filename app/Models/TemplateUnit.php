<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemplateUnit extends Model
{
    use HasFactory;

    protected $table = 'template_units';

    protected $fillable = ['base_unit_name', 'discrimination', 'template', 'status'];

    public function sub_units()
    {
        return $this->hasMany(SubUnit::class, 'template_unit_id');
    }
}
