@extends('master')

@section('title')
    ادارةالعضويات
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> ادارة الاشتراكات </h2>
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

        <div class="content-body">
            <div class="card">
                <div class="card-body">
                   
                </div>
            </div>
            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form">
                            <div class="form-body row">
                                <div class="form-group col-md-4">
                                    <label for="feedback1" class=""> البحث بواسطة العميل او الرقم التعريفي </label>
                                    <input type="text" id="feedback1" class="form-control"
                                        placeholder="البحث بواسطة العميل  او الرقم التعريفي" name="name">
                                </div>
                                <div class="form-group col-md-4">
                                    <label for="feedback1" class=""> البحث بواسطة اسم الرصيد او الرقم التعريفي
                                    </label>
                                    <input type="text" id="feedback1" class="form-control"
                                        placeholder="البحث بواسطة اسم الباقة  او الرقم التعريفي" name="name">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="feedback2" class=""> الحالة </label>
                                    <select id="feedback2" class="form-control">
                                        <option value="">اختر الحالة </option>

                                    </select>
                                </div>
                            </div>
                            <div class="form-body row">
                                <!-- Row 1 -->
                                <div class="form-group col-md-3">
                                    <label> تاريخ التحاق(من )</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ التحاق(الى )</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ الانتهاء(من )</label>
                                    <input type="date" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label> تاريخ الانتهاء(الى )</label>
                                    <input type="date" class="form-control">
                                </div>

                            </div>
                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>


                                <button type="reset" class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر
                                </button>
                            </div>
                        </form>

                    </div>

                </div>

            </div>
            @include('layouts.alerts.error')
            @include('layouts.alerts.success')

            <div class="card">
                <div class="card-body">

                    <table class="table" style="font-size: 1.1rem;">
                        <thead>
                            <tr>
                                <th>المعرف</th>
                                <th>بيانات العميل</th>

                                <th>الباقة الحالية </th>
                                <th>تاريخ الانتهاء</th>
                                <th>رقم الفاتورة </th>
                                <th>الحالة</th>
                                <th>ترتيب بواسطة</th>
                            </tr>
                        </thead>
                        <tbody>
                        
                            @foreach ($memberships as $membership)
                                
                            
                            <tr>
                                <td>#1</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-danger">
                                            <span class="avatar-content">أ</span>
                                        </div>
                                        <div>
                                            {{$membership->client->first_name ?? ""}}
                                            <br>
                                            <small class="text-muted"></small>
                                        </div>
                                    </div>
                                </td>
                                <td><br><small class="text-muted">{{$membership->packege->commission_name ?? ""}}</small></td>

                                <td><small class="text-muted">{{$membership->end_date ?? ""}}</small></td>
                                <td><small class="text-muted">{{$membership->invoice_id ?? ""}}</small></td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="rounded-circle bg-info" style="width: 8px; height: 8px;"></div>
                                        <span class="text-muted">
                                          @if($membership->status == "active")
                                          نشط
                                          @else
                                          غير نشط 
                                          @endif
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <div class="dropdown">
                                            <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                type="button"id="dropdownMenuButton303" data-toggle="dropdown"
                                                aria-haspopup="true"aria-expanded="false"></button>
                                            <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                <li>
                                                    <a class="dropdown-item"
                                                        href="{{ route('Memberships.show_subscription', $membership->id) }}">
                                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                    </a>
                                                </li>
                                             

                                                <li>
                                                    <a class="dropdown-item text-danger" href="{{ route('Memberships.delete', $membership->id) }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </li>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>


        @endsection
