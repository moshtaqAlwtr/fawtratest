{{-- تبويب المواد المستلمة --}}
<div class="tab-pane" id="received-materials" aria-labelledby="received-materials-tab" role="tabpanel">
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fa fa-box"></i> المواد المستلمة</h4>
                @if(!$order->isCompleted() && $order->status !== 'closed')
                    <a href="{{ route('store_permits_management.create', ['source' => 'manufacturing_order', 'order_id' => $order->id, 'type' => 'receive']) }}" class="btn btn-success btn-sm">
                        <i class="fa fa-plus"></i> استلام مواد
                    </a>
                @endif
            </div>

            {{-- قائمة المواد المستلمة --}}
            <div class="materials-card">
                @php
                    // استعلام للحصول على إذونات الاستلام المرتبطة بهذا الأمر
                    $receivedPermits = App\Models\WarehousePermits::where('permission_source_id', 15)
                        ->with(['items.product', 'storeHouse'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp

                @if($receivedPermits->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="thead-light">
                                <tr>
                                    <th>رقم الإذن</th>
                                    <th>التاريخ</th>
                                    <th>المستودع</th>
                                    <th>الحالة</th>
                                    <th>المجموع</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($receivedPermits as $permit)
                                <tr>
                                    <td>
                                        <strong>{{ $permit->number }}</strong>
                                        <br><small class="text-muted">{{ $permit->details }}</small>
                                    </td>
                                    <td>{{ $permit->permission_date }}</td>
                                    <td>{{ $permit->storeHouse->name ?? 'غير محدد' }}</td>
                                    <td>
                                        @if($permit->status === 'approved')
                                            <span class="badge badge-success">موافق عليه</span>
                                        @elseif($permit->status === 'pending')
                                            <span class="badge badge-warning">في الانتظار</span>
                                        @else
                                            <span class="badge badge-danger">مرفوض</span>
                                        @endif
                                    </td>
                                    <td>{{ number_format($permit->grand_total, 2) }} ر.س</td>
                                    <td>
                                        <a href="{{ route('store_permits_management.show', $permit->id) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-eye"></i> عرض
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="empty-state">
                        <i class="fa fa-box"></i>
                        <h5>لا توجد مواد مستلمة</h5>
                        <p class="text-muted">لم يتم استلام أي مواد لهذا الأمر بعد.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>