@extends('master')

@section('title')
    ادراة الاقسام
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">ادارة الاقسام</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">تصدير
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
                        <div></div>
                        <div class="d-flex justify-between">
                            <div class="vs-checkbox-con vs-checkbox-primary" style="padding: 0 12px">
                                <input type="checkbox" id="select-all" checked>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">تحديد الكل</span>
                            </div>

                            <button type="submit" form="export-form" class="btn btn-outline-info">
                                <i class="fa fa-share-square me-2"></i>  تصدير
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h5 class="card-title p-1" style="background: #f8f8f8">تصدير قسم</h5>
                    </div>
                    <div class="card-body">
                        <form id="export-form" action="{{ route('department.export') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="id">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">المعرف</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="name">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">الاسم</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="description">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">الوصف</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="status">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">الحالة</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="created_at">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">تاريخ الانشاء</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="updated_at">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">تاريخ اخر تعديل</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="short_name">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">الاختصار</span>
                                            </div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <fieldset>
                                            <div class="vs-checkbox-con vs-checkbox-primary">
                                                <input type="checkbox" class="checkbox-option" name="fields[]" checked value="managers">
                                                <span class="vs-checkbox">
                                                    <span class="vs-checkbox--check">
                                                        <i class="vs-icon feather icon-check"></i>
                                                    </span>
                                                </span>
                                                <span class="">المديرون</span>
                                            </div>
                                        </fieldset>
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
        document.addEventListener('DOMContentLoaded', function () {
            const selectAllCheckbox = document.getElementById('select-all');
            const checkboxes = document.querySelectorAll('.checkbox-option');

            selectAllCheckbox.addEventListener('change', function () {
                const isChecked = this.checked;
                checkboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });
            });

            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    if (!this.checked) {
                        selectAllCheckbox.checked = false;
                    } else if ([...checkboxes].every(chk => chk.checked)) {
                        selectAllCheckbox.checked = true;
                    }
                });
            });
        });
    </script>
@endsection
