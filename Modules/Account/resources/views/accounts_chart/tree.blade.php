{{-- <!DOCTYPE html>
<html class="loading" lang="ar" data-textdirection="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دليل الحسابات</title>

    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/fonts/Cairo/stylesheet.css') }}">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors-rtl.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/semi-dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/pages/app-chat.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/custom-rtl.css">
    <link rel="stylesheet" type="text/css" href="../../../assets/css/style-rtl.css">
    <!-- END: Theme CSS-->

    <!-- JSTree & SweetAlert -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/themes/default/style.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        body,
        h1, h2, h3, h4, h5, h6,
        .navigation,
        .header-navbar,
        .breadcrumb {
            font-family: 'Cairo', sans-serif;
        }

        body {
            direction: rtl;
            background: linear-gradient(135deg, #e3e4e7 0%, rgb(255, 255, 255) 100%);
            min-height: 100vh;
        }

        /* تحسين الشجرة */
        #tree-container {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 20px;
            margin-bottom: 20px;
            max-height: 600px;
            overflow-y: auto;
            border: 2px solid #e3f2fd;
        }

        #tree {
            font-family: 'Cairo', sans-serif;
        }

        /* تخصيص عقد الشجرة */
        .jstree-default .jstree-node {
            margin: 8px 0;
            transition: all 0.3s ease;
        }

        .jstree-default .jstree-anchor {
            border-radius: 10px;
            padding: 12px 15px;
            margin: 2px 0;
            font-size: 14px;
            font-weight: 500;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border: 1px solid #dee2e6;
            color: #495057;
            text-decoration: none;
            display: flex;
            align-items: center;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }

        .jstree-default .jstree-anchor::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: linear-gradient(135deg, #ebecf1 0%, #ffffff 100%);
            transition: width 0.3s ease;
            z-index: 0;
        }

        .jstree-default .jstree-anchor:hover::before {
            width: 100%;
        }

        .jstree-default .jstree-anchor:hover {
            background: transparent;
            color: black;
            transform: translateX(-5px);
            box-shadow: 0 8px 25px rgba(255, 255, 255, 0.3);
            border-color: #ffffff;
        }

        .jstree-default .jstree-anchor > * {
            position: relative;
            z-index: 1;
        }

        .jstree-default .jstree-clicked {
            background: linear-gradient(135deg, #f5f5f5 0%, #ffffff 100%) !important;
            color: white !important;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* أيقونات الشجرة المحسنة */
        .jstree-default .jstree-icon {
            margin-left: 8px;
            font-size: 16px;
        }

        .jstree-default .jstree-icon.jstree-themeicon-custom {
            background-image: none;
        }

        .jstree-default .jstree-icon.jstree-themeicon-custom::before {
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            color: #667eea;
            font-size: 16px;
        }

        /* أيقونات مختلفة للحسابات الرئيسية والفرعية */
        .jstree-default .jstree-node[data-type="main"] > .jstree-anchor .jstree-icon::before {
            content: '\f07b'; /* folder */
            color: #28a745;
        }

        .jstree-default .jstree-node[data-type="sub"] > .jstree-anchor .jstree-icon::before {
            content: '\f15b'; /* file */
            color: #17a2b8;
        }

        .jstree-default .jstree-node[data-type="assets"] > .jstree-anchor .jstree-icon::before {
            content: '\f0d6'; /* money */
            color: #28a745;
        }

        .jstree-default .jstree-node[data-type="liabilities"] > .jstree-anchor .jstree-icon::before {
            content: '\f3d1'; /* battery-half */
            color: #dc3545;
        }

        .jstree-default .jstree-node[data-type="equity"] > .jstree-anchor .jstree-icon::before {
            content: '\f201'; /* chart-line */
            color: #6f42c1;
        }

        .jstree-default .jstree-node[data-type="revenue"] > .jstree-anchor .jstree-icon::before {
            content: '\f0d6'; /* money */
            color: #20c997;
        }

        .jstree-default .jstree-node[data-type="expense"] > .jstree-anchor .jstree-icon::before {
            content: '\f555'; /* hand-holding-usd */
            color: #fd7e14;
        }

        /* خطوط الاتصال */
        .jstree-default .jstree-container-ul .jstree-children {
            border-right: 2px dotted #dee2e6;
            margin-right: 20px;
        }

        .jstree-default .jstree-children .jstree-node {
            position: relative;
        }

        .jstree-default .jstree-children .jstree-node::before {
            content: '';
            position: absolute;
            right: -22px;
            top: 20px;
            width: 20px;
            height: 2px;
            background: #dee2e6;
        }

        /* تحسين البحث */
        .search-container {
            background: #ffffff;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        #searchInput {
            border-radius: 25px;
            border: 2px solid #e3f2fd;
            padding: 15px 50px 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        #searchInput:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            outline: none;
        }

        #branchFilter {
            border-radius: 25px;
            border: 2px solid #e3f2fd;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        #branchFilter:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: white;
            outline: none;
        }

        /* نتائج البحث المحسنة */
        #search-results-dropdown {
            position: absolute;
            width: 100%;
            max-height: 400px;
            overflow-y: auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            top: 100%;
            margin-top: 10px;
            border: 2px solid #e3f2fd;
        }

        .search-result-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f8f9fa;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .search-result-item:hover {
            background: linear-gradient(135deg, #ffffff 0%, #fbf7ff 100%);
            color: white;
        }

        .search-result-item:last-child {
            border-bottom: none;
            border-radius: 0 0 15px 15px;
        }

        .search-result-item:first-child {
            border-radius: 15px 15px 0 0;
        }

        /* تحسين الجدول */
        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .table {
            margin-bottom: 0;
        }

        .table th {
            background: linear-gradient(135deg, #f0f0f0 0%, #fcf9ff 100%);
            color: black;
            font-weight: 600;
            padding: 15px;
            border: none;
        }

        .table td {
            padding: 15px;
            border-bottom: 1px solid #f8f9fa;
            vertical-align: middle;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.1);
            cursor: pointer;
        }

        /* الأزرار المحسنة */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .btn-add {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 25px;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
            color: white;
        }

        /* تحسين الكاردات */
        .card {
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            border: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
        }

        .card-header {
            background: transparent;
            border-bottom: 2px solid #e3f2fd;
            padding: 20px;
        }

        .card-body {
            padding: 20px;
        }

        /* تحسين المودال */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px;
        }

        .modal-body {
            padding: 30px;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e3f2fd;
            padding: 12px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* مؤشر التحميل */
        .loading-spinner {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .spinner-border {
            width: 3rem;
            height: 3rem;
            border-width: 4px;
        }

        /* تحسين الشريط الجانبي */
        .sidebar-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            margin: 10px;
        }

        /* تحسين العمليات */
        .operations-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .btn-operation {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: all 0.3s ease;
        }

        .btn-edit {
            background: #17a2b8;
            color: white;
        }

        .btn-edit:hover {
            background: #138496;
            transform: scale(1.1);
        }

        .btn-delete {
            background: #dc3545;
            color: white;
        }

        .btn-delete:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        /* تحسين الرصيد */
        .balance-info {
            text-align: center;
        }

        .balance-amount {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }

        .balance-type {
            font-size: 12px;
            color: #6c757d;
            text-transform: uppercase;
        }

        .balance-debit {
            color: #dc3545;
        }

        .balance-credit {
            color: #28a745;
        }

        /* ضمان بقاء الشجرة ظاهرة */
        .sidebar-left {
            position: sticky;
            top: 0;
            height: fit-content;
            max-height: 100vh;
            overflow-y: auto;
            flex: 0 0 350px; /* عرض ثابت للشجرة */
        }

        .content-right {
            flex: 1; /* يأخذ باقي المساحة */
        }

        .content-area-wrapper {
            display: flex;
            gap: 20px;
            align-items: flex-start;
        }

        /* تحسين responsive */
        @media (max-width: 768px) {
            .content-area-wrapper {
                flex-direction: column;
            }

            .sidebar-left {
                width: 100%;
                margin-bottom: 20px;
                position: relative;
            }

            .content-right {
                width: 100%;
            }

            #tree-container {
                max-height: 400px;
            }
        }

        /* تحسين الرادio buttons */
        .custom-radio {
            margin: 10px 0;
        }

        .custom-control-label {
            font-weight: 500;
            color: #495057;
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-color: #667eea;
        }

        /* تأثيرات إضافية */
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* تحسين scroll bar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern content-left-sidebar chat-application navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar">

    <!-- BEGIN: Header-->
    @include('layouts.header')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.sidebar')
    <!-- END: Main Menu-->

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>{{ session('error') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper">

            <!-- Sidebar للشجرة -->
            <div class="sidebar-left">
                <div class="sidebar">
                    <div class="sidebar-content card fade-in">
                        <span class="sidebar-close-icon">
                            <i class="feather icon-x"></i>
                        </span>

                        <!-- منطقة البحث المحسنة -->
                        <div class="search-container">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group position-relative">
                                        <input type="text" id="searchInput" class="form-control" placeholder="🔍 ابحث عن حساب...">
                                        <div class="form-control-position" style="left: 15px; top: 50%; transform: translateY(-50%);">
                                            <i class="feather icon-search text-muted"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <select id="branchFilter" class="form-control">
                                        <option value="all">🏢 كل الفروع</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <!-- مؤشر التحميل -->
                            <div id="loading-spinner" class="loading-spinner" style="display: none;">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="sr-only">جار التحميل...</span>
                                </div>
                            </div>

                            <!-- نتائج البحث -->
                            <div class="position-relative">
                                <div id="search-results-dropdown" class="search-results-dropdown" style="display: none;"></div>
                            </div>
                        </div>

                        <!-- منطقة الشجرة المحسنة -->
                        <div id="tree-container" class="fade-in">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <h5 class="mb-0">
                                    <i class="fas fa-sitemap text-primary"></i>
                                    دليل الحسابات
                                </h5>
                                <button class="btn btn-sm btn-gradient" onclick="$('#tree').jstree('open_all')">
                                    <i class="fas fa-expand-arrows-alt"></i> توسيع الكل
                                </button>
                            </div>
                            <div id="tree"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- المحتوى الرئيسي -->
            <div class="content-right">
                <div class="content-wrapper">
                    <div class="content-body">
                        <div class="chat-overlay"></div>
                        <section class="chat-app-window">

                            <div class="card fade-in" style="min-height: 600px;">
                                <div class="card-header">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div>
                                            <h4 class="mb-0">
                                                <i class="fas fa-chart-bar text-primary"></i>
                                                فرع القيود
                                            </h4>
                                            <small class="text-muted">إدارة الحسابات والقيود المالية</small>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <button id="backToMainAccounts" class="btn btn-secondary mr-2" style="display: none;">
                                                <i class="fas fa-arrow-right"></i>
                                                العودة للحسابات الرئيسية
                                            </button>
                                            <select class="form-control mr-3" style="width: auto;">
                                                <option value="all">كل الفروع</option>
                                                @foreach ($branches as $branch)
                                                    <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                                @endforeach
                                            </select>
                                            <button id="addAccountModalButton" class="btn btn-add" data-toggle="modal" data-target="#info-modal-account">
                                                <i class="fas fa-plus"></i>
                                                إضافة حساب جديد
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-body">
                                    <!-- الجدول الرئيسي -->
                                    <div class="table-container">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th width="50">النوع</th>
                                                    <th>اسم الحساب</th>
                                                    <th width="120">الرصيد</th>
                                                    <th width="100">العمليات</th>
                                                </tr>
                                            </thead>
                                            <tbody id="table-body">
                                                <!-- البيانات ستحمل هنا بواسطة JavaScript -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <!-- مودال إضافة حساب -->
    <div class="modal fade" id="info-modal-account" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i>
                        إضافة حساب جديد
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="addAccountForm" action="/accounts/store_account" method="POST">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-tag"></i>
                                        نوع الحساب
                                    </label>
                                    <select name="type" class="form-control">
                                        <option value="sub">حساب فرعي</option>
                                        <option value="main">حساب رئيسي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-hashtag"></i>
                                        الكود
                                    </label>
                                    <input type="number" id="accountCode" class="form-control" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-signature"></i>
                                        اسم الحساب
                                    </label>
                                    <input type="text" id="accountName" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-sitemap"></i>
                                        الحساب الرئيسي
                                    </label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">لا يوجد حساب رئيسي</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }} - {{ $account->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-3">
                                        <i class="fas fa-balance-scale"></i>
                                        نوع الرصيد
                                    </label>
                                    <div class="d-flex justify-content-around">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="balance_type" id="customRadio1" value="credit">
                                            <label class="custom-control-label" for="customRadio1">
                                                <i class="fas fa-arrow-down text-success"></i>
                                                دائن
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="balance_type" id="customRadio2" value="debit">
                                            <label class="custom-control-label" for="customRadio2">
                                                <i class="fas fa-arrow-up text-danger"></i>
                                                مدين
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-gradient" form="addAccountForm">
                        <i class="fas fa-save"></i>
                        حفظ الحساب
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- مودال تعديل الحساب -->
    <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i>
                        تعديل الحساب
                    </h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editAccountForm">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-tag"></i>
                                        نوع الحساب
                                    </label>
                                    <select name="type" class="form-control">
                                        <option value="sub">حساب فرعي</option>
                                        <option value="main">حساب رئيسي</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-hashtag"></i>
                                        الكود
                                    </label>
                                    <input type="number" id="accountCode" class="form-control" name="code">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-signature"></i>
                                        اسم الحساب
                                    </label>
                                    <input type="text" id="accountName" class="form-control" name="name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-sitemap"></i>
                                        الحساب الرئيسي
                                    </label>
                                    <select name="parent_id" class="form-control">
                                        <option value="">لا يوجد حساب رئيسي</option>
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}">{{ $account->name }} - {{ $account->code }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>
                                        <i class="fas fa-money-bill"></i>
                                        الرصيد
                                    </label>
                                    <input type="number" name="balance" id="balance" class="form-control" step="0.01">
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group">
                                    <label class="mb-3">
                                        <i class="fas fa-balance-scale"></i>
                                        نوع الرصيد
                                    </label>
                                    <div class="d-flex justify-content-around">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="balance_type" id="editCustomRadio1" value="credit">
                                            <label class="custom-control-label" for="editCustomRadio1">
                                                <i class="fas fa-arrow-down text-success"></i>
                                                دائن
                                            </label>
                                        </div>
                                        <div class="custom-control custom-radio">
                                            <input type="radio" class="custom-control-input" name="balance_type" id="editCustomRadio2" value="debit">
                                            <label class="custom-control-label" for="editCustomRadio2">
                                                <i class="fas fa-arrow-up text-danger"></i>
                                                مدين
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i>
                        إلغاء
                    </button>
                    <button type="submit" class="btn btn-gradient" form="editAccountForm">
                        <i class="fas fa-save"></i>
                        حفظ التعديلات
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.footer')
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/components.js"></script>
    <script src="../../../app-assets/js/scripts/pages/app-chat.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.16/jstree.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // تهيئة الشجرة مع التصميم المحسن
            $('#tree').jstree({
                'core': {
                    'themes': {
                        'rtl': true,
                        'icons': true,
                        'dots': false,
                        'responsive': true
                    },
                    'data': {
                        'url': '/accounts/tree',
                        'dataType': 'json'
                    },
                    'check_callback': true
                },
                'plugins': ['themes', 'wholerow', 'state'],
                'state': {
                    'key': 'accounts_tree_state'
                }
            });

            // تحسين عرض الشجرة بعد التحميل
            $('#tree').on('loaded.jstree', function (e, data) {
                // إضافة أيقونات مخصصة بناء على نوع الحساب
                $('#tree').find('.jstree-anchor').each(function() {
                    const $anchor = $(this);
                    const nodeData = $('#tree').jstree().get_node($anchor.parent());

                    // إضافة classes للتمييز بين أنواع الحسابات
                    if (nodeData.original && nodeData.original.account_type) {
                        $anchor.parent().attr('data-type', nodeData.original.account_type);
                    }
                });
            });

            // التعامل مع اختيار العقد
            $('#tree').on('select_node.jstree', function(e, data) {
                const nodeId = data.node.id;
                const node = data.node;

                // إضافة تأثير انتقالي
                $('.jstree-clicked').removeClass('jstree-clicked');
                data.node.a_attr.class += ' jstree-clicked';

                if (node.children.length === 0) {
                    // عرض التفاصيل في الجدول الرئيسي بدلاً من إخفاء الشجرة
                    $('#loading-spinner').show();

                    $.ajax({
                        url: `/Accounts/accounts_chart/testone/${nodeId}`,
                        type: 'GET',
                        success: function(response) {
                            $('#loading-spinner').hide();
                            // عرض التفاصيل في الجدول الرئيسي
                            $('#table-body').html(response);
                            // إضافة عنوان يوضح أننا نعرض تفاصيل الحساب
                            $('.card-header h4').html(`
                                <i class="fas fa-file-invoice text-primary"></i>
                                تفاصيل الحساب: ${node.text}
                            `);
                            // إظهار زر العودة
                            $('#backToMainAccounts').show();
                        },
                        error: function() {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء جلب البيانات.',
                                icon: 'error',
                                confirmButtonClass: 'btn btn-gradient'
                            });
                            $('#loading-spinner').hide();
                        }
                    });
                } else {
                    // إعادة تعيين العنوان للحسابات الرئيسية
                    $('.card-header h4').html(`
                        <i class="fas fa-chart-bar text-primary"></i>
                        فرع القيود
                    `);
                    // إخفاء زر العودة
                    $('#backToMainAccounts').hide();
                    loadAccountChildren(nodeId);
                }
            });

            // تحميل الحسابات الرئيسية عند بدء التشغيل
        loadParentAccounts();

        // وظيفة زر العودة للحسابات الرئيسية
        $('#backToMainAccounts').on('click', function() {
            // إلغاء تحديد العقد في الشجرة
            $('#tree').jstree('deselect_all');

            // إعادة تحميل الحسابات الرئيسية
            loadParentAccounts();

            // إعادة تعيين العنوان
            $('.card-header h4').html(`
                <i class="fas fa-chart-bar text-primary"></i>
                فرع القيود
            `);

            // إخفاء زر العودة
            $(this).hide();
        });

            // تحديد العقدة عند النقر على صف الجدول
            $('#table-body').on('click', 'tr', function(e) {
                if (!$(e.target).closest('.operations-buttons').length) {
                    const nodeId = $(this).data('node-id');
                    if (nodeId) {
                        $('#tree').jstree('deselect_all');
                        $('#tree').jstree('select_node', nodeId);
                        $('#tree').jstree('open_node', nodeId);
                    }
                }
            });
        });

        // دالة تحميل الحسابات الرئيسية
        function loadParentAccounts() {
            $.ajax({
                url: '/accounts/parents',
                type: 'GET',
                success: function(parents) {
                    displayAccountsInTable(parents);
                    // إخفاء زر العودة عند عرض الحسابات الرئيسية
                    $('#backToMainAccounts').hide();
                    // إعادة تعيين العنوان
                    $('.card-header h4').html(`
                        <i class="fas fa-chart-bar text-primary"></i>
                        فرع القيود
                    `);
                },
                error: function() {
                    console.error('فشل في تحميل الحسابات الرئيسية');
                }
            });
        }

        // دالة تحميل أبناء الحساب
        function loadAccountChildren(nodeId) {
            $.ajax({
                url: `/accounts/${nodeId}/children`,
                type: 'GET',
                success: function(children) {
                    displayAccountsInTable(children);
                },
                error: function() {
                    console.error('فشل في تحميل أبناء الحساب');
                }
            });
        }

        // دالة عرض الحسابات في الجدول مع التصميم المحسن
        function displayAccountsInTable(accounts) {
            const tableBody = $('#table-body');
            tableBody.empty();

            if (accounts.length === 0) {
                tableBody.append(`
                    <tr>
                        <td colspan="4" class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">لا توجد حسابات للعرض</h5>
                        </td>
                    </tr>
                `);
                return;
            }

            accounts.forEach(account => {
                const balanceClass = account.balance_type === 'debit' ? 'balance-debit' : 'balance-credit';
                const balanceIcon = account.balance_type === 'debit' ? 'fa-arrow-up' : 'fa-arrow-down';
                const balanceText = account.balance_type === 'debit' ? 'مدين' : 'دائن';

                tableBody.append(`
                    <tr data-node-id="${account.id}" class="fade-in">
                        <td>
                            <i class="fas fa-folder text-primary" style="font-size: 24px;"></i>
                        </td>
                        <td>
                            <div>
                                <strong class="d-block">${account.name}</strong>
                                <small class="text-muted">
                                    <i class="fas fa-hashtag"></i> ${account.code}
                                </small>
                            </div>
                        </td>
                        <td>
                            <div class="balance-info">
                                <div class="balance-amount ${balanceClass}">
                                    ${parseFloat(account.balance).toLocaleString('ar-SA')}
                                </div>
                                <div class="balance-type">
                                    <i class="fas ${balanceIcon}"></i>
                                    ${balanceText}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="operations-buttons">
                                <button class="btn btn-operation btn-edit edit-button" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-operation btn-delete" onclick="confirmDelete('${account.id}')" title="حذف">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `);
            });
        }

        // تحسين البحث مع التأثيرات المرئية
        let searchTimer;

        function performSearch(searchText, branchId) {
            clearTimeout(searchTimer);
            $('#loading-spinner').show();
            $('#search-results-dropdown').hide();

            if (searchText.length < 2 && branchId === 'all') {
                $('#loading-spinner').hide();
                return;
            }

            searchTimer = setTimeout(() => {
                $.ajax({
                    url: '/Accounts/accounts_chart/accounts/search',
                    type: 'GET',
                    data: {
                        search: searchText,
                        branch_id: branchId
                    },
                    success: function(response) {
                        displaySearchResults(response);
                        $('#loading-spinner').hide();
                    },
                    error: function() {
                        $('#loading-spinner').hide();
                        $('#search-results-dropdown').html(
                            '<div class="search-result-item text-danger">حدث خطأ أثناء البحث</div>'
                        ).show();
                    }
                });
            }, 300);
        }

        // عرض نتائج البحث مع التصميم المحسن
        function displaySearchResults(results) {
            const resultsContainer = $('#search-results-dropdown');
            resultsContainer.empty();

            if (results.length > 0) {
                results.forEach(account => {
                    const balanceIcon = account.balance_type === 'debit' ? 'fa-arrow-up text-danger' : 'fa-arrow-down text-success';
                    const balanceText = account.balance_type === 'debit' ? 'مدين' : 'دائن';

                    resultsContainer.append(`
                        <div class="search-result-item" onclick="selectAccount(${account.id}, '${account.name}', '${account.code}')">
                            <div>
                                <strong>${account.name}</strong>
                                <small class="text-muted d-block">
                                    <i class="fas fa-hashtag"></i> ${account.code}
                                </small>
                            </div>
                            <div class="text-left">
                                <div class="font-weight-bold">${parseFloat(account.balance).toLocaleString('ar-SA')}</div>
                                <small class="d-block">
                                    <i class="fas ${balanceIcon}"></i> ${balanceText}
                                </small>
                            </div>
                        </div>
                    `);
                });
            } else {
                resultsContainer.append(`
                    <div class="search-result-item text-muted text-center py-4">
                        <i class="fas fa-search fa-2x mb-2"></i>
                        <div>لا توجد نتائج مطابقة</div>
                    </div>
                `);
            }

            resultsContainer.show();
        }

        // اختيار حساب من نتائج البحث
        function selectAccount(accountId, accountName, accountCode) {
            $('#search-results-dropdown').hide();
            $('#searchInput').val(`${accountName} (${accountCode})`);

            $('#tree').jstree('deselect_all');
            $('#tree').jstree('select_node', accountId);
            $('#tree').jstree('open_node', accountId);
        }

        // أحداث البحث
        $('#searchInput').on('keyup', function() {
            const searchText = $(this).val().trim();
            const branchId = $('#branchFilter').val();
            performSearch(searchText, branchId);
        });

        $('#branchFilter').on('change', function() {
            const searchText = $('#searchInput').val().trim();
            const branchId = $(this).val();
            performSearch(searchText, branchId);
        });

        // إخفاء نتائج البحث عند النقر خارجها
        $(document).on('click', function(e) {
            if (!$(e.target).closest('#search-results-dropdown, #searchInput').length) {
                $('#search-results-dropdown').hide();
            }
        });

        // تحسين مودال إضافة الحساب
        $('#addAccountModalButton').on('click', function() {
            const selectedNode = $('#tree').jstree('get_selected', true)[0];
            const parentId = selectedNode ? selectedNode.id : null;

            if (parentId) {
                $('select[name="parent_id"]').val(parentId);
                $('select[name="type"]').val('sub');

                generateSequentialCode(parentId).done(function(response) {
                    $('#accountCode').val(response.nextCode);
                }).fail(function() {
                    console.error('فشل جلب الكود الجديد');
                });

                getAccountDetails(parentId).done(function(response) {
                    if (response.success) {
                        const mainAccountName = response.category;
                        if (['الأصول', 'الدخل'].includes(mainAccountName)) {
                            $('#customRadio1').prop('checked', true);
                        } else if (['الخصوم', 'المصروفات'].includes(mainAccountName)) {
                            $('#customRadio2').prop('checked', true);
                        }
                    }
                });
            } else {
                $('select[name="parent_id"]').val('');
                $('select[name="type"]').val('main');
                $('#customRadio1').prop('checked', true);
                $('#accountCode').val(1);
            }

            $('#info-modal-account').modal('show');
        });

        // تحديث الكود عند تغيير الحساب الرئيسي
        $('select[name="parent_id"]').on('change', function() {
            const parentId = $(this).val();
            if (parentId) {
                generateSequentialCode(parentId).done(function(response) {
                    $('#accountCode').val(response.nextCode);
                });
            } else {
                $('#accountCode').val(1);
            }
        });

        // إرسال نموذج إضافة الحساب
        $('#addAccountForm').on('submit', function(e) {
            e.preventDefault();
            const formData = $(this).serialize();

            $.ajax({
                url: $(this).attr('action'),
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم بنجاح!',
                            text: response.message || 'تمت إضافة الحساب بنجاح.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonClass: 'btn btn-gradient'
                        });

                        $('#tree').jstree('refresh');
                        updateParentAccounts();
                        $('#info-modal-account').modal('hide');
                        loadParentAccounts();
                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء إضافة الحساب.',
                            icon: 'error',
                            confirmButtonClass: 'btn btn-gradient'
                        });
                    }
                },
                error: function(xhr) {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء تنفيذ العملية.',
                        icon: 'error',
                        confirmButtonClass: 'btn btn-gradient'
                    });
                }
            });
        });

        // تحسين مودال التعديل
        $('#table-body').on('click', '.edit-button', function(e) {
            e.stopPropagation();
            const nodeId = $(this).closest('tr').data('node-id');
            $('#editAccountModal').data('node-id', nodeId);

            if (nodeId) {
                $.ajax({
                    url: `/accounts/${nodeId}/edit`,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('#editAccountModal input[name="name"]').val(response.data.name);
                            $('#editAccountModal input[name="code"]').val(response.data.code);
                            $('#editAccountModal select[name="parent_id"]').val(response.data.parent_id);
                            $('#editAccountModal input[name="balance"]').val(response.data.balance);
                            $(`#editAccountModal input[name="balance_type"][value="${response.data.balance_type}"]`).prop('checked', true);
                            $('#editAccountModal').modal('show');
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء جلب بيانات الحساب.',
                            icon: 'error',
                            confirmButtonClass: 'btn btn-gradient'
                        });
                    }
                });
            }
        });

        // إرسال نموذج تعديل الحساب
        $('#editAccountForm').on('submit', function(e) {
            e.preventDefault();
            const nodeId = $('#editAccountModal').data('node-id');
            const formData = $(this).serialize();

            $.ajax({
                url: `/accounts/${nodeId}/update`,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم التعديل!',
                            text: 'تم تعديل الحساب بنجاح.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                            confirmButtonClass: 'btn btn-gradient'
                        });

                        $('#editAccountModal').modal('hide');
                        $('#tree').jstree('refresh');
                        loadParentAccounts();
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التعديل.',
                        icon: 'error',
                        confirmButtonClass: 'btn btn-gradient'
                    });
                }
            });
        });

        // دالة الحذف المحسنة
        function confirmDelete(accountId) {
            Swal.fire({
                title: 'تأكيد الحذف',
                text: 'هل أنت متأكد من حذف هذا الحساب؟ لن تتمكن من استعادته!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="fas fa-trash"></i> نعم، احذف',
                cancelButtonText: '<i class="fas fa-times"></i> إلغاء',
                confirmButtonClass: 'btn btn-danger',
                cancelButtonClass: 'btn btn-secondary'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: `/accounts/${accountId}/delete`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: 'تم حذف الحساب بنجاح.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });

                            $(`tr[data-node-id="${accountId}"]`).fadeOut(500, function() {
                                $(this).remove();
                            });
                            $('#tree').jstree('refresh');
                        },
                        error: function(xhr) {
                            Swal.fire({
                                title: 'خطأ في الحذف!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء حذف الحساب.',
                                icon: 'error',
                                confirmButtonClass: 'btn btn-gradient'
                            });
                        }
                    });
                }
            });
        }

        // الدوال المساعدة
        function getAccountDetails(parentId) {
            return $.ajax({
                url: `/accounts/${parentId}/details`,
                type: 'GET',
                dataType: 'json'
            });
        }

        function generateSequentialCode(parentId) {
            return $.ajax({
                url: `/accounts/${parentId}/next-code`,
                type: 'GET',
                dataType: 'json'
            });
        }

        function updateParentAccounts() {
            $.ajax({
                url: '/accounts/parents',
                type: 'GET',
                success: function(accounts) {
                    const parentSelect = $('select[name="parent_id"]');
                    parentSelect.empty();
                    parentSelect.append('<option value="">لا يوجد حساب رئيسي</option>');
                    accounts.forEach(account => {
                        parentSelect.append(`<option value="${account.id}">${account.name}</option>`);
                    });
                }
            });
        }

        // تهيئة الأحداث عند تحميل الصفحة
        $(document).ready(function() {
            // إضافة تأثيرات الحركة للعناصر
            $('.fade-in').each(function(index) {
                $(this).delay(index * 100).queue(function(next) {
                    $(this).addClass('fade-in');
                    next();
                });
            });
        });
    </script>

</body>
</html> --}}



<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description"
        content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords"
        content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>دليل الحسابات</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors-rtl.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/pages/app-chat.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/custom-rtl.css">
    <link rel="stylesheet" type="text/css" href="../../../assets/css/style-rtl.css">
    <!-- END: Custom CSS-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('assets/fonts/Cairo/stylesheet.css') }}">
    <style>
        body,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        .navigation,
        .header-navbar,
        .breadcrumb {
            font-family: 'Cairo';
        }

        #tree li {
            margin-bottom: 10px;
        }

        #tree ul {
            padding-right: 20px;
        }

        .profile-picture-header {
            width: 40px;
            height: 40px;
            background-color: #7367F0;
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .search-results-dropdown {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 300px;
            overflow-y: auto;
            width: 100%;
            z-index: 1000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }

        .search-result-item {
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #eee;
        }

        .search-result-item:hover {
            background: #f8f9fa;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        table th,
        table td {
            padding: 12px;
            text-align: right;
            border-bottom: 1px solid #ddd;
        }

        #table-body tr {
            cursor: pointer;
        }

        #table-body tr:hover {
            background-color: #f0f8ff;
        }

        #tree {
            max-height: 400px;
            overflow-y: auto;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f8f9fa;
        }

        .jstree-node {
            margin-bottom: 5px;
        }

        .jstree-anchor {
            font-size: 14px;
            color: #333;
        }

        .jstree-anchor:hover {
            color: #7367F0;
        }

        .jstree-icon {
            margin-left: 5px;
        }

        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
        }

        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 10px;
        }

        .btn {
            border-radius: 5px;
        }
        /* تحسين نتائج البحث */
#search-results-dropdown {
    position: absolute;
    width: calc(100% - 30px);
    max-height: 400px;
    overflow-y: auto;
    background: white;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    z-index: 1000;
    right: 15px;
    top: 100%;
    margin-top: 5px;
}

.search-result-item {
    padding: 10px 15px;
    border-bottom: 1px solid #eee;
    cursor: pointer;
    transition: background 0.2s;
}

.search-result-item:hover {
    background: #f8f9fa;
}

.search-result-item strong {
    color: #7367F0;
}

.search-result-item small {
    font-size: 0.8em;
    color: #6c757d;
}

/* تحسين حقل البحث */
#searchInput {
    padding-right: 40px;
    border-radius: 5px;
    border: 1px solid #ddd;
    transition: border 0.3s;
}

#searchInput:focus {
    border-color: #7367F0;
    box-shadow: 0 0 0 0.2rem rgba(115, 103, 240, 0.25);
}
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body
    class="vertical-layout vertical-menu-modern content-left-sidebar chat-application navbar-floating footer-static   menu-collapsed"
    data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar">

    <!-- BEGIN: Header-->
    @include('layouts.header')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.sidebar')
    <!-- END: Main Menu-->
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>{{ session('error') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper">
            <div class="sidebar-left">
                <div class="sidebar">
                    <!-- User Chat profile area -->
                    <div class="chat-profile-sidebar">
                        <header class="chat-profile-header">
                            <span class="close-icon">
                                <i class="feather icon-x"></i>
                            </span>
                        </header>
                    </div>
                    <!--/ User Chat profile area -->

                    <!-- Chat Sidebar area -->
                    <div class="sidebar-content card">
                        <span class="sidebar-close-icon">
                            <i class="feather icon-x"></i>
                        </span>
                        <div class="chat-fixed-search" style="position: absolute">
                            <div class="d-flex align-items-center">
                                <fieldset class="form-group position-relative has-icon-left mx-1 my-0 w-50">
                                    <input type="text" id="searchInput" class="form-control"
                                        placeholder="بحث بالاسم"
                                        onkeyup="performSearch(this.value, $('#branchFilter').val())">
                                    <div class="form-control-position">
                                        <i class="feather icon-search"></i>
                                    </div>
                                </fieldset>

                                <fieldset class="form-group position-relative mx-1 my-0 w-50">
                                    <select name="branchFilter" id="branchFilter" class="form-control select2"
                                        onchange="performSearch($('#searchInput').val(), this.value)">
                                        <option value="all">كل الفروع</option>
                                        @foreach ($branches as $branch)
                                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                        @endforeach
                                    </select>
                                </fieldset>

                                <div id="loading-spinner" style="display: none;">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="sr-only">جار التحميل...</span>
                                    </div>
                                </div>
                            </div>

                            <!-- `div` لعرض النتائج -->
                            <div id="search-results-dropdown" class="search-results-dropdown"></div>
                        </div>


                        <div id="users-list" class="list-group position-relative"
                            style="margin-top: 5rem; height: 600px">
                            <!-- 3 setup a container element -->
                            <div id="tree"></div>
                        </div>
                    </div>
                    <!--/ Chat Sidebar area -->

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="chat-overlay"></div>
                        <section class="chat-app-window">

                            <div class="card" style="height: calc(var(--vh, 1vh) * 150 - 13rem); overflow-y: auto; position: relative;">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span>فرع القيود</span>
                                            </div>
                                            <fieldset class="form-group position-relative  mx-1 my-0 w-50">
                                                <select name="" class="form-control select2" id="">
                                                    <option value="all">كل الفروع</option>
                                                    @foreach ($branches as $branch)
                                                        <option value="{{ $branch->id }}">{{ $branch->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </fieldset>
                                        </div>
                                        <hr>

                                        <div class="">
                                            <table class="table" dir="rtl">
                                                <tbody id="table-body">

                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="content-right">
                                            <div class="content-wrapper">
                                                <div class="content-body">
                                                    <!-- مكان عرض البيانات -->
                                                    <div id="table-container"></div>
                                                </div>
                                            </div>
                                        </div>

                                        <button id="addAccountModalButton"
                                            class="btn btn-outline-info btn-sm waves-effect waves-light"
                                            data-toggle="modal" data-target="#info-modal-account">
                                            <i class="fa fa-plus-circle me-2"></i>أضف حساب
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="info-modal-account" tabindex="-1"
                                            role="dialog" aria-labelledby="myModalLabel130" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                                                role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                        <h5 class="modal-title" id="myModalLabel130">أضف حساب</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="addAccountForm" action="/accounts/store_account"
                                                            method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-name-vertical">نوع
                                                                            الحساب</label>
                                                                        <select name="type" id=""
                                                                            class="form-control">
                                                                            <option value="sub">حساب فرعي</option>
                                                                            <option value="main">حساب رئيسي</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="code-id-vertical">الكود</label>
                                                                        <input type="number" id="accountCode"
                                                                            class="form-control" name="code">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="name-info-vertical">الاسم</label>
                                                                        <input type="text" id="accountName"
                                                                            class="form-control" name="name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-vertical">حساب
                                                                            رئيسي</label>
                                                                        <select name="parent_id" id=""
                                                                            class="form-control">
                                                                            <option value="">لا يوجد حساب رئيسي
                                                                            </option>
                                                                            @foreach ($accounts as $account)
                                                                                <option value="{{ $account->id }}">
                                                                                    {{ $account->name }} - {{ $account->code }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="account-vertical" class="mb-2">النوع
                                                                        :</label>
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li class="d-inline-block mr-2">
                                                                            <fieldset>
                                                                                <div
                                                                                    class="custom-control custom-radio">
                                                                                    <input type="radio"
                                                                                        class="custom-control-input"
                                                                                        name="balance_type"
                                                                                        id="customRadio1"
                                                                                        value="credit">
                                                                                    <label class="custom-control-label"
                                                                                        for="customRadio1">دائن</label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </li>
                                                                        <li class="d-inline-block mr-2">
                                                                            <fieldset>
                                                                                <div
                                                                                    class="custom-control custom-radio">
                                                                                    <input type="radio"
                                                                                        class="custom-control-input"
                                                                                        name="balance_type"
                                                                                        id="customRadio2"
                                                                                        value="debit">
                                                                                    <label class="custom-control-label"
                                                                                        for="customRadio2">مدين</label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </li>
                                                                    </ul>
                                                                </div>

                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">إغلاق</button>
                                                        <button type="submit" class="btn btn-info"
                                                            form="addAccountForm">حفظ</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->


                                        <!--- Edit Modal--->
                                        <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog"
                                            aria-labelledby="editAccountModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg"
                                                role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                        <h5 class="modal-title" id="myModalLabel130">تعديل الحساب</h5>
                                                        <button type="button" class="close" data-dismiss="modal"
                                                            aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form id="editAccountForm">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-name-vertical">نوع
                                                                            الحساب</label>
                                                                        <select name="type" id=""
                                                                            class="form-control">
                                                                            <option value="sub">حساب فرعي</option>
                                                                            <option value="main">حساب رئيسي</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="code-id-vertical">الكود</label>
                                                                        <input type="number" id="accountCode"
                                                                            class="form-control" name="code">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="name-info-vertical">الاسم</label>
                                                                        <input type="text" id="accountName"
                                                                            class="form-control" name="name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-vertical">حساب
                                                                            رئيسي</label>
                                                                        <select name="parent_id" id=""
                                                                            class="form-control">
                                                                            <option value="">لا يوجد حساب رئيسي
                                                                            </option>
                                                                            @foreach ($accounts as $account)
                                                                                <option value="{{ $account->id }}">
                                                                                    {{ $account->name }} {{ $account->code }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="balance">الرصيد</label>
                                                                        <input type="number" name="balance"
                                                                            id="balance" class="form-control"
                                                                            step="0.01">
                                                                    </div>
                                                                </div>
                                                                <div class="form-group col-12">
                                                                    <label for="account-vertical" class="mb-2">النوع
                                                                        :</label>
                                                                    <ul class="list-unstyled mb-0">
                                                                        <li class="d-inline-block mr-2">
                                                                            <fieldset>
                                                                                <div
                                                                                    class="custom-control custom-radio">
                                                                                    <input type="radio"
                                                                                        class="custom-control-input"
                                                                                        name="balance_type"
                                                                                        id="customRadio1"
                                                                                        value="credit">
                                                                                    <label class="custom-control-label"
                                                                                        for="customRadio1">دائن</label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </li>
                                                                        <li class="d-inline-block mr-2">
                                                                            <fieldset>
                                                                                <div
                                                                                    class="custom-control custom-radio">
                                                                                    <input type="radio"
                                                                                        class="custom-control-input"
                                                                                        name="balance_type"
                                                                                        id="customRadio2"
                                                                                        value="debit">
                                                                                    <label class="custom-control-label"
                                                                                        for="customRadio2">مدين</label>
                                                                                </div>
                                                                            </fieldset>
                                                                        </li>
                                                                    </ul>
                                                                </div>

                                                            </div>
                                                        </form>

                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">إغلاق</button>
                                                        <button type="submit" class="btn btn-info"
                                                            form="editAccountForm">حفظ التعديلات</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!--- Edit Modal--->

                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.footer')
    <!-- END: Footer-->

    <!-- BEGIN: Vendor JS-->
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="../../../app-assets/js/scripts/pages/app-chat.js"></script>
    <!-- END: Page JS-->

    <!-- 5 include the minified jstree source -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            $('#tree').jstree({
                'core': {
                    'themes': {
                        'rtl': true,
                        'icons': true
                    },
                    'data': {
                        'url': '/accounts/tree',
                        'dataType': 'json'
                    }
                }
            });

            $('#tree').on('select_node.jstree', function(e, data) {
                const nodeId = data.node.id;
                const node = data.node;

                if (node.children.length === 0) {
                    $('#loading-spinner').show();
                    $('#table-container').hide();

                    $.ajax({
                        url: `/Accounts/accounts_chart/testone/${nodeId}`,
                        type: 'GET',
                        success: function(response) {
                            $('#loading-spinner').hide();
                            $('#table-container').html(response).show();
                        },
                        error: function() {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء جلب البيانات.',
                                icon: 'error',
                            });
                            $('#loading-spinner').hide();
                        }
                    });
                } else {
                    $('#table-container').hide();
                }
            });
        });

        $(document).ready(function() {
            // جلب الآباء عند تحميل الصفحة
            $.ajax({
                url: '/accounts/parents', // رابط الـ API لجلب الآباء
                type: 'GET',
                success: function(parents) {
                    const tableBody = $('#table-body');
                    tableBody.empty(); // تفريغ الجدول

                    // إضافة الآباء إلى الجدول
                    parents.forEach(parent => {
                        tableBody.append(`
                            <tr data-node-id="${parent.id}" class="table-active">
                                <td style="width: 3%">
                                    <i class="feather icon-folder" style="font-size: 30px"></i>
                                </td>
                                <td>
                                    <strong>${parent.name}</strong><br>
                                    <small>${parent.code} #</small>
                                </td>
                                <td style="width: 5%">
                                    <strong>${parent.balance}</strong><br>
                                    <small>${parent.balance_type === 'debit' ? 'مدين' : 'دائن'}</small>
                                </td>
                                <td style="width: 10%">
                                    <div class="operation">
                                        <a id="edit" href="#" class="edit-button"><i class="fa fa-edit mr-1"></i></a>
                                        <a id="delete" href="#" class="text-danger" onclick="confirmDelete('${parent.id}')">
                                            <i class="fa fa-trash mr-1"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        `);
                    });
                }
            });

            // التعامل مع اختيار عقدة من الشجرة
            $('#tree').on('select_node.jstree', function(e, data) {
                const nodeId = data.node.id; // ID العقدة المختارة

                // جلب بيانات الأبناء
                $.ajax({
                    url: `/accounts/${nodeId}/children`, // استعلام عن أبناء العقدة
                    type: 'GET',
                    success: function(children) {
                        const tableBody = $('#table-body');
                        tableBody.empty(); // تفريغ الجدول

                        children.forEach(child => {
                            tableBody.append(`
                                <tr data-node-id="${child.id}" class="table-active">
                                    <td style="width: 3%">
                                        <i class="feather icon-folder" style="font-size: 30px"></i>
                                    </td>
                                    <td>
                                        <strong>${child.name}</strong><br>
                                        <small>${child.code} #</small>
                                    </td>
                                    <td style="width: 5%">
                                        <strong>${child.balance}</strong><br>
                                        <small>${child.balance_type === 'debit' ? 'مدين' : 'دائن'}</small>
                                    </td>
                                    <td style="width: 10%">
                                        <div class="operation">
                                            <a id="edit" href="#" class="edit-button"><i class="fa fa-edit mr-1"></i></a>
                                            <a id="delete" href="#" class="text-danger" onclick="confirmDelete('${child.id}')">
                                                <i class="fa fa-trash mr-1"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            });

        });

        // حدث عند الضغط على صف في الجدول
        $('#table-body').on('click', 'tr', function() {
            const nodeId = $(this).data('node-id'); // الحصول على ID العقدة من الصف

            if (nodeId) {
                // فتح وتحديد العقدة في الشجرة
                $('#tree').jstree('deselect_all'); // إلغاء تحديد أي عقدة محددة
                $('#tree').jstree('select_node', nodeId); // تحديد العقدة
                $('#tree').jstree('open_node', nodeId); // فتح العقدة
            }
        });

        $('#table-body').on('click', '.operation', function(event) {
            event.stopPropagation(); // منع الحدث من الانتقال إلى الصف
        });
    </script>

    <script>
        $(document).ready(function() {
            // فتح المودال وتعبئة الحقول
            $('#addAccountModalButton').on('click', function() {
                const selectedNode = $('#tree').jstree('get_selected', true)[
                    0]; // الحصول على العقدة المحددة
                const parentId = selectedNode ? selectedNode.id : null;

                if (parentId) {
                    // إذا تم تحديد عقدة
                    $('select[name="parent_id"]').val(parentId); // ضبط الحساب الرئيسي
                    $('select[name="type"]').val('sub'); // افتراضيًا، الحساب فرعي

                    // جلب الكود الجديد
                    generateSequentialCode(parentId).done(function(response) {
                        $('#accountCode').val(response.nextCode); // تعيين الكود الجديد
                    }).fail(function() {
                        console.error('فشل جلب الكود الجديد');
                    });

                    // جلب تفاصيل الحساب لتحديد النوع
                    getAccountDetails(parentId).done(function(response) {
                        if (response.success) {
                            const mainAccountName = response.category;

                            if (['الأصول', 'الدخل'].includes(mainAccountName)) {
                                $('#customRadio1').prop('checked', true); // النوع دائن
                            } else if (['الخصوم', 'المصروفات'].includes(mainAccountName)) {
                                $('#customRadio2').prop('checked', true); // النوع مدين
                            }
                        } else {
                            console.error('فشل جلب تفاصيل الحساب');
                        }
                    }).fail(function() {
                        console.error('فشل في الاتصال بالـ API لجلب التفاصيل');
                    });

                } else {
                    // إذا لم يتم تحديد أي عقدة
                    $('select[name="parent_id"]').val(''); // لا يوجد حساب رئيسي
                    $('select[name="type"]').val('main'); // الحساب رئيسي
                    $('#customRadio1').prop('checked', true); // النوع الافتراضي دائن
                    $('#accountCode').val(1); // الكود يبدأ بـ 1
                }

                // فتح المودال
                $('#info-modal-account').modal('show');
            });

            // ضبط الكود بشكل تلقائي عند تغيير الحقل
            $('select[name="parent_id"]').on('change', function() {
                const parentId = $(this).val();

                if (parentId) {
                    generateSequentialCode(parentId).done(function(response) {
                        $('#accountCode').val(response.nextCode); // تعيين الكود الجديد
                    }).fail(function() {
                        console.error('فشل جلب الكود الجديد');
                    });
                } else {
                    $('#accountCode').val(1); // افتراضيًا، اجعل الكود يبدأ بـ 1
                }
            });

            // دالة لجلب تفاصيل الحساب
            function getAccountDetails(parentId) {
                return $.ajax({
                    url: `/accounts/${parentId}/details`, // رابط API لجلب تفاصيل الحساب
                    type: 'GET',
                    dataType: 'json',
                });
            }

            // دالة لتوليد الكود الجديد
            function generateSequentialCode(parentId) {
                return $.ajax({
                    url: `/accounts/${parentId}/next-code`, // رابط API لجلب الكود الجديد
                    type: 'GET',
                    dataType: 'json',
                });
            }

            // ADD ACCOUNT ###################################################################################

            // عند تقديم النموذج
            $('#addAccountForm').on('submit', function(e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'تم بنجاح!',
                                text: response.message || 'تمت العملية بنجاح.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });

                            // تحديث الشجرة
                            $('#tree').jstree('refresh');

                            // إعادة تحميل الحسابات الرئيسية في المودال
                            updateParentAccounts();

                            // إغلاق المودال
                            setTimeout(function() {
                                $('#info-modal-account').removeClass('show');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            });

                            // reloadPageWithTreeState();

                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: response.message || 'حدث خطأ أثناء العملية.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function(xhr) {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء تنفيذ العملية. الرجاء المحاولة مرة أخرى.',
                            icon: 'error',
                        });
                    }
                });
            });


        });
    </script>

    <script>
        function updateParentAccounts() {
            $.ajax({
                url: '/accounts/parents', // رابط API لجلب الحسابات الرئيسية
                type: 'GET',
                success: function(accounts) {
                    const parentSelect = $('select[name="parent_id"]');
                    parentSelect.empty(); // تفريغ الخيارات
                    parentSelect.append('<option value="">لا يوجد حساب رئيسي</option>');

                    accounts.forEach(account => {
                        parentSelect.append(`<option value="${account.id}">${account.name}</option>`);
                    });
                },
            });
        }
    </script>

    <script>
        function confirmDelete(parentId) {
            event.preventDefault();

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'لن تتمكن من استعادة هذا العنصر بعد الحذف!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // استدعاء AJAX لتنفيذ الحذف
                    $.ajax({
                        url: `/accounts/${parentId}/delete`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: response.message || 'تم حذف العنصر بنجاح.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $(`tr[data-node-id="${parentId}"]`).remove();
                            $('#tree').jstree('refresh');
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText); // فحص الخطأ
                            Swal.fire({
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message ||
                                    'حدث خطأ أثناء الحذف. الرجاء المحاولة مرة أخرى.',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }
    </script>

    <script>
        let openedNodes = [];
        let selectedNode = null;

        // احفظ الحالة قبل التحديث
        $('#tree').on('state_ready.jstree', function(e, data) {
            openedNodes = $('#tree').jstree('get_opened');
            selectedNode = $('#tree').jstree('get_selected')[0];
        });

        $(document).ready(function() {
            $('#tree').on('loaded.jstree', function() {
                // إعادة فتح العقد المحفوظة
                if (openedNodes.length) {
                    $('#tree').jstree('open_node', openedNodes);
                }

                // تحديد العقدة المحفوظة
                if (selectedNode) {
                    $('#tree').jstree('select_node', selectedNode);
                }
            });
        });

        function reloadPageWithTreeState() {
            openedNodes = $('#tree').jstree('get_opened'); // احفظ العقد المفتوحة
            selectedNode = $('#tree').jstree('get_selected')[0]; // احفظ العقدة المحددة

            // خزّن الحالة في localStorage
            localStorage.setItem('openedNodes', JSON.stringify(openedNodes));
            localStorage.setItem('selectedNode', selectedNode);

            // أعد تحميل الصفحة
            location.reload();
        }

        $(document).ready(function() {
            // استرجاع الحالة من localStorage
            let savedOpenedNodes = JSON.parse(localStorage.getItem('openedNodes')) || [];
            let savedSelectedNode = localStorage.getItem('selectedNode') || null;

            $('#tree').on('loaded.jstree', function() {
                // إعادة فتح العقد المحفوظة
                if (savedOpenedNodes.length) {
                    $('#tree').jstree('open_node', savedOpenedNodes);
                }

                // تحديد العقدة المحفوظة
                if (savedSelectedNode) {
                    $('#tree').jstree('select_node', savedSelectedNode);
                }

                // تنظيف البيانات المحفوظة
                localStorage.removeItem('openedNodes');
                localStorage.removeItem('selectedNode');
            });
        });

        // إضافة حدث عند الضغط على زر التعديل
        $('#table-body').on('click', '.edit-button', function() {
            const nodeId = $(this).closest('tr').data('node-id'); // الحصول على ID العنصر
            $('#editAccountModal').data('node-id', nodeId);

            if (nodeId) {
                // جلب بيانات العنصر
                $.ajax({
                    url: `/accounts/${nodeId}/edit`, // رابط API لجلب بيانات العنصر
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            // تعبئة المودال بالبيانات
                            $('#editAccountModal input[name="name"]').val(response.data.name);
                            $('#editAccountModal input[name="code"]').val(response.data.code);
                            $('#editAccountModal select[name="parent_id"]').val(response.data
                                .parent_id);
                            $('#editAccountModal input[name="balance"]').val(response.data.balance);
                            $(`#editAccountModal input[name="balance_type"][value="${response.data.balance_type}"]`)
                                .prop('checked', true);

                            // عرض المودال
                            $('#editAccountModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: response.message || 'حدث خطأ أثناء جلب البيانات.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء جلب البيانات.',
                            icon: 'error',
                        });
                    }
                });
            }
        });

        // احفظ تعديلات الحساب
        $('#editAccountForm').on('submit', function(e) {
            e.preventDefault();

            const nodeId = $('#editAccountModal').data('node-id');
            if (!nodeId) {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'رقم الحساب غير موجود.',
                    icon: 'error',
                });
                return;
            }

            const formData = $(this).serialize();

            $.ajax({
                url: `/accounts/${nodeId}/update`,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم التعديل!',
                            text: response.message || 'تم تعديل البيانات بنجاح.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });

                        const row = $('#table-body').find(`tr[data-node-id="${nodeId}"]`);
                        if (row.length) {
                            row.html(`
                                <td>${response.data.code}</td>
                                <td>${response.data.name}</td>
                                <td>${response.data.balance}</td>
                                <td>${response.data.balance_type === 'credit' ? 'دائن' : 'مدين'}</td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-button">تعديل</button>
                                </td>
                            `);
                        } else {
                            console.error('لم يتم العثور على الصف المطلوب.');
                        }

                        // إغلاق المودال
                        setTimeout(function() {
                            $('#editAccountModal').removeClass('show');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        });

                        // تحديث الشجرة
                        $('#tree').jstree('refresh');

                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء التعديل.',
                            icon: 'error',
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التعديل.',
                        icon: 'error',
                    });
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            // جلب القيود عند تحديد فرع من الشجرة
            $('#tree').on('select_node.jstree', function(e, data) {
                const nodeId = data.node.id; // ID العقدة المحددة
                const node = data.node;

                // التحقق مما إذا كان الحساب لديه أبناء (آخر جذر)
                if (node.children.length === 0) {
                    // إظهار مؤشر التحميل
                    $('#loading-spinner').show();
                    $('#table-container').hide();

                    // جلب القيود المرتبطة بالحساب
                    $.ajax({
                        url: `/Accounts/accounts_chart/${nodeId}/journal-entries`, // رابط API لجلب القيود
                        type: 'GET',
                        success: function(response) {
                            const tableBody = $('#table-body');
                            tableBody.empty(); // تفريغ الجدول

                            if (response.length > 0) {
                                response.forEach(entry => {
                                    // إنشاء الرابط بشكل ديناميكي
                                    const showUrl =
                                        `/ar/Accounts/journal/show/${entry.id}`;

                                    tableBody.append(`
                                <tr>
                                    <td>${entry.account.name} (${entry.account.code})</td>
                                    <td>${entry.description}</td>
                                    <td>${entry.amount}</td>
                                    <td>
                                        <a href="${showUrl}" class="btn btn-sm btn-info">
                                            عرض القيد
                                        </a>
                                    </td>
                                </tr>
                            `);
                                });
                            } else {
                                tableBody.append(
                                    '<tr><td colspan="4">لا توجد قيود لهذا الحساب.</td></tr>'
                                );
                            }

                            // إخفاء مؤشر التحميل وإظهار الجدول
                            $('#loading-spinner').hide();
                            $('#table-container').show();
                        },
                        error: function() {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء جلب القيود.',
                                icon: 'error',
                            });

                            // إخفاء مؤشر التحميل في حالة الخطأ
                            $('#loading-spinner').hide();
                        }
                    });
                } else {
                    // إخفاء الجدول إذا كان الحساب لديه أبناء
                    $('#table-container').hide();
                }
            });
        });

     // متغير لتأخير البحث (Debounce)
let searchTimer;

function performSearch(searchText, branchId) {
    // إلغاء البحث السابق إذا كان لا يزال قيد التنفيذ
    clearTimeout(searchTimer);

    // إظهار مؤشر التحميل
    $('#loading-spinner').show();
    $('#search-results-dropdown').hide();

    // البحث فقط إذا كان النص أكثر من حرفين أو تم اختيار فرع
    if (searchText.length < 2 && branchId === 'all') {
        $('#loading-spinner').hide();
        return;
    }

    // تأخير البحث 300 مللي ثانية بعد آخر كتابة
    searchTimer = setTimeout(() => {
        $.ajax({
            url: '/Accounts/accounts_chart/accounts/search',
            type: 'GET',
            data: {
                search: searchText,
                branch_id: branchId
            },
            success: function(response) {
                const resultsContainer = $('#search-results-dropdown');
                resultsContainer.empty();

                if (response.length > 0) {
                    response.forEach(account => {
                        resultsContainer.append(`
                            <div class="search-result-item"
                                 onclick="selectAccount(${account.id}, '${account.name}', '${account.code}')">
                                <strong>${account.name}</strong>
                                <span class="text-muted">(${account.code})</span>
                                <small class="d-block">${account.balance_type === 'debit' ? 'مدين' : 'دائن'}: ${account.balance}</small>
                            </div>
                        `);
                    });
                } else {
                    resultsContainer.append(
                        '<div class="search-result-item text-muted">لا توجد نتائج مطابقة</div>'
                    );
                }

                $('#loading-spinner').hide();
                resultsContainer.show();
            },
            error: function() {
                $('#loading-spinner').hide();
                $('#search-results-dropdown').html('<div class="search-result-item text-danger">حدث خطأ أثناء البحث</div>').show();
            }
        });
    }, 300);
}

// دالة عند اختيار نتيجة بحث
function selectAccount(accountId, accountName, accountCode) {
    // إخفاء نتائج البحث
    $('#search-results-dropdown').hide();

    // تحديث حقل البحث
    $('#searchInput').val(`${accountName} (${accountCode})`);

    // تحديد الحساب في الشجرة
    $('#tree').jstree('deselect_all');
    $('#tree').jstree('select_node', accountId);
    $('#tree').jstree('open_node', accountId);

    // جلب تفاصيل الحساب
    loadAccountDetails(accountId);
}

// تهيئة البحث عند تحميل الصفحة
$(document).ready(function() {
    // أحداث البحث
    $('#searchInput').on('keyup', function() {
        const searchText = $(this).val().trim();
        const branchId = $('#branchFilter').val();
        performSearch(searchText, branchId);
    });

    $('#branchFilter').on('change', function() {
        const searchText = $('#searchInput').val().trim();
        const branchId = $(this).val();
        performSearch(searchText, branchId);
    });

    // إخفاء نتائج البحث عند النقر خارجها
    $(document).on('click', function(e) {
        if (!$(e.target).closest('#search-results-dropdown, #searchInput').length) {
            $('#search-results-dropdown').hide();
        }
    });
});
// عند تحميل الصفحة، عرض بعض الحسابات الشائعة
$(document).ready(function() {
    performSearch('', 'all');
});
    </script>

</body>
<!-- END: Body-->

</html>
