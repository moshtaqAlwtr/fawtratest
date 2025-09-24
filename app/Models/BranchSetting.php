<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchSetting extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'key',
    ];

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_setting_branch')
            ->withPivot('status');
    }
}
