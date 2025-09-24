@extends('master')

@section('title')
    تعديل قاعدة ولاء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل قاعدة ولاء</h2>
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


    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <form action="{{ route('loyalty_points.update', $loyaltyRule->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
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
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                    <div>
                        <button type="submit" class="btn btn-outline-danger">
                            <i class="fa fa-plus me-2"></i>الغاء
                        </button>
                        <button type="submit" class="btn btn-outline-success">
                            <i class="fa fa-plus me-2"></i>حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">معلومات نقاط الولاء</h4>
                </div>

                <div class="card-body">
                    <div class="form-body row">
                        <div class="form-group col-md-6">
                            <label for="name" class=""> الاسم </label>
                            <input type="text" id="name" class="form-control" placeholder="الاسم" name="name"
                                value="{{ old('name', $loyaltyRule->name) }}" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label> الحالة </label>
                            <select class="form-control" id="status" name="status" required>
                                <option value="">الحالة</option>
                                <option value="1" {{ $loyaltyRule->status == 1 ? 'selected' : '' }}> نشط</option>
                                <option value="2" {{ $loyaltyRule->status == 2 ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="priority_level" class=""> درجة الاولوية </label>
                            <input type="text" id="priority_level" class="form-control" placeholder="درجة الاولوية"
                                name="priority_level" value="{{ old('priority_level', $loyaltyRule->priority_level) }}">
                        </div>

                        <div class="form-group col-md-6">
                            <label for="">تصنيف العملاء</label>
                            <select id="feedback2" class="form-control select2" name="client_ids[]" multiple="multiple">
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}"
                                        {{ $loyaltyRule->clients->contains($client->id) ? 'selected' : '' }}>
                                        {{ $client->trade_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-lg-6 col-md-6 col-sm-6 container-collection_factor">
                            <div class="form-group h-auto input-error-target" data-izam1-forms1-input-container="collection_factor">
                                <label for="collection_factor" class="control-label">معامل جمع الرصيد <span class="required">*</span> <span class="tip-circle tip" title="مقدار الإنفاق بالعملة الأساسية = 1 نقطة ولاء"><i class="fas fa-question-circle"></i></span></label>
                                <div>
                                    <div class="input-group">
                                        <input type='number' name='collection_factor' class='form-control' unit='1 نقطة'
                                            info='مقدار الإنفاق بالعملة الأساسية = 1 نقطة ولاء' currency='SAR'
                                            data-parsley-type='number' data-parsley-errors-container='#error-collection_factor'
                                            step='0.001' required='required' id='collection_factor'
                                            value="{{ old('collection_factor', $loyaltyRule->collection_factor) }}" />
                                        <div class="input-group-text">
                                            <span>
                                                <span class="d-inline-block">SAR</span>
                                                <i class="d-inline-block fa-equals far mx-1"></i>
                                                <span class="d-inline-block">1 نقطة</span>
                                            </span>
                                        </div>
                                    </div>
                                    <small class="form-text text-muted">مقدار الإنفاق بالعملة الأساسية = 1 نقطة ولاء</small>
                                    <div id="error-collection_factor" class="invalid-message overflow-hidden pt-0 w-100" style="max-height: 30px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 container-minimum_total_spent">
                            <div class="form-group h-auto input-error-target" data-izam1-forms1-input-container="minimum_total_spent">
                                <label for="minimum_total_spent" class="control-label">الحد الأدنى للصرف <span class="tip-circle tip" title="الحد الأدنى لإجمالي الإنفاق هو الحد الأدنى لمبلغ الفاتورة التي يدفعها العميل من أجل تطبيق قاعدة الولاء (حتى يتمكن العميل من الحصول على نقاط الولاء)"><i class="fas fa-question-circle"></i></span></label>
                                <div class="input-group form-group mb-0">
                                    <input type='number' name='minimum_total_spent' class='form-control' data-parsley-type='number'
                                        step='0.00001' data-parsley-errors-container='#error-minimum_total_spent'
                                        value="{{ old('minimum_total_spent', $loyaltyRule->minimum_total_spent) }}" />
                                    <div class="currency form-group mb-0 input-error-target">
                                        <select type='select' name='currency_type' no-translate='1' class='form-control' data-select='true'>
                                            <option value='1' {{ $loyaltyRule->currency_type == 1 ? 'selected' : '' }}>AED</option>
                                            <option value='2' {{ $loyaltyRule->currency_type == 2 ? 'selected' : '' }}>SAR</option>
                                            <option value='3' {{ $loyaltyRule->currency_type == 3 ? 'selected' : '' }}>SBD</option>
                                            <option value='4' {{ $loyaltyRule->currency_type == 4 ? 'selected' : '' }}>SCR</option>
                                            <option value='5' {{ $loyaltyRule->currency_type == 5 ? 'selected' : '' }}>SDG</option>
                                            <option value='6' {{ $loyaltyRule->currency_type == 6 ? 'selected' : '' }}>USD</option>
                                            <option value='7' {{ $loyaltyRule->currency_type == 7 ? 'selected' : '' }}>UYU</option>
                                            <option value='8' {{ $loyaltyRule->currency_type == 8 ? 'selected' : '' }}>ZWL</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="error-minimum_total_spent" class="invalid-message overflow-hidden pt-0 w-100" style="max-height: 30px;"></div>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6 container-period">
                            <div class="form-group h-auto input-error-target" data-izam1-forms1-input-container="period">
                                <label for="period" class="control-label">تنتهى صلاحيته بعد</label>
                                <div class="input-group form-group mb-0">
                                    <input type='number' name='period' class='form-control col-md-6' data-parsley-type='number'
                                        data-parsley-errors-container='#error-period' id='period-number'
                                        value="{{ old('period', $loyaltyRule->period) }}" />
                                    <div class="form-group mb-0 px-0 col-md-6 input-error-target">
                                        <select type='select' name='period_unit' class='form-control' data-select='true' id='period-unit'>
                                            <option value='1' {{ $loyaltyRule->period_unit == 1 ? 'selected' : '' }}>يوم</option>
                                            <option value='2' {{ $loyaltyRule->period_unit == 2 ? 'selected' : '' }}>الشهر</option>
                                            <option value='3' {{ $loyaltyRule->period_unit == 3 ? 'selected' : '' }}>السنة</option>
                                            <option value='4' {{ $loyaltyRule->period_unit == 4 ? 'selected' : '' }}>لا تنتهي صلاحيته</option>
                                        </select>
                                    </div>
                                </div>
                                <div id="error-period" class="invalid-message overflow-hidden pt-0 w-100" style="max-height: 30px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
@endsection

@section('scripts')
    <script>
        document.getElementById('period-unit').addEventListener('change', function() {
            var periodNumberInput = document.getElementById('period-number');
            if (this.value === '4') { // '4' هو قيمة الخيار "لا تنتهي صلاحيته"
                periodNumberInput.setAttribute('readonly', true);
                periodNumberInput.value = ''; // يمكنك أيضًا مسح القيمة إذا لزم الأمر
            } else {
                periodNumberInput.removeAttribute('readonly');
            }
        });

        // تشغيل الحدث عند التحميل للتأكد من الحالة الأولية
        document.getElementById('period-unit').dispatchEvent(new Event('change'));
    </script>
@endsection
