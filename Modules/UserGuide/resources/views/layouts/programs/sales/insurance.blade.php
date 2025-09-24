<!-- Insurance Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة تأمينات العملاء</h2>
                <p>أدِر تأمينات العملاء وشركات التأمين باحترافية مع تطبيق التأمينات الذي يوفره لك نظام فوترة بواجهة سهلة
                    الاستخدام متضمنة أدوات وخصائص مؤتمتة مع دعم كامل للغة العربية، احفظ بيانات الشركات المتعاقد معها
                    وحدد شروطهم التأمينية ونسب الخصم للعملاء المنتفعين. احصل على المرونة اللازمة في تحديد نسب التأمين
                    وإمكانية تعريف العديد من الفئات والشرائح التأمينية لدى كل شركة. حدد المنتجات والخدمات التي تخضع كل
                    منها لنسب التأمين المتفق عليها، حيث سيعمل فوترة على تطبيق نسب التأمين والخصم آليًا في الفواتير
                    الصادرة للعملاء المنتفعين؛ إلى جانب إمكانية إصدار الفواتير المستحقة على شركات التأمين بسلاسة على نحو
                    دقيق.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/insurance.webp') }}" alt="برنامج إدارة تأمينات العملاء">
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
<!-- Insurance Companies Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/client-invoice-sc.png') }}"
                    alt="عرِّف شركات التأمين المتعاقد معها في حسابك">
            </div>
            <div class="section-text">
                <h2>عرِّف شركات التأمين المتعاقد معها في حسابك</h2>
                <p>فعِّل خصائص التأمين الطبي للعملاء في حسابك وابدأ في إضافة قائمة شركات التأمين المتعاقد معها في ملف
                    تعريفي يتضمن كل البيانات اللازمة لإتمام المعاملات بسلاسة. تمتع بتجربة مميزة مع الواجهة سهلة
                    الاستخدام عند تحديد الفئات والشرائح التأمينية لكل شركة، مع إمكانية عرض كافة الفواتير للمبالغ المطلوب
                    تحصيلها عن الفترات المحددة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام الآن</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Insurance Categories Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>حدد الفئات والشرائح التأمينية لكل شركة على حدة</h2>
                <p>في خطوات بسيطة؛ عرِّف مختلف الفئات التأمينية لكل شركة تأمين لديك، وحدد مجموعات المنتجات والخدمات
                    الخاضعة للتأمين في كل فئة، ثم ابدأ في تعيين هذه الفئات للعملاء المنتفعين من نظام التأمين في حسابك،
                    وسيقوم برنامج فوترة بتطبيق نسب التأمين والخصم على العملاء داخل الفواتير تلقائيًا، إلى جانب تحديد
                    قيمة السداد التشاركي الخاص بشركة التأمين وحساب صافي المستحق سداده من العميل، وذلك بفضل خصائص الأتمتة
                    المتقدمة من نظام فوترة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/co-payment-sc.png') }}"
                    alt="حدد الفئات والشرائح التأمينية لكل شركة على حدة">
            </div>
        </div>
    </div>
</section>

<!-- Insurance Rates Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/classes-sc.png') }}"
                    alt="حدد نسب التأمين والخصم على فئات المنتجات والخدمات بالحساب">
            </div>
            <div class="section-text">
                <h2>حدد نسب التأمين والخصم على فئات المنتجات والخدمات بالحساب</h2>
                <p>يتيح لك تطبيق إدارة التأمينات في فوترة إمكانية تحديد نسبة الغطاء التأميني للشركة ونسبة الخصم للعميل
                    إن وجد، وذلك على فئة أو أكثر من فئات المنتجات والخدمات الخاضعة للتأمين، إلى جانب إمكانية تحديد الحد
                    الأقصى لمبلغ التأمين الذي تتحمله الشركة عن كل فاتورة يتم إصدارها لأحد العملاء على الحساب.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة التأمينات',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
