<div class="header-fixed">
    <header class="header">
        <div class="container">
            <a class="logo" href="{{ route('userguide') }}">
                <img width="140" height="70" src="{{ asset('app-assets/images/logo/logo.png') }}" alt="فوتره">
            </a>

            <ul class="list-unstyled nav-links" id="navLinks">
                <li>
                    <a class="open-modules" onclick="openProgramsModalWindow()">البرامج</a>
                </li>
                <li>
                    <a class="industry-menu-link" onclick="openBusinessAreasModal()">مجالات العمل</a>
                </li>
                <li>
                    <a href="{{ route('prices') }}">الأسعار</a>
                </li>
                <li>
                    <a href="{{ route('userguide.contact.us') }}">اتصل بنا</a>
                </li>

                <li class="lang-btn order-lg-2 order-1">
                    <div class="menu-item-dropdown">
                        <button class="dropbtn">
                            EN
                            <i class="dropbtn-icon fa fa-angle-down"></i>
                        </button>
                        <div class="dropdown-content">
                            <a href="#" class="en-lang">English</a>
                        </div>
                    </div>
                </li>

                <li class="login-link order-lg-2 order-1">
                    <a href="{{ route('userguide') }}" class="login sub-btn">حسابي</a>
                </li>
                <li class="get-stated-free-link order-lg-3 order-1">
                    <a href="#" class="register main-btn">ابدأ الاستخدام مجاناً</a>
                </li>
            </ul>

            <!-- أيقونة القائمة للجوال -->
            <div class="mobile-menu-toggle" id="mobileMenuToggle">
                <i class="fa fa-bars"></i>
            </div>
        </div>
    </header>
</div>

<!-- تضمين النافذة المنبثقة لمجالات العمل -->
@include('userguide::layouts.business_areas.menu')
@include('userguide::layouts.programs.menu')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuToggle = document.getElementById('mobileMenuToggle');
        const navLinks = document.getElementById('navLinks');

        if (mobileMenuToggle && navLinks) {
            mobileMenuToggle.addEventListener('click', function() {
                navLinks.classList.toggle('active');

                // تغيير أيقونة القائمة
                const icon = this.querySelector('i');
                if (navLinks.classList.contains('active')) {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                } else {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });

            // إغلاق القائمة عند النقر خارجها
            document.addEventListener('click', function(event) {
                if (!event.target.closest('.header')) {
                    navLinks.classList.remove('active');
                    const icon = mobileMenuToggle.querySelector('i');
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                }
            });
        }

        // إضافة وظيفة القائمة المنسدلة للغة
        const languageDropdown = document.querySelector('.menu-item-dropdown');
        const dropdownBtn = document.querySelector('.dropbtn');

        if (languageDropdown && dropdownBtn) {
            // للأجهزة المحمولة والأجهزة اللوحية
            if (window.innerWidth <= 1024) {
                dropdownBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    languageDropdown.classList.toggle('active');
                });

                // إغلاق القائمة المنسدلة عند النقر خارجها
                document.addEventListener('click', function(event) {
                    if (!event.target.closest('.menu-item-dropdown')) {
                        languageDropdown.classList.remove('active');
                    }
                });

                // إغلاق القائمة المنسدلة عند اختيار لغة
                const dropdownLinks = document.querySelectorAll('.dropdown-content a');
                dropdownLinks.forEach(link => {
                    link.addEventListener('click', function() {
                        languageDropdown.classList.remove('active');
                    });
                });
            }
        }

        // إعادة تحديد السلوك عند تغيير حجم الشاشة
        window.addEventListener('resize', function() {
            if (window.innerWidth > 1024) {
                languageDropdown.classList.remove('active');
            }
        });
    });
</script>
