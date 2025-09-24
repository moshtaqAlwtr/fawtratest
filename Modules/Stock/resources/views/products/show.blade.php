@extends('master')

@section('title')
المنتجات
@stop

@section('css')
<style>
    .user-avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        background-color: #007bff;
        color: #fff;
        font-weight: bold;
        font-size: 14px;
    }

    .loading {
        text-align: center;
        padding: 20px;
    }

    .spinner-border {
        width: 2rem;
        height: 2rem;
    }

    .tab-loading {
        display: none;
        text-align: center;
        padding: 40px;
    }

    /* تحسينات للشاشات الصغيرة */
    @media (max-width: 768px) {
        .content-header-title {
            font-size: 1.5rem !important;
            text-align: center;
            margin-bottom: 1rem;
        }

        .breadcrumb-wrapper {
            text-align: center;
        }

        .d-flex.justify-content-between {
            flex-direction: column;
            gap: 15px;
        }

        .d-flex.justify-content-between > div {
            text-align: center;
        }

        .btn-group-mobile {
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            justify-content: center;
        }

        .btn-group-mobile .btn {
            flex: 1;
            min-width: 120px;
            font-size: 12px;
            padding: 8px 10px;
        }

        .card-title .btn {
            margin: 2px;
            font-size: 11px;
            padding: 6px 8px;
        }

        /* جعل الجدول قابل للتمرير أفقياً */
        .table-responsive {
            border: none;
        }

        .table {
            margin-bottom: 0;
            font-size: 13px;
        }

        .table th, .table td {
            padding: 8px;
            vertical-align: middle;
        }

        /* تحسين عرض الإحصائيات */
        .stats-card {
            margin-bottom: 15px;
        }

        .stats-value {
            font-size: 1.2rem !important;
        }

        .stats-label {
            font-size: 11px;
        }

        /* تحسين التبويبات للموبايل */
        .nav-tabs {
            flex-wrap: wrap;
        }

        .nav-tabs .nav-item {
            margin-bottom: 5px;
        }

        .nav-tabs .nav-link {
            padding: 8px 12px;
            font-size: 13px;
            border-radius: 20px !important;
            margin: 2px;
        }

        /* تحسين عرض الصور */
        .product-image {
            max-width: 100%;
            height: auto;
        }

        /* تحسين النموذج المنبثق للموبايل */
        .modal-dialog {
            margin: 10px;
        }

        .modal-body {
            padding: 15px;
        }

        .modal-footer {
            padding: 10px 15px;
        }

        .modal-footer .btn {
            margin: 2px;
        }
    }

    /* تحسينات للتابلت */
    @media (min-width: 769px) and (max-width: 1024px) {
        .container {
            max-width: 95% !important;
        }

        .table {
            font-size: 14px;
        }

        .btn {
            font-size: 13px;
        }

        .card-title .btn {
            margin: 3px;
        }
    }

    /* تحسينات للشاشات الكبيرة */
    @media (min-width: 1200px) {
        .container {
            max-width: 1200px;
        }
    }

    /* تحسين عرض البيانات في الجدول */
    .table-stats {
        background: #f8f9fa;
        border-radius: 8px;
        overflow: hidden;
    }

    .table-stats th {
        background: #e9ecef;
        font-weight: 600;
        text-align: center;
        border: none;
        padding: 15px 10px;
    }

    .table-stats td {
        text-align: center;
        border: none;
        padding: 20px 10px;
    }

    /* تحسين عرض الأزرار */
    .btn-responsive {
        margin: 2px;
    }

    @media (max-width: 576px) {
        .btn-responsive {
            width: 100%;
            margin-bottom: 5px;
        }
    }

    /* تحسين عرض المحتوى في التبويبات */
    .tab-content {
        margin-top: 20px;
    }

    .tab-pane {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* تحسين عرض معلومات المخازن */
    .store-info {
        background: #f8f9fa;
        border-radius: 5px;
        padding: 8px 12px;
        margin: 5px 0;
        border-left: 3px solid #007bff;
    }

    /* تحسين عرض الشارات */
    .badge-responsive {
        font-size: 11px;
        padding: 5px 8px;
    }

    @media (max-width: 768px) {
        .badge-responsive {
            font-size: 10px;
            padding: 3px 6px;
        }
    }

    /* تحسين عرض تفاصيل المنتج */
    .product-details {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        align-items: flex-start;
    }

    .product-image-container {
        flex: 0 0 auto;
    }

    .product-info {
        flex: 1;
        min-width: 200px;
    }

    @media (max-width: 768px) {
        .product-details {
            flex-direction: column;
            text-align: center;
        }

        .product-image-container {
            align-self: center;
        }
    }
</style>
@endsection

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة المنتجات</h2>
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

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div class="mb-2 mb-md-0">
                        <strong>{{ $product->name }} </strong> |
                        <small>{{ $product->serial_number }}#</small> |
                        <span class="badge badge-pill badge-success badge-responsive">في المخزن</span>
                    </div>

                    <div>
                        <a href="{{ route('products.edit',$product->id) }}" class="btn btn-outline-primary btn-responsive">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="container-fluid" style="max-width: 1200px">
            <div class="card">
                <div class="card-title p-2">
                    <div class="btn-group-mobile">
                        <a href="{{ route('products.edit',$product->id) }}" class="btn btn-outline-primary btn-sm">
                            تعديل <i class="fa fa-edit"></i>
                        </a>
                        <a href="#" class="btn btn-outline-danger btn-sm" data-toggle="modal" data-target="#modal_DELETE{{ $product->id }}">
                            حذف <i class="fa fa-trash"></i>
                        </a>
                        @if($product->type == "products" || $product->type == "compiled")
                        <a href="{{ route('store_permits_management.manual_conversion') }}" class="btn btn-outline-success btn-sm">
                            نقل <i class="fa fa-reply-all"></i>
                        </a>
{{--
                        <a href="{{ route('store_permits_management.create') }}" class="btn btn-outline-info btn-sm">
                            اضف عمليه <i class="fa fa-plus"></i>
                        </a>
                        <a href="{{ route('store_permits_management.manual_disbursement') }}" class="btn btn-outline-warning btn-sm">
                            عمليه صرف <i class="fa fa-minus"></i>
                        </a> --}}
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <ul class="nav nav-tabs justify-content-center" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" aria-controls="home" role="tab" aria-selected="true">معلومات</a>
                        </li>
                        @if($product->type == "products" || $product->type == "compiled")
                        <li class="nav-item">
                            <a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" aria-controls="profile" role="tab" aria-selected="false" onclick="loadStockMovements(1)">حركة المخزون</a>
                        </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" id="about-tab" data-toggle="tab" href="#about" aria-controls="about" role="tab" aria-selected="false" onclick="loadTimeline(1)">الجدول الزمني</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="activate-tab" data-toggle="tab" href="#activate" aria-controls="activate" role="tab" aria-selected="false" onclick="loadActivityLogs(1)">سجل النشاطات</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        {{-- تبويب المعلومات الأساسية --}}
                        <div class="tab-pane active" id="home" aria-labelledby="home-tab" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-stats">
                                            <thead class="table-light">
                                                <tr>
                                                    @if($product->type == "products" || $product->type == "compiled")
                                                    <th class="text-center">
                                                        <i class="feather icon-package text-info font-medium-5 mr-1"></i>
                                                        <br><span class="stats-label">اجمالي المخزون</span>
                                                    </th>
                                                    @endif
                                                    <th class="text-center">
                                                        <i class="feather icon-shopping-cart text-warning font-medium-5 mr-1"></i>
                                                        <br><span class="stats-label">اجمالي القطع المباعه</span>
                                                    </th>
                                                    <th class="text-center">
                                                        <i class="feather icon-calendar text-danger font-medium-5 mr-1"></i>
                                                        <br><span class="stats-label">آخر 28 أيام</span>
                                                    </th>
                                                    <th class="text-center">
                                                        <i class="feather icon-calendar text-primary font-medium-5 mr-1"></i>
                                                        <br><span class="stats-label">آخر 7 أيام</span>
                                                    </th>
                                                    <th class="text-center">
                                                        <i class="feather icon-bar-chart-2 text-success font-medium-5 mr-1"></i>
                                                        <br><span class="stats-label">متوسط سعر التكلفة</span>
                                                    </th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    @if($product->type == "products" || $product->type == "compiled")
                                                    <td class="text-center">
                                                        <h4 class="text-bold-700 stats-value">
                                                            {{ $total_quantity ? number_format($total_quantity) : 'غير متوفر' }}
                                                            {{$firstTemplateUnit ?? ""}}
                                                        </h4>

                                                        @if ($storeQuantities->isNotEmpty())
                                                            @foreach ($storeQuantities as $storeQuantity)
                                                                @if (!empty($storeQuantity->storeHouse))
                                                                    <div class="store-info">
                                                                        <span>{{ $storeQuantity->storeHouse->name }} :</span>
                                                                        <strong>{{ number_format($storeQuantity->total_quantity) }}</strong>
                                                                    </div>
                                                                @endif
                                                            @endforeach
                                                        @endif

                                                        <div class="mt-2">
                                                            <a href="{{ route('products.manual_stock_adjust',$product->id) }}" class="btn btn-outline-info btn-sm">
                                                                اضف عميله على المخزون
                                                            </a>
                                                        </div>
                                                    </td>
                                                    @endif
                                                    <td class="text-center">
                                                        <h4 class="text-bold-700 stats-value">{{ $total_sold ? number_format($total_sold) : 0 }}<small>قطع</small></h4>
                                                    </td>
                                                    <td class="text-center">
                                                        <h4 class="text-bold-700 stats-value">{{ $sold_last_28_days ? number_format($sold_last_28_days) : 0 }}<small>قطع</small></h4>
                                                    </td>
                                                    <td class="text-center">
                                                        <h4 class="text-bold-700 stats-value">{{ $sold_last_7_days ? number_format($sold_last_7_days) : 0 }}<small>قطع</small></h4>
                                                    </td>
                                                    <td class="text-center">
                                                        <h4 class="text-bold-700 stats-value">{{ $average_cost ? number_format($average_cost, 2) . ' ر.س' : 'غير متوفر' }}</h4>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <div class="card">
                                <div class="card-header">
                                    <strong>التفاصيل :</strong>
                                </div>
                                <div class="card-body">
                                    <div class="product-details">
                                        <div class="product-image-container">
                                            @if ($product->images)
                                                <img src="{{ asset('assets/uploads/product/'.$product->images) }}" alt="img" class="product-image" width="150">
                                            @else
                                                <img src="{{ asset('assets/uploads/no_image.jpg') }}" alt="img" class="product-image" width="150">
                                            @endif
                                        </div>
                                        <div class="product-info">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <strong>كود المنتج </strong>: {{ $product->serial_number }}#
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <strong>نوع التتبع</strong><br>
                                                    <small>
                                                        @if ($product->inventory_type == 0)
                                                        الرقم التسلسلي
                                                        @elseif ($product->inventory_type == 1)
                                                        رقم الشحنة
                                                        @elseif ($product->inventory_type == 2)
                                                        تاريخ الانتهاء
                                                        @elseif ($product->inventory_type == 3)
                                                        رقم الشحنة وتاريخ الانتهاء
                                                        @else
                                                        الكمية فقط
                                                        @endif
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            @if($product->type == "compiled")
                            <div class="card">
                                <div class="card-header">
                                    <strong>منتجات التجميعة :</strong>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        @foreach ($CompiledProducts as $CompiledProduct)
                                        <div class="col-md-6 col-lg-4 mb-2">
                                            <div class="store-info">
                                                <b>{{$CompiledProduct->Product->name ?? ""}}</b> : {{$CompiledProduct->qyt ?? ""}}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        {{-- تبويب حركة المخزون --}}
                        <div class="tab-pane" id="profile" aria-labelledby="profile-tab" role="tabpanel">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="tab-loading" id="stock-loading">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">جاري التحميل...</span>
                                            </div>
                                            <p class="mt-2">جاري تحميل حركة المخزون...</p>
                                        </div>
                                        <div id="stock-movements-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- تبويب الجدول الزمني --}}
                        <div class="tab-pane" id="about" aria-labelledby="about-tab" role="tabpanel">
                            <div class="card">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="tab-loading" id="timeline-loading">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="sr-only">جاري التحميل...</span>
                                            </div>
                                            <p class="mt-2">جاري تحميل الجدول الزمني...</p>
                                        </div>
                                        <div id="timeline-content"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- تبويب سجل النشاطات --}}
                        <div class="tab-pane" id="activate" aria-labelledby="activate-tab" role="tabpanel">
                            <div class="tab-loading" id="activity-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">جاري التحميل...</span>
                                </div>
                                <p class="mt-2">جاري تحميل سجل النشاطات...</p>
                            </div>
                            <div id="activity-logs-content"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal delete -->
        <div class="modal fade text-left" id="modal_DELETE{{ $product->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #EA5455 !important;">
                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $product->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                        <a href="{{ route('products.delete',$product->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                    </div>
                </div>
            </div>
        </div>
        <!--end delete-->
    </div>

@endsection

@section('scripts')


<script>
    // متغير لتخزين معرف المنتج
    const productId = {{ $product->id }};

    // دالة تحميل حركة المخزون
    function loadStockMovements(page = 1) {
        $('#stock-loading').show();
        $('#stock-movements-content').html('');

        $.ajax({
            url: `{{ route('products.stock_movements', '') }}/${productId}`,
            type: 'GET',
            data: { page: page },
            success: function(response) {
                $('#stock-loading').hide();
                $('#stock-movements-content').html(response.html);
            },
            error: function(xhr, status, error) {
                $('#stock-loading').hide();
                $('#stock-movements-content').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        <p class="mb-0">حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.</p>
                    </div>
                `);
                console.error('Error loading stock movements:', error);
            }
        });
    }

    // دالة تحميل الجدول الزمني
    function loadTimeline(page = 1) {
        $('#timeline-loading').show();
        $('#timeline-content').html('');

        $.ajax({
            url: `{{ route('products.timeline', '') }}/${productId}`,
            type: 'GET',
            data: { page: page },
            success: function(response) {
                $('#timeline-loading').hide();
                $('#timeline-content').html(response.html);
            },
            error: function(xhr, status, error) {
                $('#timeline-loading').hide();
                $('#timeline-content').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        <p class="mb-0">حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.</p>
                    </div>
                `);
                console.error('Error loading timeline:', error);
            }
        });
    }

    // دالة تحميل سجل النشاطات
    function loadActivityLogs(page = 1) {
        $('#activity-loading').show();
        $('#activity-logs-content').html('');

        $.ajax({
            url: `{{ route('products.activity_logs', '') }}/${productId}`,
            type: 'GET',
            data: { page: page },
            success: function(response) {
                $('#activity-loading').hide();
                $('#activity-logs-content').html(response.html);
            },
            error: function(xhr, status, error) {
                $('#activity-loading').hide();
                $('#activity-logs-content').html(`
                    <div class="alert alert-danger text-center" role="alert">
                        <p class="mb-0">حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.</p>
                    </div>
                `);
                console.error('Error loading activity logs:', error);
            }
        });
    }

    // دوال التعامل مع التبويبات
    $(document).ready(function() {
        // عند النقر على تبويب حركة المخزون
        $('#profile-tab').on('shown.bs.tab', function (e) {
            if ($('#stock-movements-content').is(':empty')) {
                loadStockMovements(1);
            }
        });

        // عند النقر على تبويب الجدول الزمني
        $('#about-tab').on('shown.bs.tab', function (e) {
            if ($('#timeline-content').is(':empty')) {
                loadTimeline(1);
            }
        });

        // عند النقر على تبويب سجل النشاطات
        $('#activate-tab').on('shown.bs.tab', function (e) {
            if ($('#activity-logs-content').is(':empty')) {
                loadActivityLogs(1);
            }
        });

        // تحسين عرض الجداول في الشاشات الصغيرة
        function adjustTableDisplay() {
            if ($(window).width() < 768) {
                $('.table-responsive').addClass('table-responsive-sm');
            } else {
                $('.table-responsive').removeClass('table-responsive-sm');
            }
        }

        // تشغيل التحسين عند تحميل الصفحة وتغيير حجم النافذة
        adjustTableDisplay();
        $(window).resize(adjustTableDisplay);
    });

    // دوال إضافية للتعامل مع النماذج
    function remove_disabled() {
        if (document.getElementById("ProductTrackStock").checked) {
            disableForm(false);
        }
        if (!document.getElementById("ProductTrackStock").checked) {
            disableForm(true);
        }
    }

    function disableForm(flag) {
        var elements = document.getElementsByClassName("ProductTrackingInput");
        for (var i = 0, len = elements.length; i < len; ++i) {
            elements[i].readOnly = flag;
            elements[i].disabled = flag;
        }
    }

    function remove_disabled_ckeckbox() {
        if(document.getElementById("available_online").checked)
            document.getElementById("featured_product").disabled = false;
        else
            document.getElementById("featured_product").disabled = true;
    }
</script>
@endsection
