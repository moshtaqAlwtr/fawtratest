@extends('master')

@section('title')
أضافة جهاز جديد
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">اضافة جهاز جديد</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">الرئيسية</li>
                        <li class="breadcrumb-item active">الإعدادات</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
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
<div class="card mt-5">
    <!-- البطاقة -->
    <div class="card-body">
        <form>
            <div class="row mb-3">
                <!-- الاسم -->
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" id="name" class="form-control" placeholder="أدخل الاسم">
                </div>

                <!-- المخزون -->
                <div class="col-md-6">
                    <label for="inventory" class="form-label">المخزن</label>
                    <select id="inventory" class="form-control">
                        <option value="">المستودع الرئيسي</option>
                        <!-- يمكنك إضافة خيارات إضافية هنا -->
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <!-- التصنيف الرئيسي -->
                <div class="col-md-6">
                    <label for="category" class="form-label">التصنيف الرئيسي</label>
                    <select id="category" class="form-control">
                        <option value="">اختر التصنيف</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>

                <!-- الحالة -->
                <div class="col-md-6">
                    <label for="status" class="form-label">الحالة</label>
                    <select id="status" class="form-control">
                        <option value="">اختر الحالة</option>
                        <!-- خيارات إضافية -->
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <!-- الصورة -->
                <div class="col-md-12">
                    <label for="image" class="form-label">الصورة</label>
                    <div class="d-flex align-items-center">
                        <input type="file" id="image" class="form-control w-50">
                        <small class="ms-3 text-muted">
                            صيغ الملفات (jpeg,jpg,png) أقصى حجم للملف: 20MB.
                        </small>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- الوصف -->
                <div class="col-md-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" class="form-control" rows="3" placeholder="أدخل الوصف"></textarea>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection