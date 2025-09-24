<!-- Asset Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة الأصول</h2>
                <p>أدِر مواردك بفعالية مع تطبيق إدارة الأصول من فوترة بواجهة سهلة الاستخدام تدعم اللغة العربية. وهو ما
                    يساعدك في مقابلة أصولك بالخصوم لضبط قائمة المركز المالي وإدارة تدفقاتك النقدية، كما يعينك على معظمة
                    استغلال الأصول لزيادة الأرباح المولدة عنها.</p>
                <ul class="invoice-features-list-unique">
                    <li>أضف أصول أعمالك بمختلف أنواعها.</li>
                    <li>اضبط إعدادات الإهلاك واختر من بين أساليب الإهلاك المتوفرة.</li>
                    <li>يمكنك شطب الأصول أو بيعها أو إعادة تقييمها بدقة.</li>
                    <li>إمكانية إصدار التقارير ذات الصلة بما في ذلك دفتر الأستاذ العام وتقارير المعاملات المالية
                        والميزانية العمومية.</li>
                </ul>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-assets.webp') }}" alt="برنامج إدارة الأصول">
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


<!-- Organize Fixed Assets Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/fixed-assets.webp') }}" alt="نظم أصولك الثابتة بكفاءة">
            </div>
            <div class="section-text">
                <h2>نظم أصولك الثابتة بكفاءة</h2>
                <p>يتيح لك برنامج الأصول الثابتة من فوترة تنظيمًا فعالًا لأصولك الثابتة مع أتمتة احترافية للإهلاكات، حيث
                    يمكنك إدارة قيم الأصل بكل أنواعها وتعيين الأصل إلى موظف معين وتصنيف الأصول ضمن فئات محددة، كما يمكنك
                    تتبع الأصول الموجودة قيد الخدمة وكذلك عرض تقارير مفصلة وتاريخ المعاملات والعمليات ذات الصلة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Depreciation Settings Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>اضبط إعدادات الإهلاك بدقة</h2>
                <p>يمكنك من خلال برنامج الأصول الثابتة وإهلاكها تحديد طريقة الإهلاك بناءً على الأصل، وكذلك فترة الإهلاك
                    والمبلغ وسيقوم النظام بتحديث الإهلاك الدوري تلقائيًا حتى نهاية دورة حياة الأصل.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/depreciation-rate.webp') }}"
                    alt="اضبط إعدادات الإهلاك بدقة">
            </div>
        </div>
    </div>
</section>

<!-- Different Depreciation Methods Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/different-methods.webp') }}"
                    alt="طبِّق طرق الإهلاك المناسبة لمتطلبات أعمالك">
            </div>
            <div class="section-text">
                <h2>طبِّق طرق الإهلاك المناسبة لمتطلبات أعمالك</h2>
                <p>يدعم نظام إدارة الأصول من فوترة طرق حساب إهلاك مختلفة ويقوم بتطبيقها آليًا وفقًا لمبادئ المحاسبة
                    المقبولة عمومًا، بما في ذلك القسط الثابت والقسط المتناقص ووحدات الإنتاج.</p>
            </div>
        </div>
    </div>
</section>

<!-- Manual and Automatic Depreciation Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أضف الإهلاك يدويًا أو آليًا</h2>
                <p>يتم تنفيذ الإهلاك يدويًا استنادًا إلى تفاصيل إهلاك الأصل المحددة، ولكن يمكنك تعيين إدخالات الإهلاك
                    اليدوي أيضًا عن طريق تحديد تاريخ الإهلاك والتكلفة، وستتم إضافته إلى قائمة مبالغ الإهلاك للأصل.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/automate.webp') }}" alt="أضف الإهلاك يدويًا أو آليًا">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Asset Evaluation Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/evaluate-asset.webp') }}"
                    alt="اشطب الأصل أو قم ببيعه أو أعد تقييمه بسلاسة">
            </div>
            <div class="section-text">
                <h2>اشطب الأصل أو قم ببيعه أو أعد تقييمه بسلاسة</h2>
                <p>تحكم بسهولة في قيمة الأصول لصالحك من خلال برنامج إدارة أصول المؤسسة عن طريق شطب أحد الأصول أو بيعه أو
                    إعادة تقييمه اعتمادًا على نوعه وكذلك متطلبات أعمالك.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة الأصول',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
