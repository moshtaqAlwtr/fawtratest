<!-- قسم برنامج إدارة التصنيع -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة التصنيع</h2>
                <p>يساعد برنامج التصنيع على إدارة عملية الإنتاج بالكامل، بدءًا من تتبع مسارات ومراحل الإنتاج، وإضافة
                    محطات العمل، وإنشاء قوائم مواد الإنتاج التي تُحوَّل إلى أوامر التصنيع؛ إلى ضبط التكاليف المباشرة
                    وغير المباشرة وتوزيعها على المنتجات المُصنَّعة. بالإضافة لربط عملية التصنيع بالمخزون لتتبع المواد
                    الخام المُستهلَكة في العملية التصنيعية وكذا المنتجات النهائية والمواد الهالكة الناتجين عن التصنيع.
                </p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-maker.webp') }}" alt="برنامج إدارة التصنيع">
                @include('userguide::layouts.programs.btn_google_apple')
            </div>
        </div>
    </div>
</section>

<!-- قسم المسافات -->
<section class="fotra-spacing-section-large"></section>

@include('userguide::layouts.business_areas.cta_section_first')

<!-- قسم المسافات -->
<section class="fotra-spacing-section-large"></section>

<!-- Inventory Management Features Icons Section -->
@include('userguide::layouts.programs.inventory.inventory_management_features_icons_section')

<!-- قسم مراحل إنتاج سلسة ومنظمة -->
<section class="content-section">
    <div class="container">
        <div class="section-content">

            <div class="section-text">
                <h2>مراحل إنتاج سلسة ومنظمة</h2>
                <p>نظّم عملك من خلال مسارات الإنتاج؛ حيث يتكون كل مسار من عدة مراحل إنتاجية ويمكنك ربط كل محطة عمل
                    (عملية تصنيعية)/ مادة خام/ مصروف بالمرحلة الإنتاجية المتعلقة به. وهو ما يجعل تقارير التصنيع
                    مُصنَّفة، وكل معاملة منسوبة للمرحلة الإنتاجية التي حدثت بها، وهو ما ينتج عنه خطوات عمل مُرتَّبة،
                    وتتبع دقيق، وتقارير أكثر موثوقية.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_bom.png') }}"
                    alt="أضف قوائم مواد شاملة ومُفصَّلة">
            </div>
        </div>
    </div>
</section>

<!-- قسم تقدير دقيق للتكاليف المباشرة وغير المباشرة -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_costs.png') }}"
                    alt="تقدير دقيق للتكاليف المباشرة وغير المباشرة">
            </div>
            <div class="section-text">
                <h2>تقدير دقيق للتكاليف المباشرة وغير المباشرة</h2>
                <p>وَزِّع التكاليف المباشرة وغير المباشرة على عناصر العملية الإنتاجية، وصنف التكاليف وفقًا لطبيعتها،
                    واربط كل مصروف بالحساب المتعلق به إن أردت، وحمِّل التكلفة أيًا كان نوعها على المنتجات النهائية تبعًا
                    لطريقة احتساب التكاليف التي تعمل بها، وهو ما ييسر احتساب تسعير منتجاتك بناءً على احتساب دقيق لتكلفة
                    التصنيع.</p>
            </div>

        </div>
    </div>
</section>

<!-- قسم أضف قوائم مواد شاملة ومُفصَّلة -->
<section class="content-section">
    <div class="container">
        <div class="section-content">

            <div class="section-text">
                <h2>أضف قوائم مواد شاملة ومُفصَّلة</h2>
                <p>أنشئ قائمة مواد الإنتاج باعتبارها قالب؛ ترتكز عليه لإنشاء أوامر تصنيع دقيقة. ويمكنك الاحتفاظ بنفس
                    مكونات وكميات قائمة مواد الإنتاج في أمر التصنيع الناشىء عنها أو التعديل عليه ليناسب طلبات التصنيع
                    المخصصة لأحد عملائك أو لفترة معينة. وتتضمن قوائم مواد الإنتاج المنتج النهائي المراد تصنيعه والمواد
                    الخام والهالك، بالإضافة لمحطات العمل والتكاليف الإنتاجية.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_bom.png') }}"
                    alt="أضف قوائم مواد شاملة ومُفصَّلة">
            </div>
        </div>
    </div>
</section>

<!-- قسم أوامر تصنيع مخصصة لعملياتك -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_inventory.png') }}"
                    alt="أوامر تصنيع مخصصة لعملياتك">
            </div>
            <div class="section-text">
                <h2>أوامر تصنيع مخصصة لعملياتك</h2>
                <p>أنشئ أوامر التصنيع بناءً على قائمة المواد الخاصة بك، وحدد بها كافة التكاليف المالية والتشغيلية الخاصة
                    بالمنتج. وبناءً على أمر التصنيع تبدأ التوجيهات التنفيذية فتستطيع صرف المواد الخام من المخزون،
                    واستقبال المنتج النهائي بعد التصنيع في مستودعاتك، لتنشأ الحركات المخزنية والقيود المحاسبية نتيجة
                    لذلك، كما يتم إنشاء تقارير التكاليف آليًا.</p>
            </div>

        </div>
    </div>
</section>

<!-- قسم أدر العمليات التشغيلية عبر محطات العمل -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أدر العمليات التشغيلية عبر محطات العمل</h2>
                <p>ربط أوامر التصنيع بمحطات عمل معينة يسمح لك باحتساب الجوانب التشغيلية المتعلقة بمحطة العمل؛ فتُحتسَّب
                    التكلفة تبعًا لفترة التشغيل وتكلفة الأجور الخاصة بعمال هذه المحطة، وتكلفة الأصول المستخدمة في عملية
                    التصنيع، وأي مصروفات أخرى.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_workstation.png') }}"
                    alt="أدر العمليات التشغيلية عبر محطات العمل">
            </div>
        </div>
    </div>
</section>

<!-- قسم أصدر تقارير التكاليف في لحظات -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manufacturing_finance.png') }}"
                    alt="أصدر تقارير التكاليف في لحظات">
            </div>
            <div class="section-text">
                <h2>أصدر تقارير التكاليف في لحظات</h2>
                <p>أصدر تقارير مفصلة للتكاليف؛ وحلل وتتبع تكاليف كل مرحلة إنتاجية، حتى تتمكن من معالجة أوجه القصور،
                    وتتخذ القرارات اللازمة لتخفيض تكاليف التشغيل وتحسين عملياتك التصنيعية القادمة.</p>
            </div>

        </div>
    </div>
</section>

<!-- قسم الأسعار -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة التصنيع',
])

<!-- قسم تكامل النظام -->
@include('userguide::layouts.business_areas.system_integration')

<!-- قسم لماذا تختارنا -->
@include('userguide::layouts.business_areas.why_choose_us')
