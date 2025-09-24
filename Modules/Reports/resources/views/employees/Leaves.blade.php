@extends('master')

@section('title')
تقرير أجازات الموظفين
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير أجازات الموظفين</h2>
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
                <!-- الموظف -->
                <div class="col-md-6">
                    <label for="employee-search" class="form-label">الموظف:</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="employee-search" 
                        name="employee" 
                        placeholder="البحث باسم الموظف أو المعرف أو البريد الإلكتروني">
                </div>

                <!-- الفترة من -->
                <div class="col-md-3">
                    <label for="date_from" class="form-label">الفترة من:</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- الفترة إلى -->
                <div class="col-md-3">
                    <label for="date_to" class="form-label">الفترة إلى:</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-md-3">
                    <label for="group_by" class="form-label">نوع الوردية:</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>رئيسي</option>
                        <option>فرعي</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">المستوى الوظيفي :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>أختر</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">حالة الموظف :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>الكل</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">نوع الوظيفة :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>الكل</option>
                       
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-4">
           
                <div class="col-md-3">
                    <label for="group_by" class="form-label">المسمى الوظيفي :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>أختر</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">فرع :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>الكل</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">قسم :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>رئيسي</option>
                        <option>فرعي</option>
                    </select>
                </div>
            </div>
            <div class="d-flex justify-content-start mt-3">
                <!-- زر البحث -->
                <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">بحث</button>

                <!-- زر الإلغاء -->
                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">إلغاء</button>
            </div>
        </form>
    </div>
</div>
<div class="container my-5" >
    <!-- القسم العلوي -->
    <div class="row align-items-center mb-4">
      
        <div class="col-md-6 text-start">
            <h6 class="mb-1">مؤسسة أعمال خاصة للتجارة</h6>
            <p class="text-muted mb-0">الرياض، الرياض، الرياض</p>
            <div class="d-inline-block rounded-circle bg-light p-2">
                <i class="bi bi-person fs-1 text-muted"></i>
            </div>
        </div>
        <div class="col-md-6 text-end">
            <h5 class="mb-0">تقرير رصيد إجازات الموظفين</h5>
            <p class="text-muted mb-0">
                الوقت: <span>22:37:36 20-12-2024</span><br>
                التاريخ من: <span>01-01-2024</span> إلى: <span>20-12-2024</span><br>
                عدد موظفين: (1 الموظفين)
            </p>
        </div>
    </div>

    <!-- الجدول -->
     <div class="card">
        
    <div class="table-responsive">
        <table class="table table-bordered text-center">
            <thead class="table-light">
                <tr>
                    <th>اسم الموظف</th>
                    <th>إجمالي الأيام المأخوذة</th>
                    <th>إجمالي المأخوذة من قبل</th>
                    <th>إجمالي المأخوذة</th>
                    <th>إجمالي الرصيد المتبقي</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>محمد المنصوب مدير</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                    <td>0</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>








@endsection