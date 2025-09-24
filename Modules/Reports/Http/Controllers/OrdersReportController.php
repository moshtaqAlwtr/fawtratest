<?php

namespace Modules\Reports\Http\Controllers;
use App\Http\Controllers\Controller;

class OrdersReportController extends Controller
{
    // صفحة الفهرس (Index) الرئيسية
    public function index()
    {
        return view('reports::orders.index');
    }

    // عرض صفحة أمر التوريد
    public function supplyOrder()
    {
        return view('reports::orders.supply-order');
    }

    // عرض صفحة أوامر التوريد بالوسوم
    public function taggedSupplyOrders()
    {
        return view('reports::orders.tagged-supply-orders');
    }

    // عرض صفحة مواعيد أوامر التوريد
    public function supplyOrdersSchedule()
    {
        return view('reports::orders.supply-orders-schedule');
    }

    // عرض صفحة أرباح أوامر التوريد - الملخص
    public function supplyOrdersProfitSummary()
    {
        return view('reports::orders.supply-orders-profit-summary');
    }

    // عرض صفحة أرباح أوامر التوريد - التفاصيل
    public function supplyOrdersProfitDetails()
    {
        return view('reports::orders.supply-orders-profit-details');
    }
}
