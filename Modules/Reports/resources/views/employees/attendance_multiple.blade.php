@extends('master')

@section('title')
تقرير حضور تفصيلي عدة موظفين 
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير حضور تفصيلي عدة موظفين</h2>
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
    <div class="card">
        <!-- رأس الجدول -->
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">جدول الحضور</h5>
        </div>

        <!-- جسم الجدول -->
        <div class="card-body">
            <!-- جعل الجدول قابلاً للتمرير -->
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <!-- رأس الجدول -->
                    <thead class="bg-light">
                        <tr>
                            <th>Employee Name</th>
                            <th>TA</th>
                            <th>TL</th>
                            <th>TH</th>
                            <!-- أيام الشهر -->
                            <th>1</th>
                            <th>2</th>
                            <th>3</th>
                            <th>4</th>
                            <th>5</th>
                            <th>6</th>
                            <th>7</th>
                            <th>8</th>
                            <th>9</th>
                            <th>10</th>
                            <th>11</th>
                            <th>12</th>
                            <th>13</th>
                            <th>14</th>
                            <th>15</th>
                            <th>16</th>
                            <th>17</th>
                            <th>18</th>
                            <th>19</th>
                            <th>20</th>
                            <th>21</th>
                            <th>22</th>
                            <th>23</th>
                            <th>24</th>
                            <th>25</th>
                            <th>26</th>
                            <th>27</th>
                            <th>28</th>
                            <th>29</th>
                            <th>30</th>
                            <th>31</th>
                        </tr>
                    </thead>
                    <!-- جسم الجدول -->
                    <tbody>
                        <!-- الصف الأول -->
                        <tr>
                            <td>أحمد علي</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <!-- الأيام -->
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                        </tr>
                        <!-- الصف الثاني -->
                        <tr>
                            <td>محمد سعيد</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                            <td>OFF</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- القيم الإجمالية -->
            <div class="d-flex justify-content-between mt-3">
                <div>
                    <span class="badge bg-warning">إجمالي الغياب (TA):</span> 0
                </div>
                <div>
                    <span class="badge bg-danger">إجمالي التأخير (TL):</span> 0
                </div>
                <div>
                    <span class="badge bg-success">إجمالي الحضور (TP):</span> 0
                </div>
            </div>
        </div>
    </div>
</div>







@endsection