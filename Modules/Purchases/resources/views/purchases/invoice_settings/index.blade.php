@extends('master')

@section('title')
اعدادات فواتير الشراء
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
                <!-- فاتورة الشراء -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="{{ route('purchase_invoices.settings') }}">
                                    <i class="fas fa-file-invoice-dollar fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>فاتورة الشراء</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تصاميم فواتير الشراء / مرتجعات المشتريات -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-paint-brush fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>تصاميم فواتير الشراء / مرتجعات المشتريات</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- قوالب للطباعة -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-print fa-8x p-3" style="color: #17a2b8;"></i>
                                    <h5><strong>قوالب للطباعة</strong></h5>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- الحقول الإضافية -->
                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card hover-card">
                        <div class="card-content">
                            <div class="card-body setting">
                                <a href="">
                                    <i class="fas fa-plus-circle fa-8x p-3" style="color: #17a2b8;"></i>
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
