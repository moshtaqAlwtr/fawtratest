<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <!-- Meta Tags -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=2.0">
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <meta name="keywords" content="@yield('keywords', 'فوتره, برنامج محاسبة, إدارة الأعمال')" />
    <meta name="description" content="@yield('description', 'نظام فوتره لإدارة الأعمال والمحاسبة')" />
    <title>@yield('title', 'لوحة التحكم - فوتره')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('app-assets/images/logo/favicon.ico') }}" type="image/x-icon">

    <!-- CSS Files -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css">
    <link rel="stylesheet" href="{{ asset('assets/css/programs.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/businessAreas.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/dashboard.css') }}">


    <!-- Additional CSS -->
    @stack('styles')

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body class="@yield('body_class', '')">
    <div class="layout">
        @include('userguide::layouts.header')

        <!-- Main Content Area -->
        <main>
            @yield('content')
        </main>

        @include('userguide::layouts.footer')
    </div>

    <!-- Scripts -->
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script defer src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.0/js/bootstrap.min.js"></script>

    <!-- Custom Scripts -->
    @stack('scripts')

    <script>
        $(document).ready(function() {
            // Dropdown menu functionality
            $('.menu-item-dropdown').hover(
                function() {
                    $(this).find('.dropdown-content').show();
                },
                function() {
                    $(this).find('.dropdown-content').hide();
                }
            );

            // Subnav functionality
            $('.subnav').hide();

            // Check if any subnav item is active and show its parent menu
            $('.subnav li a.active').each(function() {
                $(this).closest('.subnav').show();
                $(this).closest('li').find('a.has-subnav').addClass('active-parent');
            });

            $('.quick-nav a[href="#"]').click(function(e) {
                e.preventDefault();
                $('.subnav', $(this).parent()).slideToggle('fast');
            });

            // Dropdown functionality for actions
            $('#dropdownMenuButton').click(function(e) {
                e.stopPropagation();
                $(this).siblings('.dropdown-menu').toggle();
            });

            $(document).click(function() {
                $('.dropdown-menu').hide();
            });
        });
    </script>
</body>

</html>
