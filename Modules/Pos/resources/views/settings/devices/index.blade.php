@extends('master')

@section('title')
أعدادات أجهزة نقاط البيع
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أعدادات أجهزة نقاط البيع</h2>
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
<div class="card-body">
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('pos.settings.devices.create') }}" class="btn btn-outline-primary">
                            <i class="fa fa-plus me-2"></i> جهاز جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
 

<div class="card mt-5">
    <!-- نموذج البحث -->
    <div class="card p-4">
        <h5 class="card-title">بحث</h5>
        <form>
            <div class="mb-3">
                <label for="name" class="form-label">الاسم</label>
                <select id="name" class="form-control">
                    <option value="all">الكل</option>
                    <option value="option1">اختيار 1</option>
                    <option value="option2">اختيار 2</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="category" class="form-label">التصنيف الرئيسي</label>
                <select id="category" class="form-control">
                    <option value="all">الكل</option>
                    <option value="category1">تصنيف 1</option>
                    <option value="category2">تصنيف 2</option>
                </select>
            </div>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" onclick="search()">بحث</button>
                <button type="button" class="btn btn-secondary" onclick="resetFilters()">إلغاء الفلتر</button>
            </div>
        </form>
    </div>
</div>

    <!-- عرض النتائج -->
<div class="card p-4">
   <div class="card-body">
    <div class="mt-4">
        <h5>النتائج</h5>
        <div class="list-group">
            <div class="list-group-item d-flex justify-content-between align-items-center">
                <div>
                    <strong>Main POS Shift</strong>
                    <p class="mb-0">Main POS Shift</p>
                </div>
                <td>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                <li>
                                    <a class="dropdown-item" href="">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item text-danger" href="#">
                                        <i class="fa fa-trash me-2"></i>حذف
                                    </a>
                                </li>
                            </div>
                        </div>
                    </div>
                </td>
            </div>
        </div>
    </div>
</div>
</div>

</div>


<script>
    function search() {
        alert('تم تنفيذ عملية البحث.');
    }

    function resetFilters() {
        document.getElementById('name').value = 'all';
        document.getElementById('category').value = 'all';
        alert('تمت إعادة تعيين الفلاتر.');
    }
</script>



@endsection