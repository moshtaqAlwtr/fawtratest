<!-- Check Cycle Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج دورة الشيكات</h2>
                <p>استخدم برنامج إدارة الشيكات من فوترة لتحصيل وإصدار الشيكات في مؤسستك، قم بتنظيم حالات الشيكات، وضبط
                    البيانات المطلوبة وتنظيمها في شكل دفاتر يتم ربط كل منها بأحد الحسابات البنكية لديك، كما يمكنك ضبط
                    قيد الشيك سواء لصرفه مباشر أو بتحديد قيد انتظار حلول تاريخ محدد للصرف، مع ربط كامل الدورة المحاسبية
                    للشيكات المصروفة والمستلمة بباقي برامج نظام فوترة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/page-banner-shick.webp') }}" alt="برنامج دورة الشيكات">
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


<!-- Manage Check Books Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/manage-cheque.webp') }}"
                    alt="أدِر دفاتر الشيكات الخاصة بك">
            </div>
            <div class="section-text">
                <h2>أدِر دفاتر الشيكات الخاصة بك</h2>
                <p>قم بتسجيل دفاتر الشيكات التي تمتلكها، مع التحكم في عملة كل دفتر والحساب البنكي المرتبط به وغيرها من
                    الصلاحيات. كما لك القدرة على متابعة الشيكات الصادرة من كل دفتر من خلال رقم الشيك، ومراقبة حالة كل
                    شيك ومعرفة الشيكات المدفوعة والمبالغ المصروفة من كل فوترة، بالإضافة إلى إمكانية تعطيل العمل بدفتر
                    شيكات لفترة محددة ثم إعادة تفعيله وقتما أردت.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Track Issued Checks Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>تتبع الشيكات المُصدَّرة عنك</h2>
                <p>الشيكات المدفوعة لك أن تصدرها محدِدًا موعد الإصدار وموعد الاستحقاق الموجب للصرف، مع تسجيل بيانات
                    الشيك المطلوبة كرقم الشيك، والبنك المتاح للمستلم صرف الشيك من خلاله، ودفتر الشيكات التابع له هذا
                    الشيك، ومكانه بدفتر الحسابات، ثم بعد الإصدار يمكنك توثيق حالة الشيك سواء حصّل المستلم الشيك أو رفضه،
                    ويمكنك تتبع الإجراءات المنفذة على الشيك مع معرفة الفاعل والتوقيت.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/record-issued.webp') }}" alt="تتبع الشيكات المُصدَّرة عنك">
            </div>
        </div>
    </div>
</section>

<!-- Register Received Checks Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/register-payments.webp') }}" alt="سجّل الشيكات المستلمة">
            </div>
            <div class="section-text">
                <h2>سجّل الشيكات المستلمة</h2>
                <p>يمكنك توثيق عملية استلام الشيك التي قد تُختَزل في التحصيل أو الرفض الآني من البنك، أو أن تعيد قيد
                    الشيك قيد عكسي في حالة اكتشافك عدم وجود رصيد في حساب من أصدر لك الشيك لتلاشي قيد الاستلام. أو قم
                    بتظهير الشيك لشخص آخر لتمكينه من صرف مبلغ الشيك المُسَلم لك مع تسوية الحسابات الخاصة بعملية التظهير،
                    وستجد حالات متعددة لوسم الشيكات بها كرفض الشيك، إيداعه، أو تحصيله.</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Check Movement Reports Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>اعرض التقارير الخاصة بحركة الشيكات</h2>
                <p>بالإضافة للتقارير الخاصة بالمدفوعات والمقبوضات بشكل عام، هناك تقارير للشيكات المدفوعة وتقارير أخرى
                    للشيكات المستلمة، وعدد كبير من أدوات الفلترة لإصدار تقرير مثالي يوافق البيانات المراد الاحتفاظ بها
                    أو تحليلها.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/reporting.webp') }}"
                    alt="اعرض التقارير الخاصة بحركة الشيكات">
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج دورة الشيكات',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
