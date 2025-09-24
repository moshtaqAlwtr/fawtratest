<!-- Offers Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة العروض</h2>
                <p>بفضل الأدوات المتقدمة في نظام فوترة، يمكنك إنشاء مختلف العروض على منتجاتك أو خدماتك بسهولة في واجهة
                    سهلة الاستخدام تدعم اللغة العربية، وكذلك الاختيار من بين أنواع متعددة من العروض بناءً على الفترة
                    الزمنية مثل العروض الموسمية؛ وذلك لتحديد الخصم سواء كان خصم نسبة أو مبلغ معين، مع إمكانيات أتمتة
                    تاريخ البدء والانتهاء لهذه العروض دون تدخل يدوي للتركيز على تحقيق أرباحك المستهدفة من المبيعات.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-point.webp') }}" alt="برنامج إدارة العروض">
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

<!-- Sales Features Icons Section -->
@include('userguide::layouts.programs.sales.sales_features_icons_section')
<!-- Time-based Offers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/automate-duration.webp') }}"
                    alt="صمم عروضك بناءً على فترة زمنية">
            </div>
            <div class="section-text">
                <h2>صمم عروضك بناءً على فترة زمنية</h2>
                <p>وفر مختلف العروض لعملائك ليتم تطبيقها في فترة زمنية معينة، حدد تواريخ توافر العروض والعروض الموسمية
                    وسيتولى النظام تعيين تاريخي البدء والانتهاء تلقائيًا دون تدخل يدوي.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Discount Types Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>خصص أنواع العروض والخصومات على منتجاتك</h2>
                <p>يوفر لك فوترة العديد من الخيارات التي يمكنك استخدامها لتطبيق عروضك أو خصوماتك على المنتجات في متجرك،
                    حيث يمكنك الاختيار من بين "خصم على الصنف" أو "شراء عدد من الأصناف وتطبيق خصم على القطعة الأرخص"،
                    وعندها يمكنك تحديد نوع الخصم المطبق على القطعة الأرخص سواء نسبة معينة أو كمية.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/discount.webp') }}"
                    alt="خصص أنواع العروض والخصومات على منتجاتك">
            </div>
        </div>
    </div>
</section>

<!-- Flexible Offers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/bulk-sale.webp') }}"
                    alt="تجربة مرنة لتطبيق العروض والخصومات">
            </div>
            <div class="section-text">
                <h2>تجربة مرنة لتطبيق العروض والخصومات</h2>
                <p>يوفر لك فوترة تجربة مرنة لتطبيق الخصم والعروض على منتجاتك، حيث يمكنك تطبيق الخصم على تصنيف أو عدة
                    تصنيفات معينة، أو يمكنك تطبيقه على عنصر واحد أو عدة عناصر داخل هذه التصنيفات. ويعمل النظام على تطبيق
                    الخصومات في فاتورة مبيعات العملاء آلياً وبيان نسبة الخصم في الفاتورة.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Promotional Offers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>زِد من حجم مبيعاتك مع العروض الترويجية بكفاءة</h2>
                <p>يمنحك فوترة حرية التنقل بين العروض وتغييرها بسهولة من خلال خيارات عديدة يمكنك تخصيصها حسب احتياجات
                    أعمالك، بما في ذلك تخفيضات كبيرة لفترة محدودة، أو شراء عدد معين من المنتج للحصول على عدد آخر مجاني،
                    إلى جانب الخصومات بالنسبة المئوية أو المبالغ والمشتريات المتعددة (2 بسعر 1) وكذلك العروض الموسمية.
                </p>

            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/dynamic-offer.webp') }}"
                    alt="زِد من حجم مبيعاتك مع العروض الترويجية بكفاءة">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة العروض',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
