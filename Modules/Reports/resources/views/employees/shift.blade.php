@extends('master')

@section('title')
وردية الحضور
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">وردية الحضور</h2>
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
<div class="card mt-5">
    <!-- جدول الموظفين -->
    <div class="table-responsive">
        <table class="table table-bordered text-center align-middle">
            <thead class="table-light">
                <tr>
                    <th class="text-start">موظف</th>
                    <th>1<br><span class="text-muted">ديسمبر</span></th>
                    <th>2<br><span class="text-muted">ديسمبر</span></th>
                    <th>3<br><span class="text-muted">ديسمبر</span></th>
                    <th>4<br><span class="text-muted">ديسمبر</span></th>
                    <th>5<br><span class="text-muted">ديسمبر</span></th>
                    <th>6<br><span class="text-muted">ديسمبر</span></th>
                    <th>7<br><span class="text-muted">ديسمبر</span></th>
                    <th>8<br><span class="text-muted">ديسمبر</span></th>
                    <th>9<br><span class="text-muted">ديسمبر</span></th>
                    <th>10<br><span class="text-muted">ديسمبر</span></th>
                    <th>11<br><span class="text-muted">ديسمبر</span></th>
                    <th>12<br><span class="text-muted">ديسمبر</span></th>
                    <th>13<br><span class="text-muted">ديسمبر</span></th>
                    <th>14<br><span class="text-muted">ديسمبر</span></th>
                    <th>15<br><span class="text-muted">ديسمبر</span></th>
                    <th>16<br><span class="text-muted">ديسمبر</span></th>
                    <th>17<br><span class="text-muted">ديسمبر</span></th>
                    <th>18<br><span class="text-muted">ديسمبر</span></th>
                    <th>19<br><span class="text-muted">ديسمبر</span></th>
                    <th>20<br><span class="text-muted">ديسمبر</span></th>
                    <th>21<br><span class="text-muted">ديسمبر</span></th>
                    <th>22<br><span class="text-muted">ديسمبر</span></th>
                    <th>23<br><span class="text-muted">ديسمبر</span></th>
                    <th>24<br><span class="text-muted">ديسمبر</span></th>
                    <th>25<br><span class="text-muted">ديسمبر</span></th>
                    <th>26<br><span class="text-muted">ديسمبر</span></th>
                    <th>27<br><span class="text-muted">ديسمبر</span></th>
                    <th>28<br><span class="text-muted">ديسمبر</span></th>
                    <th>29<br><span class="text-muted">ديسمبر</span></th>
                    <th>30<br><span class="text-muted">ديسمبر</span></th>
                    <th>31<br><span class="text-muted">ديسمبر</span></th>
                </tr>
            </thead>
            <tbody>
                <!-- صفوف الموظفين -->
                <tr>
                    <td class="text-start">راكان الغثاني</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-start">عدنان العوفي</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-start">محمد الدريسي</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td class="text-start">محمد المنصوب مدير</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>








@endsection