@extends('master')

@section('title')
خزائن وحسابات بنكية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">خزائن وحسابات بنكية</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافة حساب
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="content-body">

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>

                    <div>
                        <a href="{{ route('treasury.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="products_form" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h4 class="card-title">تسجيل البيانات</h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form id="products_form" class="form form-vertical" action="{{ route('treasury.store_account_bank') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-body">
                            <div class="row">

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="first-name-vertical">النوع</label>
                                        <input type="text" disabled id="first-name-vertical" class="form-control" value="حساب بنكي">
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email-id-vertical">الاسم<span style="color: red">*</span></label>
                                        <input type="text" id="email-id-vertical" class="form-control"name="name" value="{{ old('name') }}">
                                        @error('name')
                                        <span class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email-id-vertical">اسم البنك<span style="color: red">*</span></label>
                                        <input type="text" id="email-id-vertical" class="form-control"name="bank_name" value="{{ old('bank_name') }}">
                                        @error('name')
                                        <span class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email-id-vertical">رقم الحساب البنكي<span style="color: red">*</span></label>
                                        <input type="text" id="email-id-vertical" class="form-control"name="account_number" value="{{ old('account_number') }}">
                                        @error('account_number')
                                        <span class="text-danger" id="basic-default-name-error" class="error">
                                            {{ $message }}
                                        </span>
                                        @enderror
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="first-name-vertical">العملة</label>
                                        <select class="form-control" id="basicSelect" name="currency">
                                            <option value="0" {{ old('currency') == 0 ? 'selected' : '' }}>ريال</option>
                                            <option value="1" {{ old('currency') == 1 ? 'selected' : '' }}>جنية</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="first-name-vertical">الحالة</label>
                                        <select class="form-control" id="basicSelect" name="status">
                                            <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>نشط</option>
                                            <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>غير نشط</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-6">
                                    <div class="form-group">
                                        <label for="email-id-vertical">الوصف</label>
                                        <textarea name="description" class="form-control" id="basicTextarea" rows="2">{{ old('description') }}</textarea>
                                    </div>
                                </div>

                            </div>

                            <hr>
                            <div class="card">
                                <div class="card-header">
                                    <h5>الصلاحيات</h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="first-name-icon">ايداع</label>
                                                <select class="form-control" id="deposit_permissions" name="deposit_permissions">
                                                    <option selected value="0">الكل</option>
                                                    <option value="1" {{ old('deposit_permissions') == 1 ? 'selected' : '' }}>موظف محدد</option>
                                                    <option value="2" {{ old('deposit_permissions') == 2 ? 'selected' : '' }}>دور وظيفي محدد</option>
                                                    <option value="3" {{ old('deposit_permissions') == 3 ? 'selected' : '' }}>فرع محدد</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group">
                                                <label for="first-name-icon">سحب</label>
                                                <select class="form-control" id="withdraw_permissions" name="withdraw_permissions">
                                                    <option value="0">الكل</option>
                                                    <option value="1" {{ old('withdraw_permissions') == 1 ? 'selected' : '' }}>موظف محدد</option>
                                                    <option value="2" {{ old('withdraw_permissions') == 2 ? 'selected' : '' }}>دور وظيفي محدد</option>
                                                    <option value="3" {{ old('withdraw_permissions') == 3 ? 'selected' : '' }}>فرع محدد</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="row">

                                        <div class="col-6">
                                            <div class="form-group" style='display: none' id="employee_id">
                                                <label for="first-name-icon">اختر الموظف</label>
                                                <select class="form-control" name="v_employee_id">
                                                    @foreach ($employees as $employee)
                                                        <option value="1">{{ $employee->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" style='display: none' id="functional_role_id">
                                                <label for="first-name-icon">اختر الدور الوظيفي</label>
                                                <select class="form-control" name="v_functional_role_id">
                                                    <option value="1">دور وظيفي 1</option>
                                                    <option value="2">دور وظيفي 2</option>
                                                    <option value="3">دور وظيفي 3</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style='display: none' id="branch_id">
                                                <label for="first-name-icon">اختر الفرع</label>
                                                <select class="form-control" name="v_branch_id">
                                                    <option value="1">فرع 1</option>
                                                    <option value="2">فرع 2</option>
                                                    <option value="3">فرع 3</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-6">
                                            <div class="form-group" style='display: none' id="1employee_id">
                                                <label for="first-name-icon">اختر الموظف</label>
                                                <select class="form-control" name="c_employee_id">
                                                    @foreach ($employees as $employee)
                                                        <option value="1">{{ $employee->full_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group" style='display: none' id="1functional_role_id">
                                                <label for="first-name-icon">اختر الدور الوظيفي</label>
                                                <select class="form-control" name="c_functional_role_id">
                                                    <option value="1">دور وظيفي 1</option>
                                                    <option value="2">دور وظيفي 2</option>
                                                    <option value="3">دور وظيفي 3</option>
                                                </select>
                                            </div>
                                            <div class="form-group" style='display: none' id="1branch_id">
                                                <label for="first-name-icon">اختر الفرع</label>
                                                <select class="form-control" name="c_branch_id">
                                                    <option value="1">فرع 1</option>
                                                    <option value="2">فرع 2</option>
                                                    <option value="3">فرع 3</option>
                                                </select>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>


                        </div>
                </div>
            </div>
        </div>

    </div>


@endsection

@section('scripts')
    <script>
        document.getElementById('deposit_permissions').onchange = function(){
            if(this.value == 1){
                document.getElementById('employee_id').style.display = '';
                document.getElementById('functional_role_id').style.display = 'none';
                document.getElementById('branch_id').style.display = 'none';
            } else if(this.value == 2) {
                document.getElementById('functional_role_id').style.display = '';
                document.getElementById('branch_id').style.display = 'none';
                document.getElementById('employee_id').style.display = 'none';
            } else if(this.value == 3) {
                document.getElementById('branch_id').style.display = '';
                document.getElementById('employee_id').style.display = 'none';
                document.getElementById('functional_role_id').style.display = 'none';
            }
            else{
                document.getElementById('branch_id').style.display = 'none';
                document.getElementById('employee_id').style.display = 'none';
                document.getElementById('functional_role_id').style.display = 'none';
            }
        };
    </script>

    <script>
        document.getElementById('withdraw_permissions').onchange = function(){
            if(this.value == 1){
                document.getElementById('1employee_id').style.display = '';
                document.getElementById('1functional_role_id').style.display = 'none';
                document.getElementById('1branch_id').style.display = 'none';
            } else if(this.value == 2) {
                document.getElementById('1functional_role_id').style.display = '';
                document.getElementById('1branch_id').style.display = 'none';
                document.getElementById('1employee_id').style.display = 'none';
            } else if(this.value == 3) {
                document.getElementById('1branch_id').style.display = '';
                document.getElementById('1employee_id').style.display = 'none';
                document.getElementById('1functional_role_id').style.display = 'none';
            }
            else{
                document.getElementById('1branch_id').style.display = 'none';
                document.getElementById('1employee_id').style.display = 'none';
                document.getElementById('1functional_role_id').style.display = 'none';
            }
        };
    </script>

@endsection
