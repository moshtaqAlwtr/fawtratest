@extends('master')

@section('title')
    عرض تتبع الوقت
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض تتبع الوقت</h2>
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




    <div class="content-body">
        <div class="container-fluid">

            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>استخراج فاتورة من Time Sheet <span style="color: red"></span> </label>
                        </div>

                        <div>
                            <a href="" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>

                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">


                    <div class="card-body">
                        <form class="form">
                            <div class="form-body row">

                                <div class="form-group col-md-3">
                                    <label for="feedback2" class=""> عرض ب</label>
                                    <select name="project_id" class="form-control select2">
                                        <option value=""> المشروع </option>
                                        <option value="1"> النشاط </option>
                                        <option value="2"> الموظف </option>
                                        <option value="3"> التاريخ </option>

                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="feedback2" class=""> الصيغة</label>
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">اختر النشاط </option>
                                        <option value="1">نشط </option>
                                        <option value="0">غير نشط</option>
                                    </select>
                                </div>
                                <div class="form-group col-md-6">
                                    <label class="form-check-label fs-5" for="project" style="margin-bottom: 10px;">
                                        اضف بهذا الوصف :
                                    </label>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" readonly id="project"
                                                style="width: 1.5em; height: 1.5em;">
                                            <label class="form-check-label fs-5" for="project" style="margin-right: 10px;">
                                                المشروع
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="activity"
                                                style="width: 1.5em; height: 1.5em;">
                                            <label class="form-check-label fs-5" for="activity" style="margin-right: 10px;">
                                                النشاط
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="employee"
                                                style="width: 1.5em; height: 1.5em;">
                                            <label class="form-check-label fs-5" for="employee" style="margin-right: 10px;">
                                                موظف
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="date"
                                                style="width: 1.5em; height: 1.5em;">
                                            <label class="form-check-label fs-5" for="date" style="margin-right: 10px;">
                                                التاريخ
                                            </label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="notes"
                                                style="width: 1.5em; height: 1.5em;">
                                            <label class="form-check-label fs-5" for="notes" style="margin-right: 10px;">
                                                الملاحظات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="form-body row">
                                <div class="form-group col-md-6">
                                    <label for="period" class=""> الفترة من / الى </label>
                                    <div class="dropdown">
                                        <input type="text" class="form-control" id="period" data-bs-toggle="dropdown" aria-expanded="false" placeholder="الفترة من / إلى">
                                        <div class="dropdown-menu w-60 p-0">
                                            <div class="list-group list-group-flush">
                                                <a href="#" data-period="last_week" class="list-group-item list-group-item-action border-0 px-3">الأسبوع الماضي</a>
                                                <a href="#" data-period="last_month" class="list-group-item list-group-item-action border-0 px-3">الشهر الأخير</a>
                                                <a href="#" data-period="month_to_date" class="list-group-item list-group-item-action border-0 px-3">من أول الشهر حتى اليوم</a>
                                                <a href="#" data-period="last_year" class="list-group-item list-group-item-action border-0 px-3">السنة الماضية</a>
                                                <a href="#" data-period="year_to_date" class="list-group-item list-group-item-action border-0 px-3">من أول السنة حتى اليوم</a>
                                                {{-- <a href="#" data-period="custom" class="list-group-item list-group-item-action border-0 px-3">الفترة من / إلى</a>
                                                <a href="#" data-period="specific_date" class="list-group-item list-group-item-action border-0 px-3">تاريخ محدد</a>
                                                <a href="#" data-period="all_before" class="list-group-item list-group-item-action border-0 px-3">كل التواريخ قبل</a>
                                                <a href="#" data-period="all_after" class="list-group-item list-group-item-action border-0 px-3">كل التواريخ بعد</a> --}}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="hourly_rate" class=""> الراتب الافتراضي للساعة ؟ </label>
                                    <input type="text" class="form-control" id="hourly_rate">
                                </div>
                            </div>



                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for="feedback2" class="">المشروع</label>
                                    <select name="project_id" class="form-control select2">
                                        <option value="">اختر المشروع</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="feedback2" class="">النشاط</label>
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">اختر النشاط</option>
                                        <option value="1">نشط</option>
                                        <option value="0">غير نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="feedback2" class=""> الموظف</label>
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">اختر الموظف</option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                        </form>

                    </div>

                </div>

            </div>


            <!-- Modal delete -->



        </div>
    </div>
    </div>
@endsection
@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dropdownItems = document.querySelectorAll('.list-group-item-action');
        const periodInput = document.getElementById('period');

        function formatDate(date) {
            return date.toISOString().split('T')[0];
        }

        function getDateRange(period) {
            const today = new Date();
            let startDate, endDate;

            switch(period) {
                case 'last_week':
                    endDate = today;
                    startDate = new Date(today);
                    startDate.setDate(today.getDate() - 7);
                    break;
                case 'last_month':
                    endDate = today;
                    startDate = new Date(today);
                    startDate.setMonth(today.getMonth() - 1);
                    break;
                case 'month_to_date':
                    endDate = today;
                    startDate = new Date(today.getFullYear(), today.getMonth(), 1);
                    break;
                case 'last_year':
                    endDate = today;
                    startDate = new Date(today);
                    startDate.setFullYear(today.getFullYear() - 1);
                    break;
                case 'year_to_date':
                    endDate = today;
                    startDate = new Date(today.getFullYear(), 0, 1);
                    break;
                default:
                    return '';
            }

            return `${formatDate(startDate)} - ${formatDate(endDate)}`;
        }

        dropdownItems.forEach(item => {
            item.addEventListener('click', function(e) {
                e.preventDefault();
                const period = this.dataset.period;
                const dateRange = getDateRange(period);

                if (dateRange) {
                    periodInput.value = dateRange;
                } else {
                    periodInput.value = this.textContent.trim();
                }
            });
        });
    });
</script>
@endsection
