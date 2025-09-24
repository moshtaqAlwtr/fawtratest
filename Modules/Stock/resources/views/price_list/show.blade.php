@extends('master')

@section('title')
قوائم الاسعار
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قوائم الاسعار</h2>
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

        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card-body">

            <div class="card">
                <div class="card-title p-2">
                    <a href="" data-toggle="modal" data-target="#modal_ADD_PRODUCT{{ $price_list->id  }}" class="btn btn-sm btn-outline-primary">اضافه منتج <i class="fa fa-plus"></i></a>
                    <a href="" data-toggle="modal" data-target="#modal_DELETE{{ $price_list->id }}" class="btn btn-sm btn-outline-danger">حذف <i class="fa fa-trash"></i></a>
                </div>

                <div class="card-body">

                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="">بحث بالمنتجات</label>
                            <select name="category" class="form-control" id="">
                                <option value="">الكل</option>
                                @foreach ($list_products as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="">بحث بالتصنيف</label>
                            <select name="category" class="form-control" id="">
                                <option value="">الكل</option>
                                <option value="1">تصنيف 1</option>
                                <option value="2">تصنيف 2</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="">بحث بالماركة</label>
                            <select name="category" class="form-control" id="">
                                <option value="">الكل</option>
                                <option value="1">ماركة 1</option>
                                <option value="2">ماركة 2</option>
                            </select>
                        </div>

                    </div>

                    <br>

                    @if (@isset($list_products) && !@empty($list_products) && count($list_products) > 0)

                    <table class="table">
                        <thead>
                            <tr>
                                <th class="d-none d-md-table-cell"> الكود</th>
                                <th class="d-none d-md-table-cell"> البند</th>
                                <th class="d-none d-md-table-cell"> الماركة</th>
                                <th class="d-none d-md-table-cell">سعر البيع</th>
                                <th class="d-none d-md-table-cell"> اجراء</th>
                            </tr>
                        </thead>
                        @foreach ($list_products as $product)
                        <tr>
                            <td>{{ $product->barcode }}</td>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->brand }}</td>
                            <td>{{ $product->sale_price }}</td>
                            <td><a href="{{ route('price_list.delete_product',$product->id) }}" class="btn btn-sm btn-outline-danger">حذف <i class="fa fa-trash"></i></a></td>
                        </tr>
                        @endforeach
                    </table>

                    @else
                        <div class="alert alert-danger text-xl-center" role="alert">
                            <p class="mb-0">
                                لا توجد عناصر اضيفت لقائهة الاسعار حتى الان !
                            </p>
                        </div>
                    @endif

                </div>
            </div>

        </div>

    </div>

        <!-- Modal delete -->
        <div class="modal fade text-left" id="modal_DELETE{{ $price_list->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #EA5455 !important;">
                        <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف {{ $price_list->name }}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true" style="color: #DC3545">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <strong>
                            هل انت متاكد من انك تريد الحذف ؟
                        </strong>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                        <a href="{{ route('price_list.delete',$price_list->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                    </div>
                </div>
            </div>
        </div>
        <!--end delete-->

        <!-- Modal Edit -->
        <div class="modal fade text-left" id="modal_ADD_PRODUCT{{ $price_list->id  }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel1">اضافه منتج</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form class="form" action="{{ route('price_list.add_product',$price_list->id) }}" method="POST" >
                            @csrf
                            <div class="form-body">
                                <div class="row">

                                    <div class="col-12">
                                        <div class="form-label-group">
                                            <select id="product-select" class="form-control select2" aria-invalid="false" name="product_id" onchange="updateInput()">
                                                <option value="" disabled selected>-- اختر المنتج --</option>
                                                @foreach ($products as $product)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->sale_price }}" {{ old('product_id',$product->product_id) == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                                                @endforeach
                                            </select>

                                            @error('product_id')
                                            <span class="text-danger" id="basic-default-name-error" class="error">
                                                {{ $message }}
                                            </span>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-label-group">
                                            <input id="product-name" type="text" class="form-control" placeholder="سعر البيع" name="sale_price" value="{{ old('sale_price',$price_list->sale_price) }}">
                                            <label for="first-name-floating">سعر البيع</label>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-outline-primary btn-sm round mr-1 mb-1 waves-effect waves-light">اضافه</button>
                            <button type="reset" class="btn btn-outline-warning btn-sm round mr-1 mb-1 waves-effect waves-light">تفريغ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!--end Model-->


@endsection


@section('scripts')

<script>
    function updateInput() {
        const selectElement = document.getElementById('product-select');
        const inputElement = document.getElementById('product-name');

        const selectedOption = selectElement.options[selectElement.selectedIndex];

        inputElement.value = selectedOption.getAttribute('data-price') || '';
    }
</script>

@endsection
