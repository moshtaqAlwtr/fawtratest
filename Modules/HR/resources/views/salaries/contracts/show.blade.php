@extends('master')

@section('title')
    عرض العقد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض عقد الراتب</h2>
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
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-md bg-light-primary">
                        <span class="avatar-content fs-4">{{ Str::substr($contract->employee->full_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <h4 class="mb-0">{{ $contract->employee->full_name }}</h4>
                        <small class="text-success">
                            @if ($contract->employee->status == 1)
                                <span class="badge badge-success">نشط</span>
                            @else
                                <span class="badge badge-danger">غير نشط</span>
                            @endif
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE1">
                حذف <i class="fa fa-trash ms-1"></i>
            </a>
            <div class="vr"></div>
            <a href=""
                class="btn btn-outline-primary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                تعديل <i class="fa fa-edit ms-1"></i>
            </a>
            <div class="vr"></div>

            <!-- Prints Dropdown -->

            <div class="vr"></div>

            <!-- Actions Dropdown -->
            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" id="actionsDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    الإجراءات <i class="dropdown-toggle"></i>
                </button>
                <ul class="dropdown-menu py-1" aria-labelledby="actionsDropdown">
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-file-alt me-2 text-secondary"></i>مسودة</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-sync me-2 text-primary"></i>استبدل</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-pause me-2 text-warning"></i>ايقاف</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-redo me-2 text-info"></i>تجديد</a></li>
                    <li><a class="dropdown-item d-flex align-items-center" href="#"><i
                                class="fa fa-times me-2 text-danger"></i>إلغاء</a></li>
                </ul>
            </div>
            <div class="dropdown d-inline-block  ">
                <button class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                    style="min-width: 90px;" type="button" id="printDropdown" data-bs-toggle="dropdown"
                    aria-expanded="false">
                    المطبوعات <i class="dropdown-toggle"></i>
                </button>
                <ul class="dropdown-menu py-1" aria-labelledby="printDropdown">
                    <li><a class="dropdown-item d-flex align-items-center" href="{{ route('Contracts.print', $contract->id) }}"><i
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
            <div class="btn-group">
                <a href="{{ route('Contracts.print1', $contract->id) }}" class="btn btn-info btn-sm" target="_blank">
                    <i class="fas fa-eye"></i> معاينة
                </a>
                <a href="{{ route('Contracts.print', $contract->id) }}" class="btn btn-primary btn-sm" target="_blank">
                    <i class="fas fa-print"></i> PDF طباعة
                </a>
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
                    <div class="row">
                        <div class="col-md-12 mb-4">


                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">معلومات العقد</h5>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <tbody>
                                        <tr>
                                            <th class="">المسمى الوظيفي:</th>
                                            <td>{{ $contract->jobTitle->name ?? '--' }}</td>
                                            <th class="">المستوى الوظيفي:</th>
                                            <td>{{ $contract->jobLevel->name ?? '--' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">العقد الأساسي:</th>
                                            <td>{{ $contract->parent_contract_id ?? '--' }}</td>
                                            <th class="">الوصف:</th>
                                            <td>{{ $contract->description ?? '--' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">تاريخ البدء:</th>
                                            <td>{{ $contract->start_date ?? '--' }}</td>
                                            <th class="">تاريخ الانتهاء:</th>
                                            <td>{{ $contract->end_date ?? '--' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">تاريخ الالتحاق:</th>
                                            <td>{{ $contract->join_date ?? '--' }}</td>
                                            <th class="">تاريخ نهاية مدة الاختبار:</th>
                                            <td>{{ $contract->probation_end_date ?? '--' }}</td>
                                        </tr>
                                        <tr>
                                            <th class="">تاريخ توقيع العقد:</th>
                                            <td colspan="3">{{ $contract->contract_date ?? '--' }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">بيانات المرتب</h5>
                            </div>


                            <div class="p-3 rounded mb-4">
                                <div class="d-flex justify-content-between">
                                    <div class="d-flex gap-2">
                                        <span class="text-muted">قالب الراتب:</span>
                                        <span>{{ $contract->salaryTemplate->name ?? '--' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="text-muted">العملة:</span>
                                        <span>{{ $contract->currency ?? '--' }}</span>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <span class="text-muted">دورة القبض:</span>
                                        @if ($contract->receiving_cycle == 1)
                                            <span class="badge badge-success">شهري</span>
                                        @elseif ($contract->receiving_cycle == 2)
                                            <span class="badge badge-danger">اسبوعي</span>
                                        @elseif ($contract->receiving_cycle == 3)
                                            <span class="badge badge-danger">سنوي</span>
                                        @elseif ($contract->receiving_cycle == 4)
                                            <span class="badge badge-danger">ربع سنوي</span>
                                        @else
                                            <span class="badge badge-danger">مرة كل اسبوع</span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <!-- مستحق -->
                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0"> مستحق</h5>
                            </div>
                            <div class="table-responsive mb-4">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>بند الراتب</th>
                                            <th>الصيغة الحسابية</th>
                                            <th class="text-end">المبلغ</th>
                                        </tr>
                                    </thead>
                                    @foreach ($additionItems as $addition)
                                        <tbody>
                                            <tr>

                                                <td>{{ $addition->name }}</td>
                                                <td>{{ $addition->calculation_formula }}</td>
                                                <td class="text-end">{{ number_format($addition->amount, 2) }}</td>
                                            </tr>

                                        </tbody>
                                    @endforeach
                                </table>
                            </div>

                            <!-- مستقطع -->
                            <div style="background-color: #f8f9fa;" class="p-2 rounded mb-2">
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
                                    @foreach ($deductionItems as $deductionItem)
                                        <tbody>

                                            <tr>
                                                <td>{{ $deductionItem->name }}</td>
                                                <td>{{ $deductionItem->calculation_formula }}</td>
                                                <td class="text-end">{{ $deductionItem->amount }}</td>
                                            </tr>
                                        </tbody>
                                    @endforeach
                                </table>
                            </div>

                            <div style="background-color: #f8f9fa;"
                                class="d-flex justify-content-between align-items-center p-2 rounded mb-3">
                                <h5 class="mb-0">المرفقات</h5>
                                @if (isset($contract->attachments) && $contract->attachments->isNotEmpty())
                                    <span class="badge bg-info">{{ $contract->attachments->count() }} مرفقات</span>
                                @endif
                            </div>
                            <div class="p-3 rounded mb-4 border">
                                @if (isset($contract->attachments) && $contract->attachments->isNotEmpty())
                                    <div class="row">
                                        @foreach ($contract->attachments as $attachment)
                                            <div class="col-md-4 mb-2">
                                                <div class="d-flex align-items-center">
                                                    <i class="fas fa-file me-2 text-primary"></i>
                                                    <a href="{{ asset('storage/' . $attachment->path) }}" target="_blank"
                                                        class="text-decoration-none">
                                                        {{ $attachment->name }}
                                                    </a>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted mb-0 text-center">لا توجد مرفقات</p>
                                @endif
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
