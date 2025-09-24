@extends('userguide::layouts.app')

@section('title', 'لوحة التحكم - فوتره')
@section('page_title', 'لوحة التحكم')
@section('page_subtitle', 'مرحبا بك')
@section('header_class', 'Dashboard')

@php
    $showPageHeader = true;
@endphp

@section('content')
    <style>
        .rtl-text {
            direction: rtl;
            text-align: right;
        }
    </style>

    <div class="pages-view" dir="rtl">
        <div class="container">
            @switch($page)
                @case('success_partners')
                    @include('userguide::layouts.success_partners')
                @break

                @case('system_functions')
                    @include('userguide::layouts.system_functions')
                @break

                @case('contact_us')
                    @include('userguide::layouts.contact_us')
                @break

                @case('prices')
                    @include('userguide::layouts.pricing')
                @break

                {{-- المحلات التجارية ونقطة البيع --}}
                @case('commercial_accounting')
                    @include('userguide::layouts.business_areas.shops_pos.commercial_accounting')
                @break

                @case('hardware_paint')
                    @include('userguide::layouts.business_areas.shops_pos.hardware_paint')
                @break

                @case('perfume')
                    @include('userguide::layouts.business_areas.shops_pos.perfume')
                @break

                @case('mobile')
                    @include('userguide::layouts.business_areas.shops_pos.mobile_store_management')
                @break

                @case('commercial')
                    @include('userguide::layouts.business_areas.shops_pos.enterprise_supply_management')
                @break

                @case('ceramic')
                    @include('userguide::layouts.business_areas.shops_pos.ceramic_warehouse_management')
                @break

                @case('bookstore')
                    @include('userguide::layouts.business_areas.shops_pos.library_management')
                @break

                @case('computer')
                    @include('userguide::layouts.business_areas.shops_pos.computer_store_management')
                @break

                @case('auto_parts')
                    @include('userguide::layouts.business_areas.shops_pos.auto_parts_store_management')
                @break

                @case('jewelry')
                    @include('userguide::layouts.business_areas.shops_pos.gold_jewelry_management')
                @break

                @case('optics')
                    @include('userguide::layouts.business_areas.shops_pos.optical_glasses_management')
                @break

                {{-- الحرف والخدمات المهنية --}}
                @case('landscape')
                    @include('userguide::layouts.business_areas.crafts_professional_services.landscape_management')
                @break

                @case('hvac')
                    @include('userguide::layouts.business_areas.crafts_professional_services.air_conditioning_installation_maintenance_management')
                @break

                @case('furniture')
                    @include('userguide::layouts.business_areas.crafts_professional_services.furniture_workshop_management')
                @break

                @case('factory')
                    @include('userguide::layouts.business_areas.crafts_professional_services.factory_management')
                @break

                @case('coworking')
                    @include('userguide::layouts.business_areas.crafts_professional_services.managing_shared_workspaces')
                @break

                @case('gaming')
                    @include('userguide::layouts.business_areas.crafts_professional_services.games_playstation_management')
                @break

                @case('laundry')
                    @include('userguide::layouts.business_areas.crafts_professional_services.laundry_program')
                @break

                @case('maintenance')
                    @include('userguide::layouts.business_areas.crafts_professional_services.management_maintenance_technical_support')
                @break

                @case('cleaning')
                    @include('userguide::layouts.business_areas.crafts_professional_services.management_cleaning_companies')
                @break

                @case('equipment_rental')
                    @include('userguide::layouts.business_areas.crafts_professional_services.equipment_rental_program')
                @break

                {{-- خدمات الأعمال --}}
                @case('marketing')
                    @include('userguide::layouts.business_areas.business_services.marketing_management_companies')
                @break

                @case('consulting')
                    @include('userguide::layouts.business_areas.business_services.office_management_consulting_firms')
                @break

                @case('hosting_web')
                    @include('userguide::layouts.business_areas.business_services.hosting_website_development_company_management')
                @break

                @case('accounting')
                    @include('userguide::layouts.business_areas.business_services.management_accounting_offices')
                @break

                @case('translation')
                    @include('userguide::layouts.business_areas.business_services.localization_translation_management')
                @break

                @case('real_estate')
                    @include('userguide::layouts.business_areas.business_services.business_supply_chain_management')
                @break

                @case('event_planning')
                    @include('userguide::layouts.business_areas.business_services.management_recruitment_offices')
                @break

                @case('legal')
                    @include('userguide::layouts.business_areas.business_services.law_firm_management')
                @break

                @case('photography')
                    @include('userguide::layouts.business_areas.business_services.pharmaceutical_companies_management_medical_supplies')
                @break

                @case('insurance')
                    @include('userguide::layouts.business_areas.business_services.printing_press_management')
                @break

                {{-- الرعاية الطبية --}}
                @case('clinic')
                    @include('userguide::layouts.business_areas.medical_care.clinic_medical_center_management')
                @break

                @case('lab')
                    @include('userguide::layouts.business_areas.medical_care.medical_laboratory_management')
                @break

                @case('dental')
                    @include('userguide::layouts.business_areas.medical_care.dental_clinic_management')
                @break

                @case('veterinary')
                    @include('userguide::layouts.business_areas.medical_care.pediatric_clinic_management')
                @break

                @case('physiotherapy')
                    @include('userguide::layouts.business_areas.medical_care.obstetrics_gynecology_clinic_management')
                @break

                @case('pharmacy')
                    @include('userguide::layouts.business_areas.medical_care.pharmacy_management')
                @break

                @case('radiology')
                    @include('userguide::layouts.business_areas.medical_care.pharmaceutical_company_management')
                @break

                {{-- logistics ervices --}}
                @case('shipping')
                    @include('userguide::layouts.business_areas.logistics_ervices.shipping_logistics_companies')
                @break

                @case('delivery')
                    @include('userguide::layouts.business_areas.logistics_ervices.management_car_rental_companies')
                @break

                {{-- tourism_transportation_hospitality --}}
                @case('hotel')
                    @include('userguide::layouts.business_areas.tourism_transportation_hospitality.hotel_management')
                @break

                @case('taxi')
                    @include('userguide::layouts.business_areas.tourism_transportation_hospitality.transportation_travel_management')
                @break

                @case('travel')
                    @include('userguide::layouts.business_areas.tourism_transportation_hospitality.travel_tourism_companies_management')
                @break

                {{-- body_care_fitness --}}
                @case('gym')
                    @include('userguide::layouts.business_areas.body_care_fitness.sports_clubs_gyms_fitness_centers')
                @break

                @case('salon')
                    @include('userguide::layouts.business_areas.body_care_fitness.management_beauty_centers_womens_salons')
                @break

                {{-- education --}}
                @case('school')
                    @include('userguide::layouts.business_areas.education.schools_nurseries')
                @break

                @case('training')
                    @include('userguide::layouts.business_areas.education.management_educational_centers')
                @break

                {{-- cars --}}
                @case('repair')
                    @include('userguide::layouts.business_areas.cars.car_maintenance_centers')
                @break

                @case('dealership')
                    @include('userguide::layouts.business_areas.cars.car_showrooms')
                @break

                {{-- projects_contracting_real_estate_investment --}}
                @case('general_construction')
                    @include('userguide::layouts.business_areas.projects_contracting_real_estate_investment.real_estate_investment_companies')
                @break

                @case('project_management')
                    @include('userguide::layouts.business_areas.projects_contracting_real_estate_investment.management_contracting_companies')
                @break

                {{-- programs --}}
                @case('sales')
                    @include('userguide::layouts.programs.sales.sales')
                @break

                @case('invoices_quotes')
                    @include('userguide::layouts.programs.sales.invoices_quotes')
                @break

                @case('point_of_sale')
                    @include('userguide::layouts.programs.sales.point_of_sale')
                @break

                @case('offers')
                    @include('userguide::layouts.programs.sales.offers')
                @break

                @case('installments')
                    @include('userguide::layouts.programs.sales.installments')
                @break

                @case('targeted_sales_commissions')
                    @include('userguide::layouts.programs.sales.targeted_sales_commissions')
                @break

                @case('sales_insurance')
                    @include('userguide::layouts.programs.sales.insurance')
                @break

                @case('saudi_electronic_invoice')
                    @include('userguide::layouts.programs.sales.saudi_electronic_invoice')
                @break

                @case('egyptian_electronic_invoice')
                    @include('userguide::layouts.programs.sales.egyptian_electronic_invoice')
                @break

                @case('jordanian_electronic_invoice')
                    @include('userguide::layouts.programs.sales.jordanian_electronic_invoice')
                @break

                {{-- قسم العملاء --}}
                @case('customer_management')
                    @include('userguide::layouts.programs.customers.customer_management')
                @break

                @case('customer_follow_up')
                    @include('userguide::layouts.programs.customers.customer_management')
                @break

                @case('customer_loyalty_points')
                    @include('userguide::layouts.programs.customers.customer_loyalty_points')
                @break

                @case('points_balances')
                    @include('userguide::layouts.programs.customers.points_balances')
                @break

                @case('subscriptions_memberships')
                    @include('userguide::layouts.programs.customers.subscriptions_memberships')
                @break

                {{-- قسم الحسابات --}}
                @case('accounts')
                    @include('userguide::layouts.programs.accounts.accounts')
                @break

                @case('expenses')
                    @include('userguide::layouts.programs.accounts.expenses')
                @break

                @case('accounting_program')
                    @include('userguide::layouts.programs.accounts.accounting_program')
                @break

                @case('chart_of_accounts')
                    @include('userguide::layouts.programs.accounts.chart_of_accounts')
                @break

                @case('asset_management')
                    @include('userguide::layouts.programs.accounts.asset_management')
                @break

                @case('cost_centers')
                    @include('userguide::layouts.programs.accounts.cost_centers')
                @break

                @case('check_cycle')
                    @include('userguide::layouts.programs.accounts.check_cycle')
                @break

                {{-- قسم المخزون --}}
                @case('inventory_warehouses')
                    @include('userguide::layouts.programs.inventory.inventory_warehouses')
                @break

                @case('product_management')
                    @include('userguide::layouts.programs.inventory.product_management')
                @break

                @case('purchases')
                    @include('userguide::layouts.programs.inventory.purchases')
                @break

                @case('purchase_cycle')
                    @include('userguide::layouts.programs.inventory.purchase_cycle')
                @break

                @case('supplier_management')
                    @include('userguide::layouts.programs.inventory.supplier_management')
                @break

                @case('inventory_permissions')
                    @include('userguide::layouts.programs.inventory.inventory_permissions')
                @break

                @case('inventory_management')
                    @include('userguide::layouts.programs.inventory.inventory_management')
                @break

                @case('manufacturing_management')
                    @include('userguide::layouts.programs.inventory.manufacturing_management')
                @break

                @case('production_order_management')
                    @include('userguide::layouts.programs.inventory.production_order_management')
                @break

                {{-- قسم شؤون الموظفين --}}
                @case('employee_affairs')
                    @include('userguide::layouts.programs.hr.employee_affairs')
                @break

                @case('organizational_structure_management')
                    @include('userguide::layouts.programs.hr.organizational_structure_management')
                @break

                @case('attendance_departure')
                    @include('userguide::layouts.programs.hr.attendance_departure')
                @break

                @case('contract_management')
                    @include('userguide::layouts.programs.hr.contract_management')
                @break

                @case('salary_management')
                    @include('userguide::layouts.programs.hr.salary_management')
                @break

                @case('request_management')
                    @include('userguide::layouts.programs.hr.request_management')
                @break

                {{-- قسم التشغيل --}}
                @case('operations')
                    @include('userguide::layouts.programs.operations.operations')
                @break

                @case('work_cycle')
                    @include('userguide::layouts.programs.operations.work_cycle')
                @break

                @case('work_orders')
                    @include('userguide::layouts.programs.operations.work_orders')
                @break

                @case('reservations')
                    @include('userguide::layouts.programs.operations.reservations')
                @break

                @case('rental_unit_management')
                    @include('userguide::layouts.programs.operations.rental_unit_management')
                @break

                @case('time_tracking')
                    @include('userguide::layouts.programs.operations.time_tracking')
                @break

                {{-- قسم تطبيقات الجوال --}}
                @case('mobile_apps')
                    @include('userguide::layouts.programs.mobile.mobile_apps')
                @break

                @case('mobile_business_management')
                    @include('userguide::layouts.programs.mobile.mobile_business_management')
                @break

                @case('mobile_pos')
                    @include('userguide::layouts.programs.mobile.mobile_pos')
                @break

                @case('desktop_pos')
                    @include('userguide::layouts.programs.mobile.desktop_pos')
                @break

                @case('mobile_attendance')
                    @include('userguide::layouts.programs.mobile.mobile_attendance')
                @break

                @case('mobile_expense_tracking')
                    @include('userguide::layouts.programs.mobile.mobile_expense_tracking')
                @break

                @case('mobile_invoice_reader')
                    @include('userguide::layouts.programs.mobile.mobile_invoice_reader')
                @break

                {{-- القائمة الجانبية الإضافية --}}
                @case('features')
                    @include('userguide::layouts.programs.additional.all_features')
                @break

                @case('userguides')
                    @include('userguide::layouts.programs.additional.user_guide')
                @break

                @case('fawtura_agents')
                    @include('userguide::layouts.programs.additional.fawtura_agents')
                @break

                @case('account_setup_services')
                    @include('userguide::layouts.programs.additional.account_setup_services')
                @break

                @case('accounting_services')
                    @include('userguide::layouts.programs.additional.accounting_services')
                @break

                @case('our_clients')
                    @include('userguide::layouts.programs.additional.our_clients')
                @break

                @case('about_fawtura')
                    @include('userguide::layouts.programs.additional.about_fawtura')
                @break

                @case('blog')
                    @include('userguide::layouts.programs.additional.blog')
                @break

                @case('contact_us')
                    @include('userguide::layouts.contact_us')
                @break

                @case('learning_center')
                    @include('userguide::layouts.programs.additional.learning_center')
                @break

                @case('latest_updates')
                    @include('userguide::layouts.programs.additional.latest_updates')
                @break

                @default
                    @include('userguide::layouts.business_areas.default', [
                        'pageTitle' => ucfirst(str_replace('_', ' ', $page)),
                        'pageDescription' => 'صفحة مخصصة لمجال ' . $page,
                    ])
            @endswitch
        </div>
    </div>
@endsection
