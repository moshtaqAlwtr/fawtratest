<!-- Modal لعرض مجالات العمل -->
<div id="businessAreasModal" class="business-areas-modal" style="display: none;">
    <div class="modal-overlay" onclick="closeBusinessAreasModal()"></div>
    <div class="modal-content">
        <div class="modal-header">
            <div class="breadcrumb-container">
                <button class="back-btn" onclick="goBack()" style="display: none;">
                    <i class="fas fa-arrow-right"></i>
                    <span>رجوع</span>
                </button>
                <h2 id="modalTitle">اختيار مجال العمل</h2>
            </div>
            <button class="close-btn" onclick="closeBusinessAreasModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="search-container">
                <input type="text" class="search-box" placeholder="ابحث عن مجال عملك" id="businessSearch">
                <i class="fas fa-search search-icon"></i>
            </div>

            <!-- الأقسام الرئيسية -->
            <div class="categories-grid" id="mainCategories">
                <div class="category-card retail" onclick="showSubCategories('retail')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-store category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                المحلات التجارية ونقطة البيع
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            إدارة المحال التجارية ونقاط البيع (الكاشير) ومتابعة المخزون والحركات المالية.
                        </div>
                    </div>
                </div>

                <div class="category-card hardware" onclick="showSubCategories('hardware')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-tools category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                الحرف والخدمات المعدنية
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            إدارة سجل طلبات الصيانة والخدمات حتى التسليم.
                        </div>
                    </div>
                </div>

                <div class="category-card business" onclick="showSubCategories('business')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-briefcase category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                خدمات الأعمال
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            مع مزايا التذكير الآلي، إدارة مكاتب المحاماة والمكاتب الاستشارية.
                        </div>
                    </div>
                </div>

                <div class="category-card medical" onclick="showSubCategories('medical')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-stethoscope category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                الرعاية الطبية
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            إدارة خدمات العيادة الطبية وإدارة الزيارات وتنظيم المواعيد الطبية والمرضى.
                        </div>
                    </div>
                </div>

                <div class="category-card logistics" onclick="showSubCategories('logistics')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-shipping-fast category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                الخدمات اللوجستية
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            إدارة خدمات الشحن واللوجستية وشركات النقل والتوصيل.
                        </div>
                    </div>
                </div>

                <div class="category-card hospitality" onclick="showSubCategories('hospitality')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-hotel category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                السياحة والنقل والضيافة
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            تنظيم وحجز الغرف الفندقية ومتابعة الحجوزات وإدارة الوحدات وأصول الشركة.
                        </div>
                    </div>
                </div>

                <div class="category-card fitness" onclick="showSubCategories('fitness')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-dumbbell category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                العناية بالجسم واللياقة البدنية
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            تسجيل الأعضاء ومتابعة الحضور، تنبيه للاشتراكات المنتهية واستكمال المواعيد أونلاين.
                        </div>
                    </div>
                </div>

                <div class="category-card education" onclick="showSubCategories('education')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-graduation-cap category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                التعليم
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            إدارة القاعات ومتابعة قوائم الطلاب والدورات التعليمية.
                        </div>
                    </div>
                </div>

                <div class="category-card automotive" onclick="showSubCategories('automotive')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-car category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                السيارات
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            بيع وشراء وتأجير السيارات وإدارة مخزون قطع الغيار.
                        </div>
                    </div>
                </div>

                <div class="category-card construction" onclick="showSubCategories('construction')">
                    <div class="card-gradient"></div>
                    <div class="category-content">
                        <div class="category-header">
                            <div class="icon-wrapper">
                                <i class="fas fa-building category-icon"></i>
                            </div>
                            <div class="bs-category-title">
                                المشاريع والمقاولات والاستثمار العقاري
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="category-description">
                            حلول إدارة المشاريع والمقاولات وعمل المستخلصات، وبيع وشراء وتأجير الوحدات العقارية.
                        </div>
                    </div>
                </div>
            </div>

            <!-- الفروع الفرعية -->
            <div class="subcategories-grid" id="subCategories" style="display: none;">
                <!-- سيتم ملئها ديناميكياً -->
            </div>
        </div>
    </div>
</div>
<style>
    .bs-category-title {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1e293b;
        line-height: 1.4;
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: right;
    }
</style>


<script>
    // بيانات الفروع الفرعية لكل قسم
    const subCategoriesData = {
        'retail': {
            title: 'المحلات التجارية ونقطة البيع',
            icon: 'fas fa-store',
            subcategories: [

                {
                    id: 'hardware-paint',
                    title: 'برنامج محلات حدايد وبويات',
                    description: 'إدارة مخزون الحدايد والبويات ومتابعة المبيعات',
                    icon: 'fas fa-paint-brush'
                },
                {
                    id: 'perfume',
                    title: 'برنامج إدارة محلات العطور',
                    description: 'نظام خاص بإدارة محلات العطور والمستحضرات',
                    icon: 'fas fa-spray-can'
                },
                {
                    id: 'mobile',
                    title: 'برنامج إدارة محلات الجوالات',
                    description: 'إدارة مبيعات الجوالات والإكسسوارات والصيانة',
                    icon: 'fas fa-mobile-alt'
                },
                {
                    id: 'commercial',
                    title: 'برنامج إدارة المؤسسات التجارية والتوريد',
                    description: 'حلول للمؤسسات التجارية الكبيرة وشركات التوريد',
                    icon: 'fas fa-industry'
                },
                {
                    id: 'ceramic',
                    title: 'برنامج إدارة مخازن السيراميك',
                    description: 'إدارة مخزون السيراميك والبلاط ومواد البناء',
                    icon: 'fas fa-border-all'
                },
                {
                    id: 'bookstore',
                    title: 'برنامج إدارة المكتبات',
                    description: 'نظام إدارة المكتبات والقرطاسية والكتب',
                    icon: 'fas fa-book'
                },
                {
                    id: 'computer',
                    title: 'برنامج إدارة محلات الكمبيوتر',
                    description: 'إدارة محلات الكمبيوتر والإكسسوارات التقنية',
                    icon: 'fas fa-desktop'
                },
                {
                    id: 'auto-parts',
                    title: 'برنامج إدارة متاجر قطع غيار السيارات',
                    description: 'إدارة مخزون قطع غيار السيارات ومتابعة الطلبات',
                    icon: 'fas fa-cogs'
                },
                {
                    id: 'jewelry',
                    title: 'برنامج إدارة محلات الذهب والمجوهرات',
                    description: 'إدارة محلات الذهب والمجوهرات مع حساب الأوزان والعيارات',
                    icon: 'fas fa-gem'
                },
                {
                    id: 'optics',
                    title: 'برنامج إدارة محلات النظارات والبصريات',
                    description: 'إدارة محلات النظارات والعدسات وفحص البصر',
                    icon: 'fas fa-glasses'
                }
            ]
        },
        'hardware': {
            title: 'الحرف والخدمات المهنية',
            icon: 'fas fa-tools',
            subcategories: [

                {
                    id: 'landscape',
                    title: 'برنامج إدارة مشاريع اللاند سكيب',
                    description: 'إدارة مشاريع تنسيق الحدائق والمناظر الطبيعية',
                    icon: 'fas fa-seedling'
                },
                {
                    id: 'hvac',
                    title: 'برنامج إدارة مراكز تركيب وصيانة أجهزة التكييف',
                    description: 'إدارة مراكز تركيب وصيانة أنظمة التكييف والتبريد',
                    icon: 'fas fa-snowflake'
                },
                {
                    id: 'furniture',
                    title: 'برنامج إدارة ورش الموبيليا',
                    description: 'إدارة ورش تصنيع وتصليح الأثاث والموبيليا',
                    icon: 'fas fa-couch'
                },
                {
                    id: 'factory',
                    title: 'برنامج إدارة المصانع',
                    description: 'إدارة العمليات الصناعية والإنتاج والجودة',
                    icon: 'fas fa-industry'
                },
                {
                    id: 'coworking',
                    title: 'برنامج إدارة مساحات العمل المشتركة',
                    description: 'إدارة مساحات العمل المشتركة والحجوزات والأعضاء',
                    icon: 'fas fa-users'
                },
                {
                    id: 'gaming',
                    title: 'برنامج ادارة صالات الألعاب والبلايستيشن',
                    description: 'إدارة صالات الألعاب والبلايستيشن ونظام الساعات',
                    icon: 'fas fa-gamepad'
                },
                {
                    id: 'laundry',
                    title: 'برنامج إدارة مراكز تنظيف وكي الملابس (المغسلة)',
                    description: 'إدارة المغاسل وخدمات التنظيف والكي والتسليم',
                    icon: 'fas fa-tshirt'
                },
                {
                    id: 'maintenance',
                    title: 'برنامج إدارة مراكز الصيانة و الدعم الفنى',
                    description: 'إدارة مراكز الصيانة والدعم الفني وطلبات الخدمة',
                    icon: 'fas fa-wrench'
                },
                {
                    id: 'cleaning',
                    title: 'برنامج إدارة شركات النظافة',
                    description: 'إدارة شركات النظافة والعقود والفرق والمواعيد',
                    icon: 'fas fa-broom'
                },
                {
                    id: 'equipment-rental',
                    title: 'برنامج إدارة شركات تأجير المعدات',
                    description: 'إدارة تأجير المعدات والآلات والعقود والصيانة',
                    icon: 'fas fa-truck-loading'
                }
            ]
        },
        'business': {
            title: 'خدمات الأعمال',
            icon: 'fas fa-briefcase',
            subcategories: [

                {
                    id: 'marketing',
                    title: 'برنامج إدارة شركات التسويق',
                    description: 'إدارة شركات التسويق والحملات الإعلانية والعملاء',
                    icon: 'fas fa-bullhorn'
                },
                {
                    id: 'consulting',
                    title: 'برنامج فوترة لإدارة المكاتب و الشركات الاستشارية',
                    description: 'إدارة المكاتب الاستشارية والخدمات المهنية والعملاء',
                    icon: 'fas fa-user-tie'
                },
                {
                    id: 'hosting-web',
                    title: 'برنامج إدارة شركات الاستضافة وتطوير المواقع',
                    description: 'إدارة شركات الاستضافة والمواقع والخوادم والعملاء',
                    icon: 'fas fa-server'
                },
                {
                    id: 'accounting',
                    title: 'برنامج فوترة لإدارة مكاتب المحاسبة',
                    description: 'إدارة مكاتب المحاسبة والعملاء والتقارير المالية',
                    icon: 'fas fa-calculator'
                },
                {
                    id: 'translation',
                    title: 'برنامج إدارة مراكز الترجمة',
                    description: 'إدارة مراكز الترجمة والمترجمين والمشاريع والتسليم',
                    icon: 'fas fa-language'
                },
                {
                    id: 'commercial-supply',
                    title: 'برنامج إدارة المؤسسات التجارية والتوريد',
                    description: 'إدارة المؤسسات التجارية وشركات التوريد والمخزون',
                    icon: 'fas fa-industry'
                },
                {
                    id: 'recruitment',
                    title: 'برنامج إدارة شركات ومكاتب الاستقدام',
                    description: 'إدارة مكاتب الاستقدام والعمالة والكفلاء والإجراءات',
                    icon: 'fas fa-user-plus'
                },
                {
                    id: 'law-office',
                    title: 'برنامج إدارة مكاتب المحاماة والاستشارات القانونية',
                    description: 'إدارة مكاتب المحاماة والقضايا والعملاء والجلسات',
                    icon: 'fas fa-balance-scale'
                },
                {
                    id: 'pharmaceutical',
                    title: 'برنامج إدارة شركات الأدوية',
                    description: 'إدارة شركات الأدوية والتوزيع والمخزون والصيدليات',
                    icon: 'fas fa-pills'
                },
                {
                    id: 'printing-advertising',
                    title: 'برنامج إدارة شركات الطباعة والدعاية والإعلان',
                    description: 'إدارة مطابع ومكاتب الدعاية والإعلان والطلبات',
                    icon: 'fas fa-print'
                }
            ]
        },
        'medical': {
            title: 'الرعاية الطبية',
            icon: 'fas fa-stethoscope',
            subcategories: [

                {
                    id: 'clinic-medical-center',
                    title: 'برنامج إدارة العيادات والمراكز الطبية',
                    description: 'إدارة العيادات ومواعيد المرضى والملفات الطبية والفواتير',
                    icon: 'fas fa-clinic-medical'
                },
                {
                    id: 'lab',
                    title: 'برنامج إدارة معامل التحاليل الطبية',
                    description: 'إدارة معامل التحاليل والفحوصات والنتائج والتقارير',
                    icon: 'fas fa-vial'
                },
                {
                    id: 'dental',
                    title: 'برنامج إدارة عيادات الأسنان',
                    description: 'نظام خاص بإدارة عيادات الأسنان والعلاجات والمواعيد',
                    icon: 'fas fa-tooth'
                },
                {
                    id: 'pediatric',
                    title: 'برنامج إدارة عيادات الأطفال',
                    description: 'إدارة عيادات الأطفال والتطعيمات والنمو والمتابعة',
                    icon: 'fas fa-baby'
                },
                {
                    id: 'gynecology',
                    title: 'برنامج إدارة عيادات النساء والتوليد',
                    description: 'إدارة عيادات النساء والولادة والمتابعة والحوامل',
                    icon: 'fas fa-female'
                },
                {
                    id: 'pharmacy',
                    title: 'برنامج إدارة الصيدليات',
                    description: 'إدارة الصيدليات والأدوية والمخزون وتواريخ الانتهاء',
                    icon: 'fas fa-prescription-bottle'
                },
                {
                    id: 'pharmaceutical-company',
                    title: 'برنامج إدارة شركات الأدوية',
                    description: 'إدارة شركات الأدوية والتصنيع والتوزيع والمندوبين',
                    icon: 'fas fa-pills'
                }
            ]
        },
        'logistics': {
            title: 'الخدمات اللوجستية',
            icon: 'fas fa-shipping-fast',
            subcategories: [

                {
                    id: 'shipping-logistics',
                    title: 'برنامج إدارة شركات الشحن واللوجستيات',
                    description: 'إدارة شركات الشحن والطرود والتوصيل والمتابعة',
                    icon: 'fas fa-shipping-fast'
                },
                {
                    id: 'car-rental-limo',
                    title: 'برنامج إدارة شركات تأجير السيارات والليموزين',
                    description: 'إدارة تأجير السيارات والليموزين والحجوزات والعقود',
                    icon: 'fas fa-car-side'
                }
            ]
        },
        'hospitality': {
            title: 'السياحة والنقل والضيافة',
            icon: 'fas fa-hotel',
            subcategories: [

                {
                    id: 'hotel',
                    title: 'برنامج إدارة الفنادق',
                    description: 'إدارة الفنادق والحجوزات والغرف والخدمات والعملاء',
                    icon: 'fas fa-hotel'
                },
                {
                    id: 'car-rental-limo',
                    title: 'برنامج إدارة شركات تأجير السيارات والليموزين',
                    description: 'إدارة تأجير السيارات والليموزين للسياح والرحلات',
                    icon: 'fas fa-car-side'
                },
                {
                    id: 'transport-trips',
                    title: 'برنامج إدارة رحلات النقل والمواصلات',
                    description: 'إدارة رحلات النقل السياحي والمواصلات والحافلات',
                    icon: 'fas fa-bus'
                },
                {
                    id: 'travel-tourism',
                    title: 'برنامج إدارة شركات السفر والسياحة',
                    description: 'إدارة شركات السفر والبرامج السياحية والحجوزات',
                    icon: 'fas fa-plane'
                }
            ]
        },
        'fitness': {
            title: 'العناية بالجسم واللياقة البدنية',
            icon: 'fas fa-dumbbell',
            subcategories: [

                {
                    id: 'gym-fitness-club',
                    title: 'برنامج إدارة الجيم و مراكز اللياقة و النوادى الصحية',
                    description: 'إدارة الصالات الرياضية والأعضاء والاشتراكات والمدربين',
                    icon: 'fas fa-dumbbell'
                },
                {
                    id: 'beauty-salon',
                    title: 'برنامج إدارة مراكز التجميل و صالونات الكوافير والمشاغل النسائية',
                    description: 'إدارة صالونات التجميل والمواعيد والخدمات والعملاء',
                    icon: 'fas fa-cut'
                }
            ]
        },
        'education': {
            title: 'التعليم',
            icon: 'fas fa-graduation-cap',
            subcategories: [

                {
                    id: 'school-nursery',
                    title: 'برنامج إدارة المدارس والحضانات',
                    description: 'إدارة المدارس والطلاب والمناهج والدرجات والحضانات',
                    icon: 'fas fa-school'
                },
                {
                    id: 'educational-center',
                    title: 'برنامج إدارة المراكز التعليمية',
                    description: 'إدارة مراكز التعليم والدورات والطلاب والمعلمين',
                    icon: 'fas fa-chalkboard-teacher'
                }
            ]
        },
        'automotive': {
            title: 'السيارات',
            icon: 'fas fa-car',
            subcategories: [

                {
                    id: 'car-maintenance',
                    title: 'برنامج إدارة مراكز صيانة السيارات | فوترة',
                    description: 'إدارة مراكز صيانة السيارات والأعطال والقطع والعملاء',
                    icon: 'fas fa-wrench'
                },
                {
                    id: 'auto-parts',
                    title: 'برنامج إدارة متاجر قطع غيار السيارات',
                    description: 'إدارة مخزون قطع غيار السيارات ومتابعة الطلبات والمبيعات',
                    icon: 'fas fa-cogs'
                },
                {
                    id: 'car-showroom',
                    title: 'برنامج إدارة معارض السيارات',
                    description: 'إدارة معارض بيع السيارات والمخزون والعملاء والتمويل',
                    icon: 'fas fa-car'
                },
                {
                    id: 'car-rental-limo',
                    title: 'برنامج إدارة شركات تأجير السيارات والليموزين',
                    description: 'إدارة تأجير السيارات والليموزين والحجوزات والعقود',
                    icon: 'fas fa-car-side'
                }
            ]
        },
        'construction': {
            title: 'المشاريع والمقاولات والاستثمار العقاري',
            icon: 'fas fa-building',
            subcategories: [

                {
                    id: 'construction-real-estate',
                    title: 'برنامج إدارة شركات الإنشاء والاستثمار العقاري',
                    description: 'إدارة شركات الإنشاء والاستثمارات العقارية والوحدات والإيجارات',
                    icon: 'fas fa-building'
                },
                {
                    id: 'contractor',
                    title: 'برنامج إدارة شركات المقاولات',
                    description: 'إدارة شركات المقاولات والمشاريع والمستخلصات والعمالة',
                    icon: 'fas fa-hard-hat'
                }
            ]
        }
    };

    let currentCategory = null;

    // وظائف النافذة المنبثقة لمجالات العمل
    function openBusinessAreasModal() {
        closeProgramsModalWindow();
        const modal = document.getElementById('businessAreasModal');
        if (modal) {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
            // إعادة تعيين العرض للأقسام الرئيسية
            showMainCategories();
            // إضافة تأثير التركيز التلقائي على مربع البحث
            setTimeout(() => {
                const searchBox = document.getElementById('businessSearch');
                if (searchBox) {
                    searchBox.focus();
                }
            }, 300);
        }
    }

    function closeBusinessAreasModal() {
        const modal = document.getElementById('businessAreasModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            // إعادة تعيين العرض للأقسام الرئيسية
            showMainCategories();
        }
    }

    function showMainCategories() {
        const mainCategories = document.getElementById('mainCategories');
        const subCategories = document.getElementById('subCategories');
        const backBtn = document.querySelector('.back-btn');
        const modalTitle = document.getElementById('modalTitle');

        if (mainCategories && subCategories && backBtn && modalTitle) {
            mainCategories.style.display = 'grid';
            subCategories.style.display = 'none';
            backBtn.style.display = 'none';
            modalTitle.textContent = 'اختيار مجال العمل';
            currentCategory = null;

            // إعادة تعيين البحث
            const searchBox = document.getElementById('businessSearch');
            if (searchBox) {
                searchBox.value = '';
                searchBox.placeholder = 'ابحث عن مجال عملك';
            }
        }
    }

    function showSubCategories(categoryKey) {
        const category = subCategoriesData[categoryKey];
        if (!category) return;

        currentCategory = categoryKey;
        const mainCategories = document.getElementById('mainCategories');
        const subCategories = document.getElementById('subCategories');
        const backBtn = document.querySelector('.back-btn');
        const modalTitle = document.getElementById('modalTitle');

        if (mainCategories && subCategories && backBtn && modalTitle) {
            // إخفاء الأقسام الرئيسية وإظهار الفروع
            mainCategories.style.display = 'none';
            subCategories.style.display = 'grid';
            backBtn.style.display = 'flex';
            modalTitle.textContent = category.title;

            // تحديث placeholder البحث
            const searchBox = document.getElementById('businessSearch');
            if (searchBox) {
                searchBox.placeholder = `ابحث في ${category.title}`;
                searchBox.value = '';
            }

            // إنشاء HTML للفروع الفرعية
            const subcategoriesHTML = category.subcategories.map(sub => `
                <div class="subcategory-card ${categoryKey}" onclick="selectSubCategory('${sub.id}', '${sub.title}')">
                    <div class="card-gradient"></div>
                    <div class="subcategory-content">
                        <div class="subcategory-header">
                            <div class="icon-wrapper">
                                <i class="${sub.icon} category-icon"></i>
                            </div>
                            <div class="subcategory-title">
                                ${sub.title}
                                <i class="fas fa-chevron-left ic-arrow-icon"></i>
                            </div>
                        </div>
                        <div class="subcategory-description">
                            ${sub.description}
                        </div>
                    </div>
                </div>
            `).join('');

            subCategories.innerHTML = subcategoriesHTML;

            // إضافة تأثير الحركة
            subCategories.style.animation = 'slideInLeft 0.5s ease-out';
        }
    }

    function goBack() {
        showMainCategories();

        // إضافة تأثير الحركة للعودة
        const mainCategories = document.getElementById('mainCategories');
        if (mainCategories) {
            mainCategories.style.animation = 'slideInRight 0.5s ease-out';
        }
    }

    // إضافة الوظيفة المفقودة selectSubCategory
    // إضافة الوظيفة المفقودة selectSubCategory
    function selectSubCategory(categoryId, categoryTitle) {
        console.log('تم اختيار:', categoryId, categoryTitle);

        // إغلاق المودال
        closeBusinessAreasModal();

        // التوجيه مباشر باستخدام URLs
        const routes = {
            // المحلات التجارية ونقطة البيع
            'hardware-paint': '/business-areas/retail/hardware-paint',
            'perfume': '/business-areas/retail/perfume',
            'mobile': '/business-areas/retail/mobile',
            'commercial': '/business-areas/retail/commercial',
            'ceramic': '/business-areas/retail/ceramic',
            'bookstore': '/business-areas/retail/bookstore',
            'computer': '/business-areas/retail/computer',
            'auto-parts': '/business-areas/retail/auto-parts',
            'jewelry': '/business-areas/retail/jewelry',
            'optics': '/business-areas/retail/optics',

            // الحرف والخدمات المهنية
            'landscape': '/business-areas/hardware/landscape',
            'hvac': '/business-areas/hardware/hvac',
            'furniture': '/business-areas/hardware/furniture',
            'factory': '/business-areas/hardware/factory',
            'coworking': '/business-areas/hardware/coworking',
            'gaming': '/business-areas/hardware/gaming',
            'laundry': '/business-areas/hardware/laundry',
            'maintenance': '/business-areas/hardware/maintenance',
            'cleaning': '/business-areas/hardware/cleaning',
            'equipment-rental': '/business-areas/hardware/equipment-rental',

            // خدمات الأعمال
            'marketing': '/business-areas/business/marketing',
            'consulting': '/business-areas/business/consulting',
            'hosting-web': '/business-areas/business/hosting-web',
            'accounting': '/business-areas/business/accounting',
            'translation': '/business-areas/business/translation',
            'law-office': '/business-areas/business/legal',
            'commercial-supply': '/business-areas/business/real-estate',
            'recruitment': '/business-areas/business/event-planning',
            'pharmaceutical': '/business-areas/business/photography',
            'printing-advertising': '/business-areas/business/insurance',

            // الرعاية الطبية
            'clinic-medical-center': '/business-areas/medical/clinic',
            'lab': '/business-areas/medical/lab',
            'dental': '/business-areas/medical/dental',
            'pediatric': '/business-areas/medical/veterinary',
            'gynecology': '/business-areas/medical/physiotherapy',
            'pharmacy': '/business-areas/medical/pharmacy',
            'pharmaceutical-company': '/business-areas/medical/radiology',

            // الخدمات اللوجستية
            'shipping-logistics': '/business-areas/logistics/shipping',
            'car-rental-limo': '/business-areas/logistics/delivery',

            // السياحة والنقل والضيافة
            'hotel': '/business-areas/hospitality/hotel',
            'transport-trips': '/business-areas/hospitality/taxi',
            'travel-tourism': '/business-areas/hospitality/travel',

            // العناية بالجسم واللياقة البدنية
            'gym-fitness-club': '/business-areas/fitness/gym',
            'beauty-salon': '/business-areas/fitness/salon',

            // التعليم
            'school-nursery': '/business-areas/education/school',
            'educational-center': '/business-areas/education/training',

            // السيارات
            'car-maintenance': '/business-areas/automotive/repair',
            'car-showroom': '/business-areas/automotive/dealership',

            // المشاريع والمقاولات والاستثمار العقاري
            'construction-real-estate': '/business-areas/construction/general',
            'contractor': '/business-areas/construction/project-management'
        };

        if (routes[categoryId]) {
            window.location.href = routes[categoryId];
        } else {
            console.error('Route not found for:', categoryId);
        }
    }


    // إضافة وظيفة البحث المحسنة
    document.addEventListener('DOMContentLoaded', function() {
        const searchBox = document.getElementById('businessSearch');
        if (searchBox) {
            searchBox.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();

                // تحديد أي نوع من البطاقات يتم عرضه حالياً
                const isMainView = document.getElementById('mainCategories').style.display !== 'none';
                const selector = isMainView ? '#mainCategories .category-card' :
                    '#subCategories .subcategory-card';
                const cards = document.querySelectorAll(selector);

                let visibleCount = 0;

                cards.forEach(card => {
                    const titleSelector = isMainView ? '.bs-category-title' :
                        '.subcategory-title';
                    const descriptionSelector = isMainView ? '.category-description' :
                        '.subcategory-description';

                    const title = card.querySelector(titleSelector).textContent
                        .toLowerCase().replace(/\s+/g, ' ').trim();
                    const description = card.querySelector(descriptionSelector).textContent
                        .toLowerCase().replace(/\s+/g, ' ').trim();

                    const isVisible = searchTerm === '' ||
                        title.includes(searchTerm) ||
                        description.includes(searchTerm);

                    if (isVisible) {
                        card.style.display = 'block';
                        card.style.animation =
                            `fadeIn 0.3s ease-out ${visibleCount * 0.1}s both`;
                        visibleCount++;
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        }
    });

    // إغلاق النافذة المنبثقة بالضغط على مفتاح Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeBusinessAreasModal();
        }
    });

    // منع إغلاق النافذة عند الضغط على المحتوى
    document.addEventListener('DOMContentLoaded', function() {
        const modalContent = document.querySelector('#businessAreasModal .modal-content');
        if (modalContent) {
            modalContent.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }
    });
</script>
