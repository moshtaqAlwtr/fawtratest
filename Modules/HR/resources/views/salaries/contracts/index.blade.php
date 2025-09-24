@extends('master')

@section('title')
    العقود
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
                    <h2 class="content-header-title float-left mb-0">العقود</h2>
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
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">

                    </div>

                    <div class="d-flex align-items-center gap-2">
                        <nav aria-label="Page navigation">
                            <ul class="pagination pagination-sm mb-0">
                                <li class="page-item">
                                    <button class="btn btn-sm btn-outline-secondary px-2" aria-label="Previous">
                                        <i class="fa fa-angle-right"></i>
                                    </button>
                                </li>
                                <li class="page-item mx-2">
                                    <span class="text-muted">صفحة 1 من 1</span>
                                </li>
                                <li class="page-item">
                                    <button class="btn btn-sm btn-outline-secondary px-2" aria-label="Next">
                                        <i class="fa fa-angle-left"></i>
                                    </button>
                                </li>
                            </ul>
                        </nav>

                        <span class="text-muted mx-2">1-1 من 1</span>

                        <a href="{{ route('Contracts.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-1"></i>
                            أضف عقد
                        </a>
                    </div>
                </div>
            </div>
        </div>


        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">

                    <span class="hide-button-text">

                        بحث وتصفية
                    </span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">اخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse"
                        data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                </div>

            </div>
            <div class="card-body">
                <form class="form" id="searchForm" method="GET" action="{{ route('Contracts.index') }}">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <select name="employee_search" class="form-control">
                                <option value="">البحث بواسطة إسم الموظف أو الرقم التعريفي</option>
                                @foreach ($employees as $employee)
                                    <option value="{{ $employee->id }}"
                                        {{ request('employee_search') == $employee->id ? 'selected' : '' }}>
                                        {{ $employee->full_name }} - {{ $employee->employee_id }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-control">
                                <option value="">إختر النوع</option>
                                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>فعال</option>
                                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير فعال</option>
                            </select>
                        </div>
                        <div class="col advanced-field" style="display: none;">
                            <input type="date" name="end_date_to" class="form-control" placeholder="تاريخ الانتهاء (إلى)"
                                value="{{ request('end_date_to') }}">
                        </div>
                        <div class="col advanced-field" style="display: none;">
                            <input type="date" name="end_date_from" class="form-control"
                                placeholder="تاريخ الانتهاء (من)" value="{{ request('end_date_from') }}">
                        </div>
                        <div class="col-md-3 advanced-field" style="display: none;">
                            <select name="job_title_id" class="form-control">
                                <option value="">إختر المسمى الوظيفي</option>
                                @foreach ($jobTitles as $title)
                                    <option value="{{ $title->id }}"
                                        {{ request('job_title_id') == $title->id ? 'selected' : '' }}>
                                        {{ $title->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="collapse" id="advancedSearchForm">
                        <div class="row g-3 mt-2">
                            <div class="col-md-3">
                                <select name="department_id" class="form-control">
                                    <option value="">إختر قسم</option>
                                    @foreach ($departments as $dept)
                                        <option value="{{ $dept->id }}"
                                            {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="contract_component" class="form-control"
                                    placeholder="contract component" value="{{ request('contract_component') }}">
                            </div>

                            <div class="col-md-1-5">
                                <input type="date" name="start_date_from" class="form-control"
                                    value="{{ request('start_date_from') }}">
                            </div>
                            <div class="col-md-1-5">
                                <input type="date" name="start_date_to" class="form-control"
                                    value="{{ request('start_date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="salary_item_id" class="form-control">
                                    <option value="">إختر بند الراتب</option>
                                    @foreach ($salaryItems as $item)
                                        <option value="{{ $item->id }}"
                                            {{ request('salary_item_id') == $item->id ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-3">
                                <input type="date" name="join_date_from" class="form-control"
                                    value="{{ request('join_date_from') }}">
                            </div>
                            <div class="col-md-3">
                                <input type="date" name="join_date_to" class="form-control"
                                    value="{{ request('join_date_to') }}">
                            </div>
                            <div class="col-md-3">
                                <select name="salary_temp_id" class="form-control">
                                    <option value="">إختر قالب الراتب</option>
                                    @foreach ($salaryTemplates as $template)
                                        <option value="{{ $template->id }}"
                                            {{ request('salary_temp_id') == $template->id ? 'selected' : '' }}>
                                            {{ $template->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-1-5">
                                <input type="date" name="probation_date_from" class="form-control"
                                    value="{{ request('probation_date_from') }}">
                            </div>
                            <div class="col-md-1-5">
                                <input type="date" name="probation_date_to" class="form-control"
                                    value="{{ request('probation_date_to') }}">
                            </div>

                            <div class="col-md-3">
                                <select name="employee_status" class="form-control">
                                    <option value="">اختر حالة الموظف</option>
                                    <option value="active" {{ request('employee_status') == 'active' ? 'selected' : '' }}>
                                        نشط</option>
                                    <option value="inactive"
                                        {{ request('employee_status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="termination_type" class="form-control">
                                    <option value="">نوع الانتهاء</option>
                                    <option value="1" {{ request('termination_type') == '1' ? 'selected' : '' }}>
                                        استقالة</option>
                                    <option value="2" {{ request('termination_type') == '2' ? 'selected' : '' }}>
                                        إنهاء خدمة</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="created_by" class="form-control">
                                    <option value="">أضيفت بواسطة</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ request('created_by') == $user->id ? 'selected' : '' }}>
                                            {{ $user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="branch_id" class="form-control">
                                    <option value="">إختر الفروع</option>
                                    @foreach ($branches as $branch)
                                        <option value="{{ $branch->id }}"
                                            {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                                            {{ $branch->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <a href="{{ route('Contracts.index') }}" type="reset"
                            class="btn btn-outline-warning">إلغاء</a>
                    </div>
                </form>

            </div>
        </div>

        <div class="card">
            <div class="card-body">
                @if ($contracts->count() > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>رقم</th>
                                <th>موظف</th>
                                <th>تاريخ البدء</th>
                                <th>تاريخ الإنتهاء</th>
                                <th>صافي الراتب</th>
                                <th>الحالة</th>
                                <th style="width: 10%">ترتيب ...</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($contracts as $key => $contract)
                                <tr>
                                    <td>{{ $key + 1 }}</td>
                                    <td>
                                        {{ $contract->employee->full_name }}
                                        @if ($contract->employee->image)
                                            <img src="{{ asset('storage/' . $contract->employee->image) }}"
                                                class="rounded-circle" width="30" height="30">
                                        @endif
                                    </td>
                                    <td>{{ \Carbon\Carbon::parse($contract->start_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($contract->end_date)->format('d/m/Y') }}</td>
                                    <td>{{ number_format($contract->amount, 3) }} ر.س</td>
                                    <td>
                                        @if ($contract->status == 'draft')
                                            <span class="badge bg-secondary">مسودة</span>
                                        @elseif($contract->status == 'active')
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-danger">منتهي</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" id="dropdownMenuButton{{ $contract->id }}"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                </button>
                                                <div class="dropdown-menu"
                                                    aria-labelledby="dropdownMenuButton{{ $contract->id }}">
                                                    <a class="dropdown-item"
                                                        href="{{ route('Contracts.show', $contract->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('Contracts.edit', $contract->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="#"
                                                        data-toggle="modal"
                                                        data-target="#modal_DELETE{{ $contract->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Modal delete -->
                                        <div class="modal fade text-left" id="modal_DELETE{{ $contract->id }}"
                                            tabindex="-1" role="dialog" aria-labelledby="myModalLabel1"
                                            aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header"
                                                        style="background-color: #EA5455 !important;">
                                                        <h4 class="modal-title" id="myModalLabel1"
                                                            style="color: #FFFFFF">
                                                            حذف العقد
                                                        </h4>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button"
                                                            class="btn btn-light waves-effect waves-light"
                                                            data-dismiss="modal">الغاء</button>
                                                        <a href="{{ route('Contracts.destroy', $contract->id) }}"
                                                            class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">لا توجد عقود مضافة حتى الآن !!</p>
                    </div>
                @endif
            </div>
        </div>








    @endsection

    @section('css')
        <style>
            .col-md-1-5 {
                flex: 0 0 12.5%;
                max-width: 12.5%;
                padding-right: 15px;
                padding-left: 15px;
            }

            .form-control {
                margin-bottom: 10px;
            }
        </style>
    @endsection
    @section('scripts')
        <script>
            function toggleSearchText(button) {
                const buttonText = button.querySelector('.button-text');
                const advancedFields = document.querySelectorAll('.advanced-field');

                if (buttonText.textContent.trim() === 'متقدم') {
                    buttonText.textContent = 'بحث بسيط';
                    advancedFields.forEach(field => field.style.display = 'block');
                } else {
                    buttonText.textContent = 'متقدم';
                    advancedFields.forEach(field => field.style.display = 'none');
                }
            }

            function toggleSearchFields(button) {
                const searchForm = document.getElementById('searchForm');
                const buttonText = button.querySelector('.hide-button-text');
                const icon = button.querySelector('i');

                if (buttonText.textContent === 'اخفاء') {
                    searchForm.style.display = 'none';
                    buttonText.textContent = 'اظهار';
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-eye');
                } else {
                    searchForm.style.display = 'block';
                    buttonText.textContent = 'اخفاء';
                    icon.classList.remove('fa-eye');
                    icon.classList.add('fa-times');
                }
            }
        </script>
    @endsection
