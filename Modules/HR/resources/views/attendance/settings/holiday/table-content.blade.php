{{-- ملف: resources/views/hr/attendance/settings/holiday/table-content.blade.php --}}

@if(isset($holiday_lists) && !@empty($holiday_lists) && $holiday_lists->count() > 0)
    <table class="table table-striped">
        <thead class="table-light">
            <tr>
                <th scope="col">الاسم</th>
                <th scope="col">اجمالى الايام</th>
                <th scope="col">اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($holiday_lists as $holiday_list)
                <tr id="row-{{ $holiday_list->id }}">
                    <td>{{ $holiday_list->name }}</td>
                    <td>{{ $holiday_list->holidays()->count() }}</td>
                    <td style="width: 10%">
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" id="dropdownMenuButton{{ $holiday_list->id }}"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $holiday_list->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('holiday_lists.show', $holiday_list->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('holiday_lists.edit', $holiday_list->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger delete-item" href="#"
                                           data-url="{{ route('holiday_lists.delete', $holiday_list->id) }}"
                                           data-name="{{ $holiday_list->name }}"
                                           data-toggle="modal" data-target="#modal_DELETE{{ $holiday_list->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Modal delete -->
                    <div class="modal fade text-left" id="modal_DELETE{{ $holiday_list->id }}"
                         tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $holiday_list->id }}" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                    <h4 class="modal-title" id="myModalLabel{{ $holiday_list->id }}" style="color: #FFFFFF">
                                        حذف {{ $holiday_list->name }}
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">
                                        الغاء
                                    </button>
                                    <button type="button" class="btn btn-danger waves-effect waves-light confirm-delete"
                                            data-url="{{ route('holiday_lists.delete', $holiday_list->id) }}"
                                            data-modal="#modal_DELETE{{ $holiday_list->id }}">
                                        تأكيد
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end delete modal-->

                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- إحصائيات النتائج --}}
    <div class="mt-3">
        <small class="text-muted">
            عدد النتائج: {{ $holiday_lists->count() }} قائمة عطل
        </small>
    </div>

@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <strong>لا توجد نتائج</strong>
        <p class="mb-0 mt-2">
            @if(request('keywords'))
                لا توجد قوائم عطل تطابق البحث "{{ request('keywords') }}"
            @else
                لا توجد قوائم عطل مضافة حتى الآن
            @endif
        </p>
        @if(request('keywords'))
            <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="$('#keywords').val(''); $('#searchForm').submit();">
                <i class="fa fa-times me-1"></i>مسح البحث
            </button>
        @endif
    </div>
@endif

<script>
// معالج حذف العناصر بـ AJAX من داخل المودال
$(document).on('click', '.confirm-delete', function() {
    const deleteUrl = $(this).data('url');
    const modalId = $(this).data('modal');
    const button = $(this);

    // تعطيل الزر ومؤشر التحميل
    button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> جاري الحذف...');

    $.ajax({
        url: deleteUrl,
        method: 'DELETE',
        data: {
            _token: $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response) {
            if (response.success) {
                $(modalId).modal('hide');
                showAlert('تم الحذف بنجاح', 'success');
                // إعادة تحميل النتائج
                setTimeout(() => {
                    $('#searchForm').submit();
                }, 1000);
            } else {
                showAlert(response.message || 'فشل في الحذف', 'danger');
            }
        },
        error: function(xhr) {
            let errorMessage = 'حدث خطأ أثناء الحذف';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert(errorMessage, 'danger');
        },
        complete: function() {
            // إعادة تفعيل الزر
            button.prop('disabled', false).html('تأكيد');
        }
    });
});

function showAlert(message, type) {
    const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            <strong>${message}</strong>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    `;

    // إضافة التنبيه في أعلى الصفحة
    if ($('.content-body .alert').length === 0) {
        $('.content-body').prepend(alertHtml);
    }

    // إخفاء التنبيه تلقائياً
    setTimeout(() => {
        $('.alert').fadeOut(() => {
            $('.alert').remove();
        });
    }, 4000);
}
</script>
