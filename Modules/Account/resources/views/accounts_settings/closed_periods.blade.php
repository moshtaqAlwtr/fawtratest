@extends('master')

@section('title')
    الفترات المغلقة
@stop

@section('css')
  @section('css')
<style>
    .account-routing-container {
        max-width: 1100px;
        margin: auto;
        margin-top: 20px;
    }

    .routing-box {
        background-color: #ffffff; /* أبيض واضح */
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05); /* ظل خفيف */
    }

    .routing-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 15px;
        color: #333;
    }

    .form-label {
        font-weight: 600;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-control {
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        margin-top: 1.5rem;
    }

    .btn-save {
        background-color: #28a745;
        color: #fff;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: #fff;
    }
</style>
@endsection

@endsection

@section('content')
<div class="container mt-4">
    <div class="card shadow-sm">
        <!-- رأس الصفحة وزر الإضافة -->
        <div class="page-header">
            <h4>إعدادات الحسابات العامة > الفترات المغلقة</h4>
            <a href="#" class="btn btn-green">
                <i class="fa fa-plus"></i> إضافة
            </a>
        </div>

        <!-- قسم الفلاتر -->
        <div class="filter-section">
            <form action="" method="GET">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label for="from_date" class="form-label">من</label>
                        <input type="date" name="from_date" id="from_date" class="form-control" value="">
                    </div>

                    <div class="col-md-3">
                        <label for="to_date" class="form-label">إلى</label>
                        <input type="date" name="to_date" id="to_date" class="form-control" value="">
                    </div>

                    <div class="col-md-3">
                        <label for="status" class="form-label">نشط</label>
                        <select name="status" id="status" class="form-control">
                            <option value="">[أي]</option>
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex gap-2 mt-2">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fa fa-search"></i> بحث
                        </button>
                        <a href="#" class="btn btn-gray">
                            إلغاء الفلاتر
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- النتائج -->
        <div class="results-box">
            <h6 class="mb-3">النتائج</h6>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>من</th>
                        <th>إلى</th>
                        <th>الحالة</th>
                        <th>الوصف</th>
                        <th>خيارات</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- بيانات وهمية --}}
                    <tr>
                        <td>01/01/2024</td>
                        <td>31/03/2024</td>
                        <td><span class="badge badge-success">نشط</span></td>
                        <td>الربع الأول من السنة المالية</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning">تعديل</a>
                            <form action="#" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                    <tr>
                        <td>01/04/2024</td>
                        <td>30/06/2024</td>
                        <td><span class="badge badge-danger">غير نشط</span></td>
                        <td>الربع الثاني من السنة المالية</td>
                        <td>
                            <a href="#" class="btn btn-sm btn-warning">تعديل</a>
                            <form action="#" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-danger" onclick="return confirm('هل أنت متأكد؟')">حذف</button>
                            </form>
                        </td>
                    </tr>
                </tbody>
            </table>

            {{-- لو ما في بيانات --}}
            {{-- <div class="no-data">لا توجد نتائج.</div> --}}
        </div>
    </div>
</div>
@endsection
