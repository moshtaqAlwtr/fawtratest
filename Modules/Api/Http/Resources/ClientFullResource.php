<?php

namespace Modules\Api\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ClientFullResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->trade_name,
            'tax_number' => $this->tax_number,
            'phone' => $this->phone,
            'emdail' => $this->email,
            'type' => $this->type,
            'visit_type' => $this->visit_type,
            'balance' => optional($this->account_client)->balance ?? 0,
            'status' => optional($this->status_client)->name,
            'status_color' => optional($this->status_client)->color,
            'branch' => optional($this->branch)->name,
            'region' => optional(optional($this->neighborhood)->region)->name,
            'neighborhood' => optional($this->neighborhood)->name,
            'employee' => optional($this->employee)->name,
            'latitude'  => optional($this->locations)->latitude,
'longitude' => optional($this->locations)->longitude,
// 'contacts'  => ContactResource::collection($this->whenLoaded('contacts')),
'contacts' => ContactResource::collection($this->contacts),

            'categories' => $this->whenLoaded('categoriesClient', fn() => $this->categoriesClient->pluck('name')),
 // لو العلاقة موجودة
            'distance_km' => $this->distance,
            'created_at' => $this->created_at?->format('Y-m-d'),
            
            'analytics' => $this->when(isset($this->analytics), fn() => $this->analytics),
         

            // 'contacts'             => ClientContactResource::collection($this->whenLoaded('contacts')),
            'employees'            => EmployeeResource::collection($this->whenLoaded('employees')),
            // 'branch'               => new BranchResource($this->whenLoaded('branch')),
            // 'locations'            => LocationResource::collection($this->whenLoaded('locations')),
            // 'group'                => new RegionGroupResource($this->whenLoaded('group')),

            'invoices'             => InvoiceResource::collection($this->whenLoaded('invoices')),
            'payments'             => PaymentResource::collection($this->whenLoaded('payments')),
            // 'appointments'         => AppointmentResource::collection($this->whenLoaded('appointments')),
            // 'appointment_notes'    => AppointmentNoteResource::collection($this->whenLoaded('appointmentNotes')),
            'visits'               => VisitResource::collection($this->whenLoaded('visits')),
            // 'account'              => new AccountResource($this->whenLoaded('account')),
            // 'membership'           => MembershipResource::collection($this->whenLoaded('memberships')),
            // 'bookings'             => BookingResource::collection($this->whenLoaded('bookings')),
            'notes'     => ClientRelationResource::collection($this->whenLoaded('appointmentNotes')),
            // 'installments'         => InstallmentResource::collection($this->whenLoaded('installments')),
           

           
            'balance'                  => $this->invoices->sum('due_value') ?? 0,
        ];
    }
}

