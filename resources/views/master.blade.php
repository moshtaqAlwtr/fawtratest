<!DOCTYPE html>
<html class="loading" dir="{{ App::getLocale() == 'ar' || App::getLocale() == 'ur' ? 'rtl' : 'ltr' }}">
<!-- BEGIN: Head-->

@if (App::getLocale() == 'ar')
    <!-- BEGIN: Head-->
    @include('layouts.head_rtl')
    <!-- END: Head-->
@elseif (App::getLocale() == 'ur')
    <!-- END: Head-->
    @include('layouts.head_rtl')
    <!-- BEGIN: Head-->
@else
    <!-- BEGIN: Head-->
    @include('layouts.head_ltr')
    <!-- END: Head-->
@endif

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.10.1/css/jquery.fileupload.css" />

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-file-upload/9.10.1/js/jquery.fileupload.js"></script>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.6.4/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.bootstrap3.min.css" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous" />

    <style>
        #DataTable_filter input {
            border-radius: 5px;
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
        }

        #location-permission-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.8);
            z-index: 9999;
            color: white;
            display: none;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }

        #location-permission-content {
            max-width: 600px;
            background: white;
            padding: 30px;
            border-radius: 10px;
            color: #333;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        }

        #location-permission-overlay h3 {
            color: #7367F0;
            margin-bottom: 20px;
        }

        #location-permission-overlay p {
            margin-bottom: 20px;
            font-size: 16px;
        }

        #location-status {
            margin: 15px 0;
        }

        .tracking-status {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 9998;
            padding: 10px 15px;
            border-radius: 5px;
            font-weight: bold;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            transition: opacity 0.5s ease;
        }

        .tracking-active {
            background-color: #28a745;
            color: white;
        }

        .tracking-inactive {
            background-color: #dc3545;
            color: white;
        }

        .tracking-paused {
            background-color: #ffc107;
            color: #212529;
        }

        .fade-out {
            opacity: 0;
        }

        /* رسالة التحميل */
        .loading-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.7);
            z-index: 9999;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            font-size: 18px;
        }

        .loading-spinner {
            border: 5px solid #f3f3f3;
            border-top: 5px solid #7367F0;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .custom-toast {
            background-color: #28a745;
            color: white;
            border-radius: 8px;
            padding: 15px 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: flex;
            align-items: center;
            max-width: 350px;
            margin: 0 auto;
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 99999;
            transition: all 0.5s ease;
            opacity: 0;
            transform: translateY(20px);
        }

        .custom-toast i {
            font-size: 24px;
            margin-left: 10px;
        }

        .custom-toast-content {
            flex: 1;
        }

        .custom-toast-title {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .custom-toast-text {
            font-size: 14px;
        }

        /* أنماط جديدة لمعالجة WebView */
        .webview-instructions {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            text-align: right;
        }

        .manual-location-form {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            display: none;
        }

        .location-alternatives {
            display: none;
            margin-top: 20px;
        }

        .detection-status {
            padding: 10px;
            border-radius: 5px;
            margin: 10px 0;
            text-align: center;
        }

        .status-webview {
            background-color: #17a2b8;
            color: white;
        }

        .status-browser {
            background-color: #28a745;
            color: white;
        }

        .status-unknown {
            background-color: #6c757d;
            color: white;
        }
    </style>
</head>

<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern 2-columns  navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="2-columns">

    <!-- رسالة التحميل -->


    <!-- طبقة حجب التطبيق حتى يتم تفعيل الموقع -->
    <div id="location-permission-overlay">
        <div id="location-permission-content">
            <h3><i class="fas fa-map-marker-alt"></i> تفعيل خدمة الموقع</h3>

            <!-- إظهار نوع البيئة -->
            <div id="environment-status" class="detection-status"></div>

            <p>يتطلب نظامنا تفعيل خدمة الموقع لتسجيل الزيارات والعملاء القريبين تلقائياً.</p>

            <ul class="text-start mb-3">
                <li>سيتم تسجيل موقعك أثناء وقت العمل فقط</li>
                <li>لن يتم مشاركة موقعك مع أي جهات خارجية</li>
                <li>يمكنك إيقاف التتبع في أي وقت من الإعدادات</li>
            </ul>

            <!-- تعليمات WebView -->
            <div id="webview-instructions" class="webview-instructions" style="display: none;">
                <h5><i class="fas fa-mobile-alt"></i> تعليمات للتطبيق</h5>
                <p>يبدو أنك تستخدم التطبيق. لتفعيل الموقع:</p>
                <ol class="text-start">
                    <li>اذهب إلى إعدادات التطبيق في جهازك</li>
                    <li>ابحث عن "الأذونات" أو "Permissions"</li>
                    <li>تأكد من تفعيل إذن "الموقع" أو "Location"</li>
                    <li>أعد تشغيل التطبيق</li>
                </ol>
                <p class="text-danger"><strong>مهم:</strong> إذا لم تظهر رسالة طلب الإذن، فقد تحتاج لتفعيل الموقع من إعدادات التطبيق مباشرة.</p>
            </div>

            <!-- نموذج الموقع اليدوي -->
            <div id="manual-location-form" class="manual-location-form">
                <h5><i class="fas fa-map-marker"></i> إدخال الموقع يدوياً</h5>
                <p>يمكنك إدخال موقعك يدوياً كحل بديل:</p>
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">خط الطول (Longitude)</label>
                        <input type="number" class="form-control" id="manual-longitude" step="any" placeholder="مثال: 46.7219">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">خط العرض (Latitude)</label>
                        <input type="number" class="form-control" id="manual-latitude" step="any" placeholder="مثال: 24.6877">
                    </div>
                </div>
                <button id="use-manual-location" class="btn btn-warning mt-3">
                    <i class="fas fa-check"></i> استخدام هذا الموقع
                </button>
                <p class="small mt-2 text-muted">يمكنك الحصول على إحداثيات موقعك من خرائط جوجل</p>
            </div>

            <div class="form-check mb-3 text-start">
                <input class="form-check-input" type="checkbox" id="remember-choice">
                <label class="form-check-label" for="remember-choice">تذكر اختياري ولا تسألني مرة أخرى</label>
            </div>

            <div class="alert alert-warning" id="location-status">جاري فحص إمكانية الوصول للموقع...</div>

            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <button id="enable-location-btn" class="btn btn-primary">
                    <i class="fas fa-check-circle"></i> موافق وتفعيل
                </button>
                <button id="retry-location-btn" class="btn btn-info" style="display: none;">
                    <i class="fas fa-redo"></i> إعادة المحاولة
                </button>
                <button id="show-alternatives-btn" class="btn btn-secondary" style="display: none;">
                    <i class="fas fa-cog"></i> خيارات أخرى
                </button>
                <button id="open-in-browser-btn" class="btn btn-success" style="display: none;">
                    <i class="fas fa-external-link-alt"></i> فتح في المتصفح
                </button>
                <button id="cancel-location-btn" class="btn btn-danger" style="display: none;">
                    <i class="fas fa-times-circle"></i> رفض (تسجيل الخروج)
                </button>
            </div>

            <!-- الخيارات البديلة -->
            <div id="location-alternatives" class="location-alternatives">
                <div class="alert alert-info">
                    <h6>خيارات بديلة:</h6>
                    <button id="manual-location-btn" class="btn btn-sm btn-warning me-2">
                        <i class="fas fa-edit"></i> إدخال الموقع يدوياً
                    </button>
                    <button id="skip-location-btn" class="btn btn-sm btn-secondary">
                        <i class="fas fa-skip-forward"></i> تخطي (بدون تتبع)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- شريط حالة التتبع -->
    <div id="tracking-status" class="tracking-status tracking-inactive" style="display: none;">
        <i class="fas fa-map-marker-alt"></i> <span id="tracking-status-text">جاري التتبع</span>
    </div>

    <!-- BEGIN: Header-->
    @include('layouts.header')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
    @include('layouts.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
    @include('layouts.footer')
    <!-- END: Footer-->

    <!-- Scripts هنا -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
    <script src="{{ asset('app-assets/vendors/js/charts/apexcharts.min.js')}}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js')}}"></script>
    <script src="{{ asset('app-assets/js/core/app.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js')}}"></script>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places"></script>
    <script src="{{ asset('app-assets/js/scripts/forms/select/form-select2.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/dashboard-ecommerce.js')}}"></script>
    <script src="{{ asset('app-assets/js/scripts/pages/app-chat.js')}}"></script>
    <script src="https://cdn.tiny.cloud/1/61l8sbzpodhm6pvdpqdk0vlb1b7wazt4fbq47y376qg6uslq/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.datatables.net/1.10.22/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.4/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/js/standalone/selectize.min.js" integrity="sha256-+C0A5Ilqmu4QcSPxrlGpaZxJ04VjsRjKu+G82kl5UJk=" crossorigin="anonymous"></script>

    <script>
        $(document).ready(function() {
            $('.loading-message').fadeOut(500);

            $('#fawtra').DataTable({
                dom: 'Bfrtip',
                "pagingType": "full_numbers",
                buttons: [
                    {
                        "extend": 'excel',
                        "text": ' اكسيل',
                        'className': 'btn btn-success fa fa-plus'
                    },
                    {
                        "extend": 'print',
                        "text": ' طباعه',
                        'className': 'btn btn-warning fa fa-print'
                    },
                    {
                        "extend": 'copy',
                        "text": ' نسخ',
                        'className': 'btn btn-info fa fa-copy'
                    }
                ],
                initComplete: function() {
                    var btns = $('.dt-button');
                    btns.removeClass('dt-button');
                },
            });

            $('').selectize({
                sortField: 'text'
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const overlay = document.getElementById('location-permission-overlay');
            const enableBtn = document.getElementById('enable-location-btn');
            const retryBtn = document.getElementById('retry-location-btn');
            const alternativesBtn = document.getElementById('show-alternatives-btn');
            const browserBtn = document.getElementById('open-in-browser-btn');
            const cancelBtn = document.getElementById('cancel-location-btn');
            const manualLocationBtn = document.getElementById('manual-location-btn');
            const skipLocationBtn = document.getElementById('skip-location-btn');
            const useManualLocationBtn = document.getElementById('use-manual-location');
            const statusElement = document.getElementById('location-status');
            const trackingStatusElement = document.getElementById('tracking-status');
            const trackingStatusText = document.getElementById('tracking-status-text');
            const rememberChoice = document.getElementById('remember-choice');
            const environmentStatus = document.getElementById('environment-status');
            const webviewInstructions = document.getElementById('webview-instructions');
            const manualLocationForm = document.getElementById('manual-location-form');
            const locationAlternatives = document.getElementById('location-alternatives');

            // متغيرات التتبع
            let watchId = null;
            let trackingInterval = null;
            let isTracking = false;
            let lastLocation = null;
            let permissionDenied = false;
            let trackingPaused = false;
            let pageRefreshInterval = null;
            let isWebView = false;
            let permissionAttempts = 0;
            let maxRetries = 3;

            // فحص البيئة (WebView أم متصفح عادي)
            function detectEnvironment() {
                const userAgent = navigator.userAgent.toLowerCase();
                const isAndroidWebView = userAgent.includes('wv') ||
                                       userAgent.includes('android') && !userAgent.includes('chrome') ||
                                       userAgent.includes('webview');
                const isIOSWebView = userAgent.includes('mobile/') && !userAgent.includes('safari');
                const isInApp = window.navigator.standalone === true ||
                               window.matchMedia('(display-mode: standalone)').matches;

                isWebView = isAndroidWebView || isIOSWebView || isInApp;

                // إظهار حالة البيئة
                if (isWebView) {
                    environmentStatus.textContent = 'تم اكتشاف: تطبيق WebView';
                    environmentStatus.className = 'detection-status status-webview';
                    webviewInstructions.style.display = 'block';
                } else {
                    environmentStatus.textContent = 'تم اكتشاف: متصفح عادي';
                    environmentStatus.className = 'detection-status status-browser';
                }

                return isWebView;
            }

            // تهيئة حالة التتبع من sessionStorage
            if (!sessionStorage.getItem('trackingState')) {
                sessionStorage.setItem('trackingState', JSON.stringify({
                    isTracking: false,
                    lastLocation: null,
                    lastUpdate: null,
                    permissionAsked: false,
                    pageAlreadyLoaded: false,
                    isManualLocation: false
                }));
            }

            // فحص حالة التتبع عند التحميل
            const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));
            if (trackingState.isTracking) {
                if (trackingState.isManualLocation) {
                    updateTrackingStatus('active', 'جاري التتبع - موقع ثابت');
                } else {
                    updateTrackingStatus('active', 'جاري التتبع - موقعك يتم تسجيله');
                }
            }

            // فحص البيئة عند التحميل
            detectEnvironment();

            // التحقق من إذن الموقع عند تحميل الصفحة
            checkLocationPermission();

            // بدء مؤقت لتحديث الصفحة
            startPageRefreshTimer();

            function startPageRefreshTimer() {
                if (pageRefreshInterval) {
                    clearInterval(pageRefreshInterval);
                }
                pageRefreshInterval = setInterval(() => {
                    location.reload();
                }, 3600000); // كل ساعة
            }

            function showToastNotification(title, text, type) {
                const toast = document.createElement('div');
                toast.className = `custom-toast toast-${type}`;
                toast.innerHTML = `
                    <div class="custom-toast-content">
                        <div class="custom-toast-title">${title}</div>
                        <div class="custom-toast-text">${text}</div>
                    </div>
                    <i class="fas fa-check-circle"></i>
                `;

                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 100);

                setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        if (document.body.contains(toast)) {
                            document.body.removeChild(toast);
                        }
                    }, 500);
                }, 5000);
            }

            function checkLocationPermission() {
                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));

                // إذا كان المستخدم قد رفض الإذن سابقاً
                if (localStorage.getItem('locationPermission') === 'denied') {
                    showPermissionDenied();
                    return;
                }

                // إذا كان المستخدم يستخدم موقعاً يدوياً
                if (localStorage.getItem('locationPermission') === 'manual') {
                    const manualLocation = JSON.parse(localStorage.getItem('manualLocation'));
                    if (manualLocation) {
                        startTrackingWithManualLocation(manualLocation);
                        return;
                    }
                }

                // إذا كان المستخدم قد وافق سابقاً
                if (localStorage.getItem('locationPermission') === 'granted') {
                    startTrackingSilently();

                    if (trackingState.isTracking) {
                        updateTrackingStatus('active', 'جاري التتبع - موقعك يتم تسجيله');
                        if (!trackingState.pageAlreadyLoaded) {
                            setTimeout(() => {
                                fadeOutTrackingStatus();
                            }, 5000);
                            trackingState.pageAlreadyLoaded = true;
                            sessionStorage.setItem('trackingState', JSON.stringify(trackingState));
                        }
                    }
                    return;
                }

                // إذا لم يتم طلب الإذن بعد في هذه الجلسة
                if (!trackingState.permissionAsked) {
                    showPermissionRequest();
                    trackingState.permissionAsked = true;
                    sessionStorage.setItem('trackingState', JSON.stringify(trackingState));
                }
            }

            function showPermissionRequest() {
                if (permissionDenied) return;

                overlay.style.display = 'flex';
                statusElement.textContent = 'جاري فحص إمكانية الوصول للموقع...';
                statusElement.className = 'alert alert-info';

                // إخفاء جميع الأزرار ما عدا التفعيل
                retryBtn.style.display = 'none';
                alternativesBtn.style.display = 'none';
                browserBtn.style.display = 'none';
                cancelBtn.style.display = 'none';
                locationAlternatives.style.display = 'none';

                enableBtn.style.display = 'block';
            }

            function showPermissionDenied() {
                overlay.style.display = 'flex';
                statusElement.textContent = 'تم رفض إذن الوصول إلى الموقع. يرجى تفعيله في إعدادات المتصفح أو التطبيق.';
                statusElement.className = 'alert alert-danger';

                // إظهار جميع الخيارات
                enableBtn.style.display = 'none';
                retryBtn.style.display = 'block';
                alternativesBtn.style.display = 'block';
                if (isWebView) {
                    browserBtn.style.display = 'block';
                }
                cancelBtn.style.display = 'block';
                permissionDenied = true;

                updateTrackingStatus('inactive', 'تم إيقاف التتبع - إذن الموقع مرفوض');
            }

            function requestLocationPermission() {
                permissionAttempts++;
                statusElement.textContent = `جاري طلب إذن الموقع... (المحاولة ${permissionAttempts})`;
                statusElement.className = 'alert alert-info';

                if (!navigator.geolocation) {
                    statusElement.textContent = 'المتصفح لا يدعم ميزة تحديد الموقع';
                    statusElement.className = 'alert alert-danger';
                    showPermissionDenied();
                    return;
                }

                // إعدادات مختلفة للحصول على الموقع حسب البيئة
                const options = isWebView ? {
                    enableHighAccuracy: false, // تقليل الدقة في WebView لتحسين التوافق
                    timeout: 15000, // مهلة أطول
                    maximumAge: 300000 // قبول مواقع أقدم (5 دقائق)
                } : {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    locationPermissionGranted,
                    locationPermissionDenied,
                    options
                );
            }

            function locationPermissionGranted(position) {
                overlay.style.display = 'none';
                permissionAttempts = 0;

                if (rememberChoice.checked) {
                    localStorage.setItem('locationPermission', 'granted');
                }

                startTracking(position);
                showToastNotification('تم تفعيل التتبع', 'سيتم الآن تسجيل موقعك تلقائياً لتسجيل الزيارات', 'success');

                setTimeout(() => {
                    fadeOutTrackingStatus();
                }, 5000);
            }

            function locationPermissionDenied(error) {
                let errorMessage = 'حدث خطأ غير معروف';
                let showAlternatives = false;

                switch(error.code) {
                    case error.PERMISSION_DENIED:
                        if (isWebView) {
                            errorMessage = 'تم رفض إذن الوصول إلى الموقع. في التطبيق، تحتاج لتفعيل إذن الموقع من إعدادات جهازك.';
                        } else {
                            errorMessage = 'تم رفض إذن الوصول إلى الموقع. يرجى تفعيله في إعدادات المتصفح.';
                        }
                        showAlternatives = true;
                        if (rememberChoice.checked) {
                            localStorage.setItem('locationPermission', 'denied');
                        }
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage = 'معلومات الموقع غير متوفرة حالياً. تأكد من تفعيل GPS.';
                        showAlternatives = true;
                        break;
                    case error.TIMEOUT:
                        if (permissionAttempts < maxRetries) {
                            errorMessage = `انتهت مهلة طلب الموقع. جاري إعادة المحاولة... (${permissionAttempts}/${maxRetries})`;
                            setTimeout(() => {
                                requestLocationPermission();
                            }, 2000);
                            return;
                        } else {
                            errorMessage = 'انتهت مهلة طلب الموقع بعد عدة محاولات. يمكنك استخدام الخيارات البديلة.';
                            showAlternatives = true;
                        }
                        break;
                }

                statusElement.textContent = errorMessage;
                statusElement.className = 'alert alert-danger';

                if (showAlternatives) {
                    showPermissionDenied();
                } else {
                    retryBtn.style.display = 'block';
                }

                updateTrackingStatus('inactive', 'تم إيقاف التتبع - خطأ في الموقع');
            }

            function startTracking(position) {
                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));

                lastLocation = {
                    latitude: position.coords.latitude,
                    longitude: position.coords.longitude
                };

                trackingState.isTracking = true;
                trackingState.lastLocation = lastLocation;
                trackingState.lastUpdate = new Date().toISOString();
                trackingState.pageAlreadyLoaded = true;
                trackingState.isManualLocation = false;
                sessionStorage.setItem('trackingState', JSON.stringify(trackingState));

                sendLocationToServer(position);

                // إعدادات مختلفة للـ watchPosition حسب البيئة
                const watchOptions = isWebView ? {
                    enableHighAccuracy: false,
                    timeout: 30000, // مهلة أطول للـ WebView
                    maximumAge: 600000 // قبول مواقع أقدم (10 دقائق)
                } : {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                watchId = navigator.geolocation.watchPosition(
                    handlePositionUpdate,
                    handleTrackingError,
                    watchOptions
                );

                // فترة إرسال أطول للـ WebView لتوفير البطارية
                const intervalDuration = isWebView ? 120000 : 60000; // دقيقتان للـ WebView، دقيقة للمتصفح

                trackingInterval = setInterval(() => {
                    if (lastLocation) {
                        sendLocationToServer({
                            coords: {
                                latitude: lastLocation.latitude,
                                longitude: lastLocation.longitude,
                                accuracy: 20
                            }
                        });
                    }
                }, intervalDuration);

                isTracking = true;
                trackingPaused = false;

                updateTrackingStatus('active', 'جاري التتبع - موقعك يتم تسجيله');
            }

            function startTrackingWithManualLocation(location) {
                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));

                lastLocation = location;

                trackingState.isTracking = true;
                trackingState.lastLocation = lastLocation;
                trackingState.lastUpdate = new Date().toISOString();
                trackingState.pageAlreadyLoaded = true;
                trackingState.isManualLocation = true;
                sessionStorage.setItem('trackingState', JSON.stringify(trackingState));

                // إرسال الموقع اليدوي للخادم
                sendLocationToServer({
                    coords: {
                        latitude: location.latitude,
                        longitude: location.longitude,
                        accuracy: 100 // دقة أقل للموقع اليدوي
                    }
                });

                // إرسال دوري للموقع اليدوي كل 5 دقائق
                trackingInterval = setInterval(() => {
                    sendLocationToServer({
                        coords: {
                            latitude: location.latitude,
                            longitude: location.longitude,
                            accuracy: 100
                        }
                    });
                }, 300000); // 5 دقائق

                isTracking = true;
                trackingPaused = false;

                updateTrackingStatus('active', 'جاري التتبع - موقع ثابت');

                setTimeout(() => {
                    fadeOutTrackingStatus();
                }, 5000);
            }

            function startTrackingSilently() {
                if (!navigator.geolocation) return;

                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));

                if (trackingState.isTracking && trackingState.lastLocation) {
                    const lastUpdate = new Date(trackingState.lastUpdate);
                    const now = new Date();
                    const minutesDiff = (now - lastUpdate) / (1000 * 60);

                    if (minutesDiff < 10) { // إذا مر أقل من 10 دقائق منذ آخر تحديث
                        lastLocation = trackingState.lastLocation;
                        isTracking = true;
                        trackingPaused = false;

                        if (!trackingState.isManualLocation) {
                            const watchOptions = isWebView ? {
                                enableHighAccuracy: false,
                                timeout: 30000,
                                maximumAge: 600000
                            } : {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            };

                            watchId = navigator.geolocation.watchPosition(
                                handlePositionUpdate,
                                handleTrackingError,
                                watchOptions
                            );
                        }

                        const intervalDuration = isWebView ? 120000 : 60000;
                        trackingInterval = setInterval(() => {
                            if (lastLocation) {
                                sendLocationToServer({
                                    coords: {
                                        latitude: lastLocation.latitude,
                                        longitude: lastLocation.longitude,
                                        accuracy: trackingState.isManualLocation ? 100 : 20
                                    }
                                });
                            }
                        }, intervalDuration);

                        if (trackingState.isManualLocation) {
                            updateTrackingStatus('active', 'جاري التتبع - موقع ثابت');
                        } else {
                            updateTrackingStatus('active', 'جاري التتبع - موقعك يتم تسجيله');
                        }

                        if (!trackingState.pageAlreadyLoaded) {
                            setTimeout(() => {
                                fadeOutTrackingStatus();
                            }, 5000);
                            trackingState.pageAlreadyLoaded = true;
                            sessionStorage.setItem('trackingState', JSON.stringify(trackingState));
                        }

                        return;
                    }
                }

                // بدء جلسة جديدة
                const options = isWebView ? {
                    enableHighAccuracy: false,
                    timeout: 15000,
                    maximumAge: 300000
                } : {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                };

                navigator.geolocation.getCurrentPosition(
                    position => {
                        startTracking(position);
                    },
                    error => {
                        console.error('خطأ في الحصول على الموقع:', error);
                        updateTrackingStatus('inactive', 'تم إيقاف التتبع - خطأ في الموقع');
                    },
                    options
                );
            }

            function handlePositionUpdate(position) {
                const { latitude, longitude } = position.coords;

                if (!lastLocation || getDistance(latitude, longitude, lastLocation.latitude, lastLocation.longitude) > 50) {
                    lastLocation = { latitude, longitude };

                    const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));
                    trackingState.lastLocation = lastLocation;
                    trackingState.lastUpdate = new Date().toISOString();
                    sessionStorage.setItem('trackingState', JSON.stringify(trackingState));

                    sendLocationToServer(position);
                }
            }

            function handleTrackingError(error) {
                console.error('خطأ في تتبع الموقع:', error);

                if (error.code === error.PERMISSION_DENIED) {
                    permissionDenied = true;
                    updateTrackingStatus('inactive', 'تم إيقاف التتبع - إذن الموقع مرفوض');

                    Swal.fire({
                        icon: 'error',
                        title: 'تم إيقاف التتبع',
                        text: 'تم سحب إذن الموقع، يرجى تحديث الصفحة ومنح الإذن مرة أخرى',
                        confirmButtonText: 'حسناً'
                    });
                }
            }

            function updateTrackingStatus(status, text) {
                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));

                trackingStatusElement.style.display = 'block';
                trackingStatusElement.classList.remove('fade-out');
                trackingStatusText.textContent = text;

                trackingStatusElement.classList.remove('tracking-active', 'tracking-inactive', 'tracking-paused');

                if (status === 'active') {
                    trackingStatusElement.classList.add('tracking-active');
                } else if (status === 'paused') {
                    trackingStatusElement.classList.add('tracking-paused');
                } else {
                    trackingStatusElement.classList.add('tracking-inactive');
                }

                if (!trackingState.pageAlreadyLoaded) {
                    setTimeout(() => {
                        fadeOutTrackingStatus();
                    }, 5000);
                }
            }

            function fadeOutTrackingStatus() {
                if (trackingStatusElement.style.display !== 'none') {
                    trackingStatusElement.classList.add('fade-out');
                    setTimeout(() => {
                        trackingStatusElement.style.display = 'none';
                    }, 500);
                }
            }

            async function sendLocationToServer(position) {
                const { latitude, longitude, accuracy } = position.coords;

                try {
                    const response = await fetch("{{ route('visits.storeLocationEnhanced') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({
                            latitude,
                            longitude,
                            accuracy: accuracy || null,
                            timestamp: new Date().toISOString(),
                            is_manual: position.coords.accuracy === 100
                        })
                    });

                    if (!response.ok) {
                        throw new Error('خطأ في الخادم');
                    }

                    const data = await response.json();

                    if (data.nearby_clients && data.nearby_clients.length > 0) {
                        console.log('العملاء القريبون:', data.nearby_clients);
                    }

                } catch (error) {
                    console.error('خطأ في إرسال الموقع:', error);
                }
            }

            function getDistance(lat1, lon1, lat2, lon2) {
                const R = 6371000;
                const φ1 = lat1 * Math.PI / 180;
                const φ2 = lat2 * Math.PI / 180;
                const Δφ = (lat2 - lat1) * Math.PI / 180;
                const Δλ = (lon2 - lon1) * Math.PI / 180;

                const a = Math.sin(Δφ / 2) * Math.sin(Δφ / 2) +
                          Math.cos(φ1) * Math.cos(φ2) *
                          Math.sin(Δλ / 2) * Math.sin(Δλ / 2);
                const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

                return R * c;
            }

            // معالجة الأزرار
            enableBtn.addEventListener('click', requestLocationPermission);

            retryBtn.addEventListener('click', () => {
                permissionAttempts = 0;
                showPermissionRequest();
                requestLocationPermission();
            });

            alternativesBtn.addEventListener('click', () => {
                locationAlternatives.style.display = locationAlternatives.style.display === 'none' ? 'block' : 'none';
            });

            browserBtn.addEventListener('click', () => {
                const currentUrl = window.location.href;
                window.open(currentUrl, '_blank');
                showToastNotification('تم فتح المتصفح', 'يتم الآن فتح التطبيق في متصفح منفصل', 'info');
            });

            cancelBtn.addEventListener('click', () => {
                window.location.href = "{{ route('logout') }}";
            });

            manualLocationBtn.addEventListener('click', () => {
                manualLocationForm.style.display = 'block';
                locationAlternatives.style.display = 'none';
            });

            skipLocationBtn.addEventListener('click', () => {
                overlay.style.display = 'none';
                updateTrackingStatus('inactive', 'التتبع معطل - تم التخطي');
                if (rememberChoice.checked) {
                    localStorage.setItem('locationPermission', 'skipped');
                }

                setTimeout(() => {
                    fadeOutTrackingStatus();
                }, 3000);
            });

            useManualLocationBtn.addEventListener('click', () => {
                const longitude = parseFloat(document.getElementById('manual-longitude').value);
                const latitude = parseFloat(document.getElementById('manual-latitude').value);

                if (!longitude || !latitude) {
                    Swal.fire({
                        icon: 'error',
                        title: 'بيانات ناقصة',
                        text: 'يرجى إدخال خط الطول والعرض بشكل صحيح',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                if (latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
                    Swal.fire({
                        icon: 'error',
                        title: 'إحداثيات غير صحيحة',
                        text: 'يرجى التأكد من صحة الإحداثيات المدخلة',
                        confirmButtonText: 'حسناً'
                    });
                    return;
                }

                const manualLocation = { latitude, longitude };

                if (rememberChoice.checked) {
                    localStorage.setItem('locationPermission', 'manual');
                    localStorage.setItem('manualLocation', JSON.stringify(manualLocation));
                }

                overlay.style.display = 'none';
                startTrackingWithManualLocation(manualLocation);

                showToastNotification('تم حفظ الموقع', 'سيتم استخدام الموقع المحدد لتسجيل الزيارات', 'success');
            });

            // أحداث لضمان استمرارية التتبع (مخففة للـ WebView)
            if (!isWebView) {
                window.addEventListener('blur', () => {
                    if (isTracking && !trackingState.isManualLocation) {
                        trackingPaused = true;
                        updateTrackingStatus('paused', 'التتبع متوقف مؤقتاً');
                    }
                });

                window.addEventListener('focus', () => {
                    if (trackingPaused && !permissionDenied) {
                        trackingPaused = false;
                        if (lastLocation) {
                            updateTrackingStatus('active', 'جاري التتبع - موقعك يتم تسجيله');
                        }
                    }
                });
            }

            window.addEventListener('beforeunload', () => {
                if (lastLocation) {
                    // إرسال إشارة إنهاء الجلسة
                    navigator.sendBeacon("{{ route('visits.storeLocationEnhanced') }}", JSON.stringify({
                        latitude: lastLocation.latitude,
                        longitude: lastLocation.longitude,
                        accuracy: 20,
                        timestamp: new Date().toISOString(),
                        isExit: true
                    }));
                }

                if (pageRefreshInterval) {
                    clearInterval(pageRefreshInterval);
                }
            });

            // فحص دوري للتتبع (كل دقيقتين للـ WebView)
            const checkInterval = isWebView ? 120000 : 60000;
            setInterval(() => {
                const trackingState = JSON.parse(sessionStorage.getItem('trackingState'));
                if (trackingState.isTracking && !isTracking && !trackingPaused && !permissionDenied) {
                    if (trackingState.isManualLocation) {
                        const manualLocation = JSON.parse(localStorage.getItem('manualLocation'));
                        if (manualLocation) {
                            startTrackingWithManualLocation(manualLocation);
                        }
                    } else {
                        startTrackingSilently();
                    }
                }
            }, checkInterval);
        });
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
