<!-- Workflow Management System Section -->
<section class="content-section" style="padding:0px"></section>
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>برنامج إدارة دورة العمل</h2>
                <p>احصل على تجربة سير عمل أكثر سلاسة وتنظيمًا مع أدوات فوترة المتقدمة سهلة الاستخدام التي تتيح لك إدارة
                    مشروعاتك وحجوزاتك وتتبع الوقت لديك، باستخدام هذه الأدوات والخصائص ستتمكن من ضمان رضا عملائك، وإدارة
                    موظفيك وسير الأعمال لديك بكفاءة أكبر، ارفع مستوى إدارة أعمالك وعظم أرباح شركتك.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/operations.webp') }}" alt="برنامج إدارة دورة العمل">
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

<!-- Operations Management Features Icons Section -->
@include('userguide::layouts.programs.operations.operations_management_features_icons_section')

<!-- Customer Reservations Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/customer-service.webp') }}"
                    alt="نظِّم حجوزات عملائك بكفاءة">
            </div>
            <div class="section-text">
                <h2>نظِّم حجوزات عملائك بكفاءة</h2>
                <p>تتبح لك خصائص الأتمتة في فوترة إمكانية إنشاء الحجوزات على خدماتك حسب مجال عملك، حيث تتم خطوات الحجز
                    بسلاسة من خلال تحديد الخدمة المطلوبة للعميل وتعيين موظف لها حسب وردية عمله، مع إمكانية إنشاء فاتورة
                    لهذا الحجز متضمنة كافة بيانات وإرسالها للعميل مع إتاحة الحجز والدفع عبر الإنترنت للعملاء بسهولة.</p>
                <div style="text-align: right">
                    <button class="cta-button">ابدأ الاستخدام مجانًا</button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Time Tracking Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>تتبع وقت تنفيذ مشروعاتك وفوترها بسهولة</h2>
                <p>مع تطبيق تتبع الوقت في فوترة، ستتمكن من تتبع ساعات عمل موظفيك وفرقك بسهولة ودقة باستخدام سجلات زمنية
                    دقيقة قابلة للفوترة. خصص مشروعاتك وعينها إلى موظفيك وحدد معدل الساعة الافتراضي حسب كل خدمة في
                    المشروع أو معدل الساعات المخصص لكل مشروع وابدأ العمل، بحيث يمكنك تتبع إنتاجية الفرق في أي مكان وأي
                    وقت، بمجرد أن ينتهي أحد أعضاء الفريق من إنجاز المهمة المسندة إليه، أوقف المؤقت وحوِّل إدخالات تعقب
                    الوقت إلى فاتورة وأرسلها إلى العميل أو الموظف لإنهاء عملية الدفع.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/billable-hours.webp') }}"
                    alt="تتبع وقت تنفيذ مشروعاتك وفوترها بسهولة">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Project Management Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/work-order.webp') }}" alt="أدِر مشروعاتك بفعالية">
            </div>
            <div class="section-text">
                <h2>أدِر مشروعاتك بفعالية</h2>
                <p>احتفظ بكل المستندات ومراجع المعاملات المالية ومواعيد الاجتماعات ذات الصلة بالمشروع، وحدد مواعيد
                    التسليمات الجزئية أو النهائية مع العميل وفريق العمل لديك، كل ذلك في سجل نشاط واحد مخصص لكل مشروع على
                    حدة للحصول على تجربة قابلة للتخصيص تناسب طبيعة كل مشروع لديك.</p>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
@include('userguide::layouts.programs.prices', [
    'title' => 'أسعار برنامج إدارة دورة العمل',
])

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')

<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
