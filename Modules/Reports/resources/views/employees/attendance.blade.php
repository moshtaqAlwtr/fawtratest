@extends('master')

@section('title')
تقرير حضور تفصيلي موظف واحد
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تقرير حضور تفصيلي موظف واحد</h2>
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
                <div class="col-md-4">
                    <label for="employee-search" class="form-label">الموظف:</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="employee-search" 
                        name="employee" 
                        placeholder="البحث باسم الموظف أو المعرف أو البريد الإلكتروني">
                </div>

                <!-- الفترة من -->
                <div class="col-md-4">
                    <label for="date_from" class="form-label">الفترة من:</label>
                    <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}" class="form-control">
                </div>

                <!-- الفترة إلى -->
                <div class="col-md-4">
                    <label for="date_to" class="form-label">الفترة إلى:</label>
                    <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}" class="form-control">
                </div>
            </div>

            <div class="row g-3 mt-4">
                <div class="col-md-4">
                    <label for="group_by" class="form-label">نوع الوردية:</label>
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
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">جدول الورديات</h5>
        </div>
        <div class="card-body">
            <!-- جعل الجدول قابلاً للتمرير -->
            <div class="table-responsive">
                <table class="table table-bordered text-center">
                    <thead class="bg-light">
                        <tr>
                            <th>الشهر</th>
                            <th>TP</th>
                            <th>TA</th>
                            <th>TH</th>
                            <!-- الأيام -->
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
                            <th>TR</th>
                            <th>TH</th>
                            <th>TP</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- صفوف الجدول -->
                        <tr>
                            <td>يناير 2024</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <!-- البيانات -->
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
                            <td>OFF</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                        <tr>
                            <td>فبراير 2024</td>
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
                            <td>OFF</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- إجمالي القيم -->
            <div class="d-flex justify-content-between mt-4">
                <div>
                    <span class="badge bg-warning">إجمالي الغياب (TA):</span> 0
                </div>
                <div>
                    <span class="badge bg-info">إجمالي الحضور (TP):</span> 0
                </div>
                <div>
                    <span class="badge bg-success">إجمالي الساعات (TH):</span> 0
                </div>
            </div>
        </div>
    </div>
</div>






@endsection