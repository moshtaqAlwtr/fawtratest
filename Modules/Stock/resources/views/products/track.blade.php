@extends('master')

@section('title')
    أدارة الأشتراكات
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> تتبع المنتجات </h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                                <li class="breadcrumb-item active">عرض</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>


            <div class="container mt-4">
              
        
                <div class="card p-3 mt-4">
                    <table class="table table-bordered text-center">
                        <thead class="table-light">
                            <tr>
                                <th>المنتج </th>
                                <th>نوع التتبع</th>
                                <th>الكمية</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الايام</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($Products as $Product)
                            <tr>
                               <td>{{$Product->name ?? ""}}</td>
                               <td>
                               @if($Product->track_inventory == 4)
                               تتبع الكمية
                               @elseif($Product->track_inventory == 2)
                               تتبع تاريخ الانتهاء
                               @endif

                               </td>
                                <td>   الكمية اقل من : <b>{{$Product->low_stock_alert ?? ""}}</b></td>
                                <td>{{$Product->expiry_date ?? ""}}</td>
                                <td>التاريخ اقل من : <b>{{$Product->notify_before_days ?? ""}} </b></td>
                                
                             
                            </tr>
                               @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
   
     
        
        @endsection