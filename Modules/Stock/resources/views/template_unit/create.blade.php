@extends('master')

@section('title')
قوالب الوحدات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">قوالب الوحدات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">اضافة
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="container-fluid">
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

            <form class="form-horizontal" action="{{ route('template_unit.store') }}" method="POST">
                @csrf
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div>
                                <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                            </div>

                            <div>
                                <a href="{{ route('template_unit.index') }}" class="btn btn-outline-danger">
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
                            <h4 class="card-title">قوالب الوحدات</h4>
                        </div>

                        <div class="card-body">
                            <div class="form-body row">
                                <div class="form-group col-md-6">
                                    <label for="">اسم الوحدة الاساسية</label>
                                    <input type="text" id="datetime" class="form-control" name="base_unit_name" placeholder="مثال : جرام" value="{{ old('base_unit_name') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">التمييز</label>
                                    <input type="text" id="datetime" class="form-control" name="discrimination" placeholder="مثال : جم" value="{{ old('discrimination') }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="">القالب</label>
                                    <input type="text" id="datetime" class="form-control" name="template" placeholder="مثال : الوزن" value="{{ old('template') }}">
                                </div>

                                <div class="form-group col-md-2 mt-2">
                                    <div class="vs-checkbox-con vs-checkbox-primary">
                                        <input type="checkbox" name="status" value="1" checked {{ old('status') ? 'checked' : '' }}>
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">نشط</span>
                                    </div>
                                </div>

                                <div class="form-group col-md-12">
                                    <table class="table table-striped" id="itemsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>اسم الوحدة الأكبر</th>
                                                <th></th>
                                                <th>معامل التحويل</th>
                                                <th>التمييز</th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr id="firstRow">
                                                <td><input type="text" name="larger_unit_name[]" class="form-control larger_unit_name" placeholder="مثال : كيلوجرام" value="{{ old('larger_unit_name') }}"></td>
                                                <td><span class="conversion-text"></span></td>
                                                <td><input type="number" name="conversion_factor[]" class="form-control conversion_factor" placeholder="مثال : 1000" value="{{ old('conversion_factor') }}"></td>
                                                <td><input type="text" name="sub_discrimination[]" class="form-control sub_discrimination" placeholder="مثال : كجم" value="{{ old('sub_discrimination') }}"></td>
                                                <td>
                                                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" disabled><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <hr>
                                    <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
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
        document.addEventListener('DOMContentLoaded', function() {
            const itemsTable = document.getElementById('itemsTable');
            const addRowButton = document.getElementById('addRow');

            addRowButton.addEventListener('click', function() {
                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><input type="text" name="larger_unit_name[]" class="form-control larger_unit_name" placeholder="مثال : كيلوجرام"></td>
                    <td><span class="conversion-text"></span></td>
                    <td><input type="number" name="conversion_factor[]" class="form-control conversion_factor" placeholder="مثال : 1000"></td>
                    <td><input type="text" name="sub_discrimination[]" class="form-control sub_discrimination" placeholder="مثال : كجم"></td>
                    <td>
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-minus"></i></button>
                    </td>
                `;
                itemsTable.querySelector('tbody').appendChild(newRow);

                const largerUnitInput = newRow.querySelector('.larger_unit_name');
                const conversionFactorInput = newRow.querySelector('.conversion_factor');
                const conversionText = newRow.querySelector('.conversion-text');

                largerUnitInput.addEventListener('input', updateConversionText);
                conversionFactorInput.addEventListener('input', updateConversionText);

                newRow.querySelector('.removeRow').addEventListener('click', function() {
                    newRow.remove();
                });

                function updateConversionText() {
                    const largerUnit = largerUnitInput.value || 'الوحدة الأكبر';
                    conversionText.textContent = `1 ${largerUnit} يساوي`;
                }
            });

            itemsTable.querySelectorAll('.removeRow').forEach(button => {
                button.addEventListener('click', function() {
                    const row = button.closest('tr');
                    if (row.id !== 'firstRow') {
                        row.remove();
                    }
                });
            });

            document.querySelectorAll('.larger_unit_name, .conversion_factor').forEach(input => {
                input.addEventListener('input', function() {
                    const row = input.closest('tr');
                    const largerUnit = row.querySelector('.larger_unit_name').value || 'الوحدة الأكبر';
                    row.querySelector('.conversion-text').textContent = `1 ${largerUnit} يساوي`;
                });
            });
        });
    </script>
@endsection
