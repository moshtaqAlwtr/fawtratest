@extends('client')

@section('title')
    ادارة المواعيد
@stop

@section('head')
    <!-- تضمين ملفات Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
@endsection

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('toast_message'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        toastr.{{ session('toast_type', 'success') }}('{{ session('toast_message') }}', '', {
            positionClass: 'toast-bottom-left',
            closeButton: true,
            progressBar: true,
            timeOut: 5000
        });
    });
</script>
@endif
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المواعيد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">
        <div class="card shadow-lg border-0 rounded-lg mb-4">
            <div class="card-header bg-white py-3">
                <div class="row justify-content-between align-items-center mx-2">
                    <!-- القسم الأيمن -->
                    <div class="col-auto d-flex align-items-center gap-5">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                        </div>
                        <div class="dropdown">
                            <button class="btn btn-outline-secondary dropdown-toggle px-4" type="button"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-filter me-2"></i>
                            </button>
                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item py-2" href="#"><i
                                            class="fas fa-sort-alpha-down me-2"></i>ترتيب حسب الاسم</a></li>
                                <li><a class="dropdown-item py-2" href="#"><i
                                            class="fas fa-sort-numeric-down me-2"></i>ترتيب حسب الرقم</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-sync me-2"></i>تحديث</a>
                                </li>
                            </ul>
                        </div>
                        <div class="dropdown">

                            <ul class="dropdown-menu shadow-sm">
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-edit me-2"></i>تعديل
                                        المحدد</a></li>
                                <li><a class="dropdown-item py-2" href="#"><i class="fas fa-trash me-2"></i>حذف
                                        المحدد</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item py-2" href="#"><i
                                            class="fas fa-file-export me-2"></i>تصدير</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- القسم الأيسر -->
                    <div class="col-auto d-flex align-items-center gap-5">
                        <!-- التنقل بين الصفحات -->
                        <nav aria-label="Page navigation" class="d-flex align-items-center">
                            <ul class="pagination mb-0 pagination-sm">
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-start" href="#" aria-label="First">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link border-0" href="#" aria-label="Previous">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                                <li class="page-item"><span class="page-link border-0">صفحة 1 من 10</span></li>
                                <li class="page-item">
                                    <a class="page-link border-0" href="#" aria-label="Next">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link border-0 rounded-end" href="#" aria-label="Last">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                            </ul>
                        </nav>

                        <div class="d-flex gap-4">

                            <button class="btn btn-outline-secondary sitting px-4" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false">
                                <i class="fas fa-cog me-2"></i>
                            </button>
                            <!-- زر إضافة عميل -->
                            <a href="{{ route('appointments.create') }}" class="btn btn-success px-4">
                                <i class="fas fa-plus-circle me-2"></i>
                                موعد جديد
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- محتوى الجدول -->

        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">بحث</h4>
                </div>

                <div class="card-body">
                    <form class="form">
                        <div class="form-body row">
                            <div class="form-group col-md-4">
                                <select name="status" id="feedback2" class="form-control">
                                    <option value="">-- اختر الحالة --</option>
                                    <option value="pending">تم جدولته</option>
                                    <option value="completed">تم</option>
                                    <option value="ignored">صرف النظر عنه</option>
                                    <option value="rescheduled">تم جدولته مجددا</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select name="employee_id" class="form-control" id="">
                                    <option value="">الموضف </option>
                                    @if (@isset($employees) && !@empty($employees) && count($employees) > 0)
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->employee_id }}">{{ $employee->full_name }}
                                                {{-- {{ $employee->last_name }}</option> --}}
                                        @endforeach
                                    @else
                                        <option value="">لا توجد عملا�� حاليا</option>
                                    @endif
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="time" class="form-label">اختار الحالة </label>
                                <select class="form-control" name="client_id" required>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}">
                                            @if($client->client_type == 1)
                                                عميل عادي
                                            @elseif($client->client_type == 2)
                                                عميل VIP
                                            @else
                                                غير محدد
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <select id="feedback2" class="form-control">
                                    <option value="">اي اجراء</option>
                                </select>
                            </div>
                        </div>

                        <div class="collapse" id="advancedSearchForm">
                            <div class="form-body row d-flex align-items-center g-0">

                                <div class="form-group col-md-2">
                                    <select id="feedback2" class="form-control">
                                        <option value="">تخصيص</option>
                                        <option value=""></option>
                                        <option value=""></option>
                                        <option value=""></option>
                                    </select>
                                </div>

                                <div class="form-group col-md-2">
                                    <input type="date" id="feedback1" class="form-control" placeholder="من"
                                        name="from_date">
                                </div>

                                <!-- إلى (التاريخ) -->
                                <div class="form-group col-md-2">
                                    <input type="date" id="feedback2" class="form-control" placeholder="إلى"
                                        name="to_date">
                                </div>
                                <div class="form-group col-md-3">
                                    <select name="" class="form-control" id="">
                                        <option value="">العميل </option>
                                        @if (@isset($clients) && !@empty($clients) && count($clients) > 0)
                                            @foreach ($clients as $client)
                                                <option value="{{ $client->id }}">{{ $client->trade_name }}
                                                    {{ $client->last_name }}</option>
                                            @endforeach
                                        @else
                                            <option value="">لا توجد حالات حاليا</option>
                                        @endif
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="employee_id" class="form-control" id="">
                                        <option value="">اضيفت بواسطة </option>
                                        @if (@isset($employees) && !@empty($employees) && count($employees) > 0)
                                            @foreach ($employees as $employee)
                                                <option value="{{ $employee->employee_id }}">{{ $employee->first_name }}
                                                    {{ $employee->last_name }}</option>
                                            @endforeach
                                        @else
                                            <option value="">لا توجد عملا�� حاليا</option>
                                        @endif
                                    </select>
                                </div>
                                <div class="form-group col-md-3">
                                    <select name="" class="form-control" id="">
                                        <option value=""> ضعط </option>

                                    </select>
                                </div>







                            </div>

                        </div>



                </div>



                <div class="form-actions">
                    <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>

                    <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse"
                        data-target="#advancedSearchForm">
                        <i class="bi bi-sliders"></i> بحث متقدم
                    </a>
                    <button type="reset" class="btn btn-outline-warning waves-effect waves-light">Cancel</button>
                </div>



            </div>
        </div>
        @if (@isset($appointments) && !@empty($appointments) && count($appointments) > 0)
        @foreach ($appointments as $info)
            <div class="card mb-3" style="height: auto; min-height: 150px;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 me-3">{{ $info->client->trade_name }}</h5>
                        <span id="status-badge-{{ $info->id }}" class="badge {{ $info->status == 1 ? 'bg-warning' : ($info->status == 2 ? 'bg-success' : ($info->status == 3 ? 'bg-danger' : 'bg-info')) }}" style="margin-right: 20px">
                            {{ $info->status == 1 ? 'قيد الانتظار' : ($info->status == 2 ? 'مكتمل' : ($info->status == 3 ? 'ملغي' : 'معاد جدولته')) }}
                        </span>

                        <!-- In your dropdown menu -->

                    </div>
                    <div class="col-md-2 text-end">
                        <div class="btn-group">
                    {{-- <div class="dropdown">
                        <button class="btn bg-gradient-info fa fa-ellipsis-v" type="button" id="dropdownMenuButton{{ $info->id }}" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton{{ $info->id }}">
                            <li>
                                <a class="dropdown-item" href="{{ route('appointments.show', $info->id) }}">
                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('appointments.edit', $info->id) }}">
                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                </a>
                            </li>
                            <form action="{{ route('appointments.update-status', $info->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="1">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-clock me-2 text-warning"></i>تم جدولته
                                </button>
                            </form>

                            <form action="{{ route('appointments.update-status', $info->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="2">
                                <input type="hidden" name="auto_delete" value="1">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-check me-2 text-success"></i>تم
                                </button>
                            </form>

                            <!-- For ignored status -->
                            <form action="{{ route('appointments.update-status', $info->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="3">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-times me-2 text-danger"></i>صرف النظر عنه
                                </button>
                            </form>
                            <form action="{{ route('appointments.update-status', $info->id) }}" method="POST" class="d-inline">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="4">
                                <button type="submit" class="dropdown-item">
                                    <i class="fa fa-redo me-2 text-info"></i>تم جدولته مجددا
                                </button>
                            </form>

                            <li>
                                <form action="{{ route('appointments.destroy', $info->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger" onclick="return confirm('هل أنت متأكد من حذف هذا الموعد؟')">
                                        <i class="fa fa-trash me-2"></i>حذف
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div> --}}
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">

                            <div class="mb-2">
                                <strong>اسم العميل التجاري:</strong> {{ $info->client->trade_name }}
                            </div>
                            <div class="mb-2">
                                <strong>نوع العميل:</strong>
                                @if($info->client->client_type == 1)
                                    عميل عادي
                                @elseif($info->client->client_type == 2)
                                    عميل VIP
                                @else
                                    غير محدد
                                @endif
                            </div>
                            <div class="mb-2">
                                <strong>رقم الهاتف:</strong> {{ $info->client->phone }}
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-2">
                                <strong>التاريخ:</strong> {{ \Carbon\Carbon::parse($info->appointment_date)->format('Y-m-d') }}
                            </div>
                            <div class="mb-2">
                                <strong>الوقت:</strong> {{ $info->time }}
                            </div>
                            <div class="mb-2">
                                <strong>المدة:</strong> {{ $info->duration ?? 'غير محدد' }}
                            </div>
                            <div class="mb-2">
                                <strong>الموظف:</strong> {{ $info->employee ? $info->employee->name : 'غير محدد' }}
                            </div>
                        </div>
                    </div>
                    @if($info->notes)
                        <div class="mt-3 bg-light p-2 rounded">
                            <strong>ملاحظات:</strong>
                            <p class="mb-0">{{ $info->anotes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="alert alert-info text-center">
            <p class="mb-0">لا توجد مواعيد مسجلة حالياً</p>
        </div>
    @endif
    </div>
    </div>
    </div>




@endsection


@section('scripts')
<script src="{{ asset('assets/js/applmintion.js') }}"></script>


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

@endsection

