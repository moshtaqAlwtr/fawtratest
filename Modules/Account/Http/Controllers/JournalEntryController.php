<?php

namespace Modules\Account\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\ChartOfAccount;
use App\Models\Client;
use App\Models\Employee;
use App\Models\CostCenter;
use App\Models\JournalEntry;
use App\Models\Log as ModelsLog;
use App\Models\JournalEntryDetail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class JournalEntryController extends Controller
{
   public function index(Request $request)
{
    $query = JournalEntry::with([
        'details.account',
        'client',
        'costCenter',
        'createdByEmployee',
        'account',
    ]);

    // البحث حسب الحساب
    if ($request->filled('account_id')) {
        $query->whereHas('details', function ($q) use ($request) {
            $q->where('account_id', $request->account_id);
        });
    }

    // البحث حسب الوصف
    if ($request->filled('description')) {
        $query->where('description', 'like', '%' . $request->description . '%');
    }

    // البحث حسب التخصيص
    if ($request->filled('date_type')) {
        $query->where('date_type', $request->date_type);
    }

    // البحث حسب الإجمالي الأدنى
    if ($request->filled('total_from')) {
        $query->whereHas('details', function ($q) use ($request) {
            $q->havingRaw('SUM(debit) >= ?', [$request->total_from]);
        });
    }

    // البحث حسب الإجمالي الأعلى
    if ($request->filled('total_to')) {
        $query->whereHas('details', function ($q) use ($request) {
            $q->havingRaw('SUM(debit) <= ?', [$request->total_to]);
        });
    }

    // البحث حسب التاريخ
    if ($request->filled('from_date') && $request->filled('to_date')) {
        $query->whereBetween('date', [$request->from_date, $request->to_date]);
    } elseif ($request->filled('from_date')) {
        $query->where('date', '>=', $request->from_date);
    } elseif ($request->filled('to_date')) {
        $query->where('date', '<=', $request->to_date);
    }

    // البحث حسب أضيفت بواسطة
    if ($request->filled('created_by')) {
        $query->where('created_by_employee', $request->created_by);
    }

    // البحث حسب مركز التكلفة
    if ($request->filled('cost_center')) {
        $query->where('cost_center_id', $request->cost_center);
    }

    // البحث حسب حالة القيد
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // البحث حسب المصدر
    if ($request->filled('source')) {
        $query->where('source', $request->source);
    }

    // تقسيم النتائج إلى صفحات، مع عرض 50 عنصرًا في كل صفحة
    $entries = $query->orderBy('created_at', 'desc')->paginate(50);

    // الحفاظ على معاملات البحث في الروابط
    $entries->appends($request->query());

    $entryDetails = JournalEntryDetail::with('account')->get();
    $users = User::all();
    $costCenters = CostCenter::all();
    $accounts = Account::all();

    return view('account::journal.index', compact('entries', 'entryDetails', 'users', 'costCenters', 'accounts'));
}
    public function create()
    {
        $journalEntry = JournalEntry::all();
        $clients = Client::all();
        $employees = Employee::all();
        $entryDetails = JournalEntryDetail::all();
        $costCenters = CostCenter::all();

        // جلب جميع الحسابات
        $accounts = Account::select('id', 'name', 'code', 'parent_id')->with('parent')->get();

        // بناء شجرة الحسابات
        $sortedAccounts = $this->buildAccountTree($accounts);

        return view('account::journal.create', compact('sortedAccounts', 'accounts', 'clients', 'employees', 'costCenters', 'journalEntry', 'entryDetails'));
    }
    private function buildAccountTree($accounts, $parentId = null, $level = 0)
    {
        $tree = [];

        foreach ($accounts as $account) {
            if ($account->parent_id == $parentId) {
                $account->level = $level;
                $tree[] = $account;
                // إضافة الحسابات الفرعية عبر البحث في الحسابات
                $tree = array_merge($tree, $this->buildAccountTree($accounts, $account->id, $level + 1));
            }
        }

        return $tree;
    }

    public function store(Request $request)
    {
        // $request->validate([
        //     'journal_entry.date' => 'required|date',
        //     'journal_entry.description' => 'required|string|max:500',
        //     'journal_entry.reference_number' => 'nullable|string|max:50',
        //     'journal_entry.client_id' => 'nullable|exists:clients,id',
        //     'journal_entry.cost_center_id' => 'nullable|exists:cost_centers,id',
        //     'journal_entry.currency' => 'nullable|string|max:10',
        //     'journal_entry.attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        //     'details' => 'required|array|min:1',
        //     'details.*.account_id' => 'required',
        //     'details.*.description' => 'nullable|string|max:255',
        //     'details.*.debit' => 'required_without:details',
        //     'details.*.credit' => 'required_without:details',
        //     'details.*.cost_center_id' => 'nullable|exists:cost_centers,id',
        // ]);

        try {
            DB::beginTransaction();

            // التحقق من توازن القيد
            $totalDebit = collect($request->details)->sum('debit');
            $totalCredit = collect($request->details)->sum('credit');

            if ($totalDebit != $totalCredit) {
                return back()
                    ->withErrors(['message' => 'مجموع المدين يجب أن يساوي مجموع الدائن'])
                    ->withInput();
            }

            // إنشاء القيد
            $journalEntry = new JournalEntry();
            $journalEntry->date = $request->input('journal_entry.date');
            $journalEntry->description = $request->input('journal_entry.description');
            $journalEntry->reference_number = $request->input('journal_entry.reference_number');
            $journalEntry->status = 0; // معلق
            $journalEntry->client_id = $request->input('journal_entry.client_id');
            $journalEntry->cost_center_id = $request->input('journal_entry.cost_center_id');
            $journalEntry->currency = $request->input('journal_entry.currency', 'SAR');
            $journalEntry->created_by_employee = auth()->id();

            // معالجة المرفق
            if ($request->hasFile('journal_entry.attachment')) {
                $file = $request->file('journal_entry.attachment');
                if ($file->isValid()) {
                    $filename = time() . '_' . $file->getClientOriginalName();
                    $file->move(public_path('assets/uploads/journal'), $filename);
                    $journalEntry->attachment = $filename;
                }
            }

            $journalEntry->save();

            // إضافة التفاصيل
            foreach ($request->details as $detail) {
                if (!empty($detail['debit']) || !empty($detail['credit'])) {
                    JournalEntryDetail::create([
                        'journal_entry_id' => $journalEntry->id,
                        'account_id' => $detail['account_id'],
                        'description' => $detail['description'] ?? null,
                        'debit' => $detail['debit'] ?? 0,
                        'credit' => $detail['credit'] ?? 0,
                        'cost_center_id' => $detail['cost_center_id'] ?? null,
                    ]);
                }
            }

            ModelsLog::create([
                'type' => 'finance_log',
                'type_id' => $journalEntry->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة قيد جديد  **' . '**',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

            DB::commit();

            return redirect()->route('journal.index')->with('success', 'تم إنشاء القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['message' => 'حدث خطأ أثناء حفظ القيد: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show($id)
    {
        $entry = JournalEntry::with(['details.account'])->findOrFail($id);
        $entryDetails = JournalEntryDetail::with('account')->get();
        return view('account::journal.show', compact('entry', 'entryDetails'));
    }

    public function edit($id)
    {
        $entry = JournalEntry::findOrFail($id);
        $journal = JournalEntry::findOrFail($id);
        $accounts = Account::all();
        $clients = Client::all();
        $employees = Employee::all();
        $costCenters = CostCenter::all();

        return view('account::journal.edit', compact('entry', 'journal', 'accounts', 'clients', 'employees', 'costCenters'));
    }

    public function update(Request $request, JournalEntry $entry)
    {
        if ($entry->status != 0) {
            return back()->with('error', 'لا يمكن تعديل القيد بعد اعتماده أو رفضه');
        }

        $request->validate([
            'journal_entry.date' => 'required|date',
            'journal_entry.description' => 'required|string|max:500',
            'journal_entry.reference_number' => 'nullable|string|max:50',
            'journal_entry.client_id' => 'nullable|exists:clients,id',
            'journal_entry.cost_center_id' => 'nullable|exists:cost_centers,id',
            'journal_entry.currency' => 'nullable|string|max:10',
            'journal_entry.attachment' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'details' => 'required|array|min:1',
            'details.*.account_id' => 'required|exists:chart_of_accounts,id',
            'details.*.description' => 'nullable|string|max:255',
            'details.*.debit' => 'required_without:details.*.credit|numeric|min:0',
            'details.*.credit' => 'required_without:details.*.debit|numeric|min:0',
            'details.*.cost_center_id' => 'nullable|exists:cost_centers,id',
        ]);

        try {
            DB::beginTransaction();

            // التحقق من توازن القيد
            $totalDebit = collect($request->details)->sum('debit');
            $totalCredit = collect($request->details)->sum('credit');

            if ($totalDebit != $totalCredit) {
                return back()
                    ->withErrors(['message' => 'مجموع المدين يجب أن يساوي مجموع الدائن'])
                    ->withInput();
            }

            // تحديث القيد
            $entry->date = $request->input('journal_entry.date');
            $entry->description = $request->input('journal_entry.description');
            $entry->reference_number = $request->input('journal_entry.reference_number');
            $entry->client_id = $request->input('journal_entry.client_id');
            $entry->cost_center_id = $request->input('journal_entry.cost_center_id');
            $entry->currency = $request->input('journal_entry.currency', 'SAR');

            // معالجة المرفق
            $attachmentPath = $entry->attachment;
            if ($request->hasFile('journal_entry.attachment')) {
                // حذف المرفق القديم
                if ($attachmentPath) {
                    Storage::disk('public')->delete($attachmentPath);
                }
                $attachmentPath = $request->file('journal_entry.attachment')->store('journal_entries', 'public');
            }

            $entry->attachment = $attachmentPath;
            $entry->save();

            // حذف التفاصيل القديمة
            $entry->details()->delete();

            // إضافة التفاصيل الجديدة
            foreach ($request->details as $detail) {
                if (!empty($detail['debit']) || !empty($detail['credit'])) {
                    JournalEntryDetail::create([
                        'journal_entry_id' => $entry->id,
                        'account_id' => $detail['account_id'],
                        'description' => $detail['description'] ?? null,
                        'debit' => $detail['debit'] ?? 0,
                        'credit' => $detail['credit'] ?? 0,
                        'cost_center_id' => $detail['cost_center_id'] ?? null,
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('journal.show', $entry->id)->with('success', 'تم تحديث القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['message' => 'حدث خطأ أثناء تحديث القيد: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function approve(JournalEntry $entry)
    {
        if ($entry->status != 0) {
            return back()->with('error', 'لا يمكن اعتماد القيد - الحالة غير معلقة');
        }

        try {
            $entry->update([
                'status' => 1, // معتمد
                'approved_by_employee' => auth()->id(),
            ]);

            return back()->with('success', 'تم اعتماد القيد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء اعتماد القيد: ' . $e->getMessage());
        }
    }

    public function reject(JournalEntry $entry)
    {
        if ($entry->status != 0) {
            return back()->with('error', 'لا يمكن رفض القيد - الحالة غير معلقة');
        }

        try {
            $entry->update([
                'status' => 2, // مرفوض
                'approved_by_employee' => auth()->id(),
            ]);

            return back()->with('success', 'تم رفض القيد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء رفض القيد: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $entry = JournalEntry::findOrFail($id);
            $entry->details()->delete();
            $entry->delete();

            DB::commit();

            return redirect()->route('journal.index')->with('success', 'تم حذف القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'حدث خطاء اثناء حذف القيد: ' . $e->getMessage());
        }
    }

    public function record_modifications(Request $request)
    {
        // نجلب تفاصيل القيود مع القيود المعلقة
        $query = JournalEntryDetail::with(['account', 'journalEntry.client', 'journalEntry.costCenter', 'journalEntry.createdByEmployee'])
            ->whereHas('journalEntry')
            ->orWhereNull('journal_entry_id')
            ->latest();

        // تطبيق الفلاتر
        if ($request->filled('account_id')) {
            $query->where('account_id', $request->account_id);
        }

        if ($request->filled('description')) {
            $query->where(function ($q) use ($request) {
                $q->where('description', 'like', '%' . $request->description . '%')->orWhereHas('journalEntry', function ($q) use ($request) {
                    $q->where('description', 'like', '%' . $request->description . '%');
                });
            });
        }

        if ($request->filled('from_date')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->whereDate('date', '>=', $request->from_date);
            });
        }

        if ($request->filled('to_date')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->whereDate('date', '<=', $request->to_date);
            });
        }

        if ($request->filled('employee_id')) {
            $query->whereHas('journalEntry', function ($q) use ($request) {
                $q->where('created_by_employee', $request->employee_id);
            });
        }

        $entries = $query->paginate(20);
        $accounts = ChartOfAccount::all();
        $employees = Employee::all();

        return view('account::journal.record_modifications', compact('entries', 'accounts', 'employees'));
    }
}
