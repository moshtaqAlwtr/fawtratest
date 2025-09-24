@extends('master')

@section('title')
قيمة المخزون التقديرية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قيمة المخزون التقديرية</h2>
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
                <div class="d-flex justify-content-between">

                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th>الكود</th>
                                <th>الاسم</th>
                                <th>الكمية</th>
                                <th>سعر البيع الحالي</th>
                                <th>متوسط سعر الشراء</th>
                                <th>إجمالي سعر البيع المتوقع</th>
                                <th>إجمالي سعر الشراء</th>
                                <th>الربح المتوقع</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                @php
                                    $totalPurchasePrice = $product->product->purchase_price * $product->quantity;
                                    $totalSalePrice = $product->product->sale_price * $product->quantity;
                                    $expectedProfit = $totalSalePrice - $totalPurchasePrice;
                                @endphp
                                <tr>
                                    <td>{{ $product->product->barcode }}</td>
                                    <td>{{ $product->product->name }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td>{{ number_format($product->product->sale_price, 2) }}</td>
                                    <td>{{ number_format($product->product->purchase_price, 2) }}</td>
                                    <td>{{ number_format($totalSalePrice, 2) }}</td>
                                    <td>{{ number_format($totalPurchasePrice, 2) }}</td>
                                    <td>{{ number_format($expectedProfit, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="table-info">
                                <td colspan="5"><strong>الإجمالي</strong></td>
                                <td><strong>{{ number_format($products->sum(fn($p) => $p->product->sale_price * $p->quantity), 2) }}</strong></td>
                                <td><strong>{{ number_format($products->sum(fn($p) => $p->product->purchase_price * $p->quantity), 2) }}</strong></td>
                                <td><strong>{{ number_format($products->sum(fn($p) => ($p->product->sale_price * $p->quantity) - ($p->product->purchase_price * $p->quantity)), 2) }}</strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
@endsection
a
