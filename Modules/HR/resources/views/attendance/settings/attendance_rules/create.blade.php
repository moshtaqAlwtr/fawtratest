@extends('master')

@section('title', 'إضافة قواعد الحضور')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إضافة قواعد الحضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance-rules.index') }}">قواعد الحضور</a></li>
                            <li class="breadcrumb-item active">إضافة</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Form Actions -->
    <div class="card mb-3">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                </div>
                <div>
                    <a href="{{ route('attendance-rules.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                    <button type="submit" form="createForm" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="cart mt-5">
        <div class="card">
            <div class="card-header text-center">
                <h4>قواعد الحضور</h4>
            </div>
            <div class="card-body">
                <form id="createForm" action="{{ route('attendance-rules.store') }}" method="POST">
                    @csrf

                    <!-- الصف الأول: الاسم واللون -->
                    <div class="row mb-3">
                        <!-- الاسم -->
                        <div class="col-md-6">
                            <label for="name" class="form-label">الاسم: <span style="color: red">*</span></label>
                            <input type="text"
                                   class="form-control @error('name') is-invalid @enderror"
                                   id="name"
                                   name="name"
                                   value="{{ old('name') }}"
                                   placeholder="أدخل الاسم"
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- اللون -->
                        <div class="col-md-6">
                            <label for="color" class="form-label">اللون:</label>
                            <div class="d-flex align-items-center">
                                <input type="color"
                                       class="form-control form-control-color @error('color') is-invalid @enderror"
                                       id="color"
                                       name="color"
                                       value="{{ old('color', '#4e5381') }}"
                                       title="اختر اللون"
                                       style="max-width: 50px; margin-left: 10px;">
                                <input type="text"
                                       class="form-control"
                                       id="colorDisplay"
                                       value="{{ old('color', '#4e5381') }}"
                                       readonly>
                            </div>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- الصف الثاني: الحالة والوردية -->
                    <div class="row mb-3">
                        <!-- الحالة -->
                        <div class="col-md-6">
                            <label for="status" class="form-label">الحالة: <span style="color: red">*</span></label>
                            <select class="form-control select2 @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                <option value="active" {{ old('status', 'active') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                            @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوردية -->
                        <div class="col-md-6">
                            <label for="shift" class="form-label">الوردية: <span style="color: red">*</span></label>
                            <select class="form-control select2 @error('shift_id') is-invalid @enderror"
                                    id="shift"
                                    name="shift_id"
                                    required>
                                <option value="">اختر الوردية</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift->id }}"
                                            {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shift_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- الصف الثالث: الوصف (عرض كامل) -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="description" class="form-label">الوصف:</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description"
                                      name="description"
                                      rows="3"
                                      placeholder="أدخل الوصف">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- الصف الرابع: الصيغة الحسابية والشرط -->
                    <div class="row mb-3">
                        <!-- الصيغة الحسابية -->
                        <div class="col-md-6">
                            <label for="formula" class="form-label">الصيغة الحسابية:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button">🔍</button>
                                <input type="text"
                                       class="form-control @error('formula') is-invalid @enderror"
                                       id="formula"
                                       name="formula"
                                       value="{{ old('formula') }}"
                                       placeholder="أدخل الصيغة الحسابية">
                            </div>
                            @error('formula')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الشرط -->
                        <div class="col-md-6">
                            <label for="condition" class="form-label">الشرط:</label>
                            <div class="input-group">
                                <button class="btn btn-outline-secondary" type="button">🔍</button>
                                <input type="text"
                                       class="form-control @error('condition') is-invalid @enderror"
                                       id="condition"
                                       name="condition"
                                       value="{{ old('condition') }}"
                                       placeholder="أدخل الشرط">
                            </div>
                            @error('condition')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
$(document).ready(function() {
    // Initialize Select2
    $('.select2').select2({
        dir: 'rtl',
        language: 'ar'
    });

    // Color picker functionality
    $('#color').on('change', function() {
        $('#colorDisplay').val($(this).val());
    });

    // Form submission with SweetAlert
    $('#createForm').on('submit', function(e) {
        e.preventDefault();

        // Validate required fields
        let isValid = true;
        const requiredFields = ['name', 'status', 'shift_id'];

        requiredFields.forEach(function(field) {
            const fieldElement = document.getElementById(field === 'shift_id' ? 'shift' : field);
            if (!fieldElement.value.trim()) {
                isValid = false;
                fieldElement.classList.add('is-invalid');
            } else {
                fieldElement.classList.remove('is-invalid');
            }
        });

        if (!isValid) {
            Swal.fire({
                icon: 'error',
                title: 'خطأ!',
                text: 'يرجى ملء جميع الحقول المطلوبة',
                confirmButtonText: 'موافق'
            });
            return;
        }

        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: 'سيتم إضافة قاعدة حضور جديدة',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احفظ',
            cancelButtonText: 'إلغاء',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: 'جاري الحفظ...',
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                // Submit form
                this.submit();
            }
        });
    });

    // Display success message if exists
    @if(session('success'))
        Swal.fire({
            icon: 'success',
            title: 'تم بنجاح!',
            text: '{{ session('success') }}',
            confirmButtonText: 'موافق'
        });
    @endif

    // Display error message if exists
    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'خطأ!',
            text: '{{ session('error') }}',
            confirmButtonText: 'موافق'
        });
    @endif

    // Display validation errors
    @if($errors->any())
        let errorMessages = '';
        @foreach($errors->all() as $error)
            errorMessages += '• {{ $error }}\n';
        @endforeach

        Swal.fire({
            icon: 'error',
            title: 'خطأ في البيانات المدخلة',
            text: errorMessages,
            confirmButtonText: 'موافق'
        });
    @endif
});
</script>
@endpush