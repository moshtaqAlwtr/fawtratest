@extends('master')

@section('title')
قوائم الأسعار
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة قوائم الأسعار</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-header"></div>
            <div class="card-body">
                <form class="form form-vertical" id="editForm" action="{{ route('price_list.update',$price_list->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="first-name-vertical">الاسم</label>
                                    <input type="text" value="{{ old('name',$price_list->name) }}" class="form-control" name="name" placeholder="اخل اسم قامه الاسعار">
                                    @error('name')
                                    <span class="text-danger" id="basic-default-name-error" class="error">
                                        {{ $message }}
                                    </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="email-id-vertical">الحالة</label>
                                    <select class="form-control" id="basicSelect" name="status">
                                        <option value="0" {{ old('status',$price_list->status) == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status',$price_list->status) == 1 ? 'selected' : '' }}>موقوف</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" id="updateBtn" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">تحديث</button>
                                <button type="button" id="resetBtn" class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">إعادة تعيين</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // حفظ البيانات الأصلية
    const originalData = {
        name: '{{ $price_list->name }}',
        status: '{{ $price_list->status }}'
    };

    // تأكيد التحديث
    document.getElementById('updateBtn').addEventListener('click', function(e) {
        e.preventDefault();

        // التحقق من صحة البيانات
        const nameInput = document.querySelector('input[name="name"]');
        const statusSelect = document.querySelector('select[name="status"]');

        if (!nameInput.value.trim()) {
            Swal.fire({
                title: 'خطأ!',
                text: 'يرجى إدخال اسم قائمة الأسعار',
                icon: 'error',
                confirmButtonText: 'موافق'
            });
            return;
        }

        // التحقق من وجود تغييرات
        const hasChanges = nameInput.value !== originalData.name ||
                          statusSelect.value !== originalData.status;

        if (!hasChanges) {
            Swal.fire({
                title: 'لا توجد تغييرات',
                text: 'لم يتم إجراء أي تعديلات على البيانات',
                icon: 'info',
                confirmButtonText: 'موافق'
            });
            return;
        }

        Swal.fire({
            title: 'تأكيد التحديث',
            text: 'هل أنت متأكد من رغبتك في تحديث قائمة الأسعار؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، حدث',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار loader أثناء التحديث
                Swal.fire({
                    title: 'جاري التحديث...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال النموذج
                document.getElementById('editForm').submit();
            }
        });
    });

    // إعادة تعيين البيانات إلى القيم الأصلية
    document.getElementById('resetBtn').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'تأكيد إعادة التعيين',
            text: 'هل أنت متأكد من رغبتك في إعادة تعيين البيانات إلى قيمتها الأصلية؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، أعد التعيين',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إعادة تعيين القيم إلى القيم الأصلية
                document.querySelector('input[name="name"]').value = originalData.name;
                document.querySelector('select[name="status"]').value = originalData.status;

                Swal.fire({
                    title: 'تم!',
                    text: 'تم إعادة تعيين البيانات بنجاح',
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    });

    // رسائل النجاح من الخادم
    @if(session('success'))
        Swal.fire({
            title: 'تم التحديث بنجاح!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'موافق'
        });
    @endif

    // رسائل الخطأ من الخادم
    @if($errors->any())
        Swal.fire({
            title: 'خطأ في البيانات!',
            html: '<ul style="text-align: right;">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>',
            icon: 'error',
            confirmButtonText: 'موافق'
        });
    @endif

    // تحذير عند مغادرة الصفحة مع وجود تغييرات غير محفوظة
    let formChanged = false;

    document.querySelectorAll('input, select').forEach(element => {
        element.addEventListener('change', function() {
            const nameInput = document.querySelector('input[name="name"]');
            const statusSelect = document.querySelector('select[name="status"]');

            formChanged = nameInput.value !== originalData.name ||
                         statusSelect.value !== originalData.status;
        });
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
</script>
@endsection