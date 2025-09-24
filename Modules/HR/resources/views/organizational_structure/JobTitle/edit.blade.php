@extends('master')

@section('title')
تعديل مسمى وظيفي
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل مسمى وظيفي</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تعديل
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
            <form class="form-horizontal" action="{{ route('JobTitles.update', $title->id) }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="" class="btn btn-outline-danger">
                                    <i class="fa fa-ban"></i>الغاء
                                </a>
                                <button type="submit" class="btn btn-outline-primary">
                                    <i class="fa fa-save"></i>حفظ
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <h4 class="card-title">معلومات  المستوى  الوظيفي </h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">

                                <div class="form-group col-md-6">
                                    <label for="">الاسم <span style="color: red">*</span></label>
                                    <input type="text" id="feedback2" class="form-control" placeholder="الاسم" name="name" value="{{ old('name', $title->name) }}">
                                    @error('name')
                                    <small class="text-danger" id="basic-default-name-error" class="error">
                                        {{ $message }}
                                    </small>
                                    @enderror
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">الحالة <span style="color: red">*</span></label>
                                    <select class="form-control" id="basicSelect" name="status">
                                        <option value="0" {{ old('status', $title->status) == 0 ? 'selected' : '' }}>نشط</option>
                                        <option value="1" {{ old('status', $title->status) == 1 ? 'selected' : '' }}>غير نشط</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">القسم</label>
                                    <select class="form-control" id="basicSelect" name="department_id">
                                        <option value="">اختر قسم </option>
                                        @foreach ($departments as $department)
                                            <option value="{{ $department->id }}" {{ old('department_id', $title->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="feedback2" class="sr-only">الوصف </label>
                                    <textarea id="feedback2" class="form-control" rows="2" placeholder="الوصف" name="description">{{ old('description', $title->description) }}</textarea>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>

            </form>
        </div>
    </div>
@endsection
