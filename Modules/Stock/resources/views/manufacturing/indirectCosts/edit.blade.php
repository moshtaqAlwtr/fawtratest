@extends('master')

@section('title')
التكاليف غير المباشرة - تعديل
@stop

@section('css')
    <style>
        .section-header {
            cursor: pointer;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        .section-header:hover {
            background-color: #c8cbcf !important;
        }
        .restriction-info {
            font-size: 0.85em;
            color: #666;
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 4px;
            border-left: 3px solid #007bff;
        }
        .date-notice {
            background-color: #e3f2fd;
            border: 1px solid #bbdefb;
            color: #1976d2;
            padding: 10px;
            border-radius: 4px;
            margin: 10px 0;
        }
        .loading-restrictions {
            text-align: center;
            padding: 20px;
            color: #666;
        }
        .table th {
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        .btn-outline-success:hover {
            color: #fff;
            background-color: #28a745;
            border-color: #28a745;
        }
        .btn-outline-danger:hover {
            color: #fff;
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 8px;
        }
        .alert {
            border-radius: 6px;
        }
        .breadcrumb-item + .breadcrumb-item::before {
            content: ">";
        }
    </style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="feather icon-edit"></i> التكاليف غير المباشرة - تعديل
                    </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('manufacturing.indirectcosts.index') }}">التكاليف غير المباشرة</a></li>
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
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h5><i class="icon fa fa-ban"></i> خطأ في البيانات!</h5>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <i class="icon fa fa-ban"></i> {{ session('error') }}
                </div>
            @endif

            <form class="form-horizontal" id="indirectCostEditForm"
                  action="{{ route('manufacturing.indirectcosts.update', $indirectCost->id) }}"
                  method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- بطاقة الأزرار -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex align-items-center">
                                <i class="feather icon-info text-primary mr-1"></i>
                                <label class="mb-0">الحقول التي عليها علامة <span style="color: red">*</span> إلزامية</label>
                            </div>

                            <div class="btn-group">
                                <a href="{{ route('manufacturing.indirectcosts.index') }}" class="btn btn-outline-danger">
                                    <i class="fa fa-ban mr-1"></i>إلغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary" id="updateButton">
                                    <i class="fa fa-save mr-1"></i>تحديث
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة البيانات الأساسية -->
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">
                            <i class="feather icon-settings mr-2"></i>معلومات التكاليف غير المباشرة
                        </h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="account_id">
                                    <i class="feather icon-credit-card mr-1"></i>الحساب
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control select2" id="account_id" name="account_id" required>
                                    <option value="" disabled>-- اختر الحساب --</option>
                                    @foreach ($accounts as $account)
                                        <option value="{{ $account->id }}"
                                                {{ $indirectCost->account_id == $account->id ? 'selected' : '' }}>
                                            {{ $account->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="from_date">
                                    <i class="feather icon-calendar mr-1"></i>التاريخ من
                                    <span style="color: red">*</span>
                                </label>
                                <input type="date" class="form-control" id="from_date" name="from_date"
                                       value="{{ \Carbon\Carbon::parse($indirectCost->from_date)->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group col-md-3">
                                <label for="to_date">
                                    <i class="feather icon-calendar mr-1"></i>التاريخ إلى
                                    <span style="color: red">*</span>
                                </label>
                                <input type="date" class="form-control" id="to_date" name="to_date"
                                       value="{{ \Carbon\Carbon::parse($indirectCost->to_date)->format('Y-m-d') }}" required>
                            </div>

                            <div class="form-group col-md-12">
                                <label class="d-block mb-2">
                                    <i class="feather icon-target mr-1"></i>نوع التوزيع
                                    <span style="color: red">*</span>
                                </label>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="based_on"
                                           id="quantity_based" value="1"
                                           {{ $indirectCost->based_on == 1 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="quantity_based">
                                        <i class="feather icon-package mr-1"></i>بناءً على الكمية
                                    </label>
                                </div>
                                <div class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="based_on"
                                           id="cost_based" value="2"
                                           {{ $indirectCost->based_on == 2 ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="cost_based">
                                        <i class="feather icon-dollar-sign mr-1"></i>بناءً على التكلفة
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة القيود اليومية -->
                <div class="card">
                    <div class="card-header">
                        <p onclick="toggleSection('expenses')"
                           class="d-flex justify-content-between align-items-center section-header mb-0"
                           style="cursor: pointer;">
                            <span class="font-weight-bold">
                                <i class="fa fa-money mr-2"></i>القيود اليومية
                                (<span id="rowExpensesCount">{{ $indirectCost->indirectCostItems->where('restriction_id', '!=', null)->count() }}</span>)
                            </span>
                            <i class="feather icon-chevron-down"></i>
                        </p>
                    </div>

                    <!-- رسالة تنبيه للتواريخ -->
                    <div id="dateNotice" class="date-notice mx-3" style="display: none;">
                        <i class="fa fa-info-circle mr-2"></i>
                        عند تغيير التواريخ، سيتم تحديث القيود المتاحة في الفترة الجديدة.
                    </div>

                    <div id="expenses" class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="itemsTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50%">
                                            <i class="feather icon-file-text mr-1"></i>القيد المحاسبي
                                        </th>
                                        <th width="30%">
                                            <i class="feather icon-dollar-sign mr-1"></i>المجموع
                                        </th>
                                        <th width="20%" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $expenseItems = $indirectCost->indirectCostItems->where('restriction_id', '!=', null) @endphp
                                    @if($expenseItems->count() > 0)
                                        @foreach ($expenseItems as $item)
                                            <tr>
                                                <td>
                                                    <select class="form-control restriction-select" name="restriction_id[]">
                                                        <option value="" disabled>-- اختر القيد --</option>
                                                        @foreach ($restrictions as $restriction)
                                                            <option value="{{ $restriction->id }}"
                                                                    data-reference="{{ $restriction->reference_number }}"
                                                                    data-description="{{ $restriction->description }}"
                                                                    data-date="{{ $restriction->date->format('Y-m-d') }}"
                                                                    {{ $item->restriction_id == $restriction->id ? 'selected' : '' }}>
                                                                {{ $restriction->reference_number }} - {{ Str::limit($restriction->description, 50) }}
                                                                <small class="text-muted">({{ $restriction->date->format('Y-m-d') }})</small>
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    @if($item->restriction_id && $item->journalEntry)
                                                        <div class="restriction-info mt-2">
                                                            <small class="text-muted">
                                                                <strong>الوصف:</strong> <span class="restriction-description">{{ $item->journalEntry->description }}</span><br>
                                                                <strong>التاريخ:</strong> <span class="restriction-date">{{ $item->journalEntry->date->format('Y-m-d') }}</span>
                                                            </small>
                                                        </div>
                                                    @else
                                                        <div class="restriction-info mt-2" style="display: none;">
                                                            <small class="text-muted">
                                                                <strong>الوصف:</strong> <span class="restriction-description"></span><br>
                                                                <strong>التاريخ:</strong> <span class="restriction-date"></span>
                                                            </small>
                                                        </div>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" class="form-control expenses-total"
                                                               name="restriction_total[]" value="{{ $item->restriction_total }}"
                                                               min="0" placeholder="0.00">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">ر.س</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                                                            title="حذف الصف">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select class="form-control select2 restriction-select" name="restriction_id[]">
                                                    <option value="" disabled selected>-- اختر القيد --</option>
                                                    @foreach ($restrictions as $restriction)
                                                        <option value="{{ $restriction->id }}"
                                                                data-reference="{{ $restriction->reference_number }}"
                                                                data-description="{{ $restriction->description }}"
                                                                data-date="{{ $restriction->date->format('Y-m-d') }}">
                                                            {{ $restriction->reference_number }} - {{ Str::limit($restriction->description, 50) }}
                                                            <small class="text-muted">({{ $restriction->date->format('Y-m-d') }})</small>
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="restriction-info mt-2" style="display: none;">
                                                    <small class="text-muted">
                                                        <strong>الوصف:</strong> <span class="restriction-description"></span><br>
                                                        <strong>التاريخ:</strong> <span class="restriction-date"></span>
                                                    </small>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" class="form-control expenses-total"
                                                           name="restriction_total[]" value="0" min="0" placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">ر.س</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                                                        title="حذف الصف">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-success" id="ExpensesAddRow">
                                <i class="fa fa-plus mr-1"></i>إضافة قيد
                            </button>
                            <div class="text-right">
                                <h5 class="mb-0">
                                    <span class="text-muted">الإجمالي: </span>
                                    <span class="text-primary font-weight-bold expenses-grand-total">0.00</span>
                                    <small class="text-muted">ر.س</small>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة أوامر التصنيع -->
                <div class="card">
                    <div class="card-header">
                        <p onclick="toggleSection('manufacturing')"
                           class="d-flex justify-content-between align-items-center section-header mb-0"
                           style="cursor: pointer;">
                            <span class="font-weight-bold">
                                <i class="feather icon-package mr-2"></i>أوامر التصنيع
                                (<span id="rowManufacturingCount">{{ $indirectCost->indirectCostItems->where('manufacturing_order_id', '!=', null)->count() }}</span>)
                            </span>
                            <i class="feather icon-chevron-down"></i>
                        </p>
                    </div>

                    <div id="manufacturing" class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="manufacturingTable">
                                <thead class="thead-light">
                                    <tr>
                                        <th width="50%">
                                            <i class="feather icon-package mr-1"></i>طلب التصنيع
                                        </th>
                                        <th width="30%">
                                            <i class="feather icon-dollar-sign mr-1"></i>المبلغ
                                        </th>
                                        <th width="20%" class="text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $manufacturingItems = $indirectCost->indirectCostItems->where('manufacturing_order_id', '!=', null) @endphp
                                    @if($manufacturingItems->count() > 0)
                                        @foreach ($manufacturingItems as $item)
                                            <tr>
                                                <td>
                                                    <select name="manufacturing_order_id[]" class="form-control select2">
                                                        <option value="">-- اختر طلب التصنيع --</option>
                                                        @foreach ($manufacturing_orders as $manufacturing_order)
                                                            <option value="{{ $manufacturing_order->id }}"
                                                                    {{ $item->manufacturing_order_id == $manufacturing_order->id ? 'selected' : '' }}>
                                                                {{ $manufacturing_order->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="number" step="0.01" name="manufacturing_price[]"
                                                               class="form-control manufacturing-price"
                                                               value="{{ $item->manufacturing_price }}"
                                                               min="0" placeholder="0.00">
                                                        <div class="input-group-append">
                                                            <span class="input-group-text">ر.س</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                                                            title="حذف الصف">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td>
                                                <select name="manufacturing_order_id[]" class="form-control select2">
                                                    <option value="">-- اختر طلب التصنيع --</option>
                                                    @foreach ($manufacturing_orders as $manufacturing_order)
                                                        <option value="{{ $manufacturing_order->id }}">
                                                            {{ $manufacturing_order->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                            <td>
                                                <div class="input-group">
                                                    <input type="number" step="0.01" name="manufacturing_price[]"
                                                           class="form-control manufacturing-price" value="0"
                                                           min="0" placeholder="0.00">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text">ر.س</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <button type="button" class="btn btn-outline-danger btn-sm removeRow"
                                                        title="حذف الصف">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-success" id="ManufacturingAddRow">
                                <i class="fa fa-plus mr-1"></i>إضافة أمر تصنيع
                            </button>
                            <div class="text-right">
                                <h5 class="mb-0">
                                    <span class="text-muted">الإجمالي: </span>
                                    <span class="text-success font-weight-bold manufacturing-grand-total">0.00</span>
                                    <small class="text-muted">ر.س</small>
                                </h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- بطاقة الإجمالي العام -->
                <div class="card">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h4 class="mb-0">
                                    <i class="feather icon-calculator mr-2 text-warning"></i>
                                    المجموع الإجمالي للتكاليف غير المباشرة
                                </h4>
                            </div>
                            <div class="col-md-4 text-right">
                                <h2 class="mb-0">
                                    <span id="displayTotal" class="text-warning font-weight-bold">{{ number_format($indirectCost->total, 2) }}</span>
                                    <small class="text-muted">ر.س</small>
                                </h2>
                            </div>
                        </div>
                        <input type="hidden" id="total" name="total" value="{{ $indirectCost->total }}">
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 Library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.10.5/dist/sweetalert2.all.min.js"></script>
@php
    $restrictionsArray = $restrictions->map(function($restriction) {
        return [
            'id' => $restriction->id,
            'reference_number' => $restriction->reference_number,
            'description' => $restriction->description,
            'date' => $restriction->date->format('Y-m-d'),
            'display_text' => $restriction->reference_number . ' - ' .
                              \Illuminate\Support\Str::limit($restriction->description, 50) .
                              ' (' . $restriction->date->format('Y-m-d') . ')'
        ];
    });
@endphp


      <script>
    let availableRestrictions = @json($restrictionsArray);
</script>
    <script>

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            const icon = section.previousElementSibling.querySelector('i.feather');

            if (section.style.display === "none") {
                section.style.display = "block";
                icon.className = "feather icon-chevron-down";
            } else {
                section.style.display = "none";
                icon.className = "feather icon-chevron-up";
            }
        }

        // جلب القيود بناءً على التواريخ
        function fetchRestrictions() {
            const fromDate = document.getElementById('from_date').value;
            const toDate = document.getElementById('to_date').value;
            const dateNotice = document.getElementById('dateNotice');

            if (!fromDate || !toDate) {
                return;
            }

            if (fromDate > toDate) {
                Swal.fire({
                    icon: 'warning',
                    title: 'تنبيه',
                    text: 'التاريخ من يجب أن يكون أقل من أو يساوي التاريخ إلى',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // إظهار رسالة التنبيه
            dateNotice.style.display = 'block';

            // إظهار مؤشر التحميل
            showLoadingInRestrictions();

            // طلب Ajax لجلب القيود
            fetch('{{ route("manufacturing.indirectcosts.getRestrictionsByDate") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ||
                                   document.querySelector('input[name="_token"]').value
                },
                body: JSON.stringify({
                    from_date: fromDate,
                    to_date: toDate
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    availableRestrictions = data.restrictions;
                    updateRestrictionSelects();
                    dateNotice.style.display = 'none';

                    Swal.fire({
                        icon: 'success',
                        title: 'تم بنجاح',
                        text: `تم تحديث القيود المتاحة (${data.restrictions.length} قيد)`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'خطأ',
                        text: 'حدث خطأ في جلب القيود',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'خطأ في الاتصال',
                    text: 'حدث خطأ في الاتصال بالخادم',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#dc3545'
                });
                dateNotice.style.display = 'none';
            });
        }

        function showLoadingInRestrictions() {
            const selects = document.querySelectorAll('.restriction-select');
            selects.forEach(select => {
                select.innerHTML = '<option value="">جاري التحميل...</option>';
                select.disabled = true;
            });
        }

        function updateRestrictionSelects() {
            const selects = document.querySelectorAll('.restriction-select');

            selects.forEach(select => {
                const currentValue = select.value;
                select.innerHTML = '<option value="" disabled>-- اختر القيد --</option>';

                availableRestrictions.forEach(restriction => {
                    const option = document.createElement('option');
                    option.value = restriction.id;
                    option.textContent = restriction.display_text;
                    option.dataset.reference = restriction.reference_number;
                    option.dataset.description = restriction.description;
                    option.dataset.date = restriction.date;

                    if (currentValue == restriction.id) {
                        option.selected = true;
                    }

                    select.appendChild(option);
                });

                select.disabled = false;
            });
        }

        // عرض تفاصيل القيد عند الاختيار
        function handleRestrictionChange(selectElement) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const infoDiv = selectElement.parentNode.querySelector('.restriction-info');

            if (selectedOption.value) {
                const description = selectedOption.dataset.description || 'غير محدد';
                const date = selectedOption.dataset.date || 'غير محدد';

                infoDiv.querySelector('.restriction-description').textContent = description;
                infoDiv.querySelector('.restriction-date').textContent = date;
                infoDiv.style.display = 'block';
            } else {
                infoDiv.style.display = 'none';
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const ExpensesAddRowButton = document.getElementById('ExpensesAddRow');
            const manufacturingTable = document.getElementById('manufacturingTable').querySelector('tbody');
            const ManufacturingAddRowButton = document.getElementById('ManufacturingAddRow');
            const totalInput = document.getElementById('total');
            const displayTotal = document.getElementById('displayTotal');
            const fromDateInput = document.getElementById('from_date');
            const toDateInput = document.getElementById('to_date');
            const form = document.getElementById('indirectCostEditForm');

            // مراقبة تغيير التواريخ
            fromDateInput.addEventListener('change', fetchRestrictions);
            toDateInput.addEventListener('change', fetchRestrictions);

            // رسالة التأكيد عند التحديث
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                const total = parseFloat(totalInput.value) || 0;

                if (total <= 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'يجب أن يكون المجموع الكلي أكبر من صفر',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                Swal.fire({
                    title: 'تأكيد التحديث',
                    html: `
                        <div class="text-center">
                            <i class="feather icon-edit text-primary" style="font-size: 48px;"></i>
                            <h5 class="mt-3">هل أنت متأكد من تحديث التكاليف غير المباشرة؟</h5>
                            <div class="alert alert-info mt-3">
                                <strong>المجموع الكلي: ${total.toFixed(2)} ر.س</strong>
                            </div>
                        </div>
                    `,
                    icon: null,
                    showCancelButton: true,
                    confirmButtonColor: '#28a745',
                    cancelButtonColor: '#dc3545',
                    confirmButtonText: '<i class="fa fa-save mr-1"></i>نعم، حدث',
                    cancelButtonText: '<i class="fa fa-times mr-1"></i>إلغاء',
                    showLoaderOnConfirm: true,
                    allowOutsideClick: false,
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            // إظهار مؤشر التحميل
                            Swal.fire({
                                title: 'جاري التحديث...',
                                text: 'يرجى الانتظار',
                                allowOutsideClick: false,
                                allowEscapeKey: false,
                                showConfirmButton: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });

                            form.submit();
                            resolve();
                        });
                    }
                });
            });

            function updateRowCount(table, countElementId) {
                const rowCount = table.querySelectorAll('tr').length;
                document.getElementById(countElementId).textContent = rowCount;
            }

            function calculateExpensesTotal() {
                let total = 0;
                itemsTable.querySelectorAll('tr').forEach(row => {
                    const priceInput = row.querySelector('.expenses-total');
                    if (priceInput) {
                        const price = parseFloat(priceInput.value) || 0;
                        total += price;
                    }
                });
                document.querySelector('.expenses-grand-total').textContent = total.toFixed(2);
                updateTotalSum();
            }

            function calculateManufacturingTotal() {
                let total = 0;
                manufacturingTable.querySelectorAll('tr').forEach(row => {
                    const priceInput = row.querySelector('.manufacturing-price');
                    if (priceInput) {
                        const price = parseFloat(priceInput.value) || 0;
                        total += price;
                    }
                });
                document.querySelector('.manufacturing-grand-total').textContent = total.toFixed(2);
                updateTotalSum();
            }

            function updateTotalSum() {
                const expensesTotal = parseFloat(document.querySelector('.expenses-grand-total').textContent) || 0;
                const manufacturingTotal = parseFloat(document.querySelector('.manufacturing-grand-total').textContent) || 0;
                const totalSum = expensesTotal + manufacturingTotal;

                totalInput.value = totalSum.toFixed(2);
                displayTotal.textContent = totalSum.toLocaleString('ar-SA', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            }

            function attachExpensesRowEvents(row) {
                const priceInput = row.querySelector('.expenses-total');
                const restrictionSelect = row.querySelector('.restriction-select');

                if (priceInput) {
                    priceInput.addEventListener('input', calculateExpensesTotal);

                    // تأثير بصري عند التركيز
                    priceInput.addEventListener('focus', function() {
                        this.parentNode.classList.add('shadow-sm');
                    });

                    priceInput.addEventListener('blur', function() {
                        this.parentNode.classList.remove('shadow-sm');
                    });
                }

                if (restrictionSelect) {
                    restrictionSelect.addEventListener('change', function() {
                        handleRestrictionChange(this);
                    });
                }
            }

            function attachManufacturingRowEvents(row) {
                const priceInput = row.querySelector('.manufacturing-price');
                if (priceInput) {
                    priceInput.addEventListener('input', calculateManufacturingTotal);

                    // تأثير بصري عند التركيز
                    priceInput.addEventListener('focus', function() {
                        this.parentNode.classList.add('shadow-sm');
                    });

                    priceInput.addEventListener('blur', function() {
                        this.parentNode.classList.remove('shadow-sm');
                    });
                }
            }

            function createRestrictionOptions() {
                let optionsHtml = '<option value="" disabled selected>-- اختر القيد --</option>';
                availableRestrictions.forEach(restriction => {
                    optionsHtml += `
                        <option value="${restriction.id}"
                                data-reference="${restriction.reference_number}"
                                data-description="${restriction.description}"
                                data-date="${restriction.date}">
                            ${restriction.display_text}
                        </option>
                    `;
                });
                return optionsHtml;
            }

            ExpensesAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                if (availableRestrictions.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'تنبيه',
                        text: 'لا توجد قيود متاحة للفترة المحددة',
                        confirmButtonText: 'حسناً',
                        confirmButtonColor: '#ffc107'
                    });
                    return;
                }

                const exNewRow = document.createElement('tr');
                exNewRow.innerHTML = `
                    <td>
                        <select class="form-control restriction-select" name="restriction_id[]">
                            ${createRestrictionOptions()}
                        </select>
                        <div class="restriction-info mt-2" style="display: none;">
                            <small class="text-muted">
                                <strong>الوصف:</strong> <span class="restriction-description"></span><br>
                                <strong>التاريخ:</strong> <span class="restriction-date"></span>
                            </small>
                        </div>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.01" name="restriction_total[]" class="form-control expenses-total" value="0" min="0" placeholder="0.00">
                            <div class="input-group-append">
                                <span class="input-group-text">ر.س</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="حذف الصف">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                itemsTable.appendChild(exNewRow);
                attachExpensesRowEvents(exNewRow);
                updateRowCount(itemsTable, 'rowExpensesCount');

                // تأثير بصري للصف الجديد
                exNewRow.style.backgroundColor = '#e8f5e8';
                setTimeout(() => {
                    exNewRow.style.backgroundColor = '';
                }, 1000);
            });

            ManufacturingAddRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td>
                        <select name="manufacturing_order_id[]" class="form-control">
                            <option value="">-- اختر طلب التصنيع --</option>
                            @foreach ($manufacturing_orders as $manufacturing_order)
                                <option value="{{ $manufacturing_order->id }}">{{ $manufacturing_order->name }}</option>
                            @endforeach
                        </select>
                    </td>
                    <td>
                        <div class="input-group">
                            <input type="number" step="0.01" name="manufacturing_price[]" class="form-control manufacturing-price" value="0" min="0" placeholder="0.00">
                            <div class="input-group-append">
                                <span class="input-group-text">ر.س</span>
                            </div>
                        </div>
                    </td>
                    <td class="text-center">
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow" title="حذف الصف">
                            <i class="fa fa-trash"></i>
                        </button>
                    </td>
                `;

                manufacturingTable.appendChild(newRow);
                attachManufacturingRowEvents(newRow);
                updateRowCount(manufacturingTable, 'rowManufacturingCount');

                // تأثير بصري للصف الجديد
                newRow.style.backgroundColor = '#e8f5e8';
                setTimeout(() => {
                    newRow.style.backgroundColor = '';
                }, 1000);
            });

            // Event delegation for remove buttons مع إضافة رسالة تأكيد
            document.addEventListener('click', function (e) {
                if (e.target.classList.contains('removeRow') || e.target.closest('.removeRow')) {
                    const button = e.target.classList.contains('removeRow') ? e.target : e.target.closest('.removeRow');
                    const row = button.closest('tr');
                    const table = button.closest('table');
                    const tableBody = table.querySelector('tbody');

                    if (tableBody.rows.length > 1) {
                        // رسالة تأكيد الحذف
                        Swal.fire({
                            title: 'تأكيد الحذف',
                            text: 'هل أنت متأكد من حذف هذا الصف؟',
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: '<i class="fa fa-trash mr-1"></i>نعم، احذف',
                            cancelButtonText: '<i class="fa fa-times mr-1"></i>إلغاء'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // تأثير بصري للحذف
                                row.style.backgroundColor = '#f8d7da';
                                row.style.transition = 'all 0.3s ease';

                                setTimeout(() => {
                                    row.remove();

                                    if (table.id === 'itemsTable') {
                                        calculateExpensesTotal();
                                        updateRowCount(itemsTable, 'rowExpensesCount');
                                    } else if (table.id === 'manufacturingTable') {
                                        calculateManufacturingTotal();
                                        updateRowCount(manufacturingTable, 'rowManufacturingCount');
                                    }

                                    // رسالة نجاح الحذف
                                    Swal.fire({
                                        title: 'تم الحذف!',
                                        text: 'تم حذف الصف بنجاح',
                                        icon: 'success',
                                        timer: 1500,
                                        showConfirmButton: false
                                    });
                                }, 300);
                            }
                        });
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            title: 'تنبيه',
                            text: 'لا يمكنك حذف جميع الصفوف! يجب أن يبقى صف واحد على الأقل',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#ffc107'
                        });
                    }
                }
            });

            // Initialize existing rows
            itemsTable.querySelectorAll('tr').forEach(row => attachExpensesRowEvents(row));
            manufacturingTable.querySelectorAll('tr').forEach(row => attachManufacturingRowEvents(row));

            // تفعيل معالج القيود للصفوف الموجودة
            document.querySelectorAll('.restriction-select').forEach(select => {
                if (select.value) {
                    handleRestrictionChange(select);
                }
            });

            // Initial calculations
            calculateExpensesTotal();
            calculateManufacturingTotal();
            updateRowCount(itemsTable, 'rowExpensesCount');
            updateRowCount(manufacturingTable, 'rowManufacturingCount');

            // إضافة تأثيرات بصرية للجداول
            document.querySelectorAll('.table tbody tr').forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });

                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });

            // تحسين UX للحقول الرقمية
            document.querySelectorAll('input[type="number"]').forEach(input => {
                input.addEventListener('wheel', function(e) {
                    e.preventDefault(); // منع التمرير من تغيير القيم
                });
            });
        });
    </script>
@endsection