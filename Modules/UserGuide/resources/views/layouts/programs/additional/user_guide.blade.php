<div class="daftra-help-wrapper">
    <div class="daftra-help-main-container">
        <!-- القسم الرئيسي -->
        <div class="daftra-help-hero-section">
            <h1 class="daftra-help-main-title">كيف يمكننا مساعدتك؟</h1>
            <p class="daftra-help-main-subtitle">ابحث في قاعدة معارفنا أو تصفح المواضيع حسب البرنامج</p>
        </div>

        <!-- قسم البحث -->
        <div class="daftra-help-search-container">
            <form class="daftra-help-search-row" onsubmit="daftraHelpPerformSearch(event)">
                <div class="daftra-help-search-field">
                    <input type="text" class="daftra-help-search-input"
                        placeholder="ابحث عن أي موضوع تريد المساعدة فيه..." id="daftraHelpSearchInput"
                        autocomplete="off">
                    <i class="fas fa-search daftra-help-search-icon-wrapper"></i>
                </div>
                <select class="daftra-help-category-dropdown" id="daftraHelpCategorySelect"
                    onchange="daftraHelpUpdateSelection()">
                    <option value="all">كل البرامج</option>
                    <option value="sales">المبيعات</option>
                    <option value="customers">العملاء</option>
                    <option value="inventory">المخازن</option>
                    <option value="purchases">المشتريات</option>
                    <option value="accounting">الحسابات</option>
                    <option value="employees">الموظفين</option>
                    <option value="operations">التشغيل</option>
                    <option value="settings">الإعدادات</option>
                    <option value="account">حسابي</option>
                    <option value="developers">للمبرمجين</option>
                    <option value="reports">التقارير</option>
                    <option value="apps">تطبيقات فوترة</option>
                    <option value="industries">مجالات العمل</option>
                </select>
                <button type="submit" class="daftra-help-search-btn">
                    <i class="fas fa-search"></i>
                    بحث
                </button>
            </form>

            <div class="daftra-help-current-filter">
                تصفح المواضيع حسب البرنامج: <span class="daftra-help-highlight" id="daftraHelpCurrentCategory">كل
                    البرامج</span>
            </div>

            <!-- نتائج البحث -->
            <div class="daftra-help-search-results" id="daftraHelpSearchResults">
                <div id="daftraHelpResultsContent"></div>
            </div>
        </div>

        <!-- شبكة المواضيع -->
        <div class="daftra-help-topics-grid" id="daftraHelpTopicsContainer">
            <!-- المبيعات -->
            <div class="daftra-help-topic-card" data-category="sales">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">المبيعات</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الفواتير
                            وعروض الأسعار</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الدفعات
                            المقدمة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">نقاط
                            البيع</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الأقساط</a>
                    </li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">المبيعات
                            المستهدفة والعمولات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">المتجر
                            الإلكتروني</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">نقاط
                            الولاء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">وكلاء
                            التأمين</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الفاتورة
                            الإلكترونية السعودية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الفاتورة
                            الإلكترونية المصرية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الفاتورة
                            الإلكترونية الأردنية</a></li>
                </ul>
            </div>

            <!-- العملاء -->
            <div class="daftra-help-topic-card" data-category="customers">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">العملاء</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">العملاء</a>
                    </li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">متابعة
                            العملاء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">حضور
                            العملاء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">النقاط
                            والأرصدة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">الاشتراكات والعضويات</a></li>
                </ul>
            </div>

            <!-- المخازن -->
            <div class="daftra-help-topic-card" data-category="inventory">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-boxes"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">المخازن</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            المستودعات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الأذون
                            المخزنية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            الجرد</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">المنتجات
                            والخدمات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">قائمة
                            الأسعار</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">قوالب
                            الوحدات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">المنتجات
                            المجمعة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تتبع
                            المنتجات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            المخزون</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            المنتجات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">التصنيع</a></li>
                </ul>
            </div>

            <!-- المشتريات -->
            <div class="daftra-help-topic-card" data-category="purchases">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">المشتريات</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">طلبات
                            الشراء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">طلبات
                            عروض الأسعار</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">عروض
                            أسعار الشراء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">أوامر
                            الشراء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">فواتير
                            الشراء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            الموردين</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            الموردين</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            المشتريات</a></li>
                </ul>
            </div>

            <!-- الحسابات -->
            <div class="daftra-help-topic-card" data-category="accounting">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">الحسابات</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">المالية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الحسابات
                            العامة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">دورة
                            الشيكات</a></li>
                </ul>
            </div>

            <!-- الموظفين -->
            <div class="daftra-help-topic-card" data-category="employees">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-user-friends"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">الموظفين</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            الموظفين</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الهيكل
                            التنظيمي</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الحضور
                            والانصراف</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            العقود</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            المرتبات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            الطلبات</a></li>
                </ul>
            </div>

            <!-- التشغيل -->
            <div class="daftra-help-topic-card" data-category="operations">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-cogs"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">التشغيل</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">أوامر
                            الشغل</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">دورات
                            العمل</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">الحجوزات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">الإيجارات والوحدات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تتبع
                            الوقت</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">PNR</a>
                    </li>
                </ul>
            </div>

            <!-- الإعدادات -->
            <div class="daftra-help-topic-card" data-category="settings">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-cog"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">الإعدادات</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            الضرائب</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">طرق
                            الدفع</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            الترقيم المتسلسل</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            SMTP</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            التطبيقات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            التطبيقات الخارجية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الرسائل
                            النصية القصيرة SMS</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">الفروع</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">القوالب</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">عن
                            فوترة</a></li>
                </ul>
            </div>

            <!-- حسابي -->
            <div class="daftra-help-topic-card" data-category="account">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-user"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">حسابي</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إعدادات
                            الحساب</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">ترقية
                            الحساب وعمليات الدفع</a></li>
                </ul>
            </div>

            <!-- للمبرمجين -->
            <div class="daftra-help-topic-card" data-category="developers">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-code"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">للمبرمجين</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">واجهة
                            برمجة التطبيقات API</a></li>
                </ul>
            </div>

            <!-- التقارير -->
            <div class="daftra-help-topic-card" data-category="reports">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-chart-bar"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">التقارير</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تقارير
                            المبيعات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تقارير
                            المشتريات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تقارير
                            الحسابات العامة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تقارير
                            العملاء</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تقارير
                            المخزون</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">سجل
                            النشاطات للحساب</a></li>
                </ul>
            </div>

            <!-- تطبيقات فوترة -->
            <div class="daftra-help-topic-card" data-category="apps">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">تطبيقات فوترة</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            تسجيل الحضور ESS</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            تسجيل المصروفات السريع</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            جرد المخزون</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            فوترة العام</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            نقاط البيع - سطح المكتب</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            نقاط البيع - للجوال</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">تطبيق
                            قارئ الفاتورة الإلكترونية</a></li>
                </ul>
            </div>

            <!-- مجالات العمل -->
            <div class="daftra-help-topic-card" data-category="industries">
                <div class="daftra-help-topic-header">
                    <div class="daftra-help-topic-icon">
                        <i class="fas fa-industry"></i>
                    </div>
                    <h3 class="daftra-help-topic-name">مجالات العمل</h3>
                </div>
                <ul class="daftra-help-topic-list">
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            الصيدليات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            متاجر التجزئة</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            متاجر قطع غيار السيارات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">إدارة
                            معارض إيجار السيارات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">البلايستيشن</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">الجيم
                            والنوادي الصحية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">العيادات
                            الطبية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#"
                            class="daftra-help-topic-link">الفنادق</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">المتاجر
                            الإلكترونية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">شركات
                            الأدوية</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">شركات
                            الطباعة والدعاية والإعلان</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">عيادات
                            الأسنان</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">محلات
                            البصريات والنظارات</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">مراكز
                            التجميل</a></li>
                    <li class="daftra-help-topic-list-item"><a href="#" class="daftra-help-topic-link">مكاتب
                            المحاماة والاستشارات القانونية</a></li>
                </ul>
            </div>
        </div>

        <!-- قسم التواصل -->
        <div class="daftra-help-contact-section">
            <h2 class="daftra-help-contact-title">لم تجد ما تريد؟</h2>
            <p class="daftra-help-contact-subtitle">نحن سعداء بمساعدتك. تواصل معنا</p>
            <a href="#" class="daftra-help-contact-btn">
                <i class="fas fa-headset"></i>
                تواصل مع فريق الدعم
            </a>
        </div>
    </div>
</div>

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

<script>
    // بيانات المواضيع مع البادئة الفريدة
    const daftraHelpTopicsData = {
        sales: [
            'الفواتير وعروض الأسعار', 'الدفعات المقدمة', 'نقاط البيع', 'الأقساط',
            'المبيعات المستهدفة والعمولات', 'المتجر الإلكتروني', 'نقاط الولاء', 'وكلاء التأمين',
            'الفاتورة الإلكترونية السعودية', 'الفاتورة الإلكترونية المصرية', 'الفاتورة الإلكترونية الأردنية'
        ],
        customers: ['العملاء', 'متابعة العملاء', 'حضور العملاء', 'النقاط والأرصدة', 'الاشتراكات والعضويات'],
        inventory: [
            'إدارة المستودعات', 'الأذون المخزنية', 'إدارة الجرد', 'المنتجات والخدمات',
            'قائمة الأسعار', 'قوالب الوحدات', 'المنتجات المجمعة', 'تتبع المنتجات',
            'إعدادات المخزون', 'إعدادات المنتجات', 'التصنيع'
        ],
        purchases: [
            'طلبات الشراء', 'طلبات عروض الأسعار', 'عروض أسعار الشراء', 'أوامر الشراء',
            'فواتير الشراء', 'إدارة الموردين', 'إعدادات الموردين', 'إعدادات المشتريات'
        ],
        accounting: ['المالية', 'الحسابات العامة', 'دورة الشيكات'],
        employees: ['إدارة الموظفين', 'الهيكل التنظيمي', 'الحضور والانصراف', 'إدارة العقود', 'إدارة المرتبات',
            'إدارة الطلبات'
        ],
        operations: ['أوامر الشغل', 'دورات العمل', 'الحجوزات', 'الإيجارات والوحدات', 'تتبع الوقت', 'PNR'],
        settings: [
            'إعدادات الضرائب', 'طرق الدفع', 'إعدادات الترقيم المتسلسل', 'إعدادات SMTP',
            'إدارة التطبيقات', 'إدارة التطبيقات الخارجية', 'الرسائل النصية القصيرة SMS',
            'الفروع', 'القوالب', 'عن فوترة'
        ],
        account: ['إعدادات الحساب', 'ترقية الحساب وعمليات الدفع'],
        developers: ['واجهة برمجة التطبيقات API'],
        reports: ['تقارير المبيعات', 'تقارير المشتريات', 'تقارير الحسابات العامة', 'تقارير العملاء',
            'تقارير المخزون', 'سجل النشاطات للحساب'
        ],
        apps: [
            'تطبيق تسجيل الحضور ESS', 'تطبيق تسجيل المصروفات السريع', 'تطبيق جرد المخزون',
            'تطبيق فوترة العام', 'تطبيق نقاط البيع - سطح المكتب', 'تطبيق نقاط البيع - للجوال',
            'تطبيق قارئ الفاتورة الإلكترونية'
        ],
        industries: [
            'إدارة الصيدليات', 'إدارة متاجر التجزئة', 'إدارة متاجر قطع غيار السيارات',
            'إدارة معارض إيجار السيارات',
            'البلايستيشن', 'الجيم والنوادي الصحية', 'العيادات الطبية', 'الفنادق', 'المتاجر الإلكترونية',
            'شركات الأدوية', 'شركات الطباعة والدعاية والإعلان', 'عيادات الأسنان', 'محلات البصريات والنظارات',
            'مراكز التجميل', 'مكاتب المحاماة والاستشارات القانونية'
        ]
    };

    const daftraHelpCategoryNames = {
        all: 'كل البرامج',
        sales: 'المبيعات',
        customers: 'العملاء',
        inventory: 'المخازن',
        purchases: 'المشتريات',
        accounting: 'الحسابات',
        employees: 'الموظفين',
        operations: 'التشغيل',
        settings: 'الإعدادات',
        account: 'حسابي',
        developers: 'للمبرمجين',
        reports: 'التقارير',
        apps: 'تطبيقات فوترة',
        industries: 'مجالات العمل'
    };

    // وظائف البحث والتفاعل مع البادئة الفريدة
    function daftraHelpUpdateSelection() {
        const select = document.getElementById('daftraHelpCategorySelect');
        const currentCategory = document.getElementById('daftraHelpCurrentCategory');
        currentCategory.textContent = daftraHelpCategoryNames[select.value];
        daftraHelpFilterSections(select.value);
    }

    function daftraHelpFilterSections(category) {
        const sections = document.querySelectorAll('.daftra-help-topic-card');
        sections.forEach(section => {
            if (category === 'all' || section.dataset.category === category) {
                section.style.display = 'block';
                section.style.animation = 'daftraHelpSlideInUp 0.5s ease-out';
            } else {
                section.style.display = 'none';
            }
        });
    }

    function daftraHelpPerformSearch(event) {
        event.preventDefault();
        const searchInput = document.getElementById('daftraHelpSearchInput');
        const categorySelect = document.getElementById('daftraHelpCategorySelect');
        const searchResults = document.getElementById('daftraHelpSearchResults');
        const resultsContent = document.getElementById('daftraHelpResultsContent');

        const query = searchInput.value.trim().toLowerCase();
        const selectedCategory = categorySelect.value;

        if (!query) {
            searchResults.classList.remove('daftra-help-show');
            return;
        }

        let searchData = [];

        if (selectedCategory === 'all') {
            Object.keys(daftraHelpTopicsData).forEach(category => {
                daftraHelpTopicsData[category].forEach(topic => {
                    if (topic.toLowerCase().includes(query)) {
                        searchData.push({
                            topic: topic,
                            category: daftraHelpCategoryNames[category]
                        });
                    }
                });
            });
        } else {
            if (daftraHelpTopicsData[selectedCategory]) {
                daftraHelpTopicsData[selectedCategory].forEach(topic => {
                    if (topic.toLowerCase().includes(query)) {
                        searchData.push({
                            topic: topic,
                            category: daftraHelpCategoryNames[selectedCategory]
                        });
                    }
                });
            }
        }

        daftraHelpDisplaySearchResults(searchData, query);
    }

    function daftraHelpDisplaySearchResults(results, query) {
        const searchResults = document.getElementById('daftraHelpSearchResults');
        const resultsContent = document.getElementById('daftraHelpResultsContent');

        if (results.length === 0) {
            resultsContent.innerHTML = `
                <div class="daftra-help-no-results">
                    <i class="fas fa-search" style="font-size: 36px; color: #cbd5e1; margin-bottom: 12px; display: block;"></i>
                    <p>لم يتم العثور على نتائج للبحث عن: "<strong>${query}</strong>"</p>
                </div>`;
        } else {
            let resultsHTML =
                `<h4 style="margin-bottom: 15px; color: #2d3748; font-size: 16px;">نتائج البحث (${results.length})</h4>`;
            results.forEach(result => {
                resultsHTML += `
                    <div class="daftra-help-result-item" onclick="daftraHelpGoToTopic('${result.topic}')">
                        <div style="font-weight: 600; color: #2d3748; margin-bottom: 4px; font-size: 14px;">${result.topic}</div>
                        <div style="font-size: 12px; color: #4a5568;"><i class="fas fa-folder" style="margin-left: 5px;"></i>${result.category}</div>
                    </div>`;
            });
            resultsContent.innerHTML = resultsHTML;
        }

        searchResults.classList.add('daftra-help-show');
    }

    function daftraHelpGoToTopic(topic) {
        console.log('الانتقال إلى موضوع:', topic);
        alert(`سيتم الانتقال إلى: ${topic}`);
    }

    // تفعيل البحث الفوري
    document.getElementById('daftraHelpSearchInput').addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length >= 2) {
            clearTimeout(this.daftraHelpSearchTimeout);
            this.daftraHelpSearchTimeout = setTimeout(() => {
                daftraHelpPerformSearch(new Event('submit'));
            }, 300);
        } else if (query.length === 0) {
            document.getElementById('daftraHelpSearchResults').classList.remove('daftra-help-show');
        }
    });

    // إخفاء النتائج عند النقر خارجها
    document.addEventListener('click', function(event) {
        const searchContainer = document.querySelector('.daftra-help-search-container');
        if (!searchContainer.contains(event.target)) {
            document.getElementById('daftraHelpSearchResults').classList.remove('daftra-help-show');
        }
    });

    // تفعيل التفاعلات
    document.addEventListener('DOMContentLoaded', function() {
        // تفاعل الروابط
        document.querySelectorAll('.daftra-help-topic-link').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                daftraHelpGoToTopic(this.textContent);
            });
        });

        // تفاعل زر التواصل
        document.querySelector('.daftra-help-contact-btn').addEventListener('click', function(e) {
            e.preventDefault();
            alert('سيتم فتح نافذة الدردشة مع فريق الدعم');
        });
    });

    // اختصارات لوحة المفاتيح
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && document.activeElement.id === 'daftraHelpSearchInput') {
            daftraHelpPerformSearch(event);
        }
        if (event.key === 'Escape') {
            document.getElementById('daftraHelpSearchResults').classList.remove('daftra-help-show');
            document.getElementById('daftraHelpSearchInput').blur();
        }
    });
</script>
