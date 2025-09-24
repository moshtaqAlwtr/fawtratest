<?php

namespace Modules\Api\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApiNoteRequest;
use App\Models\Client;
use App\Models\ClientRelation;
use App\Models\EmployeeClientVisit;
use App\Models\Location;
use App\Traits\ApiResponseTrait;
use App\Models\Log as ModelsLog;
use App\Models\notifications;
use App\Models\Statuses;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class ClientRelationController extends Controller
{
       use ApiResponseTrait;
  
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return "f";
        return view('api::index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('api::create');
    }

    /**
     * Store a newly created resource in storage.
     */
 public function store(ApiNoteRequest $request)
{


 
    DB::beginTransaction();

    try {
        $client = Client::findOrFail($request->client_id);
        $underReviewStatus = Statuses::where('name', 'تحت المراجعة')->first();
        $activeStatus = Statuses::where('name', 'نشط')->first();
        $followUpStatus = Statuses::where('name', 'متابعة')->first();

        if (auth()->user()->role === 'employee') {
            $employeeLocation = Location::where('employee_id', auth()->id())->latest()->firstOrFail();
            $clientLocation = Location::where('client_id', $client->id)->latest()->firstOrFail();
            $distance = $this->calculateDistance($employeeLocation->latitude, $employeeLocation->longitude, $clientLocation->latitude, $clientLocation->longitude);

            if ($distance > 0.3) {
                throw new \Exception('يجب أن تكون ضمن نطاق 0.3 كيلومتر من العميل! المسافة الحالية: ' . round($distance, 2) . ' كم');
            }
        }

        EmployeeClientVisit::where('employee_id', auth()->id())
            ->where('client_id', $client->id)
            ->update(['status' => 'active']);

        $clientRelation = ClientRelation::create([
            'employee_id' => auth()->id(),
            'client_id' => $client->id,
            'status' => $request->status ?? 'pending',
            'process' => $request->process,
            'site_type' => $request->site_type, 
            'description' => $request->description,
            'deposit_count' => $request->deposit_count,
            'competitor_documents' => $request->competitor_documents,
            'additional_data' => json_encode([
                'deposit_count' => $request->deposit_count,
                'competitor_documents' => $request->competitor_documents,
                'latitude' => $request->current_latitude,
                'longitude' => $request->current_longitude,
            ]),
        ]);

        // إشعار ومسؤوليات متابعة/إبلاغ المشرف
        $notificationData = [
            'user_id' => auth()->id(),
            'receiver_id' => auth()->id(),
            'type' => 'client_note',
            'title' => 'تم إضافة ملاحظة للعميل ' . $client->trade_name,
            'description' => 'نوع الإجراء: ' . $request->description,
            'read' => false,
        ];

        if (in_array($request->process, ['إبلاغ المشرف', 'متابعة'])) {
            $supervisor = null;
            if ($request->process === 'إبلاغ المشرف') {
                $supervisor = User::where('role', 'manager')
                    ->where(function ($q) {
                        $q->where('id', auth()->user()->supervisor_id)
                          ->orWhere('role', 'manager');
                    })
                    ->first();
            }

            if ($followUpStatus && ($request->process === 'متابعة' || $supervisor)) {
                $oldStatus = $client->status_id;
                $client->status_id = $followUpStatus->id;
                $client->save();

                if (!ClientRelation::where('client_id', $client->id)
                    ->where('employee_id', auth()->id())
                    ->where('process', 'إبلاغ المشرف')
                    ->whereNotNull('employee_view_status')->exists()) {
                    $clientRelation->update(['employee_view_status' => $oldStatus]);
                }

                if ($supervisor) {
                    $notificationData['receiver_id'] = $supervisor->id;
                    $notificationData['type'] = 'supervisor_alert';
                    $notificationData['title'] = 'إبلاغ عن مشكلة عميل - ' . $client->trade_name;
                    $notificationData['description'] = 'يوجد مشكلة تحتاج متابعة مع العميل ' . $client->trade_name . ': ' . $request->description;
                }

                ModelsLog::create([
                    'type' => 'status_change',
                    'type_log' => 'log',
                    'description' => 'تم تغيير حالة العميل إلى "متابعة" بسبب: ' . $request->process,
                    'created_by' => auth()->id(),
                    'related_id' => $client->id,
                    'related_type' => Client::class,
                ]);
            }
        }

        notifications::create($notificationData);

        if ($underReviewStatus && $activeStatus && $client->status_id == $underReviewStatus->id) {
            $client->status_id = $activeStatus->id;
            $client->save();

            ModelsLog::create([
                'type' => 'status_change',
                'type_log' => 'log',
                'description' => 'تم تغيير حالة العميل من "تحت المراجعة" إلى "نشط" تلقائياً',
                'created_by' => auth()->id(),
                'related_id' => $client->id,
                'related_type' => Client::class,
            ]);
        }

        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                    $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                    $file->move(public_path('assets/uploads/notes'), $filename);
                    $attachments[] = $filename;
                }
            }
            $clientRelation->attachments = json_encode($attachments);
            $clientRelation->save();
        }

        if (auth()->user()->role === 'employee') {
            Location::where('employee_id', auth()->id())->latest()->first()?->update([
                'client_relation_id' => $clientRelation->id,
                'client_id' => $client->id,
                'latitude' => $request->current_latitude,
                'longitude' => $request->current_longitude,
            ]);
        }

        ModelsLog::create([
            'type' => 'client_note',
            'type_log' => 'log',
            'description' => 'تم إضافة ملاحظة للعميل: ' . $request->description,
            'created_by' => auth()->id(),
            'related_id' => $client->id,
            'related_type' => Client::class,
        ]);

        $client->last_note_at = now();
        $client->save();

        DB::commit();
        return $this->successResponse([], 'تمت إضافة الملاحظة بنجاح');
    } catch (\Exception $e) {
        DB::rollBack();
        return $this->errorResponse('فشل في إضافة الملاحظة', 500, $e->getMessage());
    }
}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('api::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('api::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
