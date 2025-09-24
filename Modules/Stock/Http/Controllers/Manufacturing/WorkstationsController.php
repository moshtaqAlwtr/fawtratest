<?php

namespace Modules\Stock\Http\Controllers\Manufacturing;
use App\Http\Controllers\Controller;
use App\Http\Requests\WorkStationRequest;
use App\Models\Account;
use App\Models\JournalEntry;
use App\Models\JournalEntryDetail;
use App\Models\Product;
use App\Models\WorkStations;
use App\Models\Log as ModelsLog;
use App\Models\WorkStationsCost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WorkstationsController extends Controller
{
      public function index()
    {
        return view('stock::manufacturing.workstations.index');
    }

    public function getData(Request $request)
    {
        $query = WorkStations::select();

        // البحث
        if ($request->has('search') && !empty($request->search)) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('code', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // الترتيب
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        // التصفح (Pagination)
        $perPage = $request->get('per_page', 10);
        $workstations = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $workstations->items(),
            'pagination' => [
                'current_page' => $workstations->currentPage(),
                'last_page' => $workstations->lastPage(),
                'per_page' => $workstations->perPage(),
                'total' => $workstations->total(),
                'from' => $workstations->firstItem(),
                'to' => $workstations->lastItem(),
                'links' => $workstations->links()->render()
            ]
        ]);
    }

    public function destroy($id)
    {
        try {
            $workstation = WorkStations::findOrFail($id);
            $workstation->delete();

            ModelsLog::create([
                'type' => 'work_station',
                'type_id' => $workstation->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم   حذف محطة العمل :  **' . $workstation->name . '**', // النص المنسق
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم حذف محطة العمل بنجاح'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء حذف محطة العمل'
            ], 500);
        }
    }
    public function create()
    {
        $record_count = DB::table('work_stations')->count();
        $serial_number = str_pad($record_count + 1, 6, '0', STR_PAD_LEFT);
        $accounts = Account::select('id', 'name')->get();
        return view('stock::manufacturing.workstations.create', compact('serial_number', 'accounts'));
    }
// إضافة هذه الدالة إلى Controller محطات العمل

// إضافة هذه الدالة إلى Controller محطات العمل

private function createAutomaticDepreciationEntries($workStation)
{
    DB::statement('SET FOREIGN_KEY_CHECKS = 0;');

    try {
        // ✅ البحث عن الحساب المختار من الأصل
        $originAccount = Account::find($workStation->origin);
        if (!$originAccount) {
            throw new \Exception('حساب الأصل المحدد غير موجود');
        }

        // ✅ البحث عن حساب الخزينة الرئيسية
        $mainTreasuryAccount = Account::where('name', 'like', '%الخزينة الرئيسية%')
            ->orWhere('name', 'like', '%خزينة رئيسية%') // في حال وجود عمود للتمييز
            ->first();

        // إذا لم نجد الخزينة الرئيسية، ننشئها
        if (!$mainTreasuryAccount) {
            $mainTreasuryAccount = Account::create([
                'name' => 'الخزينة الرئيسية',

                'balance' => 0,
                'status' => 1,

                'created_by' => auth()->id(),
            ]);
        }

        // ✅ إنشاء قيد الإهلاك التلقائي
        $journalEntry = JournalEntry::create([
            'reference_number' => 'DEPREC_WS_' . $workStation->code . '_' . now()->timestamp,
            'work_station_id' => $workStation->id, // ربط بمحطة العمل
            'date' => now(),
            'description' => 'قيد إهلاك تلقائي لمحطة العمل - ' . $workStation->name,
            'status' => 1,
            'currency' => 'SAR',
            'created_by_employee' => auth()->id(),
        ]);

        // 1. مدين: الخزينة الرئيسية (استلام القيمة)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $mainTreasuryAccount->id,
            'description' => 'إهلاك محطة العمل ' . $workStation->name . ' - استلام في الخزينة',
            'debit' => $workStation->cost_origin,
            'credit' => 0,
            'is_debit' => true,
        ]);

        // 2. دائن: حساب الأصل المحدد (خروج القيمة)
        JournalEntryDetail::create([
            'journal_entry_id' => $journalEntry->id,
            'account_id' => $originAccount->id,
            'description' => 'إهلاك محطة العمل ' . $workStation->name . ' - خروج من الأصل',
            'debit' => 0,
            'credit' => $workStation->cost_origin,
            'is_debit' => false,
        ]);

        // ✅ تحديث الأرصدة
        $mainTreasuryAccount->balance += $workStation->cost_origin; // دخول للخزينة
        $mainTreasuryAccount->save();

        $originAccount->balance -= $workStation->cost_origin; // خروج من الأصل
        $originAccount->save();

        // ✅ تسجيل لوج للإهلاك التلقائي
        ModelsLog::create([
            'type' => 'work_station_depreciation',
            'type_id' => $workStation->id,
            'type_log' => 'log',
            'description' => 'تم إنشاء قيد إهلاك تلقائي لمحطة العمل **' . $workStation->name . '** بقيمة ' . number_format($workStation->cost_origin, 2) . ' ر.س',
            'created_by' => auth()->id(),
        ]);

        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');

        return true;

    } catch (\Exception $e) {
        DB::statement('SET FOREIGN_KEY_CHECKS = 1;');
        throw $e;
    }
}

// تحديث دالة store لتشمل استدعاء دالة الإهلاك التلقائي
public function store(WorkStationRequest $request)
{
    try {
        DB::beginTransaction();

        $workStation = WorkStations::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'unit' => $request->input('unit'),
            'description' => $request->input('description'),
            'total_cost' => $request->input('total_cost'),
            'cost_wages' => $request->input('cost_wages'),
            'account_wages' => $request->input('account_wages'),
            'cost_origin' => $request->input('cost_origin'),
            'origin' => $request->input('origin'),
            'automatic_depreciation' => $request->has('automatic_depreciation') ? 1 : 0,
            'created_by' => auth()->user()->id,
        ]);

        // إضافة المصروفات
        if ($request->has('cost_expenses') && is_array($request->cost_expenses)) {
            foreach ($request->cost_expenses as $index => $cost) {
                WorkStationsCost::create([
                    'work_station_id' => $workStation->id,
                    'cost_expenses' => $cost,
                    'account_expenses' => $request->account_expenses[$index] ?? null,
                ]);
            }
        }

        // ✅ تنفيذ الإهلاك التلقائي إذا كان مفعلاً ووجد أصل وتكلفة
        if ($workStation->automatic_depreciation == 1 &&
            $workStation->origin &&
            $workStation->cost_origin > 0) {

            $this->createAutomaticDepreciationEntries($workStation);
        }

        // تسجيل لوج إضافة محطة العمل
        ModelsLog::create([
            'type' => 'work_station',
            'type_id' => $workStation->id,
            'type_log' => 'log',
            'description' => 'تم اضافة محطة عمل **' . $workStation->name . '**' .
                           ($workStation->automatic_depreciation ? ' مع تفعيل الإهلاك التلقائي' : ''),
            'created_by' => auth()->id(),
        ]);

        DB::commit();

        $message = 'تم إضافة محطة العمل بنجاح.';
        if ($workStation->automatic_depreciation == 1 && $workStation->cost_origin > 0) {
            $message .= ' وتم إنشاء قيد الإهلاك التلقائي.';
        }

        return redirect()
            ->route('manufacturing.workstations.index')
            ->with(['success' => $message]);

    } catch (\Exception $e) {
        DB::rollBack();

        // لوج الخطأ للمطورين
        Log::error('خطأ في إضافة محطة العمل: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with(['error' => 'حدث خطأ أثناء إضافة محطة العمل. يرجى المحاولة لاحقاً.']);
    }
}
    public function edit($id)
    {
        $workstation = WorkStations::find($id);
        $accounts = Account::select('id', 'name')->get();
        return view('stock::manufacturing.workstations.edit', compact('workstation', 'accounts'));
    }


// إضافة دالة لحذف قيود الإهلاك السابقة
private function deleteExistingDepreciationEntries($workStationId)
{
    try {
        // البحث عن قيود الإهلاك المرتبطة بمحطة العمل
        $existingEntries = JournalEntry::where('work_station_id', $workStationId)
            ->where('reference_number', 'like', 'DEPREC_WS_%')
            ->get();

        foreach ($existingEntries as $entry) {
            // الحصول على تفاصيل القيد لعكس الأرصدة
            $details = JournalEntryDetail::where('journal_entry_id', $entry->id)->get();

            foreach ($details as $detail) {
                $account = Account::find($detail->account_id);
                if ($account) {
                    // عكس العملية السابقة
                    if ($detail->is_debit) {
                        $account->balance -= $detail->debit; // عكس المدين
                    } else {
                        $account->balance += $detail->credit; // عكس الدائن
                    }
                    $account->save();
                }
            }

            // حذف تفاصيل القيد
            JournalEntryDetail::where('journal_entry_id', $entry->id)->delete();

            // حذف القيد نفسه
            $entry->delete();
        }

        return true;

    } catch (\Exception $e) {
        throw new \Exception('خطأ في حذف قيود الإهلاك السابقة: ' . $e->getMessage());
    }
}

// تحديث دالة update
public function update(WorkStationRequest $request, $id)
{
    try {
        DB::beginTransaction();

        $workstation = WorkStations::find($id);

        // حفظ القيم القديمة للمقارنة
        $oldAutomaticDepreciation = $workstation->automatic_depreciation;
        $oldOrigin = $workstation->origin;
        $oldCostOrigin = $workstation->cost_origin;

        $workstation->update([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'unit' => $request->input('unit'),
            'description' => $request->input('description'),
            'total_cost' => $request->input('total_cost'),
            'cost_wages' => $request->input('cost_wages'),
            'account_wages' => $request->input('account_wages'),
            'cost_origin' => $request->input('cost_origin'),
            'origin' => $request->input('origin'),
            'automatic_depreciation' => $request->has('automatic_depreciation') ? 1 : 0,
            'updated_by' => auth()->user()->id,
        ]);

        // حذف وإعادة إنشاء المصروفات
        $workstation->stationsCosts()->delete();
        if ($request->has('cost_expenses') && is_array($request->cost_expenses)) {
            foreach ($request->cost_expenses as $index => $cost) {
                WorkStationsCost::create([
                    'work_station_id' => $workstation->id,
                    'cost_expenses' => $cost,
                    'account_expenses' => $request->account_expenses[$index] ?? null,
                ]);
            }
        }

        // ✅ منطق الإهلاك التلقائي عند التحديث
        $shouldCreateDepreciation = false;
        $depreciationMessage = '';

        // التحقق من الشروط لإنشاء قيد إهلاك جديد
        if ($workstation->automatic_depreciation == 1 &&
            $workstation->origin &&
            $workstation->cost_origin > 0) {

            // الحالة 1: تم تفعيل الإهلاك التلقائي لأول مرة
            if ($oldAutomaticDepreciation == 0) {
                $shouldCreateDepreciation = true;
                $depreciationMessage = 'تم تفعيل الإهلاك التلقائي وإنشاء قيد جديد.';
            }
            // الحالة 2: تغيير في حساب الأصل أو قيمة التكلفة مع وجود إهلاك مفعل
            elseif ($oldOrigin != $workstation->origin || $oldCostOrigin != $workstation->cost_origin) {
                $shouldCreateDepreciation = true;
                $depreciationMessage = 'تم إنشاء قيد إهلاك جديد بسبب تغيير بيانات الأصل أو التكلفة.';
            }
        }

        // تنفيذ الإهلاك التلقائي إذا كان مطلوباً
        if ($shouldCreateDepreciation) {
            $this->createAutomaticDepreciationEntries($workstation);
        }

        DB::commit();

        // إنشاء لوج التعديل
        $logDescription = 'تم تعديل محطة العمل: **' . $workstation->name . '**';
        if ($shouldCreateDepreciation) {
            $logDescription .= ' - ' . $depreciationMessage;
        }

        ModelsLog::create([
            'type' => 'work_station',
            'type_id' => $workstation->id,
            'type_log' => 'log',
            'description' => $logDescription,
            'created_by' => auth()->id(),
        ]);

        // رسالة النجاح
        $successMessage = 'تم تعديل محطة العمل بنجاح.';
        if ($shouldCreateDepreciation) {
            $successMessage .= ' ' . $depreciationMessage;
        }

        return redirect()
            ->route('manufacturing.workstations.index')
            ->with(['success' => $successMessage]);

    } catch (\Exception $e) {
        DB::rollBack();

        // لوج الخطأ للمطورين
        Log::error('خطأ في تعديل محطة العمل: ' . $e->getMessage(), [
            'user_id' => auth()->id(),
            'workstation_id' => $id,
            'request_data' => $request->all(),
            'trace' => $e->getTraceAsString()
        ]);

        return redirect()
            ->back()
            ->with(['error' => 'حدث خطأ أثناء تعديل محطة العمل. يرجى المحاولة لاحقاً.']);
    }
}
// دالة مساعدة لحذف قيود الإهلاك عند حذف محطة العمل


    public function show($id)
    {
        $workstation = WorkStations::find($id);

        $logs = ModelsLog::where('type', 'work_station')
            ->where('type_id', $id)
            ->whereHas('work_station') // التأكد من وجود علاقة مع سند الصرف
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d');
            });
        return view('stock::manufacturing.workstations.show', compact('workstation','logs'));
    }


}
