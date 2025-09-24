@extends('master')

@section('title')
    تعديل اتفاقية قسط
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">  تعديل  اتفاقية قسط</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>


    @include('layouts.alerts.success')
    @include('layouts.alerts.error')


    <div class="content-body">
        <form class="form" action="{{ route('installments.update', $installment->id) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="card" style="max-width: 90%; margin: 0 auto;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h1 class="card-title"> معلومات اتفاقية التقسيط </h1>
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



                    <div class="form-body row">

                        <div class="form-group col-md-6">
                            <label for="due_date" class=""> تاريخ  الاستحقاق </label>
                            <input type="date" id="due_date" class="form-control" name="due_date" value="{{ $installment->due_date }}">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="installment_amount" class=""> المبلغ  </label>
                            <input type="number" id="installment_amount" class="form-control" name="amount">
                        </div>

                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@section('scripts')


<script>
    // تعيين التاريخ الحالي كقيمة افتراضية لحقل تاريخ بدء السداد
    document.addEventListener('DOMContentLoaded', function() {
        const today = new Date().toISOString().split('T')[0]; // الحصول على التاريخ الحالي بصيغة YYYY-MM-DD
        document.getElementById('due_date').value = today; // تعيين القيمة لحقل الإدخال
    });
</script>
@endsection
