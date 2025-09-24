@extends('master')

@section('title')
    أضف الفترة المالية
@stop

@section('css')
    <style>
        .financial-form .card-header {
            background-color: #007bff;
            color: #fff;
            padding: 1rem;
        }

        .financial-form .form-label {
            font-weight: bold;
        }

        .financial-form .form-group {
            margin-bottom: 1rem;
        }

        .financial-form .form-actions {
            display: flex;
            justify-content: flex-start;
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .btn-save {
            background-color: #28a745;
            color: white;
        }

        .btn-cancel {
            background-color: #6c757d;
            color: white;
        }
    </style>
@endsection

@section('content')
    <div class="container mt-4 financial-form">
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0">تفاصيل الفترة المالية</h4>
            </div>

            <form action="" method="POST">
                @csrf
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <label for="start_date" class="form-label">تاريخ البدء <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" id="start_date" class="form-control"
                                value="{{ old('start_date', date('Y-m-d')) }}" required>
                        </div>

                        <div class="col-md-4">
                            <label for="period" class="form-label">الفترة <span class="text-danger">*</span></label>
                            <select name="period" id="period" class="form-control" required>
                                <option value="12" {{ old('period') == 12 ? 'selected' : '' }}>1 سنة</option>
                                <option value="6" {{ old('period') == 6 ? 'selected' : '' }}>6 أشهر</option>
                                <option value="3" {{ old('period') == 3 ? 'selected' : '' }}>3 أشهر</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="end_date" class="form-label">تاريخ الانتهاء <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" id="end_date" class="form-control"
                                value="{{ old('end_date') }}" required>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-12">
                            <label for="description" class="form-label">الوصف</label>
                            <textarea name="description" id="description" rows="2" class="form-control">{{ old('description') }}</textarea>
                        </div>
                    </div>

                    <div class="form-actions mt-4">
                        <button type="submit" class="btn btn-save">
                            <i class="fa fa-save me-1"></i> حفظ
                        </button>
                        <a href="" class="btn btn-cancel">
                            <i class="fa fa-times me-1"></i> إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
