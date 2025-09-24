<?php

namespace App\Http\Controllers\Reservations;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingSetting;
use App\Models\Client;
use App\Models\Log as ModelsLog;
use App\Models\Employee;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class ReservationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bookings = Booking::with(['client', 'product'])->get();

        $calendarBookings = [];
        foreach ($bookings as $booking) {
            if ($booking->appointment_date) {
                $date = \Carbon\Carbon::parse($booking->appointment_date)->format('Y-m-d');
                if (!isset($calendarBookings[$date])) {
                    $calendarBookings[$date] = [];
                }
                $calendarBookings[$date][] = [
                    'id' => $booking->id,
                    'client' => $booking->client->trade_name ?? 'غير محدد',
                    'service' => $booking->product->name ?? 'غير محدد',
                    'time' => $booking->start_time ? \Carbon\Carbon::parse($booking->start_time)->format('H:i') : '-',
                    'status' => $booking->status,
                ];
            }
        }

        return view("reservations.index", compact('bookings', 'calendarBookings'));
    }

    public function client($id)
    {
        $bookings  = Booking::where('client_id',$id)->get();
        $Client    = Client::find($id);
        return view("reservations.client",compact('bookings','Client'));
    }

    public function updateStatus(Request $request, $id)
  {
    $reservation = Booking::findOrFail($id);
    $reservation->update([
        'status' => $request->status
    ]);
     ModelsLog::create([
                'type' => 'BOOKING',
                'type_id' => $reservation->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم تغيير حالة حجز',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);
    return back()->with('success', 'تم تحديث الحالة بنجاح');


}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $Products   = Product::all();
        $Employees  = User::where('role', 'employee')->get();
        $Clients     = Client::all();

        return view("reservations.create",compact('Products','Employees','Clients'));
    }
    public function BookingSettings()
    {

        $BookingSetting = BookingSetting::find(1);

        return view("reservations.Booking_Settings",compact('BookingSetting'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'product_id' => 'required',
            'employee_id' => 'required',
            'appointment_date' => 'required',
            'start_time' => 'required',
            'end_time' => 'required',
            'client_id' => 'required',
        ]);
       $BOOKING =  Booking::create($request->all());
         ModelsLog::create([
                'type' => 'BOOKING',
                'type_id' => $BOOKING->id, // ID النشاط المرتبط
                'type_log' => 'log', // نوع النشاط
                'description' => 'تم اضافة حجز',
                'created_by' => auth()->id(), // ID المستخدم الحالي
            ]);

        return redirect()->route('Reservations.index')->with('success', 'تم حجز الموعد بنجاح');
    }

    public function setting(Request $request)
{
    // تحقق إذا كان هناك إعدادات موجودة أم لا
    $setting = BookingSetting::first();

    if (!$setting) {
        // إذا لم يكن هناك إعدادات، يتم إنشاء إعدادات جديدة
        $setting = new BookingSetting();
        $setting->duration = $request->input('duration');

        $setting->payment_online = $request->input('payment_online');
        $setting->save();

        // إعطاء رد بعد الحفظ
        return redirect()->route('Reservations.index')->with('success', 'تم إنشاء الإعدادات بنجاح');
    } else {
        // إذا كانت هناك إعدادات، يتم تعديلها
        $setting->duration = $request->input('duration');

        $setting->payment_online = $request->input('payment_online');
        $setting->save();

        // إعطاء رد بعد التعديل
        return redirect()->route('Reservations.index')->with('success', 'تم تعديل الإعدادات بنجاح');
    }
}


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $booking  = Booking::find($id);
        return view("reservations.show",compact('booking'));

    }

    public function edit(string $id)
    {
        $booking = Booking::findOrFail($id);
        $Products = Product::all(); // قم بتعريف هذا بناءً على نموذجك
        $Employees = User::where('role', 'employee')->get(); // قم بتعريف هذا بناءً على نموذجك
        $Clients = Client::all(); // قم بتعريف هذا بناءً على نموذجك

        return view('reservations.edit', compact('booking', 'Products', 'Employees', 'Clients'));
    }

    /**
     * Update the specified resource in storage.
     */

     public function filter(Request $request)
{
    // بدء الاستعلام
    $bookings = Booking::query();

    // البحث برقم هاتف العميل أو اسم العميل
    if ($request->has('client_id')) {
        $bookings->whereHas('client', function ($query) use ($request) {
            $query->where('phone', 'like', '%' . $request->input('client') . '%')
                  ->orWhere('first_name', 'like', '%' . $request->input('client') . '%');
        });
    }

    // البحث باسم الموظف
    if ($request->has('employee')) {
        $bookings->whereHas('employee', function ($query) use ($request) {
            $query->where('first_name', 'like', '%' . $request->input('employee') . '%');
        });
    }

    // البحث بالفترة الزمنية
    if ($request->has('date_from') && $request->has('date_to')) {
        $bookings->whereBetween('appointment_date', [$request->input('date_from'), $request->input('date_to')]);
    }

    // البحث بالحالة
    if ($request->has('status')) {
        $bookings->where('status', $request->input('status'));
    }

    // تنفيذ الاستعلام وجلب النتائج
    $bookings = $bookings->paginate(10);

    return view('reservations.index', compact('bookings'));
}


     public function update(Request $request, string $id)
     {



         $booking = Booking::findOrFail($id);
         $booking->update($request->all());



         return redirect()->route('Reservations.index')->with('success', 'تم تحديث الحجز بنجاح');
     }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
        $booking = Booking::findOrFail($id);
        $booking->delete();
        return redirect()->route('Reservations.index')->with('success', 'تم حذف الحجز بنجاح');}
        catch (\Exception $e) {
            return redirect()->route('Reservations.index')->with('error', 'لا يمكن حذف الحجز');
        }
    }
}
