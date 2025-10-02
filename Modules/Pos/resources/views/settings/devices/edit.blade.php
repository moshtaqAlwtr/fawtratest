
@extends('master')

@section('title')
تعديل الجهاز - {{ $device->device_name }}
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تعديل الجهاز: {{ $device->device_name }}</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pos.settings.devices.index') }}">الأجهزة</a></li>
                        <li class="breadcrumb-item active">تعديل</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- عرض رسائل الخطأ --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h6><i class="fa fa-exclamation-triangle"></i> يرجى تصحيح الأخطاء التالية:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- عرض رسائل النجاح --}}
@if (session('success'))
    <div class="alert alert-success">
        <i class="fa fa-check-circle"></i> {{ session('success') }}
    </div>
@endif

<form action="{{ url('POS/Devices/update/' . $device->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="{{ route('pos.settings.devices.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> الغاء
                    </a>
                    <button type="submit" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ التغييرات
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-3">
        <div class="card-body">
            <div class="row mb-3">
                <!-- الاسم -->
                <div class="col-md-6">
                    <label for="device_name" class="form-label">
                        الاسم <span style="color: red">*</span>
                    </label>
                    <input type="text" 
                           id="device_name" 
                           name="device_name" 
                           class="form-control @error('device_name') is-invalid @enderror" 
                           placeholder="أدخل اسم الجهاز"
                           value="{{ old('device_name', $device->device_name) }}"
                           required>
                    @error('device_name')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- المخزن -->
                <div class="col-md-6">
                    <label for="store_id" class="form-label">
                        المخزن <span style="color: red">*</span>
                    </label>
                    <select id="store_id" 
                            name="store_id" 
                            class="form-control @error('store_id') is-invalid @enderror"
                            required>
                        <option value="">اختر المخزن</option>
                        @if(isset($storehouses))
                            @foreach($storehouses as $storehouse)
                                <option value="{{ $storehouse->id }}" 
                                        {{ (old('store_id', $device->store_id) == $storehouse->id) ? 'selected' : '' }}>
                                    {{ $storehouse->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('store_id')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- التصنيف الرئيسي -->
                <div class="col-md-6">
                    <label for="main_category_id" class="form-label">
                        التصنيف الرئيسي
                    </label>
                    <select id="main_category_id" 
                            name="main_category_id" 
                            class="form-control @error('main_category_id') is-invalid @enderror">
                        <option value="">اختر التصنيف</option>
                        @if(isset($devices))
                            @foreach($devices as $deviceOption)
                                <option value="{{ $deviceOption->id }}" 
                                        {{ (old('main_category_id', $device->main_category_id) == $deviceOption->id) ? 'selected' : '' }}>
                                    {{ $deviceOption->device_name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('main_category_id')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- الحالة -->
                <div class="col-md-6">
                    <label for="device_status" class="form-label">
                        الحالة <span style="color: red">*</span>
                    </label>
                    <select id="device_status" 
                            name="device_status" 
                            class="form-control @error('device_status') is-invalid @enderror"
                            required>
                        <option value="">اختر الحالة</option>
                        @if(isset($statusOptions) && is_array($statusOptions))
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" 
                                        {{ (old('device_status', $device->device_status) == $value) ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('device_status')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- الصورة -->
                <div class="col-md-12">
                    <label for="device_image" class="form-label">الصورة</label>
                    
                    <!-- عرض الصورة الحالية -->
                    @if($device->device_image)
                        <div class="current-image mb-2">
                            <label class="form-label">الصورة الحالية:</label>
                            <br>
                            <img src="{{ $device->image_url }}" 
                                 alt="{{ $device->device_name }}" 
                                 style="max-width: 200px; max-height: 200px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                        </div>
                    @endif
                    
                    <div class="d-flex align-items-center">
                        <input type="file" 
                               name="device_image" 
                               id="device_image" 
                               class="form-control w-50 @error('device_image') is-invalid @enderror"
                               accept="image/jpeg,image/jpg,image/png">
                        <small class="ms-3 text-muted">
                            صيغ الملفات (jpeg,jpg,png) أقصى حجم للملف: 20MB
                            <br><small>اتركه فارغاً للاحتفاظ بالصورة الحالية</small>
                        </small>
                    </div>
                    @error('device_image')
                        <div class="invalid-feedback d-block">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                    
                    <!-- معاينة الصورة الجديدة -->
                    <div id="image-preview" class="mt-2" style="display: none;">
                        <label>معاينة الصورة الجديدة:</label><br>
                        <img id="preview-img" src="" alt="معاينة الصورة" 
                             style="max-width: 200px; max-height: 200px; object-fit: cover; border: 1px solid #ddd; border-radius: 4px;">
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- الوصف -->
                <div class="col-md-12">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="4" 
                              placeholder="أدخل وصف الجهاز (اختياري)">{{ old('description', $device->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>
        </div>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // معاينة الصورة الجديدة قبل الرفع
    const imageInput = document.getElementById('device_image');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');

    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // فحص نوع الملف
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!validTypes.includes(file.type)) {
                    alert('نوع الملف غير مدعوم. يرجى اختيار صورة بصيغة JPEG أو PNG');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // فحص حجم الملف (20MB)
                const maxSize = 20 * 1024 * 1024;
                if (file.size > maxSize) {
                    alert('حجم الملف يجب ألا يتجاوز 20MB');
                    this.value = '';
                    imagePreview.style.display = 'none';
                    return;
                }

                // عرض معاينة الصورة
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                imagePreview.style.display = 'none';
            }
        });
    }

    // إخفاء رسائل النجاح تلقائياً بعد 5 ثوان
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.display = 'none';
        }, 5000);
    }
});
</script>

<style>
/* تحسين مظهر رسائل الخطأ */
.invalid-feedback {
    display: block !important;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 0.875em;
    color: #dc3545;
}

.form-control.is-invalid {
    border-color: #dc3545;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
}

.alert {
    border: none;
    border-radius: 8px;
    padding: 1rem 1.25rem;
    margin-bottom: 1rem;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
    border-left: 4px solid #dc3545;
}

.alert-success {
    background-color: #d1edff;
    color: #0c5460;
    border-left: 4px solid #28a745;
}

.current-image {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 4px;
}
</style>

@endsection
