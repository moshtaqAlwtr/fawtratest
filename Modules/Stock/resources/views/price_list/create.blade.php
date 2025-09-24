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
                            <li class="breadcrumb-item active">اضافه
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
                <form class="form form-vertical" id="createForm" action="{{ route('price_list.store') }}" method="POST">
                    @csrf
                    <div class="form-body">
                        <div class="row">
                            <div class="col-6">
                                <div class="form-group">
                                    <label for="first-name-vertical">الاسم</label>
                                    <input type="text" value="{{ old('name') }}" class="form-control" name="name" placeholder="اخل اسم قامه الاسعار">
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
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>موقوف</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-12">
                                <button type="button" id="submitBtn" class="btn btn-primary mr-1 mb-1 waves-effect waves-light">حفظ</button>
                                <button type="button" id="resetBtn" class="btn btn-outline-warning mr-1 mb-1 waves-effect waves-light">تفريغ</button>
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
    // تأكيد الحفظ
    document.getElementById('submitBtn').addEventListener('click', function(e) {
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

        Swal.fire({
            title: 'تأكيد الحفظ',
            text: 'هل أنت متأكد من رغبتك في حفظ قائمة الأسعار الجديدة؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احفظ',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                // إظهار loader أثناء الحفظ
                Swal.fire({
                    title: 'جاري الحفظ...',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // إرسال النموذج
                document.getElementById('createForm').submit();
            }
        });
    });

    // تأكيد التفريغ
    document.getElementById('resetBtn').addEventListener('click', function(e) {
        e.preventDefault();

        Swal.fire({
            title: 'تأكيد التفريغ',
            text: 'هل أنت متأكد من رغبتك في تفريغ جميع البيانات؟',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#f39c12',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، فرغ البيانات',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('createForm').reset();
                Swal.fire({
                    title: 'تم!',
                    text: 'تم تفريغ البيانات بنجاح',
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
            title: 'تم بنجاح!',
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
</script>
@endsection