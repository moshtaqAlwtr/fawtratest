@extends('master')

@section('title')
    الاعدادات العضويات
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
                                <a href="{{ route('SittingMemberships.sitting') }}">
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
