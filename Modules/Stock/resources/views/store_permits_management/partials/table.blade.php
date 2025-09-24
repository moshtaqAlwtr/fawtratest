{{-- ملف: resources/views/stock/store_permits_management/partials/table.blade.php --}}

@if ($wareHousePermits->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>رقم الإذن</th>
                    <th>مصدر الإذن</th>
                    <th>التاريخ</th>
                    <th>المستودع</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($wareHousePermits as $item)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input order-checkbox" value="{{ $item->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color:
                                    @if($item->permission_type == 1) #28a745
                                    @elseif($item->permission_type == 2) #dc3545
                                    @else #ffc107 @endif">
                                    <span class="avatar-content">
                                        @if($item->permission_type == 1) +
                                        @elseif($item->permission_type == 2) -
                                        @else ⇄ @endif
                                    </span>
                                </div>
                                <div>
                                    {{ $item->number }}
                                    <div class="text-muted small">#{{ $item->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $item->permissionSource->name ?? 'غير محدد' }}
                            <br>

                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($item->permission_date)->format('Y-m-d H:i') }}<br>
                            <small class="text-muted">أضيفت بواسطة: {{ $item->user->name ?? '---' }}</small>
                        </td>
                        <td>
    @if($item->fromStoreHouse && $item->toStoreHouse)
        {{ $item->fromStoreHouse->name }} - {{ $item->toStoreHouse->name }}
    @elseif($item->fromStoreHouse)
        {{ $item->fromStoreHouse->name }}
    @elseif($item->toStoreHouse)
        {{ $item->toStoreHouse->name }}
    @else
        غير محدد
    @endif
</td>

                        <td>
                            @if ($item->status == 'pending')
                                <span class="badge bg-warning">قيد الانتظار</span>
                            @elseif ($item->status == 'approved')
                                <span class="badge bg-success">موافق عليه</span>
                            @elseif ($item->status == 'rejected')
                                <span class="badge bg-danger">مرفوض</span>
                            @elseif ($item->status == 'processing')
                                <span class="badge bg-info">قيد المعالجة</span>
                            @endif

                            <br>
                            <small class="text-muted mt-1">{{ $item->user->branch->name ?? 'غير محدد' }}</small>
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item"
                                            href="{{ route('store_permits_management.show', $item->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>

                                        @if ($item->status == 'pending')
                                            <a class="dropdown-item"
                                                href="{{ route('store_permits_management.edit', $item->id) }}">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a>
                                        @endif

                                        @if ($item->status == 'pending')
                                            <a class="dropdown-item text-success approve-btn" href="#"
                                                data-id="{{ $item->id }}" data-number="{{ $item->number }}">
                                                <i class="fa fa-check me-2"></i>موافقة
                                            </a>
                                            <a class="dropdown-item text-warning" href="#"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                                <i class="fa fa-times me-2"></i>رفض
                                            </a>
                                        @elseif ($item->status == 'approved')
                                            <a class="dropdown-item text-warning" href="#"
                                                data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}">
                                                <i class="fa fa-times me-2"></i>رفض
                                            </a>
                                        @elseif ($item->status == 'rejected')
                                            <a class="dropdown-item text-success approve-btn" href="#"
                                                data-id="{{ $item->id }}" data-number="{{ $item->number }}">
                                                <i class="fa fa-check me-2"></i>موافقة
                                            </a>
                                        @endif

                                        <a class="dropdown-item" href="" target="_blank">
                                            <i class="fa fa-print me-2 text-info"></i>طباعة
                                        </a>
                                        <a class="dropdown-item text-danger delete-btn" href="#"
                                            data-id="{{ $item->id }}" data-number="{{ $item->number }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div class="modal fade" id="rejectModal{{ $item->id }}" tabindex="-1"
                                aria-labelledby="rejectModalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-warning">
                                            <h5 class="modal-title text-white" id="rejectModalLabel{{ $item->id }}">
                                                رفض الإذن المخزني رقم #{{ $item->number }}
                                            </h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                aria-label="Close"></button>
                                        </div>
                                        <form method="POST" action="{{ route('store_permits_management.reject', $item->id) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label for="rejection_reason{{ $item->id }}" class="form-label">
                                                        سبب الرفض <span class="text-danger">*</span>
                                                    </label>
                                                    <textarea class="form-control" id="rejection_reason{{ $item->id }}"
                                                        name="rejection_reason" rows="4" required
                                                        placeholder="يرجى كتابة سبب رفض الإذن المخزني..."></textarea>
                                                </div>
                                                <div class="alert alert-warning">
                                                    <i class="fa fa-exclamation-triangle me-2"></i>
                                                    <strong>تنبيه:</strong> سيتم رفض الإذن المخزني نهائياً ولن يمكن التراجع عن هذا الإجراء.
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                    <i class="fa fa-times me-1"></i>إلغاء
                                                </button>
                                                <button type="submit" class="btn btn-warning">
                                                    <i class="fa fa-times me-1"></i>تأكيد الرفض
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- الترقيم --}}
    @include('stock::store_permits_management.partials.pagination', ['wareHousePermits' => $wareHousePermits])
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا يوجد أذون مخزنية تطابق معايير البحث</p>
    </div>
@endif
