@extends('master')

@section('title')
   تحديث الضرائب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> تحديث الضرائب </h2>
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

    @include('layouts.alerts.success')

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

    <form action="{{ route('TaxSitting.updateAll') }}" method="POST">
        @csrf
        

        <div class="card">
            <div class="card-body">
               
                <!--<div>-->
                <!--    <a href="" class="btn btn-outline-danger">-->
                <!--        <i class="fa fa-ban"></i> إلغاء-->
                <!--    </a>-->
                <!--</div>-->
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa fa-save"></i> تحديث 
                </button>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div id="tax-container">
                @foreach ($tax as $t)
    <div class="row mb-3 tax-row">
        <input type="hidden" name="id[]" value="{{ $t->id }}">
        
        <div class="col-md-4">
            <label class="form-label">اسم الضريبة <span style="color: red">*</span></label>
            <input type="text" name="name[]" class="form-control" 
                value="{{ old('name', $t->name) }}" required>
        </div>

        <div class="col-md-4">
            <label class="form-label">نسبة الضريبة <span style="color: red">*</span></label>
            <input type="number" name="tax[]" class="form-control" step="0.01"
                value="{{ old('tax', $t->tax) }}" required>
        </div>

        <div class="col-md-3">
            <label class="form-label">نوع الضريبة <span style="color: red">*</span></label>
            <select name="type[]" class="form-control" required>
                <option value="excluded" {{ old('type', $t->type) == 'excluded' ? 'selected' : '' }}>غير متضمن</option>
                <option value="included" {{ old('type', $t->type) == 'included' ? 'selected' : '' }}>متضمن</option>
            </select>
        </div>

        <div class="col-md-1 d-flex align-items-end">
            @if($t->id)
                <button type="button" class="btn btn-danger delete-tax" data-id="{{ $t->id }}">
                    X
                </button>
            @else
                <button type="button" class="btn btn-danger remove-row">X</button>
            @endif
        </div>
    </div>
@endforeach
                </div>

                <!-- زر لإضافة ضريبة جديدة -->
                <button type="button" class="btn btn-primary mt-3" id="add-tax">إضافة ضريبة جديدة</button>
            </div>
        </div>
    </form>

@endsection

<!-- Script لإضافة وإزالة الضرائب -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    // إضافة ضريبة جديدة
    $('#add-tax').click(function () {
        let newRow = `
        <div class="row mb-3 tax-row">
            <input type="hidden" name="id[]" value="">
            <div class="col-md-4">
                <input type="text" name="name[]" class="form-control" placeholder="اسم الضريبة" required>
            </div>

            <div class="col-md-4">
                <input type="number" name="tax[]" class="form-control" placeholder="نسبة الضريبة" step="0.01" required>
            </div>

            <div class="col-md-3">
                <select name="type[]" class="form-control" required>
                    <option value="excluded">غير متضمن</option>
                    <option value="included">متضمن</option>
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-danger remove-row">X</button>
            </div>
        </div>`;
        $('#tax-container').append(newRow);
    });

    // حذف صف جديد لم يحفظ بعد
    $(document).on('click', '.remove-row', function () {
        $(this).closest('.tax-row').remove();
    });

    // حذف ضريبة موجودة في قاعدة البيانات
    $(document).on('click', '.delete-tax', function () {
        let taxId = $(this).data('id');
        let row = $(this).closest('.tax-row');
        
        if (confirm('هل أنت متأكد من حذف هذه الضريبة؟')) {
            $.ajax({
                url: '/Sitting/TaxSitting/tax-sittings/' + taxId,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        row.remove();
                        alert('تم حذف الضريبة بنجاح');
                    }
                },
                error: function(xhr) {
                    alert('حدث خطأ أثناء الحذف: ' + xhr.responseText);
                }
            });
        }
    });
});
</script>
