@php
    $getLocal = App::getLocale();
@endphp

<div class="main-menu menu-fixed menu-light menu-accordion menu-shadow" data-scroll-to-active="true">
    <div class="navbar-header">
        <ul class="nav navbar-nav flex-row">
            <li class="nav-item mr-auto"><a class="navbar-brand" href="#">
                    <div class="brand-logo"></div>
                    <h2 class="brand-text mb-0">فوترة</h2>
                </a>
            </li>

            <li class="nav-item nav-toggle"><a class="nav-link modern-nav-toggle pr-0" data-toggle="collapse"><i class="feather icon-x d-block d-xl-none font-medium-4 primary toggle-icon"></i><i class="toggle-icon feather icon-disc font-medium-4 d-none d-xl-block collapse-toggle-icon primary" data-ticon="icon-disc"></i></a></li>
        </ul>
    </div>

    <div class="shadow-bottom"></div>

    <div class="main-menu-content">

        <ul class="navigation navigation-main" id="main-menu-navigation" data-menu="menu-navigation">
            {{-- الرئيسيه --}}


            {{-- المبيعات --}}








                    <ul class="menu-content">
                        <li>
                            <a href="{{ route('clients.invoice_client') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item" data-i18n="Start Sale">الفواتير</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.questions_client') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item" data-i18n="Sessions">عروض الاسعار</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.questions_client') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item" data-i18n="POS Reports">اوامر الشغل</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('clients.appointments_client') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item"
                                    data-i18n="POS Settings">المواعيد</span>
                            </a>
                        </li>



            <!-- نقاط  البيع -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'points_sale_edit_product_prices',
                        'points_sale_add_discount',
                        'points_sale_open_sessions_all',
                        'points_sale_open_sessions_own',
                        'points_sale_close_sessions_all',
                        'points_sale_close_sessions_own',
                        'points_sale_view_all_sessions',
                        'points_sale_confirm_close_sessions_all',
                        'points_sale_confirm_close_sessions_own',
                        'points_sale_confirm_close_sessions_own',
                    ])
                )
                 {{-- نقاط البيع --}}
            @if(isset($settings['pos']) && $settings['pos'] === 'active')
            <li class="nav-item">
                <a href="#">
                    <i class="feather icon-monitor"></i>
                    <span class="menu-title" data-i18n="POS">{{ trans('main_trans.Point_of_Sale') }}</span>
                </a>
                <ul class="menu-content">
                    <li>
                        <a href="">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Start Sale">{{ trans('main_trans.Start_Sale') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="Sessions">{{ trans('main_trans.Sessions') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item" data-i18n="POS Reports">{{ trans('main_trans.POS_Reports') }}</span>
                        </a>
                    </li>
                    <li>
                        <a href="">
                            <i class="feather icon-circle"></i>
                            <span class="menu-item"
                                data-i18n="POS Settings">{{ trans('main_trans.POS_Settings') }}</span>
                        </a>
                    </li>
                </ul>
            </li>
            @endif
            @endif

            {{-- المتجر الكتروني --}}

            @can('online_store_content_management')
            @if(isset($settings['ecommerce']) && $settings['ecommerce'] === 'active')
                <li class=" nav-item {{ request()->is("$getLocal/online_store/*") ? 'active open' : '' }}"><a href="index.html">
                        <i class="feather icon-shopping-cart">
                        </i><span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Online_store') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a href="{{ route('content_management.index') }}"><i class="feather icon-circle {{ request()->is("$getLocal/online_store/content-management/*") ? 'active' : '' }}"></i><span class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Content_management') }}</span></a>
                        </li>
                        <li><a href="{{ route('store_settings.index') }}"><i class="feather icon-circle {{ request()->is("$getLocal/online_store/store_settings/*") ? 'active' : '' }}"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                        </li>
                    </ul>
                </li>
                @endif
            @endcan

            {{-- التصنيع --}}
            @can('online_store_content_management')
             @if(isset($settings['manufacturing']) && $settings['manufacturing'] === 'active')

                <li class="nav-item {{ request()->is("$getLocal/Manufacturing/*") ? 'active open' : '' }}">
                    <a href="index.html">
                        <i class="feather icon-layers"></i>
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Manufacturing') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li>
                            <a href="{{ route('BOM.index') }}">
                                <i class="feather icon-circle {{ request()->is("$getLocal/Manufacturing/BOM/*") ? 'active' : '' }}"></i> <span class="menu-item" data-i18n="Analytics">{{ trans('main_trans.Bill_of_Materials') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manufacturing.orders.index') }}">
                                <i class="feather icon-circle {{ request()->is("$getLocal/Manufacturing/Orders/*") ? 'active' : '' }}"></i>
                                <span class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Manufacturing_Orders') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manufacturing.indirectcosts.index') }}">
                                <i class="feather icon-circle {{ request()->is("$getLocal/Manufacturing/indirectcosts/*") ? 'active' : '' }}"></i>
                                <span class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Indirect_Costs') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('manufacturing.workstations.index') }}">
                                <i class="feather icon-circle {{ request()->is("$getLocal/Manufacturing/Workstations/*") ? 'active' : '' }}"></i>
                                <span class="menu-item" data-i18n="Analytics">{{ trans('main_trans.Workstations') }}</span>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('Manufacturing.settings.index') }}">
                                <i class="feather icon-circle {{ request()->is("$getLocal/Manufacturing/Settings/*") ? 'active' : '' }}"></i>
                                <span class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
                @endif
            @endcan

            <!-- إدارة الحجوزات -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'rental_unit_view_booking_orders',
                        'rental_unit_manage_booking_orders',
                        'rental_unit_manage_rental_settings',
                    ])
                )
                @if(isset($settings['booking_management']) && $settings['booking_management'] === 'active')
                <li class="nav-item"><a href="#">
                        <i class="feather icon-bookmark"></i> <!-- أيقونة الحجز -->
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Reservations') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('rental_unit_view_booking_orders')
                            <li><a href="{{ route('Reservations.create') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Add_reservation') }}</span></a>
                            </li>
                        @endcan

                        @can('rental_unit_manage_booking_orders')
                            <li><a href="{{ route('Reservations.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Manage_reservations') }}</span></a>
                            </li>
                        @endcan

                        @can('rental_unit_manage_rental_settings')
                            <li><a href="{{ route('Reservations.Booking_Settings') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Booking_Settings') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @endif
            <!-- إدارة الأقساط -->
            @can('salaries_loans_manage')
            @if(isset($settings['installments_management']) && $settings['installments_management'] === 'active')
                <li class="nav-item"><a href="index.html">
                        <i class="feather icon-credit-card"></i> <!-- أيقونة الأقساط -->
                        <span class="menu-title"
                            data-i18n="Dashboard">{{ trans('main_trans.Installment_Management') }}</span>
                    </a>
                    <ul class="menu-content">

                        <li><a href="{{ route('installments.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Installment_agreements') }}</span></a>
                        </li>

                        @can('salaries_loans_manage')
                            <li><a href="{{ route('installments.agreement_installments') }}"><i
                                        class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Installments') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endcan

            <!-- إدارة المبيعات المستهدفة والعمولات -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'targeted_sales_commissions_manage_commission_rules',
                        'targeted_sales_commissions_view_all_sales_commissions',
                        'targeted_sales_commissions_manage_sales_periods',
                    ])
                )
                @if(isset($settings['target_sales_commissions']) && $settings['target_sales_commissions'] === 'active')
                <li class="nav-item"><a href="index.html">
                        <i class="feather icon-pie-chart"></i> <!-- أيقونة المبيعات والعمولات -->
                        <span class="menu-title"
                            data-i18n="Dashboard">{{ trans('main_trans.Targeted_sales_and_commissions') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('targeted_sales_commissions_manage_commission_rules')
                            <li><a href="{{ route('CommissionRules.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Commission_rules') }}</span></a>
                            </li>
                        @endcan

                        @can('targeted_sales_commissions_view_all_sales_commissions')
                            <li><a href="{{ route('SalesCommission.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Sales_commissions') }}</span></a>
                            </li>
                        @endcan

                        @can('targeted_sales_commissions_manage_sales_periods')
                            <li><a href="{{ route('SalesPeriods.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Sales_periods') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endif

            <!-- إدارة الوحدات والإيجارات -->
            @if (
                    Auth::user()->hasAnyPermission([
                        'rental_unit_view_booking_orders',
                        'rental_unit_manage_booking_orders',
                        'rental_unit_manage_rental_settings',
                    ])
                )
                 @if(isset($settings['rental_management']) && $settings['rental_management'] === 'active')
                <li class="nav-item"><a href="index.html">
                        <i class="feather icon-home"></i> <!-- أيقونة الوحدات والإيجارات -->
                        <span class="menu-title"
                            data-i18n="Dashboard">{{ trans('main_trans.Units_and_Rentals_Management') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('rental_unit_view_booking_orders')
                            <li><a href="{{ route('rental_management.units.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item" data-i18n="Analytics">{{ trans('main_trans.Units') }}</span></a>
                            </li>
                        @endcan

                        @can('rental_unit_manage_booking_orders')
                            <li><a href="{{ route('rental_management.orders.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Seizure_orders') }}</span></a>
                            </li>
                        @endcan

                        <li><a href="{{ route('rental_management.rental_price_rule.index') }}"><i
                                    class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Pricing_rules') }}</span></a>
                        </li>
                        <li><a href="{{ route('rental_management.seasonal-prices.index') }}"><i
                                    class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Seasonal_Prices') }}</span></a>
                        </li>

                        @can('rental_unit_manage_rental_settings')
                            <li><a href="{{ route('rental_management.settings.index') }}"><i
                                        class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endif

            <!-- أوامر التوريد -->

            @if (Auth::user()->hasAnyPermission(['supply_orders_view_all', 'supply_orders_add']))
            @if(isset($settings['work_orders']) && $settings['work_orders'] === 'active')
                <li class="nav-item"><a href="{{ route('SupplyOrders.index') }}">
                        <i class="feather icon-truck"></i> <!-- أيقونة أوامر التوريد -->
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Supply_orders') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('supply_orders_view_all')
                            <li><a href="{{ route('SupplyOrders.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Supply_orders') }}</span></a>
                            </li>
                        @endcan

                        @can('supply_orders_add')
                            <li><a href="{{ route('SupplyOrders.create') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Add_a_job_order') }}</span></a>
                            </li>
                        @endcan

                        <li><a href="{{ route('SupplySittings.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Supply_Orders_Settings') }}</span></a>
                        </li>
                    </ul>
                </li>
                @endif
            @endif



           @if (
                    auth()->user()->hasAnyPermission([
                        'work_cycle',

                    ])
                )
                  <!-- دورات العمل -->
            @if(isset($settings['workflow']) && $settings['workflow'] === 'active')
            <li class="nav-item"><a href="">
                    <i class="feather icon-refresh-ccw"></i> <!-- أيقونة دورات العمل -->
                    <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Business_cycles') }}</span>
                </a>
                <ul class="menu-content">
                    <li><a href=""><i class="feather icon-circle"></i><span class="menu-item"
                                data-i18n="Analytics">{{ trans('main_trans.Settings') }}</span></a></li>
                </ul>
            </li>
           @endif
            @endif
            <!-- العملاء -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'clients_view_all_clients',
                        'client_follow_up_add_notes_attachments_appointments_all',
                        'clients_edit_client_settings',
                    ])
                )
                 @if(isset($settings['customers']) && $settings['customers'] === 'active')
                <li class="nav-item">
                    <a href="#">
                        <i class="feather icon-user"></i> <!-- أيقونة العملاء -->
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Customers') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('clients_view_all_clients')
                            <li><a href="{{ route('clients.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Customer_management') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('clients_add_client')
                            <li><a href="{{ route('clients.create') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Add_a_new_customer') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('client_follow_up_add_notes_attachments_appointments_all')
                            <li><a href="{{ route('appointments.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Appointments') }}</span>
                                </a>
                            </li>
                        @endcan

                        <li><a href="{{ route('clients.contacts') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Contact_list') }}</span>
                            </a>
                        </li>

                        <li><a href="{{ route('clients.mang_client') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Customer_relationship_management') }}</span>
                            </a>
                        </li>

                        @can('clients_edit_client_settings')
                            <li><a href="{{ route('clients.setting') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Client_settings') }}</span>
                                </a>
                            </li>
                        @endcan

                    </ul>
                </li>
                @endif
            @endif

            <!-- نقاط الارصدة -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'points_credits_credit_recharge_manage',
                        'points_credits_credit_usage_manage',
                        'points_credits_packages_manage',
                        'points_credits_transactions_manage',
                    ])
                )
                @if(isset($settings['points_balances']) && $settings['points_balances'] === 'active')
                <li class="nav-item">
                    <a href="#">
                        <i class="feather icon-layers"></i> <!-- أيقونة نقاط الارصدة -->
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Points_and_credits') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('points_credits_credit_recharge_manage')
                            <li><a href="{{route('MangRechargeBalances.index')}}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Managing_balance_transfers') }}</span></a>
                            </li>
                        @endcan

                        @can('points_credits_credit_usage_manage')
                            <li><a href="{{route('ManagingBalanceConsumption.index')}}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Managing_consumption_balances') }}</span></a>
                            </li>
                        @endcan

                        @can('points_credits_packages_manage')
                            <li><a href="{{route('PackageManagement.index')}}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Package_management') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('points_credits_credit_settings_manage')
                            <li><a href="{{route('sitting.index')}}"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endif

            <!-- نقاط الولاء -->
            @if (
                    auth()->user()->hasAnyPermission([
                        'customer_loyalty_points_managing_customer_bases',
                        'customer_loyalty_points_redeem_loyalty_points',
                    ])
                )
                  @if(isset($settings['customer_loyalty_points']) && $settings['customer_loyalty_points'] === 'active')
                <li class="nav-item">
                    <a href="#">
                        <i class="feather icon-layers"></i> <!-- أيقونة نقاط الولاء -->
                        <span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Loyalty_points') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('customer_loyalty_points_managing_customer_bases')
                            <li><a href="{{ route('loyalty_points.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Customer_loyalty_rules') }}</span>
                                </a>
                            </li>
                        @endcan

                        @can('customer_loyalty_points_redeem_loyalty_points')
                            <li><a href="{{ route('sittingLoyalty.sitting') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endif









            {{-- العضويات --}}
            @if (auth()->user()->hasAnyPermission(['membership_management', 'membership_setting_management']))
            @if(isset($settings['membership']) && $settings['membership'] === 'active')
                <li class="nav-item">
                    <a href="index.html">
                        <i class="feather icon-users"></i>
                        <span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Memberships') }}
                        </span>
                    </a>
                    <ul class="menu-content">
                        @can('membership_management')
                            <li>
                                <a href="{{ route('Memberships.index') }}">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-item" data-i18n="Analytics">
                                        {{ trans('main_trans.Membership_management') }}
                                    </span>
                                </a>
                            </li>
                        @endcan


                        <li>
                            <a href="{{ route('Memberships.subscriptions') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Subscription_management') }}
                                </span>
                            </a>


                        {{-- <li><a href="{{ route('Memberships.subscriptions') }}"><i class="feather icon-circle"></i><span class="menu-item" --}}


                        {{-- <li><a href="{{route('Memberships.subscriptions.index')}}"><i class="feather icon-circle"></i><span class="menu-item"

                                    data-i18n="eCommerce">{{ trans('main_trans.Subscription_management') }}</span></a>

                        </li> --}}

                        {{-- <li>
                            <a href="{{ route('Memberships.subscriptions.index') }}">
                                <i class="feather icon-circle"></i>
                                <span class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Subscription_management') }}
                                </span>
                            </a>
                        </li> --}}

                        @can('membership_setting_management')
                            <li>
                                <a href="{{ route('SittingMemberships.index') }}">
                                    <i class="feather icon-circle"></i>
                                    <span class="menu-item" data-i18n="eCommerce">
                                        {{ trans('main_trans.Settings') }}
                                    </span>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
        @endif

            {{-- حضور العملاء --}}
            @can('customer_attendance_display')
            @if(isset($settings['customer_attendance']) && $settings['customer_attendance'] === 'active')
                <li class=" nav-item"><a href="index.html">
                        <i class="feather icon-user-check">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Customer_attendance') }}</span>

                    </a>
                    <ul class="menu-content">
                        @can('customer_attendance_display')
                            <li><a href="{{ route('customer_attendance.index') }}"><i class="feather icon-circle"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Customer_attendance_records') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
                @endif
            @endcan

            @if (
                Auth::user()->hasAnyPermission([
                    'management_of_insurance_agents',

                ])
            )
           {{-- وكلاء التامين --}}
           @if(isset($settings['insurance']) && $settings['insurance'] === 'active')
           <li class=" nav-item"><a href="index.html">
                   <i class="feather icon-users">
                   </i><span class="menu-title" data-i18n="Dashboard">
                       {{ trans('main_trans.Insurance_Agents') }}</span>

               </a>
               <ul class="menu-content">
                   <li><a href="{{ route('Insurance_Agents.index') }}"><i class="feather icon-circle"></i><span
                               class="menu-item"
                               data-i18n="Analytics">{{ trans('main_trans.Insurance_Agents_Management') }}</span></a>
                   </li>

                   <li><a href="{{ route('Insurance_Agents.create') }}"><i class="feather icon-circle"></i><span
                               class="menu-item"
                               data-i18n="eCommerce">{{ trans('main_trans.Add_Insurance_Company') }}</span></a>
                   </li>
               </ul>

           </li>
       @endif
        @endif

            {{-- ادارة المخازن --}}
            @if (
                    Auth::user()->hasAnyPermission([
                        'products_add_product',
                        'products_view_all_products',
                        'products_view_own_products',
                        'products_edit_delete_all_products',
                        'products_edit_delete_own_products',
                        'products_view_price_groups',
                        'products_add_edit_price_groups',
                        'products_delete_price_groups',
                    ])
                )
                @if(isset($settings['inventory_management']) && $settings['inventory_management'] === 'active')
                <li class=" nav-item {{ request()->is("$getLocal/stock/*") ? 'active open' : '' }}"><a href="">
                        <i class="feather icon-box">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Stock') }}</span>

                    </a>
                    <ul class="menu-content">
                        @can('products_view_all_products')
                            <li><a href="{{ route('products.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/stock/products/*") ? 'active' : '' }}"></i><span
                                        class="menu-item" data-i18n="Analytics">
                                        {{ trans('main_trans.products_management') }}</span></a>
                            </li>
                        @endcan

                        @can('inv_manage_inventory_permission_view')
                            <li><a href="{{ route('store_permits_management.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/stock/store_permits_management/*") ? 'active' : '' }}"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Store_permissions_management') }}</span></a>
                            </li>
                        @endcan


                        <li><a href="{{ route('products.traking') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.product_tracking') }}</span></a>
                        </li>

                        @can('products_view_price_groups')
                            <li><a href="{{ route('price_list.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/stock/price_list/*") ? 'active' : '' }}"></i><span
                                        class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.price_lists') }}</span></a>
                            </li>
                        @endcan

                        {{-- @can('products_view_price_groups') --}}
                        <li><a href="{{ route('storehouse.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/stock/storehouse/*") ? 'active' : '' }}"></i><span
                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.warehouses') }}</span></a>
                        </li>
                        {{-- @endcan --}}

                        <li><a href="{{ route('inventory_management.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/stock/inventory_management/*") ? 'active' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.inventory_Management') }}</span></a>
                        </li>

                        <li><a href="{{ route('inventory_settings.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/stock/inventory_settings/*") ? 'active' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.inventory_settings') }}</span></a>
                        </li>

                        <li><a href="{{ route('product_settings.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/stock/product_settings/*") ? 'active' : '' }}"></i><span
                                    class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.products_Settings') }}</span></a>
                        </li>

                    </ul>

                </li>
                 @endif
            @endif

            {{-- المشتريات --}}
            @if (auth()->user()->hasAnyPermission(['purchase_cycle_orders_manage_orders']))
            @if(isset($settings['purchase_cycle']) && $settings['purchase_cycle'] === 'active')
                <li class=" nav-item"><a href="index.html">
                        <i class="fa fa-shopping-cart">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Purchases') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('purchase_cycle_orders_manage_orders')

                            <li><a href="{{ route('OrdersPurchases.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Analytics"> {{ trans('main_trans.Purchase_Orders') }}</span></a>
                            </li>
                        @endcan

                        <li><a href="{{ route('Quotations.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Quotation_Requests') }}

                                </span></a>
                        </li>

                        <li><a href="{{ route('pricesPurchase.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce"> {{ trans('main_trans.Purchase_Quotations') }}</span></a>
                        </li>
                        <li><a href="{{ route('OrdersRequests.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">أوامر الشراء</span></a>
                        </li>
                        <li><a href="{{ route('invoicePurchases.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Purchase_Invoices') }}
                                </span></a>
                        </li>
                        <li><a href="{{ route('ReturnsInvoice.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Purchase_Returns') }}</span></a>
                        </li>
                        <li><a href="{{ route('CityNotices.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Creditor_notices') }}</span></a>
                        </li>
                        <li><a href="{{ route('SupplierManagement.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce"> {{ trans('main_trans.Supplier_Management') }}
                                    </span></a>
                        </li>
                        <li><a href="{{route('PaymentSupplier.indexPurchase')}}"><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Supplier_Payments') }}

                                </span></a>
                        </li>
                        <li><a href=""><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Purchase_Invoices_Settings') }}</span></a>
                        </li>
                        <li><a href=""><i
                                    class="feather icon-circle"></i><span class="menu-item" data-i18n="eCommerce">
                                    {{ trans('main_trans.Supplier_Settings') }}</span></a>

                        </li>
                    </ul>

                </li>
            @endif
            @endif
            {{-- تتبع الوقت --}}
            @if (auth()->user()->hasAnyPermission(['track_time_view_other_employees_work_hours']))
            @if(isset($settings['time_tracking']) && $settings['time_tracking'] === 'active')
                <li class=" nav-item"><a href="index.html">
                        <i class="feather icon-watch">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Time_Tracking') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a href="{{ route('TrackTime.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Time_Tracking') }}</span></a>
                        </li>
                        <li><a href="{{ route('TrackTime.create_invoice_time') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Create_Invoice') }}</span></a>
                        </li>

                        <li><a href="{{ route('SittingTrackTime.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Time_Tracking_Settings') }}</span></a>
                        </li>
                    </ul>

                </li>
                @endif
            @endif

            {{-- المالية --}}
            @if (
                    auth()->user()->hasAnyPermission([
                        'finance_view_all_expenses',
                        'finance_view_all_receipts',
                        'finance_view_own_cashboxes',
                        'finance_edit_default_cashbox',
                    ])
                )
                @if(isset($settings['finance']) && $settings['finance'] === 'active')
                            <li class=" nav-item {{ request()->is("$getLocal/finance/*") ? 'active open' : '' }}">
                                <a href="index.html">
                                    <i class="feather icon-dollar-sign">
                                    </i><span class="menu-title" data-i18n="Dashboard">{{ trans('main_trans.Financial') }}</span>
                                </a>

                                <ul class="menu-content">
                                    @can('finance_view_all_expenses')
                                        <li><a href="{{ route('expenses.index') }}"><i
                                                    class="feather icon-circle {{ request()->is("$getLocal/finance/expenses/*") ? 'active' : '' }}"></i><span
                                                    class="menu-item" data-i18n="Analytics">{{ trans('main_trans.Expenses') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('finance_view_all_receipts')
                                        <li><a href="{{ route('incomes.index') }}"><i
                                                    class="feather icon-circle {{ request()->is("$getLocal/finance/incomes/*") ? 'active' : '' }}"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Receipts') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('finance_view_own_cashboxes')
                                        <li><a href="{{ route('treasury.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">
                                                    {{ trans('main_trans.Cash_and_Bank_Accounts') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('finance_edit_default_cashbox')
                                        <li><a href="{{ route('finance_settings.index') }}"><i
                                                    class="feather icon-circle {{ request()->is("$getLocal/finance/finance_settings/*") ? 'active' : '' }}"></i><span
                                                    class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Financial_Settings') }}</span></a>
                                        </li>
                                    @endcan
                                </ul>

                            </li>
            @endif
            @endif
            {{-- الحسابات العامة --}}
            @if (
                    auth()->user()->hasAnyPermission([
                        'g_a_d_r_view_all_journal_entries',
                        'g_a_d_r_add_edit_delete_all_journal_entries',
                        'g_a_d_r_manage_journal_entries',
                        'g_a_d_r_view_cost_centers',
                        'g_a_d_r_add_new_assets',
                    ])
                )
                  @if(isset($settings['general_accounts_journal_entries']) && $settings['general_accounts_journal_entries'] === 'active')
                            <li class=" nav-item {{ request()->is("$getLocal/Accounts/*") ? 'active open' : '' }}"><a href="index.html">
                                    <i class="feather icon-pie-chart">
                                    </i><span class="menu-title" data-i18n="Dashboard">
                                        {{ trans('main_trans.General_Accounts') }}</span>
                                </a>

                                <ul class="menu-content">
                                    @can('g_a_d_r_view_all_journal_entries')
                                        <li><a href="{{ route('journal.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item"
                                                    data-i18n="Analytics">{{ trans('main_trans.Journal_Entries') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('g_a_d_r_add_edit_delete_all_journal_entries')
                                        <li><a href="{{ route('journal.create') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Add_Entry') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('g_a_d_r_manage_journal_entries')
                                        <li><a href="{{ route('accounts_chart.index') }}"><i class="feather icon-circle {{ request()->is("$getLocal/Accounts/accounts_chart/*") ? 'active' : '' }}"></i><span
                                                    class="menu-item" data-i18n="eCommerce">
                                                    {{ trans('main_trans.Chart_of_Accounts') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('g_a_d_r_view_cost_centers')
                                        <li><a href="{{ route('cost_centers.index') }}"><i class="feather icon-circle {{ request()->is("$getLocal/Accounts/cost_centers/*") ? 'active' : '' }}"></i><span
                                                    class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Cost_Centers') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('g_a_d_r_add_new_assets')
                                        <li><a href="{{ route('Assets.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                                    data-i18n="eCommerce">
                                                    {{ trans('main_trans.Assets') }}</span></a>
                                        </li>
                                    @endcan

                                    <li><a href="{{ route('accounts_settings.index') }}"><i class="feather icon-circle"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.General_Accounts_Settings') }}</span></a>
                                    </li>
                                </ul>

                            </li>
            @endif
            @endif
            {{-- الشيكات --}}
            @if (auth()->user()->hasAnyPermission(['check_cycle_view_checkbook', 'check_cycle_manage_received_checks']))
            @if(isset($settings['cheque_cycle']) && $settings['cheque_cycle'] === 'active')
                <li class=" nav-item {{ request()->is("$getLocal/cheques*") ? 'active open' : '' }}"><a href="index.html">
                        <i class="feather icon-dollar-sign">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Cheques_Cycle') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('check_cycle_view_checkbook')
                            <li><a href="{{ route('payable_cheques.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/cheques/payable_cheques*") ? 'active' : '' }}"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Paid_Cheques') }}</span></a>
                            </li>
                        @endcan

                        @can('check_cycle_manage_received_checks')
                            <li><a href="{{ route('received_cheques.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/cheques/received_cheques*") ? 'active' : '' }}"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Received_Cheques') }}</span></a>
                            </li>
                        @endcan
                    </ul>

                </li>
            @endif
            @endif
            {{-- الطلبات --}}
            @if (auth()->user()->hasAnyPermission(['orders_management', 'orders_setting_management']))
            @if(isset($settings['orders']) && $settings['orders'] === 'active')
                <li class=" nav-item"><a href="index.html">
                        <i class="feather icon-briefcase">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Orders') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('orders_management')
                            <li><a href="{{ route('orders.management.mangame') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Order_Management') }}</span></a>
                            </li>
                        @endcan

                        @can('orders_setting_management')
                            <li><a href="{{ route('orders.Settings.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endif
            @endif
            {{-- الموظفين --}}
            @if (auth()->user()->hasAnyPermission(['employees_view_profile', 'employees_roles_add']))
            @if(isset($settings['employees']) && $settings['employees'] === 'active')
                <li class=" nav-item {{ request()->is("$getLocal/hr/*") ? 'active open' : '' }}">
                    <a href="index.html">
                        <i class="fa fa-users"></i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Employees') }}</span>
                    </a>
                    <ul class="menu-content">
                        @can('employees_view_profile')
                            <li><a href="{{ route('employee.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/hr/employee/*") ? 'active' : '' }}"></i><span
                                        class="menu-item"
                                        data-i18n="Analytics">{{ trans('main_trans.Employee_Management') }}</span></a>
                            </li>
                        @endcan

                        @can('employees_roles_add')
                            <li><a href="{{ route('managing_employee_roles.index') }}"><i
                                        class="feather icon-circle {{ request()->is("$getLocal/hr/managing_employee_roles/*") ? 'active' : '' }}"></i><span
                                        class="menu-item"
                                        data-i18n="eCommerce">{{ trans('main_trans.Employee_Roles_Management') }}</span></a>
                            </li>
                        @endcan

                        <li><a href="{{ route('shift_management.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/hr/shift_management/*") ? 'active' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Shift_Management') }}</span></a>
                        </li>

                        <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                        </li>
                    </ul>

                </li>
            @endif
            @endif
            {{-- الهيكل التنظيمي --}}
            @can('hr_system_management')
            @if(isset($settings['organizational_structure']) && $settings['organizational_structure'] === 'active')
                <li class=" nav-item {{ request()->is("$getLocal/OrganizationalStructure/*") ? 'active open' : '' }}">
                    <a href="index.html">
                        <i class="feather icon-layers">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Organizational_Structure') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a href="{{ route('JobTitles.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/OrganizationalStructure/JobTitles/*") ? 'active open' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="Analytics">{{ trans('main_trans.Job_Titles_Management') }}</span></a>
                        </li>

                        <li><a href="{{ route('department.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/OrganizationalStructure/department/*") ? 'active open' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Departments_Management') }}</span></a>
                        </li>

                        <li><a href="{{ route('ManagingFunctionalLevels.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/OrganizationalStructure/ManagingFunctionalLevels/*") ? 'active open' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Job_Levels_Management') }}</span></a>
                        </li>
                        <li><a href="{{ route('ManagingJobTypes.index') }}"><i
                                    class="feather icon-circle {{ request()->is("$getLocal/OrganizationalStructure/ManagingJobTypes/*") ? 'active open' : '' }}"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Job_Types_Management') }}</span></a>
                        </li>
                    </ul>
                </li>

            @endcan
            @endif
            {{-- الحضور --}}
            @if(
                    auth()->user()->hasAnyPermission([
                        'staff_attendance_view_all',
                        'staff_attendance_edit_days',
                        'staff_attendance_view_other_books',
                        'staff_attendance_import',
                        'staff_attendance_settings_manage',
                    ])
                )
                 @if(isset($settings['employee_attendance']) && $settings['employee_attendance'] === 'active')
                            <li class="nav-item {{ (request()->is("$getLocal/presence/*") ? 'active open' : '') }}"><a
                                    href="index.html">

                                    <i class="feather icon-user-check">
                                    </i><span class="menu-title" data-i18n="Dashboard">
                                        {{ trans('main_trans.Attendance') }}</span>
                                </a>
                                <ul class="menu-content">
                                    @can('staff_attendance_view_all')
                                        <li><a href="{{ route('attendance_records.index') }}"><i
                                                    class="feather icon-circle {{ (request()->is("$getLocal/presence/attendance-records/*") ? 'active' : '') }}"></i><span
                                                    class="menu-item" data-i18n="Analytics">
                                                    {{ trans('main_trans.Attendance_Records') }}</span></a>

                                        </li>
                                    @endcan

                                    @can('staff_attendance_edit_days')
                                        <li><a href="{{ route('attendanceDays.index') }}"><i
                                                    class="feather icon-circle {{ (request()->is("$getLocal/presence/attendanceDays/*") ? 'active' : '') }}"></i><span
                                                    class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Attendance_Days') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('staff_attendance_view_other_books')
                                        <li><a href="{{ route('attendance_sheets.index') }}"><i
                                                    class="feather icon-circle {{ (request()->is("$getLocal/presence/attendance-sheets/*") ? 'active' : '') }}"></i><span
                                                    class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Attendance_Books') }}</span></a>
                                        </li>
                                    @endcan

                                    <li><a href="{{ route('leave_permissions.index') }}"><i
                                                class="feather icon-circle {{ (request()->is("$getLocal/presence/leave-permissions/*") ? 'active' : '') }}"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.Leave_Permissions') }}</span></a>
                                    </li>

                                    @can('staff_leave_requests_view_all')
                                        <li><a href="{{ route('attendance.leave_requests.index') }}"><i
                                                    class="feather icon-circle"></i><span class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Leave_Requests') }}</span></a>
                                        </li>
                                    @endcan

                                    <li><a href="{{ route('shift_management.index') }}"><i
                                                class="feather icon-circle {{ (request()->is("$getLocal/presence/shift_management/*") ? 'active' : '') }}"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.Shift_Management') }}</span></a>
                                    </li>

                                    <li><a href="{{ route('custom_shifts.index') }}"><i
                                                class="feather icon-circle {{ (request()->is("$getLocal/presence/custom-shifts/*") ? 'active' : '') }}"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.Custom_Shifts') }}</span></a>
                                    </li>

                                    @can('staff_attendance_import')
                                        <li><a href="{{ route('Attendance.attendance-sessions-record.index') }}"><i
                                                    class="feather icon-circle"></i><span class="menu-item"
                                                    data-i18n="eCommerce">{{ trans('main_trans.Attendance_Sessions_Log') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('staff_attendance_settings_manage')
                                        <li><a href="{{ route('attendance.settings.index') }}"><i
                                                    class="feather icon-circle {{ (request()->is("$getLocal/presence/settings/*") ? 'active' : '') }}"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
            @endif
            @endif
            {{-- المرتبات --}}
            @if (
                    auth()->user()->hasAnyPermission([
                        'salaries_contracts_view_all',
                        'salaries_payroll_view',
                        'salaries_payroll_approve',
                        'salaries_payroll_settings_manage',
                        'salaries_loans_manage',
                    ])
                )
                 @if(isset($settings['salaries']) && $settings['salaries'] === 'active')
                            <li class=" nav-item"><a href="index.html">
                                    <i class="feather icon-dollar-sign">
                                    </i><span class="menu-title" data-i18n="Dashboard">
                                        {{ trans('main_trans.Salaries') }}</span>
                                </a>
                                <ul class="menu-content">
                                    @can('salaries_contracts_view_all')
                                        <li><a href="{{ route('Contracts.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="Analytics">{{ trans('main_trans.Contracts') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('salaries_payroll_view')
                                        <li><a href="{{ route('PayrollProcess.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Payroll') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('salaries_payroll_approve')
                                        <li><a href="{{ route('salarySlip.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">
                                                    {{ trans('main_trans.Salary_Slips') }}</span></a>
                                        </li>
                                    @endcan

                                    @can('salaries_loans_manage')
                                        <li><a href="{{ route('ancestor.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Advances') }}</span></a>
                                        </li>
                                    @endcan

                                    <li><a href="{{ route('SalaryItems.index') }}"><i class="feather icon-circle"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.Salary_Items') }}</span></a>
                                    </li>

                                    <li><a href="{{route('SalaryTemplates.index')}}"><i class="feather icon-circle"></i><span
                                                class="menu-item"
                                                data-i18n="eCommerce">{{ trans('main_trans.Salary_Templates') }}</span></a>
                                    </li>

                                    @can('salaries_payroll_settings_manage')
                                        <li><a href="{{ route('SalarySittings.index') }}"><i class="feather icon-circle"></i><span
                                                    class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Settings') }}</span></a>
                                        </li>
                                    @endcan
                                </ul>
                            </li>
            @endif
            @endif




            @if (
                auth()->user()->hasAnyPermission([
                    'branches',
                ])
            )
            {{-- الفروع --}}

            @if(isset($settings['branches']) && $settings['branches'] === 'active')
            <li class=" nav-item"><a href="index.html">
                    <i class="feather  icon-briefcase">
                    </i><span class="menu-title" data-i18n="Dashboard">
                        {{ trans('main_trans.Branches') }}</span>

                </a>
                <ul class="menu-content">
                    <li><a href="{{ route('branches.index') }}"><i class="feather icon-circle"></i><span
                                class="menu-item"
                                data-i18n="Analytics">{{ trans('main_trans.Branch_Management') }}</span></a>
                    </li>

                    <li><a href="{{ route('branches.create') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.Add_Branch') }}</span></a>
                    </li>

                    <li><a href="{{ route('branches.settings') }}"><i class="feather icon-circle"></i><span
                                class="menu-item" data-i18n="eCommerce">{{ trans('main_trans.settings') }}</span></a>
                    </li>


                </ul>

            </li>
      @endif
        @endif


        @if (
            auth()->user()->hasAnyPermission([
                'templates',
            ])
        )
          {{-- القوالب --}}
          <li class=" nav-item"><a href="index.html">
            <i class="feather icon-dollar-sign">
            </i><span class="menu-title" data-i18n="Dashboard">
                {{ trans('main_trans.Templates') }}</span>

        </a>
        <ul class="menu-content">
            <li><a href="dashboard-analytics.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="Analytics">{{ trans('main_trans.Print_Templates') }}</span></a>
            </li>

            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.Ready_Invoice_Templates') }}</span></a>
            </li>
            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.Emails') }}</span></a>
            </li>
            @if(isset($settings['sms']) && $settings['sms'] === 'active')
            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.SMS_Models') }}</span></a>
            </li>
            @endif
            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.Terms_and_Conditions') }}</span></a>
            </li>
            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.File_Management_and_Documents') }}</span></a>
            </li>
            <li><a href="dashboard-ecommerce.html"><i class="feather icon-circle"></i><span class="menu-item"
                        data-i18n="eCommerce">{{ trans('main_trans.Auto_Send_Rules') }}</span></a>
            </li>


        </ul>

    </li>
    @endif



            {{-- الاعدادات --}}
            @can('settings_edit_general_settings')
                <li class=" nav-item"><a href="index.html">
                        <i class="feather icon-settings">
                        </i><span class="menu-title" data-i18n="Dashboard">
                            {{ trans('main_trans.Settings') }}</span>
                    </a>
                    <ul class="menu-content">
                        <li><a href="{{ route('AccountInfo.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item" data-i18n="Analytics">
                                    {{ trans('main_trans.Account_Information') }}</span></a>
                        </li>

                        <li><a href="{{ route('SittingAccount.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Account_Settings') }}</span></a>
                        </li>
                        <li><a href="{{ route('SMPT.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.SMTP_Settings') }}</span></a>
                        </li>

                        <li><a href="{{ route('PaymentMethods.index') }}"><i class="feather icon-circle"></i><span
                                    class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Payment_Methods') }}</span></a></li>

                        <li><a href="{{ route('Sms.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.SMS_Settings') }}</span></a></li>

                        <li><a href="{{ route('SequenceNumbering.index', 'section') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Sequential_Numbering_Settings') }}</span></a>
                        </li>
                        <li><a href="{{ route('TaxSitting.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Tax_Settings') }}</span></a>
                        </li>
                        <li><a href="{{ route('Application.index') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.Applications_Management') }}</span></a>
                        </li>

                        <li><a href="{{ route('AccountInfo.backgroundColor') }}"><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.System_Logo_and_Colors') }}</span></a>
                        </li>

                        <li><a href=""><i class="feather icon-circle"></i><span class="menu-item"
                                    data-i18n="eCommerce">{{ trans('main_trans.API') }}</span>
                            </a>
                        </li>
                    </ul>
                </li>
            @endcan

        </ul>
    </div>
</div>
