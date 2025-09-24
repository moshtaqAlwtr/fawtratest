@extends('master')

@section('title')
    اضافة نماذج المخصصة
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة نماذج المخصصة </h2>
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


            <form class="form">
                <div class="card" style="max-width: 90%; margin: 0 auto;">

                    <div class="card-body">
                        <h1 class="card-title"> معلومات النموذج مخصصة </h1>

                        <div class="form-body row">
                            <div class="form-group col-md-6">
                                <label for="feedback1" class="">الاسم </label>
                                <input type="text" id="feedback1" class="form-control" placeholder="الاسم"
                                    name="name">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="feedback1" class="">المعرف </label>
                                <input type="text" id="feedback1" class="form-control" placeholder="المعرف"
                                    name="name">
                            </div>


                        </div>
                        <div class="form-body row">


                            <div class="form-group col-md-6">
                                <label for="feedback2" class=""> الحالة </label>
                                <select name="" class="form-control" id="">
                                    <option value=""> اختر الحالة</option>

                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="feedback2" class=""> الوصف </label>
                                <textarea name="" class="form-control" id=""></textarea>
                            </div>



                        </div>

                    </div>











                </div>

        </div>
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">الصلاحيات</h4>
            </div>
            <div class="card-content">
                <div class="card-body">

                    <div class="col">
                        <div class="form-body">
                            <!-- إضافة سجل -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>إضافة سجل <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="add_record" id="add_record" required>
                                                    <option value="">اختر...</option>
                                                    <option value="all">الكل</option>
                                                    <option value="no">لا شيء</option>
                                                    <option value="specific_branches">أفرع محددة</option>
                                                    <option value="specific_sections">أقسام محددة</option>
                                                    <option value="specific_job_titles">مسميات وظيفية محددة</option>
                                                    <option value="specific_employees">موظفين محددين</option>
                                                    <option value="specific_job_roles">أدوار وظيفية محددة</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <select class="form-control specific-field"
                                                    name="add_record_specific_branches[]" id="add_record_branches"
                                                    style="display: none;">
                                                    <option value="">اختر فروع</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="add_record_specific_sections[]" id="add_record_sections"
                                                    style="display: none;">
                                                    <option value="">اختر أقسام</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="add_record_specific_job_titles[]" id="add_record_job_titles"
                                                    style="display: none;">
                                                    <option value="">اختر مسميات وظيفية</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="add_record_specific_employees[]" id="add_record_employees"
                                                    style="display: none;">
                                                    <option value="">اختر موظفين</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="add_record_specific_job_roles[]" id="add_record_job_roles"
                                                    style="display: none;">
                                                    <option value="">اختر أدوار وظيفية</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- تعديل السجل -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>تعديل السجل <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="edit_record" id="edit_record"
                                                    required>
                                                    <option value="">اختر...</option>
                                                    <option value="all">الكل</option>
                                                    <option value="no">لا شيء</option>
                                                    <option value="specific_branches">أفرع محددة</option>
                                                    <option value="specific_sections">أقسام محددة</option>
                                                    <option value="specific_job_titles">مسميات وظيفية محددة</option>
                                                    <option value="specific_employees">موظفين محددين</option>
                                                    <option value="specific_job_roles">أدوار وظيفية محددة</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <select class="form-control specific-field"
                                                    name="edit_record_specific_branches[]" id="edit_record_branches"
                                                    style="display: none;">
                                                    <option value="">اختر فروع</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="edit_record_specific_sections[]" id="edit_record_sections"
                                                    style="display: none;">
                                                    <option value="">اختر أقسام</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="edit_record_specific_job_titles[]" id="edit_record_job_titles"
                                                    style="display: none;">
                                                    <option value="">اختر مسميات وظيفية</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="edit_record_specific_employees[]" id="edit_record_employees"
                                                    style="display: none;">
                                                    <option value="">اختر موظفين</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="edit_record_specific_job_roles[]" id="edit_record_job_roles"
                                                    style="display: none;">
                                                    <option value="">اختر أدوار وظيفية</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- عرض السجل -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>عرض السجل <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="view_record" id="view_record"
                                                    required>
                                                    <option value="">اختر...</option>
                                                    <option value="all">الكل</option>
                                                    <option value="no">لا شيء</option>
                                                    <option value="specific_branches">أفرع محددة</option>
                                                    <option value="specific_sections">أقسام محددة</option>
                                                    <option value="specific_job_titles">مسميات وظيفية محددة</option>
                                                    <option value="specific_employees">موظفين محددين</option>
                                                    <option value="specific_job_roles">أدوار وظيفية محددة</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <select class="form-control specific-field"
                                                    name="view_record_specific_branches[]" id="view_record_branches"
                                                    style="display: none;">
                                                    <option value="">اختر فروع</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="view_record_specific_sections[]" id="view_record_sections"
                                                    style="display: none;">
                                                    <option value="">اختر أقسام</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="view_record_specific_job_titles[]" id="view_record_job_titles"
                                                    style="display: none;">
                                                    <option value="">اختر مسميات وظيفية</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="view_record_specific_employees[]" id="view_record_employees"
                                                    style="display: none;">
                                                    <option value="">اختر موظفين</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="view_record_specific_job_roles[]" id="view_record_job_roles"
                                                    style="display: none;">
                                                    <option value="">اختر أدوار وظيفية</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- حذف السجل -->
                            <div class="row mb-2">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>حذف السجل <span class="text-danger">*</span></label>
                                        <div class="row">
                                            <div class="col-4">
                                                <select class="form-control" name="delete_record" id="delete_record"
                                                    required>
                                                    <option value="">اختر...</option>
                                                    <option value="all">الكل</option>
                                                    <option value="no">لا شيء</option>
                                                    <option value="specific_branches">أفرع محددة</option>
                                                    <option value="specific_sections">أقسام محددة</option>
                                                    <option value="specific_job_titles">مسميات وظيفية محددة</option>
                                                    <option value="specific_employees">موظفين محددين</option>
                                                    <option value="specific_job_roles">أدوار وظيفية محددة</option>
                                                </select>
                                            </div>
                                            <div class="col-8">
                                                <select class="form-control specific-field"
                                                    name="delete_record_specific_branches[]" id="delete_record_branches"
                                                    style="display: none;">
                                                    <option value="">اختر فروع</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="delete_record_specific_sections[]" id="delete_record_sections"
                                                    style="display: none;">
                                                    <option value="">اختر أقسام</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="delete_record_specific_job_titles[]"
                                                    id="delete_record_job_titles" style="display: none;">
                                                    <option value="">اختر مسميات وظيفية</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="delete_record_specific_employees[]" id="delete_record_employees"
                                                    style="display: none;">
                                                    <option value="">اختر موظفين</option>
                                                </select>
                                                <select class="form-control specific-field"
                                                    name="delete_record_specific_job_roles[]" id="delete_record_job_roles"
                                                    style="display: none;">
                                                    <option value="">اختر أدوار وظيفية</option>
                                                </select>
                                            </div>
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

@section('scripts')
    <script>
        $(document).ready(function() {
            // Function to handle select change
            function handleSelectChange(selectId) {
                $(`#${selectId}`).change(function() {
                    const value = $(this).val();
                    const prefix = selectId.replace('_record', '');

                    // Hide all specific fields for this select
                    $(`#${prefix}_record_branches, #${prefix}_record_sections, #${prefix}_record_job_titles, #${prefix}_record_employees, #${prefix}_record_job_roles`)
                        .hide();

                    // Show the specific field based on selection
                    switch (value) {
                        case 'specific_branches':
                            $(`#${prefix}_record_branches`).show();
                            break;
                        case 'specific_sections':
                            $(`#${prefix}_record_sections`).show();
                            break;
                        case 'specific_job_titles':
                            $(`#${prefix}_record_job_titles`).show();
                            break;
                        case 'specific_employees':
                            $(`#${prefix}_record_employees`).show();
                            break;
                        case 'specific_job_roles':
                            $(`#${prefix}_record_job_roles`).show();
                            break;
                    }
                });
            }

            // Initialize change handlers for all selects
            handleSelectChange('add_record');
            handleSelectChange('edit_record');
            handleSelectChange('view_record');
            handleSelectChange('delete_record');
        });
    </script>
@endsection
