{{-- تبويب مرتجعات المواد --}}
<div class="tab-pane" id="returned-materials" aria-labelledby="returned-materials-tab" role="tabpanel">
    <div class="row mt-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><i class="fa fa-undo"></i> مرتجعات المواد</h4>
                @if(!$order->isCompleted() && $order->status !== 'closed')
                    <a href="{{ route('store_permits_management.create', ['source' => 'manufacturing_order', 'order_id' => $order->id, 'type' => 'return']) }}" class="btn btn-warning btn-sm">
                        <i class="fa fa-undo"></i> إرجاع مواد
                    </a>
                @endif
            </div>

            {{-- قائمة المواد المرتجعة --}}
            <div class="materials-card">
                @php
                    // استعلام للحصول على إذونات الإرجاع المرتبطة بهذا الأمر
                    $returnedPermits = App\Models\WarehousePermits::where('permission_source_id', 15)
                        ->with(['items.product', 'storeHouse'])
                        ->orderBy('created_at', 'desc')
                        ->get();
                @endphp

                @if($returnedPermits->count() > 0)
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
                                @foreach($returnedPermits as $permit)
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
                        <i class="fa fa-undo"></i>
                        <h5>لا توجد مرتجعات</h5>
                        <p class="text-muted">لم يتم إرجاع أي مواد من هذا الأمر بعد.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>