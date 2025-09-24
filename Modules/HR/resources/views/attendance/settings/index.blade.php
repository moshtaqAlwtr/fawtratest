@extends('master')

@section('title')
    إعدادات الحضور
@stop

@section('css')
    <style>
        .setting {
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
        }
        .hover-card:hover {
            background-color: #cdd2d8;
            scale: .98;
        }
        .container {
            max-width: 1200px;
        }
        .card-content {
            padding: 1.5rem;
        }
        .fa-8x {
            font-size: 5rem;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
        a:hover {
            color: inherit;
        }
    </style>
@stop

@section('content')
    <div class="content-body">
        <section id="statistics-card" class="container">
            <div class="row">
                <!-- قوائم العطلات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('holiday_lists.index') }}">
                                    <i class="fas fa-calendar-alt fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>قوائم العطلات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قواعد الحضور -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('attendance-rules.index') }}">
                                    <i class="fas fa-clipboard-check fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>قواعد الحضور</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- نوع الإجازات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('leave_types.index') }}">
                                    <i class="fas fa-sign-out-alt fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>نوع الإجازات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الماكينات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('machines.index') }}">
                                    <i class="fas fa-desktop fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>الماكينات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- سياسة الإجازات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('leave_policy.index') }}">
                                    <i class="fas fa-bookmark fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>سياسة الإجازات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- رصيد الإجازات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('employee_leave_balances.index') }}">
                                    <i class="fas fa-coins fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>رصيد الإجازات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الإعدادات الأساسية -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('settings_basic.index') }}">
                                    <i class="fas fa-cog fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>الإعدادات الأساسية</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- محددات الحضور -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('attendance_determinants.index') }}">
                                    <i class="fas fa-user-check fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>محددات الحضور</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قوالب الطباعة -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('attendance.settings.printable-templates.index') }}">
                                    <i class="fas fa-print fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>قوالب الطباعة</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <!-- تأكد من إضافة Font Awesome إذا لم يكن مضافاً -->
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
@endsection
