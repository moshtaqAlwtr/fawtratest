@extends('master')

@section('title')
    الاصول
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">الاصول </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
               
               

                <!-- الجزء الخاص بالتصفح -->
               <div class="d-flex justify-content-center mt-3">
    {{ $assets->appends(request()->query())->links() }}
</div>


                <!-- قائمة الإجراءات -->

                <a href="{{ route('Assets.create') }}" class="btn btn-success btn-sm d-flex align-items-center ">
                    <i class="fa fa-plus me-2"></i> اصل جديد
                </a>

             

            </div>
        </div>
    </div>
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
        <div class="card-content">
            <div class="card-body">
                <h4 class="card-title">بحث</h4>
            </div>

            <div class="card-body">
              <form class="form" method="GET" action="{{ route('Assets.index') }}">
    <div class="form-body row">

        <!-- الاسم -->
        <div class="form-group col-md-4 mb-2">
            <label for="name">اسم الأصل</label>
            <input type="text" id="name" class="form-control" placeholder="اسم الأصل" name="name" value="{{ request('name') }}">
        </div>

        <!-- الكود -->
        <div class="form-group col-md-4 mb-2">
            <label for="code">كود الأصل</label>
            <input type="text" id="code" class="form-control" placeholder="كود الأصل" name="code" value="{{ request('code') }}">
        </div>

        <!-- الموظف -->
        <div class="form-group col-md-4 mb-2">
            <label for="employee_id">الموظف المسؤول</label>
            <select name="employee_id" id="employee_id" class="form-control">
                <option value="">-- اختر موظف --</option>
                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}" {{ request('employee_id') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name ?? '' }}
                    </option>
                @endforeach
            </select>
        </div>

        <!-- الحالة -->
        <div class="form-group col-md-4 mb-2">
            <label for="status">حالة الأصل</label>
            <select name="status" id="status" class="form-control">
                <option value="">-- اختر الحالة --</option>
                <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>في الخدمة</option>
                <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>غير نشط</option>
                <option value="2" {{ request('status') == '2' ? 'selected' : '' }}>مهلك</option>
                <option value="3" {{ request('status') == '3' ? 'selected' : '' }}>تم بيعه</option>
            </select>
        </div>

        <!-- من تاريخ -->
        <div class="form-group col-md-4 mb-2">
            <label for="from_date_2">من تاريخ</label>
            <input type="date" id="from_date_2" class="form-control" name="from_date_2" value="{{ request('from_date_2') }}">
        </div>

        <!-- إلى تاريخ -->
        <div class="form-group col-md-4 mb-2">
            <label for="to_date_2">إلى تاريخ</label>
            <input type="date" id="to_date_2" class="form-control" name="to_date_2" value="{{ request('to_date_2') }}">
        </div>

        <!-- أزرار البحث والإلغاء -->
        <div class="form-group col-md-12 mt-2 d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary waves-effect waves-light me-2">
                <i class="fa fa-search me-1"></i> بحث
            </button>
           <a href="{{ route('Assets.index') }}" class="btn btn-outline-warning waves-effect waves-light">
    <i class="fa fa-undo me-1"></i> إلغاء
</a>

        </div>
    </div>
</form>


            </div>



        </div>

        <!--end delete-->




    </div>
    <div class="card">

        <div class="card-body">
            @if($assets->count() > 0)
                @foreach($assets as $asset)
                    <div class="row border-bottom py-2 align-items-center">
                        <div class="col-md-2">
                            @if($asset->attachments)
                                <img src="{{ asset('storage/' . $asset->attachments) }}" alt="صورة الأصل" class="img-thumbnail" width="100">
                            @else
                                <img src="{{ asset('assets/images/no-image.jpg') }}" alt="لا توجد صورة" class="img-thumbnail" width="100">
                            @endif
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0">
                                <strong>{{ $asset->name }}</strong>
                            </p>
                            <small class="text-muted">
                                كود: {{ $asset->code }}
                            </small>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0">القيمة: {{ number_format($asset->purchase_value, 2) }}</p>
                            <small class="text-muted">
                                بواسطة: {{ $asset->employee ? $asset->employee->full_name : 'غير محدد' }}
                            </small>
                        </div>
                        <div class="col-md-2 text-center">
                            <strong class="text-danger">{{ number_format($asset->depreciation ? $asset->depreciation->book_value : 0, 2) }}</strong>
                            <span class="badge bg-info d-block mt-1">
                                @if($asset->depreciation)
                                    @switch($asset->depreciation->dep_method)
                                        @case(1)
                                            القسط الثابت
                                            @break
                                        @case(2)
                                            القسط المتناقص
                                            @break
                                        @case(3)
                                            وحدات الإنتاج
                                            @break
                                        @case(4)
                                            بدون إهلاك
                                            @break
                                        @default
                                            بدون إهلاك
                                    @endswitch
                                @else
                                    بدون إهلاك
                                @endif
                            </span>
                        </div>
                        <div class="col-md-2 text-end">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('Assets.edit', $asset->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <a class="dropdown-item" href="{{ route('Assets.show', $asset->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <form action="{{ route('Assets.destroy', $asset->id) }}" method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="dropdown-item text-danger">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="mt-3">
                   {{ $assets->appends(request()->query())->links() }}

                </div>
            @else
                <div class="alert alert-warning" role="alert">
                    <p class="mb-0">لا توجد أصول</p>
                </div>
            @endif
        </div>
    </div>




    <!-- Modal delete -->


@endsection
