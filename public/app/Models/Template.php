<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
      protected $fillable = [
        'name',
        'type',
        'content', // أضف هذا الحقل
        'thumbnail',
        'default_content',
        'is_default'
    ];
}
