@if(isset($client))
<div class="card">
    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3 border-0">
        <div class="btn-group" role="group">
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadPreviousClient()">
                <i class="fas fa-chevron-right"></i> السابق
            </button>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="loadNextClient()">
                التالي <i class="fas fa-chevron-left"></i>
            </button>
        </div>

        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="printClientDetails()">
                <i class="fas fa-print"></i>
            </button>
            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-edit"></i>
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-3">
            <div class="col-md-6">
                <h5 class="mb-3">معلومات العميل</h5>
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" width="40%">الاسم التجاري:</th>
                        <td>{{ $client->trade_name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">كود العميل:</th>
                        <td>#{{ $client->code ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">رقم الجوال:</th>
                        <td>{{ $client->phone ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">البريد الإلكتروني:</th>
                        <td>{{ $client->email ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">العنوان:</th>
                        <td>{{ $client->address ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">حالة العميل:</th>
                        <td>
                            <span class="badge 
                                @if(optional($client->latestStatus)->status == 'مديون') bg-warning
                                @elseif(optional($client->latestStatus)->status == 'دائن') bg-danger
                                @elseif(optional($client->latestStatus)->status == 'مميز') bg-primary
                                @else bg-secondary @endif">
                                {{ optional($client->latestStatus)->status ?? 'غير محدد' }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <div class="col-md-6">
                <h5 class="mb-3">معلومات إضافية</h5>
                <table class="table table-borderless">
                    <tr>
                        <th class="text-muted" width="40%">تاريخ التسجيل:</th>
                        <td>{{ $client->created_at->format('Y-m-d') }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">آخر تحديث:</th>
                        <td>{{ $client->updated_at->diffForHumans() }}</td>
                    </tr>
                    <tr>
                        <th class="text-muted">الملاحظات:</th>
                        <td>{{ $client->notes ?? 'لا توجد ملاحظات' }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
@else
<div class="alert alert-info">
    <i class="fas fa-info-circle me-2"></i>
    لم يتم تحديد عميل. الرجاء اختيار عميل من القائمة.
</div>
@endif
