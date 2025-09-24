@extends('master')

@section('title')
    تعديل قائمة الحالات - أمر التوريد
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل قائمة الحالات - أمر التوريد</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
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
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>الاسم</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="statusTable">
                                <tr>
                                    <td>
                                        <div class="color-box" style="background-color: #009688;"></div>
                                        <input type="text" class="form-control form-control-lg" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="color-box" style="background-color: #4CAF50;"></div>
                                        <input type="text" class="form-control form-control-lg" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="color-box" style="background-color: #F44336;"></div>
                                        <input type="text" class="form-control form-control-lg" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="color-box" style="background-color: #FF9800;"></div>
                                        <input type="text" class="form-control form-control-lg" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <button class="btn btn-success mt-2" id="addNewStatus">
                        <i class="feather icon-plus"></i> إضافة اجراء جديدة
                    </button>

                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.getElementById('addNewStatus').addEventListener('click', function() {
            let newRow = `
                <tr>
                    <td>
                        <div class="color-box" style="background-color: #009688;"></div>
                        <input type="text" class="form-control form-control-lg" />
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn">
                            <i class="feather icon-trash"></i> حذف
                        </button>
                    </td>
                </tr>
            `;
            document.getElementById('statusTable').insertAdjacentHTML('beforeend', newRow);
        });

        document.addEventListener('click', function(e) {
            if (e.target && e.target.matches('.delete-btn')) {
                e.target.closest('tr').remove();
            }
        });
    </script>
@endsection
