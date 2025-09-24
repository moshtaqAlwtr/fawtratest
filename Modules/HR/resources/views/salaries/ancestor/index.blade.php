@extends('master')

@section('title')
    سلفة الراتب
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">السلفة الراتب</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active"> عرض </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="btn-group">
                            @if ($ancestors->onFirstPage())
                                <button class="btn btn-light border" disabled>
                                    <i class="fa fa-chevron-right"></i>
                                </button>
                            @else
                                <a href="{{ $ancestors->previousPageUrl() }}" class="btn btn-light border">
                                    <i class="fa fa-chevron-right"></i>
                                </a>
                            @endif

                            @if ($ancestors->hasMorePages())
                                <a href="{{ $ancestors->nextPageUrl() }}" class="btn btn-light border">
                                    <i class="fa fa-chevron-left"></i>
                                </a>
                            @else
                                <button class="btn btn-light border" disabled>
                                    <i class="fa fa-chevron-left"></i>
                                </button>
                            @endif
                        </div>

                        <span class="mx-2">
                            {{ $ancestors->firstItem() }} - {{ $ancestors->lastItem() }} من {{ $ancestors->total() }}
                        </span>

                        <div class="input-group" style="width: 150px">
                            <input type="text" class="form-control text-center"
                                value="صفحة {{ $ancestors->currentPage() }} من {{ $ancestors->lastPage() }}">
                        </div>

                        <div class="dropdown">
                            <button class="btn btn-gradient-secondary border dropdown-toggle" type="button"
                                data-bs-toggle="dropdown">
                                الإجراءات
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ $request->fullUrlWithQuery(['per_page' => 10]) }}">10
                                        لكل صفحة</a></li>
                                <li><a class="dropdown-item" href="{{ $request->fullUrlWithQuery(['per_page' => 25]) }}">25
                                        لكل صفحة</a></li>
                                <li><a class="dropdown-item" href="{{ $request->fullUrlWithQuery(['per_page' => 50]) }}">50
                                        لكل صفحة</a></li>
                                <li><a class="dropdown-item"
                                        href="{{ $request->fullUrlWithQuery(['per_page' => 100]) }}">100 لكل صفحة</a></li>
                            </ul>
                        </div>

                        <div class="btn-group">
                            <button class="btn btn-gradient-info border">
                                <i class="fa fa-table"></i>
                            </button>
                        </div>
                    </div>
                    <div class="d-flex" style="gap: 15px">
                        <a href="{{ route('ancestor.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-2"></i>
                            أضف سلفة
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <form class="form" method="GET" action="{{ route('ancestor.index') }}">
            <div class="card">
                <div class="card-body">
                    <div class="form-body row">
                        <div class="form-group col-md-4">
                            <label for="advance_search">السلف</label>
                            <input type="text" id="advance_search" class="form-control" placeholder="البحث بواسطة السلف"
                                name="advance_search" value="{{ request('advance_search') }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label>إختر فترة القسط</label>
                            <select class="form-control" name="payment_rate">
                                <option value="">إختر فترة القسط</option>
                                @foreach ($paymentRates as $key => $rate)
                                    <option value="{{ $key }}"
                                        {{ request('payment_rate') == $key ? 'selected' : '' }}>
                                        {{ $rate }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="employee_search">البحث بواسطة الموظف</label>


                            <select class="form-control select2"name="employee_search"
                                value="{{ request('employee_search') }}">
                                <option>اختر الموظف</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-body row">
                        <div class="form-group col-md-4">
                            <label>الدفعة القادمة (من)</label>
                            <input type="date" class="form-control text-start" name="next_payment_from"
                                value="{{ request('next_payment_from') }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label>الدفعة القادمة (إلى)</label>
                            <input type="date" class="form-control text-start" name="next_payment_to"
                                value="{{ request('next_payment_to') }}">
                        </div>

                        <div class="form-group col-md-4">
                            <label>الحالة</label>
                            <select class="form-control" name="status">
                                <option value="">إختر الحالة</option>
                                @foreach ($statuses as $key => $status)
                                    <option value="{{ $key }}"
                                        {{ request('status') == $key ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="collapse {{ request()->hasAny(['branch_id', 'tag']) ? 'show' : '' }}"
                        id="advancedSearchForm">
                        <div class="form-body row">
                            <div class="form-group col-md-4">
                                <label>اختر فرع</label>
                                <select class="form-control" name="branch_id">
                                    <option value="">كل الفروع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label>اختر وسم</label>
                                <input type="text" class="form-control" name="tag">


                            </div>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary mr-1">
                            <i class="fa fa-search"></i> بحث
                        </button>

                        <a class="btn btn-outline-secondary ml-2 mr-2" data-toggle="collapse"
                            data-target="#advancedSearchForm">
                            <i class="bi bi-sliders"></i> بحث متقدم
                        </a>

                        <a href="{{ route('ancestor.index') }}" class="btn btn-outline-warning">
                            <i class="fa fa-refresh"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </div>
        </form>

        <div class="card">
            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>معرف السلفة</th>
                            <th>موظف</th>
                            <th>الأقساط المدفوعة</th>
                            <th>الدفعة القادمة</th>

                            <th>وسوم</th>
                            <th>ترتيب بواسطة</th>

                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ancestors as $ancestor)
                            @php
                                $totalPaid = $ancestor->payments->where('status', 'paid')->sum('amount');
                                $paidInstallments = $ancestor->payments->where('status', 'paid')->count();
                                $progressPercentage =
                                    $ancestor->amount > 0 ? ($totalPaid / $ancestor->amount) * 100 : 0;
                            @endphp
                            <tr>
                                <td>{{ $ancestor->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="btn btn-info btn-sm ms-2"
                                            style="background-color: #0dcaf0; border-color: #0dcaf0;">
                                            {{ mb_substr($ancestor->employee->full_name ?? 'غ', 0, 1, 'UTF-8') }}
                                        </div>
                                        <span class="text-primary" style="color: #0d6efd !important;">
                                            {{ $ancestor->employee->name ?? '--' }}
                                            <span style="margin-right: 4px;">#{{ $ancestor->employee->id }}</span>
                                        </span>
                                    </div>
                                </td>
                                @php
                                    $currency = $account_setting->currency ?? 'SAR';
                                    $currencySymbol =
                                        $currency == 'SAR' || empty($currency)
                                            ? '<img src="' .
                                                asset('assets/images/Saudi_Riyal.svg') .
                                                '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                            : $currency;
                                @endphp

                                <!-- عمود الأقساط المدفوعة -->
                                <td>
                                    <div class="mb-2">
                                        <label class="text-muted">حالة السداد:</label>
                                        <div class="mt-1">
                                            <div style="width: fit-content;">
                                                <div style="font-weight: bold; margin-bottom: 4px; position: relative;">
                                                    <div
                                                        style="border-bottom: 2px solid #ffc107; width: {{ $progressPercentage }}%; position: absolute; bottom: -2px;">
                                                    </div>
                                                    <div style="border-bottom: 1px solid #dee2e6; width: fit-content;">
                                                        {{ number_format($ancestor->amount, 2) }} {!! $currencySymbol !!}
                                                        (إجمالي السلفة)
                                                    </div>
                                                </div>
                                                <div style="color: #666; font-size: 0.9em;">
                                                    <span>{{ number_format($totalPaid, 2) }} {!! $currencySymbol !!} مدفوعة
                                                        ({{ $paidInstallments }} قسط)</span>
                                                    <span class="mx-2">|</span>
                                                    <span>{{ number_format($ancestor->amount - $totalPaid, 2) }}
                                                        {!! $currencySymbol !!} متبقي</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if ($ancestor->next_payment)
                                        {{ $ancestor->next_payment->due_date }}
                                        <span class="badge badge-warning">قادم</span>
                                    @else
                                        @if ($ancestor->payments->where('status', 'unpaid')->isEmpty())
                                            <span class="badge badge-success">مكتمل</span>
                                        @else
                                            <span class="badge badge-danger">متأخر</span>
                                        @endif
                                    @endif
                                </td>


                                <td></td>
                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button" id="dropdownMenuButton{{ $ancestor->id }}"
                                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            </button>
                                            <div class="dropdown-menu"
                                                aria-labelledby="dropdownMenuButton{{ $ancestor->id }}">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('ancestor.show', $ancestor->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('ancestor.edit', $ancestor->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        data-toggle="modal"
                                                        data-target="#delete-modal-{{ $ancestor->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-success" href="#"
                                                        data-toggle="modal"
                                                        data-target="#copy-modal-{{ $ancestor->id }}">
                                                        <i class="fa fa-copy me-2"></i>نسخ
                                                    </a>
                                                </li>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Delete -->
                                    <div class="modal fade" id="delete-modal-{{ $ancestor->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">تأكيد الحذف</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>هل أنت متأكد من حذف هذه السلفة؟</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <form action="{{ route('ancestor.destroy', $ancestor->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger">حذف</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Modal Copy -->
                                    <div class="modal fade" id="copy-modal-{{ $ancestor->id }}" tabindex="-1"
                                        role="dialog">
                                        <div class="modal-dialog" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title">نسخ السلفة</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>هل تريد نسخ هذه السلفة؟</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-dismiss="modal">إلغاء</button>
                                                    <a href="" class="btn btn-success">نسخ</a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center">لا توجد سلف</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>




    @endsection
