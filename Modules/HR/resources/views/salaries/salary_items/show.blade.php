@extends('master')

@section('title')
    عرض بنود الراتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض بنود الراتب </h2>
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
                        <span class="avatar-content fs-4">ت</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">{{ $salaryItem->name }}</h5>
                                <small class="text-muted"># {{ $salaryItem->id }}</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                <small class="text-success">
                                    @if ($salaryItem->status == 1)
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-danger">غير نشط</span>
                                    @endif
                                </small>
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

            <a href="{{ route('SalaryItems.edit', 1) }}"
                class="btn btn-outline-warning btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE1">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>

            <a href="#"
                class="btn {{ $salaryItem->status == 1 ? 'btn-outline-danger' : 'btn-outline-success' }} btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_TOGGLE_STATUS">
                {{ $salaryItem->status == 1 ? 'تعطيل' : 'تنشيط' }}
                <i class="fa {{ $salaryItem->status == 1 ? 'fa-ban' : 'fa-check' }} ms-1"></i>
            </a>

            <!-- Modal Toggle Status -->
            <div class="modal fade" id="modal_TOGGLE_STATUS" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header {{ $salaryItem->status == 1 ? 'bg-danger' : 'bg-success' }}">
                            <h5 class="modal-title text-white">تأكيد {{ $salaryItem->status == 1 ? 'التعطيل' : 'التنشيط' }}
                            </h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <form action="{{ route('SalaryItems.toggleStatus', $salaryItem->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="modal-body">
                                <p>هل أنت متأكد من {{ $salaryItem->status == 1 ? 'تعطيل' : 'تنشيط' }} بند الراتب
                                    "{{ $salaryItem->name }}"؟</p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                <button type="submit"
                                    class="btn {{ $salaryItem->status == 1 ? 'btn-danger' : 'btn-success' }}">
                                    تأكيد {{ $salaryItem->status == 1 ? 'التعطيل' : 'التنشيط' }}
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
                                <h6 class="mb-0">معلومات البند</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>الوصف</th>
                                            <th>الشرط</th>
                                            <th class="text-end">النوع</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $salaryItem->description }}</td>
                                            <td>{{ $salaryItem->condition }}</td>
                                            <td>
                                                @if ($salaryItem->type == 1)
                                                    <span class="badge badge-success">مستحق</span>
                                                @else
                                                    <span class="badge badge-danger">مستقطع</span>
                                                @endif
                                            </td>

                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2 mt-4">
                                <h6 class="mb-0">قيمة البند</h6>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>المبلغ </th>
                                            <th>الصيغة الحسابية </th>
                                            <th class="text-end">الحساب الافتراضي </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>{{ $salaryItem->amount ?? '-' }}</td>
                                            <td>{{ $salaryItem->calculation_formula ?? '-' }} </td>
                                            <td class="text-end">{{ $salaryItem->chartOfAccount->name ?? '-' }}</td>
                                        </tr>
                                    </tbody>
                                </table>

                                <div class="mt-4">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" id="referenceValue" disabled
                                            value="{{ $salaryItem->reference_value }}">
                                        <label class="form-check-label" for="referenceValue">قيمة مرجعية فقط</label>
                                    </div>
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
    </div>
@endsection
