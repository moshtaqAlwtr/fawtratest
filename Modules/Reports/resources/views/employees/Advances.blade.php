@extends('master')

@section('title')
تقرير سلف الموظفين  
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير سلف الموظفين  </h2>
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
                <div class="col-md-3">
                    <label for="date_from" class="form-label">الفترة من:</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- الفترة إلى -->
                <div class="col-md-3">
                    <label for="date_to" class="form-label">الفترة إلى:</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
                       <!-- الفترة من -->
                       <div class="col-md-3">
                    <label for="date_from" class="form-label">الدفعة القادمة من:</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- الفترة إلى -->
                <div class="col-md-3">
                    <label for="date_to" class="form-label">الدفعة القادمة إلى:</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>
            </div>


         

            <div class="row g-3 mt-4">
           
                <div class="col-md-3">
                    <label for="group_by" class="form-label">الحالة  :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>أختر</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">معدل السداد :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>الكل</option>
                      
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">الموظف  :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>الكل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="group_by" class="form-label">حالة الموظف  :</label>
                    <select class="form-control" id="group_by" name="group_by">
                        <option>أختر</option>
                      
                    </select>
                </div>
            </div>
            <div class="row g-3 mt-4">
           
           <div class="col-md-3">
               <label for="group_by" class="form-label">الوردية  :</label>
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
         
       </div>

            <div class="d-flex justify-content-start mt-3">
                <!-- زر البحث -->
                <button type="submit" class="btn btn-primary me-2 waves-effect waves-light">عرض التقرير</button>

                <!-- زر الإلغاء -->
                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">إلغاء</button>
            </div>
        </form>
    </div>
</div>








@endsection