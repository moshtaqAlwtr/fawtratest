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
                                <a href="{{ route('product_settings.barcode_settings') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/barcode.png') }}" alt="img placeholder">
                                    <h5><strong>اعدادات الباركود</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('product_settings.category') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/tag.png') }}" alt="img placeholder">
                                    <h5><strong>التصنيفات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('template_unit.index') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/balance.png') }}" alt="img placeholder">
                                    <h5><strong>قوالب الوحدات</strong></h5>
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
                                    <img class="p-3" src="{{ asset('app-assets/images/form.png') }}" alt="img placeholder">
                                    <h5><strong>حقول اضافيه</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('product_settings.default_taxes') }}">
                                    <img class="p-3" src="{{ asset('app-assets/images/invoce.png') }}" alt="img placeholder">
                                    <h5><strong>الضرائب الافتراضيه</strong></h5>
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
