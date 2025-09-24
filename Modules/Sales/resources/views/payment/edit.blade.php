@extends('master')

@section('title')
    اضافة عملية الدفع
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> تعديل الدفع </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">اضافة عملية دفع</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <form action="{{ route('paymentsClient.update', $payment) }}" method="POST" enctype="multipart/form-data">

        @csrf
        @method('PUT')


        <!-- عرض الأخطاء -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
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
                            <i class="fa fa-save"></i>تحديث عملية الدفع
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">

                <!-- الحقول -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">المبلغ <span style="color: red">*</span></label>
                        <input type="text" id="name" name="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label for="payment_date" class="form-label">تاريخ الدفع <span style="color: red">*</span></label>
                        <input type="date" id="payment_date" name="payment_date" class="form-control" value="{{ old('payment_date', $payment->payment_date) }}" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="payment_method" class="form-label">وسيلة الدفع <span style="color: red">*</span></label>
                        <select name="payment_type" class="form-control" id="payment_method" required>
                            <option value="">اختر نوع الدفع</option>
                            <option value="1" {{ old('payment_type', $payment->payment_type) == 1 ? 'selected' : '' }}>كاش</option>
                            <option value="2" {{ old('payment_type', $payment->payment_type) == 2 ? 'selected' : '' }}>شيك</option>
                            <option value="3" {{ old('payment_type', $payment->payment_type) == 3 ? 'selected' : '' }}>تحويل بنكي</option>
                            <option value="4" {{ old('payment_type', $payment->payment_type) == 4 ? 'selected' : '' }}>اونلاين</option>
                            <option value="5" {{ old('payment_type', $payment->payment_type) == 5 ? 'selected' : '' }}>أخرى</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="treasury_id" class="form-label">الخزينة المستخدمة </label>
                        <select name="treasury_id" class="form-control" id="treasury_id">
                            <option value="">اختر الخزينة</option>
                            <option value="1" {{ old('treasury_id', $payment->treasury_id) == 1 ? 'selected' : '' }}>الخزينة الرئيسية</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="status_payment" class="form-label">حالة الدفع <span style="color: red">*</span></label>
                        <select name="status_payment" class="form-control" id="status_payment" required>
                            <option value="">اختر حالة الدفع</option>
                            <option value="1" {{ old('status_payment', $payment->status_payment) == 1 ? 'selected' : '' }}>غير مكتمل</option>
                            <option value="2" {{ old('status_payment', $payment->status_payment) == 2 ? 'selected' : '' }}>مكتمل</option>
                            <option value="3" {{ old('status_payment', $payment->status_payment) == 3 ? 'selected' : '' }}>تحت المراجعة</option>
                            <option value="4" {{ old('status_payment', $payment->status_payment) == 4 ? 'selected' : '' }}>فاشلة</option>
                            <option value="5" {{ old('status_payment', $payment->status_payment) == 5 ? 'selected' : '' }}>مسودة</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="employee_id" class="form-label">تم التحصيل بواسطة <span style="color: red">*</span></label>
                        <select id="employee_id" name="employee_id" class="form-control" required>
                            @foreach($employees as $employee)
                                <option value="{{ $employee->id }}" {{ $employee->id == old('employee_id', $payment->employee_id) ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="address1" class="form-label">رقم المعرف</label>
                        <input type="text" id="address1" name="id" class="form-control" placeholder="رقم المعرف" value="{{ $id }}" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="description" class="form-label">بيانات الدفع</label>
                        <textarea id="description" name="payment_data" class="form-control" rows="2"
                            placeholder="مثل: رقم الشيك، رقم التحويل">{{ old('payment_data', $payment->payment_data) }}</textarea>
                    </div>
                </div>

                <input type="hidden" name="invoice_id" value="{{ $id }}">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="notes" class="form-label">ملاحظات</label>
                        <textarea id="notes" name="notes" class="form-control" rows="2" placeholder="أي ملاحظات إضافية">{{ old('notes', $payment->notes) }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label for="file" class="form-label">المرفقات</label>
                        <input id="file" type="file" name="attachments" class="form-control"
                            accept=".pdf,.jpg,.jpeg,.png">
                        <small class="text-muted">يمكنك رفع ملف PDF أو صورة (الحد الأقصى 2 ميجابايت)</small>
                    </div>
                </div>

            </div>
        </div>
    </form>
    </div>
@endsection
