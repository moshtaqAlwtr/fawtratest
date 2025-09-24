<!DOCTYPE html>
<html class="loading" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') | نظام المهام</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" sizes="16x16" href="https://altab.flowdo.net/user-uploads/favicon/6823163ef3557a2c287cd16b625019cb.png">

    <!-- CSS Libraries -->
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/all.min.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/css/lucide.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/simple-line-icons.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/bootstrap-icons.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/datepicker.min.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/bootstrap-timepicker.min.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/select2.min.css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    <!-- Google Fonts Arabic -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Google Material Icons -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,200,0,0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20,400,0,0">
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">

    <!-- Datatables -->
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/css/daterangepicker.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/datatables/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://altab.flowdo.net/vendor/datatables/buttons.bootstrap4.min.css">

    <!-- Main CSS -->
    <link rel="stylesheet" href="https://altab.flowdo.net/css/main.css?v=92469098">

    <!-- Additional CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.10.1/css/jquery.fileupload.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css">

    <style>
        :root {
            --header_color: #00DD90;
            --fc-border-color: #E8EEF3;
            --fc-button-text-color: #99A5B5;
            --fc-button-border-color: #99A5B5;
            --fc-button-bg-color: #ffffff;
            --fc-button-active-bg-color: #171f29;
            --fc-today-bg-color: #f2f4f7;
        }

        /* General RTL Styles */
        body {
            font-family: 'Tajawal', 'Cairo', sans-serif;
            text-align: right;
            direction: rtl;
        }

        ul, ol {
            padding-right: 0;
            padding-left: 20px;
        }

        table {
            text-align: right;
        }

        input, textarea, select {
            text-align: right;
        }

        .card, .float-right {
            float: left !important;
        }

        /* Header Styles */
        .main-header {
            justify-content: flex-start;
        }

        /* Footer Styles */
        .footer {
            text-align: left;
        }

        .footer .col-md-6:first-child {
            text-align: right;
        }

        /* Button Styles */
        .btn {
            text-align: right;
            padding-right: 15px;
            padding-left: 15px;
        }

        /* Dropdown Styles */
        .dropdown-menu {
            text-align: right;
            left: auto !important;
            right: 0 !important;
        }

        /* Custom styles */
        #DataTable_filter input {
            border-radius: 5px;
            margin-right: 0;
            margin-left: 10px;
        }

        button span {
            font-family: 'Cairo', sans-serif !important;
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
            margin-left: 10px;
            margin-right: 0;
        }

        .notification_box {
            margin-left: 15px;
            margin-right: 0;
        }

        .sidebar-menu {
            padding-right: 0;
        }

        .sidebar-brand-box {
            padding-right: 0;
        }

        .fc a[data-navlink], .ql-editor p {
            color: #99a5b5;
            line-height: 1.42;
        }

        .btn-primary, .btn-primary.disabled:hover, .btn-primary:disabled:hover {
            background-color: var(--header_color) !important;
            border: 1px solid var(--header_color) !important;
        }

        .text-primary { color: var(--header_color) !important; }
        .bg-primary { background: var(--header_color) !important; }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .navbar-left {
                justify-content: flex-end;
            }

            .page-header-right {
                justify-content: flex-start;
            }
        }
    </style>

    @yield('styles')
</head>

<body class="vertical-layout vertical-menu-modern 2-columns navbar-floating footer-static menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- ========== Header ========== -->
    <header class="main-header clearfix bg-white" id="header">
        <!-- Left Navbar (Mobile Menu Collapse) -->
        <div class="navbar-left float-right d-flex align-items-center">
            <!-- PAGE TITLE -->
            <div class="page-title d-none d-lg-flex">
                <div class="page-heading">
                    <h2 class="mb-0 pr-3 text-dark f-18 font-weight-bold d-flex align-items-center">
                        <span class="d-inline-block text-truncate mw-300">@yield('page-title', 'المشاريع')</span>
                    </h2>
                </div>
            </div>

            <!-- MOBILE MENU TOGGLE -->
            <div class="d-block d-lg-none menu-collapse cursor-pointer position-relative" onclick="openMobileMenu()">
                <div class="mc-wrap">
                    <div class="mcw-line"></div>
                    <div class="mcw-line center"></div>
                    <div class="mcw-line"></div>
                </div>
            </div>
        </div>

        <!-- Right Navbar (Search, Add, Notification, Logout) -->
        <div class="page-header-right left d-flex align-items-center justify-content-end">
            <!-- TIMER -->
            <span id="timer-clock">
                <span class="border rounded f-14 py-2 px-2 d-none d-sm-block mr-3">
                    <span id="active-timer" class="mr-2">00:00:00</span>
                    <a href="javascript:;" class="resume-active-timer mr-1 border-right" data-toggle="tooltip" data-original-title="استئناف">
                        <i class="fa fa-play-circle text-primary"></i>
                    </a>
                    <a href="javascript:;" class="stop-active-timer" data-toggle="tooltip" data-original-title="إيقاف الموقت">
                        <i class="fa fa-stop-circle text-danger"></i>
                    </a>
                </span>
            </span>

            <!-- NAV ITEMS -->
            <ul class="d-flex align-items-center mb-0">
                <!-- CHAT -->
                <li data-toggle="tooltip" data-placement="top" title="المحادثات" class="d-none d-sm-block">
                    <div class="d-flex align-items-center">
                        <a href="" class="d-block header-icon-box">
                            <i class="bi bi-chat-right-text f-16 text-dark-grey"></i>
                        </a>
                    </div>
                </li>

                <!-- SEARCH -->
                <li data-toggle="tooltip" data-placement="top" title="بحث" class="d-none d-sm-block">
                    <div class="d-flex align-items-center">
                        <a href="javascript:;" class="d-block header-icon-box open-search">
                            <i class="bi bi-search f-16 text-dark-grey"></i>
                        </a>
                    </div>
                </li>

                <!-- STICKY NOTES -->
                <li data-toggle="tooltip" data-placement="top" title="الملاحظات" class="d-none d-sm-block">
                    <div class="d-flex align-items-center">
                        <a href="" class="d-block header-icon-box openRightModal">
                            <i class="bi bi-sticky f-16 text-dark-grey"></i>
                        </a>
                    </div>
                </li>

                <!-- TIMER -->
                <li data-toggle="tooltip" data-placement="top" title="بدء المؤقت">
                    <div class="add_box dropdown">
                        <a class="d-block dropdown-toggle header-icon-box" type="link" id="show-active-timer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-stopwatch f-16 text-dark-grey"></i>
                            <span class="badge badge-primary active-timer-count position-absolute">0</span>
                        </a>
                    </div>
                </li>

                <!-- ADD BUTTON -->
                <li data-toggle="tooltip" data-placement="top" title="إضافة">
                    <div class="add_box dropdown">
                        <a class="d-block dropdown-toggle header-icon-box" type="link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-plus-circle f-16 text-dark-grey"></i>
                        </a>
                        <!-- DROPDOWN MENU -->
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink" tabindex="0">
                            <a class="dropdown-item f-14 text-dark openRightModal" href="">
                                <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> مشروع جديد
                            </a>
                            <a class="dropdown-item f-14 text-dark openRightModal" href="">
                                <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة مهمة
                            </a>
                            <a class="dropdown-item f-14 text-dark openRightModal" href="">
                                <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة عميل
                            </a>
                            <a class="dropdown-item f-14 text-dark openRightModal" href="">
                                <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة موظف
                            </a>
                        </div>
                    </div>
                </li>

                <!-- NOTIFICATIONS -->
                <li title="الإشعارات">
                    <div class="notification_box dropdown">
                        <a class="d-block dropdown-toggle header-icon-box show-user-notifications" type="link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-bell f-16 text-dark-grey"></i>
                            <span class="badge badge-primary unread-notifications-count position-absolute">0</span>
                        </a>
                        <!-- NOTIFICATION DROPDOWN -->
                        <div class="dropdown-menu dropdown-menu-right notification-dropdown border-0 shadow-lg py-0 bg-additional-grey" tabindex="0">
                            <div class="d-flex px-3 justify-content-between align-items-center border-bottom-grey py-1 bg-white">
                                <p class="f-14 mb-0 text-dark f-w-500">الإشعارات</p>
                                <div class="f-12">
                                    <a href="javascript:;" class="text-dark-grey mark-notification-read">تعيين كمقروء</a> |
                                    <a href="" class="text-dark-grey">عرض الكل</a>
                                </div>
                            </div>
                            <div id="notification-list" class="p-2">
                                <!-- Notifications will load here -->
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </header>

    <!-- ========== Sidebar ========== -->
    <aside class="sidebar-light">
        <div class="mobile-close-sidebar-panel w-100 h-100" onclick="closeMobileMenu()" id="mobile_close_panel"></div>
        <a id="sidebarToggle" class="text-lightest sidebarToggleBtn" href="javascript:;" style="z-index: 100;">
            <span class="material-symbols-rounded">keyboard_arrow_left</span>
        </a>

        <!-- MAIN SIDEBAR CONTENT -->
        <div class="main-sidebar d-flex flex-column align-items-center justify-content-between" id="mobile_menu_collapse">
            <!-- LOGO -->
            <div class="mt-4 mb-3 d-flex justify-content-center">
                <img src="https://altab.flowdo.net/user-uploads/app-logo/713a8c95c7cd38a61fc5092dce631cac.png"
                     height="40" width="40" style="width:40px;height:40px;border-radius:12px"
                     alt="Company Logo">
            </div>

            <!-- SIDEBAR MENU -->
            <div class="sidebar-menu h-100 w-100" id="sideMenuScroll">
                <ul class="list-unstyled">
                    <!-- DASHBOARD -->
                    <li class="accordionItem closeIt">
                        <div class="d-flex flex-column justify-content-center align-items-center my-2">
                            <a class="nav-item text-lightest f-15 sidebar-text-color" href="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M21.5 10.9V4.1C21.5 2.6 20.86 2 19.27 2H15.23C13.64 2 13 2.6 13 4.1V10.9C13 12.4 13.64 13 15.23 13H19.27C20.86 13 21.5 12.4 21.5 10.9Z"/>
                                    <path d="M11 13.1V19.9C11 21.4 10.36 22 8.77 22H4.73C3.14 22 2.5 21.4 2.5 19.9V13.1C2.5 11.6 3.14 11 4.73 11H8.77C10.36 11 11 11.6 11 13.1Z"/>
                                    <path opacity="0.4" d="M21.5 19.9V17.1C21.5 15.6 20.86 15 19.27 15H15.23C13.64 15 13 15.6 13 17.1V19.9C13 21.4 13.64 22 15.23 22H19.27C20.86 22 21.5 21.4 21.5 19.9Z"/>
                                    <path opacity="0.4" d="M11 6.9V4.1C11 2.6 10.36 2 8.77 2H4.73C3.14 2 2.5 2.6 2.5 4.1V6.9C2.5 8.4 3.14 9 4.73 9H8.77C10.36 9 11 8.4 11 6.9Z"/>
                                </svg>
                            </a>
                            <span class="f-10">اللوحة</span>
                        </div>
                    </li>

                    <!-- CLIENTS -->
                    <li class="accordionItem closeIt">
                        <div class="d-flex flex-column justify-content-center align-items-center my-2">
                            <a class="nav-item text-lightest f-15 sidebar-text-color" href="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <path opacity="0.4" d="M11.5 7.41V22H19.92C21.08 22 22.03 21.07 22.03 19.93V5.09C22.03 2.47 20.07 1.28 17.68 2.45L13.25 4.64C12.29 5.11 11.5 6.36 11.5 7.41Z"/>
                                    <path d="M2 15.05V19.5C2 20.88 3.12 22 4.5 22H11.5V10.42L11.03 10.52L6.99 11.42L6.51 11.53L4.47 11.99C3.98 12.09 3.53 12.26 3.14 12.51C3.14 12.52 3.13 12.52 3.13 12.52C3.03 12.59 2.93 12.67 2.84 12.76C2.38 13.22 2.08 13.89 2.01 14.87C2.01 14.93 2 14.99 2 15.05Z"/>
                                </svg>
                            </a>
                            <span class="f-10">العملاء</span>
                        </div>
                    </li>

                    <!-- PROJECTS -->
                    <li class="accordionItem closeIt">
                        <div class="d-flex flex-column justify-content-center align-items-center my-2">
                            <a class="nav-item text-lightest f-15 sidebar-text-color" href="">
                                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M13.3111 14.75H5.03356C3.36523 14.75 2.30189 12.9625 3.10856 11.4958L5.24439 7.60911L7.24273 3.96995C8.07689 2.45745 10.2586 2.45745 11.0927 3.96995L13.1002 7.60911L14.0627 9.35995L15.2361 11.4958C16.0427 12.9625 14.9794 14.75 13.3111 14.75Z"/>
                                    <path fill-opacity="0.3" d="M21.1667 15.2083C21.1667 18.4992 18.4992 21.1667 15.2083 21.1667C11.9175 21.1667 9.25 18.4992 9.25 15.2083C9.25 15.0525 9.25917 14.9058 9.26833 14.75H13.3108C14.9792 14.75 16.0425 12.9625 15.2358 11.4958L14.0625 9.36C14.4292 9.28666 14.8142 9.25 15.2083 9.25C18.4992 9.25 21.1667 11.9175 21.1667 15.2083Z"/>
                                </svg>
                            </a>
                            <span class="f-10">المشاريع</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- USER PROFILE SECTION -->
            <div class="sidebar-brand-box dropdown d-flex flex-column align-items-center cursor-pointer my-4">
                <div class="dropdown-toggle sidebar-brand d-flex align-items-center justify-content-between w-100"
                     type="link" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <div class="sidebar-brand-logo" style="border: 2px solid transparent;outline: 2px solid var(--header_color);">
                        <img src="{{ Auth::user()->avatar_url ?? 'https://altab.flowdo.net/user-uploads/avatar/ar/13.png' }}"
                             height="40" width="40" style="width:34px;height:35px"
                             alt="User Avatar">
                    </div>
                </div>

                <!-- USER DROPDOWN MENU -->
                <div class="dropdown-menu dropdown-menu-right sidebar-brand-dropdown ml-3"
                     aria-labelledby="dropdownMenuLink" tabindex="0">
                    <div class="d-flex justify-content-between align-items-center profile-box">
                        <div class="profileInfo d-flex align-items-center mr-1 flex-wrap">
                            <div class="profileImg mr-2">
                                <img class="h-100" src="{{ Auth::user()->avatar_url ?? 'https://altab.flowdo.net/user-uploads/avatar/ar/13.png' }}"
                                     alt="{{ Auth::user()->name }}">
                            </div>
                            <div class="ProfileData">
                                <h3 class="f-15 f-w-500 text-dark">{{ Auth::user()->name }}</h3>
                                <p class="mb-0 f-12 text-dark-grey"></p>
                            </div>
                        </div>
                        <a href="\" data-toggle="tooltip" data-original-title="الملف الشخصي">
                            <i class="side-icon bi bi-pencil-square"></i>
                        </a>
                    </div>

                    <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="">
                        <span>إرسال دعوة</span>
                        <i class="side-icon bi bi-person-plus"></i>
                    </a>

                    <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="javascript:;">
                        <label for="dark-theme-toggle">الوضع الداكن</label>
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="dark-theme-toggle">
                            <label class="custom-control-label f-14" for="dark-theme-toggle"></label>
                        </div>
                    </a>

                    <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        تسجيل خروج <i class="side-icon bi bi-power"></i>
                    </a>
                    <form id="logout-form" action="" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            </div>
        </div>

        <!-- EXPANDED SIDEBAR MENU (FOR DESKTOP) -->
        <div class="main-sidebar side-panel" style="z-index: 2;" id="mobile_menu_collapse2">
            <div class="sidebar-menu" id="sideMenuScroll">
                <div class="d-flex justify-content-between mb-3 mt-3 px-3 border-bottom">
                    <span class="f-18 font-weight-bold">القائمة الرئيسية</span>
                </div>

                <ul class="list-unstyled">
                    <li class="accordionItem closeIt">
                        <a class="nav-item text-lightest f-13 sidebar-text-color" href="" title="المشاريع">
                            <i class="bi bi-folder2-open mr-2"></i>
                            <span class="pl-3">المشاريع</span>
                        </a>
                    </li>
                    <li class="accordionItem closeIt">
                        <a class="nav-item text-lightest f-13 sidebar-text-color" href="" title="المهام">
                            <i class="bi bi-list-task mr-2"></i>
                            <span class="pl-3">المهام</span>
                        </a>
                    </li>
                    <li class="accordionItem closeIt">
                        <a class="nav-item text-lightest f-13 sidebar-text-color" href="" title="السجلات الزمنية">
                            <i class="bi bi-clock-history mr-2"></i>
                            <span class="pl-3">السجلات الزمنية</span>
                        </a>
                    </li>
                    <li class="accordionItem closeIt">
                        <a class="nav-item text-lightest f-13 sidebar-text-color" href="" title="التقارير">
                            <i class="bi bi-bar-chart-line mr-2"></i>
                            <span class="pl-3">التقارير</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </aside>

    <!-- ========== Main Content ========== -->
    <div class="body-wrapper clearfix">
        <section class="main-container bg-white mb-5 mb-sm-0" id="fullscreen">
            <div class="preloader-container d-none"></div>

            @yield('content')

        </section>
    </div>

    <!-- ========== Footer ========== -->
    <footer class="footer footer-static footer-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">© <script>document.write(new Date().getFullYear())</script> جميع الحقوق محفوظة لشركة <a href="#" target="_blank">الطيب الأفضل</a></p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">الإصدار 1.0.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- ========== JavaScript Libraries ========== -->
    <script src="https://altab.flowdo.net/vendor/jquery/jquery.min.js"></script>
    <script src="https://altab.flowdo.net/vendor/jquery/modernizr.min.js"></script>
    <script src="https://altab.flowdo.net/js/main.js?v=2"></script>
    <script src="https://altab.flowdo.net/js/dropbox.js"></script>
    <script src="https://altab.flowdo.net/js/google_drive.js"></script>
    <script type="text/javascript" src="https://www.dropbox.com/static/api/2/dropins.js" id="dropboxjs" data-app-key="1q2bdfumra47iqs"></script>
    <script async defer src="https://apis.google.com/js/api.js" onload="gapiLoaded()"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBv1xULyRb7-Hj178eIEhkiJHSKenrwMv0&callback=showTable" async></script>
    <script async defer src="https://accounts.google.com/gsi/client" onload="gisLoaded()"></script>
    <script src="https://cdn.jsdelivr.net/npm/jquery-ui-sortable@1.0.0/jquery-ui.min.js"></script>

    <!-- Additional JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js"></script>
    <script src="https://cdn.tiny.cloud/1/61l8sbzpodhm6pvdpqdk0vlb1b7wazt4fbq47y376qg6uslq/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

    <script>
        // Mobile Menu Functions
        function openMobileMenu() {
            document.getElementById('mobile_menu_collapse').classList.add('show');
            document.getElementById('mobile_close_panel').classList.add('show');
        }

        function closeMobileMenu() {
            document.getElementById('mobile_menu_collapse').classList.remove('show');
            document.getElementById('mobile_close_panel').classList.remove('show');
        }

        // Dark Mode Toggle
        document.getElementById('dark-theme-toggle')?.addEventListener('change', function() {
            document.body.classList.toggle('dark-mode', this.checked);
            localStorage.setItem('darkMode', this.checked);
        });

        // Initialize dark mode from localStorage
        if (localStorage.getItem('darkMode') === 'true') {
            document.getElementById('dark-theme-toggle').checked = true;
            document.body.classList.add('dark-mode');
        }

        // Timer Functionality
        function updateTimer() {
            const timerElement = document.getElementById('active-timer');
            if (!timerElement) return;

            let time = timerElement.textContent.split(':');
            let hours = parseInt(time[0]);
            let minutes = parseInt(time[1]);
            let seconds = parseInt(time[2]);

            seconds++;

            if (seconds >= 60) {
                seconds = 0;
                minutes++;
            }

            if (minutes >= 60) {
                minutes = 0;
                hours++;
            }

            timerElement.textContent =
                `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        // Start timer only if element exists
        if (document.getElementById('active-timer')) {
            setInterval(updateTimer, 1000);
        }

        // DataTable Initialization with RTL support
        $(document).ready(function() {
            $('#fawtra').DataTable({
                dom: 'Bfrtip',
                "pagingType": "full_numbers",
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.10.19/i18n/Arabic.json"
                },
                buttons: [{
                        "extend": 'excel',
                        "text": '<i class="fa fa-file-excel-o"></i> اكسيل',
                        'className': 'btn btn-success'
                    },
                    {
                        "extend": 'print',
                        "text": '<i class="fa fa-print"></i> طباعه',
                        'className': 'btn btn-warning'
                    },
                    {
                        "extend": 'copy',
                        "text": '<i class="fa fa-copy"></i> نسخ',
                        'className': 'btn btn-info'
                    }
                ],
                initComplete: function() {
                    var btns = $('.dt-button');
                    btns.removeClass('dt-button');
                },
            });

            // Selectize Initialization
            $('select').selectize({
                sortField: 'text'
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
