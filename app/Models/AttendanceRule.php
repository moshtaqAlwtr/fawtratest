<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRule extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'attendance_rules';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'color',
        'status',
        'shift_id',
        'description',
        'formula',
        'condition',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the shift that owns the attendance rule.
     */
    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    /**
     * Scope a query to only include active rules.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include inactive rules.
     */
    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    /**
     * Get the status label in Arabic.
     */
    public function getStatusLabelAttribute()
    {
        return $this->status === 'active' ? 'نشط' : 'غير نشط';
    }

    /**
     * Check if the rule is active.
     */
    public function isActive()
    {
        return $this->status === 'active';
    }

    /**
     * Check if the rule is inactive.
     */
    public function isInactive()
    {
        return $this->status === 'inactive';
    }
}