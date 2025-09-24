<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductionStage extends Model
{
    protected $table = 'production_stages';
    protected $fillable = ['production_paths_id', 'stage_name'];

    public function path()
    {
        return $this->belongsTo(ProductionPath::class);
    }
}
