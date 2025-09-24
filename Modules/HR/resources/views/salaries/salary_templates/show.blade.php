@extends('master')

@section('title')
    عرض القالب الراتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض قالب الراتب </h2>
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
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">

                    <div class="avatar avatar-md bg-light-primary">
                        <span class="avatar-content fs-4">{{ Str::substr($salaryTemplates->name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">{{ $salaryTemplates->name }}</h5>
                                <small class="text-muted">#{{ $salaryTemplates->id }}</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                <small class="text-success">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>
                                    <small class="text-success">
                                        @if ($salaryTemplates->status == 1)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-danger">غير نشط</span>
                                        @endif
                                    </small> </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">

                    <button class="btn btn-icon btn-outline-primary">
                        <i class="fa fa-chevron-up"></i>
                    </button>
                    <div class="vr mx-1"></div>
                    <button class="btn btn-icon btn-outline-primary">
                        <i class="fa fa-chevron-down"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">

            <div class="vr"></div>

            <a href="{{ route('SalaryTemplates.edit', $salaryTemplates->id) }}"
                class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <a href="#"
            class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
            data-bs-toggle="modal" data-bs-target="#modal_DELETE{{ $salaryTemplates->id }}">
            حذف <i class="fa fa-trash ms-1"></i>
        </a>

        <!-- Modal Delete -->
        <div class="modal fade text-left" id="modal_DELETE{{ $salaryTemplates->id }}" tabindex="-1" role="dialog"
            aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #EA5455 !important;">
                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف
                            {{ $salaryTemplates->name }}</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>
                            هل انت متاكد من انك تريد الحذف ؟
                        </strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light waves-effect waves-light"
                            data-bs-dismiss="modal">الغاء</button>
                        <form action="{{ route('SalaryTemplates.destroy', $salaryTemplates->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger waves-effect waves-light">تأكيد</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

            <a href="#"
                class="btn {{ $salaryTemplates->status == 1 ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_TOGGLE_STATUS">
                {{ $salaryTemplates->status == 1 ? 'تعطيل' : 'تنشيط' }}
                <i class="fa {{ $salaryTemplates->status == 1 ? 'fa-ban' : 'fa-check' }} ms-1"></i>
            </a>
            <!-- Modal Toggle Status -->
            <div class="modal fade" id="modal_TOGGLE_STATUS" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header {{ $salaryTemplates->status == 1 ? 'bg-danger' : 'bg-success' }}">
                            <h5 class="modal-title text-white">تأكيد
                                {{ $salaryTemplates->status == 1 ? 'التعطيل' : 'التنشيط' }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('SalaryItems.toggleStatus', $salaryTemplates->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <p>هل أنت متأكد من {{ $salaryTemplates->status == 1 ? 'تعطيل' : 'تنشيط' }} قالب الراتب
                                    "{{ $salaryTemplates->name }}"؟</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit"
                                    class="btn {{ $salaryTemplates->status == 1 ? 'btn-danger' : 'btn-success' }}">
                                    تأكيد {{ $salaryTemplates->status == 1 ? 'التعطيل' : 'التنشيط' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="row g-0">
                        <div class="col-12">
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2">
                                <h6 class="mb-0">معلومات قالب الراتب</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الوصف</th>
                                            <th>دورة القبض</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $salaryTemplates->description }}</td>
                                            <td>

                                                @if ($salaryTemplates->receiving_cycle == 1)
                                                    <span class="badge badge-success">شهري </span>
                                                @elseif ($salaryTemplates->receiving_cycle == 2)
                                                    <span class="badge badge-success">اسبوعي </span>
                                                @elseif ($salaryTemplates->receiving_cycle == 3)
                                                    <span class="badge badge-success">سنوي </span>
                                                @elseif ($salaryTemplates->receiving_cycle == 4)
                                                    <span class="badge badge-success">ربع سنوي </span>
                                                @else
                                                    <span class="badge badge-success">مرة كل اسبوع </span>
                                                @endif
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- قسم مستحق -->
                        <div class="col-12">
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2 mt-4">
                                <h6 class="mb-0">مستحق</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>بند الراتب</th>
                                            <th>الصيغة الحسابية</th>
                                            <th class="text-end">المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($addition as $item)
                                            <tr>
                                                <td>{{ $item->name }}</td>
                                                <td>{{ $item->calculation_formula ?: '-' }}</td>
                                                <td class="text-end">{{ number_format($item->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- قسم مستقطع -->
                        <div class="col-12">
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2 mt-4">
                                <h6 class="mb-0">مستقطع</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>بند الراتب</th>
                                            <th>الصيغة الحسابية</th>
                                            <th class="text-end">المبلغ</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($deductionItems as $deditem)
                                            <tr>
                                                <td>{{ $deditem->name }}</td>
                                                <td>{{ $deditem->calculation_formula ?: '-' }}</td>
                                                <td class="text-end">{{ number_format($deditem->amount, 2) }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
    </div>
@endsection
