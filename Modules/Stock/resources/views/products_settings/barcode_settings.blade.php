@extends('master')

@section('title')
التصنيفات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">التصنيفات  </h2>
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

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                    </div>

                    <div>
                        <a href="{{ route('product_settings.index') }}" class="btn btn-outline-danger btn-sm">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" form="products_form" class="btn btn-outline-primary btn-sm">
                            <i class="fa fa-save"></i>حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="container" style="max-width: 1200px">
            <div class="card">
                <div class="card-header"></div>
                <div class="card-content">
                    <div class="card-body">
                        <form id="products_form" class="form form-vertical" action="{{ route('category.store') }}" method="POST">
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-group">
                                            <label for="contact-info-vertical">نوع الباركود</label>
                                            <select name="main_category" class="form-control">
                                                <option value="">Code 128</option>
                                                <option value="">EAN 13</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" id="barcodeActiveCheck" onclick="showInputs()">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">تفعيل الباركود المتضمن الوزن</span>
                                            </div>
                                        </fieldset>
                                    </div>

                                    <br>
                                    <br>

                                    <div class="col-4 barcodeActive" style="display: none;">
                                        <div class="form-group">
                                            <label for="first-name-vertical">صيغة الباركود المتضمن</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="XXXXXXXXWWWWWPPPPN">
                                            @error('name')
                                            <small class="text-danger" id="basic-default-name-error" class="error">
                                                {{ $message }}
                                            </small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-4 barcodeActive" style="display: none;">
                                        <div class="form-group">
                                            <label for="first-name-vertical">تقسيم وحدة الوزن</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                            @error('name')
                                            <small class="text-danger" id="basic-default-name-error" class="error">
                                                {{ $message }}
                                            </small>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-4 barcodeActive"  style="display: none;">
                                        <div class="form-group">
                                            <label for="first-name-vertical">تقسيم العملة</label>
                                            <input type="text" class="form-control" name="name" value="{{ old('name') }}">
                                            @error('name')
                                            <small class="text-danger" id="basic-default-name-error" class="error">
                                                {{ $message }}
                                            </small>
                                            @enderror
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection


@section('scripts')

    <script>
        function showInputs() {
            // Get the checkbox
            const checkbox = document.getElementById("barcodeActiveCheck");
            // Get all elements with the class "barcodeActive"
            const barcodeElements = document.getElementsByClassName("barcodeActive");

            // Show or hide elements based on the checkbox status
            for (let element of barcodeElements) {
                element.style.display = checkbox.checked ? "block" : "none";
            }
        }
    </script>

@endsection
