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
            <div class="card ">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center" style="gap: 10px">
                                        <button class="btn btn-success" data-bs-toggle="modal"
                                            data-bs-target="#addTimeModal">
                                            <i class="fas fa-plus"></i> إضافة جديدة
                                        </button>
                                        <button class="btn btn-secondary">
                                            <i class="fas fa-file-import"></i> استيراد
                                        </button>
                                        <a href="{{ route('SittingTrackTime.create') }}" class="btn btn-secondary">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                        <div class="btn-group ms-2">
                                            <a href="{{ route('reports.time_tracking.index') }}"
                                                class="btn btn-outline-secondary">
                                                <i class="fas fa-pie-chart"></i>
                                            </a>

                                        </div>
                                    </div>


                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form">
                            <div class="form-body row">

                                <div class="form-group col-md-4">
                                    <label for="feedback2" class="sr-only"> المشروع</label>
                                    <select name="project_id" class="form-control select2">
                                        <option value="">اختر المشروع </option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">اختر النشاط </option>
                                        <option value="1">نشط </option>
                                        <option value="0">غير نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">اختر الموظف </option>
                                        @foreach ($employees as $employee)
                                            <option value="{{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>


                                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">الغاء
                                    الفلتر
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>

            <div class="modal fade" id="addTimeModal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content">
                        <div class="modal-header border-0 py-3">
                            <h6 class="modal-title fs-5">إضافة وقت</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                style="display: none;"></button>
                            <button type="button" class="btn text-danger" data-bs-dismiss="modal">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <div class="modal-body px-4">
                            <div class="mb-4">
                                <div class="row">
                                    <div class="col-6">
                                        <label class="mb-2">النشاط</label>
                                        <input type="text" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label class="mb-2">المشروع</label>
                                        <input type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-4">
                                    <label class="mb-2">الوقت ؟</label>
                                    <input type="text" class="form-control text-center fs-2 fw-light" value="0:00"
                                        readonly style="height: 80px;">
                                </div>
                                <div class="col-8">
                                    <label class="mb-2">الملاحظات</label>
                                    <textarea class="form-control" style="height: 80px; resize: none;"></textarea>
                                </div>

                            </div>
                            <div class="mb-4">
                                <label class="mb-2">موظف</label>
                                <section class="d-flex flex-wrap gap-2 select2">
                                    <option value=""> </option>
                                    <option value="1">Option 1</option>
                                </section>
                            </div>
                        </div>
                        <div class="modal-footer border-0 py-3">
                            <div class="d-flex justify-content-between w-100">
                                <button type="button" class="btn btn-success">إضافة</button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                <div>
                                    <button type="button" class="btn btn-danger">
                                        <i class="fas fa-power-off"></i>
                                        <span id="timerDisplay">0:00:00</span>
                                    </button>
                                    <button type="button" class="btn btn-primary">
                                        <i class="fas fa-play"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">النتائج</h6>
                    <div class="d-flex align-items-center gap-2">
                        <div class="btn-group">
                            <a href="{{ route('TrackTime.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-th"></i>
                            </a>
                            <a href="{{ route('TrackTime.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-list"></i>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>أضيف إلى</th>
                                <th>التاريخ</th>
                                <th>المشروع</th>
                                <th>الوقت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>

                                <td>
                                    <i class="fas fa-user me-1"></i>
                                    أحمد أبو حبيب
                                </td>
                                </td>
                                <td>27/12/2024</td>
                                <td>kjklj</td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <span>01:00</span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>1-1 من 1 النتائج المعروضة</div>
                        <button class="btn btn-danger btn-sm">
                            <i class="fas fa-trash me-1"></i>
                            حذف المحدد
                        </button>
                    </div>
                </div>
            </div>

            <!-- Modal delete -->
            <div class="modal fade" id="deleteModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">تأكيد الحذف</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-0">هل أنت متأكد من أنك تريد الحذف؟</p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                            <button type="button" class="btn btn-danger">تأكيد</button>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
    </div>
@endsection
