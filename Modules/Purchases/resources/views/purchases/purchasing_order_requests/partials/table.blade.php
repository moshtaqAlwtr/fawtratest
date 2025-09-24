@if ($purchaseOrdersRequests->count() > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>الكود</th>
                    <th>المورد</th>
                    <th>تاريخ الطلب</th>
                    <th>تاريخ الاستحقاق</th>
                    <th>صافي الدخل</th>
                    <th>الحالة</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($purchaseOrdersRequests as $req)
                    <tr class="quotation-row" data-quotation-id="{{ $req->id }}">
                        <td>
                            <input type="checkbox" class="form-check-input quotation-checkbox"
                                value="{{ $req->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2"
                                    style="background-color: #28a745; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                    <span class="avxatar-content">{{ substr($req->code, 0, 1) }}</span>
                                </div>
                                <div>
                                    <strong>{{ $req->code }}</strong>
                                    <div class="text-muted small">#{{ $req->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="fw-medium">{{ $req->supplier->trade_name??'امر شراء' }}</span>
                        </td>
                        <td>
                            <i class="fa fa-calendar text-muted me-1"></i>
                            {{ \Carbon\Carbon::parse($req->order_date)->format('Y-m-d') }}
                        </td>
                        <td>
                            @if ($req->due_date)
                                <i class="fa fa-clock text-muted me-1"></i>
                                {{ \Carbon\Carbon::parse($req->due_date)->format('Y-m-d') }}
                            @else
                                <span class="text-muted">-</span>
                            @endif

                        </td>
                        <td>
                            <span class="fw-medium">
                                {{ $req->grand_total }}
                            </span>
                        </td>
                        <td>
                            @if ($req->status == 'Under Review')
                                <span class="badge bg-warning text-dark">
                                    <i class="fa fa-clock me-1"></i>
                                    تحت المراجعة
                                </span>
                            @elseif ($req->status == 'approval')
                                <span class="badge bg-success">
                                    <i class="fa fa-check me-1"></i>
                                    مسعر
                                </span>
                                @elseif ($req->status == 'convert invoice')
                                <span class="badge bg-success">
                                    <i class="fa fa-check me-1"></i>
حولت الى فاتورة
                                </span>
                            @elseif ($req->status == 'disagree')
                                <span class="badge bg-danger">
                                    <i class="fa fa-times me-1"></i>
                                    مرفوض
                                </span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" id="dropdownMenuButton{{ $req->id }}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end"
                                        aria-labelledby="dropdownMenuButton{{ $req->id }}">
                                        <a class="dropdown-item" href="{{ route('OrdersRequests.show', $req->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('OrdersRequests.edit', $req->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                            data-bs-target="#deleteModal{{ $req->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal delete -->
                            <div class="modal fade" id="deleteModal{{ $req->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">حذف طلب الشراء</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <p>هل أنت متأكد من حذف طلب الشراء رقم "{{ $req->code }}"؟
                                            </p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light"
                                                data-bs-dismiss="modal">إلغاء</button>
                                            <form action="{{ route('OrdersRequests.destroy', $req->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger">حذف</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fa fa-file-text fa-3x text-muted"></i>
        </div>
        <h5 class="text-muted">لا يوجد   اوامر شراء</h5>
        <p class="text-muted mb-3">لا يوجد  اوامر شراء مضافة حتى الآن</p>
        <a href="{{ route('OrdersRequests.create') }}" class="btn btn-primary">
            <i class="fa fa-plus me-1"></i>
            أضف  امر شراء جديد
        </a>
    </div>
@endif
