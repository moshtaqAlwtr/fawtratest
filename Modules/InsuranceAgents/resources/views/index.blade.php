@extends('master')

@section('title')
وكلاء التأمين
@stop

@section('content')
<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">وكلاء التأمين</h2>
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
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                    <div>
                        <a href="{{ route('Insurance_Agents.create') }}" class="btn btn-outline-success">
                            <i class="fa fa-plus me-2"></i>أضف شركة تأمين
                        </a>
                    </div>
                </div>
            </div>
        </div>
<div class="card">
    <div class="card-body">
        <form action="{{ route('Insurance_Agents.index') }}" method="GET">
            <!-- الحقلين في السطر الأول -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="agent-name">اسم الوكيل</label>
                    <input type="text" id="agent-name" name="agent-name" class="form-control" placeholder="اسم الشركة">
                </div>
                <div class="col-md-6">
                    <label for="status">الحالة</label>
                    <div class="d-flex align-items-center">
                        <select id="status" name="status" class="form-control">
                            <option value="">أي</option>
                            <option value="active">نشط</option>
                            <option value="inactive">غير نشط</option>
                        </select>
                    </div>
                </div>
            </div>
            <!-- الأزرار في السطر الثاني -->
            <div class="row">
                <div class="col-md-12 text-right">
                    <button type="submit" class="btn btn-primary mr-2">بحث</button>
                    <a href="{{ route('Insurance_Agents.index') }}" class="btn btn-outline-warning">إلغاء</a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- جدول النتائج -->
<!-- جدول النتائج -->
<div class="card p-3">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>أسم الشركة</th>
                <th>الموقع</th>
                <th>عدد الفئات</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($insuranceAgents as $agent)
            <tr>
                <td>{{ $agent->name }}</td>
                <td>{{ $agent->location }}</td>
                <td>
                    <span class="badge badge-info">
                        {{ $agent->categories_count ?? 0 }}
                    </span>
                </td>
                <td>
                    @if($agent->status == 1)
                        <span class="badge badge-success">نشط</span>
                    @else
                        <span class="badge badge-secondary">غير نشط</span>
                    @endif
                </td>
                <td>
                    <div class="btn-group">
                        <div class="dropdown">
                            <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                id="dropdownMenuButton{{ $agent->id }}" data-toggle="dropdown" aria-haspopup="true"
                                aria-expanded="false"></button>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $agent->id }}">
                                <li>
                                    <a class="dropdown-item" href="{{ route('Insurance_Agents.show', $agent->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item" href="{{ route('Insurance_Agents.edit', $agent->id) }}">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                </li>

                                <li>
                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $agent->id }}">
                                        <i class="fa fa-trash me-2"></i>حذف
                                    </a>
                                </li>


                            </div>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Modal للحذف -->
            <div class="modal fade" id="modal_DELETE{{ $agent->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $agent->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h4 class="modal-title" id="deleteModalLabel{{ $agent->id }}">حذف وكيل التأمين</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>هل أنت متأكد من حذف وكيل التأمين: <strong>{{ $agent->name }}</strong>؟</p>
                            @if($agent->categories_count > 0)
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning"></i>
                                    تحذير: هذا الوكيل مرتبط بـ {{ $agent->categories_count }} فئة/فئات
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">إلغاء</button>
                            <form action="{{ route('Insurance_Agents.destroy', $agent->id) }}" method="POST" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger waves-effect waves-light">تأكيد الحذف</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            @empty
            <tr>
                <td colspan="5" class="text-center">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle"></i>
                        لا توجد وكلاء تأمين متاحين
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
{{--
    <!-- إضافة Pagination إذا كنت تريد تقسيم النتائج -->
    @if(method_exists($insuranceAgents, 'links'))
        <div class="d-flex justify-content-center">
            {{ $insuranceAgents->links() }}
        </div>
    @endif --}}
</div>
@endsection
