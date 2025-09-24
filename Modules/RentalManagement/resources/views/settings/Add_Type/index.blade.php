@extends('master')

@section('title')
    أنواع الوحدات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">أنواع الوحدات</h2>
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
    
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                 
                    <div>
                        <a href="{{ route('rental_management.Settings.Add_Type.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-2"></i>أضف نوع الوحدة
                        </a>
                    </div>
                </div>
            </div>
        </div>





<div class="card mt-5">


    <!-- حقول البحث -->
    <div class="card p-3 mb-4">
        <form id="searchForm" class="row g-3">
            <!-- البحث بواسطة اسم الوحدة -->
            <div class="col-md-4">
                <label for="unitName" class="form-label">اسم نوع الوحدة</label>
                <input type="text" id="unitName" class="form-control" placeholder="اسم الوحدة">
            </div>

            <!-- البحث بواسطة قاعدة التسعير -->
            <div class="col-md-4">
                <label for="pricingRule" class="form-label">قاعدة التسعير</label>
                <select id="pricingRule" class="form-control">
                    <option value="">اختر قاعدة</option>
                    <option value="fixed">ثابت</option>
                    <option value="dynamic">ديناميكي</option>
                </select>
            </div>

            <!-- البحث بواسطة الحالة -->
            <div class="col-md-4">
                <label for="status" class="form-label">الحالة</label>
                <select id="status" class="form-control">
                    <option value="">اختر الحالة</option>
                    <option value="available">متاحة</option>
                    <option value="unavailable">غير متاحة</option>
                </select>
            </div>

            <!-- البحث بواسطة طريقة التسعير -->
            <div class="col-md-4">
                <label for="pricingMethod" class="form-label">طريقة التسعير</label>
                <select id="pricingMethod" class="form-control">
                    <option value="">اختر الطريقة</option>
                    <option value="perNight">بالليلة</option>
                    <option value="perHour">بالساعة</option>
                </select>
            </div>

            <!-- أزرار البحث وإلغاء الفلتر -->
            <div class="col-12 d-flex justify-content-end">
                <button type="button" class="btn btn-primary me-2">بحث</button>
                <button type="reset" class="btn btn-secondary">إلغاء الفلتر</button>
            </div>
        </form>
    </div>
    </div>

    <!-- جدول النتائج -->
<!-- جدول النتائج -->
<div class="card p-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>طريقة التسعير</th>
                <th>قاعدة التسعير</th>
                <th>الحالة</th>
                <th>الترتيب</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unitstype as $type)
                <tr>
                    <td>{{ $type->name }}</td>
                    <td>{{ $type->PricingRule->pricingName }}</td>
                    <td>
                        @if ($type->PricingRule->pricingMethod == '1')
                            بالساعات
                        @elseif ($type->PricingRule->pricingMethod  == '2')
                            بالايام
                        @elseif ($type->PricingRule->pricingMethod  == '3')
                            بالاسبوع
                        @elseif ($type->PricingRule->pricingMethod  == '4')
                        بالشهر
                        @elseif ($type->PricingRule->pricingMethod  == '5')
                            ستة اشهر
                        @else
                            سنة
                        @endif
                    </td>
                    <td>{{ $type->status == 1 ? 'نشط' : 'غير نشط' }}</td> <!-- تحويل القيم 1 و 2 إلى نص -->
                    
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" id="dropdownMenuButton{{ $type->id }}" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $type->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('rental_management.Settings.Add_Type.show', $type->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('rental_management.Settings.Add_Type.edit', $type->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <form action="{{ route('rental_management.Settings.Add_Type.delete', $type->id) }}" method="POST"
                                              onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه الوحدة؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger">
                                                <i class="fa fa-trash"></i> حذف
                                            </button>
                                        </form>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>        
    </table>
</div>

















        @endsection