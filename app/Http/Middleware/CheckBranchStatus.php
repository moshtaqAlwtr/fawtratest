<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
class CheckBranchStatus
{
    public function handle(Request $request, Closure $next)
    {
        // التحقق مما إذا كان المستخدم مسجل دخول
        if (Auth::check()) {
            $user = Auth::user();

            // التحقق مما إذا كان لديه فرع مرتبط وإذا كان الفرع غير نشط
            if ($user->branch && $user->branch->status == 1) {
                return response()->view('Branches.branch_inactive'); // عرض صفحة الفرع غير النشط
            }
        }

        return $next($request);
    }
}
