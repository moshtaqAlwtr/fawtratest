@extends('master')

@section('title')
اصدر شيك
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اصدر شيك</h2>
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
            <form class="form-horizontal" action="{{ route('payable_cheques.store') }}" method="POST" enctype="multipart/form-data">
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
                            <h4 class="card-title">معلومات الشيك</h4> </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <div class="form-group col-md-6">
                                    <label for="">المبلغ <span style="color: red">*</span></label>
                                    <input type="number" step="0.01" min="0" class="form-control" name="amount" value="{{ old('amount') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">رقم الشيك <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="cheque_number" value="{{ old('cheque_number') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">اسم البنك <span style="color: red">*</span></label>
                                    <select id="bank_id" class="form-control" name="bank_id">
                                        <option value="" disabled selected>اختر البنك</option>
                                        @foreach ($bank_accounts as $bank_account)
                                            <option value="{{ $bank_account->id }}" {{ old('bank_id') == $bank_account->id ? 'selected' : '' }}>{{ $bank_account->bank_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">رقم دفتر الشيكات <span style="color: red">*</span></label>
                                    <select  class="form-control" name="cheque_book_id">
                                        @foreach ($check_books as $check_books)
                                        <option value="{{ $check_books->id }}" {{ old('cheque_book_id') == $check_books->id ? 'selected' : '' }}>{{ $check_books->cheque_book_number }}</option>
                                            
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">تاريخ الإصدار <span style="color: red">*</span></label>
                                    <input type="date" id="issue_date" class="form-control" name="issue_date" value="{{ old('issue_date') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">تاريخ الاستحقاق </label>
                                    <input type="date" id="due_date" class="form-control" name="due_date" value="{{ old('due_date') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب المستلم <span style="color: red">*</span></label>
                                    <select class="form-control select2" id="basicSelect" name="recipient_account_id">
                                        <option value="" disabled selected>اختر الحساب المستلم </option>
                                        <option value="1" {{ old('recipient_account_id') == 1 ? 'selected' : '' }}>حساب شخصي</option>
                                        <option value="2" {{ old('recipient_account_id') == 2 ? 'selected' : '' }}>حساب شركة</option>
                                        <option value="3" {{ old('recipient_account_id') == 3 ? 'selected' : '' }}>حساب مؤسسة</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الاسم على الشيك <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="payee_name" value="{{ old('payee_name') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">المرفق </label>
                                    <input type="file" class="form-control" name="attachment">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الوصف </label>
                                    <textarea class="form-control" rows="2" placeholder="أدخل الوصف" name="description">{{ old('description') }}</textarea>
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
        // الرابط الديناميكي لجلب دفاتر الشيكات
        const getCheckbooksUrl = "{{ route('get.checkbooks', ['bankId' => ':bankId']) }}";

        // استماع لتغيير البنك
        document.getElementById('bank_id').addEventListener('change', function () {
            const bankId = this.value; // الحصول على البنك المحدد
            const chequeBookSelect = document.getElementById('cheque_book_id'); // عنصر دفاتر الشيكات


            // إفراغ القائمة الحالية
            chequeBookSelect.innerHTML  = '';

            if (bankId) {
                // استبدال :bankId في الرابط بالقيمة الفعلية
                const url = getCheckbooksUrl.replace(':bankId', bankId);

                // تسجيل الرابط للتحقق
                console.log("Fetching from URL:", url);

                // طلب البيانات باستخدام fetch
                fetch(url)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("Data received:", data); // تسجيل البيانات للتحقق
                        data.forEach(checkbook => {
                            const option = document.createElement('option');
                            option.value = checkbook.id; // تحديد القيمة
                            option.textContent = checkbook.cheque_book_number; // النص الظاهر
                            chequeBookSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error("Error fetching cheque books:", error);
                        alert('حدث خطأ أثناء جلب دفاتر الشيكات. حاول مرة أخرى.');
                    });
            }
        });

    </script>
@endsection

