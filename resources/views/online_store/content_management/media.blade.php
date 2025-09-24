@extends('master')

@section('title')
الوسائط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إدارة المحتوي</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">الوسائط</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-title p-2">
            <a href="{{ route('content_management.media_edit') }}" class="btn btn-outline-primary btn-sm waves-effect waves-light">تعديل <i class="fa fa-edit"></i></a>
        </div>
        <div class="card-body">
            <div class="row">
                <table class="table">
                    <thead style="background: #f8f8f8">
                        <tr>
                            <th>معلومات الوسائط</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <p><small>المعرف</small>: </p><span>main_gallery</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table">
                    <thead style="background: #f8f8f8">
                        <tr>
                            <th>المرفقات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="" width="260px" height="180px" alt="media">
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection
