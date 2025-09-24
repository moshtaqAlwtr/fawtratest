@extends('master')

@section('title')
    إعدادات الفاتورة الإلكترونية
@stop

@section('content')
<style>
.card-electronic {
    border: 1px solid #d8e1ef;
    border-radius: 15px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    transition: box-shadow 0.2s;
    text-align: center;
    padding: 40px 20px 30px 20px;
    min-height: 280px;
    background: #fff;
}
.card-electronic:hover {
    box-shadow: 0 4px 20px rgba(0,0,0,.10);
}
.card-electronic .title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #243858;
    margin-bottom: 15px;
}
.card-electronic .desc {
    color: #63759c;
    font-size: 1.1rem;
    min-height: 45px;
}
.btn-activate {
    background-color: #18b772;
    color: #fff;
    border-radius: 8px;
    margin-top: 20px;
    min-width: 110px;
    font-weight: bold;
    transition: background .2s;
}
.btn-activate:hover {
    background-color: #169e62;
    color: #fff;
}
.icon-check {
    font-size: 1.6rem;
    vertical-align: -3px;
    margin-right: 5px;
}
.settings-icon {
    font-size: 3.5rem;
    color: #63759c;
    margin-bottom: 15px;
}
</style>

<div class="container py-4">
    <h3 class="mb-4 text-primary">إعدادات الفاتورة الإلكترونية</h3>
    <div class="row justify-content-center">
        <!-- منصة فاتورة التجريبية -->
        <div class="col-md-5 mb-4">
            <div class="card-electronic">
                <div class="title">منصة فوترة التجريبية</div>
                <div class="desc mb-3">يمكنك اختبار التكامل من خلال محاكاة فوترة</div>
               
                    
                    <a href="{{route('settings_send_fawtra')}}" class="btn btn-activate">
                        <span class="icon-check">&#10003;</span> تفعيل
                    </a>
               
            </div>
        </div>
        <!-- منصة فاتورة الحقيقية -->
        <div class="col-md-5 mb-4">
            <div class="card-electronic">
                <div class="title">منصة فوترة</div>
                <div class="desc mb-3">يمكنك إرسال العملية الفعلية من خلال بوابة فوترة</div>
                 <a href="{{route('settings_send_fawtra')}}" class="btn btn-activate">
                        <span class="icon-check">&#10003;</span> تفعيل
                    </a>
            </div>
        </div>
        <!-- الإعدادات العامة -->
        <div class="col-md-5 mb-4">
            <div class="card-electronic">
                <div class="settings-icon">
                    <i class="fa fa-file" aria-hidden="true"></i>
                    <i class="fa fa-cog" aria-hidden="true" style="margin-right: -20px; font-size:2.5rem"></i>
                </div>
                <div class="title">الإعدادات العامة</div>
                <div class="desc mb-3"></div>
                <a href="{{route('electronic_invoice_edit')}}" class="btn btn-outline-primary" style="min-width:110px;">
                    <i class="fa fa-sliders-h"></i> فتح الإعدادات
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
