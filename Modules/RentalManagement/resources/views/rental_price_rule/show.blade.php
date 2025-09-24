@extends('master')

@section('title')
    عرض قواعد التسعير
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض قواعد التسعير</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <strong> </strong> | <small>#</small> | 
                        <span class="badge badge-pill badge-success">في المخزن</span>
                    </div>
                    <div>
                        <a href="{{ route('rental_management.rental_price_rule.edit', $rule->id) }}" class="btn btn-outline-primary">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                        <a href="{{ route('rental_management.rental_price_rule.destroy', $rule->id) }}" class="btn btn-outline-danger" 
                            onclick="return confirm('هل أنت متأكد من أنك تريد حذف هذه القاعدة؟')">
                             <i class="fa fa-trash"></i> حذف
                         </a>
                         
                        <a href="" class="btn btn-outline-secondary">
                            <i class="fa fa-ban"></i> تعطيل
                        </a>
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab" aria-controls="details" aria-selected="true">التفاصيل</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="logs-tab" data-toggle="tab" href="#logs" role="tab" aria-controls="logs" aria-selected="false">سجل النشاطات</a>
                    </li>
                </ul>
                <div class="tab-content mt-3">
                    <!-- تفاصيل -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="p-2 rounded" style="background-color: #f0f8ff; color: #333; font-weight: bold;">
                                    معلومات عن قاعدة التسعير
                                </h4>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <strong>النوع:</strong>  <!-- استبدال "قياسي" بـ $rule->type -->
                                    </div>
                                    <div class="col-6">
                                        <strong>العملة:</strong> {{ $rule->currency }} <!-- استبدال "ريال سعودي - SAR" بـ $rule->currency -->
                                    </div>
                                    <div class="col-6 mt-2">
                                        <strong>طريقة التسعير:</strong> {{ $rule->pricingMethod }} <!-- استبدال "أيام" بـ $rule->pricingMethod -->
                                    </div>
                                </div>
                                
                                <h4 class="p-2 rounded mt-4" style="background-color: #f0f8ff; color: #333; font-weight: bold;">
                                    السعر
                                </h4>
                                <div class="row mt-3">
                                    <div class="col-6">
                                        <strong>سعر اليوم:</strong> {{ $rule->dailyPrice }} <!-- استبدال "100" بـ $rule->dailyPrice -->
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                    
<!-- سجل النشاطات -->
<div class="tab-pane fade" id="logs" role="tabpanel" aria-labelledby="logs-tab">
    <div class="row mb-3">
        <div class="col-md-4">
            <div class="form-group">
                <label for="actions">كل الإجراءات</label>
                <select id="actions" class="form-control">
                    <option value="all">كل الإجراءات</option>
                    <option value="create">إضافة</option>
                    <option value="update">تعديل</option>
                    <option value="delete">حذف</option>
                    <option value="disable">تعطيل</option>
                </select>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="users">كل الفاعلين</label>
                <select id="users" class="form-control">
                    <option value="all">كل الفاعلين</option>
                    <option value="admin">المدير</option>
                    <option value="sales">مندوبي المبيعات</option>
                    <option value="support">الدعم الفني</option>
                </select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="from-date">الفترة من</label>
                <input type="date" id="from-date" class="form-control">
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="to-date">الفترة إلى</label>
                <input type="date" id="to-date" class="form-control">
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <button class="btn btn-primary btn-block">بحث</button>
        </div>
    </div>

    <!-- عرض السجل -->
    <div class="card mt-3 border-0 shadow-sm">
        <div class="card-body position-relative">
            
            <!-- إذا لم يكن هناك سجلات، يظهر هذا النص -->
            
                <div class="text-center text-muted mt-4">
                    <strong>لا توجد نشاطات لعرضها.</strong>
                </div>
            
        </div>
    </div>
</div>

                        <hr>
                        <div class="text-center text-muted mt-4">
                            <strong>لا توجد نشاطات لعرضها.</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
