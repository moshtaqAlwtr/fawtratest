<footer class="section footer-rtl">
    <div class="container">
        <!-- قسم الشعار والوصف -->
        <div class="footer-brand">
            <div class="footer-logo">
                <h2>فوتره</h2>
                <div class="logo-tagline">نظام إدارة الأعمال الشامل</div>
            </div>
            <p class="footer-description">
                منصة متكاملة لإدارة أعمالك بكفاءة وسهولة، مع أدوات متقدمة للمحاسبة والمبيعات وإدارة المخزون
            </p>
            <div class="footer-stats">
                <div class="stat-item">
                    <span class="stat-number">10K+</span>
                    <span class="stat-label">عميل راضي</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">50+</span>
                    <span class="stat-label">ميزة متقدمة</span>
                </div>
                <div class="stat-item">
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">دعم فني</span>
                </div>
            </div>
        </div>

        <div class="row footer-row">
            <!-- البرامج والحلول -->
            <div class="col-md-3 col-sm-6 col-12 footer-col">
                <div class="footer__item">
                    <h3 class="footer-title">
                        <i class="fas fa-cogs"></i>
                        البرامج والحلول
                    </h3>
                    <div class="footer-links">
                        <a href="{{ route('programs.sales') }}" class="footer-link">
                            <i class="fas fa-receipt"></i>
                            <span>برنامج المبيعات والفواتير</span>
                        </a>
                        <a href="{{ route('programs.inventory.warehouses') }}" class="footer-link">
                            <i class="fas fa-boxes"></i>
                            <span>برنامج إدارة المخزون</span>
                        </a>
                        <a href="{{ route('programs.accounts') }}" class="footer-link">
                            <i class="fas fa-calculator"></i>
                            <span>برنامج الحسابات العامة</span>
                        </a>
                        <a href="{{ route('programs.customer.management') }}" class="footer-link">
                            <i class="fas fa-users"></i>
                            <span>برنامج إدارة علاقات العملاء</span>
                        </a>
                        <a href="{{ route('programs.employee.affairs') }}" class="footer-link">
                            <i class="fas fa-user-tie"></i>
                            <span>برنامج إدارة شؤون الموظفين</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- مجالات العمل -->
            <div class="col-md-3 col-sm-6 col-12 footer-col">
                <div class="footer__item">
                    <h3 class="footer-title">
                        <i class="fas fa-industry"></i>
                        مجالات العمل
                    </h3>
                    <div class="footer-links">
                        <a href="{{ route('business.retail.commercial_accounting') }}" class="footer-link">
                            <i class="fas fa-store"></i>
                            <span>المحلات التجارية</span>
                        </a>
                        <a href="{{ route('business.business.real-estate') }}" class="footer-link">
                            <i class="fas fa-truck"></i>
                            <span>المؤسسات والتوريد</span>
                        </a>
                        <a href="{{ route('business.fitness.salon') }}" class="footer-link">
                            <i class="fas fa-cut"></i>
                            <span>صالونات التجميل</span>
                        </a>
                        <a href="{{ route('business.fitness.gym') }}" class="footer-link">
                            <i class="fas fa-dumbbell"></i>
                            <span>الجيم والنوادي الصحية</span>
                        </a>
                        <a href="{{ route('business.medical.pharmacy') }}" class="footer-link">
                            <i class="fas fa-pills"></i>
                            <span>الصيدليات</span>
                        </a>
                        <a href="{{ route('business.medical.clinic') }}" class="footer-link">
                            <i class="fas fa-stethoscope"></i>
                            <span>العيادات الطبية</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- الشركة -->
            <div class="col-md-3 col-sm-6 col-12 footer-col">
                <div class="footer__item">
                    <h3 class="footer-title">
                        <i class="fas fa-building"></i>
                        الشركة
                    </h3>
                    <div class="footer-links">
                        <a href="{{ route('programs.about.fawtura') }}" class="footer-link">
                            <i class="fas fa-info-circle"></i>
                            <span>من نحن</span>
                        </a>
                        <a href="{{ route('prices') }}" class="footer-link">
                            <i class="fas fa-dollar-sign"></i>
                            <span>الأسعار</span>
                        </a>
                        <a href="{{ route('programs.fawtura.agents') }}" class="footer-link">
                            <i class="fas fa-handshake"></i>
                            <span>برنامج الشراكة</span>
                        </a>
                        <a href="{{ route('userguide.success_partners') }}" class="footer-link">
                            <i class="fas fa-award"></i>
                            <span>شركاء النجاح</span>
                        </a>
                        <a href="#" class="footer-link">
                            <i class="fas fa-graduation-cap"></i>
                            <span>المركز التعليمي</span>
                        </a>
                        <a href="{{ route('system.functions') }}" class="footer-link">
                            <i class="fas fa-briefcase"></i>
                            <span>الوظائف</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- الدعم والتواصل -->
            <div class="col-md-3 col-sm-6 col-12 footer-col">
                <div class="footer__item">
                    <h3 class="footer-title">
                        <i class="fas fa-headset"></i>
                        الدعم والتواصل
                    </h3>
                    <div class="contact-info">
                        <div class="contact-item">
                            <i class="fas fa-phone"></i>
                            <div class="contact-details">
                                <span class="contact-label">اتصل بنا</span>
                                <span class="contact-value">+966 50 123 4567</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-envelope"></i>
                            <div class="contact-details">
                                <span class="contact-label">البريد الإلكتروني</span>
                                <span class="contact-value">info@foutrah.com</span>
                            </div>
                        </div>
                        <div class="contact-item">
                            <i class="fas fa-clock"></i>
                            <div class="contact-details">
                                <span class="contact-label">ساعات العمل</span>
                                <span class="contact-value">الأحد - الخميس: 9ص - 6م</span>
                            </div>
                        </div>
                    </div>

                    <div class="footer-cta">
                        <a href="#" class="cta-button">
                            <i class="fas fa-rocket"></i>
                            ابدأ تجربتك المجانية
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- وسائل التواصل الاجتماعي -->
        <div class="social-section">
            <h3 class="social-title">تابعنا على</h3>
            <div class="social-icons">
                <a href="#" target="_blank" class="social-icon facebook" aria-label="فيسبوك">
                    <i class="fab fa-facebook-f"></i>
                    <span class="social-name">فيسبوك</span>
                </a>
                <a href="#" target="_blank" class="social-icon twitter" aria-label="تويتر">
                    <i class="fab fa-twitter"></i>
                    <span class="social-name">تويتر</span>
                </a>
                <a href="#" target="_blank" class="social-icon linkedin" aria-label="لينكد إن">
                    <i class="fab fa-linkedin-in"></i>
                    <span class="social-name">لينكد إن</span>
                </a>
                <a href="#" target="_blank" class="social-icon youtube" aria-label="يوتيوب">
                    <i class="fab fa-youtube"></i>
                    <span class="social-name">يوتيوب</span>
                </a>
                <a href="#" target="_blank" class="social-icon instagram" aria-label="إنستغرام">
                    <i class="fab fa-instagram"></i>
                    <span class="social-name">إنستغرام</span>
                </a>
            </div>
        </div>

        <!-- الشريط السفلي -->
        <div class="footer-bottom">
            <div class="footer-bottom-content">
                <div class="copyright">
                    <span>جميع الحقوق محفوظة © 2024 فوتره</span>
                    <div class="made-with">
                        صُنع بـ <i class="fas fa-heart"></i> في المملكة العربية السعودية
                    </div>
                </div>
                <div class="footer-legal">
                    <a href="#" class="legal-link">الشروط والأحكام</a>
                    <a href="#" class="legal-link">سياسة الخصوصية</a>
                    <a href="#" class="legal-link">سياسة الاسترداد</a>
                    <a href="#" class="legal-link">اتفاقية الاستخدام</a>
                </div>
            </div>
        </div>
    </div>

    <!-- عنصر تصميمي إضافي -->
    <div class="footer-decoration">
        <div class="decoration-circle circle-1"></div>
        <div class="decoration-circle circle-2"></div>
        <div class="decoration-circle circle-3"></div>
    </div>
</footer>
