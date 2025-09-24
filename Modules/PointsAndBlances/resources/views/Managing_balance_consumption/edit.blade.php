@extends('master')

@section('title')
    تعديل استهلاك الارصدة
@stop

@section('content')
    <div style="font-size: 1.1rem;">
        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> تعديل استهلاك الارصدة</h2>
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


        @include('layouts.alerts.success')
        @include('layouts.alerts.error')

        <div class="content-body">
            <form class="form mt-4" style="font-size: 1.1rem;" method="POST"
                action="{{ route('ManagingBalanceConsumption.update', $balanceConsumption->id) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card mb-5">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap mb-4">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
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
                <div class="card" style="max-width: 90%; margin: 0 auto;">
                    <div class="card-header">
                        <h1>
                            تفاصيل استهلاك الارصدة
                        </h1>
                    </div>
                    <div class="card-body">
                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="client_id" class="">العميل <span class="text-danger">*</span></label>
                                <select name="client_id" class="form-control" id="client_id">
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ $client->id == $balanceConsumption->client_id ? 'selected' : '' }}>
                                            {{ $client->first_name }} {{ $client->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group col-md-6 mb-3">
                                <label for="consumption_date" class="">تاريخ الاستهلاك <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="consumption_date"
                                    value="{{ $balanceConsumption->consumption_date->format('Y-m-d') }}" id="consumption_date">
                            </div>
                        </div>

                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="status" class=""> نوع الرصيد <span class="text-danger">*</span></label>
                                <select name="balance_type_id" class="form-control" id="balance_type_id">
                                    <option value="">اختر نوع الرصيد</option>
                                    @foreach ($balanceTypes as $type)
                                        <option value="{{ $type->id }}" {{ $type->id == $balanceConsumption->balance_type_id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-6 mb-3">
                                <label for="used_balance" class=""> الرصيد المستخدم <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="used_balance" id="used_balance"
                                    value="{{ $balanceConsumption->used_balance }}">
                            </div>
                        </div>

                        <div class="form-body row mb-5">
                            <div class="form-group col-md-6 mb-3">
                                <label for="description" class="">الوصف <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="description" id="description">{{ $balanceConsumption->description }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="position-relative" style="margin-top: 2rem;">
                                    <div class="input-group form-group">
                                        <div class="input-group-prepend w-100">
                                            <div class="input-group-text w-100">
                                                <div class="custom-control custom-Checkbox d-flex justify-content-start align-items-center w-100">
                                                    <input id="duration_checkbox" name="contract_type"
                                                        class="custom-control-input" type="checkbox" value="duration"
                                                        {{ $balanceConsumption->contract_type == 'duration' ? 'checked' : '' }}>
                                                    <label for="duration_checkbox" class="custom-control-label">موقوف <span
                                                            class="required">*</span></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
