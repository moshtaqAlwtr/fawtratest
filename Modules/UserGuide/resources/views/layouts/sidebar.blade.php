<div class="col-lg-3 col-12 col-sidebar">
    <div class="quick-nav">
        <ul>
            <li>
                <a href="{{ route('userguide') }}" class="{{ request()->routeIs('userguide') ? 'active' : '' }}">
                    <i class="fas fa-cogs"></i>
                    <span class="condensed">لوحة التحكم</span>
                </a>
            </li>
            <li>
                <a href="{{ route('paymentUser') }}" class="{{ request()->routeIs('paymentUser') ? 'active' : '' }}">
                    <i class="fas fa-money-check-alt"></i>
                    <span class="condensed">مدفوعاتك</span>
                </a>
            </li>
            <li>
                <a href="#" class="has-subnav">
                    <i class="fas fa-money-bill-wave"></i>
                    <span>برنامج الربح والشراكة</span>
                </a>
                <ul class="subnav">
                    <li><a href="{{ route('myCompany') }}"
                            class="{{ request()->routeIs('myCompany') ? 'active' : '' }}"><span class="condensed">رابط
                                الشراكة الخاص بي</span></a></li>
                    <li> <a href="{{ route('referrals') }}"
                            class="{{ request()->routeIs('referrals') ? 'active' : '' }}"><span
                                class="condensed">المسجلين من خلالى</span></a></li>
                    <li> <a href="{{ route('accountStatement') }}"
                            class="{{ request()->routeIs('accountStatement') ? 'active' : '' }}"><span
                                class="condensed">رصيد أرباحي</span></a></li>
                    <li><a href="{{ route('activateCouponPage') }}"
                            class="{{ request()->routeIs('activateCouponPage') ? 'active' : '' }}"><span
                                class="condensed">تفعيل قسيمة</span></a></li>
                </ul>
            </li>
            <li>
                <a href="#" class="has-subnav">
                    <i class="fas fa-user-tie"></i>
                    <span class="condensed">الملف الشخصى</span>
                </a>
                <ul class="subnav">
                    <li><a href="{{ route('changeEmailPage') }}"
                            class="{{ request()->routeIs('changeEmailPage') ? 'active' : '' }}"> تغيير البريد
                            الالكتروني</a></li>
                    <li><a href="{{ route('changePassword') }}"
                            class="{{ request()->routeIs('changePassword') ? 'active' : '' }}">تغير كلمة المرور</a>
                    </li>
                    <li><a href="{{ route('payment.settings') }}"
                            class="{{ request()->routeIs('payment.settings') ? 'active' : '' }}">اعدادات المدفوعات</a>
                    </li>
                </ul>
            </li>
            <li>
                <a href="{{ route('register') }}" class="{{ request()->routeIs('register') ? 'active' : '' }}">
                    <i class="fas fa-user-plus"></i>
                    <span class="condensed">إنشاء حساب جديد</span>
                </a>
            </li>
            <li>
                <a href="#">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="condensed">تسجيل خروج</span>
                </a>
            </li>
        </ul>
    </div>
</div>
