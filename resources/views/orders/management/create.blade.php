@extends('master')

@section('title')
    أضافة طلب أجازة
@stop

@section('content')

    <div class="card">

    </div>
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">طلب أجازة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">أضافة
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>



    <style>
        .custom-file-upload {
            border: 2px dashed #ccc;
            border-radius: 10px;
            text-align: center;
            padding: 20px;
            cursor: pointer;
        }
        .custom-file-upload:hover {
            border-color: #007bff;
        }
    </style>


    <!-- 🔹 كارد الطلب -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="mb-0">معلومات طلب</h5>
        </div>
        <div class="card-body">
            <form>

                <!-- 🔹 الحقول العلوية -->
                <div class="row g-3">
                    <!-- حقل الموظف -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">موظف <span class="text-danger">*</span></label>
                        <select class="form-control">
                            <option selected disabled>اختر موظف</option>
                            <option>موظف 1</option>
                            <option>موظف 2</option>
                        </select>
                    </div>

                    <!-- حقل تاريخ التقديم -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">تاريخ التقديم</label>
                        <input type="date" class="form-control">
                    </div>

                    <!-- حقل تاريخ التنفيذ -->
                    <div class="col-md-3">
                        <label class="form-label fw-bold">تاريخ التنفيذ <span class="text-danger">*</span></label>
                        <input type="date" class="form-control">
                    </div>
                </div>

                <!-- 🔹 المرفقات والوصف -->
                <div class="row g-3 mt-3">
                    <!-- المرفقات -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المرفقات</label>
                        <div class="custom-file-upload" id="fileUploadArea">
                            <input type="file" id="fileInput" class="d-none">
                            <p class="mb-1">📂 <b>اسحب الملف هنا أو اختر ملف من جهازك</b></p>
                            <small class="text-muted">أقصى حد للملف 5 ميجا بايت</small>
                            <br>
                            <small class="text-muted">أنواع الملفات المسموحة: png, jpg, gif, bmp, zip, office files</small>
                        </div>
                    </div>

                    <!-- الوصف -->
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الوصف <span class="text-danger">*</span></label>
                        <textarea class="form-control" rows="5" placeholder="أدخل تفاصيل الطلب"></textarea>
                    </div>
                </div>

                <!-- 🔹 زر الإرسال -->
                <div class="mt-4 text-end">
                    <button type="submit" class="btn btn-primary">إرسال الطلب</button>
                </div>

            </form>
        </div>
    </div>



    @endsection