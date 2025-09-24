@extends('master')

@section('title')
وردية جديدة
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">وردية جديدة</h2>
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
    <div class="card p-4">
        
        <form>
            <div class="row mb-3">
                <!-- الاسم -->
                <div class="col-md-6">
                    <label for="name" class="form-label">الاسم</label>
                    <input type="text" id="name" class="form-control" placeholder="أدخل الاسم">
                </div>

                <!-- المرفقات -->
                <div class="col-md-6">
                    <label for="attachments" class="form-label">المرفقات</label>
                    <div class="border p-3 rounded text-center" style="cursor: pointer;">
                        <input type="file" id="attachments" class="form-control d-none">
                        <span>افلت الملف هنا أو اختر من جهازك</span>
                        <img src="https://via.placeholder.com/20" alt="Upload Icon" class="ms-2">
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <!-- التصنيف الرئيسي -->
                <div class="col-md-6">
                    <label for="category" class="form-label">التصنيف الرئيسي</label>
                    <select id="category" class="form-control">
                        <option value="">Select category</option>
                        <option value="1">Main POS Shift</option>
                    </select>
                </div>

                <!-- الوصف -->
                <div class="col-md-6">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" class="form-control" rows="3" placeholder="أدخل الوصف"></textarea>
                </div>
            </div>

         
        </form>
    </div>
</div>

@endsection
