@extends('master')

@section('title')
    التحويلات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> التحويلات</h2>
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

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <form action="{{ route('treasury.transferTreasuryStore') }}" method="post" enctype="multipart/form-data">
        @csrf

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="{{ route('treasury.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- الخزينة المصدر -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <span>من:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label class=""> الخزينة </label>
                                            <select class="form-control select2" id="from_treasury_id" name="from_treasury_id" required>
                                                <option value="">اختر الخزينة</option>
                                                @foreach ($treasuries as $treasury)
                                                    <option value="{{ $treasury->id }}" data-balance="{{ $treasury->balance }}">
                                                        {{ $treasury->name }} ({{ $treasury->balance }} ريال)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class=""> العملة </label>
                                            <select class="form-control select2" id="from_currency" name="from_currency" required>
                                                <option value="">اختر العملة</option>
                                                <option value="1"> SAR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="">المبلغ</label>
                                            <input type="number" class="form-control" id="amount" name="amount" required>
                                        </div>
                                        <div class="col-md-4">
                                            <label class=""> المتاح قبل </label>
                                            <input type="text" class="form-control" id="available_before_from" name="available_before_from" readonly>
                                        </div>
                                        <div class="col-md-4">
                                            <label class=""> المتاح بعد </label>
                                            <input type="text" class="form-control" id="available_after_from" name="available_after_from" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الخزينة الهدف -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-3">
                                            <span>الى:</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group row">
                                        <div class="col-md-6">
                                            <label class=""> الخزينة </label>
                                            <select class="form-control select2" id="to_treasury_id" name="to_treasury_id" required>
                                                <option value="">اختر الخزينة</option>
                                                @foreach ($treasuries as $treasury)
                                                    <option value="{{ $treasury->id }}" data-balance="{{ $treasury->balance }}">
                                                        {{ $treasury->name }} ({{ $treasury->balance }} ريال)
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class=""> العملة </label>
                                            <select class="form-control select2" id="to_currency" name="to_currency" required>
                                                <option value="">اختر العملة</option>
                                                <option value="1"> SAR</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class=""> المتاح قبل </label>
                                            <input type="text" class="form-control" id="available_before_to" name="available_before_to" readonly>
                                        </div>
                                        <div class="col-md-6">
                                            <label class=""> المتاح بعد </label>
                                            <input type="text" class="form-control" id="available_after_to" name="available_after_to" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- تفاصيل إضافية -->
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="transfer_date"> تاريخ التحويل </label>
                            <input type="date" id="transfer_date" class="form-control" name="transfer_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label>الملاحظات </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="form-group">
                            <label for="attachments">المرفقات</label>
                            <input type="file" name="attachments" id="attachments" class="d-none">
                            <div class="upload-area border rounded p-3 text-center position-relative" onclick="document.getElementById('attachments').click()">
                                <div class="d-flex align-items-center justify-content-center gap-2">
                                    <i class="fas fa-cloud-upload-alt text-primary"></i>
                                    <span class="text-primary">اضغط هنا</span>
                                    <span>أو</span>
                                    <span class="text-primary">اختر من جهازك</span>
                                </div>
                                <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                    <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            // عند تغيير الخزينة المصدر
            $('#from_treasury_id').change(function() {
                const selectedTreasury = $(this).find(':selected');
                const balance = selectedTreasury.data('balance');
                $('#available_before_from').val(balance); // تحديث المتاح قبل
                calculateAvailableAfterFrom(); // حساب المتاح بعد
            });

            // عند تغيير الخزينة الهدف
            $('#to_treasury_id').change(function() {
                const selectedTreasury = $(this).find(':selected');
                const balance = selectedTreasury.data('balance');
                $('#available_before_to').val(balance); // تحديث المتاح قبل
                calculateAvailableAfterTo(); // حساب المتاح بعد
            });

            // عند تغيير المبلغ
            $('#amount').keyup(function() {
                calculateAvailableAfterFrom(); // حساب المتاح بعد للخزينة المصدر
                calculateAvailableAfterTo(); // حساب المتاح بعد للخزينة الهدف
            });

            // دالة لحساب المتاح بعد للخزينة المصدر
            function calculateAvailableAfterFrom() {
                const amount = parseFloat($('#amount').val()) || 0;
                const availableBefore = parseFloat($('#available_before_from').val()) || 0;
                const availableAfter = availableBefore - amount; // خصم المبلغ من الرصيد المتاح
                $('#available_after_from').val(availableAfter); // تحديث المتاح بعد
            }

            // دالة لحساب المتاح بعد للخزينة الهدف
            function calculateAvailableAfterTo() {
                const amount = parseFloat($('#amount').val()) || 0;
                const availableBefore = parseFloat($('#available_before_to').val()) || 0;
                const availableAfter = availableBefore + amount; // إضافة المبلغ إلى الرصيد المتاح
                $('#available_after_to').val(availableAfter); // تحديث المتاح بعد
            }
        });
    </script>
@endsection
