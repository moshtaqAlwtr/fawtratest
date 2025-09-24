@extends('master')

@section('title')

@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الشيكات المستلمه</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('received_cheques.update', $received_cheque->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>تحديث
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger" role="alert">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات الشيك المستلم</h4> </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <div class="form-group col-md-6">
                                    <label for="">المبلغ <span style="color: red">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="amount" value="{{ old('amount', $received_cheque->amount) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">رقم الشيك <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="cheque_number" value="{{ old('cheque_number', $received_cheque->cheque_number) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">تاريخ الإصدار <span style="color: red">*</span></label>
                                    <input type="date" id="issue_date" class="form-control" name="issue_date" value="{{ old('issue_date', $received_cheque->issue_date) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">تاريخ الاستحقاق </label>
                                    <input type="date" id="due_date" class="form-control" name="due_date" value="{{ old('due_date', $received_cheque->due_date) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب المستلم <span style="color: red">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="recipient_account_id">
                                        <option value="" disabled selected>اختر الحساب المستلم </option>
                                        <option value="1" {{ old('recipient_account_id', $received_cheque->recipient_account_id) == 1 ? 'selected' : '' }}>حساب شخصي</option>
                                        <option value="2" {{ old('recipient_account_id', $received_cheque->recipient_account_id) == 2 ? 'selected' : '' }}>حساب شركة</option>
                                        <option value="3" {{ old('recipient_account_id', $received_cheque->recipient_account_id) == 3 ? 'selected' : '' }}>حساب مؤسسة</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الاسم على الشيك <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="payee_name" value="{{ old('payee_name', $received_cheque->payee_name) }}">
                                </div>

                                <div class="form-group col-md-12">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" value="1" name="endorsement"  id="checkboxToggle" {{ old('endorsement', $received_cheque->endorsement) == 1 ? 'checked' : '' }}>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">تظهير</span>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <div id="nameInputContainer" style="display: none;">
                                        <label for="">الاسم</label>
                                        <input type="text" class="form-control" name="name" value="{{ old('name', $received_cheque->name) }}">
                                    </div>
                                </div>
                                <div class="form-group col-md-6"></div>

                                <div class="form-group col-md-6">
                                    <label for="">حساب التحصيل <span style="color: red">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="collection_account_id">
                                        <option value="" disabled selected>اختر الحساب</option>
                                        <option value="1" {{ old('collection_account_id', $received_cheque->collection_account_id) == 1 ? 'selected' : '' }}>حساب شخصي</option>
                                        <option value="2" {{ old('collection_account_id', $received_cheque->collection_account_id) == 2 ? 'selected' : '' }}>حساب شركة</option>
                                        <option value="3" {{ old('collection_account_id', $received_cheque->collection_account_id) == 3 ? 'selected' : '' }}>حساب مؤسسة</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">المرفق </label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>

                                <div class="form-group col-md-12">
                                    <label for="">الوصف </label>
                                    <textarea class="form-control" rows="2" placeholder="أدخل الوصف" name="description">{{ old('description', $received_cheque->description) }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const checkboxToggle = document.getElementById("checkboxToggle");
            const nameInputContainer = document.getElementById("nameInputContainer");

            if (checkboxToggle.checked) {
                nameInputContainer.style.display = "block";
            } else {
                nameInputContainer.style.display = "none";
            }

            checkboxToggle.addEventListener("change", function () {
                if (this.checked) {
                nameInputContainer.style.display = "block";
                } else {
                nameInputContainer.style.display = "none";
                }
            });
        });
    </script>
@endsection

