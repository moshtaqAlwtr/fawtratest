@extends('master')

@section('title')
    عرض الوحدة: {{ $unit->name }}
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض الوحدة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">{{ $unit->name }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <div class="container mt-4">
        <!-- الأزرار العلوية -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <button class="btn btn-primary">تعديل</button>
                <button class="btn btn-info">تعطيل</button>
                <button class="btn btn-danger">حذف</button>
            </div>
            <button class="btn btn-success">إضافة مصروف جديد</button>
        </div>

        <!-- البطاقة الرئيسية -->
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab" aria-controls="details" aria-selected="true">التفاصيل</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history" type="button" role="tab" aria-controls="history" aria-selected="false">سجل التعديلات</button>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content" id="myTabContent">
                    <!-- تبويب التفاصيل -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <h5 class="card-title">بيانات الوحدة</h5>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>الإتاحة:</strong> 
                                    <span class="{{ $unit->availability ? 'text-success' : 'text-danger' }}">
                                        {{ $unit->availability ? 'متاح' : 'غير متاح' }}
                                        <i class="bi {{ $unit->availability ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }}"></i>
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>الوصف:</strong> {{ $unit->description ?? 'لا يوجد وصف' }}</p>
                            </div>
                        </div>
                        <p><strong>درجة الأولوية:</strong> {{ $unit->priority }}</p>
                    </div>

                    <!-- تبويب سجل النشاطات -->
                    <div class="tab-pane fade" id="history" role="tabpanel" aria-labelledby="history-tab">
                        <div class="card">
                            <div class="card-body">
                                <!-- الفلاتر العلوية -->
                                <div class="row mb-3">
                                    <div class="col-md-3">
                                        <select class="form-control">
                                            <option selected>كل الوحدات</option>
                                            <option>وحدة 1</option>
                                            <option>وحدة 2</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <select class="form-control">
                                            <option selected>كل الفاعلين</option>
                                            <option>المستخدم 1</option>
                                            <option>المستخدم 2</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" placeholder="الفترة من / إلى">
                                    </div>
                                    <div class="col-md-3 d-flex align-items-center">
                                        <button class="btn btn-outline-secondary me-2"><i class="bi bi-square"></i></button>
                                        <button class="btn btn-outline-secondary"><i class="bi bi-grid-3x3-gap-fill"></i></button>
                                    </div>
                                </div>

                                <!-- سجل النشاطات -->
                                <div class="timeline">
                                    <div class="timeline-item d-flex align-items-start mb-3">
                                        <!-- العنوان والأيقونة -->
                                        <div class="text-center me-3">
                                            <h5 class="text-primary">الأمس</h5>
                                            <i class="bi bi-check-circle-fill text-success" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <!-- الكرت -->
                                        <div class="card flex-grow-1">
                                            <div class="card-body">
                                                <p class="mb-1">مرزوق الرويس قام بإضافة وحدة ثلاث غرف</p>
                                                <div class="d-flex justify-content-start align-items-center gap-2">
                                                    <span class="badge bg-secondary">مرزوق الرويس</span>
                                                    <span class="badge bg-primary">13:58:29</span>
                                                    <span class="badge bg-success">Main Branch</span>
                                                </div>
                                                
                                            </div>
                                        </div>
                                    </div>
                                    <!-- يمكن تكرار النشاطات هنا -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
