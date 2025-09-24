@extends('master')

@section('css')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/progressbar.js/1.1.0/progressbar.min.js"></script>
    <style>
        .progress-circle {
            width: 200px;
            height: 200px;
            margin: 0 auto;
            position: relative;
        }

        .progress-circle-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            width: 100%;
        }

        .progress-circle svg path:first-child {
            stroke: #eee !important;
        }

        .progress-circle svg path:last-child {
            stroke: #4E5381 !important;
        }
    </style>
@endsection

@section('title')
    عرض معلومات الشحن
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض معلومات الشحن</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض </li>
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
                        <span class="avatar-content fs-4">{{ $balanceCharge->client->initial }}</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">{{ $balanceCharge->client->trade_name }}</h5>
                                <small class="text-muted">#{{ $balanceCharge->client->id }}</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                <small class="text-muted">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>
                                    @if ($balanceCharge->client->status == 1)
                                        <span class="badge bg-success">انتهى</span>
                                    @else
                                        <span class="badge bg-danger">موقوف</span>
                                    @endif
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
            <a href="{{ route('MangRechargeBalances.edit', $balanceCharge->id) }}"
                class="btn btn-outline-info btn-sm d-inline-flex align-items-center justify-content-center px-3"
                style="min-width: 90px;">
                <i class="fa fa-edit ms-1 text-info"></i> تعديل
            </a>
            <a href="{{ route('MangRechargeBalances.updateStatus',$balanceCharge->id) }}" class="btn btn-outline-{{ $balanceCharge->status == 0 ? 'success' : 'danger' }} btn-sm waves-effect waves-light">
                {{ $balanceCharge->status == 0 ? 'الغاء الايقاف' : 'ايقاف' }} <i class="fa {{ $balanceCharge->status == 0 ? 'fa-ban' : 'fa-ban' }}"></i>
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
                        <span>معلومات الشحن</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#usage" role="tab">
                        <span>استخدام الرصيد</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب معلومات الشحن -->
                <div class="tab-pane active" id="info" role="tabpanel">
                    <div class="">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="pb-2 h-100">
                                    <div class="card h-100" style="background-color: #f8f9fa;">
                                        <div class="d-flex align-items-center gap-2 p-3">
                                            <div class="bg-danger rounded-circle d-flex align-items-center justify-content-center"
                                                style="width: 50px; height: 50px;">
                                                <span class="text-white fs-4">ا</span>
                                            </div>
                                            <div class="d-flex flex-column">
                                                <div class="d-flex align-items-center gap-2">
                                                    <span class="fw-bold fs-5">{{ $balanceCharge->client->trade_name }}</span>
                                                    <a href="" class="text-decoration-underline text-muted"
                                                        style="font-size: 0.9rem;">#{{ $balanceCharge->client->id }}</a>
                                                </div>
                                                <div class="mt-2">
                                                    <a href="{{ route('clients.show', $balanceCharge->client->id) }}"
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
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">الرصيد المستخدم:</p>
                                                <p class="lh-1">
                                                    <span
                                                        class="d-block fs-22 font-weight-bold">{{ $balanceCharge->used_balance }}</span>
                                                    <span class="fs-12">نقطة</span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">نوع الرصيد:</p>
                                                <p class="lh-16 mb-0 fs-14 font-weight-bold">
                                                    {{ $balanceCharge->balanceType->name }}
                                                    <a href=""
                                                        class="font-weight-normal fs-12 text-decoration-underline ml-2"
                                                        target="_blank">#{{ $balanceCharge->balance_type_id }}</a>
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
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">تاريخ البدء:</p>
                                                <p class="lh-1">
                                                    <span
                                                        class="d-block fs-20 font-weight-bold">{{ $balanceCharge->start_date }}</span>
                                                </p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="text-muted mb-1">تاريخ الانتهاء:</p>
                                                <p class="lh-1">
                                                    <span
                                                        class="d-block fs-20 font-weight-bold">{{ $balanceCharge->end_date }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="py-2 h-100">
                                    <div class="card h-100 d-flex align-items-center justify-content-center"
                                        style="background-color: #f8f9fa;">
                                        <div class="text-center">
                                            <div id="progress-circle" class="progress-circle">
                                                <div class="progress-circle-text">
                                                    <strong
                                                        style="font-size: 24px; display: block;">{{ $balanceCharge->remaining_balance }}</strong>
                                                    <span style="font-size: 12px; display: block;">من
                                                        {{ $balanceCharge->total_balance }}</span>
                                                    <span style="font-size: 12px; display: block;">نقطة متبقية</span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <p class="mb-0">{{ $balanceCharge->used_balance }} نقطة <strong
                                                        style="color: #4E5381">مستهلك</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="py-2 h-100">
                                    <div class="card h-100 p-3 d-block" style="background-color: #f8f9fa;">
                                        <p class="text-muted mb-1">الوصف:</p>
                                        <p class="pre mb-0">{{ $balanceCharge->description }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب استخدام الرصيد -->
                <div class="tab-pane" id="usage" role="tabpanel">
                    <p class="text-muted text-center">لا يوجد استخدام للرصيد حتى الآن</p>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <p class="text-muted text-center">لا توجد نشاطات حتى الآن</p>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var circle = new ProgressBar.Circle('#progress-circle', {
                color: '#4E5381',
                strokeWidth: 4,
                trailWidth: 4,
                duration: 1400,
                easing: 'easeInOut',
                trailColor: '#eee',
            });

            // Check if total_balance is greater than zero to avoid division by zero
            var remainingBalance = {{ $balanceCharge->remaining_balance }};
            var totalBalance = {{ $balanceCharge->total_balance }};

            if (totalBalance > 0) {
                circle.animate(remainingBalance / totalBalance); // Calculate progress
            } else {
                circle.animate(0); // Set to 0 if total balance is zero
            }
        });
    </script>
@endsection
