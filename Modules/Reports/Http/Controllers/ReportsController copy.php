<?php
namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function purchases()
    {
        return view('reports::purchases.orders');
    }

    public function supplier()
    {
        return view('reports::purchases.by_Supplier');
    }

    public function supplierDirectory()
    {
        return view('reports.purchases.supplier_directory');
    }

    public function employee()
    {
        return view('reports.purchases.by_employee');
    }

    public function balances()
    {
        return view('reports.purchases.balances');
    }

    public function aged()
    {
        return view('reports.purchases.aged');
    }

    public function payments()
    {
        return view('reports.purchases.Payments');
    }

    public function suppliersPurchases()
    {
        return view('reports.purchases.Suppliers_Purchases');
    }

    public function supplierStatement()
    {
        return view('reports.purchases.Supplier_Statement');
    }

    public function dailyPayments()
    {
        return view('reports.purchases.daily_payments');
    }

    public function weeklyPayments()
    {
        return view('reports.purchases.Weekly_Payments');
    }

    public function monthlyPayments()
    {
        return view('reports.purchases.Monthly_Payments');
    }

    public function annualPayments()
    {
        return view('reports.purchases.Annual_Payments');
    }

    public function byProduct()
    {
        return view('reports.purchases.by_product');
    }

    public function purchasesBySupplier()
    {
        return view('reports.purchases.Purchases_by_Supplier');
    }

    public function productsByEmployee()
    {
        return view('reports.purchases.Products_by_Employee');
    }
}
