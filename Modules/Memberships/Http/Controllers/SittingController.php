<?php


namespace Modules\Memberships\app\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\MembershipsSetthing;
use Illuminate\Http\Request;
use App\Models\Log as ModelsLog;
class SittingController extends Controller
{
  public function index() {


    return view('memberships::sitting_memberships.index');
  }
  // public function sitting()
  // {
  //   // $setting = MembershipsSetthing::all();
  //   return view('Memberships_subscriptions.sitting_memberships.sitting');

  //   return view('Memberships.sitting_memberships.index');
  // }
  public function sitting()
  {
    return view('memberships::sitting_memberships.sitting');

  }

  public function store(Request $request)
  {
      // البحث عن السجل الأول، وإذا لم يكن موجودًا يتم إنشاؤه
      $setting = MembershipsSetthing::firstOrNew();

      // تحديث القيم
      $setting->days_allowed   = $request->days_allowed;
      $setting->active_clients = $request->active_clients;
      $setting->save(); // الحفظ سواء كان تحديثًا أو إدخالاً جديدًا
    ModelsLog::create([
                'type' => 'setting',
                'type_id' => $setting->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
             'description' => 'تم اضافة اعداد عضوية',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
      return back()->with('success', 'تم التعديل بنجاح');
  }

}
