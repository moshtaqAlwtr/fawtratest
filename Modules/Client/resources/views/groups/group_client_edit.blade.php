@extends('master')

@section('title')
    تعديل مجموعة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل مجموعة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('groups.group_client_update', $regionGroup->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- لإرسال الطلب كـ PUT لتحديث البيانات --}}

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

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> إلغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> تحديث
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <!-- الحقول -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">المجموعة <span style="color: red">*</span></label>
                        <input type="text" id="name" name="name" class="form-control" placeholder="المجموعة"
                            value="{{ old('name', $regionGroup->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="branch_id" class="form-label">اختر الفرع <span style="color: red">*</span></label>
                        <select name="branch_id" id="branch_id" class="form-control select2">
                            @foreach ($branches as $branch)
                                <option value="{{ $branch->id }}"
                                    {{ $branch->id == $regionGroup->branch_id ? 'selected' : '' }}>
                                    {{ $branch->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4">
                        <label for="gender">الاتجاة المسموح الدخول بها</label>
                        <select name="directions_id" id="directions_id	" class="form-control">
                            @foreach ($directions as $direction)
                                <option value="{{ $direction->id }}">{{ $direction->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </form>
    </div>
@endsection
