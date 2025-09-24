@extends('master')

@section('title')
    اضافة مشروع
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0"> اضافة مشروع </h2>
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
                            <label> اضافة مشروع متابعة الوقت <span style="color: red"></span> </label>
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
                                <div class="form-group col-12">
                                    <label for="feedback2" class="" style="margin-bottom: 10px">الاسم
                                    </label>
                                    <input type="text" id="feedback2" class="form-control" placeholder="">
                                </div>
                            </div>
                            <div class="form-body row">
                                <div class="d-flex flex-wrap gap-3">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="checkbox" readonly id="project"
                                            style="width: 1.5em; height: 1.5em; margin-bottom: 20px; margin-right: 20px;">
                                        <label class="form-check-label fs-5" for="project" style="margin-bottom: 20px;">
                                            نشط
                                        </label>
                                    </div>
                                </div>
                            </div>



                            <div id="customRateSection" style="display: none;">
                                <div class="card" style="background-color: #f8f9fa;">
                                    <div class="card-header">
                                        <h5 class="mb-0">قائمة معدل الساعة المخصصة</h5>
                                    </div>
                                    <div class="card-body">
                                        <div id="ratesList">
                                            <!-- هنا سيتم إضافة معدلات الساعة -->
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-body row">
                                <div class="mb-3" style="margin-right: 20px;">
                                    <a href="#" class="text-primary text-decoration-none" id="showCustomRate">
                                        <i class="fas fa-plus-circle me-1"></i>
                                        إضافة معدل ساعة مخصص ؟
                                    </a>
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
@section('scripts')
<script>
    document.getElementById('showCustomRate').addEventListener('click', function(e) {
        e.preventDefault();
        document.getElementById('customRateSection').style.display = 'block';
        // إضافة أول صف عند فتح القسم
        addNewRateRow();
    });

    function addNewRateRow() {
        const rateRow = document.createElement('div');
        rateRow.className = 'row mb-3';
        rateRow.innerHTML = `
            <div class="col-5">
                <input type="number" class="form-control" name="hour_rates[]" placeholder="معدل الساعة">
            </div>
            <div class="col-6">
                <select class="form-select select2" name="users[]" style="width: 100%">
                    <option value="محمد العتيبي">محمد العتيبي</option>
                </select>
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-danger delete-rate">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.getElementById('ratesList').appendChild(rateRow);

        // تهيئة select2 للعنصر الجديد
        $(rateRow).find('.select2').select2({
            width: '100%',
            dropdownAutoWidth: true
        });

        // إضافة حدث لزر الحذف
        rateRow.querySelector('.delete-rate').addEventListener('click', function() {
            rateRow.remove();
            // إذا لم يتبق أي صفوف، نخفي القسم
            if (document.getElementById('ratesList').children.length === 0) {
                document.getElementById('customRateSection').style.display = 'none';
            }
        });
    }
</script>

@endsection
