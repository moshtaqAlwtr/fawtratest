@extends('master')

@section('title')
    اضافة بنود راتب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة بنود راتب</h2>
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
        <form class="form" action="{{ route('SalaryItems.store') }}" method="post" enctype="multipart/form-data">
            @csrf
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
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>

                    </div>
                </div>
            </div>


            <div class="card" style="max-width: 90%; margin: 0 auto;">

                <div class="card-body">
                    <h1 class="card-title"> معلومات بنود الراتب </h1>

                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="feedback1" class="">الاسم </label>
                            <input type="text" id="feedback1" class="form-control" placeholder="الاسم" name="name">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> نوع </label>
                            <select name="type" class="form-control" id="">
                                <option value=""> اختر النوع</option>
                                <option value="1"> مستحق </option>
                                <option value="2"> مستقطع</option>
                            </select>
                        </div>

                    </div>
                    <div class="form-body row">


                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الحالة </label>
                            <select name="status" class="form-control" id="">
                                <option value=""> اختر الحالة</option>
                                <option value="1"> نشط </option>
                                <option value="2"> غير نشط</option>
                            </select>
                        </div>

                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الوصف </label>
                            <textarea name="description" class="form-control" id=""></textarea>
                        </div>
                    </div>

                    <div class="form-body row">
                        <div class="form-group col-md-6 position-relative">
                            <label for="feedback2">الحساب الافتراضي ؟</label>
                            <div class="input-group">
                                <input type="text" id="searchAccount" class="form-control" placeholder="ابحث عن الحساب">
                                <div class="input-group-append">
                                    <span id="loadingIcon" class="input-group-text d-none">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </div>
                            </div>
                            <div id="accountResults"
                                class="list-group w-100 mt-1 position-absolute bg-white border rounded shadow"
                                style="max-height: 200px; overflow-y: auto; z-index: 1050;"></div>
                        </div>
                    </div>
                    <div class="form-body row">
                        <!-- المبلغ -->
                        <div class="col-md-6">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100 text-right">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">
                                                <input id="amount_radio" name="salary_item_value" value="1"
                                                    class="custom-control-input" type="radio" checked
                                                    onchange="toggleFields()" name="salary_item_value" value="1">
                                                <label for="amount_radio" class="custom-control-label mr-2">المبلغ
                                                    <span class="required">*</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group" id="amount-inputs">
                                    <div class="form-row">
                                        <div class="col-md-12">
                                            <label for="amount" class="form-label" style="margin-bottom: 10px">
                                                ادخل المبلغ</label>
                                            <input type="number" step="0.01" class="form-control" name="amount"
                                                id="amount" placeholder="ادخل المبلغ">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- اختيار الصيغة الحسابية  -->
                        <div class="col-md-6">
                            <div class="position-relative">
                                <div class="input-group form-group">
                                    <div class="input-group-prepend w-100">
                                        <div class="input-group-text w-100">
                                            <div
                                                class="custom-control custom-radio d-flex justify-content-start align-items-center w-100">
                                                <input id="formula_radio" name="salary_item_value" value="2"
                                                    class="custom-control-input" type="radio"
                                                    onchange="toggleFields()">
                                                <label for="formula_radio" class="custom-control-label">الصيغة الحسابية
                                                    <span class="required">*</span></label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-row">
                                    <div class="col-md-12">
                                        <label for="calculation_formula" class="form-label">ادخل الصيغة الحسابية </label>
                                        <input type="text" class="form-control" id="calculation_formula"
                                            placeholder="ادخل الصيغة الحسابية" name="calculation_formula">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-body row">


                        <div class="form-group col-md-6">
                            <label for="feedback2" class=""> الشرط ؟ </label>
                            <input type="text" class="form-control" placeholder="الشرط ؟" name="condition">
                        </div>


                    </div>
                    <div class="form-body row">
                        <div class="col-md-6">
                            <div class="input-group form-group">
                                <div class="input-group-text w-100 text-left">
                                    <div
                                        class="custom-control custom-checkbox d-flex justify-content-start align-items-center w-100">
                                        <input id="duration_check" class="custom-control-input" name="reference_value"
                                            type="checkbox">
                                        <label for="duration_check" class="custom-control-label ml-2">
                                            قيمة مرجعية فقط؟ <span class="required">*</span>
                                        </label>
                                    </div>
                                </div>
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script></script>
    <script>
        $(document).ready(function() {
            toggleFields(); // تشغيل الدالة عند تحميل الصفحة
        });

        function toggleFields() {
            const amountInput = document.getElementById('amount');
            const formulaInput = document.getElementById('calculation_formula');

            if (document.getElementById('amount_radio').checked) {
                amountInput.disabled = false;
                formulaInput.disabled = true;
                formulaInput.value = ''; // تفريغ قيمة الصيغة الحسابية
            }

            if (document.getElementById('formula_radio').checked) {
                amountInput.disabled = true;
                formulaInput.disabled = false;
                amountInput.value = ''; // تفريغ قيمة المبلغ
            }

            $(document).ready(function() {
                $('#searchAccount').on('keyup', function() {
                    var query = $(this).val();

                    if (query.length > 0) {
                        $('#loadingIcon').removeClass('d-none'); // عرض أيقونة التحميل

                        $.ajax({
                            url: "{{ route('SalaryItems.index') }}",
                            type: "GET",
                            data: {
                                query: query
                            },
                            success: function(response) {
                                $('#accountResults').html(response.options);
                            },
                            complete: function() {
                                $('#loadingIcon').addClass(
                                'd-none'); // إخفاء أيقونة التحميل بعد الانتهاء
                            }
                        });
                    } else {
                        $('#accountResults').html('');
                        $('#loadingIcon').addClass('d-none'); // إخفاء الأيقونة إذا لم يكن هناك نص
                    }
                });

                // عند اختيار عنصر، يتم إدخاله في الحقل وإخفاء القائمة
                $(document).on('click', '.account-item', function() {
                    $('#searchAccount').val($(this).text());
                    $('#accountResults').html('');
                });
            });


        }
    </script>
@endsection
