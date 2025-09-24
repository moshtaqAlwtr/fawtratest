@extends('master')

@section('title')
    الاعدادات الحسابات
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
                                <a href="{{ route('accounts_settings.financial_years') }}">
                                    <i class="fas fa-calendar-alt fa-8x p-3"  style="color: #17a2b8;"></i>
                                    <h5><strong>الفترات المالية</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('accounts_settings.closed_periods') }}">
                                    <i class="fas fa-lock fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>فترة مغلقة</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{route('accounts_settings.accounts_routing')}}">
                                    <i class="fas fa-balance-scale fa-8x p-3"  style="color: #17a2b8;"></i>
                                    <h5><strong>توجيه الحسابات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('accounts_settings.accounting_general') }}">
                                    <i class="fas fa-cogs fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>العام</strong></h5>
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
