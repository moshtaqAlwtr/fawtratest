<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'parent_task_id',
        'title',
        'description',
        'status',
        'priority',
        'start_date',
        'due_date',
        'budget',
        'completed_date',
        'estimated_hours',
        'actual_hours',
        'completion_percentage',
        'files',
        'created_by'
    ];

    protected $casts = [
        'start_date' => 'date',
        'due_date' => 'date',
        'completed_date' => 'date',
        'files' => 'array',
    ];

    /**
     * العلاقة مع المشروع
     */
    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * العلاقة مع منشئ المهمة
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }



    /**
     * العلاقة مع التعليقات (Polymorphic)
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * التحقق من انتهاء الموعد المحدد
     */
    public function isOverdue(): bool
    {
        return $this->due_date < now() && $this->status !== 'completed';
    }

    /**
     * إضافة ملف جديد للمهمة
     */
    public function addFile(string $filename, string $extension, string $originalName = null): void
    {
        $files = $this->files ?? [];
        $files[] = [
            'filename' => $filename,
            'extension' => $extension,
            'original_name' => $originalName ?? $filename,
            'uploaded_at' => now()->toISOString()
        ];
        $this->update(['files' => $files]);
    }

    /**
     * حذف ملف من المهمة
     */
    public function removeFile(string $filename): void
    {
        $files = collect($this->files ?? [])
            ->reject(function ($file) use ($filename) {
                return $file['filename'] === $filename;
            })
            ->values()
            ->toArray();

        $this->update(['files' => $files]);
    }

    /**
     * الحصول على جميع الملفات
     */
    public function getFiles(): array
    {
        return $this->files ?? [];
    }

    /**
     * التحقق من وجود ملفات
     */
    public function hasFiles(): bool
    {
        return !empty($this->files);
    }

    /**
     * المهمة الرئيسية
     */
    public function parentTask()
    {
        return $this->belongsTo(Task::class, 'parent_task_id');
    }

    /**
     * المهام الفرعية
     */
    public function subTasks()
    {
        return $this->hasMany(Task::class, 'parent_task_id');
    }

    /**
     * التحقق من وجود مهام فرعية
     */
    public function hasSubTasks()
    {
        return $this->subTasks()->exists();
    }

    /**
     * التحقق من كونها مهمة فرعية
     */
    public function isSubTask()
    {
        return !is_null($this->parent_task_id);
    }

    /**
     * الحصول على جميع المهام الفرعية (متداخلة)
     */
    public function getAllSubTasks()
    {
        $subTasks = collect();

        foreach ($this->subTasks as $subTask) {
            $subTasks->push($subTask);
            $subTasks = $subTasks->merge($subTask->getAllSubTasks());
        }

        return $subTasks;
    }
public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'task_user')
        ->withPivot('assigned_at', 'assigned_by');
}
}
