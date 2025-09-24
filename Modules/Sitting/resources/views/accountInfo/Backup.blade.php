@extends('master')

@section('title')
    تنزيل نسخة احتياطية
@stop

@section('content')
<div class="card">
    <div class="container">
        <h1>تنزيل نسخة احتياطية</h1>
        <form action="{{ route('AccountInfo.download') }}" method="POST">
            @csrf
            <div class="form-group">
                <label>اختر البيانات:</label>
                <div class="d-flex flex-wrap">
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="invoices" id="invoices">
                        <label class="form-check-label" for="invoices">الفواتير</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="clients" id="clients">
                        <label class="form-check-label" for="clients">العملاء</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="purchase_orders" id="purchase_orders">
                        <label class="form-check-label" for="purchase_orders">أوامر الشراء</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="products" id="products">
                        <label class="form-check-label" for="products">المنتجات</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="expenses" id="expenses">
                        <label class="form-check-label" for="expenses">المصروفات</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="revenues" id="revenues">
                        <label class="form-check-label" for="revenues">الإيرادات</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="employees" id="employees">
                        <label class="form-check-label" for="employees">الموظفين</label>
                    </div>
                    <div class="form-check mr-3">
                        <input class="form-check-input" type="checkbox" name="data_types[]" value="suppliers" id="suppliers">
                        <label class="form-check-label" for="suppliers">الموردين</label>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label for="file_format">اختر تنسيق الملف:</label>
                <select name="file_format" id="file_format" class="form-control" required>
                    <option value="xml">XML</option>
                    <option value="json">JSON</option>
                    <option value="csv">CSV</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">تنزيل النسخة الاحتياطية</button>
        </form>
    </div>
</div>
@endsection