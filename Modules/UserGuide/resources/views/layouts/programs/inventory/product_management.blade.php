<!-- Product Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة المنتجات</h2>
                <p>نظِّم دورة حياة المنتجات بالكامل من خلال إدارة المخزون في نظام فوترة السحابي عبر واجهة سهلة الاستخدام
                    بدعم كامل للغة العربية، احتفظ بقاعدة بيانات منتجاتك في ملف تعريفي متكامل يتضمن كافة بيانات المنتج أو
                    الخدمة التي تقدمها لعملائك، ابحث عن المنتجات بالباركود وتتبعها بطرق متعددة بما في ذلك الرقم التسلسلي
                    أو رقم الشحنة أو تاريخ الانتهاء، وأضف المنتجات المجمعة ونظمها باحترافية وضمنها في فواتيرك بسهولة، مع
                    إمكانية تتبع طلبات المنتجات ومراقبة كمياتها وحركاتها من خلال تقارير الجرد والمخزون المفصلة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-prodect.webp') }}" alt="برنامج إدارة المنتجات">
                @include('userguide::layouts.programs.btn_google_apple')
            </div>
        </div>
    </div>
</section>

<!-- Spacing Section -->
<section class="fotra-spacing-section-large"></section>

@include('userguide::layouts.business_areas.cta_section_first')

<!-- Spacing Section -->
<section class="fotra-spacing-section-large"></section>
<!-- Inventory Management Features Icons Section -->
@include('userguide::layouts.programs.inventory.inventory_management_features_icons_section')


<!-- Add Products Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/products-details.webp') }}"
                    alt="أضف منتجاتك في حسابك متضمنة كافة التفاصيل اللازمة">
            </div>
            <div class="section-text">
                <h2>أضف منتجاتك في حسابك متضمنة كافة التفاصيل اللازمة</h2>
                <p>بفضل الأدوات المتقدمة في فوترة، يمكنك إضافة قائمة بمنتجاتك متضمنة كل البيانات المطلوبة بما في ذلك
                    أكواد التخزين والباركود والأسعار، كما يمكنك تنظيم مستودعك على نحو أفضل من خلال تصنيف العناصر
                    وعلاماتها التجارية، مع تحديد تفاصيل التسعير لكل منتج بما في ذلك أسعار الشراء والبيع وأقل سعر بيع،
                    إلى جانب الخصومات المطبقة، وإمكانية إضافة المنتج إلى قائمة أسعار مخصصة، بالإضافة إلى إعدادات المخزون
                    التي تحدد كمية المنتج في المستودع وطريقة التتبع المطلوبة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bundle Products Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أنشئ منتجاتك المجمعة بسهولة</h2>
                <p>يتيح لك فوترة إمكانية إنشاء المنتجات المجمعة وفقًا لمتطلبات أعمالك بأدوات متقدمة سهلة الاستخدام
                    لتنظيمها تنظيمًا احترافيًا، سواء إنشائها وتسجيلها في مخزونك، أو تضمينها عند إصدار الفاتورة على
                    الفور، حيث يخصم النظام تلقائيًا من كمية الأصناف التي يتكون منها المنتج المجمع.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/bundle-products.webp') }}"
                    alt="أنشئ منتجاتك المجمعة بسهولة">
            </div>
        </div>
    </div>
</section>

<!-- Units of Measurement Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/customize.webp') }}" alt="خصِّص وحدات قياسك حسب أعمالك">
            </div>
            <div class="section-text">
                <h2>خصِّص وحدات قياسك حسب أعمالك</h2>
                <p>أنشئ قوالب وحدات قياس متعددة لمنتجاتك بأنواعها المختلفة سواء الأساسية أو مشتقاها، وحدد وحدة القياس
                    الأساسية لكل منتج ووحدة الشراء والبيع والمخزون المتاح، إلى جانب تحديد التمييز للمنتج أثناء إصدار
                    فاتورة البيع أو الشراء.</p>
            </div>
        </div>
    </div>
</section>

<!-- Product Tracking Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>تتبع منتجاتك بطرق متعددة</h2>
                <p>يتيح لك النظام إمكانية تتبع منتجاتك بدقة إما عن طريقة الرقم التسلسلي أو رقم الشحنة أو تاريخ الانتهاء
                    وتعيين حد معين ينبهك عنده النظام بوصول الكمية إليه أو انخفاضها عنه. كما يمكنك عرض قائمة بالعناصر
                    المتوفرة، وتتبع المباع منها أو المتبقي في المخزون بسهولة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/track-products.webp') }}" alt="تتبع منتجاتك بطرق متعددة">
            </div>
        </div>
    </div>
</section>

<!-- Low Stock Alerts Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/low-stock.webp') }}"
                    alt="احصل على إشعارات عند وصول كمية منتجاتك إلى حد معين">
            </div>
            <div class="section-text">
                <h2>احصل على إشعارات عند وصول كمية منتجاتك إلى حد معين</h2>
                <p>بفضل خصائص الأتمتة في نظام فوترة، يمكنك تحديد حد أدنى لكمية المنتج في مخزونك بحيث يرسل لك النظام
                    إشعارًا في حال وصول الكمية إلى هذا الحد أو الانخفاض عنها من أجل اتخاذ الإجراء المناسب لتوفير منتجك
                    تلبية لحاجة عملائك.</p>
            </div>
        </div>
    </div>
</section>

<!-- Price Lists Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>ضمِّن منتجاتك في قوائم الأسعار</h2>
                <p>أنشئ قوائم الأسعار المختلفة وخصصها حسب احتياجاتك وضمِّن المنتجات المطلوبة بها بسولة، مع إمكانية إضافة
                    المنتج نفسه بسعر مختلف في قائمة الأسعار من خلال تحديدها في ملف المنتج التعريفي، حيث يمكنك تحديد
                    قائمة الأسعار المطلوبة أثناء إصدار الفاتورة بسهولة لإدخال المنتجات بأسعارها الموجودة داخل هذه
                    القائمة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/price-list.webp') }}" alt="ضمِّن منتجاتك في قوائم الأسعار">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Barcode Search Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/barcode.webp') }}"
                    alt="ابحث عن المنتجات باستخدام الباركود">
            </div>
            <div class="section-text">
                <h2>ابحث عن المنتجات باستخدام الباركود</h2>
                <p>أدخل الباركود للمنتج عند إضافته إلى حسابك أو قم بإنشائه عشوائيًا، بحيث يمكنك استخدامه للبحث عن
                    منتجاتك والعثور عليها بسهولة لاستخدامها إما في الفلترة أو عند تضمينها في فواتير البيع أو الشراء.</p>
            </div>
        </div>
    </div>
</section>

<!-- Detailed Reports Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أصدر تقارير مفصلة عن منتجاتك</h2>
                <p>تتبع مدى توفر منتجاتك ومستوياتها في مستودعك من خلال تقارير مفصلة بما في ذلك الجرد ومبيعات المنتجات
                    وملخص عمليات المخزون والحركة التفصيلية للمخزون وملخص رصيد المخازن، إلى جانب تقارير تتبع المنتجات إما
                    حسب الرقم المسلسل أو رقم الشحنة أو تاريخ الانتهاء، بالإضافة إلى تقارير المنتجات المجمعة بما في ذلك
                    تقارير دليل المنتجات المجمعة أو الأرصدة المتاحة للمنتجات المجمعة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/stock-detailed.webp') }}"
                    alt="أصدر تقارير مفصلة عن منتجاتك">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة المنتجات',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
