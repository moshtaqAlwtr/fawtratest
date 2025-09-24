@extends('master')

@section('title')
اعدادات المالية
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
                                <a href="{{ route('finance_settings.treasury_employee') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/safe.png') }}" alt="img placeholder">
                                    <h5><strong>خزائن الموظفين</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('finance_settings.expenses_category') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/expenses.png') }}" alt="img placeholder">
                                    <h5><strong>تصنيفات المصروفات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('finance_settings.receipt_category') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/business-and-finance.png') }}" alt="img placeholder">
                                    <h5><strong>تصنيفات سندات القبض</strong></h5>
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
