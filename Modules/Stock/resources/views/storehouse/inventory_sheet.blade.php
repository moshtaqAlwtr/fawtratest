@extends('master')

@section('title')
ورقة الجرد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ورقة الجرد</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">{{ $storehouse->name }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div></div>
                    <div>
                        <a href="#" class="btn btn-outline-info mb-1"><i class="fa fa-print"></i> طباعة</a>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>الكود</th>
                                <th>الاسم</th>
                                <th>التصنيف</th>
                                <th>العدد بالنظام</th>
                                <th>الكمية</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $product->product->barcode }}</td>
                                    <td>{{ $product->product->name }}</td>
                                    <td>{{ $product->product->category->name }}</td>
                                    <td>{{ $product->quantity }}</td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')
@endsection
a
