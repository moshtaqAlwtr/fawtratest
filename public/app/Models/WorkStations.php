<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkStations extends Model
{
    protected $table = 'work_stations';
    protected $fillable = [
        'name',
        'code',
        'description',
        'total_cost',
        'unit',
        'cost_wages', 'account_wages',
        'cost_origin', 'origin',
        'automatic_depreciation',
        'created_by',
        'updated_by',
    ];

    public function stationsCosts()
    {
        return $this->hasMany(WorkStationsCost::class, 'work_station_id');
    }

    public function accountWages()
    {
        return $this->belongsTo(Account::class, 'account_wages');
    }

    public function accountOrigin()
    {
        return $this->belongsTo(Account::class, 'origin');
    }

    public function createdByUser()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

}
