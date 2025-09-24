@extends('master')

@section('title')
أضف دفتر الشيكات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أضف دفتر الشيكات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافة
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('check_books.store') }}" method="POST">
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
                                    <i class="fa fa-save"></i>حفظ
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
                            <h4 class="card-title">معلومات دفتر الشيكات</h4> </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <div class="form-group col-md-6">
                                    <label for="">اسم البنك <span style="color: red">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="bank_id">
                                        <option value="" disabled selected>اختر البنك </option>
                                        @foreach ($bank_accounts as $bank_account)
                                            <option value="{{ $bank_account->id }}" {{ old('bank_id') == $bank_account->id ? 'selected' : '' }}>{{ $bank_account->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">رقم دفتر الشيكات <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" placeholder="رقم دفتر الشيكات" name="cheque_book_number" value="{{ old('cheque_book_number') }}">
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="">الرقم المسلسل الاول <span style="color: red">*</span></label>
                                    <input type="number" id="start_serial_number" class="form-control" name="start_serial_number" value="{{ old('start_serial_number') }}">
                                    <small id="cheque_count" style="margin-top: 10px;;">عدد الشيكات: 0</small>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="">الرقم المسلسل الاخير <span style="color: red">*</span></label>
                                    <input type="number" id="end_serial_number" class="form-control" name="end_serial_number" value="{{ old('end_serial_number') }}">
                                    <small id="error_message" style="color: red; margin-top: 5px;"></small>
                                </div>


                                <div class="form-group col-md-6">
                                    <label for="">عملة دفتر الشيكات <span style="color: red">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="currency" required>
                                        <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>الدولار الأمريكي (USD)</option>
                                        <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>اليورو (EUR)</option>
                                        <option value="JPY" {{ old('currency') == 'JPY' ? 'selected' : '' }}>الين الياباني (JPY)</option>
                                        <option value="GBP" {{ old('currency') == 'GBP' ? 'selected' : '' }}>الجنيه الإسترليني (GBP)</option>
                                        <option value="AUD" {{ old('currency') == 'AUD' ? 'selected' : '' }}>الدولار الأسترالي (AUD)</option>
                                        <option value="CAD" {{ old('currency') == 'CAD' ? 'selected' : '' }}>الدولار الكندي (CAD)</option>
                                        <option value="CHF" {{ old('currency') == 'CHF' ? 'selected' : '' }}>الفرنك السويسري (CHF)</option>
                                        <option value="CNY" {{ old('currency') == 'CNY' ? 'selected' : '' }}>اليوان الصيني (CNY)</option>
                                        <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }} selected>الريال السعودي (SAR)</option>
                                        <option value="AED" {{ old('currency') == 'AED' ? 'selected' : '' }}>الدرهم الإماراتي (AED)</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحاله <span class="text-danger">*</span></label>
                                    <select class="form-control"  name="status" required>
                                        <option value="0" {{ old('status') == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status') == 1 ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feedback2" class="sr-only">الملاحظات </label>
                                    <textarea id="feedback2" class="form-control" rows="2" placeholder="الملاحظات" name="notes">{{ old('notes') }}</textarea>
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
        // عناصر الحقول
        const startSerialInput = document.getElementById("start_serial_number");
        const endSerialInput = document.getElementById("end_serial_number");

        // عناصر عرض الرسالة
        const chequeCountDisplay = document.getElementById("cheque_count");
        const errorMessageDisplay = document.getElementById("error_message");

        // وظيفة التحقق وعرض الرسائل
        function calculateCheques() {
            const start = parseInt(startSerialInput.value) || 0;
            const end = parseInt(endSerialInput.value) || 0;

            if (end > start) {
                const totalCheques = end - start + 1;
                chequeCountDisplay.textContent = `عدد الشيكات: ${totalCheques}`;

                if (totalCheques > 150) {
                    errorMessageDisplay.textContent = "لا يمكنك تجاوز 150 شيكًا في دفتر شيكات واحد.";
                } else {
                    errorMessageDisplay.textContent = ""; // إخفاء الرسالة إذا كان العدد صحيحًا
                }
            } else if (end <= start && end !== 0) {
                chequeCountDisplay.textContent = "عدد الشيكات: 0";
                errorMessageDisplay.textContent = "الرقم التسلسلي الأخير يجب أن يكون أكبر من الرقم التسلسلي الأول.";
            } else {
                chequeCountDisplay.textContent = "عدد الشيكات: 0";
                errorMessageDisplay.textContent = ""; // إخفاء الرسالة عندما تكون الحقول فارغة
            }
        }

        // إضافة الأحداث إلى الحقول
        startSerialInput.addEventListener("input", calculateCheques);
        endSerialInput.addEventListener("input", calculateCheques);

        calculateCheques();

    </script>
@endsection

