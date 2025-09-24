@extends('master')

@section('title')
    الفواتير
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة الفواتير</h2>
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

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form add_item">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-2">
                                                <span>الطريقه :</span>
                                            </div>
                                            <div class="col-md-4">
                                                <select class="form-control" id="methodSelect">
                                                    <option>ارسال بالبريد</option>
                                                    <option selected>طباعه</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-2">
                                                <span>العميل :</span>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control select2" id="clientSelect">
                                                    <option>يوسف</option>
                                                    <option>مشتاق</option>
                                                    <option>ابو فالح</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <button type="button"
                                                    class="btn btn-primary mr-1 mb-1 waves-effect waves-light">
                                                    <i class="fa fa-user-plus"></i>جديد
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            <form class="form">
                                <div class="row add_item">
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>رقم الفاتورة</span>
                                            </div>
                                            <div class="col-md-4">
                                                <strong>121212</strong>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الفاتورة</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="invoice_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>مسؤول المبيعات</span>
                                            </div>
                                            <div class="col-md-6">
                                                <select class="form-control select2" id="salesRepSelect">
                                                    <option>يوسف</option>
                                                    <option>مشتاق</option>
                                                    <option>ابو فالح</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>تاريخ الاصدار</span>
                                            </div>
                                            <div class="col-md-8">
                                                <input class="form-control" type="date" name="issue_date">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <span>شروط الدفع</span>
                                            </div>
                                            <div class="col-md-4">
                                                <input class="form-control" type="text" name="payment_terms">
                                            </div>
                                            <div class="col-md-3">
                                                <span>ايام</span>
                                            </div>
                                        </div>
                                    </div>


                                    <div class="col-12">
                                        <div class="form-group row">
                                            <div class="col-md-3">
                                                <input class="form-control" type="text" name="" id=""
                                                    placeholder="عنوان اضافي">
                                            </div>
                                            <div class="col-md-6">
                                                <input class="form-control" type="text" name="" id=""
                                                    placeholder="بيانات اضافيه">
                                            </div>
                                            <div class="form-label-group">
                                                <span
                                                    class="btn btn-icon btn-icon rounded-circle btn-outline-success mr-1 mb-1 waves-effect waves-light addeventmore"><i
                                                        class="fa fa-plus-circle"></i></span>
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

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="items-table">
                            <thead>
                                <tr>
                                    <th>البند</th>
                                    <th>الوصف</th>
                                    <th>سعر الوحدة</th>
                                    <th>الكمية</th>
                                    <th>الخصم</th>
                                    <th>الضريبة 1</th>
                                    <th>الضريبة 2</th>
                                    <th>المجموع</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="item-row">
                                    <td style="width:18%">
                                        <select class="form-control item-select">
                                            <option value="square">Square</option>
                                            <option value="rectangle">Rectangle</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" class="form-control" placeholder="الوصف">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control unit-price" placeholder="0.00">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control quantity" placeholder="0">
                                    </td>
                                    <td>
                                        <input type="number" class="form-control discount" placeholder="0%">
                                    </td>
                                    <td>
                                        <select class="form-control tax1">
                                            <option value="5">5%</option>
                                            <option value="10">10%</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control tax2">
                                            <option value="5">5%</option>
                                            <option value="10">10%</option>
                                        </select>
                                    </td>
                                    <td class="row-total">0.00 رس</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-danger remove-row">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="7" class="text-right">الإجمالي</td>
                                    <td id="total-amount">0.00 رس</td>
                                    <td>
                                        <button type="button" class="btn btn-icon btn-outline-primary" id="add-row">
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header bg-white">
                <!-- التبويبات الرئيسية -->
                <ul class="nav nav-tabs card-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link active" id="tab-discount" href="#">الخصم والتسوية</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-deposit" href="#">إيداع</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-shipping" href="#">بيانات الشحن</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="tab-documents" href="#">إرفاق المستندات</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <!-- القسم الأول: الخصم والتسوية -->
                <div id="section-discount" class="row">
                    <div class="col-md-3 text-end">
                        <label for="discount" class="form-label">الخصم</label>
                        <input type="text" id="discount" class="form-control" value="0" readonly>
                    </div>
                    <div class="col-md-3 text-end">
                        <label for="discount-type" class="form-label">نسبة مئوية (%)</label>
                        <select id="discount-type" class="form-select">
                            <option value="1">نسبة مئوية</option>
                            <option value="2">قيمة ثابتة</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-end">
                        <label for="loyalty-points" class="form-label">نقاط الولاء</label>
                        <input type="text" id="loyalty-points" class="form-control" value="0.00" readonly>
                    </div>
                    <div class="col-md-3 text-end">
                        <label for="settlement" class="form-label">التسوية</label>
                        <input type="text" id="settlement" class="form-control">
                    </div>
                </div>

                <!-- القسم الثاني: إيداع -->
                <div id="section-deposit" class="row d-none">
                    <div class="col-md-3 text-end">
                        <label for="advanced-payment" class="form-label">الدفعة المقدمة</label>
                        <input type="text" id="advanced-payment" class="form-control" value="0" readonly>
                    </div>
                    <div class="col-md-3 text-end">
                        <div class="form-check form-switch">
                            <label class="form-label me-4" style="margin-left: 20px;">مدفوع بالفعل</label>
                            <input class="form-check-input" type="checkbox" id="fully-paid">
                        </div>

                        <select id="amount" class="form-select">
                            <option value="1">المبلغ</option>
                            <option value="2">بالنسبة المئوية</option>
                        </select>
                    </div>
                </div>

                <!-- القسم الثالث: بيانات الشحن -->
                <div id="section-shipping" class="row d-none">
                    <div class="col-md-3">
                        <label class="form-label">بيانات الشحن</label>
                        <select class="form-control" id="methodSelect">
                            <option>ارسال بالبريد</option>
                            <option selected>طباعة</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">مصاريف الشحن</label>
                        <select class="form-control" id="shipping-costs">
                            <option></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">المستودع</label>
                        <select class="form-control" id="warehouse">
                            <option></option>
                        </select>
                        <div class="form-check">
                            <label class="form-check-label me-2" for="per-item-warehouse" style="margin-left: 20px;">
                                اختيار المستودع لكل بند
                            </label>
                            <input class="form-check-input" type="checkbox" id="per-item-warehouse">
                        </div>
                    </div>
                </div>

                <!-- القسم الرابع: إرفاق المستندات -->
                <div id="section-documents" class="d-none">
                    <!-- التبويبات الداخلية -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" id="tab-new-document" href="#">رفع مستند جديد</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="tab-uploaded-documents" href="#">بحث في الملفات</a>
                        </li>
                    </ul>

                    <!-- محتوى التبويبات -->
                    <div class="tab-content mt-3">
                        <!-- رفع مستند جديد -->
                        <div id="content-new-document" class="tab-pane active">
                            <div class="col-12 mb-3">
                                <label class="form-label">
                                    <i class="fas fa-file-upload text-primary me-2"></i>
                                    رفع مستند جديد:
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-primary text-white">
                                        <i class="fas fa-upload"></i>
                                    </span>
                                    <input type="file" class="form-control" id="uploadFile"
                                        aria-describedby="uploadButton">
                                    <button class="btn btn-primary" id="uploadButton">
                                        <i class="fas fa-cloud-upload-alt me-1"></i>
                                        رفع
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- بحث في الملفات -->
                        <div id="content-uploaded-documents" class="tab-pane d-none">
                            <div class="row">
                                <div class="col-6 d-flex align-items-center">
                                    <label class="form-label me-2">المستند:</label>
                                    <select class="form-control item-select">
                                        <option value="square">Square</option>
                                        <option value="rectangle">Rectangle</option>
                                    </select>
                                    <div class="d-flex align-items-center">
                                        <button type="button" class="btn btn-relief-success mr-1 ">ارفق</button>
                                    </div>
                                </div>
                                <div class="col-6 d-flex align-items-center">
                                    <button type="button" class="btn btn-relief-success mr-1">بحث متقدم </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header bg-white">
                <h6 class="mb-0">الملاحظات/الشروط</h6>
            </div>
            <div class="card-body">
                <textarea id="tinyMCE"></textarea>
            </div>
        </div>
        <div class="card">
            <div class="card-body py-2 align-items-right">
                <div class="d-flex justify-content-end align-items-right">
                    <div class="form-check">
                        <label class="form-check-label" for="paidCheck" style="margin-left: 20px;">
                            مدفوع بالفعل
                        </label>
                        <input class="form-check-input ms-2" type="checkbox" id="paidCheck">
                    </div>
                </div>

                <!-- حقول الدفع (مخفية بشكل افتراضي) -->
                <div id="paymentFields" class="mt-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <label for="methodSelect">وسيلة الدفع</label>
                            <select class="form-control" id="methodSelect">
                                <option></option>
                                <option selected></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">رقم المعرف</label>
                            <input type="text" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">خزنة</label>
                            <select class="form-control" id="methodSelect">
                                <option></option>
                                <option selected></option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>



        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <a href="#" data-bs-toggle="modal" data-bs-target="#customFieldsModal">
                            <i class="fas fa-cog me-2"></i>
                            <span>إعدادات الحقول المخصصة</span>
                        </a>
                    </div>
                    <div>
                        <span>هدايا مجاناً</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="customFieldsModal" tabindex="-1" aria-labelledby="customFieldsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header border-0">
                        <h5 class="modal-title" id="customFieldsModalLabel">إعدادات الحقول المخصصة</h5>
                        <button type="button" class="btn-close" data-bs-toggle="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info" role="alert">
                            You will be redirected to edit the custom fields page
                        </div>
                    </div>
                    <div class="modal-footer justify-content-start border-0">
                        <button type="button" class="btn btn-success">
                            <i class="fas fa-check me-1"></i>
                            حفظ
                        </button>
                        <button type="button" class="btn btn-danger">
                            عدم الحفظ
                        </button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            إلغاء
                        </button>
                    </div>
                </div>
            </div>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // تهيئة المودال
                var myModal = new bootstrap.Modal(document.getElementById('customFieldsModal'));

                // إضافة مستمعي الأحداث للأزرار
                document.querySelectorAll('.modal-footer .btn').forEach(button => {
                    button.addEventListener('click', function() {
                        if (this.classList.contains('btn-success')) {
                            console.log('تم الضغط على زر الحفظ');
                            // أضف هنا كود الحفظ
                        } else if (this.classList.contains('btn-danger')) {
                            console.log('تم الضغط على زر عدم الحفظ');
                            // أضف هنا كود عدم الحفظ
                        }
                        myModal.hide();
                    });
                });
            });
        </script>



        <!-- إضافة مكتبة TinyMCE -->




    </div>
    </div>

    <!------------------------->

    <div style="visibility: hidden;">
        <div class="whole_extra_item_add" id="whole_extra_item_add">
            <div class="delete_whole_extra_item_add" id="delete_whole_extra_item_add">

                <div class="col-12">
                    <div class="form-group row">
                        <div class="col-md-3">
                            <input class="form-control" type="text" name="" id=""
                                placeholder="عنوان اضافي">
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" type="text" name="" id=""
                                placeholder="بيانات اضافيه">
                        </div>
                        <div class="form-label-group">
                            <span
                                class="btn btn-icon btn-icon rounded-circle btn-outline-success mr-1 mb-1 waves-effect waves-light addeventmore"><i
                                    class="fa fa-plus-circle"></i></span>
                            <span
                                class="btn btn-icon btn-icon rounded-circle btn-outline-danger mr-1 mb-1 waves-effect waves-light removeeventmore"><i
                                    class="fa fa-minus-circle"></i></span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    </div>
@section('scripts')
    <script src="{{ asset('assets/js/scripts.js') }}"></script>
@endsection

@endsection
