<div class="programs-modal-backdrop" id="programsModalWindow" style="display: none;">
    <div class="programs-modal-container">
        <button class="programs-close-button" onclick="closeProgramsModalWindow()">
            <i class="fas fa-times"></i>
        </button>

        <div class="programs-sidebar-area">
            <div class="programs-sidebar-block">
                <ul class="programs-sidebar-menu">
                    <li><a href="{{ route('userguide') }}">الصفحة الرئيسية</a></li>
                    <li><a href="#" onclick="openBusinessAreasModal()">مجالات العمل</a></li>
                    <li><a href="{{ route('programs.features') }}">المزايا</a></li>
                    <li><a href="{{ route('prices') }}">الأسعار</a></li>
                    <li><a href="{{ route('programs.userguides') }}">دليل الاستخدام</a></li>
                </ul>
            </div>

            <div class="programs-additional-section">
                <h3 class="programs-sidebar-header">المزيد</h3>
                <ul class="programs-sidebar-menu">
                    <li><a href="{{ route('programs.fawtura.agents') }}">وكلاء فوترة</a></li>
                    <li><a href="{{ route('programs.account.setup.services') }}">خدمات تهيئة الحساب</a></li>
                    <li><a href="{{ route('programs.accounting.services') }}">الخدمات المحاسبية</a></li>
                    <li><a href="{{ route('programs.our.clients') }}">عملائنا</a></li>
                    <li><a href="{{ route('programs.about.fawtura') }}">عن فوترة</a></li>
                    <li><a href="{{ route('programs.blog') }}">المدونة</a></li>
                    <li><a href="{{ route('programs.contact.us') }}">تواصل معنا</a></li>
                    <li><a href="{{ route('programs.learning.center') }}">المركز التعليمي</a></li>
                    <li><a href="{{ route('programs.latest.updates') }}">آخر التحديثات</a></li>
                </ul>
            </div>
        </div>

        <div class="programs-main-area">
            <!-- قسم المبيعات -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.sales') }}"><i class="fas fa-arrow-right"></i> المبيعات</a>
                </h2>
                <div class="programs-features-layout">
                    <a class="programs-feature-card" href="{{ route('programs.sales') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-file-invoice"></i>
                        </div>
                        <span class="programs-feature-label">المبيعات</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.invoices.quotes') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <span class="programs-feature-label">الفواتير وعروض الأسعار</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.point.of.sale') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <span class="programs-feature-label">نقاط البيع</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.offers') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-tags"></i>
                        </div>
                        <span class="programs-feature-label">العروض</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.installments') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="programs-feature-label">الأقساط</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.targeted.sales.commissions') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <span class="programs-feature-label">المبيعات المستهدفة والعمولات</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.insurance') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <span class="programs-feature-label">التأمينات</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.saudi.electronic.invoice') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <span class="programs-feature-label">الفاتورة الإلكترونية السعودية - هيئة الزكاة
                            والدخل</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.egyptian.electronic.invoice') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <span class="programs-feature-label">الفاتورة الإلكترونية المصرية - مصلحة الضرائب</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.jordanian.electronic.invoice') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <span class="programs-feature-label">الفاتورة الإلكترونية الأردنية - دائرة ضريبة الدخل
                            والمبيعات</span>
                    </a>
                </div>
            </div>

            <!-- قسم العملاء -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.customer.management') }}"><i class="fas fa-arrow-right"></i>
                        العملاء</a>
                </h2>
                <div class="programs-features-layout">
                    <a class="programs-feature-card" href="{{ route('programs.customer.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-user-cog"></i>
                        </div>
                        <span class="programs-feature-label">إدارة العملاء</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.customer.follow.up') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <span class="programs-feature-label">متابعة العملاء</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.customer.loyalty.points') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-star"></i>
                        </div>
                        <span class="programs-feature-label">نقاط الولاء للعملاء</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.points.balances') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-coins"></i>
                        </div>
                        <span class="programs-feature-label">النقاط والأرصدة</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.subscriptions.memberships') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <span class="programs-feature-label">الاشتراكات والعضويات</span>
                    </a>

                </div>
            </div>

            <!-- قسم الحسابات -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.accounts') }}"><i class="fas fa-arrow-right"></i> الحسابات</a>
                </h2>
                <div class="programs-features-layout">

                    <a class="programs-feature-card"href="{{ route('programs.expenses') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-money-bill-wave"></i>
                        </div>
                        <span class="programs-feature-label">المصروفات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.accounting.program') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-chart-line"></i>
                        </div>
                        <span class="programs-feature-label">برنامج محاسبي</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.chart.of.accounts') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-book"></i>
                        </div>
                        <span class="programs-feature-label">دليل الحسابات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.asset.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="programs-feature-label">إدارة الأصول</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.cost.centers') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-bullseye"></i>
                        </div>
                        <span class="programs-feature-label">مراكز التكلفة</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.check.cycle') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <span class="programs-feature-label">دورة الشيكات</span>
                    </a>
                </div>
            </div>

            <!-- قسم المخزون -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.inventory.warehouses') }}"><i class="fas fa-arrow-right"></i>
                        المخزون</a>
                </h2>
                <div class="programs-features-layout">
                    <a class="programs-feature-card"href="{{ route('programs.inventory.warehouses') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-boxes"></i>
                        </div>
                        <span class="programs-feature-label">المخزون والمستودعات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.product.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-cube"></i>
                        </div>
                        <span class="programs-feature-label">إدارة المنتجات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.purchases') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <span class="programs-feature-label">المشتريات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.purchase.cycle') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <span class="programs-feature-label">دورة المشتريات</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.supplier.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-truck"></i>
                        </div>
                        <span class="programs-feature-label">إدارة الموردين</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.inventory.permissions') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="programs-feature-label">الأذون المخزنية</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.inventory.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-search"></i>
                        </div>
                        <span class="programs-feature-label">إدارة الجرد</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.manufacturing.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-industry"></i>
                        </div>
                        <span class="programs-feature-label">إدارة التصنيع</span>
                    </a>
                    <a class="programs-feature-card" href="{{ route('programs.production.order.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-cogs"></i>
                        </div>
                        <span class="programs-feature-label">إدارة أوامر الإنتاج</span>
                    </a>
                </div>
            </div>

            <!-- قسم شؤون الموظفين -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.employee.affairs') }}"><i class="fas fa-arrow-right"></i> شؤون
                        الموظفين</a>
                </h2>
                <div class="programs-features-layout">
                    <a class="programs-feature-card"href="{{ route('programs.employee.affairs') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="programs-feature-label">شؤون الموظفين</span>
                    </a>
                    <a
                        class="programs-feature-card"href="{{ route('programs.organizational.structure.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-sitemap"></i>
                        </div>
                        <span class="programs-feature-label">إدارة الهيكل التنظيمى</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.attendance.departure') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-clock"></i>
                        </div>
                        <span class="programs-feature-label">الحضور والانصراف</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.contract.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-file-contract"></i>
                        </div>
                        <span class="programs-feature-label">إدارة العقود</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.salary.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-money-check-alt"></i>
                        </div>
                        <span class="programs-feature-label">إدارة المرتبات</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.request.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-clipboard-list"></i>
                        </div>
                        <span class="programs-feature-label">إدارة الطلبات</span>
                    </a>
                </div>
            </div>
            <!-- قسم التشغيل -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.operations') }}"><i class="fas fa-arrow-right"></i> التشغيل</a>
                </h2>
                <div class="programs-features-layout">

                    <a class="programs-feature-card"href="{{ route('programs.work.cycle') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-sync-alt"></i>
                        </div>
                        <span class="programs-feature-label">دورة العمل</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.work.orders') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span class="programs-feature-label">أوامر الشغل</span>
                    </a>

                    <a class="programs-feature-card"href="{{ route('programs.reservations') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <span class="programs-feature-label">الحجوزات</span>
                    </a>

                    <a class="programs-feature-card"href="{{ route('programs.rental.unit.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="programs-feature-label">برنامج إدارة الإيجارات والوحدات</span>
                    </a>

                    <a class="programs-feature-card"href="{{ route('programs.time.tracking') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-stopwatch"></i>
                        </div>
                        <span class="programs-feature-label">تتبع الوقت</span>
                    </a>
                </div>
            </div>

            <!-- قسم تطبيقات الجوال -->
            <div class="programs-section-block">
                <h2 class="programs-section-header">
                    <a href="{{ route('programs.mobile.apps') }}"><i class="fas fa-arrow-right"></i> تطبيقات
                        الجوال</a>
                </h2>
                <div class="programs-features-layout">
                    <a class="programs-feature-card"href="{{ route('programs.mobile.apps') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-mobile-alt"></i>
                        </div>
                        <span class="programs-feature-label">تطبيقات فوترة للجوال</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.mobile.business.management') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-briefcase"></i>
                        </div>
                        <span class="programs-feature-label">تطبيق إدارة الإعمال للجوال</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.mobile.pos') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-cash-register"></i>
                        </div>
                        <span class="programs-feature-label">تطبيق نقاط البيع للجوال</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.desktop.pos') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-desktop"></i>
                        </div>
                        <span class="programs-feature-label">تطبيق نقاط البيع سطح المكتب</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.mobile.attendance') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <span class="programs-feature-label">تطبيق تسجيل الحضور للجوال</span>
                    </a>
                    <a class="programs-feature-card"href="{{ route('programs.mobile.expense.tracking') }}">
                        <div class="programs-feature-symbol">
                            <i class="fas fa-receipt"></i>
                        </div>
                        <span class="programs-feature-label">تطبيق تسجيل المصروفات السريع للجوال</span>
                    </a>
                </div>

                <a class="programs-feature-card"href="{{ route('programs.mobile.invoice.reader') }}">
                    <div class="programs-feature-symbol">
                        <i class="fas fa-qrcode"></i>
                    </div>
                    <span class="programs-feature-label">تطبيق قراءة الفاتورة الإلكترونية للجوال</span>
                </a>
            </div>
        </div>
    </div>
</div>

<script>
    function openProgramsModalWindow() {
        document.getElementById('programsModalWindow').style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    function closeProgramsModalWindow() {
        document.getElementById('programsModalWindow').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // إغلاق النافذة عند النقر خارج المحتوى
    document.getElementById('programsModalWindow').addEventListener('click', function(e) {
        if (e.target === this) {
            closeProgramsModalWindow();
        }
    });

    // إغلاق النافذة باستخدام مفتاح Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeProgramsModalWindow();
        }
    });
</script>
