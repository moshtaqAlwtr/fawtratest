<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <h1>إدارة محلات الحدايد والبويات</h1>
                <p>برنامج إدارة محلات الحدايد والبويات من فوترة يضمن لك إدارة مخزونك من أصناف البويات المتنوعة بشكل سهل
                    التصنيف والتتبع، ويضمن لك عمليات بيع أسهل، ويمنحك قدرة أكبر على متابعة أداء العاملين.</p>

                <div class="hero-features">
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>أصدر الفواتير الإلكترونية المعتمدة</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>تأكد من بساطة ودقة جوانبك المحاسبية</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>صنف المخزون بأبسط طريقة لتستطيع تتبعها</span>
                    </div>
                    <div class="hero-feature">
                        <span>شجع موظفيك على الوصول لأفضل أداء لهم</span>
                        <i class="fas fa-check-circle"></i>

                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>احصل على أفضل تقارير محاسبية يمكنك الحصول عليها</span>
                    </div>
                    <div class="hero-feature">
                        <i class="fas fa-check-circle"></i>
                        <span>قارن أرباحك بالمصاريف التي تبذلها</span>
                    </div>
                </div>

                <button class="cta-button">ابدأ الاستخدام مجانًا</button>
            </div>

            <div class="hero-image">
                <img src="{{ asset('assets/images/user_guide/page-banner.jpeg') }}"
                    alt="برنامج إدارة محلات الحدايد والبويات">
            </div>
        </div>
    </div>
</section>

<!-- Electronic Invoices Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>أصدر الفواتير الإلكترونية المعتمدة</h2>
                <p>أنواع الدهانات وألوانها العديدة لن يجعل أمر إصدار الفواتير أصعب عليك بعد الآن، حدد الصنف المراد في
                    الفاتورة بسهولة واحتسب ضرائبك بشكل آلي لحظي، وتأكد من كون الفواتير معتمدة وتتبع المعايير المطلوبة.
                </p>
                <p>هذا بالإضافة لخصائص عديدة على الفواتير مثل التقسيط أو إصدار مرتجع للفاتورة أو تخصيص أسعار مميزة
                    للمنتجات لعملاء محددين من خلال قوائم الأسعار.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/catering-accounting.jpeg') }}"
                    alt="الفواتير الإلكترونية المعتمدة">
            </div>
        </div>
    </div>
</section>

<!-- Accounting Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/gym-employees.jpeg') }}" alt="الجوانب المحاسبية">
            </div>
            <div class="section-text">
                <h2>تأكد من بساطة ودقة جوانبك المحاسبية</h2>
                <p>قم بإنشاء قيودك المحاسبية وسجل أصولك واحتسب الإهلاكات وسجل في شجرة الحسابات كل العمليات بصورة مقسّمة
                    بأفضل تنظيم شجري، واستمتع بضبط مذهل لعملك المحاسبي بأقل مجهود يمكن أن تبذله.</p>
                <p>واحتسب الضرائب والمصروفات ضمن نظام يقارن بين ما تنفقه وأرباحك، وقم بتعيين مراكز التكلفة لديك، وافصل
                    أو ادمج بين حسابات الفروع المختلفة، لتصل في النتيجة لمعرفة حقيقية لقدر ما تكسبه أو تخسره.</p>
            </div>
        </div>
    </div>
</section>

<!-- Inventory Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>صنف المخزون بأبسط طريقة لتستطيع تتبعها</h2>
                <p>أضف أنواع الدهانات والحدايد المتنوعة لمخزونك في برنامج إدارة محلات الحدايد والبويات من فوترة، مع
                    تحديد ألوان الدهانات وتميزها بوسوم تسمح لك بالوصول لها بسهولة حين البحث أو عند إنشاء الفواتير.</p>
                <p>وراقب الحد المخزوني عندك وتلقى إشعار للتنبيه قبل النفاد لتزويدك بالأصناف المطلوبة لحظة بلحظة. كما
                    يمكنك إدارة أكثر من مستودع في نفس الوقت ويمكنك تحويل البضائع من مستودع لآخر، مع مراقبة دقيقة لحركة
                    المخزون وإعطاء أذون الصرف أو الإضافة قبل أي حركة.</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/stock-sc.jpeg') }}" alt="تصنيف المخزون">
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
@include('userguide::layouts.business_areas.cta_section')

<!-- Employee Management Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/barocde-sc.jpeg') }}" alt="إدارة الموظفين">
            </div>
            <div class="section-text">
                <h2>شجع موظفيك على الوصول لأفضل أداء لهم</h2>
                <p>مع احتساب الحضور والانصراف وساعات العمل ومدى التزام كل موظف بورديته، وكفاءة جهده المبذول، تستطيع أن
                    تصرف المكافآت وتخصم من الموظفين بشكل يضمن لك إصدار الرواتب بشكل عادل، ودون جهد إضافي كبير لمراقبة كل
                    موظف وحده.</p>
                <p>حيث ترتبط كل العمليات داخل برنامج إدارة الموارد البشرية لمحلات الحدايد والبويات من فوترة. كما يمكنك
                    إعطاء الصلاحيات للموظفين طبقًا لدورهم الوظيفي والمسؤوليات الموكلة لهم.</p>
            </div>
        </div>
    </div>
</section>

<!-- Reports Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-text">
                <h2>احصل على أفضل تقارير محاسبية يمكنك الحصول عليها</h2>
                <p>تقارير للمبيعات وتقارير للمشتريات وتقارير للمخزون وأخرى للعاملين عندك وتقارير تتعلق بعملياتك
                    المحاسبية وقوائم مالية، كل هذا وأكثر تتحكم في طريقة إصداره وتقوم بتحميله بالصيغة الأنسب لك.</p>
                <p>مما يساعدك على تحليل وضعك المالي والوصول لأفضل قرارات مستقبلية ممكنة لرفع كفاءة العمل والموظفين. كما
                    يمكنك إصدار هذه التقارير بشكل مفصّل أو مجمع لتركز على جانب واحد من جوانب العمل أو لكي تلقي نظرة كلية
                    على ما يحدث بشكل يومي أو أسبوعي أو شهري لديك!</p>
            </div>
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/perfor.jpeg') }}" alt="التقارير المحاسبية">
            </div>
        </div>
    </div>
</section>

<!-- Profit Analysis Section -->
<section class="content-section">
    <div class="container">
        <div class="section-content">
            <div class="section-image">
                <img src="{{ asset('assets/images/user_guide/income-sc.jpeg') }}" alt="مقارنة الأرباح والمصاريف">
            </div>
            <div class="section-text">
                <h2>قارن أرباحك بالمصاريف التي تبذلها</h2>
                <p>اعرف مصاريف محلات الحدايد والبويات خاصتك من إيجارات ومصاريف تشغيلية ومرتبات للعمال وتكلفة البضائع،
                    لكل فرع وكلها مجملة، وأنسب مصروفات كل فرع وكل حركة تشغيلية لمركز التكلفة الخاص بها.</p>
                <p>وقارن بين المصروفات والدخل، لتعرف أرباحك بدقة ومصادر هذه الأرباح، وهو ما يساعدك على النظر في الجوانب
                    التي يمكن بضبطها أن تحصل على أرباح أكثر فتركز عليها، وكذا الجوانب التي تهلك الكثير من الأموال مقابل
                    أرباح بسيطة لربما يمكنك الحد منها.</p>
            </div>
        </div>
    </div>
</section>

<!-- System Integration Section -->
@include('userguide::layouts.business_areas.system_integration')


<!-- Why Choose Us Section -->
@include('userguide::layouts.business_areas.why_choose_us')
