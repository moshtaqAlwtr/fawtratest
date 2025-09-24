@extends('master')

@section('title')
عرض خيار الشحن
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">{{ $shippingOption->name }}</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                            <li class="breadcrumb-item active">
                                @if ($shippingOption->status == 1)
                                    <div class="badge badge-pill badge badge-success">نشط</div>
                                @else
                                    <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                @endif
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-title p-2">
            <a href="#" class="btn btn-outline-danger btn-sm waves-effect waves-light" data-toggle="modal"
                data-target="#modal_DELETE1">حذف <i class="fa fa-trash"></i></a>
            <a href="{{ route('shippingOptions.edit', $shippingOption->id) }}"
                class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
            <a href="{{ route('shippingOptions.updateStatus', $shippingOption->id) }}"
                class="btn btn-outline-{{ $shippingOption->status == 1 ? 'danger' : 'success' }} btn-sm waves-effect waves-light">
                {{ $shippingOption->status == 1 ? 'تعطيل' : 'تفعيل' }}
                <i class="fa {{ $shippingOption->status == 1 ? 'fa-ban' : 'fa-check' }}"></i>
            </a>
        </div>
        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                        aria-controls="details" aria-selected="true">تفاصيل خيار الشحن</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log" role="tab"
                        aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                </li>
            </ul>

            <div class="tab-content">
                <!-- تفاصيل خيار الشحن -->
                <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                    <div class="tab-content">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <table class="table">
                                        <thead style="background: #f8f8f8">
                                            <tr>
                                                <th colspan="4">معلومات خيار الشحن</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td><small>الاسم</small></td>
                                                <td>{{ $shippingOption->name }}</td>
                                                <td><small>الحالة</small></td>
                                                <td>
                                                    @if ($shippingOption->status == 1)
                                                        <div class="badge badge-pill badge badge-success">نشط</div>
                                                    @else
                                                        <div class="badge badge-pill badge badge-danger">غير نشط</div>
                                                    @endif
                                                </td>
                                            </tr>
                                            <tr>
                                                <td><small>التكلفة</small></td>
                                                <td>{{ $shippingOption->cost }}</td>
                                                <td><small>الضريبة</small></td>
                                                <td>{{ $shippingOption->tax }}%</td>
                                            </tr>
                                            <tr>
                                                <td><small>الحساب الافتراضي</small></td>
                                                <td colspan="3">{{ optional($shippingOption->defaultAccount)->name ?? 'غير محدد' }}</td>
                                            </tr>
                                            <tr>
                                                <td><small>ترتيب العرض</small></td>
                                                <td>{{ $shippingOption->display_order }}</td>
                                                <td><small>تاريخ الإنشاء</small></td>
                                                <td>{{ $shippingOption->created_at->format('Y-m-d') }}</td>
                                            </tr>
                                            @if($shippingOption->description)
                                            <tr>
                                                <td><small>الوصف</small></td>
                                                <td colspan="3">{{ $shippingOption->description }}</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سجل النشاطات -->
                <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                    <div class="position-relative">
                        <div class="position-absolute end-0 me-4">
                            <button class="btn btn-light btn-sm rounded-pill shadow-sm">اليوم</button>
                        </div>
                        <div class="card" style="background: #f8f9fa">
                            <div class="activity-list p-4">
                                <div class="card-content">
                                    <div class="card border-0">
                                        <ul class="activity-timeline timeline-left list-unstyled">
                                            <li class="d-flex position-relative mb-4">
                                                <div class="timeline-icon position-absolute"
                                                    style="left: -43px; top: 0;">
                                                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center"
                                                        style="width: 35px; height: 35px;">
                                                        <i class="fas fa-plus text-white"></i>
                                                    </div>
                                                </div>
                                                <div class="timeline-info position-relative"
                                                    style="padding-left: 20px; border-left: 2px solid #e9ecef;">
                                                    <div class="d-flex align-items-center mb-1">
                                                        <span class="badge bg-purple me-2">النظام</span>
                                                        <span class="text-dark">تم إنشاء خيار الشحن</span>
                                                    </div>
                                                    <div class="d-flex align-items-center mt-2">
                                                        <small class="text-muted me-3">
                                                            <i class="fas fa-calendar me-1"></i>
                                                            {{ $shippingOption->created_at->format('Y-m-d') }}
                                                        </small>
                                                        <small class="text-muted">
                                                            <i class="far fa-clock me-1"></i>
                                                            {{ $shippingOption->created_at->format('H:i:s') }}
                                                        </small>
                                                    </div>
                                                </div>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Delete -->
    <div class="modal fade" id="modal_DELETE1" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">تأكيد الحذف</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    هل أنت متأكد من حذف خيار الشحن؟
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <form action="{{ route('shippingOptions.destroy', $shippingOption->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">حذف</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
