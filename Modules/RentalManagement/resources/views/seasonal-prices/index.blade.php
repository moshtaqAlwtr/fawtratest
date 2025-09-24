@extends('master')

@section('title')
    الأسعار الموسمية
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الأسعار الموسمية</h2>
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
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('rental_management.seasonal-prices.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-2"></i>أضف سعر موسمي
                        </a>
                    </div>
                </div>
            </div>
        </div>

            <div class="card mt-5">
                <!-- بطاقة البحث -->
                <div class="card mb-4">
             
                    <div class="card-body">
                        <form>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="seasonalPricing" class="form-label">البحث بواسطة اسم سعر موسمي</label>
                                    <input type="text" id="seasonalPricing" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="searchByType" class="form-label">البحث بواسطة نوع الوحدة</label>
                                    <select id="searchByType" class="form-control">
                                        <option value="">اختر...</option>
                                        <!-- خيارات أخرى -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchByPricing" class="form-label">البحث بواسطة قاعدة التسعير</label>
                                    <select id="searchByPricing" class="form-control">
                                        <option value="">اختر...</option>
                                        <!-- خيارات أخرى -->
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="searchByDateFrom" class="form-label">البحث بواسطة التاريخ من</label>
                                    <input type="date" id="searchByDateFrom" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label for="searchByDateTo" class="form-label">البحث بواسطة التاريخ إلى</label>
                                    <input type="date" id="searchByDateTo" class="form-control">
                                </div>
                            </div>
                            <div class="row mt-3">
                            
                                <div class="col-md-6 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary me-2">بحث</button>
                                    <button type="reset" class="btn btn-secondary">إعادة تعيين</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
        
                <!-- بطاقة الجدول -->
                <div class="card">
                    <div class="card-header bg-secondary text-white">نتائج البحث</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الاسم</th>
                                    <th>التاريخ من</th>
                                    <th>التاريخ إلى</th>
                                    <th>قاعدة التسعير</th>
                                    <th>نوع الوحدة</th>
                                    <th>أيام</th>
                                    <th>ترتيب</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($prices as $price)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $price->name }}</td>
                                    <td>{{ $price->date_from }}</td>
                                    <td>{{ $price->date_to }}</td>
                                    <td>{{ $price->pricing_rule_id }}</td>
                                    <td>{{ $price->unit_type_id }}</td>
                                    <td>{{ $price->days }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton{{ $price->id }}" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $price->id }}">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('rental_management.seasonal-prices.show', $price->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('rental_management.seasonal-prices.edit', $price->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('rental_management.seasonal-prices.destroy', $price->id) }}" method="POST"
                                                            onsubmit="return confirm('هل أنت متأكد من أنك تريد حذف هذه القاعدة؟')">
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
                </div>
                
            </div>
        
 
        
        @endsection