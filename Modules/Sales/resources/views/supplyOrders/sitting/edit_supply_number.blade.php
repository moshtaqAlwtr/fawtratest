@extends('master')

@section('title')
    اعدادات امر تشغيل
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اعدادات امر تشغيل</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>




    <div class="content-body">
        <div class="container-fluid">


            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">

                        <div>
                            <a href="" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>

                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">رقم أمر التشغيل</h4>
                        <a href="{{route('SupplySittings.sitting_serial_number')}}" class="btn btn-link">إعدادات لترقيم المتسلسل</a>
                    </div>

                    <div class="card-body">
                        <form class="form">
                            <div class="form-body row">
                                <div class="form-group col-md-12">
                                    <label for="feedback2" class="sr-only"> رقم امر التشغيل</label>
                                    <input type="number" id="feedback2" class="form-control"
                                        placeholder=" رقم امر التشغيل" name="email">
                                </div>

                                <div class="form-group col-md-4">
                                    <select id="feedback2" class="form-control select2">
                                        <option value="">المصروفات  </option>
                                        <option value="1">فواتير  </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>






        </div>
    </div>
    </div>
@endsection
