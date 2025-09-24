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
                                <a href="{{ route('SupplySittings.edit_supply_number') }}">
                                    <i class="fas fa-cogs fa-8x p-3" style="color: #17a2b8;"></i>
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
                                <a href="{{ route('SupplyOrders.edit_status') }}">
                                    <i class="fas fa-clipboard-list fa-8x p-3"  style="color: #17a2b8;"></i>
                                    <h5><strong>الحالات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('SupplySittings.edit_procedures') }}">
                                    <i class="fas fa-user-shield fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>الإجراءات</strong></h5>
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
                                    <i class="fas fa-file-alt fa-8x p-3"  style="color: #17a2b8;"></i>
                                    <h5><strong>القوالب</strong></h5>
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
                                    <i class="fas fa-plus-square fa-8x p-3"  style="color: #17a2b8;"></i>
                                    <h5><strong>الحقول الإضافية</strong></h5>
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
