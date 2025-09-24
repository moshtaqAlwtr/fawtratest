@extends('master')

@section('title')
    بنود الراتب
@stop

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">بنود الراتب</h2>
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

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">


                    <div class="d-flex align-items-center gap-3">
                        <div class="btn-group">
                            <button class="btn btn-light border">
                                <i class="fa fa-chevron-right"></i>
                            </button>
                            <button class="btn btn-light border">
                                <i class="fa fa-chevron-left"></i>
                            </button>
                        </div>
                        <span class="mx-2">1 - 1 من 1</span>
                        <div class="input-group" style="width: 150px">
                            <input type="text" class="form-control text-center" value="صفحة 1 من 1">
                        </div>


                    </div>
                    <div class="d-flex" style="gap: 15px">
                        <a href="{{ route('SalaryItems.create') }}" class="btn btn-success">
                            <i class="fa fa-plus me-2"></i>
                            أضف بنود الراتب
                        </a>

                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">بحث</h4>
                </div>

                <div class="card-body">
                    <form action="{{ route('SalaryItems.index') }}" method="GET" class="form">
                        <div class="form-body row">
                            <div class="form-group col-md-8">
                                <label for="name">البحث بواسطة اسم البند</label>
                                <input type="text" id="name" class="form-control"
                                    placeholder="البحث بواسطة اسم البند" name="name" value="{{ request('name') }}">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="status">جميع الحالات</label>
                                <select id="status" name="status" class="form-control">
                                    <option value="">اختر الحالة</option>
                                    <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>نشط</option>
                                    <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>غير نشط</option>
                                </select>
                            </div>
                            <div class="form-group col-md-4">
                                <label for="type">كل الانواع</label>
                                <select id="type" name="type" class="form-control">
                                    <option value="">اختر النوع</option>
                                    <option value="1" {{ request('type') == '1' ? 'selected' : '' }}>مستحق</option>
                                    <option value="2" {{ request('type') == '2' ? 'selected' : '' }}>مستقطع</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>


                            <a href="{{ route('SalaryItems.index') }}" type="reset" class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر
                            </a>
                        </div>
                    </form>

                </div>

            </div>

        </div>

        @if (isset($salaryItems) && $salaryItems->count() > 0)


            <div class="card">
                <div class="card-body">

                    <table class="table">

                        <thead>
                            <tr>

                                <th>اسم </th>
                                <th> النوع</th>

                                <th> الحالة</th>
                                <th style="width: 10%">الترتيب</th>
                            </tr>
                        </thead>
                        @foreach ($salaryItems as $salaryItem)
                            <tbody>

                                <tr>

                                    <td>{{ $salaryItem->name }}</td>
                                    <td>
                                        @if ($salaryItem->type == 1)
                                            <span class="badge badge-success">مستحق</span>
                                        @else
                                            <span class="badge badge-danger">مستقطع</span>
                                        @endif
                                    </td>


                                    <td>
                                        @if ($salaryItem->status == 1)
                                            <span class="badge badge-success">نشط</span>
                                        @else
                                            <span class="badge badge-danger">غير نشط</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <div class="dropdown">
                                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                    type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                    aria-haspopup="true"aria-expanded="false"></button>
                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('SalaryItems.show', $salaryItem->id) }}">
                                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item"
                                                            href="{{ route('SalaryItems.edit', $salaryItem->id) }}">
                                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                        </a>
                                                    </li>

                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#"
                                                            data-toggle="modal"
                                                            data-target="#modal_DELETE{{ $salaryItem->id }}">
                                                            <i class="fa fa-trash"></i> حذف
                                                        </a>
                                                    </li>
                                                </div>
                                            </div>
                                        </div>
                                    </td>


                                    <!-- Modal Delete -->
                                    <div class="modal fade" id="modal_DELETE{{ $salaryItem->id }}" tabindex="-1"
                                        role="dialog" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header bg-danger">
                                                    <h5 class="modal-title text-white">تأكيد الحذف</h5>
                                                    <button type="button" class="close" data-dismiss="modal"
                                                        aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <form action="{{ route('SalaryItems.destroy', $salaryItem->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('DELETE')
                                                    <div class="modal-body">
                                                        <p>هل أنت متأكد من حذف بند الراتب
                                                            "{{ $salaryItem->name }}"؟</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-danger">تأكيد
                                                            الحذف</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                </tr>

                            </tbody>
                        @endforeach
                    </table>
                @else
                    <div class="alert alert-danger text-xl-center" role="alert">
                        <p class="mb-0">
                            لا توجد بنود الراتب !!
                        </p>
                    </div>
        @endif

    </div>
    </div>




@endsection
