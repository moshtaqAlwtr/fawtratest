@extends('master')

@section('title')
تعديل قائمة مواد الإنتاج
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
            background: linear-gradient(135deg, #ddd6fe 0%, #e0e7ff 100%) !important;
            border-radius: 10px;
            padding: 15px !important;
            margin: 10px 0;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .section-header:hover {
            background: linear-gradient(135deg, #c4b5fd 0%, #ddd6fe 100%) !important;
            border-color: #8b5cf6;
            transform: translateX(5px);
        }

        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
            margin-bottom: 25px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 15px 40px rgba(0,0,0,0.15);
        }

        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            transition: all 0.3s ease;
            padding: 12px 15px;
        }

        .form-control:focus, .form-select:focus {
            border-color: #74b9ff;
            box-shadow: 0 0 20px rgba(116, 185, 255, 0.3);
            transform: translateY(-2px);
        }

        .btn {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .table {
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
        }

        .table thead th {
            background: linear-gradient(135deg, #ffffff 0%, #fefefe 100%);
            color: black;
            border: none;
            padding: 20px 15px;
            font-weight: 600;
        }

        .table tbody tr:hover {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        .total-cost-box {
            background: linear-gradient(135deg, #00cec9 0%, #00b894 100%);
            border-radius: 15px;
            padding: 20px;
            color: white;
            box-shadow: 0 10px 30px rgba(0,203,201,0.3);
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255,255,255,.3);
            border-radius: 50%;
            border-top-color: #fff;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .swal2-popup {
            border-radius: 20px !important;
        }

        .alert {
            border-radius: 15px;
            border: none;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .content-header {

            color: white;
            border-radius: 15px;
            margin-bottom: 20px;
            padding: 20px;
        }

        .breadcrumb {
            background: rgba(255,255,255,0.2);
            border-radius: 10px;
        }

        .breadcrumb-item a {
            color: rgba(255,255,255,0.9);
            text-decoration: none;
        }

        .breadcrumb-item.active {
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="fas fa-edit me-2"></i>تعديل قائمة مواد الإنتاج
                    </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href=""><i class="fas fa-home me-2"></i>الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل</li>
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
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li><i class="fas fa-exclamation-circle me-2"></i>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                </div>
            @endif

            @if (session('success'))
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                </div>
            @endif

            <form class="form-horizontal" action="{{ route('Bom.update', $productionMaterial->id) }}" method="POST" enctype="multipart/form-data" id="bomEditForm">
                @csrf
                @method('PUT')

                <!-- Header Actions Card -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label><i class="fas fa-info-circle me-2"></i>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>
                            <div>
                                <button type="button" class="btn btn-outline-danger" onclick="cancelEdit()">
                                    <i class="fa fa-ban me-2"></i>إلغاء
                                </button>
                                <button type="submit" class="btn btn-outline-primary" id="updateBtn">
                                    <i class="fa fa-save me-2"></i>تحديث
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Basic Information Card -->
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">
                                <i class="fas fa-edit me-2"></i>تعديل قائمة مواد الإنتاج
                            </h4>
                        </div>

                        <div class="card-body">
                            <div class="row g-4">
                                <div class="form-group col-md-4">
                                    <label for="">الاسم <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="name" value="{{ $productionMaterial->name }}" required>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="">كود <span style="color: red">*</span></label>
                                    <input type="text" class="form-control" name="code" value="{{ $productionMaterial->code }}" required>
                                </div>

                                <div class="form-group col-md-2 mt-2">
                                    <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch1" name="status" value="1" {{ $productionMaterial->status == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch1"></label>
                                        <span class="switch-label">نشط</span>
                                    </div>
                                </div>

                                <div class="form-group col-md-2 mt-2">
                                    <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                                        <input type="checkbox" class="custom-control-input" id="customSwitch2" name="default" value="1" {{ $productionMaterial->default == 1 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="customSwitch2"></label>
                                        <span class="switch-label">الافتراضي</span>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">المنتجات <span class="text-danger">*</span></label>
                                    <select class="form-control" name="product_id" required>
                                        @foreach ($products as $product)
                                            <option value="{{ $product->id }}" {{ $productionMaterial->product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحساب <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="account_id" required>
                                        <option value="" disabled>-- اختر الحساب --</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}" {{ $productionMaterial->account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label>الكمية <span style="color: red">*</span></label>
                                    <input type="number" class="form-control" name="quantity" value="{{ $productionMaterial->quantity }}" min="1" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">مسار الانتاج <span class="text-danger">*</span></label>
                                    <select class="form-control" id="basicSelect" name="production_path_id" required>
                                        <option value="" disabled>-- اختر المسار الانتاج --</option>
                                        @foreach ($paths as $path)
                                            <option value="{{ $path->id }}" {{ $productionMaterial->production_path_id == $path->id ? 'selected' : '' }}>{{ $path->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Raw Materials Section -->
                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('rawMaterials')" class="d-flex justify-content-between section-header">
                                        <span class="p-1 font-weight-bold">
                                            <i class="feather icon-package me-2"></i> المواد الخام (<span id="rawMaterialCount">{{ $productionMaterialItems->where('raw_product_id', '!=', null)->count() }}</span>)
                                        </span>
                                        <i class="feather icon-minus-circle p-1"></i>
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
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productionMaterialItems->where('raw_product_id', '!=', null) as $item)
                                                <tr>
                                                    <td>
                                                        <select name="raw_product_id[]" class="form-control select2 product-select" required>
                                                            <option value="" disabled>-- اختر البند --</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}" {{ $item->raw_product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="raw_unit_price[]" class="form-control unit-price" value="{{ $item->raw_unit_price }}" readonly></td>
                                                    <td><input type="number" name="raw_quantity[]" class="form-control quantity" value="{{ $item->raw_quantity }}" min="1" required></td>
                                                    <td>
                                                        <select name="raw_production_stage_id[]" class="form-control select2" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->raw_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="raw_total[]" class="form-control total" value="{{ $item->raw_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="addRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="grand-total">{{ $productionMaterialItems->where('raw_product_id', '!=', null)->sum('raw_total') ?? 0 }}</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Expenses Section -->
                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('expenses')" class="d-flex justify-content-between section-header">
                                        <span class="p-1 font-weight-bold">
                                            <i class="fa fa-money me-2"></i> المصروفات (<span id="rowExpensesCount">{{ $productionMaterialItems->where('expenses_account_id', '!=', null)->count() }}</span>)
                                        </span>
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
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productionMaterialItems->where('expenses_account_id', '!=', null) as $item)
                                                <tr>
                                                    <td>
                                                        <select name="expenses_account_id[]" class="form-control select2" required>
                                                            <option value="" disabled>-- اختر الحساب --</option>
                                                            @foreach ($accounts as $account)
                                                                <option value="{{ $account->id }}" {{ $item->expenses_account_id == $account->id ? 'selected' : '' }}>{{ $account->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="expenses_cost_type[]" class="form-control select2">
                                                            <option value="1" {{ $item->expenses_cost_type == 1 ? 'selected' : '' }}>مبلغ ثابت</option>
                                                            <option value="2" {{ $item->expenses_cost_type == 2 ? 'selected' : '' }}>بناءً على الكمية</option>
                                                            <option value="3" {{ $item->expenses_cost_type == 3 ? 'selected' : '' }}>معادلة</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="expenses_price[]" class="form-control expenses-price" value="{{ $item->expenses_price }}" required></td>
                                                    <td><textarea name="expenses_description[]" class="form-control" rows="2">{{ $item->expenses_description }}</textarea></td>
                                                    <td>
                                                        <select name="expenses_production_stage_id[]" class="form-control select2" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->expenses_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="expenses_total[]" class="form-control expenses-total" value="{{ $item->expenses_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="ExpensesAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="expenses-grand-total">{{ $productionMaterialItems->where('expenses_account_id', '!=', null)->sum('expenses_total') ?? 0 }}</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Manufacturing Section -->
                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('manufacturing')" class="d-flex justify-content-between section-header">
                                        <span class="p-1 font-weight-bold">
                                            <i class="feather icon-settings me-2"></i> عمليات التصنيع (<span id="manufacturingCount">{{ $productionMaterialItems->where('workstation_id', '!=', null)->count() }}</span>)
                                        </span>
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
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productionMaterialItems->where('workstation_id', '!=', null) as $item)
                                                <tr>
                                                    <td>
                                                        <select name="workstation_id[]" class="form-control select2" required>
                                                            <option value="" disabled>-- اختر محطة العمل --</option>
                                                            @foreach ($workstations as $workstation)
                                                                <option value="{{ $workstation->id }}" {{ $item->workstation_id == $workstation->id ? 'selected' : '' }}>{{ $workstation->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <select name="manu_cost_type[]" class="form-control select2">
                                                            <option value="1" {{ $item->manu_cost_type == 1 ? 'selected' : '' }}>مبلغ ثابت</option>
                                                            <option value="2" {{ $item->manu_cost_type == 2 ? 'selected' : '' }}>بناءً على الكمية</option>
                                                            <option value="3" {{ $item->manu_cost_type == 3 ? 'selected' : '' }}>معادلة</option>
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="operating_time[]" class="form-control operating_time" value="{{ $item->operating_time }}" required></td>
                                                    <td><input type="number" name="manu_total_cost[]" class="form-control total_cost" value="{{ $item->manu_total_cost }}"></td>
                                                    <td><textarea name="manu_description[]" class="form-control" rows="2">{{ $item->manu_description }}</textarea></td>
                                                    <td>
                                                        <select name="manu_production_stage_id[]" class="form-control select2" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->manu_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="manu_total[]" class="form-control manufacturing-total" value="{{ $item->manu_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="manufacturingAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="manufacturing-grand-total">{{ $productionMaterialItems->where('workstation_id', '!=', null)->sum('manu_total') ?? 0 }}</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- End Life Materials Section -->
                                <div class="form-group col-md-12">
                                    <p onclick="toggleSection('endLife')" class="d-flex justify-content-between section-header">
                                        <span class="p-1 font-weight-bold">
                                            <i class="feather icon-trash-2 me-2"></i> المواد الهالكة (<span id="EndLifeCount">{{ $productionMaterialItems->where('end_life_product_id', '!=', null)->count() }}</span>)
                                        </span>
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
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($productionMaterialItems->where('end_life_product_id', '!=', null) as $item)
                                                <tr>
                                                    <td>
                                                        <select name="end_life_product_id[]" class="form-control select2 end-life-product-select" required>
                                                            <option value="" disabled>-- اختر البند --</option>
                                                            @foreach ($products as $product)
                                                                <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" {{ $item->end_life_product_id == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="end_life_unit_price[]" class="form-control end-life-unit-price" value="{{ $item->end_life_unit_price }}"></td>
                                                    <td><input type="number" name="end_life_quantity[]" class="form-control end-life-quantity" value="{{ $item->end_life_quantity }}" min="1" required></td>
                                                    <td>
                                                        <select name="end_life_production_stage_id[]" class="form-control select2" required>
                                                            @foreach ($stages as $stage)
                                                                <option value="{{ $stage->id }}" {{ $item->end_life_production_stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->stage_name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td><input type="number" name="end_life_total[]" class="form-control end-life-total" value="{{ $item->end_life_total }}" readonly></td>
                                                    <td>
                                                        <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                        <hr>
                                        <div class="d-flex justify-content-between mt-2">
                                            <button type="button" class="btn btn-outline-success btn-sm" id="EndLifeAddRow">
                                                <i class="fa fa-plus me-2"></i> إضافة
                                            </button>
                                            <strong style="margin-left: 13rem;">
                                                <small class="text-muted">الإجمالي الكلي : </small>
                                                <span class="end-life-grand-total">{{ $productionMaterialItems->where('end_life_product_id', '!=', null)->sum('end_life_total') ?? 0 }}</span>
                                            </strong>
                                        </div>
                                    </div>
                                </div>

                                <br><hr>

                                <!-- Total Cost Summary -->
                                <div class="form-group col-md-6"></div>
                                <div class="form-group col-md-6">
                                    <div class="total-cost-box text-center">
                                        <strong>
                                            <i class="fas fa-calculator me-2"></i>
                                            إجمالي التكلفة : <span class="total-cost">{{ $productionMaterial->last_total_cost }}</span> ر.س
                                        </strong>
                                        <input type="hidden" name="last_total_cost" id="last_total_cost" value="{{ $productionMaterial->last_total_cost }}">
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Show success message on page load if session has success
    document.addEventListener('DOMContentLoaded', function() {
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

        // Initialize totals on page load
        updateTotalCost();
        updateAllCounts();
    });

    // Form submission with validation
    document.getElementById('bomEditForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validate form
        if (!validateBomEditForm()) {
            return;
        }

        Swal.fire({
            title: 'تأكيد التحديث',
            text: 'هل أنت متأكد من تحديث قائمة مواد الإنتاج؟',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، حدث',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                const updateBtn = document.getElementById('updateBtn');
                updateBtn.disabled = true;
                updateBtn.innerHTML = '<div class="loading-spinner me-2"></div> جاري التحديث...';

                Swal.fire({
                    title: 'جاري التحديث...',
                    text: 'الرجاء الانتظار',
                    icon: 'info',
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    willOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                this.submit();
            }
        });
    });

    // Cancel edit function
    function cancelEdit() {
        Swal.fire({
            title: 'تأكيد الإلغاء',
            text: 'هل أنت متأكد من إلغاء التعديل؟ سيتم فقدان جميع التغييرات غير المحفوظة.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم، إلغاء',
            cancelButtonText: 'العودة للتعديل',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '{{ route("BOM.index") }}';
            }
        });
    }

    // Validate BOM edit form
    function validateBomEditForm() {
        const errors = [];

        // Check required fields
        const name = document.querySelector('input[name="name"]').value.trim();
        const code = document.querySelector('input[name="code"]').value.trim();
        const quantity = document.querySelector('input[name="quantity"]').value;
        const productId = document.querySelector('select[name="product_id"]').value;
        const accountId = document.querySelector('select[name="account_id"]').value;
        const productionPathId = document.querySelector('select[name="production_path_id"]').value;

        if (!name) errors.push('اسم قائمة مواد الإنتاج مطلوب');
        if (!code) errors.push('كود قائمة مواد الإنتاج مطلوب');
        if (!quantity || quantity <= 0) errors.push('الكمية مطلوبة ويجب أن تكون أكبر من صفر');
        if (!productId) errors.push('يجب اختيار المنتج');
        if (!accountId) errors.push('يجب اختيار الحساب');
        if (!productionPathId) errors.push('يجب اختيار مسار الإنتاج');

        // Check if at least one material is added
        const rawMaterialRows = document.querySelectorAll('#itemsTable tbody tr').length;
        if (rawMaterialRows === 0) {
            errors.push('يجب إضافة مادة خام واحدة على الأقل');
        }

        if (errors.length > 0) {
            Swal.fire({
                title: 'خطأ في البيانات',
                html: `
                    <div style="text-align: right; max-height: 300px; overflow-y: auto;">
                        <ul style="list-style: none; padding: 0;">
                            ${errors.map(error => `
                                <li style="color: #dc3545; margin: 8px 0; padding: 10px; background: #f8d7da; border-radius: 8px; border-right: 4px solid #dc3545;">
                                    <i class="fa fa-exclamation-triangle me-2"></i> ${error}
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `,
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#d33',
                customClass: {
                    htmlContainer: 'text-right'
                }
            });
            return false;
        }

        return true;
    }

    // Update total cost function
    function updateLastTotalCost() {
        const totalCostText = document.querySelector('.total-cost').textContent;
        const totalCost = parseFloat(totalCostText.replace(/[^\d.-]/g, '')) || 0;
        document.getElementById('last_total_cost').value = totalCost.toFixed(2);
    }

    // Update counters functions
    function updateRawMaterialCount() {
        const rowCount = document.querySelectorAll('#itemsTable tbody tr').length;
        document.getElementById('rawMaterialCount').textContent = rowCount;
    }

    function updateRawExpensesCount() {
        const rowCount = document.querySelectorAll('#ExpensesTable tbody tr').length;
        document.getElementById('rowExpensesCount').textContent = rowCount;
    }

    function updateManufacturingCount() {
        const rowCount = document.querySelectorAll('#manufacturingTable tbody tr').length;
        document.getElementById('manufacturingCount').textContent = rowCount;
    }

    function updateEndLifeCount() {
        const rowCount = document.querySelectorAll('#EndLifeTable tbody tr').length;
        document.getElementById('EndLifeCount').textContent = rowCount;
    }

    function updateAllCounts() {
        updateRawMaterialCount();
        updateRawExpensesCount();
        updateManufacturingCount();
        updateEndLifeCount();
    }

    // Toggle section function
    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        const icon = section.previousElementSibling.querySelector('.feather:last-child');

        if (section.style.display === "none") {
            section.style.display = "block";
            icon.classList.remove('icon-plus-circle');
            icon.classList.add('icon-minus-circle');

            // Add smooth animation
            section.style.opacity = '0';
            section.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                section.style.transition = 'all 0.3s ease';
                section.style.opacity = '1';
                section.style.transform = 'translateY(0)';
            }, 10);
        } else {
            section.style.display = "none";
            icon.classList.remove('icon-minus-circle');
            icon.classList.add('icon-plus-circle');
        }
    }

    // Calculate total cost for all sections (مع تطبيق طرح المواد الهالكة)
    function updateTotalCost() {
        let totalCost = 0;

        // Add raw materials total
        const rawMaterialsTotal = parseFloat(document.querySelector('.grand-total').textContent) || 0;
        totalCost += rawMaterialsTotal;

        // Add expenses total
        const expensesTotal = parseFloat(document.querySelector('.expenses-grand-total').textContent) || 0;
        totalCost += expensesTotal;

        // Add manufacturing total
        const manufacturingTotal = parseFloat(document.querySelector('.manufacturing-grand-total').textContent) || 0;
        totalCost += manufacturingTotal;

        // Subtract end life total (طرح المواد الهالكة)
        const endLifeTotal = parseFloat(document.querySelector('.end-life-grand-total').textContent) || 0;
        totalCost -= endLifeTotal;

        // Update the total cost display
        document.querySelector('.total-cost').textContent = totalCost.toFixed(2);

        // Update the hidden input field
        updateLastTotalCost();
    }
</script>

<!-- Raw Materials Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
        const addRowButton = document.getElementById('addRow');

        // Function to calculate total for a row
        function calculateTotal(row) {
            const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
            const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
            const total = unitPrice * quantity;
            row.querySelector('.total').value = total.toFixed(2);
            updateGrandTotal();
            updateTotalCost();
        }

        // Function to update grand total
        function updateGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.total').forEach(totalInput => {
                grandTotal += parseFloat(totalInput.value) || 0;
            });
            document.querySelector('.grand-total').textContent = grandTotal.toFixed(2);
        }

        // Function to attach event listeners to a row
        function attachRowEvents(row) {
            const productSelect = row.querySelector('.product-select');
            const quantityInput = row.querySelector('.quantity');

            if (productSelect) {
                productSelect.addEventListener('change', function () {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const unitPrice = selectedOption.getAttribute('data-price');
                    row.querySelector('.unit-price').value = unitPrice || 0;
                    calculateTotal(row);
                });
            }

            if (quantityInput) {
                quantityInput.addEventListener('input', function () {
                    calculateTotal(row);
                });
            }
        }

        // Attach events to existing rows
        itemsTable.querySelectorAll('tr').forEach(attachRowEvents);

        // Add Row with SweetAlert confirmation
        addRowButton.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="raw_product_id[]" class="form-control select2 product-select" required>
                        <option value="" disabled selected>-- اختر البند --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="raw_unit_price[]" class="form-control unit-price" readonly></td>
                <td><input type="number" name="raw_quantity[]" class="form-control quantity" value="1" min="1" required></td>
                <td>
                    <select name="raw_production_stage_id[]" class="form-control select2" required>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="raw_total[]" class="form-control total" readonly></td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            itemsTable.appendChild(newRow);
            attachRowEvents(newRow);
            updateRawMaterialCount();
            updateTotalCost();

            // Show success toast
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تم إضافة مادة خام جديدة',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });

        // Remove Row with confirmation
        itemsTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');

                if (itemsTable.rows.length > 1) {
                    Swal.fire({
                        title: 'تأكيد الحذف',
                        text: 'هل أنت متأكد من حذف هذه المادة الخام؟',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'نعم، احذف',
                        cancelButtonText: 'إلغاء',
                        reverseButtons: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            row.remove();
                            updateGrandTotal();
                            updateRawMaterialCount();
                            updateTotalCost();

                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'تم حذف المادة الخام',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تحذير',
                        text: 'لا يمكنك حذف جميع الصفوف! يجب وجود مادة خام واحدة على الأقل.',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#3085d6'
                    });
                }
            }
        });
    });
</script>

<!-- Expenses Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ExpensesTable = document.getElementById('ExpensesTable').querySelector('tbody');
        const ExpensesAddRowButton = document.getElementById('ExpensesAddRow');

        // Function to calculate total for a row
        function calculateExpensesTotal(row) {
            const expensesPrice = parseFloat(row.querySelector('.expenses-price').value) || 0;
            row.querySelector('.expenses-total').value = expensesPrice.toFixed(2);
            updateExpensesGrandTotal();
            updateTotalCost();
        }

        // Function to update grand total
        function updateExpensesGrandTotal() {
            let expensesGrandTotal = 0;
            document.querySelectorAll('.expenses-total').forEach(totalInput => {
                expensesGrandTotal += parseFloat(totalInput.value) || 0;
            });
            document.querySelector('.expenses-grand-total').textContent = expensesGrandTotal.toFixed(2);
        }

        // Attach events to a row
        function attachExpensesRowEvents(row) {
            const priceInput = row.querySelector('.expenses-price');
            if (priceInput) {
                priceInput.addEventListener('input', function () {
                    calculateExpensesTotal(row);
                });
            }
        }

        // Attach events to existing rows
        ExpensesTable.querySelectorAll('tr').forEach(attachExpensesRowEvents);

        // Add Row
        ExpensesAddRowButton.addEventListener('click', function (e) {
            e.preventDefault();

            const exNewRow = document.createElement('tr');
            exNewRow.innerHTML = `
                <td>
                    <select name="expenses_account_id[]" class="form-control select2" required>
                        <option value="" disabled selected>-- اختر الحساب --</option>
                        @foreach ($accounts as $account)
                            <option value="{{ $account->id }}">{{ $account->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="expenses_cost_type[]" class="form-control select2">
                        <option value="1">مبلغ ثابت</option>
                        <option value="2">بناءً على الكمية</option>
                        <option value="3">معادلة</option>
                    </select>
                </td>
                <td><input type="number" name="expenses_price[]" class="form-control expenses-price" required></td>
                <td><textarea name="expenses_description[]" class="form-control" rows="2"></textarea></td>
                <td>
                    <select name="expenses_production_stage_id[]" class="form-control select2" required>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="expenses_total[]" class="form-control expenses-total" readonly></td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            ExpensesTable.appendChild(exNewRow);
            attachExpensesRowEvents(exNewRow);
            updateRawExpensesCount();
            updateTotalCost();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تم إضافة مصروف جديد',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });

        // Remove Row
        ExpensesTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذا المصروف؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        updateExpensesGrandTotal();
                        updateRawExpensesCount();
                        updateTotalCost();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم حذف المصروف',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
</script>

<!-- Manufacturing Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const manufacturingTable = document.getElementById('manufacturingTable').querySelector('tbody');
        const manufacturingAddRowButton = document.getElementById('manufacturingAddRow');

        // Function to calculate total for a row
        function calculateManufacturingTotal(row) {
            const totalCost = parseFloat(row.querySelector('.total_cost').value) || 0;
            const operatingTime = parseFloat(row.querySelector('.operating_time').value) || 0;
            const manufacturingTotal = totalCost * operatingTime;

            row.querySelector('.manufacturing-total').value = manufacturingTotal.toFixed(2);
            updateManufacturingGrandTotal();
            updateTotalCost();
        }

        // Function to update grand total
        function updateManufacturingGrandTotal() {
            let manufacturingGrandTotal = 0;
            document.querySelectorAll('.manufacturing-total').forEach(totalInput => {
                manufacturingGrandTotal += parseFloat(totalInput.value) || 0;
            });
            document.querySelector('.manufacturing-grand-total').textContent = manufacturingGrandTotal.toFixed(2);
        }

        // Function to fetch total_cost from the server
        function fetchTotalCost(workstationId, row) {
            if (!workstationId) return;

            fetch(`/api/workstations/${workstationId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.total_cost !== undefined) {
                        row.querySelector('.total_cost').value = data.total_cost;
                        calculateManufacturingTotal(row);
                    } else {
                        console.error("total_cost غير موجود في الاستجابة:", data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching total cost:', error);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'خطأ في جلب بيانات محطة العمل',
                        showConfirmButton: false,
                        timer: 3000
                    });
                });
        }

        // Attach events to a row
        function attachManufacturingRowEvents(row) {
            const totalCostInput = row.querySelector('.total_cost');
            const operatingTimeInput = row.querySelector('.operating_time');
            const workstationSelect = row.querySelector('select[name="workstation_id[]"]');

            if (totalCostInput) {
                totalCostInput.addEventListener('input', function () {
                    calculateManufacturingTotal(row);
                });
            }

            if (operatingTimeInput) {
                operatingTimeInput.addEventListener('input', function () {
                    calculateManufacturingTotal(row);
                });
            }

            if (workstationSelect) {
                workstationSelect.addEventListener('change', function () {
                    const workstationId = this.value;
                    fetchTotalCost(workstationId, row);
                });
            }
        }

        // Attach events to existing rows
        manufacturingTable.querySelectorAll('tr').forEach(attachManufacturingRowEvents);

        // Add Row
        manufacturingAddRowButton.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="workstation_id[]" class="form-control select2" required>
                        <option value="" disabled selected>-- اختر محطة العمل --</option>
                        @foreach ($workstations as $workstation)
                            <option value="{{ $workstation->id }}">{{ $workstation->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <select name="manu_cost_type[]" class="form-control select2">
                        <option value="1">مبلغ ثابت</option>
                        <option value="2">بناءً على الكمية</option>
                        <option value="3">معادلة</option>
                    </select>
                </td>
                <td><input type="number" name="operating_time[]" class="form-control operating_time" required></td>
                <td><input type="number" name="manu_total_cost[]" class="form-control total_cost" ></td>
                <td><textarea name="manu_description[]" class="form-control" rows="2"></textarea></td>
                <td>
                    <select name="manu_production_stage_id[]" class="form-control select2" required>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="manu_total[]" class="form-control manufacturing-total" readonly></td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            manufacturingTable.appendChild(newRow);
            attachManufacturingRowEvents(newRow);
            updateManufacturingCount();
            updateTotalCost();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تم إضافة عملية تصنيع جديدة',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });

        // Remove Row
        manufacturingTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف عملية التصنيع هذه؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        updateManufacturingGrandTotal();
                        updateManufacturingCount();
                        updateTotalCost();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم حذف عملية التصنيع',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
</script>

<!-- End Life Materials Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const EndLifeTable = document.getElementById('EndLifeTable').querySelector('tbody');
        const EndLifeAddRowButton = document.getElementById('EndLifeAddRow');

        // Function to calculate total for a row
        function calculateEndLifeTotal(row) {
            const EndLifeUnitPrice = parseFloat(row.querySelector('.end-life-unit-price').value) || 0;
            const EndLifeQuantity = parseFloat(row.querySelector('.end-life-quantity').value) || 0;
            const total = EndLifeUnitPrice * EndLifeQuantity;
            row.querySelector('.end-life-total').value = total.toFixed(2);
            updateEndLifeGrandTotal();
            updateTotalCost();
        }

        // Function to update grand total
        function updateEndLifeGrandTotal() {
            let grandTotal = 0;
            document.querySelectorAll('.end-life-total').forEach(totalInput => {
                grandTotal += parseFloat(totalInput.value) || 0;
            });
            document.querySelector('.end-life-grand-total').textContent = grandTotal.toFixed(2);
        }

        // Function to attach event listeners to a row
        function attachEndLifeRowEvents(row) {
            const productSelect = row.querySelector('.end-life-product-select');
            const quantityInput = row.querySelector('.end-life-quantity');
            const priceInput = row.querySelector('.end-life-unit-price');

            if (productSelect) {
                productSelect.addEventListener('change', function () {
                    const selectedOption = productSelect.options[productSelect.selectedIndex];
                    const unitPrice = selectedOption.getAttribute('data-price');
                    row.querySelector('.end-life-unit-price').value = unitPrice || 0;
                    calculateEndLifeTotal(row);
                });
            }

            if (quantityInput) {
                quantityInput.addEventListener('input', function () {
                    calculateEndLifeTotal(row);
                });
            }

            if (priceInput) {
                priceInput.addEventListener('input', function () {
                    calculateEndLifeTotal(row);
                });
            }
        }

        // Attach events to existing rows
        EndLifeTable.querySelectorAll('tr').forEach(attachEndLifeRowEvents);

        // Add Row
        EndLifeAddRowButton.addEventListener('click', function (e) {
            e.preventDefault();

            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td>
                    <select name="end_life_product_id[]" class="form-control select2 end-life-product-select" required>
                        <option value="" disabled selected>-- اختر البند --</option>
                        @foreach ($products as $product)
                            <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="end_life_unit_price[]" class="form-control end-life-unit-price"></td>
                <td><input type="number" name="end_life_quantity[]" class="form-control end-life-quantity" value="1" min="1" required></td>
                <td>
                    <select name="end_life_production_stage_id[]" class="form-control select2" required>
                        @foreach ($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->stage_name }}</option>
                        @endforeach
                    </select>
                </td>
                <td><input type="number" name="end_life_total[]" class="form-control end-life-total" readonly></td>
                <td>
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            `;

            EndLifeTable.appendChild(newRow);
            attachEndLifeRowEvents(newRow);
            updateEndLifeCount();
            updateTotalCost();

            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'تم إضافة مادة هالكة جديدة',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        });

        // Remove Row
        EndLifeTable.addEventListener('click', function (e) {
            if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                const row = e.target.closest('tr');

                Swal.fire({
                    title: 'تأكيد الحذف',
                    text: 'هل أنت متأكد من حذف هذه المادة الهالكة؟',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'نعم، احذف',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        row.remove();
                        updateEndLifeGrandTotal();
                        updateEndLifeCount();
                        updateTotalCost();

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'تم حذف المادة الهالكة',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                });
            }
        });
    });
</script>

@endsection
