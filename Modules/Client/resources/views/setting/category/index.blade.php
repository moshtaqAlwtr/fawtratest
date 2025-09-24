@extends('master')

@section('title')
    تصنيفات العملاء
@stop

@section('css')
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }
    </style>
@stop

@section('content')

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تصنيفات العملاء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">تصنيفات العملاء</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- بطاقة الإجراءات -->
        <div class="card shadow-lg border-0 rounded-3">
            <div class="card-body">
                <div class="row align-items-center gy-3">
                    <div class="col-md-6 d-flex flex-wrap align-items-center gap-2 justify-content-center justify-content-md-start">

                    <a href="{{ route('categoriesClient.create') }}"
                        class="btn btn-success d-flex align-items-center justify-content-center"
                        style="height: 44px; padding: 0 16px; font-weight: bold; border-radius: 6px;">
                        <i class="fas fa-plus ms-2"></i>
                        أضف تصنيف
                    </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- نموذج البحث -->
        <form action="{{ route('categoriesClient.index') }}" method="GET">
            <div class="card">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="name">اسم التصنيف</label>
                            <input type="text" name="name" class="form-control" value="{{ request('name') }}"
                                placeholder="ابحث باسم التصنيف">
                        </div>
                        <div class="col-md-4">
                            <label for="status">الحالة</label>
                            <select name="active" class="form-control">
                                <option value="">-- جميع الحالات --</option>
                                <option value="1" {{ request('active') == '1' ? 'selected' : '' }}>نشط</option>
                                <option value="0" {{ request('active') == '0' ? 'selected' : '' }}>غير نشط</option>
                            </select>
                        </div>
                    </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary mr-2">
                                <i class="fa fa-search"></i> بحث
                            </button>
                            <a href="{{ route('categoriesClient.index') }}" class="btn btn-secondary">
                                <i class="fa fa-times"></i> إلغاء
                            </a>
                        </div>

                </div>
            </div>
        </form>

        <!-- جدول التصنيفات -->
        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>اسم التصنيف</th>
                                <th>الوصف</th>
                                <th>الحالة</th>
                                <th>عدد العملاء</th>
                                <th style="width: 15%">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $category->name }}</td>
                                    <td>{{ $category->description ?? 'لا يوجد وصف' }}</td>
                                    <td>
                                        @if($category->active)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>{{ $category->clients_count ?? 0 }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button" id="dropdownMenuButton{{ $category->id }}"
                                                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                </button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $category->id }}">
                                                    <a class="dropdown-item"
                                                        href="{{ route('categoriesClient.edit', $category->id) }}">
                                                        <i class="fa fa-edit me-2 text-primary"></i>تعديل
                                                    </a>
                                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal"
                                                        data-target="#deleteModal{{ $category->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Modal حذف -->
                                <div class="modal fade" id="deleteModal{{ $category->id }}" tabindex="-1" role="dialog"
                                    aria-labelledby="deleteModalLabel{{ $category->id }}" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header bg-danger text-white">
                                                <h5 class="modal-title" id="deleteModalLabel{{ $category->id }}">
                                                    تأكيد الحذف
                                                </h5>
                                                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <p>هل أنت متأكد من حذف تصنيف "{{ $category->name }}"؟</p>
                                                @if($category->clients_count > 0)
                                                    <div class="alert alert-warning">
                                                        <i class="fa fa-exclamation-triangle"></i>
                                                        هذا التصنيف يحتوي على {{ $category->clients_count }} عميل. سيتم إزالة التصنيف من هؤلاء العملاء.
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                                                <form action="{{ route('categoriesClient.destroy', $category->id) }}" method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">حذف</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- الترقيم -->

                </div>
            </div>
        </div>
    </div>
@endsection
