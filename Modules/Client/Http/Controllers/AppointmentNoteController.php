<?php

namespace Modules\Client\Http\Controllers;
use App\Http\Controllers\Controller;
use App\Models\AppointmentNote;
use App\Models\Client;
use App\Models\Appointment;
use App\Http\Requests\AppointmentNoteRequest;
use App\Models\CategoriesClient;
use App\Models\ClientRelation;
use App\Models\Employee;
use App\Models\Statuses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AppointmentNoteController extends Controller
{
    /**
     * عرض قائمة الملاحظات
     */
    public function index()
    {
        $notes = AppointmentNote::with(['appointment', 'user'])->latest()->get();


        // Assuming you want to get the client from the first note's appointment

        return view('client.contacts.show_contant', compact('notes'));
    }

    /**
     * عرض نموذج إنشاء ملاحظة جديدة
     */
    public function create($id)
{
    $clients = Client::with('latestStatus')->orderBy('created_at', 'desc')->get();
    $notes = AppointmentNote::with(['user'])
        ->latest()
        ->get();
    $appointments = Appointment::all();
    $employees = Employee::all();

    // Get the first client by default
    $client = $clients->first();

    $categories = CategoriesClient::all();
$statuses=Statuses::all();


    do {
        $lastClient = Client::orderBy('code', 'desc')->first();
        $newCode = $lastClient ? $lastClient->code + 1 : 1;
    } while (Client::where('code', $newCode)->exists());

    return view('client::appointments.note.add_note', compact('clients', 'appointments','statuses', 'id'));
}
    /**
     * حفظ ملاحظة جديدة
     */
    public function store(AppointmentNoteRequest $request)
    {
        $data = $request->except('_token');

        if($request->hasFile('attachments')){
            $data['attachments'] = $this->UploadImage('assets/uploads/note',$request->attachments);
        }
        $note = AppointmentNote::create($data);

        return redirect()->route('clients.show_contant', ['id' => $data['client_id']])
            ->with('success', 'تم إضافة الملاحظة بنجاح');
    }

    function uploadImage($folder,$image)
    {
        $fileExtension = $image->getClientOriginalExtension();
        $fileName = time().rand(1,99).'.'.$fileExtension;
        $image->move($folder,$fileName);

        return $fileName;
    }//end of uploadImage
    /**
     * عرض ملاحظة محددة
     */
    public function show(AppointmentNote $note)
    {
        return view('appointment.notes.show', compact('note'));
    }

    /**
     * عرض نموذج تعديل الملاحظة
     */
    public function edit($id)
    {
        $clients = Client::all();
        $appointments = Appointment::all();
        $note = AppointmentNote::findOrFail($id); // استرجاع الملاحظة باستخدام المعرف
        return view('client.appointments.note.edit_note', compact('note','clients','appointments')); // تأكد من وجود ملف edit_note.blade.php
    }



    /**
     * تحديث ملاحظة محددة
     */

    public function update(AppointmentNoteRequest $request, $id)
    {
        // استرجاع الملاحظة باستخدام المعرف
        $note = AppointmentNote::findOrFail($id);

        // تحديث النص
        $note->notes = $request->notes;

        // حفظ التغييرات
        $note->save();

        // إعادة توجيه مع رسالة نجاح
        return redirect()->route('client.contacts.show_contant')->with('success', 'تم تحديث الملاحظة بنجاح!');
    }

    /**
     * حذف ملاحظة محددة
     */
    public function destroy($id)
    {
        $note = AppointmentNote::find($id);

        if ($note) {
            $note->delete();
            return redirect()->back()->with('toast_message', 'ملاحظة تم حذفها بنجاح!')->with('toast_type', 'success');
        }

        return redirect()->back()->with('toast_message', 'الملاحظة غير موجودة!')->with('toast_type', 'error');
    }
    /**
     * تحميل مرفق
     */
    public function downloadAttachment($noteId, $index)
    {
        $note = AppointmentNote::findOrFail($noteId);
        $attachments = $note->attachments;

        if (isset($attachments[$index])) {
            $path = $attachments[$index];
            return Storage::disk('public')->downloadAttachment;
        }


        return back()->with('error', 'الملف غير موجود');
    }
}
