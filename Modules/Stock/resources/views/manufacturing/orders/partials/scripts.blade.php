<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
<script src="{{ asset('assets/js/notes-functions.js') }}"></script>

<script>
// تعيين معرف الأمر للاستخدام في ملفات JavaScript الأخرى
window.manufacturingOrderId = {{ $order->id ?? 'null' }};

$(document).ready(function() {
    // التحقق من تحميل المكتبات المطلوبة
    if (typeof $ === 'undefined') {
        console.error('jQuery غير محمل');
    }

    if (typeof bootstrap === 'undefined' && typeof $.fn.dropdown === 'undefined') {
        console.error('Bootstrap غير محمل');
    }

    if (typeof Swal === 'undefined') {
        console.error('SweetAlert2 غير محمل');
    }

    // تأكيد التراجع عن الإنهاء
    $('.confirm-undo').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'تأكيد التراجع عن الإنهاء',
            text: 'هل أنت متأكد من التراجع عن إنهاء أمر التصنيع؟ سيتم إعادة الأمر إلى حالة "قيد التنفيذ".',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ffc107',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-undo"></i> نعم، تراجع عن الإنهاء',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    setTimeout(() => resolve(), 1000);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري التحديث...',
                    text: 'يتم التراجع عن إنهاء أمر التصنيع',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                form.submit();
            }
        });
    });

    // تأكيد إغلاق الأمر
    $('.confirm-close').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'تأكيد إغلاق الأمر',
            html: `
                <div class="text-center mb-3">
                    <i class="fa fa-lock text-warning" style="font-size: 48px;"></i>
                </div>
                <p>هل أنت متأكد من إغلاق أمر التصنيع؟</p>
                <div class="alert alert-warning mt-3">
                    <strong>تنبيه:</strong> بعد الإغلاق لن تتمكن من تعديل الأمر أو تغيير حالته مرة أخرى.
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#6c757d',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-lock"></i> نعم، أغلق الأمر نهائياً',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    setTimeout(() => resolve(), 1500);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري الإغلاق...',
                    text: 'يتم إغلاق أمر التصنيع نهائياً',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                form.submit();
            }
        });
    });

    // تأكيد إعادة فتح الأمر
    $('.confirm-reopen').on('click', function(e) {
        e.preventDefault();
        const form = $(this).closest('form');

        Swal.fire({
            title: 'تأكيد إعادة الفتح',
            html: `
                <div class="text-center mb-3">
                    <i class="fa fa-unlock text-primary" style="font-size: 48px;"></i>
                </div>
                <p>هل أنت متأكد من إعادة فتح أمر التصنيع؟</p>
                <div class="alert alert-info mt-3">
                    <strong>ملاحظة:</strong> سيتم إعادة الأمر إلى حالة "مكتمل" ويمكن التعامل معه مرة أخرى.
                </div>
            `,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#007bff',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fa fa-unlock"></i> نعم، أعد الفتح',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    setTimeout(() => resolve(), 1000);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري إعادة الفتح...',
                    text: 'يتم إعادة فتح أمر التصنيع',
                    icon: 'info',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => Swal.showLoading()
                });
                form.submit();
            }
        });
    });

    // تفعيل التبويبات
    $('.nav-tabs a').on('click', function (e) {
        e.preventDefault();
        $(this).tab('show');
    });

    // تحميل البيانات عند النقر على تبويب النشاطات
    $('#activities-tab').on('click', function() {
        if (!$(this).hasClass('loaded')) {
            $('#logsLoading').show();
            setTimeout(function() {
                $('#logsLoading').hide();
                $('#activities-tab').addClass('loaded');
            }, 1000);
        }
    });

    // تأثيرات بصرية للجداول
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-hover-effect');
        },
        function() {
            $(this).removeClass('table-hover-effect');
        }
    );

    // التحقق من صحة نموذج إنهاء الأمر
    $('#finishOrderModal form').on('submit', function(e) {
        e.preventDefault();

        let isValid = true;
        let errorMsg = '';

        if (!$('#main_warehouse').val()) {
            isValid = false;
            errorMsg += 'يرجى اختيار مستودع المنتج الرئيسي\n';
        }

        if (!$('#waste_warehouse').val()) {
            isValid = false;
            errorMsg += 'يرجى اختيار مستودع المواد الهالكة\n';
        }

        if (!$('#delivery_date').val()) {
            isValid = false;
            errorMsg += 'يرجى تحديد تاريخ التسليم\n';
        }

        if (!isValid) {
            Swal.fire({
                title: 'خطأ في البيانات',
                html: `
                    <div class="text-center mb-3">
                        <i class="fa fa-exclamation-triangle text-warning" style="font-size: 48px;"></i>
                    </div>
                    <ul class="text-right">
                        ${errorMsg.split('\n').filter(err => err).map(error => `<li>${error}</li>`).join('')}
                    </ul>
                `,
                icon: 'error',
                confirmButtonText: 'موافق',
                confirmButtonColor: '#dc3545'
            });
            return;
        }

        // تأكيد إنهاء الأمر
        Swal.fire({
            title: 'تأكيد إنهاء أمر التصنيع',
            html: `
                <div class="text-center mb-3">
                    <i class="fa fa-check-circle text-success" style="font-size: 48px;"></i>
                </div>
                <p>هل أنت متأكد من إنهاء أمر التصنيع؟</p>
                <div class="alert alert-success mt-3">
                    <strong>سيتم:</strong>
                    <ul class="text-right mb-0">
                        <li>تغيير حالة الأمر إلى "مكتمل"</li>
                        <li>إضافة المنتج النهائي للمستودع</li>
                        <li>إضافة المواد الهالكة للمستودع المخصص</li>
                    </ul>
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#d33',
            confirmButtonText: '<i class="fa fa-check"></i> نعم، إنهاء الأمر',
            cancelButtonText: '<i class="fa fa-times"></i> إلغاء',
            reverseButtons: true,
            showLoaderOnConfirm: true,
            preConfirm: () => {
                return new Promise((resolve) => {
                    setTimeout(() => resolve(), 2000);
                });
            }
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: 'جاري إنهاء الأمر...',
                    html: `
                        <div class="text-center">
                            <div class="spinner-border text-success mb-3" role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                            <p>يتم إنهاء أمر التصنيع وتحديث المخزون...</p>
                        </div>
                    `,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false
                });
                $(this).off('submit').submit();
            }
        });
    });

    // تنسيق أفضل للجداول
    $('.table').each(function() {
        if (!$(this).parent().hasClass('table-responsive')) {
            $(this).wrap('<div class="table-responsive"></div>');
        }
    });

    // تفعيل tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // معالجة رسائل النجاح والخطأ من السيرفر
    const successMessage = $('.alert-success');
    const errorMessage = $('.alert-danger');

    if (successMessage.length) {
        const messageText = successMessage.text().trim();
        Swal.fire({
            title: 'تم بنجاح!',
            text: messageText,
            icon: 'success',
            confirmButtonText: 'ممتاز',
            confirmButtonColor: '#28a745',
            timer: 3000,
            timerProgressBar: true
        });
        successMessage.hide();
    }

    if (errorMessage.length) {
        const messageText = errorMessage.text().trim();
        Swal.fire({
            title: 'خطأ!',
            text: messageText,
            icon: 'error',
            confirmButtonText: 'موافق',
            confirmButtonColor: '#dc3545'
        });
        errorMessage.hide();
    }
});
</script>