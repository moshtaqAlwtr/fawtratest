@extends('master')

@section('title')
    معلومات الحساب
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">معلومات الحساب</h2>
                    <div class="breadcrumb-wrapper">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">معلومات الحساب</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="row match-height">
            <!-- بطاقة معلومات المؤسسة -->
            <div class="col-md-4">
                <div class="card card-profile">
                    <div class="card-body text-center">
                        <div class="profile-image-wrapper">
                            <div class="profile-image">
                                <div class="avatar">
                                    <img src="{{ asset('app-assets/images/logo/logo.png') }}" alt="Profile">
                                </div>
                            </div>
                        </div>
                        <h3 class="mb-2">{{$client->trade_name ?? ""}}</h3>

                        <div class="profile-info-list">
                            <div class="info-item">
                                <span class="info-label">الموقع الإلكتروني</span>
                                <span class="info-value">https://fawtrasmart.com</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">{{$client->city ?? ""}}</span>
                                <span class="info-value">{{ $client->region ?? ""}}</span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">العملة الافتراضية</span>
                                <span class="badge badge-light-primary">{{$account_setting->currency ?? "SAR"}}</span>
                            </div>
                           <div class="info-item">
    <span class="info-label">المنطقة الزمنية</span>
    <span class="info-value">{{ $account_setting?->timezone ?? 'غير محددة' }}</span>
</div>
                            <div class="info-item">
                                <span class="info-label">صيغة التاريخ</span>
                                <span class="info-value">{{$account_setting->time_formula ?? ""}}</span>
                            </div>
                        </div>

                        <div class="mt-3">
                            <a href="{{ route('AccountInfo.backup') }}" class="btn btn-gradient-primary w-100 waves-effect waves-float waves-light mb-2">
                                <i data-feather="download" class="mr-1"></i>
                                تنزيل نسخة احتياطية
                            </a>
                            <button class="btn btn-outline-danger w-100 waves-effect">
                                <i data-feather="x-circle" class="mr-1"></i>
                                إلغاء هذا الحساب
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- بطاقة معلومات الباقة -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="package-info">
                                    <h4 class="mb-2 text-primary">معلومات الباقة</h4>
                                    <div class="package-details">
                                        <div class="detail-item">
                                            <span class="detail-label">باقة الحساب:</span>
                                            <span class="badge badge-light-warning">Gold</span>
                                        </div>
                                        <div class="detail-item mt-2">
                                            <span class="detail-label">ينتهي في:</span>
                                            <div class="d-flex align-items-center">
                                                <span class="mr-2">05/02/2025</span>
                                                <button class="btn btn-gradient-info btn-sm waves-effect">
                                                    تجديد الباقة
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex flex-column h-100 justify-content-center">
                                    <button class="btn btn-gradient-warning waves-effect waves-float waves-light mb-2">
                                        <i data-feather="arrow-up" class="mr-1"></i>
                                        ترقية الباقة
                                    </button>
                                    <button class="btn btn-gradient-primary waves-effect waves-float waves-light">
                                        <i data-feather="grid" class="mr-1"></i>
                                        لوحة الحسابات
                                    </button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <h4 class="mb-3 text-primary">استهلاك الباقة</h4>
                        <div class="usage-stats">
                            <div class="usage-item">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الفواتير وعروض الاسعار</span>
                                    <span class="badge badge-light-primary">14</span>
                                </div>
                                <div class="progress progress-bar-primary">
                                    <div class="progress-bar" role="progressbar" style="width: 14%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>العملاء</span>
                                    <span class="badge badge-light-primary">0</span>
                                </div>
                                <div class="progress progress-bar-success">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الفواتير الدورية</span>
                                    <span class="badge badge-light-primary">0</span>
                                </div>
                                <div class="progress progress-bar-info">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الموظفين</span>
                                    <span class="badge badge-light-primary">0</span>
                                </div>
                                <div class="progress progress-bar-warning">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الفروع والمستخدمين</span>
                                    <span class="badge badge-light-danger">5</span>
                                </div>
                                <div class="progress progress-bar-danger">
                                    <div class="progress-bar" role="progressbar" style="width: 100%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>مساحة التخزين (جيجا)</span>
                                    <span class="badge badge-light-primary">0.858</span>
                                </div>
                                <div class="progress progress-bar-success">
                                    <div class="progress-bar" role="progressbar" style="width: 1.716%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الخزن النقدي و البنوك</span>
                                    <span class="badge badge-light-primary">4</span>
                                </div>
                                <div class="progress progress-bar-primary">
                                    <div class="progress-bar" role="progressbar" style="width: 8%"></div>
                                </div>
                            </div>

                            <div class="usage-item mt-2">
                                <div class="d-flex justify-content-between mb-1">
                                    <span>الإيجارات والوحدات</span>
                                    <span class="badge badge-light-primary">0</span>
                                </div>
                                <div class="progress progress-bar-info">
                                    <div class="progress-bar" role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* تنسيقات البطاقات المخصصة */
        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.1);
            transition: all 0.3s ease-in-out;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 24px 0 rgba(34, 41, 47, 0.2);
        }

        /* تنسيقات الصورة الشخصية */
        .profile-image-wrapper {
            padding: 0.5rem 0;
            margin-bottom: 1.5rem;
        }

        .avatar {
            height: 100px;
            width: 100px;
            border-radius: 50%;
            background: #f8f8f8;
            padding: 4px;
            border: 2px solid #7367f0;
        }

        .avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        /* تنسيقات قائمة المعلومات */
        .info-item {
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            border-radius: 0.3rem;
            background: rgba(115, 103, 240, 0.05);
        }

        .info-label {
            color: #6e6b7b;
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }

        /* تنسيقات الأزرار المتدرجة */
        .btn-gradient-primary {
            background: linear-gradient(45deg, #7367f0, #9e95f5);
            border: none;
            color: #fff;
        }

        .btn-gradient-warning {
            background: linear-gradient(45deg, #ff9f43, #ffc085);
            border: none;
            color: #fff;
        }

        .btn-gradient-info {
            background: linear-gradient(45deg, #00cfe8, #1ce7ff);
            border: none;
            color: #fff;
        }

        /* تنسيقات أشرطة التقدم */
        .progress {
            height: 0.857rem;
            border-radius: 0.358rem;
            background-color: rgba(115, 103, 240, 0.12);
        }

        .progress-bar-primary .progress-bar { background: linear-gradient(45deg, #7367f0, #9e95f5); }
        .progress-bar-success .progress-bar { background: linear-gradient(45deg, #28c76f, #48da89); }
        .progress-bar-warning .progress-bar { background: linear-gradient(45deg, #ff9f43, #ffc085); }
        .progress-bar-danger .progress-bar { background: linear-gradient(45deg, #ea5455, #f08182); }
        .progress-bar-info .progress-bar { background: linear-gradient(45deg, #00cfe8, #1ce7ff); }

        /* تنسيقات الشارات */
        .badge-light-primary {
            background: rgba(115, 103, 240, 0.12);
            color: #7367f0;
        }

        .badge-light-warning {
            background: rgba(255, 159, 67, 0.12);
            color: #ff9f43;
        }

        .badge-light-danger {
            background: rgba(234, 84, 85, 0.12);
            color: #ea5455;
        }
    </style>
@endsection
