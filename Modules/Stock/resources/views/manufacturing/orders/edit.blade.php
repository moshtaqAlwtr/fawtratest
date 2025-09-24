@extends('master')

@section('title')
تعديل أمر التصنيع
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل أمر التصنيع</h2>
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

            <form class="form-horizontal" action="{{ route('manufacturing.orders.update', $order->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('manufacturing.orders.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>تحديث
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات أمر التصنيع</h4>
                        </div>

                        <div class="card-body">
                            <div class="row">

                                <div class="form-group col-md-4">
                                    <label for="">الاسم <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $order->name }}">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="">كود <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="code" value="{{ $order->code }}">
                                </div>

                                <div class="form-group col-md-2">
                                    <label for="">التاريخ من<span style="color: red">*</span></label>
                                    <input type="date" class="form-control" name="from_date" value="{{ $order->from_date }}">
                                </div>
                                <div class="form-group col-md-2">
                                    <label for="">التاريخ الى<span style="color: red">*</span></label>
                                    <input type="date" class="form-control" name="to_date" value="{{ $order->to_date }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="account_id">
                                        <option value="" disabled selected>-- اختر الحساب --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $order->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الموظفين <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="employee_id">
                                        <option value="" disabled selected>-- اختر الموظفين --</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}" {{ $order->employee_id == $employee->id ? 'selected' : '' }}>{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">العميل <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="client_id">
                                        <option value="" disabled selected>-- اختر العميل --</option>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ $order->client_id == $client->id ? 'selected' : '' }}>{{ $client->trade_name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <hr>
                                <div class="form-group col-md-6">
                                    <label for="">المنتجات <span class="text-danger">*</span></label>
                                    <select class="form-control" name="product_id">
                                        <option value="" disabled selected>-- اختر المنتج --</option>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ $order->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>الكمية المطلوبة <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="quantity" value="{{ $order->quantity }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">قائمة مواد الانتاج <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="production_material_id">
                                        <option value="" disabled selected>-- اختر قائمة مواد الانتاج --</option>
                                        @foreach ($production_materials as $material)
                                            <option value="{{ $material->id }}" {{ $order->production_material_id == $material->id ? 'selected' : '' }}>{{ $material->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">مسار الانتاج <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="production_path_id">
                                        <option value="" disabled selected>-- اختر المسار الانتاج --</option>
                                        @foreach ($paths as $path)
                                            <option value="{{ $path->id }}" {{ $order->production_path_id == $path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                                        @endforeach
                                    </select>
                                </div>


                                <div class="form-group col-md-12 mt-4">
                                    <p onclick="toggleSection('rawMaterials')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="feather icon-package"></i> المواد الخام (<span id="rawMaterialCount">{{ $order->manufacturOrdersItem->count() }}</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="rawMaterials">
                                        <table class="table table-striped" id="itemsTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>المنتجات</th>
                                                    <th>سعر الوحدة</th>
                                                    <th>الكمية</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->manufacturOrdersItem as $item)
                                                <tr>
                                                    <td>
                                                        <select name="raw_product_id[]" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر البند --</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" {{ $item->raw_product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="raw_unit_price[]" class="form-control unit-price" value="{{ $item->raw_unit_price }}" readonly></td>
                                                    <td><input type="number" name="raw_quantity[]" class="form-control quantity" value="{{ $item->raw_quantity }}" min="1"></td>
                                                    <td>
                                                        <select name="raw_production_stage_id[]" class="form-control select2 product-select" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->raw_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="raw_total[]" class="form-control total" value="{{ $item->raw_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
                                            <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="grand-total">{{ $order->manufacturOrdersItem->sum('raw_total') }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('expenses')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="fa fa-money"></i> المصروفات (<span id="rowExpensesCount">{{ $order->manufacturOrdersItem->count() }}</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="expenses" style="display: none">
                                        <table class="table table-striped" id="ExpensesTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>الحساب</th>
                                                    <th>نوع التكلفة</th>
                                                    <th>المبلغ</th>
                                                    <th>الوصف</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->manufacturOrdersItem as $item)
                                                <tr>
                                                    <td>
                                                        <select name="expenses_account_id[]" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر الحساب --</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ $item->expenses_account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="expenses_cost_type[]" class="form-control select2 product-select">
                                                            <option value="1" {{ $item->expenses_cost_type == 1 ? 'selected' : '' }}>مبلغ ثابت</option>
                                                            <option value="2" {{ $item->expenses_cost_type == 2 ? 'selected' : '' }}>بناءً على الكمية</option>
                                                            <option value="3" {{ $item->expenses_cost_type == 3 ? 'selected' : '' }}>معادلة</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="expenses_price[]" class="form-control expenses-price" value="{{ $item->expenses_price }}"></td>
                                                    <td><textarea name="expenses_description[]" class="form-control" rows="1">{{ $item->expenses_description }}</textarea></td>
                                                    <td>
                                                        <select name="expenses_production_stage_id[]" class="form-control select2 product-select" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->expenses_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="expenses_total[]" class="form-control expenses-total" value="{{ $item->expenses_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="ExpensesAddRow"><i class="fa fa-plus"></i> إضافة</button>
                                            <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="expenses-grand-total">{{ $order->manufacturOrdersItem->sum('expenses_total') }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('manufacturing')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="feather icon-settings"></i> عمليات التصنيع (<span id="manufacturingCount">{{ $order->manufacturOrdersItem->count() }}</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="manufacturing" style="display: none">
                                        <table class="table table-striped" id="manufacturingTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>محطة العمل</th>
                                                    <th>نوع التكلفة</th>
                                                    <th>وقت التشغيل</th>
                                                    <th>التكلفة</th>
                                                    <th>الوصف</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->manufacturOrdersItem as $item)
                                                <tr>
                                                    <td>
                                                        <select name="workstation_id[]" class="form-control select2 product-select">
                                                            <option value="" disabled selected>-- اختر محطة العمل --</option>
                                                            @foreach ($workstations as $workstation)
                                                                <option value="{{ $workstation->id }}" {{ $item->workstation_id == $workstation->id ? 'selected' : '' }}>{{ $workstation->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="manu_cost_type[]" class="form-control select2 product-select">
                                                            <option value="1" {{ $item->manu_cost_type == 1 ? 'selected' : '' }}>مبلغ ثابت</option>
                                                            <option value="2" {{ $item->manu_cost_type == 2 ? 'selected' : '' }}>بناءً على الكمية</option>
                                                            <option value="3" {{ $item->manu_cost_type == 3 ? 'selected' : '' }}>معادلة</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="operating_time[]" class="form-control operating_time" value="{{ $item->operating_time }}"></td>
                                                    <td><input type="number" name="manu_total_cost[]" class="form-control total_cost" value="{{ $item->manu_total_cost }}" readonly></td>
                                                    <td><textarea name="manu_description[]" class="form-control" rows="1">{{ $item->manu_description }}</textarea></td>
                                                    <td>
                                                        <select name="manu_production_stage_id[]" class="form-control select2 product-select" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->manu_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="manu_total[]" class="form-control manufacturing-total" value="{{ $item->manu_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="manufacturingAddRow"><i class="fa fa-plus"></i> إضافة</button>
                                            <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="manufacturing-grand-total">{{ $order->manufacturOrdersItem->sum('manu_total') }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('endLife')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                                        <span class="p-1 font-weight-bold"><i class="feather icon-trash-2"></i> المواد الهالكة (<span id="EndLifeCount">{{ $order->manufacturOrdersItem->count() }}</span>)</span>
                                        <i class="feather icon-plus-circle p-1"></i>
                                    </p>
                                    <div id="endLife" style="display: none">
                                        <table class="table table-striped" id="EndLifeTable">
                                            <thead style="background: #f8f8f8">
                                                <tr>
                                                    <th>المنتجات</th>
                                                    <th>السعر</th>
                                                    <th>الكمية</th>
                                                    <th>المرحلة الإنتاجية</th>
                                                    <th>الإجمالي</th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($order->manufacturOrdersItem as $item)
                                                <tr>
                                                    <td>
                                                        <select name="end_life_product_id[]" class="form-control select2 end-life-product-select">
                                                            <option value="" disabled selected>-- اختر البند --</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" {{ $item->end_life_product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="end_life_unit_price[]" class="form-control end-life-unit-price" value="{{ $item->end_life_unit_price }}"></td>
                                                    <td><input type="number" name="end_life_quantity[]" class="form-control end-life-quantity" value="{{ $item->end_life_quantity }}" min="1"></td>
                                                    <td>
                                                        <select name="end_life_production_stage_id[]" class="form-control select2 product-select" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->end_life_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="end_life_total[]" class="form-control end-life-total" value="{{ $item->end_life_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="EndLifeAddRow"><i class="fa fa-plus"></i> إضافة</button>
                                            <strong style="margin-left: 13rem;"><small class="text-muted">الإجمالي الكلي : </small><span class="end-life-grand-total">{{ $order->manufacturOrdersItem->sum('end_life_total') }}</span></strong>
                                        </div>
                                    </div>
                                </div>
                                <br><hr>

                                <div class="form-group col-md-6"></div>

                                <div class="form-group col-md-6">
                                    <div class="d-flex justify-content-between p-1" style="background: #CCF5FA;">
                                        <strong>إجمالي التكلفة : </strong>
                                        <strong class="total-cost">{{ $order->last_total_cost }} ر.س</strong>
                                        <input type="hidden" name="last_total_cost" id="last_total_cost" value="{{ $order->last_total_cost }}">
                                    </div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </form>
        </div>
    </div>

@section('styles')
    <!-- Select2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- SweetAlert2 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">

    <style>
        .section-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .section-header:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
        }

        .section-collapsed {
            display: none;
        }

        .section-expanded {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .count-badge {
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            margin-left: 8px;
        }

        .total-cost-box {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .btn-outline-success:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(40, 167, 69, 0.3);
        }

        .btn-outline-danger:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(220, 53, 69, 0.3);
        }
    </style>
@endsection

@section('scripts')
    <!-- Select2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.all.min.js"></script>

    <script>
        $(document).ready(function() {
            // تهيئة Select2
            $('.select2').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                placeholder: 'اختر من القائمة...',
                allowClear: true
            });

            // عرض رسائل النجاح والخطأ
            @if(session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if(session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        });

        // تبديل عرض الأقسام
        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = document.getElementById(sectionId + 'Icon');

            if (section.classList.contains('section-collapsed')) {
                section.classList.remove('section-collapsed');
                section.classList.add('section-expanded');
                icon.classList.remove('icon-plus-circle');
                icon.classList.add('icon-minus-circle');
            } else {
                section.classList.remove('section-expanded');
                section.classList.add('section-collapsed');
                icon.classList.remove('icon-minus-circle');
                icon.classList.add('icon-plus-circle');
            }
        }

        // حساب التكلفة الإجمالية
        function updateTotalCost() {
            let totalCost = 0;

            // إضافة إجمالي المواد الخام
            const rawMaterialsTotal = parseFloat(document.querySelector('.grand-total').textContent) || 0;
            totalCost += rawMaterialsTotal;

            // إضافة إجمالي المصروفات
            const expensesTotal = parseFloat(document.querySelector('.expenses-grand-total').textContent) || 0;
            totalCost += expensesTotal;

            // إضافة إجمالي التصنيع
            const manufacturingTotal = parseFloat(document.querySelector('.manufacturing-grand-total').textContent) || 0;
            totalCost += manufacturingTotal;

            // طرح إجمالي المواد الهالكة
            const endLifeTotal = parseFloat(document.querySelector('.end-life-grand-total').textContent) || 0;
            totalCost -= endLifeTotal;

            // تحديث عرض التكلفة الإجمالية
            document.querySelector('.total-cost').textContent = totalCost.toFixed(2);
            document.getElementById('last_total_cost').value = totalCost.toFixed(2);
        }
    </script>
@endsection
