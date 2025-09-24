@extends('master')

@section('title')
    حالات أوامر الحجز
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">حالات أوامر الحجز</h2>
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
                                    <th>النوع</th>
                                    <th>اللون</th>
                                    <th>حذف</th>
                                </tr>
                            </thead>
                            <tbody id="statusTable">
                                <tr>
                                    <td>مؤكد</td>
                                    <td>
                                        <select class="form-control">
                                            <option value="open">مفتوح</option>
                                            <option value="closed">مغلق</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="color" class="form-control" value="#009688" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>معلق</td>
                                    <td>
                                        <select class="form-control">
                                            <option value="open">مفتوح</option>
                                            <option value="closed">مغلق</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="color" class="form-control" value="#4CAF50" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>ملغي</td>
                                    <td>
                                        <select class="form-control">
                                            <option value="open">مفتوح</option>
                                            <option value="closed">مغلق</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="color" class="form-control" value="#F44336" />
                                    </td>
                                    <td>
                                        <button class="btn btn-danger btn-sm delete-btn">
                                            <i class="feather icon-trash"></i> حذف
                                        </button>
                                    </td>
                                </tr>
                                <tr>
                                    <td>منتهي</td>
                                    <td>
                                        <select class="form-control">
                                            <option value="open">مفتوح</option>
                                            <option value="closed">مغلق</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="color" class="form-control" value="#FF9800" />
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
                        <i class="feather icon-plus"></i> إضافة حالة جديدة
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
                    <td>جديد</td>
                    <td>
                        <select class="form-control">
                            <option value="open">مفتوح</option>
                            <option value="closed">مغلق</option>
                        </select>
                    </td>
                    <td>
                        <input type="color" class="form-control" value="#009688" />
                    </td>
                    <td>
                        <button class="btn btn-danger delete-btn">
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
