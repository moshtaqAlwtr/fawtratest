@extends('master')

@section('title')
    الاعدادات تتبع الوقت
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
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('SittingTrackTime.create') }}">
                                    <i class="fas fa-cogs fa-8x p-3" style="color:primary;"></i>
                                    <h5><strong>العام</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('ProjectTrackTime.index') }}">
                                    <i class="fas fa-project-diagram fa-8x p-3" style="color: primary;"></i>
                                    <h5><strong>المشاريع</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('Activities.index') }}">
                                    <i class="fas fa-chart-line fa-8x p-3" style="color: primary;"></i>
                                    <h5><strong>النشاطات </strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('AverageHours.index') }}">
                                    <i class="fas fa-clock fa-8x p-3" style="color: primary;"></i>
                                    <h5><strong>معدل الساعات للموظفين </strong></h5>
                                </a>
                            </div>
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
