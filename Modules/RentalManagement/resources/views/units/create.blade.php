@extends('master')

@section('title')
أضافة وحدة 
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أضافة وحدة </h2>
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
<form action="{{ route('rental_management.units.store') }}" method="POST" id="products_form" enctype="multipart/form-data">
    @csrf <!-- إضافة CSRF token للحماية -->
    <!-- باقي العناصر هنا -->



    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
@endif
<div class="card">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
            </div>

            <div>
                <a href="{{ route('products.index') }}" class="btn btn-outline-danger">
                    <i class="fa fa-ban"></i>الغاء
                </a>
                <button type="submit" form="products_form" class="btn btn-outline-primary">
                    <i class="fa fa-save"></i>حفظ
                </button>
            </div>

        </div>
    </div>
</div>


<div class="card mt-5">
    <div class="card">
        <div class="card-body">

            <!-- الاسم ونوع الوحدة وزر أضف نوع -->
            <div class="row align-items-end g-3">

                <!-- الاسم -->
                <div class="col-md-6">
                    <label for="unitName" class="form-label">الاسم <span class="text-danger">*</span></label>
                    <input type="text" id="unitName" name="name" class="form-control" placeholder="أدخل الاسم" required>
                </div>

                <!-- نوع الوحدة -->
                <div class="col-md-6">
                    <label for="unitType" class="form-label">نوع الوحدة <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center">
                        <select id="unitType" name="unit_type_id" class="form-control me-3" required>
                            <option value="">من فضلك اختر</option>
                            @foreach($unitTypes as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        <a href="{{ route('rental_management.Settings.Add_Type.create') }}" class="btn btn-outline-primary">أضف نوع</a>
                    </div>
                </div>
            </div>

            <!-- درجة الأولوية والحالة -->
            <div class="row g-3 mt-3">

                <!-- درجة الأولوية -->
                <div class="col-md-6">
                    <label for="priority" class="form-label">درجة الأولوية</label>
                    <input type="text" id="priority" name="priority" class="form-control" placeholder="أدخل درجة الأولوية">
                </div>

                <!-- الحالة -->
                <div class="col-md-6">
                    <label class="form-label">الحالة</label>
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input type="radio" id="active" name="status" value="active" class="form-check-input" checked>
                            <label class="form-check-label" for="active">نشط</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="inactive" name="status" value="inactive" class="form-check-input">
                            <label class="form-check-label" for="inactive">غير نشط</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- الوصف -->
            <div class="row g-3 mt-3">
                <div class="col-md-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" name="description" class="form-control" rows="3" placeholder="أدخل الوصف"></textarea>
                </div>
            </div>

        </div>
    </div>
</div>
            </form>
        </div>
    </div>
</div>



@endsection
