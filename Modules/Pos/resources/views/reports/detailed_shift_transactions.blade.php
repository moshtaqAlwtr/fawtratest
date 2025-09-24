@extends('master')

@section('title')
حركة الورديات تفصيلي
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">حركة الورديات تفصيلي</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">عرض</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card mt-4">
    <div class="card-body">
        <form>
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="sessionNumber" class="form-label">رقم الجلسة</label>
                    <select class="form-control" id="sessionNumber">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category" class="form-label">التصنيف</label>
                    <select class="form-control" id="category">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="posShift" class="form-label">Pos Shift</label>
                    <select class="form-control" id="posShift">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="posShiftDevice" class="form-label">Pos Shift Device</label>
                    <select class="form-control" id="posShiftDevice">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="orderSource" class="form-label">مصدر الطلب</label>
                    <select class="form-control" id="orderSource">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="store" class="form-label">المخزن</label>
                    <select class="form-control" id="store">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="dateFrom" class="form-label">التاريخ من</label>
                    <input type="date" class="form-control" id="dateFrom" value="2024-12-10">
                </div>
                <div class="col-md-3">
                    <label for="dateTo" class="form-label">التاريخ إلى</label>
                    <input type="date" class="form-control" id="dateTo" value="2025-01-10">
                </div>
                <div class="col-md-3">
                    <label for="currency" class="form-label">العملة</label>
                    <select class="form-control" id="currency">
                        <option>الجميع إلى (SAR)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="groupBy" class="form-label">تجميع حسب</label>
                    <select class="form-control" id="groupBy">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sortBy" class="form-label">ترتيب حسب</label>
                    <select class="form-control" id="sortBy">
                        <option>التصنيف</option>
                    </select>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-left">
                    <button type="submit" class="btn btn-primary">عرض التقرير</button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="card mt-4">
    <div class="text-center mb-4">
        <h4>إجمالي مبيعات الورديات تفصيلي</h4>
        <p>الوقت: 21:57 10/01/2025</p>
        <h5>مؤسسة أعمال خاصة للتجارة</h5>
        <p>الرياض، الرياض</p>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>الكود</th>
                    <th>وردية</th>
                    <th>وقت الفتح</th>
                    <th>وقت الاغلاق</th>
                    <th>موظف الخزنة</th>
                    <th>مؤكدة بواسطة</th>
                    <th>فرع</th>
                    <th>المبيعات</th>
                    <th>المردود</th>
                    <th>الصافي</th>
                    <th>صافي نقدي</th>
                    <th>صافي غير نقدي</th>
                    <th>أجمالي أستلام نقدي</th>
                    <th>الأجل </th>
                    <th>أجمالي استلام  نقدي</th>
                    <th>أجمالي صرف نقدي</th>
                    <th>أجمالي نظري</th>
                    <th>أجمالي المستلم  نقدي</th>
                    <th>أجمالي المستلم غير نقدي</th>
                 
                    <th>أجمالي المستلم</th>
                    <th>الفرق</th>
                  
                  
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>3961</td>
                    <td> Main POS Shift </td>
                    <td>21/01/2025 </td>
                    <td>21/01/2025</td>
                    <td>OWNER</td>
                    <td>OWNER</td>
                    <td>Main Branch</td>
                    <td>87.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>-87.00</td>
                 
                </tr>
                <tr>
                    <td></td>
                    <td>  </td>
                    <td> </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>87.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>-87.00</td>
                </tr>
                <tr class="table-light fw-bold">
                    <td colspan="3">المجموع</td>
                
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>87.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>-87.00</td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th colspan="7" class="text-end">الأجماليات</th>
                  
                    <td>87.00</td>
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>78.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                  
                    <td>0.00</td>
                    <td>78.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>0.00</td>
                    <td>-87.00</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
</div>

@endsection