@extends('master')

@section('title')
تقارير الموظفين
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقارير الموظفين</h2>
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
                <!-- الضرائب -->
                <div class="col-md-3">
                    <label for="taxes" class="form-label">موظف:</label>
                    <select class="form-control" id="taxes" name="customer">
                        <option>اختر</option>
                    </select>
                </div>

                <!-- نوعية الدخل -->
                <div class="col-md-3">
                    <label for="income_type" class="form-label">حالة الموظف :</label>
                    <select class="form-control" id="income_type" name="income_type">
                        <option>اختر</option>
                    </select>
                </div>

                <!-- الفترة من -->
                <div class="col-md-3">
                    <label for="date_from" class="form-label">من تاريخ أنتهاء الأقامة :</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- الفترة إلى -->
                <div class="col-md-3">
                    <label for="date_to" class="form-label">ألى تاريخ أنتهاء الأقامة :</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>

        

                <!-- الفرع -->
                <div class="col-md-3">
                    <label for="branch" class="form-label">فرع:</label>
                    <input type="text" name="branch" id="branch" value="{{ request('branch') }}" class="form-control" placeholder="اسم الفرع">
                </div>
             

            <div class="d-flex justify-content-end mt-3">
                <!-- زر البحث -->
                <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">بحث</button>

                <!-- زر الإلغاء -->
                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">إلغاء</button>
            </div>
        </form>
    </div>
</div>

@endsection
