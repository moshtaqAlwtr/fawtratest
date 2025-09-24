@extends('master')

@section('title')
    عرض معلومات الاستهلاك والارصدة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض معلومات الاستهلاك والارصدة</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-md bg-danger">
                        <span class="avatar-content fs-4">{{ substr($balanceConsumption->client->first_name, 0, 1) }}</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">{{ $balanceConsumption->client->first_name }} {{ $balanceConsumption->client->last_name }}</h5>
                                <small class="text-muted">#{{ $balanceConsumption->client->id }}</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>
                                    {{ $balanceConsumption->contract_type == 'duration' ? 'انتهى' : 'موقوف' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title p-2 d-flex align-items-center gap-2">
            <a href="{{ route('ManagingBalanceConsumption.edit', $balanceConsumption->id) }}"
                class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                <i class="fa fa-edit ms-1 text-info"></i> تعديل
            </a>

            <a href="{{ route('ManagingBalanceConsumption.updateStatus',$balanceConsumption->id) }}" class="btn btn-outline-{{ $balanceConsumption->status == 0 ? 'success' : 'danger' }} btn-sm waves-effect waves-light">
                {{ $balanceConsumption->status == 0 ? 'الغاء الايقاف' : 'ايقاف' }} <i class="fa {{ $balanceConsumption->status == 0 ? 'fa-ban' : 'fa-ban' }}"></i>
            </a>

            <a href="#"
                class="btn btn-outline-danger btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;" data-toggle="modal" data-target="#modal_DELETE1">
                <i class="fa fa-trash ms-1 text-danger"></i> حذف
            </a>
        </div>

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#info" role="tab">
                        <span>معلومات الاستهلاك</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب معلومات الاستهلاك -->
                <div class="tab-pane active" id="info" role="tabpanel">
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="pb-2 h-100">
                                    <div class="card h-100" style="background-color: #f8f9fa;">
                                        <div class="d-flex align-items-center gap-2 p-3">
                                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <span class="text-white fs-4">{{ substr($balanceConsumption->client->first_name, 0, 1) }}</span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold fs-5">{{ $balanceConsumption->client->first_name }} {{ $balanceConsumption->client->last_name }}</span>
                                                    <a href="" class="text-decoration-underline text-muted"
                                                        style="font-size: 0.9rem;">#{{ $balanceConsumption->client->id }}</a>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{route('clients.show', $balanceConsumption->client->id) }}"
                                                        class="btn btn-light btn-sm d-flex align-items-center gap-1 px-3">
                                                        <i class="fa fa-user"></i>
                                                        عرض الصفحة الشخصية
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="pb-2 h-100">
                                    <div class="card h-100 p-3 d-block" style="background-color: #f8f9fa;">
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="text-muted mb-1">الرصيد المستخدم:</p>
                                                <p class="lh-1">
                                                    <span class="d-block fs-22 font-weight-bold">{{ $balanceConsumption->used_balance }}</span>
                                                    <span class="fs-12">نقطة</span>
                                                </p>
                                            </div>
                                            <div class="col-12">
                                                <p class="text-muted mb-1">نوع الرصيد:</p>
                                                <p class="lh-16 mb-0 fs-14 font-weight-bold">
                                                    {{ $balanceConsumption->balanceType->name }}
                                                    <a href=""
                                                        class="font-weight-normal fs-12 text-decoration-underline ml-2"
                                                        target="_blank">#{{ $balanceConsumption->balance_type_id }}</a>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="pb-2 h-100">
                                    <div class="card h-100 p-3 d-block" style="background-color: #f8f9fa;">
                                        <div class="row">
                                            <div class="col-12">
                                                <p class="text-muted mb-1">تاريخ الاستهلاك:</p>
                                                <p class="lh-1">
                                                    <span class="d-block fs-20 font-weight-bold">{{ $balanceConsumption->consumption_date->format('d-m-Y') }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12 mt-4">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="row">
                                            <!-- الجزء الأول: الجدول -->
                                            <div class="col-md-6">
                                                <table class="table table-bordered text-center">
                                                    <thead class="thead-light">
                                                        <tr>
                                                            <th>شحن الرصيد</th>
                                                            <th>مستهلك</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <div class="d-flex justify-content-center align-items-center">
                                                                    <span>نقطة</span>
                                                                    <a href="#" class="text-primary text-decoration-underline fw-bold me-2">#1</a>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <span>{{ $balanceConsumption->used_balance }} نقطة</span>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <!-- الجزء الثاني: الوصف -->
                                            <div class="col-md-6">
                                                <div class="p-3" style="background-color: #f8f9fa; border: 1px solid #dee2e6; border-radius: 5px;">
                                                    <p class="mb-0" style="font-size: 1.1rem;">
                                                        {{ $balanceConsumption->description ?? 'لا يوجد وصف متوفر' }}
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal fade text-left" id="modal_DELETE{{ $balanceConsumption->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #EA5455 !important;">
                                            <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $balanceConsumption->name }}</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                                            <form action="{{ route('ManagingBalanceConsumption.destroy', $balanceConsumption->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger waves-effect waves-light">تأكيد</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                </div>
            </div>
        </div>
    </div>
@endsection
