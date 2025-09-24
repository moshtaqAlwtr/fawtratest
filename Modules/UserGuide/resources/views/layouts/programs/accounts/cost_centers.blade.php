<!-- Cost Center Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة مراكز التكلفة</h2>
                <p>أدِر مراكز التكلفة والربح في أعمالك باستخدام الأدوات المتقدمة سهلة الاستخدام المضمنة في نظام فوترة،
                    قم بتعيين مركز تكلفة واحد أو أكثر لإدخالات قيود اليومية ونظِّمها في شكل شجري، وخصص التكاليف وراقب
                    الإيرادات والمصروفات لديك بدقة، حيث يمكنك التصفية حسب الحسابات المسجلة، ويمكنك أيضًا تتبع حركات
                    مراكز التكلفة المعينة والبقاء على اطلاع بالتدفق النقدي لديك باستخدام تقارير مركز التكلفة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-rr.webp') }}" alt="برنامج إدارة مراكز التكلفة">
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

<!-- General Accounting Features Icons Section -->
@include('userguide::layouts.programs.accounts.accounting_features_icons_section')



<!-- Monitor Cost Centers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/monitor-efficiency.webp') }}"
                    alt="راقب مراكز التكلفة بكفاءة">
            </div>
            <div class="section-text">
                <h2>راقب مراكز التكلفة بكفاءة</h2>
                <p>حدد الأقسام كمراكز تكلفة ووزع تكاليفها العامة على كل مركز تكلفة مخصص. وتعقب الحسابات التي تم تعيينها
                    لكل مركز تكلفة، واعرض كافة المعاملات ذات الصلة وأدر مراكز التكلفة في تنظيم شجري دقيق. كما يتيح لك
                    فوترة إمكانية اتخاذ القرارات والإجراءات اللازمة فيما يتعلق بالتكلفة والأقسام من خلال التقارير تقارير
                    تفصيلية.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Allocate Cost Centers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>عيِّن مراكز التكلفة للحسابات والحركات المالية</h2>
                <p>يتيح لك النظام إمكانية تتبع تكاليف على نحو دقيق عند تعيين مراكز التكلفة. حيث يمكنك تعيين مركز تكلفة
                    واحد أو أكثر لحسابات قيود اليومية وتخصيص النسب المئوية لتوزيعها، وذلك إما آليًا أو يدويًا حسب حاجة
                    أعمالك.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/allocate-costs.webp') }}"
                    alt="عيِّن مراكز التكلفة للحسابات والحركات المالية">
            </div>
        </div>
    </div>
</section>

<!-- Automate Cost Centers Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/automate-cost.webp') }}"
                    alt="نظم مراكز التكلفة إما آليًا أو يدويًا">
            </div>
            <div class="section-text">
                <h2>نظم مراكز التكلفة إما آليًا أو يدويًا</h2>
                <p>أدِر مراكز التكلفة لديك من خلال تخصيصها للحساب نفسه أو تعيينها يدويًا أثناء إرسال المعاملة بما في ذلك
                    تسجيل الإيرادات أو المصاريف أو قيود اليومية.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Track Revenue and Expenses Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>كن على اطلاع دائم بالإيرادات والمصروفات والأرباح</h2>
                <p>تتبع معاملاتك بسهولة وتعرف على المصروفات والأرباح والخسائر بدقة لاتخاذ القرارات اللازمة لتحقيق أهداف
                    أعمالك، وذلك من خلال التقارير المفصلة لمراكز التكلفة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/form-insights.webp') }}"
                    alt="كن على اطلاع دائم بالإيرادات والمصروفات والأرباح">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة مراكز التكلفة',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
