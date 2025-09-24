 <style>
        /* System Functions Unique Styles */
        .sf-wrapper {

            line-height: 1.5;
            color: #333;
            direction: rtl;
            background: #fafbfc;
        }

        .sf-wrapper * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .sf-container {
            max-width: 1000px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Hero Section */
        .sf-hero {
            background: linear-gradient(135deg, #3b82f6 0%, #6366f1 50%, #8b5cf6 100%);
            color: white;
            padding: 60px 0 40px;
            position: relative;
            overflow: hidden;
        }

        .sf-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="sf-grid" width="8" height="8" patternUnits="userSpaceOnUse"><path d="M 8 0 L 0 0 0 8" fill="none" stroke="rgba(255,255,255,0.08)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23sf-grid)"/></svg>');
        }

        .sf-hero-content {
            position: relative;
            z-index: 2;
            text-align: center;
        }

        .sf-hero-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 16px;
            letter-spacing: -0.01em;
        }

        .sf-hero-subtitle {
            font-size: 1rem;
            margin-bottom: 30px;
            opacity: 0.9;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
            line-height: 1.6;
        }

        .sf-hero-badges {
            display: flex;
            justify-content: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 30px;
        }

        .sf-hero-badge {
            background: rgba(255, 255, 255, 0.12);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            padding: 8px 16px;
            border-radius: 25px;
            font-weight: 500;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        /* Overview Section */
        .sf-overview {
            background: white;
            padding: 50px 0;
            text-align: center;
        }

        .sf-overview-title {
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 15px;
        }

        .sf-overview-subtitle {
            font-size: 0.95rem;
            color: #6b7280;
            margin-bottom: 40px;
            max-width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        .sf-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 25px;
            max-width: 600px;
            margin: 0 auto 50px;
        }

        .sf-stat-card {
            text-align: center;
            padding: 15px;
        }

        .sf-stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: #3b82f6;
            display: block;
            margin-bottom: 6px;
        }

        .sf-stat-label {
            font-size: 0.85rem;
            color: #6b7280;
            font-weight: 500;
        }

        /* Categories Section */
        .sf-categories {
            background: #f8fafc;
            padding: 50px 0;
        }

        .sf-categories-title {
            text-align: center;
            font-size: 1.8rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 40px;
        }

        .sf-categories-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
        }

        .sf-category-card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid #e5e7eb;
            position: relative;
            overflow: hidden;
            text-align:right;
        }

        .sf-category-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, #3b82f6, #6366f1, #8b5cf6);
            transform: scaleX(0);
            transform-origin: left;
            transition: transform 0.3s ease;
        }

        .sf-category-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 25px rgba(0, 0, 0, 0.1);
        }

        .sf-category-card:hover::before {
            transform: scaleX(1);
        }

        .sf-category-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
            font-size: 1.2rem;
            color: white;
        }

        .sf-category-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 12px;
        }

        .sf-category-description {
            color: #6b7280;
            margin-bottom: 16px;
            line-height: 1.5;
            font-size: 0.85rem;
        }

        .sf-features-list {
            list-style: none;
            margin-bottom: 20px;
        }

        .sf-features-list li {
            padding: 4px 0;
            color: #374151;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.8rem;
        }

        .sf-features-list li::before {
            content: '✓';
            background: #10b981;
            color: white;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            flex-shrink: 0;
        }

        .sf-view-details {
            background: linear-gradient(135deg, #3b82f6, #6366f1);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.8rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .sf-view-details:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
        }

        /* CTA Section */
        .sf-cta-section {
            background: linear-gradient(135deg, #1e40af 0%, #3730a3 100%);
            padding: 50px 0;
            text-align: center;
            color: white;
        }

        .sf-cta-content {
            max-width: 600px;
            margin: 0 auto;
        }

        .sf-cta-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 15px;
            text-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }

        .sf-cta-description {
            font-size: 0.95rem;
            margin-bottom: 30px;
            opacity: 0.9;
            line-height: 1.5;
        }

        .sf-cta-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .sf-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .sf-btn-primary {
            background: rgba(255,255,255,0.15);
            color: white;
            border-color: rgba(255,255,255,0.2);
        }

        .sf-btn-primary:hover {
            background: white;
            color: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        .sf-btn-secondary {
            background: transparent;
            color: white;
            border-color: white;
        }

        .sf-btn-secondary:hover {
            background: white;
            color: #1e40af;
            transform: translateY(-1px);
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sf-hero-title {
                font-size: 1.8rem;
            }
            
            .sf-hero-subtitle {
                font-size: 0.9rem;
            }
            
            .sf-categories-grid {
                grid-template-columns: 1fr;
            }
            
            .sf-category-card {
                padding: 20px 15px;
            }
            
            .sf-hero-badges {
                flex-direction: column;
                align-items: center;
            }
            
            .sf-cta-buttons {
                flex-direction: column;
                align-items: center;
            }
            
            .sf-btn {
                width: 100%;
                max-width: 250px;
                justify-content: center;
            }
        }
    </style>

    <div class="sf-wrapper">
        <!-- Hero Section -->
        <section class="sf-hero">
            <div class="sf-container">
                <div class="sf-hero-content">
                    <h1 class="sf-hero-title">وظائف النظام</h1>
                    <p class="sf-hero-subtitle">
                        اكتشف القوة الكاملة لنظام دفترة المحاسبي الشامل. حلول متكاملة تغطي جميع جوانب إدارة الأعمال بأحدث
                        التقنيات وأعلى معايير الأمان والجودة.
                    </p>

                    <div class="sf-hero-badges">
                        <div class="sf-hero-badge">
                            <i class="fas fa-shield-alt"></i>
                            <span>آمن ومعتمد</span>
                        </div>
                        <div class="sf-hero-badge">
                            <i class="fas fa-cloud"></i>
                            <span>سحابي بالكامل</span>
                        </div>
                        <div class="sf-hero-badge">
                            <i class="fas fa-mobile-alt"></i>
                            <span>متجاوب مع الجوال</span>
                        </div>
                        <div class="sf-hero-badge">
                            <i class="fas fa-clock"></i>
                            <span>متاح 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Overview Section -->
        <section class="sf-overview">
            <div class="sf-container">
                <h2 class="sf-overview-title">النظام الأكثر شمولية</h2>
                <p class="sf-overview-subtitle">
                    يضم نظام دفترة مئات الميزات المتقدمة والمتكاملة لتلبية كافة احتياجات الشركات من جميع الأحجام والقطاعات
                </p>

                <div class="sf-stats-grid">
                    <div class="sf-stat-card">
                        <span class="sf-stat-number">200+</span>
                        <div class="sf-stat-label">ميزة متقدمة</div>
                    </div>
                    <div class="sf-stat-card">
                        <span class="sf-stat-number">12</span>
                        <div class="sf-stat-label">وحدة أساسية</div>
                    </div>
                    <div class="sf-stat-card">
                        <span class="sf-stat-number">50+</span>
                        <div class="sf-stat-label">تقرير تفصيلي</div>
                    </div>
                    <div class="sf-stat-card">
                        <span class="sf-stat-number">99.9%</span>
                        <div class="sf-stat-label">وقت التشغيل</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories Section -->
        <section class="sf-categories">
            <div class="sf-container">
                <h2 class="sf-categories-title">الوحدات الرئيسية للنظام</h2>

                <div class="sf-categories-grid">
                    <!-- Accounting Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-calculator"></i>
                        </div>
                        <h3 class="sf-category-title">المحاسبة العامة</h3>
                        <p class="sf-category-description">
                            نظام محاسبة شامل ومتقدم يتوافق مع المعايير المحلية والدولية، مع أتمتة كاملة للعمليات المحاسبية.
                        </p>
                        <ul class="sf-features-list">
                            <li>دليل الحسابات التلقائي</li>
                            <li>القيود اليومية الذكية</li>
                            <li>القوائم المالية المعتمدة</li>
                            <li>مراكز التكلفة</li>
                            <li>العملات المتعددة</li>
                        </ul>
                        <a href="#accounting-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Invoicing Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-file-invoice-dollar"></i>
                        </div>
                        <h3 class="sf-category-title">الفوترة الإلكترونية</h3>
                        <p class="sf-category-description">
                            إصدار فواتير إلكترونية معتمدة ومتوافقة مع متطلبات الجهات الحكومية في جميع دول المنطقة.
                        </p>
                        <ul class="sf-features-list">
                            <li>فواتير معتمدة بالتوقيع الرقمي</li>
                            <li>رموز QR تلقائية</li>
                            <li>حساب الضرائب المتعددة</li>
                            <li>قوالب قابلة للتخصيص</li>
                            <li>إرسال آلي للعملاء</li>
                        </ul>
                        <a href="#invoicing-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Inventory Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <h3 class="sf-category-title">إدارة المخزون</h3>
                        <p class="sf-category-description">
                            نظام مخزون ذكي مع تتبع دقيق للمنتجات والكميات، وتنبيهات آلية لضمان استمرارية المخزون.
                        </p>
                        <ul class="sf-features-list">
                            <li>تتبع بالسيريال والدفعات</li>
                            <li>مستودعات متعددة</li>
                            <li>نظام باركود متقدم</li>
                            <li>تنبيهات المخزون الذكية</li>
                            <li>عمليات جرد آلية</li>
                        </ul>
                        <a href="#inventory-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Sales Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <h3 class="sf-category-title">المبيعات ونقاط البيع</h3>
                        <p class="sf-category-description">
                            واجهة مبيعات سريعة ومرنة مع نظام نقاط بيع متطور يدعم جميع طرق الدفع الحديثة.
                        </p>
                        <ul class="sf-features-list">
                            <li>واجهة POS سريعة</li>
                            <li>طرق دفع متعددة</li>
                            <li>برامج ولاء العملاء</li>
                            <li>تطبيق مبيعات للجوال</li>
                            <li>تحليلات مبيعات متقدمة</li>
                        </ul>
                        <a href="#sales-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Purchases Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <h3 class="sf-category-title">إدارة المشتريات</h3>
                        <p class="sf-category-description">
                            نظام مشتريات متكامل لإدارة الموردين وطلبات الشراء مع تتبع شامل لدورة الشراء.
                        </p>
                        <ul class="sf-features-list">
                            <li>طلبات شراء إلكترونية</li>
                            <li>إدارة الموردين الشاملة</li>
                            <li>مقارنة عروض الأسعار</li>
                            <li>تتبع التسليم والشحنات</li>
                            <li>تحليل أداء الموردين</li>
                        </ul>
                        <a href="#purchases-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- HR Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <h3 class="sf-category-title">الموارد البشرية</h3>
                        <p class="sf-category-description">
                            إدارة شاملة للموظفين تشمل الحضور والانصراف والرواتب والتقييمات والتطوير المهني.
                        </p>
                        <ul class="sf-features-list">
                            <li>ملفات موظفين شاملة</li>
                            <li>نظام حضور متقدم</li>
                            <li>حساب رواتب آلي</li>
                            <li>إدارة الإجازات</li>
                            <li>تقييم الأداء</li>
                        </ul>
                        <a href="#hr-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Reports Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-chart-bar"></i>
                        </div>
                        <h3 class="sf-category-title">التقارير والتحليلات</h3>
                        <p class="sf-category-description">
                            لوحة معلومات تفاعلية مع تقارير شاملة وتحليلات ذكية لدعم اتخاذ القرارات الاستراتيجية.
                        </p>
                        <ul class="sf-features-list">
                            <li>لوحة معلومات تفاعلية</li>
                            <li>تقارير مالية معتمدة</li>
                            <li>تحليلات المبيعات</li>
                            <li>التقارير الضريبية</li>
                            <li>تصدير متعدد الصيغ</li>
                        </ul>
                        <a href="#reports-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Financial Management Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <h3 class="sf-category-title">الإدارة المالية</h3>
                        <p class="sf-category-description">
                            أدوات متقدمة لإدارة السيولة والتدفقات النقدية والتخطيط المالي والتحليل الاستراتيجي.
                        </p>
                        <ul class="sf-features-list">
                            <li>إدارة البنوك والحسابات</li>
                            <li>تتبع التدفقات النقدية</li>
                            <li>الميزانيات والتخطيط</li>
                            <li>إدارة الأصول الثابتة</li>
                            <li>التحليل المالي المتقدم</li>
                        </ul>
                        <a href="#financial-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- CRM Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-handshake"></i>
                        </div>
                        <h3 class="sf-category-title">إدارة علاقات العملاء</h3>
                        <p class="sf-category-description">
                            نظام CRM شامل لإدارة العملاء والفرص التجارية مع أدوات تسويق وخدمة عملاء متطورة.
                        </p>
                        <ul class="sf-features-list">
                            <li>قاعدة بيانات عملاء شاملة</li>
                            <li>تتبع الفرص التجارية</li>
                            <li>حملات تسويقية مستهدفة</li>
                            <li>خدمة عملاء متقدمة</li>
                            <li>برامج الولاء والمكافآت</li>
                        </ul>
                        <a href="#crm-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Projects Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-project-diagram"></i>
                        </div>
                        <h3 class="sf-category-title">إدارة المشاريع</h3>
                        <p class="sf-category-description">
                            أدوات إدارة مشاريع متطورة مع تتبع التكاليف والمهام والجدولة الزمنية وإدارة الفرق.
                        </p>
                        <ul class="sf-features-list">
                            <li>تخطيط وجدولة المشاريع</li>
                            <li>تتبع التكاليف والميزانيات</li>
                            <li>إدارة المهام والفرق</li>
                            <li>تقارير تقدم المشاريع</li>
                            <li>إدارة الموارد والمواد</li>
                        </ul>
                        <a href="#projects-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- E-commerce Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-store"></i>
                        </div>
                        <h3 class="sf-category-title">التجارة الإلكترونية</h3>
                        <p class="sf-category-description">
                            منصة تجارة إلكترونية متكاملة مع ربط المتاجر الإلكترونية وإدارة الطلبات والشحن.
                        </p>
                        <ul class="sf-features-list">
                            <li>متجر إلكتروني متكامل</li>
                            <li>ربط منصات البيع المختلفة</li>
                            <li>إدارة الطلبات والشحن</li>
                            <li>بوابات دفع متعددة</li>
                            <li>تتبع المخزون الإلكتروني</li>
                        </ul>
                        <a href="#ecommerce-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Manufacturing Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-industry"></i>
                        </div>
                        <h3 class="sf-category-title">الإنتاج والتصنيع</h3>
                        <p class="sf-category-description">
                            نظام إدارة الإنتاج والتصنيع مع تخطيط الموارد وتتبع العمليات الإنتاجية وإدارة الجودة.
                        </p>
                        <ul class="sf-features-list">
                            <li>تخطيط الإنتاج والجدولة</li>
                            <li>إدارة أوامر التصنيع</li>
                            <li>تتبع المواد الخام</li>
                            <li>مراقبة الجودة</li>
                            <li>تحليل كفاءة الإنتاج</li>
                        </ul>
                        <a href="#manufacturing-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>

                    <!-- Settings Module -->
                    <div class="sf-category-card">
                        <div class="sf-category-icon">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <h3 class="sf-category-title">الإعدادات والتخصيص</h3>
                        <p class="sf-category-description">
                            إعدادات شاملة لتخصيص النظام وإدارة المستخدمين والصلاحيات مع أدوات النسخ الاحتياطي.
                        </p>
                        <ul class="sf-features-list">
                            <li>إدارة المستخدمين والصلاحيات</li>
                            <li>تخصيص واجهة النظام</li>
                            <li>إعدادات الشركة والفروع</li>
                            <li>النسخ الاحتياطي والاستعادة</li>
                            <li>سجلات النشاط والأمان</li>
                        </ul>
                        <a href="#settings-details" class="sf-view-details">
                            <span>عرض التفاصيل</span>
                            <i class="fas fa-arrow-left"></i>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Call to Action Section -->
        <section class="sf-cta-section">
            <div class="sf-container">
                <div class="sf-cta-content">
                    <h2 class="sf-cta-title">ابدأ رحلتك مع فوترة الآن</h2>
                    <p class="sf-cta-description">
                        انضم إلى آلاف الشركات التي تثق في فوترة لإدارة أعمالها بكفاءة وفعالية
                    </p>
                    <div class="sf-cta-buttons">
                        <a href="#demo" class="sf-btn sf-btn-primary">
                            <i class="fas fa-play"></i>
                            مشاهدة العرض التوضيحي
                        </a>
                        <a href="#contact" class="sf-btn sf-btn-secondary">
                            <i class="fas fa-phone"></i>
                            تواصل معنا
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- JavaScript for enhanced interactions -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Add smooth scrolling for anchor links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Add animation on scroll for category cards
                const observerOptions = {
                    threshold: 0.1,
                    rootMargin: '0px 0px -30px 0px'
                };

                const observer = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.style.opacity = '1';
                            entry.target.style.transform = 'translateY(0)';
                        }
                    });
                }, observerOptions);

                // Observe all category cards
                document.querySelectorAll('.sf-category-card').forEach(card => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';
                    card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                    observer.observe(card);
                });

                // Add counter animation for statistics
                function animateCounter(element, target) {
                    let current = 0;
                    const increment = target / 80;
                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }
                        element.textContent = Math.floor(current).toLocaleString();
                    }, 25);
                }

                // Animate counters when they come into view
                const statsObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counter = entry.target.querySelector('.sf-stat-number');
                            if (counter && !counter.classList.contains('sf-animated')) {
                                counter.classList.add('sf-animated');
                                const target = parseInt(counter.textContent.replace(/,/g, ''));
                                animateCounter(counter, target);
                            }
                        }
                    });
                }, { threshold: 0.5 });

                document.querySelectorAll('.sf-stat-card').forEach(item => {
                    statsObserver.observe(item);
                });
            });
        </script>
    </div>
