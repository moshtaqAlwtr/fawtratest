@extends('master')

@section('title')
أضف وكيل تأمين
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">أضف وكيل تأمين</h2>
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
    @include('layouts.alerts.error')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('Insurance_Agents.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
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
                        <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                            <div></div>
                            <div>
                                <button type="submit" class="btn btn-outline-danger">
                                    <i class="fa fa-plus me-2"></i>الغاء
                                </button>
                                <button type="submit" class="btn btn-outline-success">
                                    <i class="fa fa-plus me-2"></i>حفظ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-body">
                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="name" class=""> الاسم </label>
                                <input type="text" id="name" class="form-control" placeholder="" name="name">
                            </div>
                            <div class="form-group col-6">
                                <label for="attachments">المرفقات</label>
                                <input type="file" name="attachments" id="attachments" class="d-none">
                                <div class="upload-area border rounded p-3 text-center position-relative"
                                    onclick="document.getElementById('attachments').click()">
                                    <div class="d-flex align-items-center justify-content-center gap-2">
                                        <i class="fas fa-cloud-upload-alt text-primary"></i>
                                        <span class="text-primary">اضغط هنا</span>
                                        <span>أو</span>
                                        <span class="text-primary">اختر من جهازك</span>
                                    </div>
                                    <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                        <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="phone" class=""> الهاتف </label>
                                <input type="text" id="phone" class="form-control" placeholder="" name="phone">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="email" class=""> الايميل  </label>
                                <input type="text" id="email" class="form-control" placeholder="" name="email">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="location" class=""> الموقع  </label>
                                <input type="text" id="location" class="form-control" placeholder="" name="location">
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="status" class="">الحالة <span class="text-danger">*</span></label>
                                <select name="status" class="form-control" id="status">
                                    <option value="1">نشط</option>
                                    <option value="2">غير نشط</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection











