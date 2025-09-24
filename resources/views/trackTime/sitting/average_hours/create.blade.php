@extends('master')

@section('title')
    اضافة مدة الساعة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> اضافة مدة الساعة </h2>
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
                            <label> اضافة مدة الساعة <span style="color: red"></span> </label>
                        </div>

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


                    <div class="card-body">
                        <form class="form">



                            <div class="form-body row">
                                <div class="form-group col-6">
                                    <label for="feedback2" class="" style="margin-bottom: 10px">اسم الموضف</label>
                                    <section class="d-flex flex-wrap gap-2 select2">
                                        <option value=""> اختر الموضف </option>
                                        @foreach ($employees as $employee)
                                            <option value=" {{ $employee->id }}">{{ $employee->full_name }}</option>
                                        @endforeach
                                    </section>
                                </div>
                                <div class="form-group col-6">
                                    <label for="feedback2" class="" style="margin-bottom: 10px">معدل الساعة
                                    </label>
                                    <input type="text" id="feedback2" value="0:00" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-body row">
                                <div class="form-group col-6">
                                    <label for="feedback2" class="" style="margin-bottom: 10px">اختر العملة
                                    </label>
                                    <section class="d-flex flex-wrap gap-2 select2">
                                        <option value=""> اختر العملة </option>
                                        <option value="">SAR </option>
                                        <option value="">USD </option>
                                        <option value="">EUR </option>

                                    </section>
                                </div>
                            </div>
                        </form>

                    </div>

                </div>

            </div>


            <!-- Modal delete -->



        </div>
    </div>
    </div>
@endsection
