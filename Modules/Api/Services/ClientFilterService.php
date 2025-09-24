<?php

namespace Modules\Api\Services;

use Illuminate\Http\Request;
use App\Models\Client;
use App\Models\Branch;
use App\Models\EmployeeClientVisit;

class ClientFilterService
{
    public static function apply(Request $request, $user)
    {
        $query = Client::query();

        $currentDate     = now();
        $currentDay      = ($currentDate->dayOfWeek + 1) % 7;
        $englishDays     = ['Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday'];
        $currentDayName  = $englishDays[$currentDay];
        $startOfYear     = now()->startOfYear();
        $startDay        = ($startOfYear->dayOfWeek + 1) % 7;
        $daysPassed      = $startOfYear->diffInDays($currentDate);
        $week            = (int) ceil(($daysPassed + $startDay + 1) / 7);
        $year            = now()->year;

        if ($user->role === 'employee') {
            $visits = \App\Models\EmployeeClientVisit::where('employee_id', $user->id)
                ->where('day_of_week', $currentDayName)
                ->where('year', $year)
                ->where('week_number', $week)
                ->pluck('client_id');

            if ($visits->isNotEmpty()) {
                $query->whereIn('id', $visits);
            } else {
                return Client::query()->whereRaw('1=0'); // لا يوجد عملاء اليوم
            }
        } elseif ($user->branch_id) {
            $mainBranch    = Branch::where('is_main', true)->value('name');
            $currentBranch = optional(Branch::find($user->branch_id))->name;
            if ($currentBranch && $currentBranch !== $mainBranch) {
                $query->where('branch_id', $user->branch_id);
            }
        }

if ($request->filled('name')) {
    $search = $request->name;

    $query->where(function ($q) use ($search) {
        $q->where('trade_name', 'like', "%{$search}%")
          ->orWhere('phone', 'like', "%{$search}%")
          ->orWhere('mobile', 'like', "%{$search}%")
          ->orWhere('code', 'like', "%{$search}%")
          ->orWhereHas('neighborhood', fn($qq) => $qq->where('name', 'like', "%{$search}%"))
          ->orWhereHas('neighborhood.region', fn($qq) => $qq->where('name', 'like', "%{$search}%"))
          ->orWhereHas('branch', fn($qq) => $qq->where('name', 'like', "%{$search}%"));
    });
}

        if ($request->filled('client'))       $query->where('id', $request->client);
        
        if ($request->filled('status'))       $query->where('status_id', $request->status);
        if ($request->filled('region'))       $query->whereHas('neighborhood.region', fn($q) => $q->where('id', $request->region));
        if ($request->filled('neighborhood')) $query->whereHas('neighborhood', fn($q) => $q
                                                ->where('name', 'like', "%{$request->neighborhood}%")
                                                ->orWhere('id', $request->neighborhood));
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $query->whereBetween('created_at', [$request->date_from.' 00:00:00', $request->date_to.' 23:59:59']);
        } elseif ($request->filled('date_from')) {
            $query->where('created_at', '>=', $request->date_from.' 00:00:00');
        } elseif ($request->filled('date_to')) {
            $query->where('created_at', '<=', $request->date_to.' 23:59:59');
        }

        if ($request->filled('categories')) $query->where('category_id', $request->categories);
        if ($request->filled('user'))       $query->where('created_by', $request->user);
        if ($request->filled('type'))       $query->where('type', $request->type);
        if ($request->filled('employee'))   $query->where('employee_id', $request->employee);

        return $query; // مهم: لا get هنا
    }
}
