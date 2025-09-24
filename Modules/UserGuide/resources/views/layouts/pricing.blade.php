<style>
    /* ألوان هادئة لكل باقة */

    .pricing-card.basic .card-header,
    .pricing-card.basic .category-icon,
    .pricing-card.basic .select-btn {
        background: linear-gradient(135deg, #a0c4ff 0%, #76a9fa 100%);
    }

    .pricing-card.advanced .card-header,
    .pricing-card.advanced .category-icon,
    .pricing-card.advanced .select-btn {
        background: linear-gradient(135deg, #b2f2bb 0%, #81e6d9 100%);
    }

    .pricing-card.premium .card-header,
    .pricing-card.premium .category-icon,
    .pricing-card.premium .select-btn {
        background: linear-gradient(135deg, #ffd6a5 0%, #fcbf49 100%);
    }

    .pricing-card.enterprise .card-header,
    .pricing-card.enterprise .category-icon,
    .pricing-card.enterprise .select-btn {
        background: linear-gradient(135deg, #d6bcfa 0%, #b794f4 100%);
    }

    .popular-badge {
        position: absolute;
        top: -10px;
        right: 20px;
        background: #fefcbf;
        color: #2d3748;
        padding: 8px 20px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(255, 239, 184, 0.4);
    }

    .faq-section {
        direction: rtl;
        text-align: right;
    }

    .faq-section h3 {
        text-align: center;
    }

    .container {
        direction: rtl;
        text-align: right;
    }
</style>


<div class="container">
    <!-- Header Section -->
    <div class="pricing-header">
        <div class="brand-logo">
            <i class="fas fa-receipt"></i>
        </div>
        <h1 class="pricing-title">أدر أعمالك باحترافية</h1>
        <p class="pricing-subtitle">اختر الباقة المناسبة واحصل على جميع المزايا</p>

        <div class="features-list">
            <div class="feature-item">
                <i class="fas fa-headset"></i>
                دعم فني 24/7
            </div>
            <div class="feature-item">
                <i class="fas fa-sync-alt"></i>
                تحديثات مستمرة
            </div>
            <div class="feature-item">
                <i class="fas fa-shield-alt"></i>
                حماية البيانات
            </div>
        </div>

        <!-- Payment Toggle -->
        <div class="payment-toggle">
            <div class="toggle-container">
                <button class="toggle-option active" id="monthlyBtn" onclick="togglePayment('monthly')">
                    دفع شهري
                </button>
                <button class="toggle-option" id="yearlyBtn" onclick="togglePayment('yearly')">
                    دفع سنوي (خصم 30%)
                </button>
            </div>
        </div>
    </div>

    <!-- Pricing Cards -->
    <div class="pricing-grid">
        <!-- Basic Plan -->
        <div class="pricing-card basic">
            <div class="card-header">
                <h3 class="plan-name">الباقة الأساسية</h3>
                <div class="price-section">
                    <span class="price monthly-price">99</span>
                    <span class="price yearly-price" style="display: none;">990</span>
                    <span class="period monthly-period">ريال/شهرياً</span>
                    <span class="period yearly-period" style="display: none;">ريال/سنوياً</span>
                </div>
                <p class="yearly-note">موفر 17% عند الدفع سنوياً</p>
            </div>
            <div class="card-body">
                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        إدارة العملاء
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>حتى 500 عميل</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>قاعدة بيانات العملاء</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-times feature-icon limit"></i>
                            <span>تحليل سلوك العملاء</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        المخزون والمشتريات
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>إدارة المخزون الأساسي</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>مستودع واحد فقط</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-times feature-icon limit"></i>
                            <span>تنبؤ بالطلب</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        الحسابات والمالية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>فواتير أساسية</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقارير مالية بسيطة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>خزينة واحدة</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        الموارد البشرية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>حتى 3 موظفين</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>بيانات الموظفين الأساسية</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="select-btn" onclick="selectPlan('basic')">اختر الباقة</button>
            </div>
        </div>

        <!-- Advanced Plan -->
        <div class="pricing-card advanced">
            <div class="popular-badge">الأكثر شعبية</div>
            <div class="card-header">
                <h3 class="plan-name">الباقة المتقدمة</h3>
                <div class="price-section">
                    <span class="price monthly-price">199</span>
                    <span class="price yearly-price" style="display: none;">1990</span>
                    <span class="period monthly-period">ريال/شهرياً</span>
                    <span class="period yearly-period" style="display: none;">ريال/سنوياً</span>
                </div>
                <p class="yearly-note">موفر 17% عند الدفع سنوياً</p>
            </div>
            <div class="card-body">
                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        إدارة العملاء
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>عملاء غير محدود</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>أتمتة التسويق الأساسي</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تحليل سلوك العملاء</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        المخزون والمشتريات
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>مخزون ذكي</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تنبؤ بالطلب</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>حتى 3 مستودعات</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        الحسابات والمالية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>محاسبة متقدمة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقارير مالية شاملة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>حتى 3 خزائن</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        الموارد البشرية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-info-circle feature-icon info"></i>
                            <span>حتى 10 موظفين</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقييم الأداء</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>إدارة الحضور</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        مزايا إضافية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>API محدود</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>دعم إيميل</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="select-btn" onclick="selectPlan('advanced')">اختر الباقة</button>
            </div>
        </div>

        <!-- Premium Plan -->
        <div class="pricing-card premium">
            <div class="card-header">
                <h3 class="plan-name">الباقة الشاملة</h3>
                <div class="price-section">
                    <span class="price monthly-price">399</span>
                    <span class="price yearly-price" style="display: none;">3990</span>
                    <span class="period monthly-period">ريال/شهرياً</span>
                    <span class="period yearly-period" style="display: none;">ريال/سنوياً</span>
                </div>
                <p class="yearly-note">موفر 17% عند الدفع سنوياً</p>
            </div>
            <div class="card-body">
                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        إدارة العملاء
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>عملاء غير محدود</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>أتمتة التسويق المتقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تحليل متقدم للعملاء</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>CRM متكامل</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        المخزون والمشتريات
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>مخزون ذكي متقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تنبؤ بالطلب بالذكاء الاصطناعي</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>مستودعات غير محدودة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>إدارة متعددة المواقع</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        الحسابات والمالية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>محاسبة احترافية</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقارير مالية متقدمة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>مراكز تكلفة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>خزائن غير محدودة</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        الموارد البشرية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>موظفين غير محدود</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>إدارة شاملة للموارد البشرية</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقييم الأداء المتقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>كشوف الرواتب</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-globe"></i>
                        </div>
                        مزايا إضافية
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>API كامل</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تكامل متقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>دعم أولوية 24/7</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تدريب مخصص</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="select-btn" onclick="selectPlan('premium')">اختر الباقة</button>
            </div>
        </div>

        <!-- Enterprise Plan -->
        <div class="pricing-card enterprise">
            <div class="card-header">
                <h3 class="plan-name">باقة المؤسسات</h3>
                <div class="price-section">
                    <span class="price monthly-price">تواصل معنا</span>
                    <span class="price yearly-price" style="display: none;">تواصل معنا</span>
                    <span class="period monthly-period">حسب الاحتياج</span>
                    <span class="period yearly-period" style="display: none;">حسب الاحتياج</span>
                </div>
                <p class="yearly-note">أسعار مخصصة للمؤسسات الكبيرة</p>
            </div>
            <div class="card-body">
                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        إدارة العملاء
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>جميع مزايا الباقة الشاملة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تخصيص كامل للنظام</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تكامل مع الأنظمة الخارجية</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        الأمان والامتثال
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>امتثال للمعايير الدولية</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تشفير متقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>نسخ احتياطي متعدد</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        التخصيص والدعم
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>مدير حساب مخصص</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تدريب موسع للفريق</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>دعم فني مخصص</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>SLA مضمون</span>
                        </div>
                    </div>
                </div>

                <div class="feature-category">
                    <div class="category-title">
                        <div class="category-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        التحليلات المتقدمة
                    </div>
                    <div class="compact-features">
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تقارير مخصصة</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>ذكاء أعمال متقدم</span>
                        </div>
                        <div class="compact-feature">
                            <i class="fas fa-check feature-icon check"></i>
                            <span>تحليل بيانات ضخمة</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button class="select-btn" onclick="selectPlan('enterprise')">تواصل معنا</button>
            </div>
        </div>

        <!-- Additional Information -->
        <div class="additional-info">
            <div class="info-grid">
                <div class="info-card">
                    <h4><i class="fas fa-cloud" style="color: #667eea; margin-left: 8px;"></i>المساحة التخزينية
                    </h4>
                    <p>
                        <strong>الأساسية:</strong> 5 جيجا |
                        <strong>المتقدمة:</strong> 10 جيجا |
                        <strong>الشاملة:</strong> 20 جيجا
                        <br>
                        <small>مساحة إضافية متاحة بـ 18.75 ريال لكل 10 جيجا شهرياً</small>
                    </p>
                </div>
                <div class="info-card">
                    <h4><i class="fas fa-users-cog" style="color: #48bb78; margin-left: 8px;"></i>المستخدمين
                        الإضافيين</h4>
                    <p>
                        يمكنك إضافة مستخدمين إضافيين لجميع الباقات بتكلفة <strong>26.25 ريال شهرياً</strong> لكل
                        مستخدم إضافي
                        <br>
                        <small>الفروع الإضافية متاحة بـ 155 ريال شهرياً لكل فرع</small>
                    </p>
                </div>
                <div class="info-card">
                    <h4><i class="fas fa-headset" style="color: #f093fb; margin-left: 8px;"></i>الدعم الفني</h4>
                    <p>
                        دعم فني متاح 24/7 لجميع الباقات، مع أولوية للباقة الشاملة
                        <br>
                        <small>تدريب مخصص وإعداد مجاني للباقة الشاملة</small>
                    </p>
                </div>
                <div class="info-card">
                    <h4><i class="fas fa-rocket" style="color: #667eea; margin-left: 8px;"></i>ابدأ الآن</h4>
                    <p>
                        تسجيل سهل وسريع، تفعيل فوري للحساب
                        <br>
                        <small>فترة تجريبية مجانية لمدة 14 يوم لجميع الباقات</small>
                    </p>
                </div>
            </div>
        </div>

        <!-- FAQ Section -->
        <div class="faq-section"
            style="margin-top: 30px; background: white; border-radius: 15px; padding: 25px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);">
            <h3 style="text-align: center; color: #2d3748; margin-bottom: 20px; font-size: 1.5rem;">الأسئلة الشائعة
            </h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px;">
                <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-right: 4px solid #667eea;">
                    <h5 style="color: #2d3748; margin-bottom: 8px;">هل يمكنني تغيير الباقة لاحقاً؟</h5>
                    <p style="color: #718096; font-size: 14px; margin: 0;">نعم، يمكنك الترقية أو الانتقال بين
                        الباقات في أي وقت</p>
                </div>
                <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-right: 4px solid #48bb78;">
                    <h5 style="color: #2d3748; margin-bottom: 8px;">هل البيانات محمية؟</h5>
                    <p style="color: #718096; font-size: 14px; margin: 0;">نعم، جميع البيانات مشفرة ومحمية بأعلى
                        معايير الأمان</p>
                </div>
                <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-right: 4px solid #f093fb;">
                    <h5 style="color: #2d3748; margin-bottom: 8px;">هل يوجد فترة تجريبية؟</h5>
                    <p style="color: #718096; font-size: 14px; margin: 0;">نعم، فترة تجريبية مجانية لمدة 14 يوم
                        لجميع الباقات</p>
                </div>
                <div style="padding: 15px; background: #f8fafc; border-radius: 10px; border-right: 4px solid #667eea;">
                    <h5 style="color: #2d3748; margin-bottom: 8px;">كيف يتم الدفع؟</h5>
                    <p style="color: #718096; font-size: 14px; margin: 0;">عبر البطاقات الائتمانية، التحويل البنكي،
                        أو المحافظ الرقمية</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPaymentType = 'monthly';

        function togglePayment(type) {
            currentPaymentType = type;

            // Update button states
            document.getElementById('monthlyBtn').classList.remove('active');
            document.getElementById('yearlyBtn').classList.remove('active');
            document.getElementById(type + 'Btn').classList.add('active');

            // Update prices and periods
            const monthlyPrices = document.querySelectorAll('.monthly-price');
            const yearlyPrices = document.querySelectorAll('.yearly-price');
            const monthlyPeriods = document.querySelectorAll('.monthly-period');
            const yearlyPeriods = document.querySelectorAll('.yearly-period');
            const yearlyNotes = document.querySelectorAll('.yearly-note');

            if (type === 'yearly') {
                monthlyPrices.forEach(el => el.style.display = 'none');
                monthlyPeriods.forEach(el => el.style.display = 'none');
                yearlyPrices.forEach(el => el.style.display = 'inline');
                yearlyPeriods.forEach(el => el.style.display = 'inline');
                yearlyNotes.forEach(el => el.style.display = 'block');
            } else {
                monthlyPrices.forEach(el => el.style.display = 'inline');
                monthlyPeriods.forEach(el => el.style.display = 'inline');
                yearlyPrices.forEach(el => el.style.display = 'none');
                yearlyPeriods.forEach(el => el.style.display = 'none');
                yearlyNotes.forEach(el => el.style.display = 'none');
            }
        }

        function selectPlan(planType) {
            const planNames = {
                'basic': 'الأساسية',
                'advanced': 'المتقدمة',
                'premium': 'الشاملة'
            };

            const paymentText = currentPaymentType === 'yearly' ? 'سنوياً' : 'شهرياً';

            alert(`تم اختيار الباقة ${planNames[planType]} - دفع ${paymentText}\n\nسيتم توجيهك لإكمال عملية التسجيل...`);
        }

        // Animation on scroll
        function animateOnScroll() {
            const cards = document.querySelectorAll('.pricing-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach((entry, index) => {
                    if (entry.isIntersecting) {
                        setTimeout(() => {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }, index * 100);
                    }
                });
            });

            cards.forEach(card => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
                card.style.transition = 'all 0.6s ease';
                observer.observe(card);
            });
        }

        // Initialize animations when page loads
        document.addEventListener('DOMContentLoaded', function() {
            animateOnScroll();
        });
    </script>
