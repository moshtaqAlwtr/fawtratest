<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
   public function index(Request $request)
    {
        if ($request->ajax()) {
            return $this->getLogsData($request);
        }

        return view('Log.index');
    }

    private function getLogsData(Request $request)
    {
        $search = $request->input('search');
        $page = $request->input('page', 1);
        $perPage = 300;

        $query = Log::where('type_log', 'log')
            ->with(['user', 'user.branch'])
            ->when($search, function ($query) use ($search) {
                return $query->where('description', 'like', '%' . $search . '%')
                           ->orWhereHas('user', function($q) use ($search) {
                               $q->where('name', 'like', '%' . $search . '%');
                           });
            })
            ->orderBy('created_at', 'desc');

        $logs = $query->paginate($perPage, ['*'], 'page', $page);

        // تجميع البيانات حسب التاريخ
        $groupedLogs = $logs->getCollection()->filter(function ($log) {
            return !is_null($log) && !is_bool($log);
        })->groupBy(function ($log) {
            return optional($log->created_at)->format('Y-m-d');
        });

        return response()->json([
            'success' => true,
            'data' => $groupedLogs,
            'pagination' => [
                'current_page' => $logs->currentPage(),
                'last_page' => $logs->lastPage(),
                'per_page' => $logs->perPage(),
                'total' => $logs->total(),
                'has_more_pages' => $logs->hasMorePages(),
                'has_previous_pages' => $logs->currentPage() > 1,
            ]
        ]);
    }


}
