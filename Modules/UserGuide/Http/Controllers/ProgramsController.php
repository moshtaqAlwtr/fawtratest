<?php

namespace Modules\UserGuide\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    public function features() {
        return view('userguide::home_pages', ['page' => 'features']);
    }

    public function userguides() {
        return view('userguide::home_pages', ['page' => 'userguides']);
    }

    // قسم المبيعات
    public function sales() {
        return view('userguide::home_pages', ['page' => 'sales']);
    }

    public function invoicesQuotes() {
        return view('userguide::home_pages', ['page' => 'invoices_quotes']);
    }

    public function pointOfSale() {
        return view('userguide::home_pages', ['page' => 'point_of_sale']);
    }

    public function offers() {
        return view('userguide::home_pages', ['page' => 'offers']);
    }

    public function installments() {
        return view('userguide::home_pages', ['page' => 'installments']);
    }

    public function targetedSalesCommissions() {
        return view('userguide::home_pages', ['page' => 'targeted_sales_commissions']);
    }

    public function insurance() {
        return view('userguide::home_pages', ['page' => 'sales_insurance']);
    }

    public function saudiElectronicInvoice() {
        return view('userguide::home_pages', ['page' => 'saudi_electronic_invoice']);
    }

    public function egyptianElectronicInvoice() {
        return view('userguide::home_pages', ['page' => 'egyptian_electronic_invoice']);
    }

    public function jordanianElectronicInvoice() {
        return view('userguide::home_pages', ['page' => 'jordanian_electronic_invoice']);
    }

    // قسم العملاء
    public function customerManagement() {
        return view('userguide::home_pages', ['page' => 'customer_management']);
    }

    public function customerFollowUp() {
        return view('userguide::home_pages', ['page' => 'customer_follow_up']);
    }

    public function customerLoyaltyPoints() {
        return view('userguide::home_pages', ['page' => 'customer_loyalty_points']);
    }

    public function pointsBalances() {
        return view('userguide::home_pages', ['page' => 'points_balances']);
    }

    public function subscriptionsMemberships() {
        return view('userguide::home_pages', ['page' => 'subscriptions_memberships']);
    }

    // قسم الحسابات
    public function accounts() {
        return view('userguide::home_pages', ['page' => 'accounts']);
    }

    public function expenses() {
        return view('userguide::home_pages', ['page' => 'expenses']);
    }

    public function accountingProgram() {
        return view('userguide::home_pages', ['page' => 'accounting_program']);
    }

    public function chartOfAccounts() {
        return view('userguide::home_pages', ['page' => 'chart_of_accounts']);
    }

    public function assetManagement() {
        return view('userguide::home_pages', ['page' => 'asset_management']);
    }

    public function costCenters() {
        return view('userguide::home_pages', ['page' => 'cost_centers']);
    }

    public function checkCycle() {
        return view('userguide::home_pages', ['page' => 'check_cycle']);
    }

    // قسم المخزون
    public function inventoryWarehouses() {
        return view('userguide::home_pages', ['page' => 'inventory_warehouses']);
    }

    public function productManagement() {
        return view('userguide::home_pages', ['page' => 'product_management']);
    }

    public function purchases() {
        return view('userguide::home_pages', ['page' => 'purchases']);
    }

    public function purchaseCycle() {
        return view('userguide::home_pages', ['page' => 'purchase_cycle']);
    }

    public function supplierManagement() {
        return view('userguide::home_pages', ['page' => 'supplier_management']);
    }

    public function inventoryPermissions() {
        return view('userguide::home_pages', ['page' => 'inventory_permissions']);
    }

    public function inventoryManagement() {
        return view('userguide::home_pages', ['page' => 'inventory_management']);
    }

    public function manufacturingManagement() {
        return view('userguide::home_pages', ['page' => 'manufacturing_management']);
    }

    public function productionOrderManagement() {
        return view('userguide::home_pages', ['page' => 'production_order_management']);
    }

    // قسم شؤون الموظفين
    public function employeeAffairs() {
        return view('userguide::home_pages', ['page' => 'employee_affairs']);
    }

    public function organizationalStructureManagement() {
        return view('userguide::home_pages', ['page' => 'organizational_structure_management']);
    }

    public function attendanceDeparture() {
        return view('userguide::home_pages', ['page' => 'attendance_departure']);
    }

    public function contractManagement() {
        return view('userguide::home_pages', ['page' => 'contract_management']);
    }

    public function salaryManagement() {
        return view('userguide::home_pages', ['page' => 'salary_management']);
    }

    public function requestManagement() {
        return view('userguide::home_pages', ['page' => 'request_management']);
    }

    // قسم التشغيل
    public function operations() {
        return view('userguide::home_pages', ['page' => 'operations']);
    }

    public function workCycle() {
        return view('userguide::home_pages', ['page' => 'work_cycle']);
    }

    public function workOrders() {
        return view('userguide::home_pages', ['page' => 'work_orders']);
    }

    public function reservations() {
        return view('userguide::home_pages', ['page' => 'reservations']);
    }

    public function rentalUnitManagement() {
        return view('userguide::home_pages', ['page' => 'rental_unit_management']);
    }

    public function timeTracking() {
        return view('userguide::home_pages', ['page' => 'time_tracking']);
    }

    // قسم تطبيقات الجوال
    public function mobileApps() {
        return view('userguide::home_pages', ['page' => 'mobile_apps']);
    }

    public function mobileBusinessManagement() {
        return view('userguide::home_pages', ['page' => 'mobile_business_management']);
    }

    public function mobilePOS() {
        return view('userguide::home_pages', ['page' => 'mobile_pos']);
    }

    public function desktopPOS() {
        return view('userguide::home_pages', ['page' => 'desktop_pos']);
    }

    public function mobileAttendance() {
        return view('userguide::home_pages', ['page' => 'mobile_attendance']);
    }

    public function mobileExpenseTracking() {
        return view('userguide::home_pages', ['page' => 'mobile_expense_tracking']);
    }

    public function mobileInvoiceReader() {
        return view('userguide::home_pages', ['page' => 'mobile_invoice_reader']);
    }

    // دوال القائمة الجانبية الإضافية
    public function fawturaAgents() {
        return view('userguide::home_pages', ['page' => 'fawtura_agents']);
    }

    public function accountSetupServices() {
        return view('userguide::home_pages', ['page' => 'account_setup_services']);
    }

    public function accountingServices() {
        return view('userguide::home_pages', ['page' => 'accounting_services']);
    }

    public function ourClients() {
        return view('userguide::home_pages', ['page' => 'our_clients']);
    }

    public function aboutFawtura() {
        return view('userguide::home_pages', ['page' => 'about_fawtura']);
    }

    public function blog() {
        return view('userguide::home_pages', ['page' => 'blog']);
    }

    public function contactUs() {
        return view('userguide::home_pages', ['page' => 'contact_us']);
    }

    public function learningCenter() {
        return view('userguide::home_pages', ['page' => 'learning_center']);
    }

    public function latestUpdates() {
        return view('userguide::home_pages', ['page' => 'latest_updates']);
    }
}
