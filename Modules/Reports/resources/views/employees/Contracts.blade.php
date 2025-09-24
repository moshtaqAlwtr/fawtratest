@extends('master')

@section('title')
تقرير عقود الموظفين  
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير عقود الموظفين  </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                        </li>
                        <li class="breadcrumb-item active">عرض
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>




<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">نموذج البحث</h5>
    </div>
    <div class="card-body">
        <form method="GET">
            <div class="row g-4">
                <!-- تاريخ البدء من -->
                <div class="col-3">
                    <label for="date_from" class="form-label">تاريخ البدء من:</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- تاريخ البدء إلى -->
                <div class="col-3">
                    <label for="date_to" class="form-label">تاريخ البدء إلى:</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>

                <!-- تاريخ الانتهاء من -->
                <div class="col-3">
                    <label for="end_from" class="form-label">تاريخ الانتهاء من:</label>
                    <input type="date" name="end_from" id="end_from" value="{{ request('end_from') }}" class="form-control">
                </div>

                <!-- تاريخ الانتهاء إلى -->
                <div class="col-3">
                    <label for="end_to" class="form-label">تاريخ الانتهاء إلى:</label>
                    <input type="date" name="end_to" id="end_to" value="{{ request('end_to') }}" class="form-control">
                </div>
            </div>

            <div class="row g-4 mt-4">
                <!-- تاريخ الالتحاق من -->
                <div class="col-3">
                    <label for="join_from" class="form-label">تاريخ الالتحاق من:</label>
                    <input type="date" name="join_from" id="join_from" value="{{ request('join_from') }}" class="form-control">
                </div>

                <!-- تاريخ الالتحاق إلى -->
                <div class="col-3">
                    <label for="join_to" class="form-label">تاريخ الالتحاق إلى:</label>
                    <input type="date" name="join_to" id="join_to" value="{{ request('join_to') }}" class="form-control">
                </div>

                <!-- تاريخ نهاية مدة الاختبار من -->
                <div class="col-3">
                    <label for="test_from" class="form-label">تاريخ نهاية مدة الاختبار من:</label>
                    <input type="date" name="test_from" id="test_from" value="{{ request('test_from') }}" class="form-control">
                </div>

                <!-- تاريخ نهاية مدة الاختبار إلى -->
                <div class="col-3">
                    <label for="test_to" class="form-label">تاريخ نهاية مدة الاختبار إلى:</label>
                    <input type="date" name="test_to" id="test_to" value="{{ request('test_to') }}" class="form-control">
                </div>
            </div>

            <div class="row g-4 mt-4">
                <!-- تاريخ توقيع العقد من -->
                <div class="col-3">
                    <label for="contract_from" class="form-label">تاريخ توقيع العقد من:</label>
                    <input type="date" name="contract_from" id="contract_from" value="{{ request('contract_from') }}" class="form-control">
                </div>

                <!-- تاريخ توقيع العقد إلى -->
                <div class="col-3">
                    <label for="contract_to" class="form-label">تاريخ توقيع العقد إلى:</label>
                    <input type="date" name="contract_to" id="contract_to" value="{{ request('contract_to') }}" class="form-control">
                </div>

                <!-- الحالة -->
                <div class="col-3">
                    <label for="status" class="form-label">الحالة:</label>
                    <select class="form-control" id="status" name="status">
                        <option>أختر</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>

                <!-- فترة العقود -->
                <div class="col-3">
                    <label for="contract_period" class="form-label">فترة العقود:</label>
                    <select class="form-control" id="contract_period" name="contract_period">
                        <option>الكل</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>
            </div>

            <div class="row g-4 mt-4">
                <!-- الموظف -->
                <div class="col-3">
                    <label for="employee" class="form-label">الموظف:</label>
                    <select class="form-control" id="employee" name="employee">
                        <option>الكل</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>

                <!-- حالة الموظف -->
                <div class="col-3">
                    <label for="employee_status" class="form-label">حالة الموظف:</label>
                    <select class="form-control" id="employee_status" name="employee_status">
                        <option>أختر</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>

                <!-- الفرع -->
                <div class="col-3">
                    <label for="branch" class="form-label">الفرع:</label>
                    <select class="form-control" id="branch" name="branch">
                        <option>الكل</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>
                <!--الورية -->
                <div class="col-3">
                    <label for="branch" class="form-label">الورية:</label>
                    <select class="form-control" id="branch" name="branch">
                        <option>الكل</option>
                        <!-- خياراتإضافية -->
                    </select>
                </div>
                       

                <!-- زر البحث -->
                <div class="col-3">
                    <button type="submit" class="btn btn-primary w-100 mt-4">عرض التقرير</button>
                </div>

                <!-- زر الإلغاء -->
                <div class="col-3">
                    <button type="reset" class="btn btn-outline-warning w-100 mt-4">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>









@endsection