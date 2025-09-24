<?php

namespace Modules\TaskManager\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:2000|min:3',
                'commentable_type' => 'required|string',
                'commentable_id' => 'required|integer',
                'parent_id' => 'nullable|exists:comments,id'
            ], [
                'content.required' => 'محتوى التعليق مطلوب',
                'content.min' => 'التعليق يجب أن يكون على الأقل 3 أحرف',
                'content.max' => 'التعليق لا يمكن أن يتجاوز 2000 حرف',
                'commentable_type.required' => 'نوع العنصر المعلق عليه مطلوب',
                'commentable_id.required' => 'معرف العنصر المعلق عليه مطلوب',
                'parent_id.exists' => 'التعليق الأصلي غير موجود'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات المدخلة غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            // التحقق من صحة نوع العنصر المعلق عليه
            $allowedTypes = [
                'App\\Models\\Project',
                'App\\Models\\Task',
                'App\\Models\\Post' // إضافة أنواع جديدة حسب الحاجة
            ];

            if (!in_array($request->commentable_type, $allowedTypes)) {
                return response()->json([
                    'success' => false,
                    'message' => 'نوع العنصر المعلق عليه غير مدعوم'
                ], 400);
            }

            // التحقق من وجود العنصر المعلق عليه
            $commentableClass = $request->commentable_type;
            $commentable = $commentableClass::find($request->commentable_id);

            if (!$commentable) {
                return response()->json([
                    'success' => false,
                    'message' => 'العنصر المعلق عليه غير موجود'
                ], 404);
            }

            // التحقق من صحة التعليق الأصلي إذا كان موجوداً
            if ($request->parent_id) {
                $parentComment = Comment::where('id', $request->parent_id)
                    ->where('commentable_type', $request->commentable_type)
                    ->where('commentable_id', $request->commentable_id)
                    ->first();

                if (!$parentComment) {
                    return response()->json([
                        'success' => false,
                        'message' => 'التعليق الأصلي غير صحيح'
                    ], 400);
                }
            }

            // تنظيف محتوى التعليق
            $cleanContent = strip_tags(trim($request->content));

            // إنشاء التعليق
            $comment = Comment::create([
                'user_id' => Auth::id(),
                'commentable_type' => $request->commentable_type,
                'commentable_id' => $request->commentable_id,
                'parent_id' => $request->parent_id,
                'content' => $cleanContent,
                'is_approved' => true // أو false إذا كنت تريد مراجعة التعليقات
            ]);

            // تحميل بيانات المستخدم والردود
            $comment->load([
                'user:id,name,email',
                'replies.user:id,name,email'
            ]);

            // إضافة تاريخ مقروء
            $comment->created_at_human = $comment->created_at->diffForHumans();

            return response()->json([
                'success' => true,
                'message' => 'تم إضافة التعليق بنجاح',
                'data' => $comment
            ], 201);

        } catch (\Exception $e) {
            Log::error('Error creating comment: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'user_id' => Auth::id(),
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء إضافة التعليق، يرجى المحاولة مرة أخرى'
            ], 500);
        }
    }

    /**
     * جلب التعليقات لعنصر معين
     */
    /**
 * جلب التعليقات لعنصر معين
 */
   public function getComments($type, $id)
    {
        try {
            $allowedTypes = [
                'project' => 'App\\Models\\Project',
                'task'    => 'App\\Models\\Task',
                'post'    => 'App\\Models\\Post'
            ];

            if (!isset($allowedTypes[$type])) {
                return response()->json([
                    'success' => false,
                    'message' => 'نوع العنصر غير مدعوم'
                ], 400);
            }

            // التحقق من وجود العنصر
            $modelClass = $allowedTypes[$type];
            $item = $modelClass::find($id);

            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'العنصر غير موجود'
                ], 404);
            }

            $comments = Comment::where('commentable_type', $allowedTypes[$type])
                ->where('commentable_id', $id)
                ->whereNull('parent_id') // التعليقات الرئيسية فقط
                ->with([
                    'user:id,name,email,name',
                    'replies.user:id,name,email,name',
                    'replies' => function ($query) {
                        $query->orderBy('created_at', 'asc');
                    }
                ])
                ->withCount('replies')
                ->orderBy('created_at', 'desc')
                ->get();

            // إضافة التاريخ المقروء وتحسين بيانات المستخدم
            $comments->each(function ($comment) {
                $comment->created_at_human = $comment->created_at->diffForHumans();

                if ($comment->user) {
                    $comment->user->display_name = trim(($comment->user->name ?? '') . ' ' . ($comment->user->last_name ?? ''))
                        ?: ($comment->user->name ?? 'مستخدم');
                }

                $comment->replies->each(function ($reply) {
                    $reply->created_at_human = $reply->created_at->diffForHumans();
                    if ($reply->user) {
                        $reply->user->display_name = trim(($reply->user->name ?? '') . ' ' . ($reply->user->last_name ?? ''))
                            ?: ($reply->user->name ?? 'مستخدم');
                    }
                });
            });

            return response()->json([
                'success' => true,
                'data'    => $comments,
                'total'   => $comments->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching comments: ' . $e->getMessage(), [
                'type'        => $type,
                'id'          => $id,
                'stack_trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في جلب التعليقات'
            ], 500);
        }
    }
    /**
     * حذف تعليق
     */
    public function destroy($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // التحقق من الصلاحية
            if ($comment->user_id !== Auth::id() && !Auth::user()->hasRole('admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك بحذف هذا التعليق'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'success' => true,
                'message' => 'تم حذف التعليق بنجاح'
            ]);

        } catch (\Exception $e) {
            Log::error('Error deleting comment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في حذف التعليق'
            ], 500);
        }
    }

    /**
     * تحديث تعليق
     */
    public function update(Request $request, $id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // التحقق من الصلاحية
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'غير مسموح لك بتعديل هذا التعليق'
                ], 403);
            }

            $validator = Validator::make($request->all(), [
                'content' => 'required|string|max:2000|min:3'
            ], [
                'content.required' => 'محتوى التعليق مطلوب',
                'content.min' => 'التعليق يجب أن يكون على الأقل 3 أحرف',
                'content.max' => 'التعليق لا يمكن أن يتجاوز 2000 حرف'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'البيانات المدخلة غير صحيحة',
                    'errors' => $validator->errors()
                ], 422);
            }

            $comment->update([
                'content' => strip_tags(trim($request->content)),
                'updated_at' => now()
            ]);

            $comment->load('user:id,name,email');
            $comment->created_at_human = $comment->created_at->diffForHumans();

            return response()->json([
                'success' => true,
                'message' => 'تم تحديث التعليق بنجاح',
                'data' => $comment
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating comment: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تحديث التعليق'
            ], 500);
        }
    }

    /**
     * الإعجاب بتعليق
     */
    public function toggleLike($id)
    {
        try {
            $comment = Comment::findOrFail($id);
            $userId = Auth::id();

            // التحقق من وجود إعجاب مسبق
            $existingLike = $comment->likes()->where('user_id', $userId)->first();

            if ($existingLike) {
                // إلغاء الإعجاب
                $comment->likes()->where('user_id', $userId)->delete();
                $liked = false;
            } else {
                // إضافة إعجاب
                $comment->likes()->create(['user_id' => $userId]);
                $liked = true;
            }

            return response()->json([
                'success' => true,
                'liked' => $liked,
                'likes_count' => $comment->likes()->count()
            ]);

        } catch (\Exception $e) {
            Log::error('Error toggling comment like: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ في تسجيل الإعجاب'
            ], 500);
        }
    }
    public function getTaskComments($taskId)
{
    try {
        // التحقق من وجود المهمة والصلاحيات
        $task = Task::findOrFail($taskId);

        // التحقق من صلاحية الوصول للمهمة
        $user = auth()->user();
        if (!$this->canAccessTask($task, $user)) {
            return response()->json([
                'success' => false,
                'message' => 'غير مصرح لك بمشاهدة تعليقات هذه المهمة'
            ], 403);
        }

        // جلب التعليقات الأساسية مع الردود
        $comments = Comment::where('commentable_type', 'App\Models\Task')
            ->where('commentable_id', $taskId)
            ->whereNull('parent_id')
            ->with([
                'user:id,name,email,avatar,display_name',
                'replies' => function($query) {
                    $query->with('user:id,name,email,avatar,display_name')
                          ->orderBy('created_at', 'asc');
                }
            ])
            ->withCount('replies')
            ->orderBy('created_at', 'desc')
            ->get();

        // تنسيق التواريخ للعرض
        $comments->each(function($comment) {
            $comment->created_at_human = $comment->created_at->diffForHumans();

            if ($comment->replies) {
                $comment->replies->each(function($reply) {
                    $reply->created_at_human = $reply->created_at->diffForHumans();
                });
            }
        });

        // حساب العدد الإجمالي للتعليقات (تشمل الردود)
        $totalCount = $comments->count() + $comments->sum('replies_count');

        return response()->json([
            'success' => true,
            'data' => $comments,
            'total' => $totalCount,
            'main_comments' => $comments->count(),
            'replies_count' => $comments->sum('replies_count')
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'success' => false,
            'message' => 'المهمة غير موجودة'
        ], 404);
    } catch (\Exception $e) {
        Log::error('خطأ في جلب تعليقات المهمة: ' . $e->getMessage(), [
            'task_id' => $taskId,
            'user_id' => auth()->id()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'حدث خطأ في تحميل التعليقات'
        ], 500);
    }
}

/**
 * التحقق من صلاحية الوصول للمهمة
 */
private function canAccessTask($task, $user)
{
    // المنشئ يمكنه الوصول
    if ($task->created_by === $user->id) {
        return true;
    }

    // المستخدمين المكلفين يمكنهم الوصول
    if ($task->assignedUsers->contains($user)) {
        return true;
    }

    // أعضاء المشروع يمكنهم الوصول
    if ($task->project && $task->project->users->contains($user)) {
        return true;
    }

    // الإداريين يمكنهم الوصول
    if ($user->hasRole('admin')) {
        return true;
    }

    return false;
}
}
