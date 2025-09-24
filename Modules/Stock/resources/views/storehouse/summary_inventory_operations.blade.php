@extends('master')

@section('title')
    ملخص عمليات المخزون
@stop
@section('css')

    <style>
        /* تأثير التدرج للأزرار */
        .btn-gradient-blue {
            background: linear-gradient(45deg, #007bff, #00c6ff);
            color: #fff;
            border: none;
            transition: 0.3s;
        }

        .btn-gradient-blue:hover {
            background: linear-gradient(45deg, #0056b3, #009edb);
        }

        .btn-gradient-primary {
            background: linear-gradient(45deg, #28a745, #00c853);
            color: #fff;
            border: none;
            transition: 0.3s;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(45deg, #1e7e34, #009624);
        }
    </style>
@endsection
@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ملخص عمليات المخزون</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $storehouse->name }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center p-2">
                <div class="d-flex gap-2">
                    <span class="hide-button-text">بحث وتصفية</span>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <!-- إضافة المستودع بجانب الأزرار -->
                    <div class="d-flex align-items-center">
                        <i class="fa fa-warehouse"></i>
                        <span class="ms-1">المستودع: {{ $storehouse->name }}</span>
                    </div>
                    <button class="btn btn-outline-secondary btn-sm" onclick="toggleSearchFields(this)">
                        <i class="fa fa-times"></i>
                        <span class="hide-button-text">إخفاء</span>
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#advancedSearchForm" onclick="toggleSearchText(this)">
                        <i class="fa fa-filter"></i>
                        <span class="button-text">متقدم</span>
                    </button>
                </div>
            </div>
            <div class="card-body">
                <form class="form" id="searchForm" method="GET" action="">
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label for="custom_period">التخصيص</label>
                            <select name="custom_period" class="form-control" id="custom_period">
                                <option value="">التخصيص</option>
                                <option value="monthly">شهريًا</option>
                                <option value="weekly">أسبوعيًا</option>
                                <option value="daily">يوميًا</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label for="from_date">التاريخ من</label>
                            <input type="date" id="from_date" class="form-control" name="from_date">
                        </div>
                        <div class="col-md-2">
                            <label for="to_date">التاريخ إلى</label>
                            <input type="date" id="to_date" class="form-control" name="to_date">
                        </div>
                        <div class="col-md-3">
                            <label for="client_id">النوع</label>
                            <select name="client_id" class="form-control select2" id="client_id">
                                <option value="">أي نوع</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="storehouse">المستودع</label>
                            <select name="storehouse" class="form-control select2" id="storehouse">
                                <option value="">أي مستودع</option>
                                @if(!empty($storehouse) && is_iterable($storehouse))
                                    @foreach ($storehouse as $store)
                                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="product">المنتج</label>
                            <select name="product" class="form-control" id="product">
                                <option value="">اختر المنتج</option>
                                @if(!empty($products) && is_iterable($products))
                                    @foreach ($products as $product)
                                        @if(is_object($product))
                                            <option value="{{ $product->id }}">{{ $product->name }}</option>
                                        @endif
                                    @endforeach
                                @endif
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="category">التصنيف</label>
                            <select name="category" class="form-control" id="category">
                                <option value="">اختر التصنيف</option>
                                @if(!empty($categories) && is_iterable($categories))
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>

                    </div>
                    <div class="form-actions mt-2">
                        <button type="submit" class="btn btn-primary">بحث</button>
                        <a href="" class="btn btn-outline-warning">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-end align-items-center gap-2">
                    <a href="#" class="btn btn-gradient-blue mb-1">
                        <i class="fa fa-print"></i> طباعة
                    </a>

                    <div class="btn-group">
                        <button type="button" class="btn btn-gradient-primary dropdown-toggle" data-bs-toggle="dropdown"
                            aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-download"></i> تصدير
                        </button>

                        <div class="dropdown-menu">
                            <a class="dropdown-item" href="#">PDF</a>
                            <a class="dropdown-item" href="#">Excel</a>
                            <a class="dropdown-item" href="#">CSV</a>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th rowspan="2" class="border border-info">اسم المنتج</th>
                                <th colspan="5" class="border border-info">الوارد</th>
                                <th colspan="5" class="border border-info">المنصرف</th>
                                <th rowspan="2" class="border border-info">إجمالي الحركة</th>
                            </tr>
                            <tr class="table-info">
                                <th>فواتير الشراء</th>
                                <th>مرتجع المبيعات</th> <!-- تم تغيير هذا العمود -->
                                <th>التحويل</th>
                                <th>يدوي</th>
                                <th>الإجمالي</th>
                                <th>فواتير البيع</th>
                                <th>مرتجع مشتريات</th>
                                <th>التحويل</th>
                                <th>يدوي</th>
                                <th>الإجمالي</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>
                                        <strong>
                                            <a href="{{ route('products.show', $product['id']) }}">{{ $product['name'] }}</a>
                                        </strong>
                                    </td>
                                    <td>0</td>
                                    <td>{{ $product['sales_return_quantity'] }}</td> <!-- عرض مرتجع المبيعات -->
                                    <td>{{ $product['incoming_transfer'] }}</td>
                                    <td>{{ $product['incoming_manual'] }}</td>
                                    <td>{{ $product['incoming_total'] }}</td>

                                    <td>{{ $product['sold_quantity'] }}</td> <!-- عرض عدد فواتير البيع -->

                                    <td>0</td>
                                    <td>{{ $product['outgoing_transfer'] != 0 ? '-' . $product['outgoing_transfer'] : 0 }}</td>
                                    <td>{{ $product['outgoing_manual'] != 0 ? '-' . $product['outgoing_manual'] : 0 }}</td>
                                    <td>{{ $product['outgoing_total'] != 0 ? '-' . $product['outgoing_total'] : 0 }}</td>

                                    <td class="active">
                                        <a href="">
                                            <u>
                                                <strong>{{ $product['movement_total'] - $product['sold_quantity'] }}</strong>
                                            </u>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
<script src="{{ asset('assets/js/search.js') }}"></script>
@endsection
