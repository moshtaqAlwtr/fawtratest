<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class ChartOfAccount extends Model
{


    protected $fillable = [
        'name',
        'type',
        'code',
        'operation',
        'parent_id',
        'level',
        'normal_balance',
    ];

    protected $casts = [
        'level' => 'integer',
    ];

    public function parentAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_id');
    }

    public function childAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_id');
    }

    public function allDescendants()
    {
        return $this->childAccounts()->with('allDescendants');
    }
}
