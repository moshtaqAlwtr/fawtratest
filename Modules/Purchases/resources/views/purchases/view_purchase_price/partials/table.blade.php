@if ($purchaseQuotation->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                </th>
                <th>الكود</th>
                <th>المورد</th>
                <th>التاريخ</th>
                <th>صالح حتى</th>
                <th>صافي الدخل</th>
                <th>الحالة</th>
                <th style="width: 10%">خيارات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchaseQuotation as $quot)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input order-checkbox" value="{{ $quot->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-2" style="background-color: #4B6584">
                                <span class="avatar-content">{{ substr($quot->code, 0, 1) }}</span>
                            </div>
                            <div>
                                {{ $quot->code }}
                                <div class="text-muted small">#{{ $quot->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>{{ $quot->supplier->trade_name ?? 'غير محدد' }}</td>
                    <td>{{ \Carbon\Carbon::parse($quot->date)->format('Y-m-d') }}</td>
                    <td>
                        @if ($quot->valid_days)
                            {{ \Carbon\Carbon::parse($quot->date)->addDays($quot->valid_days)->format('Y-m-d') }}
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ number_format($quot->grand_total, 2) }}</td>
                    <td>
                        @if ($quot->status == "Under Review")
                            <span class="badge bg-warning">تحت المراجعة</span>
                        @elseif ($quot->status == "approval")
                            <span class="badge bg-success">تم تحويلها الى امر شراء</span>
                        @elseif ($quot->status == "disagree")
                            <span class="badge bg-danger">مرفوض</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                    type="button" id="dropdownMenuButton{{ $quot->id }}"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu dropdown-menu-end"
                                    aria-labelledby="dropdownMenuButton{{ $quot->id }}">
                                    <a class="dropdown-item" href="{{ route('pricesPurchase.show', $quot->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                    <a class="dropdown-item" href="{{ route('pricesPurchase.edit', $quot->id) }}">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                    <a class="dropdown-item text-danger" href="#"
                                        onclick="confirmDelete('{{ $quot->id }}', '{{ $quot->code }}')">
                                        <i class="fa fa-trash me-2"></i>حذف
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Modal delete (يتم إنشاؤه ديناميكياً) -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">حذف عرض السعر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p id="delete-message">هل أنت متأكد من حذف عرض السعر؟</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                    <button type="button" class="btn btn-danger" id="confirm-delete">حذف</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    function confirmDelete(id, code) {
        $('#delete-message').text('هل أنت متأكد من حذف عرض السعر رقم "' + code + '"؟');
        $('#confirm-delete').off('click').on('click', function() {
            deleteQuotation(id);
        });
        $('#deleteModal').modal('show');
    }

    function deleteQuotation(id) {
        $.ajax({
            url: '{{ route("pricesPurchase.destroy", ":id") }}'.replace(':id', id),
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            success: function(response) {
                $('#deleteModal').modal('hide');
                // إعادة تحميل البيانات بعد الحذف
                window.loadData();
                // عرض رسالة نجاح
                showAlert('success', 'تم حذف عرض السعر بنجاح');
            },
            error: function(xhr, status, error) {
                $('#deleteModal').modal('hide');
                showAlert('danger', 'حدث خطأ أثناء الحذف');
            }
        });
    }

    function showAlert(type, message) {
        let alertHtml = '<div class="alert alert-' + type + ' alert-dismissible fade show" role="alert">' +
                       message +
                       '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>' +
                       '</div>';

        $('.content-body').prepend(alertHtml);

        // إخفاء التنبيه تلقائياً بعد 5 ثوان
        setTimeout(function() {
            $('.alert').fadeOut();
        }, 5000);
    }
    </script>

@else
    <div class="alert alert-info text-center" role="alert">
        <p class="mb-0">لا يوجد عروض أسعار مضافة حتى الآن</p>
    </div>
@endif
