@extends('master')

@section('title')
    الاعدادات الشحن والارصدة
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

        .icon-layers {
            color: #4b4e6d; /* لون مطابق للأيقونة في الصورة */
        }

        h5 {
            color: #4b4e6d; /* نفس اللون للنص */
        }
    </style>
@endsection

@section('content')
    <div class="content-body">

        <section id="statistics-card" class="container">
            <div class="row">
                <div class="col-lg-4 col-sm-7 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('BalanceType.index') }}">
                                    <i class="fas fa-layer-group fa-8x p-3 icon-layers"></i>
                                    <h5><strong>انواع الرصيد</strong></h5>
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
