@extends('master')

@section('title')

    تعديل تصنيف العملاء

@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> تعديل تصنيف</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل تصنيف
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
                <form action="{{ route('categoriesClient.update', $category->id) }}" method="POST" id="categoryForm">

                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="name">اسم التصنيف <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                    id="name" name="name" value="{{ old('name', $category->name ?? '') }}"
                                    placeholder="أدخل اسم التصنيف" required>
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="active">الحالة</label>
                                <select class="form-control" id="active" name="active">
                                    <option value="1"
                                        {{ old('active', $category->active ?? 1) == 1 ? 'selected' : '' }}>نشط</option>
                                    <option value="0"
                                        {{ old('active', $category->active ?? 1) == 0 ? 'selected' : '' }}>غير نشط
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="description">الوصف</label>
                                <textarea class="form-control" id="description" name="description" rows="3"
                                    placeholder="أدخل وصفًا للتصنيف (اختياري)">{{ old('description', $category->description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="row mt-3">

                        <div class="col-12 text-right">
                            <a href="{{ route('categoriesClient.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(document).ready(function() {
            // التحقق من الصحة قبل الإرسال
            $('#categoryForm').validate({
                rules: {
                    name: {
                        required: true,
                        maxlength: 100
                    }
                },
                messages: {
                    name: {
                        required: "حقل اسم التصنيف مطلوب",
                        maxlength: "يجب ألا يتجاوز اسم التصنيف 100 حرف"
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.form-group').append(error);
                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@endsection
