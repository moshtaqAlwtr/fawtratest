@extends('master')

@section('title')
تعديل فرع 
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تعديل فرع </h2>
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




<div class="card mt-5">
    <!-- الكرت -->
    <div class="card">
        <div class="card-header text-center bg-primary text-white">
            <h5>إضافة نوع الوحدات</h5>
        </div>
        <form id="unitTypeForm" method="POST" action="{{ route('rental_management.Settings.Add_Type.update', $type->id) }}">
            @csrf
        
            <!-- السطر الأول: الاسم والحالة -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="unitName" class="form-label">اسم الوحدة</label>
                    <input type="text" id="unitName" name="unitName" class="form-control" 
                           placeholder="اسم الوحدة" value="{{ old('unitName', $type->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">الحالة <span class="text-danger">*</span></label>
                    <div class="d-flex align-items-center">
                        <div class="form-check me-3">
                            <input type="radio" id="active" name="status" class="form-check-input" value="1" 
                                   {{ old('status', $type->status) == 1 ? 'checked' : '' }}>
                            <label for="active" class="form-check-label">نشط</label>
                        </div>
                        <div class="form-check">
                            <input type="radio" id="inactive" name="status" class="form-check-input" value="2" 
                                   {{ old('status', $type->status) == 2 ? 'checked' : '' }}>
                            <label for="inactive" class="form-check-label">غير نشط</label>
                        </div>
                    </div>
                </div>
            </div>
        
            <!-- السطر الثاني: طريقة التسعير وقاعدة التسعير -->
            <div class="row mb-3 align-items-end">
                <div class="col-md-5">
                    <label for="pricingMethod" class="form-label">طريقة التسعير <span class="text-danger">*</span></label>
                    <select id="pricingMethod" name="pricingMethod" class="form-control" required>
                        <option value="">اختر طريقة التسعير</option>
                        @foreach($pricingRules as $rule)
                            <option value="{{ $rule->id }}" 
                                    {{ old('pricingMethod', $type->pricing_rule_id) == $rule->id ? 'selected' : '' }}
                                    data-rule="{{ $rule->pricingRule }}">
                                {{ $rule->pricingName }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-5">
                    <label for="pricingRule" class="form-label">قاعدة التسعير</label>
                    <input type="text" id="pricingRule" name="pricingRule" class="form-control" 
                           placeholder="قاعدة التسعير" value="{{ old('pricingRule', $type->pricing_rule) }}" readonly>
                </div>
                <div class="col-md-2">
                    <label class="form-label d-block">إضافة قاعدة</label>
                    <a href="{{ route('rental_management.rental_price_rule.create') }}" 
                       class="btn btn-secondary w-100 text-decoration-none">أضف قاعدة</a>
                </div>
            </div>
        
            <!-- السطر الثالث: الحضور والمغادرة -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="checkInTime" class="form-label">وقت الحضور</label>
                    <input type="time" id="checkInTime" name="checkInTime" class="form-control" 
                           value="{{ old('checkInTime', $type->check_in_time) }}" required>
                </div>
                <div class="col-md-6">
                    <label for="checkOutTime" class="form-label">وقت المغادرة</label>
                    <input type="time" id="checkOutTime" name="checkOutTime" class="form-control" 
                           value="{{ old('checkOutTime', $type->check_out_time) }}" required>
                </div>
            </div>
        
            <!-- السطر الرابع: الضريبة 1 والضريبة 2 -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="tax1" class="form-label">الضريبة 1 (%)</label>
                    <input type="number" id="tax1" name="tax1" class="form-control" 
                           placeholder="أدخل نسبة الضريبة" value="{{ old('tax1', $type->tax1) }}">
                </div>
                <div class="col-md-6">
                    <label for="tax2" class="form-label">الضريبة 2 (%)</label>
                    <input type="number" id="tax2" name="tax2" class="form-control" 
                           placeholder="أدخل نسبة الضريبة" value="{{ old('tax2', $type->tax2) }}">
                </div>
            </div>
        
            <!-- السطر الأخير: الوصف -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" name="description" class="form-control" rows="3"
                              placeholder="أدخل وصف الوحدة">{{ old('description', $type->description) }}</textarea>
                </div>
            </div>
        
            <!-- زر الحفظ -->
            <div class="text-center">
                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
            </div>
        </form>
        
    </div>
</div>

<script>
document.getElementById('pricingMethod').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex]; // الخيار المحدد
    const pricingRule = selectedOption.getAttribute('data-rule'); // قاعدة التسعير من الخاصية
    
    console.log('Selected Option:', selectedOption);
    console.log('Pricing Rule:', pricingRule);
    
    document.getElementById('pricingRule').value = pricingRule; // تعيين قاعدة التسعير
});
</script>

@endsection