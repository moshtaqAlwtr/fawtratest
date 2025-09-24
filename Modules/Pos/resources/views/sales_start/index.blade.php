<!DOCTYPE html>
<html class="loading" lang="ar" data-textdirection="rtl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="نظام نقاط البيع المتطور">
    <meta name="author" content="POS System">
    <title>نظام نقاط البيع</title>

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <link rel="apple-touch-icon" href="{{asset('app-assets/images/ico/apple-icon-120.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('app-assets/images/ico/favicon.ico')}}">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/vendors-rtl.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/extensions/nouislider.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/vendors/css/forms/select/select2.min.css')}}">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/bootstrap.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/bootstrap-extended.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/colors.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/components.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/themes/dark-layout.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/themes/semi-dark-layout.css')}}">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/core/menu/menu-types/vertical-menu.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/core/colors/palette-gradient.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/plugins/extensions/noui-slider.min.css')}}">
    <link rel="stylesheet" type="text/css" href="{{asset('app-assets/css-rtl/pages/app-ecommerce-shop.css')}}">
    <!-- END: Page CSS-->

    <!-- Custom Fonts -->
    <link rel="stylesheet" href="{{ asset('assets/fonts/Cairo/stylesheet.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <style>
        :root {
            --primary-color: #373F6A;
            --secondary-color: #4CAF50;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-bg: #E9EEF4;
            --card-shadow: 0 4px 15px rgba(0,0,0,0.1);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        body, h1, h2, h3, h4, h5, h6, .navigation, .header-navbar, .breadcrumb {
            font-family: 'Cairo', sans-serif !important;
        }

        .app-content {
            margin-right: 0px !important;
            height: 100vh;
            padding: 8px;
        }

        .content-wrapper {
            display: flex;
            height: 100%;
            gap: 12px;
        }

        /* الجانب الأيسر - المنتجات */
        .left-card {
            flex: 2.2;
            min-width: 0;
        }

        /* الجانب الأيمن - الطلبات */
        .right-card {
            flex: 1;
            min-width: 350px;
        }

        .card {
            height: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            overflow: hidden;
        }

        .card-content {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        /* شريط الأدوات المحسن */
        .header-navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4a5296 100%) !important;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
            padding: 15px 20px;
            box-shadow: 0 2px 10px rgba(55, 63, 106, 0.3);
        }

        .navbar-wrapper {
            width: 100%;
        }

        .navbar-container {
            width: 100%;
        }

        .navbar-nav {
            width: 100%;
            justify-content: space-between;
            align-items: center;
        }

        .nav-item .nav-link {
            color: white !important;
            padding: 10px 15px;
            border-radius: 8px;
            transition: var(--transition);
            background: rgba(255,255,255,0.1);
            margin: 0 5px;
        }

        .nav-item .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* منطقة البحث والأزرار المحسنة */
        .search-section {
            background: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .search-row {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-search {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 12px 16px;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
            min-height: 44px; /* للمس */
        }

        .btn-search:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-search.active {
            background: var(--primary-color);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: var(--border-radius);
            padding: 10px;
        }

        .dropdown-item {
            border-radius: 8px;
            transition: var(--transition);
            padding: 10px 15px;
            margin-bottom: 5px;
        }

        .dropdown-item:hover {
            background: var(--light-bg);
            transform: translateX(-5px);
        }

        .search-input {
            flex: 1;
            min-width: 250px;
            padding: 12px 20px;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            font-size: 16px;
            transition: var(--transition);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(76, 175, 80, 0.1);
        }

        /* منطقة المحتوى المحسنة */
        .content-body {
            flex: 1;
            padding: 20px;
            overflow: auto;
            background: #fafbfc;
        }

        /* التصنيفات المحسنة */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .category-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            min-height: 140px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }

        .category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--secondary-color);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .category-card:hover {
            border-color: var(--secondary-color);
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .category-card:hover::before {
            transform: scaleX(1);
        }

        .category-card.active {
            border-color: var(--secondary-color);
            background: linear-gradient(135deg, var(--secondary-color) 0%, #45a049 100%);
            color: white;
        }

        .category-card.active::before {
            transform: scaleX(1);
        }

        .category-image {
            width: 60px;
            height: 60px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 10px;
            border: 3px solid #e9ecef;
            transition: var(--transition);
        }

        .category-card:hover .category-image {
            border-color: var(--secondary-color);
        }

        /* المنتجات المحسنة */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(190px, 1fr));
            gap: 16px;
        }

        .product-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 15px;
            cursor: pointer;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--success-color);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .product-card:hover {
            border-color: var(--success-color);
            transform: translateY(-5px);
            box-shadow: var(--card-shadow);
        }

        .product-card:hover::before {
            transform: scaleX(1);
        }

        .product-image {
            width: 100%;
            height: 130px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }

        .product-name {
            font-weight: 600;
            font-size: 0.95rem;
            margin-bottom: 8px;
            color: #333;
            line-height: 1.3;
            height: 2.6em;
            overflow: hidden;
        }

        .product-price {
            color: var(--success-color);
            font-weight: 700;
            font-size: 1.1rem;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
        }

        .info-btn {
            background: var(--light-bg);
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .info-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        /* الجانب الأيمن - منطقة الطلبات المحسنة */
        .order-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4a5296 100%);
            color: white;
            padding: 20px;
        }

        .order-tabs {
            display: flex;
            gap: 8px;
            margin-bottom: 15px;
            overflow-x: auto;
            padding-bottom: 5px;
        }

        .order-tab {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 10px 15px;
            border-radius: 20px;
            white-space: nowrap;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            min-width: 44px;
            backdrop-filter: blur(10px);
        }

        .order-tab.active {
            background: white;
            color: var(--primary-color);
            font-weight: 600;
        }

        .order-tab:hover:not(.active) {
            background: rgba(255,255,255,0.25);
        }

        .client-selector {
            background: rgba(255,255,255,0.1);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: var(--border-radius);
            padding: 12px 16px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .client-selector:hover {
            background: rgba(255,255,255,0.2);
        }

        /* منطقة الطلبات */
        .order-content {
            flex: 1;
            background: var(--light-bg);
            overflow-y: auto;
            padding: 15px;
        }

        .order-items-container {
            max-height: 100%;
            overflow-y: auto;
        }

        .order-item {
            background: white;
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            gap: 15px;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .order-item:hover {
            border-color: var(--secondary-color);
            transform: translateX(-3px);
            box-shadow: var(--card-shadow);
        }

        .item-details {
            flex: 1;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .item-price {
            color: #666;
            font-size: 0.9rem;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 25px;
            padding: 2px;
        }

        .qty-btn {
            width: 32px;
            height: 32px;
            border: none;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
        }

        .qty-btn:hover {
            background: #45a049;
            transform: scale(1.1);
        }

        .qty-display {
            min-width: 40px;
            text-align: center;
            font-weight: 600;
            font-size: 16px;
        }

        .item-total {
            font-weight: 700;
            color: var(--success-color);
            font-size: 1.1rem;
        }

        .delete-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .delete-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        /* ملخص الطلب المحسن */
        .order-summary {
            background: white;
            padding: 20px;
            border-top: 3px solid var(--secondary-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--success-color);
            border-top: 2px solid var(--secondary-color);
            padding-top: 15px;
            margin-top: 10px;
        }

        .discount-section {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 15px;
            margin: 15px 0;
            border: 2px solid #e9ecef;
        }

        .discount-controls {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 10px;
        }

        .discount-type-select {
            flex: none;
            width: 100px;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background: white;
        }

        .discount-input {
            flex: 1;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            text-align: center;
        }

        /* أزرار العمليات المحسنة */
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .action-btn {
            flex: 1;
            padding: 15px 20px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 50px;
        }

        .btn-hold {
            background: linear-gradient(135deg, var(--warning-color) 0%, #e0a800 100%);
            color: #212529;
        }

        .btn-hold:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 193, 7, 0.3);
        }

        .btn-pay {
            background: linear-gradient(135deg, var(--success-color) 0%, #20c997 100%);
            color: white;
        }

        .btn-pay:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3);
        }

        /* حالة فارغة محسنة */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* التصميم المتجاوب المحسن */
        @media (max-width: 1400px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(170px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            .content-wrapper {
                flex-direction: column;
                gap: 8px;
            }
            
            .left-card {
                flex: none;
                height: 60vh;
            }
            
            .right-card {
                flex: none;
                height: 38vh;
                min-width: auto;
            }
        }

        @media (max-width: 768px) {
            .app-content {
                padding: 4px;
            }
            
            .search-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .search-buttons {
                width: 100%;
                justify-content: space-around;
            }
            
            .search-input {
                min-width: auto;
                width: 100%;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
                gap: 10px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 10px;
            }
            
            .category-card, .product-card {
                min-height: 120px;
                padding: 12px;
            }
            
            .product-image {
                height: 100px;
            }
            
            .order-tabs {
                justify-content: flex-start;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        /* تحسينات اللمس */
        @media (pointer: coarse) {
            .category-card, .product-card, .order-tab, .qty-btn, .delete-btn, .action-btn {
                min-height: 44px;
                min-width: 44px;
            }
            
            .search-input {
                font-size: 16px; /* منع التكبير في iOS */
                padding: 12px 16px;
            }
            
            .btn-search {
                padding: 12px 16px;
            }
        }

        /* رسوم متحركة */
        .fade-in {
            animation: fadeIn 0.4s ease-out;
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

        .slide-in {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { 
                transform: translateX(-20px); 
                opacity: 0; 
            }
            to { 
                transform: translateX(0); 
                opacity: 1; 
            }
        }

        /* مؤشر التحميل */
        .loading-spinner {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #e9ecef;
            border-top: 4px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* تحسين Scrollbar */
        .order-content::-webkit-scrollbar,
        .content-body::-webkit-scrollbar {
            width: 6px;
        }

        .order-content::-webkit-scrollbar-track,
        .content-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .order-content::-webkit-scrollbar-thumb,
        .content-body::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .order-content::-webkit-scrollbar-thumb:hover,
        .content-body::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* تأثير النبض للأزرار المهمة */
        .btn-pay {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); }
            50% { box-shadow: 0 6px 25px rgba(40, 167, 69, 0.5); }
            100% { box-shadow: 0 6px 20px rgba(40, 167, 69, 0.3); }
        }

        /* اختصارات لوحة المفاتيح */
        .keyboard-shortcut {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.7rem;
            font-weight: 500;
        }
    </style>
</head>

<body class="vertical-layout vertical-menu-modern 2-columns navbar-floating footer-static menu-collapsed" 
      data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- BEGIN: Content-->
    <div class="app-content">
        <div class="content-wrapper">
            
            <!-- الجانب الأيسر - المنتجات والتصنيفات -->
            <div class="left-card">
                <div class="card">
                    <div class="card-content">
                        <!-- شريط الأدوات العلوي -->
                        <nav class="header-navbar navbar-expand-lg navbar navbar-with-menu navbar-light navbar-shadow">
                            <div class="navbar-wrapper">
                                <div class="navbar-container content">
                                    <div class="navbar-collapse">
                                        <ul class="nav navbar-nav bookmark-icons mr-auto">
                                            <li class="nav-item">
                                                <a class="nav-link" href="#" title="الطباعة (F4)">
                                                    <i class="fas fa-print"></i>
                                                    <span class="keyboard-shortcut">F4</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link" href="#" title="عرض كامل (F11)">
                                                    <i class="fas fa-expand"></i>
                                                    <span class="keyboard-shortcut">F11</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ route('dashboard_sales.index') }}" target="_blank" 
                                                   class="nav-link" title="الصفحة الرئيسية (F12)">
                                                    <i class="fas fa-home"></i>
                                                    <span class="keyboard-shortcut">F12</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <ul class="nav navbar-nav float-right">
                                            <li class="dropdown dropdown-user nav-item">
                                                <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                                    <div class="user-nav d-sm-flex d-none">
                                                        <span class="user-name text-bold-600 text-white">
                                                            {{ auth()->user()->name }}
                                                        </span>
                                                        <span class="user-status">
                                                            <span class="text-white" id="currentDateTime"></span>
                                                        </span>
                                                    </div>
                                                    <span>
                                                        <img class="round" src="{{asset('app-assets/images/portrait/small/avatar-s-13.jpg')}}" 
                                                             alt="avatar" height="40" width="40">
                                                    </span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"><i class="feather icon-power"></i> تسجيل الخروج</a>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </nav>

                        <!-- منطقة البحث والأزرار -->
                        <div class="search-section">
                            <div class="search-row">
                                <div class="search-buttons">
                                    <button class="btn btn-search" id="categoriesBtn" title="التصنيفات (F1)">
                                        <i class="fas fa-th-large"></i>
                                        <span class="d-none d-md-inline ms-2">التصنيفات</span>
                                        <span class="keyboard-shortcut">F1</span>
                                    </button>
                                    
                                    <div class="dropdown">
                                        <button class="btn btn-search dropdown-toggle" type="button" 
                                                data-toggle="dropdown" title="خيارات البحث (F2)">
                                            <i class="fas fa-search"></i>
                                            <span class="d-none d-md-inline ms-2" id="searchModeText">المنتجات</span>
                                            <span class="keyboard-shortcut">F2</span>
                                        </button>
                                        <div class="dropdown-menu">
                                            <a class="dropdown-item" href="#" data-mode="products">
                                                <i class="fas fa-box me-2"></i> بحث المنتجات
                                            </a>
                                            <a class="dropdown-item" href="#" data-mode="clients">
                                                <i class="fas fa-users me-2"></i> بحث العملاء
                                            </a>
                                            <a class="dropdown-item" href="#" data-mode="invoices">
                                                <i class="fas fa-file-invoice me-2"></i> بحث الفواتير
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                
                                <input type="text" class="form-control search-input" id="searchInput" 
                                       placeholder="ابحث عن المنتجات..." autocomplete="off">
                            </div>
                        </div>

                        <!-- منطقة المحتوى الرئيسية -->
                        <div class="content-body">
                            
                            <!-- التصنيفات -->
                            <div id="categoriesSection" class="content-section">
                                <div class="categories-grid" id="categoriesGrid">
                                    @foreach ($categories as $category)
                                        <div class="category-card fade-in" data-category-id="{{ $category->id }}">
                                            @if($category->attachments)
                                                <img src="{{ asset($category->attachments) }}" 
                                                     alt="{{ $category->name }}" class="category-image">
                                            @else
                                                <div class="category-image d-flex align-items-center justify-content-center" 
                                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                    <i class="fas fa-tag fa-2x"></i>
                                                </div>
                                            @endif
                                            <h6 class="text-center mb-0 mt-2">{{ $category->name }}</h6>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <!-- المنتجات -->
                            <div id="productsSection" class="content-section" style="display: none;">
                                <div class="products-grid" id="productsGrid">
                                    <!-- سيتم ملؤها بـ JavaScript -->
                                </div>
                            </div>

                            <!-- العملاء -->
                            <div id="clientsSection" class="content-section" style="display: none;">
                                <div class="clients-grid" id="clientsGrid">
                                    <!-- سيتم ملؤها بـ JavaScript -->
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <!-- الجانب الأيمن - منطقة الطلبات -->
            <div class="right-card">
                <div class="card">
                    <div class="card-content">
                        
                        <!-- رأس منطقة الطلبات -->
                        <div class="order-header">
                            <div class="order-tabs" id="orderTabs">
                                <button class="order-tab active" data-order="1">#1</button>
                                <button class="order-tab" onclick="addNewOrder()">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            
                            <div class="client-selector" id="clientSelector" title="اختيار العميل (F3)">
                                <i class="fas fa-user"></i>
                                <span id="selectedClientName">اختر العميل</span>
                                <i class="fas fa-chevron-down ms-auto"></i>
                                <span class="keyboard-shortcut">F3</span>
                            </div>
                        </div>

                        <!-- محتوى الطلب -->
                        <div class="order-content" id="orderContent">
                            <div class="empty-state">
                                <i class="fas fa-shopping-cart"></i>
                                <p>لا توجد عناصر في الطلب</p>
                                <small>اختر المنتجات لإضافتها للطلب</small>
                            </div>
                        </div>

                        <!-- ملخص الطلب -->
                        <div class="order-summary">
                            <div class="summary-row">
                                <span>عدد العناصر:</span>
                                <span id="totalItems">0</span>
                            </div>
                            <div class="summary-row">
                                <span>المجموع الفرعي:</span>
                                <span id="subtotalAmount">0.00 ر.س</span>
                            </div>
                            
                            <div class="discount-section">
                                <label class="form-label fw-bold">
                                    <i class="fas fa-percent me-1"></i>الخصم:
                                </label>
                                <div class="discount-controls">
                                    <select class="discount-type-select" id="discountType">
                                        <option value="amount">مبلغ</option>
                                        <option value="percentage">نسبة %</option>
                                    </select>
                                    <input type="number" class="discount-input" id="discountValue" 
                                           value="0" min="0" step="0.01" placeholder="0">
                                </div>
                            </div>
                            
                            <div class="summary-row total">
                                <span>الإجمالي النهائي:</span>
                                <span id="finalTotal">0.00 ر.س</span>
                            </div>
                            
                            <div class="action-buttons">
                                <button class="action-btn btn-hold" id="holdOrderBtn" title="تعليق الطلب (F5)">
                                    <i class="fas fa-pause"></i>
                                    <span>تعليق</span>
                                    <span class="keyboard-shortcut">F5</span>
                                </button>
                                <button class="action-btn btn-pay" id="payOrderBtn" title="الدفع (F6)">
                                    <i class="fas fa-credit-card"></i>
                                    <span>دفع</span>
                                    <span class="keyboard-shortcut">F6</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal نافذة الدفع -->
    <div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="border-radius: var(--border-radius); border: none;">
                <div class="modal-header" style="background: var(--primary-color); color: white;">
                    <h5 class="modal-title" id="paymentModalLabel">
                        <i class="fas fa-credit-card me-2"></i>إتمام عملية الدفع
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" style="padding: 30px;">
                    <form id="paymentForm">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    المبلغ الإجمالي المستحق: <strong id="modalTotalAmount">0.00 ر.س</strong>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            @if(isset($paymentMethods) && count($paymentMethods) > 0)
                                @foreach($paymentMethods as $method)
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label fw-bold">{{ $method->name }}</label>
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-money-bill-wave"></i>
                                            </span>
                                            <input type="number" class="form-control payment-method-input" 
                                                   data-method-id="{{ $method->id }}" 
                                                   value="0" min="0" step="0.01" 
                                                   placeholder="0.00">
                                            <span class="input-group-text">ر.س</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        لا توجد طرق دفع متاحة حالياً. يرجى إضافة طرق الدفع من الإعدادات.
                                    </div>
                                </div>
                            @endif
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-md-6">
                                <div class="summary-card" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <h6>إجمالي المدفوع</h6>
                                    <h4 class="text-success" id="totalPaidAmount">0.00 ر.س</h4>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="summary-card" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                                    <h6>المبلغ المتبقي</h6>
                                    <h4 class="text-danger" id="remainingAmount">0.00 ر.س</h4>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer" style="border-top: 2px solid var(--secondary-color);">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-success" id="confirmPaymentBtn" style="background: var(--success-color);">
                        <i class="fas fa-check me-2"></i>تأكيد الدفع
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- BEGIN: Vendor JS-->
    <script src="{{asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/ui/prism.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/wNumb.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/extensions/nouislider.min.js')}}"></script>
    <script src="{{asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <!-- END: Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="{{asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{asset('app-assets/js/core/app.js')}}"></script>
    <script src="{{asset('app-assets/js/scripts/components.js')}}"></script>
    <!-- END: Theme JS-->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // بيانات التطبيق من Laravel
        const appData = {
            categories: @json($categories),
            products: @json($products),
            clients: @json($clients),
            paymentMethods: @json($paymentMethods ?? []),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            printUrlTemplate: "{{ route('POS.invoices.print', ['id' => 'INVOICE_ID']) }}"
        };

        // حالة التطبيق
        let currentView = 'categories';
        let currentOrder = 1;
        let orders = {
            1: {
                items: [],
                client: null,
                discount: { type: 'amount', value: 0 }
            }
        };
        let searchTimeout;
        let selectedCategory = null;

        // عناصر DOM
        const searchInput = document.getElementById('searchInput');
        const searchModeText = document.getElementById('searchModeText');
        const categoriesSection = document.getElementById('categoriesSection');
        const productsSection = document.getElementById('productsSection');
        const clientsSection = document.getElementById('clientsSection');
        const orderContent = document.getElementById('orderContent');
        const selectedClientName = document.getElementById('selectedClientName');

        // تهيئة التطبيق
        document.addEventListener('DOMContentLoaded', function() {
            initializeApp();
            setupEventListeners();
            updateDateTime();
            setInterval(updateDateTime, 60000); // تحديث كل دقيقة
        });

        function initializeApp() {
            // تحديد العميل الافتراضي إذا كان موجوداً
            const defaultClient = appData.clients.find(client => client.id === 1);
            if (defaultClient && orders[currentOrder]) {
                orders[currentOrder].client = defaultClient;
                selectedClientName.textContent = defaultClient.trade_name;
            }
            
            updateOrderDisplay();
        }

        function setupEventListeners() {
            // البحث
            searchInput.addEventListener('input', handleSearch);
            
            // أزرار البحث
            document.getElementById('categoriesBtn').addEventListener('click', () => switchView('categories'));
            
            // خيارات البحث
            document.querySelectorAll('[data-mode]').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const mode = e.currentTarget.dataset.mode;
                    switchSearchMode(mode);
                });
            });

            // التصنيفات
            document.addEventListener('click', (e) => {
                if (e.target.closest('.category-card')) {
                    const categoryCard = e.target.closest('.category-card');
                    const categoryId = categoryCard.dataset.categoryId;
                    selectCategory(categoryId);
                }
            });

            // المنتجات
            document.addEventListener('click', (e) => {
                if (e.target.closest('.product-card')) {
                    const productCard = e.target.closest('.product-card');
                    const productId = productCard.dataset.productId;
                    addProductToOrder(productId);
                }
            });

            // العملاء
            document.addEventListener('click', (e) => {
                if (e.target.closest('.client-card')) {
                    const clientCard = e.target.closest('.client-card');
                    const clientId = clientCard.dataset.clientId;
                    selectClient(clientId);
                }
            });

            // اختيار العميل
            document.getElementById('clientSelector').addEventListener('click', () => {
                switchSearchMode('clients');
            });

            // تبويبات الطلبات
            document.addEventListener('click', (e) => {
                if (e.target.closest('.order-tab') && e.target.closest('.order-tab').dataset.order) {
                    const orderNumber = parseInt(e.target.closest('.order-tab').dataset.order);
                    switchOrder(orderNumber);
                }
            });

            // الخصم
            document.getElementById('discountType').addEventListener('change', updateTotals);
            document.getElementById('discountValue').addEventListener('input', updateTotals);

            // أزرار العمليات
            document.getElementById('holdOrderBtn').addEventListener('click', holdOrder);
            document.getElementById('payOrderBtn').addEventListener('click', initiatePayment);

            // نافذة الدفع
            document.getElementById('confirmPaymentBtn').addEventListener('click', confirmPayment);
            document.addEventListener('input', (e) => {
                if (e.target.classList.contains('payment-method-input')) {
                    calculatePaymentSummary();
                }
            });

            // اختصارات لوحة المفاتيح
            setupKeyboardShortcuts();
        }

        // تحديث التاريخ والوقت
        function updateDateTime() {
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            document.getElementById('currentDateTime').textContent = 
                now.toLocaleDateString('ar-SA', options);
        }

        // تبديل العرض
        function switchView(view) {
            currentView = view;
            
            // إخفاء جميع الأقسام
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // عرض القسم المطلوب
            document.getElementById(view + 'Section').style.display = 'block';
            
            // تحديث الأزرار
            document.querySelectorAll('.btn-search').forEach(btn => {
                btn.classList.remove('active');
            });
            
            if (view === 'categories') {
                document.getElementById('categoriesBtn').classList.add('active');
                searchInput.placeholder = 'البحث في التصنيفات...';
            }
        }

        // تبديل وضع البحث
        function switchSearchMode(mode) {
            currentView = mode;
            
            const modeTexts = {
                products: 'المنتجات',
                clients: 'العملاء',
                invoices: 'الفواتير'
            };
            
            searchModeText.textContent = modeTexts[mode];
            
            const placeholders = {
                products: 'ابحث عن المنتجات...',
                clients: 'ابحث عن العملاء...',
                invoices: 'ابحث عن الفواتير...'
            };
            
            searchInput.placeholder = placeholders[mode];
            
            // تبديل العرض
            switchView(mode);
            
            // التركيز على حقل البحث
            searchInput.focus();
        }

        // البحث
        function handleSearch(e) {
            clearTimeout(searchTimeout);
            const query = e.target.value.trim();
            
            searchTimeout = setTimeout(() => {
                performSearch(query);
            }, 300);
        }

        function performSearch(query) {
            if (!query) {
                // عرض البيانات الافتراضية
                if (currentView === 'categories') {
                    loadCategories();
                } else if (currentView === 'products') {
                    loadProducts();
                } else if (currentView === 'clients') {
                    loadClients();
                }
                return;
            }

            // عرض مؤشر التحميل
            showLoading();

            // إرسال طلب البحث
            fetch(`/POS/search?query=${encodeURIComponent(query)}&type=${currentView}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displaySearchResults(data);
                } else {
                    showError('حدث خطأ أثناء البحث');
                }
            })
            .catch(error => {
                console.error('خطأ في البحث:', error);
                showError('حدث خطأ أثناء البحث');
            });
        }

        // عرض نتائج البحث
        function displaySearchResults(data) {
            if (currentView === 'products') {
                displayProducts(data.products || []);
            } else if (currentView === 'clients') {
                displayClients(data.clients || []);
            }
        }

        // تحميل التصنيفات
        function loadCategories() {
            const grid = document.getElementById('categoriesGrid');
            if (appData.categories.length === 0) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-folder-open"></i><p>لا توجد تصنيفات</p></div>';
                return;
            }
            // التصنيفات محملة بالفعل في HTML
        }

        // اختيار تصنيف
        function selectCategory(categoryId) {
            selectedCategory = categoryId;
            
            // تحديث حالة التصنيفات
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('active');
            });
            document.querySelector(`[data-category-id="${categoryId}"]`).classList.add('active');
            
            // تحميل منتجات التصنيف
            loadProductsByCategory(categoryId);
            switchView('products');
        }

        // تحميل المنتجات بناءً على التصنيف
        function loadProductsByCategory(categoryId) {
            showLoading();
            
            fetch(`/POS/products-by-category?category_id=${categoryId}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': appData.csrfToken
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    displayProducts(data.products);
                } else {
                    showError('حدث خطأ أثناء تحميل المنتجات');
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل المنتجات:', error);
                showError('حدث خطأ أثناء تحميل المنتجات');
            });
        }

        // تحميل جميع المنتجات
        function loadProducts() {
            displayProducts(appData.products);
        }

        // عرض المنتجات
        function displayProducts(products) {
            const grid = document.getElementById('productsGrid');
            
            if (products.length === 0) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-box-open"></i><p>لا توجد منتجات</p></div>';
                return;
            }
            
            const productsHtml = products.map(product => `
                <div class="product-card fade-in" data-product-id="${product.id}">
                    <img src="${product.images || '/assets/images/default.png'}" 
                         alt="${product.name}" class="product-image">
                    <div class="product-name">${product.name}</div>
                    <div class="product-info">
                        <span class="product-price">${parseFloat(product.sale_price).toFixed(2)} ر.س</span>
                        <button class="info-btn" title="تفاصيل المنتج">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            
            grid.innerHTML = productsHtml;
        }

        // تحميل العملاء
        function loadClients() {
            displayClients(appData.clients);
        }

        // عرض العملاء
        function displayClients(clients) {
            const grid = document.getElementById('clientsGrid');
            
            if (clients.length === 0) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><p>لا توجد عملاء</p></div>';
                return;
            }
            
            const clientsHtml = clients.map(client => `
                <div class="client-card fade-in" data-client-id="${client.id}">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas fa-user-circle fa-3x text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${client.trade_name}</h6>
                            <small class="text-muted">
                                <i class="fas fa-phone me-1"></i>${client.phone}
                            </small>
                        </div>
                    </div>
                </div>
            `).join('');
            
            // إضافة CSS للعملاء
            const clientsCSS = `
                <style>
                .clients-grid {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                }
                .client-card {
                    background: white;
                    border: 2px solid #e9ecef;
                    border-radius: var(--border-radius);
                    padding: 15px;
                    cursor: pointer;
                    transition: var(--transition);
                }
                .client-card:hover {
                    border-color: var(--secondary-color);
                    transform: translateY(-2px);
                    box-shadow: var(--card-shadow);
                }
                .client-card.selected {
                    border-color: var(--secondary-color);
                    background: #f8f9fa;
                }
                </style>
            `;
            
            if (!document.getElementById('clientsCSS')) {
                const style = document.createElement('div');
                style.id = 'clientsCSS';
                style.innerHTML = clientsCSS;
                document.head.appendChild(style);
            }
            
            grid.innerHTML = clientsHtml;
        }

        // إضافة منتج للطلب
        function addProductToOrder(productId) {
            const product = appData.products.find(p => p.id == productId);
            if (!product) return;
            
            const order = orders[currentOrder];
            const existingItem = order.items.find(item => item.id == productId);
            
            if (existingItem) {
                existingItem.quantity += 1;
                existingItem.total = existingItem.quantity * existingItem.price;
            } else {
                order.items.push({
                    id: productId,
                    name: product.name,
                    price: parseFloat(product.sale_price),
                    quantity: 1,
                    total: parseFloat(product.sale_price)
                });
            }
            
            updateOrderDisplay();
            showNotification('تم إضافة المنتج للطلب', 'success');
        }

        // اختيار عميل
        function selectClient(clientId) {
            const client = appData.clients.find(c => c.id == clientId);
            if (!client) return;
            
            orders[currentOrder].client = client;
            selectedClientName.textContent = client.trade_name;
            
            // تمييز العميل المختار
            document.querySelectorAll('.client-card').forEach(card => {
                card.classList.remove('selected');
            });
            document.querySelector(`[data-client-id="${clientId}"]`).classList.add('selected');
            
            showNotification(`تم اختيار العميل: ${client.trade_name}`, 'success');
        }

        // تحديث عرض الطلب
        function updateOrderDisplay() {
            const order = orders[currentOrder];
            
            if (!order.items.length) {
                orderContent.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-shopping-cart"></i>
                        <p>لا توجد عناصر في الطلب</p>
                        <small>اختر المنتجات لإضافتها للطلب</small>
                    </div>
                `;
            } else {
                const itemsHtml = order.items.map(item => `
                    <div class="order-item slide-in">
                        <div class="item-details">
                            <div class="item-name">${item.name}</div>
                            <div class="item-price">${item.price.toFixed(2)} ر.س للوحدة</div>
                        </div>
                        <div class="item-controls">
                            <div class="quantity-controls">
                                <button class="qty-btn" onclick="updateQuantity(${item.id}, -1)">
                                    <i class="fas fa-minus"></i>
                                </button>
                                <span class="qty-display">${item.quantity}</span>
                                <button class="qty-btn" onclick="updateQuantity(${item.id}, 1)">
                                    <i class="fas fa-plus"></i>
                                </button>
                            </div>
                            <div class="item-total">${item.total.toFixed(2)} ر.س</div>
                            <button class="delete-btn" onclick="removeFromOrder(${item.id})" title="حذف العنصر">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
                
                orderContent.innerHTML = itemsHtml;
            }
            
            updateTotals();
        }

        // تحديث الكمية
        function updateQuantity(itemId, change) {
            const order = orders[currentOrder];
            const item = order.items.find(i => i.id == itemId);
            
            if (!item) return;
            
            item.quantity = Math.max(1, item.quantity + change);
            item.total = item.quantity * item.price;
            
            updateOrderDisplay();
        }

        // حذف من الطلب
        function removeFromOrder(itemId) {
            const order = orders[currentOrder];
            order.items = order.items.filter(item => item.id != itemId);
            
            updateOrderDisplay();
            showNotification('تم حذف العنصر من الطلب', 'warning');
        }

        // تحديث الإجماليات
        function updateTotals() {
            const order = orders[currentOrder];
            
            const totalItems = order.items.reduce((sum, item) => sum + item.quantity, 0);
            const subtotal = order.items.reduce((sum, item) => sum + item.total, 0);
            
            // حساب الخصم
            const discountType = document.getElementById('discountType').value;
            const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
            
            let discountAmount = 0;
            if (discountValue > 0) {
                if (discountType === 'percentage') {
                    discountAmount = subtotal * (discountValue / 100);
                } else {
                    discountAmount = discountValue;
                }
            }
            
            const finalTotal = Math.max(0, subtotal - discountAmount);
            
            // حفظ بيانات الخصم في الطلب
            order.discount = { type: discountType, value: discountValue };
            
            // تحديث العرض
            document.getElementById('totalItems').textContent = totalItems;
            document.getElementById('subtotalAmount').textContent = subtotal.toFixed(2) + ' ر.س';
            document.getElementById('finalTotal').textContent = finalTotal.toFixed(2) + ' ر.س';
        }

        // إضافة طلب جديد
        function addNewOrder() {
            const newOrderNumber = Object.keys(orders).length + 1;
            
            orders[newOrderNumber] = {
                items: [],
                client: null,
                discount: { type: 'amount', value: 0 }
            };
            
            // إضافة تبويب جديد
            const newTab = document.createElement('button');
            newTab.className = 'order-tab';
            newTab.textContent = `#${newOrderNumber}`;
            newTab.dataset.order = newOrderNumber;
            newTab.onclick = () => switchOrder(newOrderNumber);
            
            const addBtn = document.querySelector('.order-tab:last-child');
            addBtn.parentNode.insertBefore(newTab, addBtn);
            
            switchOrder(newOrderNumber);
        }

        // تبديل الطلب
        function switchOrder(orderNumber) {
            currentOrder = orderNumber;
            
            // تحديث التبويبات
            document.querySelectorAll('.order-tab').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelector(`[data-order="${orderNumber}"]`).classList.add('active');
            
            // تحديث العميل
            const order = orders[orderNumber];
            if (order.client) {
                selectedClientName.textContent = order.client.trade_name;
            } else {
                selectedClientName.textContent = 'اختر العميل';
            }
            
            // تحديث الخصم
            document.getElementById('discountType').value = order.discount.type;
            document.getElementById('discountValue').value = order.discount.value;
            
            updateOrderDisplay();
        }

        // تعليق الطلب
        function holdOrder() {
            const order = orders[currentOrder];
            
            if (!order.items.length) {
                showNotification('لا يمكن تعليق طلب فارغ', 'error');
                return;
            }
            
            Swal.fire({
                title: 'تعليق الطلب',
                text: 'هل تريد تعليق هذا الطلب؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، علق الطلب',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#ffc107'
            }).then((result) => {
                if (result.isConfirmed) {
                    // حفظ الطلب المعلق (يمكن حفظه في localStorage أو قاعدة البيانات)
                    const heldOrders = JSON.parse(localStorage.getItem('heldOrders') || '[]');
                    heldOrders.push({
                        ...order,
                        orderNumber: currentOrder,
                        heldAt: new Date().toISOString()
                    });
                    localStorage.setItem('heldOrders', JSON.stringify(heldOrders));
                    
                    showNotification('تم تعليق الطلب بنجاح', 'success');
                    addNewOrder();
                }
            });
        }

        // بدء عملية الدفع
        function initiatePayment() {
            const order = orders[currentOrder];
            
            if (!order.items.length) {
                showNotification('لا يمكن إتمام دفع طلب فارغ', 'error');
                return;
            }
            
            const finalTotal = parseFloat(document.getElementById('finalTotal').textContent.replace(' ر.س', ''));
            document.getElementById('modalTotalAmount').textContent = finalTotal.toFixed(2) + ' ر.س';
            
            // تعيين القيمة الافتراضية لطريقة الدفع الأولى
            const firstPaymentInput = document.querySelector('.payment-method-input');
            if (firstPaymentInput) {
                firstPaymentInput.value = finalTotal.toFixed(2);
                calculatePaymentSummary();
            }
            
            $('#paymentModal').modal('show');
        }

        // حساب ملخص الدفع
        function calculatePaymentSummary() {
            const totalAmount = parseFloat(document.getElementById('modalTotalAmount').textContent.replace(' ر.س', ''));
            let totalPaid = 0;
            
            document.querySelectorAll('.payment-method-input').forEach(input => {
                totalPaid += parseFloat(input.value) || 0;
            });
            
            const remaining = totalAmount - totalPaid;
            
            document.getElementById('totalPaidAmount').textContent = totalPaid.toFixed(2) + ' ر.س';
            document.getElementById('remainingAmount').textContent = remaining.toFixed(2) + ' ر.س';
            
            // تلوين المبلغ المتبقي
            const remainingElement = document.getElementById('remainingAmount');
            if (remaining > 0) {
                remainingElement.className = 'text-danger';
            } else if (remaining < 0) {
                remainingElement.className = 'text-warning';
            } else {
                remainingElement.className = 'text-success';
            }
        }

        // تأكيد الدفع
        function confirmPayment() {
            const totalAmount = parseFloat(document.getElementById('modalTotalAmount').textContent.replace(' ر.س', ''));
            const totalPaid = parseFloat(document.getElementById('totalPaidAmount').textContent.replace(' ر.س', ''));
            const remaining = totalAmount - totalPaid;
            
            if (remaining > 0.01) {
                Swal.fire({
                    title: 'المبلغ غير مكتمل',
                    text: 'يرجى دفع كامل المبلغ المستحق',
                    icon: 'warning',
                    confirmButtonText: 'حسناً'
                });
                return;
            }
            
            // جمع بيانات الدفع
            const payments = [];
            document.querySelectorAll('.payment-method-input').forEach(input => {
                const amount = parseFloat(input.value);
                if (amount > 0) {
                    payments.push({
                        method_id: input.dataset.methodId,
                        amount: amount
                    });
                }
            });
            
            const order = orders[currentOrder];
            const invoiceData = {
                client_id: order.client?.id || null,
                client_name: order.client?.trade_name || 'عميل نقدي',
                products: order.items.map(item => ({
                    id: item.id,
                    name: item.name,
                    unit_price: item.price,
                    quantity: item.quantity,
                    total: item.total
                })),
                discount_type: order.discount.type,
                discount_value: order.discount.value,
                total: order.items.reduce((sum, item) => sum + item.total, 0),
                net_total: totalAmount,
                payments: payments
            };
            
            // إرسال البيانات للخادم
            saveInvoice(invoiceData);
        }

        // حفظ الفاتورة
        function saveInvoice(invoiceData) {
            // عرض مؤشر التحميل
            Swal.fire({
                title: 'جاري معالجة الطلب...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            fetch('/POS/sales-start/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': appData.csrfToken
                },
                body: JSON.stringify(invoiceData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    $('#paymentModal').modal('hide');
                    
                    Swal.fire({
                        title: 'تم إتمام العملية بنجاح!',
                        text: `رقم الفاتورة: ${data.invoice_id}`,
                        icon: 'success',
                        showCancelButton: true,
                        confirmButtonText: 'طباعة الفاتورة',
                        cancelButtonText: 'إغلاق'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // فتح صفحة الطباعة
                            const printUrl = appData.printUrlTemplate.replace('INVOICE_ID', data.invoice_id);
                            window.open(printUrl, '_blank');
                        }
                    });
                    
                    // إعادة تعيين الطلب
                    resetCurrentOrder();
                    
                } else {
                    Swal.fire({
                        title: 'خطأ!',
                        text: data.message || 'حدث خطأ أثناء معالجة الطلب',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                }
            })
            .catch(error => {
                console.error('خطأ في حفظ الفاتورة:', error);
                Swal.fire({
                    title: 'خطأ في الشبكة!',
                    text: 'تعذر الاتصال بالخادم',
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
            });
        }

        // إعادة تعيين الطلب الحالي
        function resetCurrentOrder() {
            orders[currentOrder] = {
                items: [],
                client: null,
                discount: { type: 'amount', value: 0 }
            };
            
            selectedClientName.textContent = 'اختر العميل';
            document.getElementById('discountType').value = 'amount';
            document.getElementById('discountValue').value = 0;
            
            updateOrderDisplay();
        }

        // عرض مؤشر التحميل
        function showLoading() {
            const currentSection = document.querySelector('.content-section[style*="block"]') || 
                                  document.getElementById('categoriesSection');
            
            if (currentSection) {
                currentSection.innerHTML = `
                    <div class="loading-spinner">
                        <div class="spinner"></div>
                    </div>
                `;
            }
        }

        // عرض الإشعارات
        function showNotification(message, type = 'success') {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer);
                    toast.addEventListener('mouseleave', Swal.resumeTimer);
                }
            });

            const icons = {
                success: 'success',
                error: 'error',
                warning: 'warning',
                info: 'info'
            };

            Toast.fire({
                icon: icons[type] || 'info',
                title: message
            });
        }

        // عرض الأخطاء
        function showError(message) {
            showNotification(message, 'error');
        }

        // إعداد اختصارات لوحة المفاتيح
        function setupKeyboardShortcuts() {
            document.addEventListener('keydown', (e) => {
                // تجاهل الاختصارات عند الكتابة في حقول الإدخال
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') {
                    return;
                }
                
                switch(e.key) {
                    case 'F1':
                        e.preventDefault();
                        switchView('categories');
                        break;
                    case 'F2':
                        e.preventDefault();
                        switchSearchMode('products');
                        break;
                    case 'F3':
                        e.preventDefault();
                        switchSearchMode('clients');
                        break;
                    case 'F4':
                        e.preventDefault();
                        // وظيفة الطباعة
                        break;
                    case 'F5':
                        e.preventDefault();
                        holdOrder();
                        break;
                    case 'F6':
                        e.preventDefault();
                        initiatePayment();
                        break;
                    case 'F11':
                        e.preventDefault();
                        toggleFullscreen();
                        break;
                    case 'F12':
                        e.preventDefault();
                        window.open('/dashboard', '_blank');
                        break;
                    case 'Escape':
                        if (searchInput.value) {
                            searchInput.value = '';
                            performSearch('');
                        }
                        break;
                }
                
                // اختصارات بـ Ctrl
                if (e.ctrlKey) {
                    switch(e.key) {
                        case 'f':
                        case 'F':
                            e.preventDefault();
                            searchInput.focus();
                            break;
                        case 'n':
                        case 'N':
                            e.preventDefault();
                            addNewOrder();
                            break;
                    }
                }
            });
        }

        // تبديل الشاشة الكاملة
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen();
            } else {
                document.exitFullscreen();
            }
        }

        // تحسين تجربة اللمس
        if ('ontouchstart' in window) {
            // إضافة دعم السحب للتمرير في التبويبات
            let isDown = false;
            let startX;
            let scrollLeft;

            const orderTabs = document.getElementById('orderTabs');
            
            orderTabs.addEventListener('mousedown', (e) => {
                isDown = true;
                startX = e.pageX - orderTabs.offsetLeft;
                scrollLeft = orderTabs.scrollLeft;
            });

            orderTabs.addEventListener('mouseleave', () => {
                isDown = false;
            });

            orderTabs.addEventListener('mouseup', () => {
                isDown = false;
            });

            orderTabs.addEventListener('mousemove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.pageX - orderTabs.offsetLeft;
                const walk = (x - startX) * 2;
                orderTabs.scrollLeft = scrollLeft - walk;
            });
        }

        // تحسين الأداء - تأخير تحديث البحث
        const debounce = (func, wait) => {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        };
        
        // تطبيق التأخير على البحث
        const debouncedSearch = debounce(performSearch, 300);
    </script>
</body>
</html>