<!DOCTYPE html>
<html class="loading" lang="ar" data-textdirection="rtl">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="فوترة - نظام إدارة الأعمال">
    <meta name="keywords" content="فوترة, إدارة, أعمال">
    <meta name="author" content="فوترة">
    <link rel="apple-touch-icon" href="{{ asset('app-assets/images/ico/apple-icon-120.png') }}">
    <link rel="shortcut icon" type="image/x-icon" href="{{ asset('app-assets/images/ico/favicon.ico') }}">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- CSS Files -->
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/vendors/css/vendors-rtl.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/bootstrap.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/bootstrap-extended.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/colors.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/components.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/themes/dark-layout.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/themes/semi-dark-layout.css') }}">
    <link rel="stylesheet" type="text/css"
        href="{{ asset('app-assets/css-rtl/core/menu/menu-types/vertical-menu.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/core/colors/palette-gradient.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/pages/authentication.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('app-assets/css-rtl/custom-rtl.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style-rtl.css') }}">
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

        /* تقليص المسافات */
        .responsive-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 15px;
        }

        .responsive-buttons .btn {
            flex: 1;
            min-width: 120px;
            padding: 8px 15px;
            font-size: 14px;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .responsive-buttons {
                flex-direction: column;
                gap: 6px;
            }

            .responsive-buttons .btn {
                width: 100%;
            }
        }

        .form-row {
            display: flex;
            gap: 10px;
            margin-bottom: 0.8rem;
        }

        .form-row .form-label-group {
            flex: 1;
        }

        /* تقليص المساحات الداخلية */
        .card-body {
            padding: 1rem !important;
        }

        .form-label-group {
            margin-bottom: 0.8rem !important;
        }

        .form-control {
            padding: 8px 40px 8px 12px !important;
            font-size: 14px !important;
        }

        @media (max-width: 768px) {
            .form-row {
                flex-direction: column;
                gap: 0;
            }

            .card-body {
                padding: 0.8rem !important;
            }
        }

        .password-toggle {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #6c757d;
            cursor: pointer;
            z-index: 5;
        }

        .password-toggle:hover {
            color: #007bff;
        }

        .checkbox-container {
            display: flex;
            align-items: flex-start;
            gap: 8px;
            margin: 12px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 6px;
            border: 1px solid #e9ecef;
        }

        .checkbox-container input[type="checkbox"] {
            margin-top: 2px;
        }

        .checkbox-container label {
            margin-bottom: 0;
            font-size: 13px;
            line-height: 1.3;
        }

        .success-message {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
            padding: 10px 15px;
            border-radius: 6px;
            margin-bottom: 15px;
            border: 1px solid;
            display: none;
            font-size: 14px;
        }

        .card-header .brand-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            margin-bottom: 10px;
            padding: 5px 0;
        }

        .brand-logo i {
            font-size: 1.8rem;
            color: #007bff;
        }

        .brand-logo h3 {
            margin: 0;
            color: #333;
            font-weight: 700;
            font-size: 1.5rem;
        }

        .card-title {
            margin-bottom: 10px;
        }

        .card-title h4 {
            font-size: 1.3rem;
        }

        .card-header {
            padding-bottom: 0.5rem;
        }

        .px-0 {
            margin-bottom: 15px !important;
        }

        .login-footer {
            margin-top: 15px;
        }

        .divider {
            margin: 15px 0;
        }

        .auth-footer-btn {
            gap: 8px;
        }

        .auth-footer-btn .btn {
            padding: 8px 12px;
        }
    </style>
</head>

<body
    class="vertical-layout vertical-menu-modern 1-column navbar-floating footer-static bg-full-screen-image menu-collapsed blank-page blank-page">
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-wrapper">
            <div class="content-header row"></div>
            <div class="content-body">
                <section class="row flexbox-container">
                    <div class="col-xl-10 col-12 d-flex justify-content-center">
                        <div class="card bg-authentication rounded-0 mb-0"
                            style="width: 100%; max-width: 1000px; margin:20px;">
                            <div class="row m-0">
                                <div class="col-lg-6 d-lg-block d-none text-center align-self-center px-1 py-0">
                                    <img src="{{ asset('app-assets/images/pages/login.png') }}" alt="branding logo">
                                </div>
                                <div class="col-lg-6 col-12 p-0">
                                    <div class="card rounded-0 mb-0 px-4 py-3">
                                        <div class="card-header pb-1">
                                            <div class="brand-logo">
                                                <i class="feather icon-file-text"></i>
                                                <h3>فوترة</h3>
                                            </div>
                                            <div class="card-title">
                                                <h4 class="mb-0">إنشاء حساب جديد</h4>
                                            </div>
                                        </div>
                                        <p class="px-0 mb-3">انضم إلينا اليوم وابدأ في إدارة أعمالك بكفاءة أكبر.</p>

                                        <div class="success-message" id="successMessage">
                                            <i class="feather icon-check-circle"></i>
                                            تم إنشاء الحساب بنجاح! مرحباً بك في فوترة.
                                        </div>

                                        @if ($errors->any())
                                            <div class="alert alert-danger">
                                                <ul class="mb-0">
                                                    @foreach ($errors->all() as $error)
                                                        <li>{{ $error }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif

                                        <div class="card-content">
                                            <div class="card-body pt-1">
                                                <form action="#" method="POST" id="registerForm">
                                                    @csrf

                                                    <!-- الاسم الأول والأخير -->
                                                    <div class="form-row">
                                                        <fieldset
                                                            class="form-label-group form-group position-relative has-icon-left">
                                                            <input type="text" name="first_name" class="form-control"
                                                                id="firstName" placeholder="الاسم الأول" required>
                                                            <div class="form-control-position">
                                                                <i class="feather icon-user"></i>
                                                            </div>
                                                            <label for="firstName">الاسم الأول</label>
                                                        </fieldset>

                                                        <fieldset
                                                            class="form-label-group form-group position-relative has-icon-left">
                                                            <input type="text" name="last_name"
                                                                class="form-control" id="lastName"
                                                                placeholder="الاسم الأخير" required>
                                                            <div class="form-control-position">
                                                                <i class="feather icon-user"></i>
                                                            </div>
                                                            <label for="lastName">الاسم الأخير</label>
                                                        </fieldset>
                                                    </div>

                                                    <!-- البريد الإلكتروني -->
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="email" name="email" class="form-control"
                                                            id="email" placeholder="البريد الإلكتروني" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-mail"></i>
                                                        </div>
                                                        <label for="email">البريد الإلكتروني</label>
                                                    </fieldset>

                                                    <!-- رقم الهاتف -->
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <input type="tel" name="phone" class="form-control"
                                                            id="phone" placeholder="رقم الهاتف" required>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-phone"></i>
                                                        </div>
                                                        <label for="phone">رقم الهاتف</label>
                                                    </fieldset>

                                                    <!-- كلمة المرور وتأكيدها -->
                                                    <div class="form-row">
                                                        <fieldset
                                                            class="form-label-group position-relative has-icon-left">
                                                            <input type="password" name="password"
                                                                class="form-control" id="password"
                                                                placeholder="كلمة المرور" required>
                                                            <div class="form-control-position">
                                                                <i class="feather icon-lock"></i>
                                                            </div>
                                                            <button type="button" class="password-toggle"
                                                                onclick="togglePassword('password')">
                                                                <i class="feather icon-eye" id="passwordIcon"></i>
                                                            </button>
                                                            <label for="password">كلمة المرور</label>
                                                        </fieldset>

                                                        <fieldset
                                                            class="form-label-group position-relative has-icon-left">
                                                            <input type="password" name="password_confirmation"
                                                                class="form-control" id="confirmPassword"
                                                                placeholder="تأكيد كلمة المرور" required>
                                                            <div class="form-control-position">
                                                                <i class="feather icon-lock"></i>
                                                            </div>
                                                            <button type="button" class="password-toggle"
                                                                onclick="togglePassword('confirmPassword')">
                                                                <i class="feather icon-eye"
                                                                    id="confirmPasswordIcon"></i>
                                                            </button>
                                                            <label for="confirmPassword">تأكيد كلمة المرور</label>
                                                        </fieldset>
                                                    </div>

                                                    <!-- البلد -->
                                                    <fieldset
                                                        class="form-label-group form-group position-relative has-icon-left">
                                                        <select name="country" class="form-control" id="country"
                                                            required>
                                                            <option value="">اختر البلد</option>
                                                            <option value="SA">السعودية</option>
                                                            <option value="AE">الإمارات العربية المتحدة</option>
                                                            <option value="EG">مصر</option>
                                                            <option value="JO">الأردن</option>
                                                            <option value="KW">الكويت</option>
                                                            <option value="QA">قطر</option>
                                                            <option value="BH">البحرين</option>
                                                            <option value="OM">عمان</option>
                                                        </select>
                                                        <div class="form-control-position">
                                                            <i class="feather icon-globe"></i>
                                                        </div>
                                                        <label for="country">البلد</label>
                                                    </fieldset>

                                                    <!-- الموافقة على الشروط -->
                                                    <div class="checkbox-container">
                                                        <input type="checkbox" name="agree_terms" id="agreeTerms"
                                                            required>
                                                        <label for="agreeTerms">
                                                            أوافق على <a href="#" class="text-primary">الشروط
                                                                والأحكام</a> و <a href="#"
                                                                class="text-primary">سياسة الخصوصية</a>
                                                        </label>
                                                    </div>

                                                    <div class="responsive-buttons">
                                                        <a href="#" class="btn btn-outline-primary"
                                                            onclick="goToLogin()">لديك حساب؟ سجل دخولك</a>
                                                        <button type="submit" class="btn btn-primary">إنشاء
                                                            الحساب</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                        <div class="login-footer">
                                            <div class="divider">
                                                <div class="divider-text">أو سجل باستخدام</div>
                                            </div>

                                            <!-- داخل الـ body -->
                                            <div class="auth-footer-btn d-flex justify-content-center">
                                                <a href="#" class="btn btn-facebook"
                                                    onclick="socialRegister('facebook')">
                                                    <i class="fab fa-facebook-f"></i> تسجيل عبر Facebook
                                                </a>
                                                <a href="#" class="btn btn-google"
                                                    onclick="socialRegister('google')">
                                                    <i class="fab fa-google"></i> تسجيل عبر Google
                                                </a>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <!-- JavaScript Files -->
    <script src="{{ asset('app-assets/vendors/js/vendors.min.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app-menu.js') }}"></script>
    <script src="{{ asset('app-assets/js/core/app.js') }}"></script>
    <script src="{{ asset('app-assets/js/scripts/components.js') }}"></script>

    <script>
        // تبديل إظهار كلمة المرور
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + 'Icon');

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('icon-eye');
                icon.classList.add('icon-eye-off');
            } else {
                field.type = 'password';
                icon.classList.remove('icon-eye-off');
                icon.classList.add('icon-eye');
            }
        }

        // التحقق من صحة النموذج
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // التحقق من تطابق كلمة المرور
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirmPassword').value;

            if (password !== confirmPassword) {
                alert('كلمة المرور غير متطابقة');
                return;
            }

            // التحقق من طول كلمة المرور
            if (password.length < 6) {
                alert('كلمة المرور يجب أن تكون 6 أحرف على الأقل');
                return;
            }

            // التحقق من الموافقة على الشروط
            if (!document.getElementById('agreeTerms').checked) {
                alert('يجب الموافقة على الشروط والأحكام');
                return;
            }

            // محاكاة إرسال النموذج
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;

            submitBtn.innerHTML = 'جاري إنشاء الحساب...';
            submitBtn.disabled = true;

            setTimeout(() => {
                document.getElementById('successMessage').style.display = 'block';
                this.reset();
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                setTimeout(() => {
                    alert('مرحباً بك في فوترة! تم إنشاء حسابك بنجاح');
                }, 1500);
            }, 2000);
        });

        // التسجيل بالشبكات الاجتماعية
        function socialRegister(provider) {
            alert(`سيتم التسجيل باستخدام ${provider}`);
        }

        // الانتقال لصفحة تسجيل الدخول
        function goToLogin() {
            alert('سيتم التوجيه لصفحة تسجيل الدخول');
        }

        // التحقق من صحة البريد الإلكتروني أثناء الكتابة
        document.getElementById('email').addEventListener('blur', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (email && !emailRegex.test(email)) {
                this.style.borderColor = '#dc3545';
                alert('البريد الإلكتروني غير صحيح');
            } else {
                this.style.borderColor = '';
            }
        });

        // التحقق من رقم الهاتف
        document.getElementById('phone').addEventListener('input', function() {
            // إزالة أي أحرف غير رقمية عدا + و -
            this.value = this.value.replace(/[^\d+\-\s]/g, '');
        });
    </script>
</body>

</html>
