@extends('master')

@section('title')
    قائمة الحالات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قائمة الحالات</h2>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <a href="" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> الغاء
                        </a>
                        <button type="submit" form="statusForm" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> حفظ
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <form id="statusForm" action="{{ route('SupplyOrders.storeStatus') }}" method="POST">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th>الاسم</th>
                                        <th>اللون</th>
                                        <th>الحالة</th>
                                        <th>الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="statusTable">
                                    @foreach ($statuses as $status)
                                        <tr data-status-id="{{ $status->id }}">
                                            <td>
                                                <input type="text" name="statuses[{{ $status->id }}][name]"
                                                    class="form-control form-control-lg" placeholder="اسم الحالة"
                                                    value="{{ $status->name }}" required />
                                            </td>
                                            <td>
                                                <input type="color" name="statuses[{{ $status->id }}][color]"
                                                    class="form-control form-control-lg" value="{{ $status->color }}" />
                                            </td>
                                            <td>
                                                <select name="statuses[{{ $status->id }}][state]" class="form-control">
                                                    <option value="open" {{ $status->state == 'open' ? 'selected' : '' }}>مفتوح</option>
                                                    <option value="closed" {{ $status->state == 'closed' ? 'selected' : '' }}>مغلق</option>
                                                </select>
                                            </td>
                                            <td>
                                                @if($status->is_deletable)
                                                    <button type="button" class="btn btn-danger btn-sm delete-btn">
                                                        <i class="feather icon-trash"></i> حذف
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <button type="button" class="btn btn-success mt-2" id="addRow">
                            <i class="feather icon-plus"></i> إضافة حالة جديدة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة صف جديد
            document.getElementById('addRow').addEventListener('click', function() {
                let uniqueId = Date.now();
                let newRow = `
                    <tr>
                        <td>
                            <input type="text" name="statuses[${uniqueId}][name]"
                                   class="form-control form-control-lg"
                                   placeholder="اسم الحالة" required>
                        </td>
                        <td>
                            <input type="color" name="statuses[${uniqueId}][color]"
                                   class="form-control form-control-lg"
                                   value="#009688">
                        </td>
                        <td>
                            <select name="statuses[${uniqueId}][state]" class="form-control">
                                <option value="open">مفتوح</option>
                                <option value="closed">مغلق</option>
                            </select>
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm delete-btn">
                                <i class="feather icon-trash"></i> حذف
                            </button>
                        </td>
                    </tr>`;

                document.getElementById('statusTable').insertAdjacentHTML('beforeend', newRow);
            });

            // حذف صف
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('delete-btn')) {
                    if (confirm('هل أنت متأكد من حذف هذه الحالة؟')) {
                        event.target.closest('tr').remove();
                    }
                }
            });
        });
    </script>
@endsection
