@extends('master')

@section('title')
تقارير SMS
@stop

@section('content')

<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الحملات التسويقية SMS</h2>
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

<div class="container my-5">
    <h1 class="text-center mb-4">رسائل الحملات التسويقية</h1>
    <div class="alert alert-danger text-center">
        يجب عليك تكوين إعدادات الإضافة لإرسال الرسائل
    </div>

    <div class="row">
        <!-- شركة 1 -->
        <div class="col-md-3 mb-4">
            <a href="https://4jawaly.net" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=4Jawaly" class="card-img-top" alt="4jawaly">
                    <div class="card-body">
                        <h5 class="card-title">فور جوالي</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 2 -->
        <div class="col-md-3 mb-4">
            <a href="https://alghaddm.com" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=Alghaddm" class="card-img-top" alt="Alghaddm">
                    <div class="card-body">
                        <h5 class="card-title">الغدم</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 3 -->
        <div class="col-md-3 mb-4">
            <a href="https://www.twilio.com" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=Twilio" class="card-img-top" alt="Twilio">
                    <div class="card-body">
                        <h5 class="card-title">Twilio</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 4 -->
        <div class="col-md-3 mb-4">
            <a href="https://www.plivo.com" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=Plivo" class="card-img-top" alt="Plivo">
                    <div class="card-body">
                        <h5 class="card-title">Plivo</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 5 -->
        <div class="col-md-3 mb-4">
            <a href="https://mobily.ws" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=Mobily" class="card-img-top" alt="Mobily">
                    <div class="card-body">
                        <h5 class="card-title">Mobily.ws</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 6 -->
        <div class="col-md-3 mb-4">
            <a href="https://mobile.net.sa" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=MobileNet" class="card-img-top" alt="MobileNet">
                    <div class="card-body">
                        <h5 class="card-title">Mobile.net.sa</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 7 -->
        <div class="col-md-3 mb-4">
            <a href="#" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=HiSMS" class="card-img-top" alt="HiSMS">
                    <div class="card-body">
                        <h5 class="card-title">Hi SMS</h5>
                    </div>
                </div>
            </a>
        </div>
        <!-- شركة 8 -->
        <div class="col-md-3 mb-4">
            <a href="#" target="_blank" class="text-decoration-none">
                <div class="card text-center h-100">
                    <img src="https://via.placeholder.com/150x80?text=HawaSMS" class="card-img-top" alt="HawaSMS">
                    <div class="card-body">
                        <h5 class="card-title">Hawa SMS</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection
