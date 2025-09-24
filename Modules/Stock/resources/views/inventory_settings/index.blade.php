@extends('master')

@section('title')
اعدادات المخزون
@stop

@section('css')
    <style>
        .setting{
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            flex-direction: column;
        }
        .hover-card:hover{
            background-color: #cdd2d8;
            scale: .98;
        }
        .container{
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
                                <a href="{{ route('inventory_settings.general') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/settings.png') }}" alt="img placeholder">
                                    <h5><strong>عام</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('inventory_settings.employee_default_warehouse') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/inventory-management.png') }}" alt="img placeholder">
                                    <h5><strong>المستودع الافتراضي للموظف</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <img class="p-3" src="{{ asset('app-assets/images/printer.png') }}" alt="img placeholder">
                                    <h5><strong>قوالب للطباعة</strong></h5>
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
