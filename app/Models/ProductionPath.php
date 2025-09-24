<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionPath extends Model
{
    protected $fillable = ['name', 'code','created_by', 'updated_by'];

    public function stages()
    {
        return $this->hasMany(ProductionStage::class, 'production_paths_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
