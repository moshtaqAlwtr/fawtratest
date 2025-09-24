@extends('master')

@section('title')
إعدادات واجهة التسوق
@stop

@section('content')
    <div class="content-body">

        <section id="statistics-card" class="container">
            <div class="row">

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div>
                                <h1 class="mb-0">
                                    English Template
                                </h1>
                                <hr>
                                <h5 class="mt-1">
                                    <span class="mr-1 bullet bullet-success bullet-sm"></span><small class="mail-date">نشط</small>
                                </h5>
                            </div>
                            <a class="btn btn-info w-100 box-shadow-1 mt-2 waves-effect waves-light" href="">الاعدادات<i class="feather icon-settings"></i></a>
                            <a class="btn btn-primary w-100 box-shadow-1 mt-2 waves-effect waves-light" href="">اعادة تطبيق<i class="feather icon-check"></i></a>
                            <a class="btn btn-success w-100 box-shadow-1 mt-2 waves-effect waves-light" href="{{ route('store_settings.template.preview') }}" target="_blank">عرض تجريبي<i class="feather icon-eye"></i></a>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-body d-flex justify-content-between align-items-start flex-column">
                            <div>
                                <h1 class="mb-0">
                                    قالب عربي
                                </h1>
                                <hr>
                                <h5 class="mt-1">
                                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><small class="mail-date">غير نشط</small>
                                </h5>
                            </div>
                            <a class="btn btn-info w-100 box-shadow-1 mt-2 waves-effect waves-light" href="">الاعدادات<i class="feather icon-settings"></i></a>
                            <a class="btn btn-primary w-100 box-shadow-1 mt-2 waves-effect waves-light" href="">اعادة تطبيق<i class="feather icon-check"></i></a>
                            <a class="btn btn-success w-100 box-shadow-1 mt-2 waves-effect waves-light" href="{{ route('store_settings.template.preview') }}" target="_blank">عرض تجريبي<i class="feather icon-eye"></i></a>
                        </div>
                    </div>
                </div>

            </div>
        </section>

    </div>
@endsection


@section('scripts')
@endsection
