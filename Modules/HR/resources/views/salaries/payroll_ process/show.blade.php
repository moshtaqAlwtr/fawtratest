@extends('master')

@section('title')
    عرض مسيرالراتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض مسير الراتب </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-1">
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <span class="fs-6">Payrun (2024-12-30) SAR #1</span>
                    <span class="text-muted px-2 fs-6">|</span>
                    <span class="text-muted fs-6">تم إنشاؤها</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-primary d-inline-flex align-items-center justify-content-center px-3"
                        style="min-width: 120px;">
                        موافقة الكل <i class="fa fa-check ms-1"></i>
                    </button>
                    <a href="{{ route('ancestor.index') }}"
                        class="btn btn-outline-success d-inline-flex align-items-center justify-content-center px-3"
                        style="min-width: 120px;">
                        أضف قسيمة الراتب <i class="fa fa-plus ms-1"></i>
                    </a>
                    <div class="vr"></div>
                    <button class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center px-3">
                        <i class="fa fa-chevron-right"></i>
                    </button>
                    <button class="btn btn-outline-secondary d-inline-flex align-items-center justify-content-center px-3">
                        <i class="fa fa-chevron-left"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">

            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
            <div class="vr"></div>

            <a href="#"
                class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تصدير قسائم الرواتب <i class="fa fa-money-bill ms-1"></i>
            </a>

            <div class="vr"></div>

            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-dark btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" id="printDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    المطبوعات <i class="fa fa-print ms-1"></i>
                </button>
                <ul class="dropdown-menu py-1" aria-labelledby="printDropdown">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 1</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 2</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-primary"></i>Contract Layout 3</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 1 عقد</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 2 عقد</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-pdf me-2 text-danger"></i>نموذج 3 عقد</a></li>
                </ul>
            </div>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#installments" role="tab">
                        <span>قسائم الراتب</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#payments" role="tab">
                        <span>المدفوعات </span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table">
                            <tbody>
                                <tr>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">تفاصيل مسير الراتب:</label>
                                            <div>Payrun (2024-12-30) SAR #1</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ التسجيل:</label>
                                            <div>30/12/2024</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ البدء:</label>
                                            <div>02/12/2024</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">تاريخ الانتهاء:</label>
                                            <div>30/12/2025</div>
                                        </div>
                                    </td>
                                    <td class="text-start" style="width: 50%">
                                        <div class="mb-2">
                                            <label class="text-muted">تم إنشاؤها:</label>
                                            <div>0 موافق عليه / 0 مدفوعة</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">التحقق من الحضور:</label>
                                            <div>لا</div>
                                        </div>
                                        <div class="mb-2">
                                            <label class="text-muted">الموظفين:</label>
                                            <div>محمد الدريسي 5#</div>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- تبويب قسائم الرواتب  -->

                <div class="tab-pane" id="installments" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3" style="background-color: #f8f9fa">
                        <h5 class="mb-0">قسائم الرواتب</h5>
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">عرض الجميع: 1 من 1</span>
                        </div>
                    </div>



                    <div class="card">
                        <div class="card-body">

                            <table class="table">
                                <thead>
                                    <tr>
                                        <th class="text-end">المعرف</th>
                                        <th class="text-end">إسم الموظف</th>
                                        <th class="text-end">الفترة</th>
                                        <th class="text-end">اجمالي المبلغ</th>
                                        <th class="text-end">الحالة</th>
                                        <th class="text-end" style="width: 10%">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td>#1</td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <span class="avatar avatar-xs" style="background-color: #6c757d">م</span>
                                                محمد الدريسي #5
                                            </div>
                                        </td>
                                        <td>
                                            <div>02/12/2024 - 30/12/2024</div>
                                        </td>
                                        <td>4,267 ر.س</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="fa fa-circle text-secondary me-1" style="font-size: 8px;"></i>
                                                تم إنشاؤها
                                            </div>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                        <li>
                                                            <a href="{{ route('salarySlip.show', 1) }}" class="dropdown-item" href="#">
                                                                <i class="fa fa-eye me-2 text-primary"></i> عرض
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('salarySlip.edit', 1) }}"
                                                                class="dropdown-item text-warning">
                                                                <i class="fa fa-edit me-2"></i> تعديل
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-success" href="#"
                                                                data-toggle="modal" data-target="#modal_APPROVE">
                                                                <i class="fa fa-check-circle me-2"></i> موافقة
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                data-toggle="modal" data-target="#modal_DELETE">
                                                                <i class="fa fa-trash me-2"></i> حذف
                                                            </a>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>

                                        </td>
                                        <!-- Modal delete -->
                                        {{-- <div class="modal fade text-left" id="modal_DELETE{{ $title->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header" style="background-color: #EA5455 !important;">
                                                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $title->name }}</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong>
                                                            هل انت متاكد من انك تريد الحذف ؟
                                                        </strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                                        <a href="{{ route('JobTitles.delete', $title->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
                                        <!--end delete-->
                                    </tr>

                                </tbody>
                            </table>
                            {{-- @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">
                                لا توجد مسميات وظيفية مضافة حتى الان !!
                            </p>
                        </div>
                    @endif --}}
                            {{-- {{ $shifts->links('pagination::bootstrap-5') }} --}}
                        </div>
                    </div>

                    <div class="d-flex justify-content-end align-items-center mt-3" style="background-color: #f8f9fa">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-muted">عرض الجميع: 1 من 1</span>
                        </div>
                    </div>
                </div>
                <!-- المدفوعات  -->

                <div class="tab-pane" id="payments" role="tabpanel">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-center align-items-center py-5">
                                <div class="text-center">
                                    <h5 class="text-muted mb-0">لا يوجد مدفوعات مسير الراتب أضيفت حتى الآن</h5>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                        <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
