@extends('master')

@section('title')
    الاعدادات الرواتب
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
    </style>
@endsection

@section('content')
    <div class="content-body">

        <section id="statistics-card" class="container">
            <div class="row">
                <!-- الحقول المخصصة للعقود -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-folder-open fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>الحقول المخصصة للعقود</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- اشعارات العقد -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-bell fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>اشعارات العقد</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- القوالب -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('RelatedModels.index') }}">
                                    <i class="fas fa-project-diagram fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>النماذج ذات الصلة </strong></h5>
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
                                <a href="">
                                    <i class="fas fa-print fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>قوالب الطباعة</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- اعدادات العمولات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-percentage fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>اعدادات العمولات</strong></h5>
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
@endsection
