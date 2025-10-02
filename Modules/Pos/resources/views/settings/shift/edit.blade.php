
@extends('master')

@section('title')
تعديل الوردية - {{ $shift->name }}
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">تعديل الوردية: {{ $shift->name }}</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('pos.settings.shift.index') }}">الورديات</a></li>
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

<form action="{{ url('/POS/Shift/update/' . $shift->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>

                <div>
                    <a href="{{ route('pos.settings.shift.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
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
                    <label for="name" class="form-label">
                        اسم الوردية <span style="color: red">*</span>
                    </label>
                    <input type="text" 
                           id="name" 
                           name="name" 
                           class="form-control @error('name') is-invalid @enderror" 
                           placeholder="أدخل اسم الوردية"
                           value="{{ old('name', $shift->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- التصنيف الرئيسي -->
                <div class="col-md-6">
                    <label for="parent_id" class="form-label">
                        التصنيف الرئيسي
                    </label>
                    <select id="parent_id" 
                            name="parent_id" 
                            class="form-control @error('parent_id') is-invalid @enderror">
                        <option value="">وردية رئيسية</option>
                        @if(isset($parentShifts))
                            @foreach($parentShifts as $parentShift)
                                <option value="{{ $parentShift->id }}" 
                                        {{ (old('parent_id', $shift->parent_id) == $parentShift->id) ? 'selected' : '' }}>
                                    {{ $parentShift->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                    @error('parent_id')
                        <div class="invalid-feedback">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>
            </div>

            <div class="row mb-3">
                <!-- المرفقات -->
                <div class="col-md-6">
                    <label for="attachment" class="form-label">المرفقات</label>
                    
                    <!-- عرض المرفق الحالي -->
                    @if($shift->attachment)
                        <div class="current-attachment mb-2 p-2 border rounded">
                            <label class="form-label">المرفق الحالي:</label>
                            <div class="d-flex align-items-center">
                                <i class="fa fa-file text-primary me-2"></i>
                                <a href="{{ Storage::url($shift->attachment) }}" target="_blank" class="text-decoration-none">
                                    {{ basename($shift->attachment) }}
                                </a>
                                <a href="{{ Storage::url($shift->attachment) }}" download class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fa fa-download"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                    
                    <div class="upload-area border rounded p-4 text-center @error('attachment') is-invalid @enderror" 
                         onclick="document.getElementById('attachment').click()" 
                         style="cursor: pointer; border-style: dashed !important;">
                        <input type="file" 
                               id="attachment" 
                               name="attachment"
                               class="d-none"
                               accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <div id="upload-content">
                            <i class="fa fa-cloud-upload fa-2x text-muted mb-2"></i>
                            <p class="mb-0">اسحب الملف هنا أو انقر للاختيار</p>
                            <small class="text-muted">
                                PDF, DOC, DOCX, JPG, PNG (حتى 10MB)<br>
                                <small>اتركه فارغاً للاحتفاظ بالمرفق الحالي</small>
                            </small>
                        </div>
                        <div id="file-info" style="display: none;">
                            <i class="fa fa-file text-primary fa-2x mb-2"></i>
                            <p class="mb-0" id="file-name"></p>
                            <small class="text-muted" id="file-size"></small>
                        </div>
                    </div>
                    @error('attachment')
                        <div class="invalid-feedback d-block">
                            <i class="fa fa-exclamation-circle"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- الوصف -->
                <div class="col-md-6">
                    <label for="description" class="form-label">الوصف</label>
                    <textarea id="description" 
                              name="description" 
                              class="form-control @error('description') is-invalid @enderror" 
                              rows="5" 
                              placeholder="أدخل وصف الوردية (اختياري)">{{ old('description', $shift->description) }}</textarea>
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
    const fileInput = document.getElementById('attachment');
    const uploadContent = document.getElementById('upload-content');
    const fileInfo = document.getElementById('file-info');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');

    // معالج تغيير الملف
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // فحص حجم الملف (10MB)
            const maxSize = 10 * 1024 * 1024;
            if (file.size > maxSize) {
                alert('حجم الملف يجب ألا يتجاوز 10MB');
                this.value = '';
                showUploadArea();
                return;
            }

            // عرض معلومات الملف
            fileName.textContent = file.name;
            fileSize.textContent = formatFileSize(file.size);
            showFileInfo();
        } else {
            showUploadArea();
        }
    });

    // معالج السحب والإفلات
    const uploadArea = document.querySelector('.upload-area');
    
    uploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '#f8f9fa';
    });

    uploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
    });

    uploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        this.style.backgroundColor = '';
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });

    function showFileInfo() {
        uploadContent.style.display = 'none';
        fileInfo.style.display = 'block';
    }

    function showUploadArea() {
        uploadContent.style.display = 'block';
        fileInfo.style.display = 'none';
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    // إخفاء رسائل النجاح تلقائياً
    const successAlert = document.querySelector('.alert-success');
    if (successAlert) {
        setTimeout(function() {
            successAlert.style.display = 'none';
        }, 5000);
    }
});
</script>

<style>
.upload-area {
    transition: background-color 0.3s ease;
}

.upload-area:hover {
    background-color: #f8f9fa;
}

.current-attachment {
    background-color: #f8f9fa;
}

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
</style>

@endsection