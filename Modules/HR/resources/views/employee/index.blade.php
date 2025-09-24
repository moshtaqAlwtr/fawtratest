@extends('master')

@section('title')
    أدارة الموظفين
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أدارة الموظفين</h2>
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

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('employee.export_view') }}" class="btn btn-outline-info waves-effect waves-light">
                            <i class="fa fa-share-square me-2"></i> تصدير
                        </a>
                        <a href="{{ route('employee.create') }}" class="btn btn-outline-primary waves-effect waves-light">
                            <i class="fa fa-plus me-2"></i> اضافة موظف جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form class="form" method="GET" action="#">
                        <div class="form-body row">
                            <div class="form-group col-md-3">
                                <select name="employee" class="form-control">
                                    <option value="">البحث بواسطة اسم الموظف أو الرقم التعريفي</option>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}">{{ $client->trans_name }}
                                            {{ $client->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <input type="text" class="form-control" placeholder="أختر الحالة" name="status">
                            </div>

                            <div class="form-group col-md-3">
                                <select name="type" class="form-control">
                                    <option value="">أختر النوع</option>
                                    <option value="1">مستخدم</option>
                                    <option value="0">موظف</option>
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <select name="role" class="form-control">
                                    <option value="">أختر الدور الوظيفي</option>
                                    <option value="1">مدير</option>
                                    <option value="0">موظف</option>
                                </select>
                            </div>
                        </div>
                        <!-- Hidden Div -->
                        <div class="collapse" id="advancedSearchForm" style="">
                            <div class="form-body row">
                                <div class="form-group col-md-3">
                                    <select name="job_type" class="form-control">
                                        <option value="">أختر نوع الوظيفة</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="level" class="form-control">
                                        <option value="">أختر المستوى الوظيفي</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="department" class="form-control">
                                        <option value="">أختر القسم</option>
                                        <option value="1">الكل</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="manager" class="form-control">
                                        <option value="">البحث بواسطة المدير المباشر</option>
                                        <option value="1">الكل</option>
                                        <option value="0">محمد</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="position" class="form-control">
                                        <option value="">أختر المسمى الوظيفي</option>
                                        <option value="1">الكل</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="attendance_status" class="form-control">
                                        <option value="">أختر قيد الحضور</option>
                                        <option value="1">الكل</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="citizenship_status" class="form-control">
                                        <option value="">أختر حالة المواطنة</option>
                                        <option value="1">الكل</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <select name="nationality" class="form-control">
                                        <option value="">أختر الجنسية</option>
                                        <option value="1">الكل</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="from_date">إنهاء الإقامة (من)</label>
                                    <input type="date" id="from_date" class="form-control" name="from_date">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="to_date">إنهاء الإقامة (إلى)</label>
                                    <input type="date" id="to_date" class="form-control" name="to_date">
                                </div>
                            </div>

                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>

                            <a class="btn btn-outline-secondary ml-2 mr-2 waves-effect waves-light collapsed"
                                data-toggle="collapse" data-target="#advancedSearchForm" aria-expanded="false">
                                <i class="bi bi-sliders"></i> بحث متقدم
                            </a>
                            <a href="#" class="btn btn-outline-danger waves-effect waves-light">الغاء الفلترة</a>
                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">قائمة الموظفين</h4>
            </div>
            <div class="card-body">
                @if (@isset($employees) && !@empty($employees) && count($employees) > 0)
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover mb-0">
                            <thead class="thead-light">
                                <tr>
                                    <th width="20%">الاسم</th>
                                    <th width="15%">الدور الوظيفي</th>
                                    <th width="15%">المسمى الوظيفي</th>
                                    <th width="15%">قسم</th>
                                    <th width="15%">فرع</th>
                                    <th width="10%">الحالة</th>
                                    <th width="10%">إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($employees as $employee)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar mr-1">
                                                    @if ($employee->photo)
                                                        <img src="{{ asset('storage/' . $employee->photo) }}"
                                                            alt="صورة الموظف" width="40" height="40"
                                                            class="rounded-circle">
                                                    @else
                                                        <span
                                                            class="avatar-content">{{ substr($employee->full_name, 0, 1) }}</span>
                                                    @endif
                                                </div>
                                                <div>
                                                    <p class="mb-0 font-weight-bold">{{ $employee->full_name }}</p>
                                                    <small class="text-muted">#{{ $employee->id }}</small>
                                                </div>
                                            </div>س
                                        </td>
                                        <td>
                                            <p class="mb-0 font-weight-bold">
                                                {{ $employee->job_role->role_name ?? 'غير محدد' }}</p>
                                            <small
                                                class="badge badge-light-{{ $employee->employee_type == 1 ? 'primary' : 'secondary' }}">
                                                @if ($employee->employee_type == 1)
                                                    موظف
                                                @else
                                                    مستخدم
                                                @endif
                                            </small>
                                        </td>
                                        <td>{{ optional($employee->job_title)->name ?? 'غير محدد' }}</td>
                                        <td>{{ optional($employee->department)->name ?? 'غير محدد' }}</td>
                                        <td>{{ optional($employee->branch)->name ?? 'غير محدد' }}

                                        </td>
                                        <td>
                                            @if ($employee->status == 1)
                                                <span class="badge badge-pill badge-success">نشط</span>
                                            @else
                                                <span class="badge badge-pill badge-danger">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1"
                                                        type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true"aria-expanded="false"></button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('employee.show', $employee->id) }}">
                                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="#" data-toggle="modal"
                                                                data-target="#ChangePassword">
                                                                <i class="fa fa-lock me-2 text-info"></i>تغيير كلمة المرور
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item"
                                                                href="{{ route('employee.edit', $employee->id) }}">
                                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                            </a>
                                                        </li>

                                                        <li>
                                                            <a class="dropdown-item text-danger" href="#"
                                                                data-toggle="modal"
                                                                data-target="#modal_DELETE{{ $employee->id }}">
                                                                <i class="fa fa-trash me-2"></i>حذف
                                                            </a>
                                                        <li>
                                                            <a class="dropdown-item text-primary"
                                                                href="{{ route('employee.send_email', $employee->id) }}">
                                                                <i class="fa fa-paper-plane me-2"></i> ارسال بيانات الدخول
                                                                بالبريد
                                                            </a>

                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>


                                    </tr>

                                    <!-- Modal delete -->
                                    <div class="modal fade" id="modal_DELETE{{ $employee->id }}" tabindex="-1"
                                        aria-labelledby="deleteModalLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger text-white">
                                                    <h5 class="modal-title" id="deleteModalLabel">حذف الموظف</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p>هل أنت متأكد من حذف الموظف
                                                        <strong>{{ $employee->full_name }}</strong>؟</p>
                                                    <p class="text-danger">هذا الإجراء لا يمكن التراجع عنه!</p>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">إلغاء</button>
                                                    <form action="{{ route('employee.delete', $employee->id) }}"
                                                        method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger">تأكيد الحذف</button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end delete-->

                                    <!-- Modal change password -->
                                    <div class="modal fade" id="ChangePassword{{ $employee->id }}" tabindex="-1"
                                        aria-labelledby="changePasswordLabel" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <div class="modal-header bg-primary text-white">
                                                    <h5 class="modal-title" id="changePasswordLabel">تغيير كلمة المرور
                                                    </h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <form action="{{ route('employee.updatePassword', $employee->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="password{{ $employee->id }}"
                                                                class="form-label">كلمة المرور الجديدة</label>
                                                            <input type="password" class="form-control"
                                                                id="password{{ $employee->id }}" name="password"
                                                                required>
                                                            @error('password')
                                                                <div class="invalid-feedback d-block">{{ $message }}
                                                                </div>
                                                            @enderror
                                                        </div>
                                                        <div class="mb-3">
                                                            <label for="password_confirmation{{ $employee->id }}"
                                                                class="form-label">تأكيد كلمة المرور</label>
                                                            <input type="password" class="form-control"
                                                                id="password_confirmation{{ $employee->id }}"
                                                                name="password_confirmation" required>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary">حفظ
                                                            التغييرات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                    <!--end Model change password-->
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle me-2"></i>
                        لا يوجد موظفين مسجلين حتى الآن
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection
