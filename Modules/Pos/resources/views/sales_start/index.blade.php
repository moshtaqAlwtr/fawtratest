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
            padding: 5px;
        }

        .content-wrapper {
            display: flex;
            height: 100%;
            gap: 10px;
        }

        /* الجانب الأيسر - المنتجات */
        .left-card {
            flex: 2.5;
            min-width: 0;
        }

        /* الجانب الأيمن - الطلبات */
        .right-card {
            flex: 1;
            min-width: 320px;
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
            padding: 10px 15px;
            box-shadow: 0 2px 10px rgba(55, 63, 106, 0.3);
            min-height: auto;
        }

        .navbar-wrapper {
            width: 100%;
        }

        .navbar-container {
            width: 100%;
            padding: 0;
        }

        .navbar-collapse {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
            width: 100%;
        }

        .navbar-nav {
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
        }

        .nav-item {
            margin: 0 3px;
        }

        .nav-item .nav-link {
            color: white !important;
            padding: 8px 12px;
            border-radius: 8px;
            transition: var(--transition);
            background: rgba(255,255,255,0.1);
            position: relative;
            font-size: 0.9rem;
        }

        .nav-item .nav-link:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-1px);
        }

        .user-nav {
            text-align: right;
        }

        .user-name {
            font-size: 0.85rem;
            line-height: 1.2;
        }

        .user-status span {
            font-size: 0.75rem;
        }

        /* منطقة البحث والأزرار المحسنة */
        .search-section {
            background: #f8f9fa;
            padding: 15px;
            border-bottom: 1px solid #e9ecef;
        }

        .search-row {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .search-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-search {
            background: var(--secondary-color);
            border: none;
            color: white;
            padding: 10px 14px;
            border-radius: 8px;
            transition: var(--transition);
            font-weight: 500;
            min-height: 44px;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-search:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
        }

        .btn-search.active {
            background: var(--primary-color);
            box-shadow: 0 4px 12px rgba(55, 63, 106, 0.3);
        }

        .dropdown-menu {
            border: none;
            box-shadow: var(--card-shadow);
            border-radius: var(--border-radius);
            padding: 8px;
        }

        .dropdown-item {
            border-radius: 8px;
            transition: var(--transition);
            padding: 10px 12px;
            margin-bottom: 2px;
        }

        .dropdown-item:hover {
            background: var(--light-bg);
            transform: translateX(-3px);
        }

        .search-input {
            flex: 1;
            min-width: 200px;
            padding: 10px 15px;
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
            padding: 15px;
            overflow: auto;
            background: #fafbfc;
        }

        /* التصنيفات المحسنة */
        .categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 12px;
            margin-bottom: 15px;
        }
.btn-return {
    background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
    color: white;
}

.btn-return:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
}
        .category-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 15px;
            text-align: center;
            cursor: pointer;
            transition: var(--transition);
            min-height: 120px;
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
            transform: translateY(-3px);
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
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-bottom: 8px;
            border: 2px solid #e9ecef;
            transition: var(--transition);
        }

        .category-card:hover .category-image {
            border-color: var(--secondary-color);
        }

        .category-card.active .category-image {
            border-color: white;
        }

        /* المنتجات المحسنة */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 12px;
        }

        .product-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 12px;
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
            height: 3px;
            background: var(--success-color);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .product-card:hover {
            border-color: var(--success-color);
            transform: translateY(-3px);
            box-shadow: var(--card-shadow);
        }

        .product-card:hover::before {
            transform: scaleX(1);
        }

        .product-image {
            width: 100%;
            height: 110px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
        }

        .product-name {
            font-weight: 600;
            font-size: 0.85rem;
            margin-bottom: 6px;
            color: #333;
            line-height: 1.3;
            height: 2.4em;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-price {
            color: var(--success-color);
            font-weight: 700;
            font-size: 1rem;
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 8px;
        }

        .info-btn {
            background: var(--light-bg);
            border: none;
            border-radius: 50%;
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            font-size: 0.8rem;
        }

        .info-btn:hover {
            background: var(--primary-color);
            color: white;
        }

        /* العملاء */
        .clients-container {
            display: flex;
            flex-direction: column;
            gap: 12px;
            width: 100%;
        }

        .client-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 15px;
            cursor: pointer;
            transition: var(--transition);
            width: 100%;
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

        /* الجانب الأيمن - منطقة الطلبات المحسنة */
        .order-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, #4a5296 100%);
            color: white;
            padding: 15px;
        }

        .order-tabs {
            display: flex;
            gap: 6px;
            margin-bottom: 12px;
            overflow-x: auto;
            padding-bottom: 3px;
        }

        .order-tab {
            background: rgba(255,255,255,0.15);
            border: none;
            color: white;
            padding: 8px 12px;
            border-radius: 18px;
            white-space: nowrap;
            cursor: pointer;
            transition: var(--transition);
            font-weight: 500;
            min-width: 44px;
            backdrop-filter: blur(10px);
            font-size: 0.9rem;
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
            padding: 10px 12px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            position: relative;
        }

        .client-selector:hover {
            background: rgba(255,255,255,0.2);
        }

        /* منطقة الطلبات */
        .order-content {
            flex: 1;
            background: var(--light-bg);
            overflow-y: auto;
            padding: 12px;
        }

        .order-item {
            background: white;
            border-radius: var(--border-radius);
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 12px;
            transition: var(--transition);
            border: 2px solid transparent;
        }

        .order-item:hover {
            border-color: var(--secondary-color);
            transform: translateX(-2px);
            box-shadow: var(--card-shadow);
        }

        .item-details {
            flex: 1;
            min-width: 0;
        }

        .item-name {
            font-weight: 600;
            margin-bottom: 4px;
            color: #333;
            font-size: 0.9rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-price {
            color: #666;
            font-size: 0.8rem;
        }

        .item-controls {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .quantity-controls {
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 20px;
            padding: 2px;
        }

        .qty-btn {
            width: 28px;
            height: 28px;
            border: none;
            background: var(--secondary-color);
            color: white;
            border-radius: 50%;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .qty-btn:hover {
            background: #45a049;
            transform: scale(1.1);
        }

        .qty-display {
            min-width: 35px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
        }

        .item-total {
            font-weight: 700;
            color: var(--success-color);
            font-size: 0.95rem;
            min-width: 60px;
            text-align: right;
        }

        .delete-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 50%;
            width: 32px;
            height: 32px;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
        }

        .delete-btn:hover {
            background: #c82333;
            transform: scale(1.1);
        }

        /* ملخص الطلب المحسن */
        .order-summary {
            background: white;
            padding: 15px;
            border-top: 3px solid var(--secondary-color);
        }

        .summary-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }

        .summary-row:last-child {
            border-bottom: none;
        }

        .summary-row.total {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--success-color);
            border-top: 2px solid var(--secondary-color);
            padding-top: 12px;
            margin-top: 8px;
        }

        .discount-section {
            background: #f8f9fa;
            border-radius: var(--border-radius);
            padding: 12px;
            margin: 12px 0;
            border: 2px solid #e9ecef;
        }

        .discount-controls {
            display: flex;
            gap: 8px;
            align-items: center;
            margin-top: 8px;
        }

        .discount-type-select {
            flex: none;
            width: 80px;
            padding: 6px 8px;
            border: 1px solid #ddd;
            border-radius: 6px;
            background: white;
            font-size: 0.85rem;
        }

        .discount-input {
            flex: 1;
            padding: 6px 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            text-align: center;
            font-size: 0.9rem;
        }

        /* أزرار العمليات المحسنة */
        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .action-btn {
            flex: 1;
            padding: 12px 15px;
            border: none;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            min-height: 44px;
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
            padding: 30px 15px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 2.5rem;
            margin-bottom: 12px;
            opacity: 0.5;
        }

        .empty-state p {
            font-size: 1rem;
            margin-bottom: 5px;
        }

        .empty-state small {
            font-size: 0.85rem;
        }

        /* التصميم المتجاوب المحسن */
        @media (max-width: 1400px) {
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(100px, 1fr));
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }

        @media (max-width: 1200px) {
            .left-card {
                flex: 2;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(90px, 1fr));
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(130px, 1fr));
            }
        }

        @media (max-width: 992px) {
            .content-wrapper {
                flex-direction: column;
                gap: 8px;
            }
            
            .left-card {
                flex: none;
                height: 55vh;
            }
            
            .right-card {
                flex: none;
                height: 43vh;
                min-width: auto;
            }

            .search-row {
                flex-direction: column;
                gap: 10px;
                align-items: stretch;
            }
            
            .search-buttons {
                width: 100%;
                justify-content: space-around;
            }
            
            .search-input {
                min-width: auto;
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .app-content {
                padding: 3px;
            }
            
            .content-wrapper {
                gap: 5px;
            }

            .header-navbar {
                padding: 8px 12px;
            }
            
            .nav-item .nav-link {
                padding: 6px 10px;
                font-size: 0.8rem;
            }
            
            .user-name {
                font-size: 0.8rem;
            }
            
            .user-status span {
                font-size: 0.7rem;
            }
            
            .search-section {
                padding: 12px;
            }
            
            .btn-search {
                padding: 8px 10px;
                font-size: 0.8rem;
            }
            
            .btn-search span {
                display: none;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
                gap: 8px;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
                gap: 8px;
            }
            
            .category-card, .product-card {
                min-height: 100px;
                padding: 10px;
            }
            
            .category-image {
                width: 40px;
                height: 40px;
            }
            
            .product-image {
                height: 80px;
            }
            
            .product-name {
                font-size: 0.8rem;
            }
            
            .product-price {
                font-size: 0.9rem;
            }
            
            .order-header {
                padding: 12px;
            }
            
            .order-tabs {
                justify-content: flex-start;
            }
            
            .order-content {
                padding: 10px;
            }
            
            .order-item {
                padding: 10px;
                gap: 8px;
            }
            
            .item-name {
                font-size: 0.85rem;
            }
            
            .item-total {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                gap: 8px;
            }
            
            .action-btn {
                padding: 10px 12px;
                font-size: 0.9rem;
            }
        }

        /* تحسينات اللمس */
        @media (pointer: coarse) {
            .category-card, .product-card, .order-tab, .qty-btn, .delete-btn, .action-btn, .client-card {
                min-height: 44px;
                min-width: 44px;
            }
            
            .search-input {
                font-size: 16px; /* منع التكبير في iOS */
                padding: 12px 15px;
            }
            
            .btn-search {
                padding: 12px 15px;
            }
            
            .info-btn {
                width: 32px;
                height: 32px;
            }
        }

        /* رسوم متحركة */
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }

        @keyframes fadeIn {
            from { 
                opacity: 0; 
                transform: translateY(15px); 
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
                transform: translateX(-15px); 
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
            padding: 30px;
        }

        .spinner {
            width: 35px;
            height: 35px;
            border: 3px solid #e9ecef;
            border-top: 3px solid var(--secondary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* تحسين Scrollbar */
        .order-content::-webkit-scrollbar,
        .content-body::-webkit-scrollbar,
        .order-tabs::-webkit-scrollbar {
            width: 4px;
            height: 4px;
        }

        .order-content::-webkit-scrollbar-track,
        .content-body::-webkit-scrollbar-track,
        .order-tabs::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .order-content::-webkit-scrollbar-thumb,
        .content-body::-webkit-scrollbar-thumb,
        .order-tabs::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }

        .order-content::-webkit-scrollbar-thumb:hover,
        .content-body::-webkit-scrollbar-thumb:hover,
        .order-tabs::-webkit-scrollbar-thumb:hover {
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
            top: 3px;
            right: 3px;
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 0.65rem;
            font-weight: 500;
        }

        /* تحسينات إضافية للجوال */
        @media (max-width: 480px) {
            .keyboard-shortcut {
                display: none;
            }
            
            .search-buttons {
                flex-direction: column;
                width: 100%;
            }
            
            .btn-search {
                width: 100%;
                justify-content: center;
            }
            
            .categories-grid {
                grid-template-columns: repeat(auto-fill, minmax(70px, 1fr));
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(95px, 1fr));
            }
            
            .product-name {
                font-size: 0.75rem;
                height: auto;
                line-height: 1.2;
            }
            
            .product-price {
                font-size: 0.85rem;
            }
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
                                        <ul class="nav navbar-nav bookmark-icons">
                                            <!--<li class="nav-item">-->
                                            <!--    <a class="nav-link" href="#" title="الطباعة (F4)">-->
                                            <!--        <i class="fas fa-print"></i>-->
                                            <!--        <span class="keyboard-shortcut">F4</span>-->
                                            <!--    </a>-->
                                            <!--</li>-->
                                            <li class="nav-item">
                                                <a class="nav-link" href="#" title="عرض كامل (F11)" onclick="toggleFullscreen()">
                                                    <i class="fas fa-expand"></i>
                                                    <span class="keyboard-shortcut">F11</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a href="{{ url('/') }}" target="_blank" 
                                                   class="nav-link" title="الصفحة الرئيسية (F12)">
                                                    <i class="fas fa-home"></i>
                                                    <span class="keyboard-shortcut">F12</span>
                                                </a>
                                            </li>
                                        </ul>

                                        <ul class="nav navbar-nav">
                                            <li class="dropdown dropdown-user nav-item">
                                                <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown">
                                                    <div class="user-nav d-sm-flex d-none">
                                                        <span class="user-name text-bold-600 text-white">
                                                            {{ auth()->user()->name ?? 'المستخدم' }}
                                                        </span>
                                                        <span class="user-status">
                                                            <span class="text-white" id="currentDateTime"></span>
                                                        </span>
                                                    </div>
                                                    <span>
                                                        <img class="round" src="{{asset('app-assets/images/portrait/small/avatar-s-13.jpg')}}" 
                                                             alt="avatar" height="35" width="35">
                                                    </span>
                                                </a>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="#"><i class="feather icon-user"></i> الملف الشخصي</a>
                                                    <a class="dropdown-item" href="#"><i class="feather icon-settings"></i> الإعدادات</a>
                                                    <div class="dropdown-divider"></div>
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
                                    <button class="btn btn-search active" id="categoriesBtn" title="التصنيفات (F1)">
                                        <i class="fas fa-th-large"></i>
                                        <span class="d-none d-md-inline">التصنيفات</span>
                                        <span class="keyboard-shortcut">F1</span>
                                    </button>
                                    
                                    <button class="btn btn-search" id="productsBtn" title="المنتجات (F2)">
                                        <i class="fas fa-box"></i>
                                        <span class="d-none d-md-inline">المنتجات</span>
                                        <span class="keyboard-shortcut">F2</span>
                                    </button>
                                    
                                    <button class="btn btn-search" id="clientsBtn" title="العملاء (F3)">
                                        <i class="fas fa-users"></i>
                                        <span class="d-none d-md-inline">العملاء</span>
                                        <span class="keyboard-shortcut">F3</span>
                                    </button>
                                    
                                    <button class="btn btn-search" id="invoicesBtn" title="الفواتير (F4)">
                                        <i class="fas fa-file-invoice"></i>
                                        <span class="d-none d-md-inline">الفواتير</span>
                                        <span class="keyboard-shortcut">F4</span>
                                    </button>
                                </div>
                                
                                <input type="text" class="form-control search-input" id="searchInput" 
                                       placeholder="ابحث هنا..." autocomplete="off">
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
                                                     alt="{{ $category->name }}" class="category-image"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="category-image d-none align-items-center justify-content-center" 
                                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                    <i class="fas fa-tag"></i>
                                                </div>
                                            @else
                                                <div class="category-image d-flex align-items-center justify-content-center" 
                                                     style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                                                    <i class="fas fa-tag"></i>
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
                                <div id="clientsContainer">
                                    <!-- سيتم ملؤها بـ JavaScript -->
                                </div>
                            </div>

                            <!-- الفواتير -->
                            <div id="invoicesSection" class="content-section" style="display: none;">
                                <div id="invoicesContainer">
                                     سيتم ملؤها بـ JavaScript 
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
                            
                            <div class="client-selector" id="clientSelector" title="اختيار العميل">
                                <i class="fas fa-user"></i>
                                <span id="selectedClientName">اختر العميل</span>
                                <i class="fas fa-chevron-down ms-auto"></i>
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
                                <label class="form-label fw-bold" style="font-size: 0.9rem;">
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
    
    <!-- زر الاسترداد الجديد -->
    <button class="action-btn btn-return" id="returnBtn" title="استرداد (F7)">
        <i class="fas fa-undo"></i>
        <span>استرداد</span>
        <span class="keyboard-shortcut">F7</span>
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

<!-- Modal نافذة الاسترداد -->
<div class="modal fade" id="returnModal" tabindex="-1" role="dialog" aria-labelledby="returnModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
        <div class="modal-content" style="border-radius: var(--border-radius); border: none;">
            <div class="modal-header" style="background: #dc3545; color: white;">
                <h5 class="modal-title" id="returnModalLabel">
                    <i class="fas fa-undo me-2"></i>استرداد المنتجات
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" style="padding: 20px;">
                <!-- قائمة المنتجات للاسترداد -->
                <div class="row">
                    <div class="col-md-8">
                        <h6>المنتجات المتاحة للاسترداد:</h6>
                        <div id="returnItemsList" class="mb-3">
                            <!-- سيتم ملؤها ديناميكياً -->
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="return-summary" style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                            <h6>ملخص الاسترداد</h6>
                            <div class="summary-row">
                                <span>عدد العناصر:</span>
                                <span id="returnTotalItems">0</span>
                            </div>
                            <div class="summary-row">
                                <span>المجموع الفرعي:</span>
                                <span id="returnSubtotalAmount">0.00 ر.س</span>
                            </div>
                            <div class="summary-row">
                                <span>ضريبة القيمة المضافة (15%):</span>
                                <span id="returnTaxAmount">0.00 ر.س</span>
                            </div>
                            <div class="summary-row total">
                                <span>إجمالي الاسترداد:</span>
                                <span id="returnFinalTotal">0.00 ر.س</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="border-top: 2px solid #dc3545;">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times me-2"></i>إلغاء
                </button>
                <button type="button" class="btn btn-danger" id="confirmReturnBtn">
                    <i class="fas fa-check me-2"></i>تأكيد الاسترداد
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
            categories: @json($categories ?? []),
            products: @json($products ?? []),
            clients: @json($clients ?? []),
            paymentMethods: @json($paymentMethods ?? []),
            defaultCustomerId: @json($defaultCustomerId ?? null),
            csrfToken: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            printUrlTemplate: ""
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
        const categoriesSection = document.getElementById('categoriesSection');
        const productsSection = document.getElementById('productsSection');
        const clientsSection = document.getElementById('clientsSection');
        const orderContent = document.getElementById('orderContent');
        const selectedClientName = document.getElementById('selectedClientName');

        // تهيئة التطبيق
        document.addEventListener('DOMContentLoaded', function() {
            console.log('بيانات التطبيق:', appData);
            initializeApp();
            setupEventListeners();
            updateDateTime();
            setInterval(updateDateTime, 60000);
        });

  function initializeApp() {
    // تحديد العميل الافتراضي من الإعدادات
    const defaultClientId = appData.defaultCustomerId;
    if (defaultClientId) {
        const defaultClient = appData.clients.find(client => client.id == defaultClientId);
        if (defaultClient && orders[currentOrder]) {
            orders[currentOrder].client = defaultClient;
            selectedClientName.textContent = defaultClient.trade_name;
        }
    }
    
    // تحميل البيانات الافتراضية
    loadCategories();
    updateOrderDisplay();
}

        function setupEventListeners() {
            // البحث
            searchInput.addEventListener('input', handleSearch);
            
            // أزرار البحث
            document.getElementById('categoriesBtn').addEventListener('click', () => switchView('categories'));
            document.getElementById('productsBtn').addEventListener('click', () => switchView('products'));
            document.getElementById('clientsBtn').addEventListener('click', () => switchView('clients'));
            document.getElementById('invoicesBtn').addEventListener('click', () => switchView('invoices'));

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
                if (e.target.closest('.product-card') && !e.target.closest('.info-btn')) {
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
                switchView('clients');
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
                weekday: 'short',
                day: 'numeric',
                month: 'short',
                hour: '2-digit',
                minute: '2-digit'
            };
            const dateTimeElement = document.getElementById('currentDateTime');
            if (dateTimeElement) {
                dateTimeElement.textContent = now.toLocaleDateString('ar-SA', options);
            }
        }

        // تبديل العرض
        function switchView(view) {
            currentView = view;
            
            // تحديث الأزرار
            document.querySelectorAll('.btn-search').forEach(btn => {
                btn.classList.remove('active');
            });
            
            if (view === 'categories') {
                document.getElementById('categoriesBtn').classList.add('active');
                searchInput.placeholder = 'البحث في التصنيفات...';
                loadCategories();
            } else if (view === 'products') {
                document.getElementById('productsBtn').classList.add('active');
                searchInput.placeholder = 'البحث في المنتجات...';
                loadProducts();
            } else if (view === 'clients') {
                document.getElementById('clientsBtn').classList.add('active');
                searchInput.placeholder = 'البحث في العملاء...';
                loadClients();
            }
            
            // إخفاء جميع الأقسام
            document.querySelectorAll('.content-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // عرض القسم المطلوب
            document.getElementById(view + 'Section').style.display = 'block';
            
            // مسح البحث السابق
            searchInput.value = '';
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

            // البحث المحلي
            try {
                if (currentView === 'products') {
                    const filteredProducts = appData.products.filter(product => 
                        product.name.toLowerCase().includes(query.toLowerCase()) ||
                        (product.code && product.code.toLowerCase().includes(query.toLowerCase()))
                    );
                    displayProducts(filteredProducts);
                } else if (currentView === 'clients') {
                    const filteredClients = appData.clients.filter(client => 
                        client.trade_name.toLowerCase().includes(query.toLowerCase()) ||
                        (client.phone && client.phone.includes(query))
                    );
                    displayClients(filteredClients);
                } else if (currentView === 'categories') {
                    const filteredCategories = appData.categories.filter(category => 
                        category.name.toLowerCase().includes(query.toLowerCase())
                    );
                    displayCategories(filteredCategories);
                }
            } catch (error) {
                console.error('خطأ في البحث:', error);
                showError('حدث خطأ أثناء البحث');
            }
        }

        // تحميل التصنيفات
        function loadCategories() {
            displayCategories(appData.categories);
        }

        function displayCategories(categories) {
            const grid = document.getElementById('categoriesGrid');
            
            if (!categories || categories.length === 0) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-folder-open"></i><p>لا توجد تصنيفات</p></div>';
                return;
            }
            
            // التصنيفات محملة بالفعل في HTML - نحتاج فقط لتحديث الفلترة
            const existingCards = grid.querySelectorAll('.category-card');
            existingCards.forEach(card => {
                const categoryId = card.dataset.categoryId;
                const categoryExists = categories.some(cat => cat.id == categoryId);
                card.style.display = categoryExists ? 'flex' : 'none';
            });
        }

        // اختيار تصنيف
        function selectCategory(categoryId) {
            selectedCategory = categoryId;
            
            // تحديث حالة التصنيفات
            document.querySelectorAll('.category-card').forEach(card => {
                card.classList.remove('active');
            });
            const selectedCard = document.querySelector(`[data-category-id="${categoryId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('active');
            }
            
            // تحميل منتجات التصنيف
            loadProductsByCategory(categoryId);
            switchView('products');
        }

        // تحميل المنتجات بناءً على التصنيف
        function loadProductsByCategory(categoryId) {
            const categoryProducts = appData.products.filter(product => 
                product.category_id == categoryId
            );
            displayProducts(categoryProducts);
        }
// استبدل هذه الدوال في الكود JavaScript الموجود

// اختيار تصنيف وتحميل منتجاته من الخادم
function selectCategory(categoryId) {
    console.log('تم اختيار التصنيف:', categoryId);
    selectedCategory = categoryId;
    
    // تحديث حالة التصنيفات بصرياً
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
    });
    const selectedCard = document.querySelector(`[data-category-id="${categoryId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('active');
    }
    
    // التبديل إلى عرض المنتجات
    switchView('products');
    
    // تحميل منتجات التصنيف باستخدام API
    loadProductsByCategory(categoryId);
    
    // عرض رسالة تأكيد
    const categoryName = selectedCard ? selectedCard.querySelector('h6').textContent : 'التصنيف';
    showNotification(`تم اختيار تصنيف: ${categoryName}`, 'info');
}

// تحميل المنتجات بناءً على التصنيف باستخدام API
function loadProductsByCategory(categoryId) {
    const productsGrid = document.getElementById('productsGrid');
    
    // عرض حالة التحميل
    productsGrid.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p class="mt-2">جاري تحميل منتجات التصنيف...</p>
        </div>
    `;
    
    // إرسال طلب لجلب منتجات التصنيف
    fetch(`/POS/products-by-category?category_id=${categoryId}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': appData.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('منتجات التصنيف:', data);
        if (data.success && data.products) {
            if (data.products.length > 0) {
                displayProductsWithHeader(data.products, categoryId);
            } else {
                displayEmptyProducts('لا توجد منتجات في هذا التصنيف', categoryId);
            }
        } else {
            displayProductsError('فشل في تحميل منتجات التصنيف', categoryId);
        }
    })
    .catch(error => {
        console.error('خطأ في تحميل منتجات التصنيف:', error);
        displayProductsError(error.message, categoryId);
    });
}

// عرض المنتجات مع رأس يوضح التصنيف المختار
function displayProductsWithHeader(products, categoryId) {
    const grid = document.getElementById('productsGrid');
    
    // الحصول على اسم التصنيف
    const categoryCard = document.querySelector(`[data-category-id="${categoryId}"]`);
    const categoryName = categoryCard ? categoryCard.querySelector('h6').textContent : 'التصنيف المختار';
    
    // إنشاء رأس التصفية
    const headerHtml = `
      
    `;
    
    // إنشاء شبكة المنتجات
    const productsHtml = products.map(product => `
        <div class="product-card fade-in" data-product-id="${product.id}">
            <img src="${getProductImage(product)}" 
                 alt="${product.name}" class="product-image"
                 onerror="this.src='/assets/uploads/no_image.jpg'">
            <div class="product-name" title="${product.name}">${product.name}</div>
            <div class="product-info">
                <span class="product-price">${parseFloat(product.sale_price || 0).toFixed(2)} ر.س</span>
                <button class="info-btn" title="تفاصيل المنتج" onclick="showProductInfo(${product.id})">
                    <i class="fas fa-info-circle"></i>
                </button>
            </div>
        </div>
    `).join('');
    
    // عرض المحتوى
    grid.innerHTML = headerHtml + '<div class="products-grid">' + productsHtml + '</div>';
}

// عرض رسالة فارغة مع خيارات
function displayEmptyProducts(message = 'لا توجد منتجات', categoryId = null) {
    const productsGrid = document.getElementById('productsGrid');
    
    let headerHtml = '';
    if (categoryId) {
        const categoryCard = document.querySelector(`[data-category-id="${categoryId}"]`);
        const categoryName = categoryCard ? categoryCard.querySelector('h6').textContent : 'التصنيف';
        
        headerHtml = `
        
        `;
    }
    
    productsGrid.innerHTML = headerHtml + `
        <div class="empty-state">
            <i class="fas fa-box-open"></i>
            <p>${message}</p>
            
        </div>
    `;
}

// عرض خطأ مع خيارات إعادة المحاولة
function displayProductsError(errorMessage, categoryId = null) {
    const productsGrid = document.getElementById('productsGrid');
    
    productsGrid.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle text-warning"></i>
            <p>خطأ في تحميل المنتجات</p>
            <small class="text-muted">${errorMessage}</small>
            <div class="mt-3">
                ${categoryId ? `
                    <button class="btn btn-primary me-2" onclick="loadProductsByCategory(${categoryId})">
                        <i class="fas fa-redo"></i> إعادة المحاولة
                    </button>
                ` : ''}
                <button class="btn btn-secondary" onclick="clearCategorySelection()">
                    <i class="fas fa-list"></i> عرض جميع المنتجات
                </button>
            </div>
        </div>
    `;
}

// إلغاء اختيار التصنيف وعرض جميع المنتجات
function clearCategorySelection() {
    selectedCategory = null;
    
    // إزالة التحديد من جميع التصنيفات
    document.querySelectorAll('.category-card').forEach(card => {
        card.classList.remove('active');
    });
    
    // تحديث placeholder البحث
    if (currentView === 'products') {
        searchInput.placeholder = 'البحث في المنتجات...';
    }
    
    // تحميل جميع المنتجات
    displayProducts(appData.products);
    
    showNotification('تم إلغاء اختيار التصنيف - عرض جميع المنتجات', 'info');
}

// تحديث دالة loadProducts للتحقق من التصنيف المختار
function loadProducts() {
    if (selectedCategory) {
        // إذا كان هناك تصنيف مختار، استخدمه
        loadProductsByCategory(selectedCategory);
    } else {
        // عرض جميع المنتجات
        displayProducts(appData.products);
    }
}

// تحديث دالة switchView للتعامل مع التصنيف المختار
function switchView(view) {
    console.log('تبديل العرض إلى:', view);
    currentView = view;
    
    // تحديث الأزرار
    document.querySelectorAll('.btn-search').forEach(btn => {
        btn.classList.remove('active');
    });
    
    const btnId = view + 'Btn';
    const activeBtn = document.getElementById(btnId);
    if (activeBtn) {
        activeBtn.classList.add('active');
    }
    
    // تحديث placeholder البحث
    const placeholders = {
        categories: 'البحث في التصنيفات...',
        products: selectedCategory ? 'البحث في منتجات التصنيف...' : 'البحث في المنتجات...',
        clients: 'البحث في العملاء...',
        invoices: 'البحث في الفواتير...'
    };
    searchInput.placeholder = placeholders[view] || 'البحث...';
    
    // إخفاء جميع الأقسام
    document.querySelectorAll('.content-section').forEach(section => {
        section.style.display = 'none';
    });
    
    // عرض القسم المطلوب
    const targetSection = document.getElementById(view + 'Section');
    if (targetSection) {
        targetSection.style.display = 'block';
    }
    
    // تحميل البيانات حسب النوع
    switch(view) {
        case 'categories':
            // عند العودة للتصنيفات، لا نقوم بإلغاء الاختيار
            loadCategories();
            break;
        case 'products':
            loadProducts(); // ستتحقق من وجود تصنيف مختار
            break;
        case 'clients':
            loadClients();
            break;
        case 'invoices':
            if (typeof loadInvoices === 'function') {
                loadInvoices();
            }
            break;
    }
    
    // مسح البحث السابق
    searchInput.value = '';
}

// تحديث دالة performSearch للتعامل مع البحث في منتجات التصنيف
function performSearch(query) {
    if (!query) {
        if (currentView === 'categories') {
            loadCategories();
        } else if (currentView === 'products') {
            loadProducts(); // ستتحقق من التصنيف المختار
        } else if (currentView === 'clients') {
            loadClients();
        } else if (currentView === 'invoices') {
            if (typeof loadInvoices === 'function') {
                loadInvoices();
            }
        }
        return;
    }

    try {
        if (currentView === 'products') {
            searchInProducts(query);
        } else if (currentView === 'clients') {
            const filteredClients = appData.clients.filter(client => 
                client.trade_name.toLowerCase().includes(query.toLowerCase()) ||
                (client.phone && client.phone.includes(query))
            );
            displayClients(filteredClients);
        } else if (currentView === 'categories') {
            const filteredCategories = appData.categories.filter(category => 
                category.name.toLowerCase().includes(query.toLowerCase())
            );
            displayCategories(filteredCategories);
        } else if (currentView === 'invoices') {
            if (typeof searchInvoices === 'function') {
                searchInvoices(query);
            }
        }
    } catch (error) {
        console.error('خطأ في البحث:', error);
        showError('حدث خطأ أثناء البحث');
    }
}

// البحث في المنتجات مع مراعاة التصنيف المختار
function searchInProducts(query) {
    if (selectedCategory) {
        // البحث في منتجات التصنيف المحدد باستخدام API
        searchInCategoryProducts(query);
    } else {
        // البحث في جميع المنتجات (البحث المحلي)
        const filteredProducts = appData.products.filter(product => 
            product.name.toLowerCase().includes(query.toLowerCase()) ||
            (product.code && product.code.toLowerCase().includes(query.toLowerCase()))
        );
        displayProducts(filteredProducts);
    }
}

// البحث في منتجات التصنيف المحدد باستخدام API
function searchInCategoryProducts(query) {
    const productsGrid = document.getElementById('productsGrid');
    productsGrid.innerHTML = `
        <div class="loading-spinner">
            <div class="spinner"></div>
            <p class="mt-2">جاري البحث في منتجات التصنيف...</p>
        </div>
    `;
    
    fetch(`/POS/products-by-category?category_id=${selectedCategory}&search=${encodeURIComponent(query)}`, {
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': appData.csrfToken,
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success && data.products) {
            if (data.products.length > 0) {
                displayProductsWithHeader(data.products, selectedCategory);
            } else {
                displayEmptyProducts(`لم يتم العثور على منتجات تحتوي على "${query}" في هذا التصنيف`, selectedCategory);
            }
        } else {
            displayProductsError('فشل في البحث', selectedCategory);
        }
    })
    .catch(error => {
        console.error('خطأ في البحث:', error);
        displayProductsError(error.message, selectedCategory);
    });
}

// إجعل الدوال متاحة عالمياً
window.selectCategory = selectCategory;
window.loadProductsByCategory = loadProductsByCategory;
window.clearCategorySelection = clearCategorySelection;
window.displayProductsWithHeader = displayProductsWithHeader;
window.searchInProducts = searchInProducts;
window.searchInCategoryProducts = searchInCategoryProducts;
        // تحميل جميع المنتجات
        function loadProducts() {
            displayProducts(appData.products);
        }

        // عرض المنتجات
        function displayProducts(products) {
            const grid = document.getElementById('productsGrid');
            
            if (!products || products.length === 0) {
                grid.innerHTML = '<div class="empty-state"><i class="fas fa-box-open"></i><p>لا توجد منتجات</p></div>';
                return;
            }
            
            const productsHtml = products.map(product => `
                <div class="product-card fade-in" data-product-id="${product.id}">
                    <img src="${getProductImage(product)}" 
                         alt="${product.name}" class="product-image"
                         onerror="this.src=''">
                    <div class="product-name" title="${product.name}">${product.name}</div>
                    <div class="product-info">
                        <span class="product-price">${parseFloat(product.sale_price || 0).toFixed(2)} ر.س</span>
                        <button class="info-btn" title="تفاصيل المنتج" onclick="showProductInfo(${product.id})">
                            <i class="fas fa-info-circle"></i>
                        </button>
                    </div>
                </div>
            `).join('');
            
            grid.innerHTML = productsHtml;
        }

        // الحصول على صورة المنتج
        function getProductImage(product) {
            if (product.images) {
                if (product.images.startsWith('http')) {
                    return product.images;
                }
                if (product.images.startsWith('/')) {
                    return product.images;
                }
                return '/assets/uploads/product/' + product.images;
            }
            return '';
        }

        // عرض تفاصيل المنتج
        function showProductInfo(productId) {
            const product = appData.products.find(p => p.id == productId);
            if (!product) return;
            
            Swal.fire({
                title: product.name,
                html: `
                    <div class="text-start">
                        <img src="${getProductImage(product)}" class="img-fluid mb-3" style="max-height: 200px; border-radius: 10px;">
                        <p><strong>الكود:</strong> ${product.code || 'غير محدد'}</p>
                        <p><strong>السعر:</strong> ${parseFloat(product.sale_price || 0).toFixed(2)} ر.س</p>
                    </div>
                `,
                confirmButtonText: 'إغلاق',
                confirmButtonColor: '#4CAF50'
            });
        }

        // تحميل العملاء
        function loadClients() {
            displayClients(appData.clients);
        }

        // عرض العملاء
        function displayClients(clients) {
            const container = document.getElementById('clientsContainer');
            
            if (!clients || clients.length === 0) {
                container.innerHTML = '<div class="empty-state"><i class="fas fa-users"></i><p>لا توجد عملاء</p></div>';
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
                                <i class="fas fa-phone me-1"></i>${client.phone || 'غير محدد'}
                            </small>
                        </div>
                    </div>
                </div>
            `).join('');
            
            container.innerHTML = `<div class="clients-container">${clientsHtml}</div>`;
        }

        // إضافة منتج للطلب
        function addProductToOrder(productId) {
    const product = appData.products.find(p => p.id == productId);
    if (!product) {
        showError('المنتج غير موجود');
        return;
    }
    
    const order = orders[currentOrder];
    
    // التحقق من وجود عميل فقط إذا لم يكن هناك عميل افتراضي
    if (!order.client && !appData.defaultCustomerId) {
        Swal.fire({
            title: 'يجب اختيار عميل',
            text: 'الرجاء اختيار عميل قبل إضافة المنتجات',
            icon: 'warning',
            confirmButtonText: 'اختيار عميل',
            confirmButtonColor: '#4CAF50'
        }).then(() => {
            switchView('clients');
        });
        return;
    }
    
    // إذا لم يكن هناك عميل مختار ولكن يوجد عميل افتراضي
    if (!order.client && appData.defaultCustomerId) {
        const defaultClient = appData.clients.find(client => client.id == appData.defaultCustomerId);
        if (defaultClient) {
            order.client = defaultClient;
            selectedClientName.textContent = defaultClient.trade_name;
        }
    }
    
    const existingItem = order.items.find(item => item.id == productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
        existingItem.total = existingItem.quantity * existingItem.price;
    } else {
        order.items.push({
            id: productId,
            name: product.name,
            price: parseFloat(product.sale_price || 0),
            quantity: 1,
            total: parseFloat(product.sale_price || 0)
        });
    }
    
    updateOrderDisplay();
    showNotification(`تم إضافة "${product.name}" للطلب`, 'success');
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
            const selectedCard = document.querySelector(`[data-client-id="${clientId}"]`);
            if (selectedCard) {
                selectedCard.classList.add('selected');
            }
            
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
                            <div class="item-name" title="${item.name}">${item.name}</div>
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
    
    // حساب المجموع قبل الضريبة والضريبة منفصلة
    let subtotalExcludingTax = 0;
    let totalTax = 0;
    
    order.items.forEach(item => {
        const priceIncludingTax = item.price;
        const priceExcludingTax = priceIncludingTax / 1.15;
        const taxPerUnit = priceIncludingTax - priceExcludingTax;
        
        subtotalExcludingTax += priceExcludingTax * item.quantity;
        totalTax += taxPerUnit * item.quantity;
    });
    
    const subtotalIncludingTax = subtotalExcludingTax + totalTax;
    
    // حساب الخصم
    const discountType = document.getElementById('discountType').value;
    const discountValue = parseFloat(document.getElementById('discountValue').value) || 0;
    
    let discountAmount = 0;
    if (discountValue > 0) {
        if (discountType === 'percentage') {
            discountAmount = subtotalIncludingTax * (discountValue / 100);
        } else {
            discountAmount = discountValue;
        }
    }
    
    const finalTotal = Math.max(0, subtotalIncludingTax - discountAmount);
    
    // إضافة عرض تفصيل الضريبة
    document.getElementById('totalItems').textContent = totalItems;
    document.getElementById('subtotalAmount').textContent = subtotalExcludingTax.toFixed(2) + ' ر.س';
    
    // إضافة عرض الضريبة إذا لم يكن موجود
    if (!document.getElementById('taxAmount')) {
        const taxRow = document.createElement('div');
        taxRow.className = 'summary-row';
        taxRow.innerHTML = `
            <span>ضريبة القيمة المضافة (15%):</span>
            <span id="taxAmount">${totalTax.toFixed(2)} ر.س</span>
        `;
        document.getElementById('subtotalAmount').parentElement.insertAdjacentElement('afterend', taxRow);
    } else {
        document.getElementById('taxAmount').textContent = totalTax.toFixed(2) + ' ر.س';
    }
    
    document.getElementById('finalTotal').textContent = finalTotal.toFixed(2) + ' ر.س';
    
    // حفظ بيانات الخصم في الطلب
    order.discount = { type: discountType, value: discountValue };
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
            const activeTab = document.querySelector(`[data-order="${orderNumber}"]`);
            if (activeTab) {
                activeTab.classList.add('active');
            }
            
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

        // تأكيد الدفع مع التحقق من المبالغ
        function confirmPayment() {
            const totalAmount = parseFloat(document.getElementById('modalTotalAmount').textContent.replace(' ر.س', ''));
            const totalPaid = parseFloat(document.getElementById('totalPaidAmount').textContent.replace(' ر.س', ''));
            const remaining = totalAmount - totalPaid;
            
            // التحقق من المبلغ المدفوع
            if (remaining > 0.01) {
                Swal.fire({
                    title: 'المبلغ غير مكتمل',
                    text: `يتبقى ${remaining.toFixed(2)} ر.س لإتمام الدفع`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'متابعة كدفعة جزئية',
                    cancelButtonText: 'إلغاء',
                    confirmButtonColor: '#ffc107'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processPartialPayment(totalAmount, totalPaid);
                    }
                });
                return;
            } else if (remaining < -0.01) {
                Swal.fire({
                    title: 'المبلغ المدفوع أكبر من المستحق',
                    text: `المبلغ الزائد: ${Math.abs(remaining).toFixed(2)} ر.س`,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: 'متابعة مع الباقي',
                    cancelButtonText: 'تعديل المبلغ',
                    confirmButtonColor: '#17a2b8'
                }).then((result) => {
                    if (result.isConfirmed) {
                        processExcessPayment(totalAmount, totalPaid, Math.abs(remaining));
                    }
                });
                return;
            }
            
            // إذا كان المبلغ صحيح تماماً
            processExactPayment(totalAmount);
        }

        // معالجة الدفعة الجزئية
        function processPartialPayment(totalAmount, paidAmount) {
            showNotification('تم حفظ الدفعة الجزئية - الفاتورة لم تكتمل', 'warning');
            $('#paymentModal').modal('hide');
            resetPaymentForm();
        }

        // معالجة الدفعة الزائدة
        function processExcessPayment(totalAmount, paidAmount, changeAmount) {
            completePaymentProcess(totalAmount).then(() => {
                Swal.fire({
                    title: 'تم الدفع بنجاح',
                    text: `الباقي للعميل: ${changeAmount.toFixed(2)} ر.س`,
                    icon: 'success',
                    confirmButtonText: 'طباعة الفاتورة',
                    confirmButtonColor: '#28a745'
                }).then(() => {
                    openPrintPage();
                });
            });
        }

        // معالجة الدفعة الصحيحة
        function processExactPayment(totalAmount) {
            completePaymentProcess(totalAmount).then(() => {
                Swal.fire({
                    title: 'تم الدفع بنجاح!',
                    text: 'تم إتمام العملية بنجاح',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false
                }).then(() => {
                    openPrintPage();
                });
            });
        }

        // إتمام عملية الدفع
        async function completePaymentProcess(totalAmount) {
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
            
try {
    const response = await saveInvoiceToServer(invoiceData);
    if (response.success) {
        // حفظ معرف الفاتورة للطباعة
        window.lastInvoiceId = response.invoice_id;
        $('#paymentModal').modal('hide');
        resetPaymentForm();
        resetCurrentOrder();
        return response;
    } else {
        throw new Error(response.message || 'فشل في حفظ الفاتورة');
    }
} catch (error) {
    console.error('خطأ في الدفع:', error);

    // لو الرد جاي من السيرفر برسالة
    let message = 'حدث خطأ أثناء معالجة الدفع';
    if (error.response && error.response.data && error.response.data.message) {
        message = error.response.data.message;
    } else if (error.message) {
        message = error.message;
    }

    Swal.fire({
        title: 'خطأ في المعاملة',
        text: message,
        icon: 'error'
    });

    throw error;
}


        }

        // حفظ الفاتورة في الخادم
        async function saveInvoiceToServer(invoiceData) {
            const response = await fetch('/POS/sales-start/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(invoiceData)
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const result = await response.json();
            return result;
        }

        // فتح صفحة الطباعة
        function openPrintPage() {
            if (window.lastInvoiceId) {
                
                const printUrl = `/POS/Sales_Start/invoices/${window.lastInvoiceId}/print`;
                window.open(printUrl, '_blank', 'width=800,height=600');
            } else {
                showError('لا يمكن العثور على الفاتورة للطباعة');
            }
        }

        // إعادة تعيين نموذج الدفع
        function resetPaymentForm() {
            document.querySelectorAll('.payment-method-input').forEach(input => {
                input.value = '0';
            });
            document.getElementById('totalPaidAmount').textContent = '0.00 ر.س';
            document.getElementById('remainingAmount').textContent = '0.00 ر.س';
            document.getElementById('modalTotalAmount').textContent = '0.00 ر.س';
        }

        // تبديل وضع البحث مع إضافة الفواتير
        function switchSearchMode(mode) {
            currentView = mode;
            
            const modeTexts = {
                products: 'المنتجات',
                clients: 'العملاء',
                invoices: 'الفواتير'
            };
            
            const placeholders = {
                products: 'ابحث عن المنتجات...',
                clients: 'ابحث عن العملاء...',
                invoices: 'ابحث عن الفواتير...'
            };
            
            document.getElementById('searchModeText').textContent = modeTexts[mode];
            searchInput.placeholder = placeholders[mode];
            
            // تبديل العرض
            switchView(mode);
            
            // تحميل البيانات حسب النوع
            if (mode === 'invoices') {
                loadInvoices();
            }
            
            // التركيز على حقل البحث
            searchInput.focus();
        }

        // تحميل الفواتير
        function loadInvoices() {
            // عرض حالة التحميل
            // showLoading();
            
            // إرسال طلب لجلب الفواتير
            fetch('/POS/search?type=invoices&query=', {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.invoices) {
                    displayInvoices(data.invoices);
                } else {
                    displayInvoices([]);
                }
            })
            .catch(error => {
                console.error('خطأ في تحميل الفواتير:', error);
                displayInvoices([]);
            });
        }

        // عرض الفواتير
        function displayInvoices(invoices) {
            const container = document.getElementById('invoicesContainer');
            if (!container) {
                // إنشاء المحتوى إذا لم يكن موجوداً
                const invoicesSection = document.getElementById('invoicesSection');
                if (!invoicesSection) {
                    const newSection = document.createElement('div');
                    newSection.id = 'invoicesSection';
                    newSection.className = 'content-section';
                    newSection.style.display = 'none';
                    newSection.innerHTML = '<div id="invoicesContainer"></div>';
                    document.querySelector('.content-body').appendChild(newSection);
                }
            }
            
            const invoicesContainer = document.getElementById('invoicesContainer') || 
                                     document.querySelector('#invoicesSection');
            
            if (!invoices || invoices.length === 0) {
                invoicesContainer.innerHTML = `
                    <div class="empty-state">
                        <i class="fas fa-file-invoice"></i>
                        <p>لا توجد فواتير</p>
                    </div>
                `;
                return;
            }
            
            const invoicesHtml = invoices.map(invoice => `
                <div class="invoice-card fade-in" data-invoice-id="${invoice.id}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">فاتورة رقم: ${invoice.code || invoice.id}</h6>
                            <small class="text-muted">
                                <i class="fas fa-user me-1"></i>${invoice.client_name || 'عميل غير محدد'}
                            </small>
                            <br>
                            <small class="text-muted">
                                <i class="fas fa-calendar me-1"></i>${formatDate(invoice.invoice_date)}
                            </small>
                            <div class="mt-2">
                                <span class="badge ${getInvoiceStatusBadge(invoice.payment_status)}">
                                    ${getInvoiceStatusText(invoice.payment_status)}
                                </span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success mb-2">
                                ${parseFloat(invoice.grand_total || 0).toFixed(2)} ر.س
                            </div>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewInvoiceDetails(${invoice.id})" title="عرض التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="btn btn-outline-success" onclick="createReturn(${invoice.id})" title="إنشاء مرتجع">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `).join('');
            
            const invoicesCSS = `
                <style>
                .invoice-card {
                    background: white;
                    border: 2px solid #e9ecef;
                    border-radius: var(--border-radius);
                    padding: 15px;
                    margin-bottom: 12px;
                    cursor: pointer;
                    transition: var(--transition);
                }
                
                .invoice-card:hover {
                    border-color: var(--secondary-color);
                    transform: translateY(-2px);
                    box-shadow: var(--card-shadow);
                }
                
                .badge {
                    font-size: 0.75rem;
                    padding: 4px 8px;
                }
                
                .badge.bg-success { background-color: #28a745 !important; }
                .badge.bg-warning { background-color: #ffc107 !important; color: #000; }
                .badge.bg-danger { background-color: #dc3545 !important; }
                .badge.bg-secondary { background-color: #6c757d !important; }
                </style>
            `;
            
            if (!document.getElementById('invoicesCSS')) {
                const style = document.createElement('style');
                style.id = 'invoicesCSS';
                style.textContent = invoicesCSS;
                document.head.appendChild(style);
            }
            
            invoicesContainer.innerHTML = invoicesHtml;
        }

        // وظائف مساعدة للفواتير
        function formatDate(dateString) {
            if (!dateString) return 'غير محدد';
            const date = new Date(dateString);
            return date.toLocaleDateString('ar-SA');
        }

        function getInvoiceStatusBadge(status) {
            const badges = {
                1: 'bg-success',
                2: 'bg-warning', 
                3: 'bg-secondary',
                4: 'bg-info',
                5: 'bg-danger'
            };
            return badges[status] || 'bg-secondary';
        }

        function getInvoiceStatusText(status) {
            const texts = {
                1: 'مدفوعة',
                2: 'جزئياً', 
                3: 'مسودة',
                4: 'تحت المراجعة',
                5: 'ملغية'
            };
            return texts[status] || 'غير محدد';
        }

        // عرض تفاصيل الفاتورة
        function viewInvoiceDetails(invoiceId) {
            Swal.fire({
                title: 'جاري تحميل تفاصيل الفاتورة...',
                allowOutsideClick: false,
                didOpen: () => {
                    // Swal.showLoading();
                }
            });
            
            fetch(`/POS/invoices/${invoiceId}/details`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showInvoiceDetailsModal(data.invoice);
                } else {
                    Swal.fire('خطأ', 'لا يمكن تحميل تفاصيل الفاتورة', 'error');
                }
            })
            .catch(error => {
                console.error('خطأ:', error);
                Swal.fire('خطأ', 'حدث خطأ أثناء تحميل البيانات', 'error');
            });
        }

        // عرض نافذة تفاصيل الفاتورة
        function showInvoiceDetailsModal(invoice) {
            const itemsHtml = invoice.items.map(item => `
                <tr>
                    <td>${item.product_name || item.item}</td>
                    <td>${item.quantity}</td>
                    <td>${parseFloat(item.unit_price).toFixed(2)} ر.س</td>
                    <td>${parseFloat(item.total).toFixed(2)} ر.س</td>
                </tr>
            `).join('');
            
            Swal.fire({
                title: `فاتورة رقم: ${invoice.id}`,
                html: `
                    <div class="text-start">
                        <p><strong>العميل:</strong> ${invoice.client_name || 'غير محدد'}</p>
                        <p><strong>التاريخ:</strong> ${formatDate(invoice.invoice_date)}</p>
                        <p><strong>الحالة:</strong> ${getInvoiceStatusText(invoice.payment_status)}</p>
                        <hr>
                        <h6>العناصر:</h6>
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>المنتج</th>
                                    <th>الكمية</th>
                                    <th>السعر</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${itemsHtml}
                            </tbody>
                        </table>
                        <hr>
                        <p class="text-end"><strong>الإجمالي: ${parseFloat(invoice.grand_total).toFixed(2)} ر.س</strong></p>
                    </div>
                `,
                width: '600px',
                confirmButtonText: 'إغلاق',
                showCancelButton: true,
                cancelButtonText: 'إنشاء مرتجع',
                cancelButtonColor: '#dc3545'
            }).then((result) => {
                if (result.dismiss === Swal.DismissReason.cancel) {
                    createReturn(invoice.id);
                }
            });
        }

        // إنشاء مرتجع
        function createReturn(invoiceId) {
            Swal.fire({
                title: 'إنشاء مرتجع',
                text: 'هل تريد إنشاء مرتجع لهذه الفاتورة؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، أنشئ مرتجع',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // توجيه لصفحة إنشاء المرتجع
                    window.open(`/POS/ReturnIInvoices/create?invoice_id=${invoiceId}`, '_blank');
                }
            });
        }

        // تحديث دالة البحث لتشمل الفواتير
        function performSearch(query) {
            if (!query) {
                if (currentView === 'categories') {
                    loadCategories();
                } else if (currentView === 'products') {
                    loadProducts();
                } else if (currentView === 'clients') {
                    loadClients();
                } else if (currentView === 'invoices') {
                    loadInvoices();
                }
                return;
            }

            // البحث المحلي للمنتجات والعملاء
            if (currentView === 'products') {
                const filteredProducts = appData.products.filter(product => 
                    product.name.toLowerCase().includes(query.toLowerCase()) ||
                    (product.code && product.code.toLowerCase().includes(query.toLowerCase()))
                );
                displayProducts(filteredProducts);
            } else if (currentView === 'clients') {
                const filteredClients = appData.clients.filter(client => 
                    client.trade_name.toLowerCase().includes(query.toLowerCase()) ||
                    (client.phone && client.phone.includes(query))
                );
                displayClients(filteredClients);
            } else if (currentView === 'categories') {
                const filteredCategories = appData.categories.filter(category => 
                    category.name.toLowerCase().includes(query.toLowerCase())
                );
                displayCategories(filteredCategories);
            } else if (currentView === 'invoices') {
                // البحث في الفواتير عبر الخادم
                searchInvoices(query);
            }
        }

        // البحث في الفواتير
        function searchInvoices(query) {
            // showLoading();
            
            fetch(`/POS/search?type=invoices&query=${encodeURIComponent(query)}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.invoices) {
                    displayInvoices(data.invoices);
                } else {
                    displayInvoices([]);
                }
            })
            .catch(error => {
                console.error('خطأ في البحث:', error);
                displayInvoices([]);
            });
        }

        // إضافة زر الفواتير للواجهة
        // document.addEventListener('DOMContentLoaded', function() {
        //     // إضافة زر الفواتير بعد التحميل
        //     const clientsBtn = document.getElementById('clientsBtn');
        //     if (clientsBtn && !document.getElementById('invoicesBtn')) {
        //         const invoicesBtn = document.createElement('button');
        //         invoicesBtn.className = 'btn btn-search';
        //         invoicesBtn.id = 'invoicesBtn';
        //         invoicesBtn.title = 'الفواتير';
        //         invoicesBtn.innerHTML = `
        //             <i class="fas fa-file-invoice"></i>
        //             <span class="d-none d-md-inline">الفواتير</span>
        //         `;
        //         invoicesBtn.addEventListener('click', () => switchView('invoices'));
                
        //         clientsBtn.parentNode.insertBefore(invoicesBtn, clientsBtn.nextSibling);
        //     }
        // });

        // إجعل الدوال متاحة عالمياً
        window.viewInvoiceDetails = viewInvoiceDetails;
        window.createReturn = createReturn;

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
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA' || e.target.tagName === 'SELECT') {
                    return;
                }
                
                switch(e.key) {
                    case 'F1':
                        e.preventDefault();
                        switchView('categories');
                        break;
                    case 'F2':
                        e.preventDefault();
                        switchView('products');
                        break;
                    case 'F3':
                        e.preventDefault();
                        switchView('clients');
                        break;
                    case 'F5':
                        e.preventDefault();
                        holdOrder();
                        break;
                    case 'F6':
                        e.preventDefault();
                        initiatePayment();
                        break;
                        case 'F7':  // أضف هذا
                e.preventDefault();
                initiateReturn();
                break;
                    case 'F11':
                        e.preventDefault();
                        toggleFullscreen();
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
// متغيرات الاسترداد
let returnItems = [];
let availableInvoices = [];

// دالة بدء الاسترداد
async function initiateReturn() {
    try {
        // جلب الفواتير المتاحة للاسترداد من الجلسة الحالية
        const response = await fetch('/POS/available-invoices-for-return', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': appData.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.invoices && data.invoices.length > 0) {
            availableInvoices = data.invoices;
            showInvoiceSelectionModal();
        } else {
            showNotification('لا توجد فواتير متاحة للاسترداد في الجلسة الحالية', 'warning');
        }
    } catch (error) {
        console.error('خطأ في جلب الفواتير:', error);
        showError('حدث خطأ أثناء جلب الفواتير المتاحة للاسترداد');
    }
}

// عرض modal اختيار الفاتورة
function showInvoiceSelectionModal() {
    const invoicesHtml = availableInvoices.map(invoice => `
        <div class="invoice-selection-item" data-invoice-id="${invoice.id}" data-invoice-code="${invoice.code}" data-client-name="${invoice.client_name || ''}" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; cursor: pointer; border-radius: 8px;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h6>فاتورة رقم: ${invoice.code}</h6>
                    <small class="text-muted">العميل: ${invoice.client_name || 'عميل نقدي'}</small><br>
                    <small class="text-muted">التاريخ: ${formatDate(invoice.invoice_date)}</small>
                </div>
                <div class="text-end">
                    <span class="h5 text-success">${parseFloat(invoice.grand_total).toFixed(2)} ر.س</span>
                </div>
            </div>
        </div>
    `).join('');
    
    Swal.fire({
        title: 'اختر الفاتورة للاسترداد',
        html: `
            <div style="margin-bottom: 15px;">
                <input type="text" id="invoiceSearchInput" class="form-control" placeholder="ابحث برقم الفاتورة أو اسم العميل..." style="padding: 10px; border-radius: 8px; border: 2px solid #ddd;">
            </div>
            <div id="invoicesContainer" style="max-height: 400px; overflow-y: auto;">
                ${invoicesHtml}
            </div>
        `,
        showCancelButton: true,
        showConfirmButton: false,
        cancelButtonText: 'إلغاء',
        width: '600px',
        didOpen: () => {
            const searchInput = document.getElementById('invoiceSearchInput');
            const invoicesContainer = document.getElementById('invoicesContainer');
            
            // إضافة مستمعات للنقر على الفواتير
            function addInvoiceClickListeners() {
                document.querySelectorAll('.invoice-selection-item').forEach(item => {
                    item.addEventListener('click', function() {
                        const invoiceId = this.dataset.invoiceId;
                        loadInvoiceForReturn(invoiceId);
                        Swal.close();
                    });
                });
            }
            
            // إضافة المستمعات للفواتير الحالية
            addInvoiceClickListeners();
            
            // وظيفة البحث
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                if (!searchTerm) {
                    // عرض جميع الفواتير
                    invoicesContainer.innerHTML = invoicesHtml;
                    addInvoiceClickListeners();
                    return;
                }
                
                // فلترة الفواتير
                const filteredInvoices = availableInvoices.filter(invoice => {
                    const invoiceCode = (invoice.code || '').toLowerCase();
                    const clientName = (invoice.client_name || '').toLowerCase();
                    
                    return invoiceCode.includes(searchTerm) || clientName.includes(searchTerm);
                });
                
                // عرض النتائج المفلترة
                if (filteredInvoices.length > 0) {
                    const filteredHtml = filteredInvoices.map(invoice => `
                        <div class="invoice-selection-item" data-invoice-id="${invoice.id}" style="border: 1px solid #ddd; padding: 10px; margin-bottom: 10px; cursor: pointer; border-radius: 8px;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h6>فاتورة رقم: ${invoice.code}</h6>
                                    <small class="text-muted">العميل: ${invoice.client_name || 'عميل نقدي'}</small><br>
                                    <small class="text-muted">التاريخ: ${formatDate(invoice.invoice_date)}</small>
                                </div>
                                <div class="text-end">
                                    <span class="h5 text-success">${parseFloat(invoice.grand_total).toFixed(2)} ر.س</span>
                                </div>
                            </div>
                        </div>
                    `).join('');
                    
                    invoicesContainer.innerHTML = filteredHtml;
                } else {
                    invoicesContainer.innerHTML = `
                        <div style="text-align: center; padding: 30px; color: #666;">
                            <i class="fas fa-search" style="font-size: 2em; margin-bottom: 10px;"></i>
                            <p>لم يتم العثور على فواتير تطابق البحث</p>
                            <small>جرب البحث برقم فاتورة مختلف أو اسم العميل</small>
                        </div>
                    `;
                }
                
                // إعادة إضافة المستمعات للنتائج الجديدة
                addInvoiceClickListeners();
            });
            
            // التركيز على حقل البحث
            searchInput.focus();
        }
    });
}

// تحميل الفاتورة للاسترداد
async function loadInvoiceForReturn(invoiceId) {
    try {
        const response = await fetch(`/POS/invoice-details-for-return/${invoiceId}`, {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': appData.csrfToken,
                'Accept': 'application/json'
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.invoice) {
            displayReturnModal(data.invoice);
        } else {
            showError('فشل في تحميل تفاصيل الفاتورة');
        }
    } catch (error) {
        console.error('خطأ في تحميل الفاتورة:', error);
        showError('حدث خطأ أثناء تحميل تفاصيل الفاتورة');
    }
}

// عرض modal الاسترداد
function displayReturnModal(invoice) {
    returnItems = invoice.items.map(item => ({
        ...item,
        return_quantity: 0,
        max_quantity: item.quantity - (item.returned_quantity || 0)
    }));
    
    const itemsHtml = returnItems.map((item, index) => `
        <div class="return-item" data-index="${index}" style="border: 1px solid #ddd; padding: 15px; margin-bottom: 10px; border-radius: 8px;">
            <div class="row align-items-center">
                <div class="col-md-5">
                    <h6 class="mb-1">${item.item}</h6>
                    <small class="text-muted">السعر: ${parseFloat(item.unit_price).toFixed(2)} ر.س</small><br>
                    <small class="text-muted">الكمية الأصلية: ${item.quantity}</small><br>
                    <small class="text-muted">المتاح للاسترداد: ${item.max_quantity}</small>
                </div>
                <div class="col-md-4">
                    <div class="quantity-controls d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateReturnQuantity(${index}, -1)">-</button>
                        <input type="number" class="form-control mx-2 text-center" style="width: 80px;" 
                               value="0" min="0" max="${item.max_quantity}" 
                               onchange="updateReturnQuantity(${index}, this.value, true)">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateReturnQuantity(${index}, 1)">+</button>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <button class="btn btn-sm btn-warning" onclick="setFullReturn(${index})">استرداد كامل</button>
                    <div class="mt-2">
                        <span class="h6 text-danger" id="returnTotal-${index}">0.00 ر.س</span>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
    
    document.getElementById('returnItemsList').innerHTML = itemsHtml;
    updateReturnSummary();
    $('#returnModal').modal('show');
}

// تحديث كمية الاسترداد
function updateReturnQuantity(index, change, direct = false) {
    const item = returnItems[index];
    const input = document.querySelector(`[data-index="${index}"] input`);
    
    if (direct) {
        item.return_quantity = Math.max(0, Math.min(parseInt(change) || 0, item.max_quantity));
    } else {
        item.return_quantity = Math.max(0, Math.min(item.return_quantity + parseInt(change), item.max_quantity));
    }
    
    input.value = item.return_quantity;
    
    // حساب المجموع للعنصر
    const itemTotal = item.return_quantity * (parseFloat(item.unit_price) / 1.15); // السعر بدون ضريبة
    document.getElementById(`returnTotal-${index}`).textContent = (itemTotal * 1.15).toFixed(2) + ' ر.س';
    
    updateReturnSummary();
}

// تعيين استرداد كامل للعنصر
function setFullReturn(index) {
    updateReturnQuantity(index, returnItems[index].max_quantity, true);
}

// تحديث ملخص الاسترداد
function updateReturnSummary() {
    let totalItems = 0;
    let subtotalExcludingTax = 0;
    let totalTax = 0;
    
    returnItems.forEach(item => {
        if (item.return_quantity > 0) {
            totalItems += item.return_quantity;
            const priceExcludingTax = parseFloat(item.unit_price) / 1.15;
            const taxPerUnit = parseFloat(item.unit_price) - priceExcludingTax;
            
            subtotalExcludingTax += priceExcludingTax * item.return_quantity;
            totalTax += taxPerUnit * item.return_quantity;
        }
    });
    
    const finalTotal = subtotalExcludingTax + totalTax;
    
    document.getElementById('returnTotalItems').textContent = totalItems;
    document.getElementById('returnSubtotalAmount').textContent = subtotalExcludingTax.toFixed(2) + ' ر.س';
    document.getElementById('returnTaxAmount').textContent = totalTax.toFixed(2) + ' ر.س';
    document.getElementById('returnFinalTotal').textContent = finalTotal.toFixed(2) + ' ر.س';
}

// إضافة مستمع للزر وتأكيد الاسترداد
document.addEventListener('DOMContentLoaded', function() {
    // إضافة مستمع للزر
    document.getElementById('returnBtn').addEventListener('click', initiateReturn);
    
    // تأكيد الاسترداد
    document.getElementById('confirmReturnBtn').addEventListener('click', async function() {
        const itemsToReturn = returnItems.filter(item => item.return_quantity > 0);
        
        if (itemsToReturn.length === 0) {
            showNotification('يجب اختيار عنصر واحد على الأقل للاسترداد', 'warning');
            return;
        }
        
        const returnData = {
            invoice_id: returnItems[0].invoice_id,
            items: itemsToReturn.map(item => ({
                product_id: item.product_id,
                quantity: item.return_quantity,
                unit_price: item.unit_price,
                total: item.return_quantity * item.unit_price
            }))
        };
        
        try {
            const response = await fetch('/POS/process-return', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': appData.csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(returnData)
            });
            
            const result = await response.json();
            
            if (result.success) {
                $('#returnModal').modal('hide');
                showNotification('تم إنشاء فاتورة الاسترداد بنجاح', 'success');
                
                // فتح صفحة طباعة فاتورة الاسترداد
                if (result.return_invoice_id) {
                    const printUrl = `/POS/return_invoices/${result.return_invoice_id}/print`;
                    window.open(printUrl, '_blank', 'width=800,height=600');
                }
            } else {
                showError(result.message || 'فشل في إنشاء فاتورة الاسترداد');
            }
        } catch (error) {
            console.error('خطأ في معالجة الاسترداد:', error);
            showError('حدث خطأ أثناء معالجة الاسترداد');
        }
    });
});

// إجعل الدوال متاحة عالمياً
window.initiateReturn = initiateReturn;
window.updateReturnQuantity = updateReturnQuantity;
window.setFullReturn = setFullReturn;
        // تبديل الشاشة الكاملة
        function toggleFullscreen() {
            if (!document.fullscreenElement) {
                document.documentElement.requestFullscreen().catch(err => {
                    console.log('خطأ في الشاشة الكاملة:', err);
                });
            } else {
                document.exitFullscreen();
            }
        }

        // تحسين تجربة اللمس
        if ('ontouchstart' in window) {
            let isDown = false;
            let startX;
            let scrollLeft;

            const orderTabs = document.getElementById('orderTabs');
            
            orderTabs.addEventListener('touchstart', (e) => {
                isDown = true;
                startX = e.touches[0].pageX - orderTabs.offsetLeft;
                scrollLeft = orderTabs.scrollLeft;
            }, { passive: true });

            orderTabs.addEventListener('touchend', () => {
                isDown = false;
            }, { passive: true });

            orderTabs.addEventListener('touchmove', (e) => {
                if (!isDown) return;
                e.preventDefault();
                const x = e.touches[0].pageX - orderTabs.offsetLeft;
                const walk = (x - startX) * 2;
                orderTabs.scrollLeft = scrollLeft - walk;
            });
        }

        // إجعل الدوال متاحة عالمياً
        window.addNewOrder = addNewOrder;
        window.updateQuantity = updateQuantity;
        window.removeFromOrder = removeFromOrder;
        window.showProductInfo = showProductInfo;
        window.toggleFullscreen = toggleFullscreen;
    </script>
</body>
</html>