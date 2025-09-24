@extends('master')

@section('title')
حالات طلبات التصنيع
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">حالات طلبات التصنيع </h2>
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
                    <button type="submit" form="general_form" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>حفظ
                    </button>
                </div>

            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card">
            <div class="card-header" style="background-color: #f8f8f8">
                <strong class="mb-1">حالات طلبات التصنيع</strong>
            </div>
                <form id="general_form" action="{{ route('Manufacturing.order_manual_status.update') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="custom-control custom-switch custom-switch-success custom-control-inline">
                            <input type="checkbox" class="custom-control-input" id="customSwitch1" name="active" value="1" {{ optional($order_manual_status)->active == 1 ? 'checked' : '' }}>
                            <label class="custom-control-label" for="customSwitch1"></label>
                            <span class="switch-label">تفعيل حالات طلبات التصنيع</span>
                        </div>
                    </div>

                    <br>

                    <div class="form-group col-md-12">
                        <p onclick="toggleSection('rawMaterials')" class="d-flex justify-content-between section-header" style="background: #DBDEE2; width: 100%;">
                            <span class="p-1 font-weight-bold"><i class="feather icon-package mr-1"></i> الحالات اليدوية لطلبات التصنيع (<span id="rawMaterialCount">1</span>)</span>
                            <i class="feather icon-plus-circle p-1"></i>
                        </p>
                        <div id="rawMaterials">
                            <table class="table table-striped" id="itemsTable">
                                <thead style="background: #f8f8f8">
                                    <tr>
                                        <th>رقم</th>
                                        <th>الاسم</th>
                                        <th>اللون</th>
                                        <th></th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach(optional($order_manual_status)->manualOrderStatus ?? [] as $key => $manualStatus)
                                        <tr>
                                            <td class="row-number">{{ $key + 1 }}</td>
                                            <td><input type="text" name="name[]" class="form-control manualUnitName" value="{{ $manualStatus->name }}"></td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-8">
                                                        <input type="text" disabled class="form-control colorDisplay" value="{{ $manualStatus->color }}">
                                                        <input type="hidden" name="color[]" class="form-control colorHidden" value="{{ $manualStatus->color }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <input type="text" class="colorPicker form-control" value="{{ $manualStatus->color }}">
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>

                            </table>
                            <hr>
                            <div class="d-flex justify-content-between mt-2">
                                <button type="button" class="btn btn-outline-success btn-sm" id="addRow"><i class="fa fa-plus"></i> إضافة</button>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(document).ready(function () {
            $(".colorPicker").spectrum({
                showPalette: true,
                showInput: true,
                preferredFormat: "hex",
                palette: [
                    ["#9a4d40", "#f44336", "#e91e63", "#9c27b0", "#673ab7"],
                    ["#3f51b5", "#2196f3", "#03a9f4", "#00bcd4", "#009688"],
                    ["#4caf50", "#8bc34a", "#cddc39", "#ffeb3b", "#ffc107"]
                ],
                i18n: {
                    cancelText: "إلغاء",
                    chooseText: "اختيار اللون",
                    clearText: "مسح اللون",
                    noColorSelectedText: "لم يتم اختيار لون"
                },

                change: function (color) {
                    let row = $(this).closest("tr");
                    row.find(".colorDisplay").val(color.toHexString());
                    row.find(".colorHidden").val(color.toHexString());
                }

            });
        });
    </script>

    <script>
        function updateRawMaterialCount() {
            const rowCount = document.querySelectorAll('#itemsTable tbody tr').length;
            document.getElementById('rawMaterialCount').textContent = rowCount;
        }

        function updateRowNumbers() {
            document.querySelectorAll("#itemsTable tbody tr").forEach((row, index) => {
                row.querySelector(".row-number").textContent = index + 1;
            });
        }

        function toggleSection(sectionId) {
            const section = document.getElementById(sectionId);
            section.style.display = section.style.display === "none" ? "block" : "none";
        }

        document.addEventListener('DOMContentLoaded', function () {
            const itemsTable = document.getElementById('itemsTable').querySelector('tbody');
            const addRowButton = document.getElementById('addRow');

            addRowButton.addEventListener('click', function (e) {
                e.preventDefault();

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td style="width: 10%" class="row-number"></td> <!-- سيتم تحديث الرقم لاحقًا -->
                    <td><input type="text" name="name[]" class="form-control unit-price"></td>
                    <td>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="text" disabled class="form-control colorDisplay" value="#9a4d40">
                                <input type="hidden" name="color[]" class="form-control colorHidden" value="#9a4d40">
                            </div>

                            <div class="col-md-2">
                                <input type="text" class="colorPicker form-control" value="#9a4d40">
                            </div>
                        </div>
                    </td>
                    <td style="width: 10%">
                        <button type="button" class="btn btn-outline-danger btn-sm removeRow"><i class="fa fa-trash"></i></button>
                    </td>
                `;

                itemsTable.appendChild(newRow);
                updateRawMaterialCount();
                updateRowNumbers();

                // تهيئة مكتبة spectrum لعناصر اللون الجديدة فقط
                $(newRow).find(".colorPicker").spectrum({
                    showPalette: true,
                    showInput: true,
                    preferredFormat: "hex",
                    palette: [
                        ["#9a4d40", "#f44336", "#e91e63", "#9c27b0", "#673ab7"],
                        ["#3f51b5", "#2196f3", "#03a9f4", "#00bcd4", "#009688"],
                        ["#4caf50", "#8bc34a", "#cddc39", "#ffeb3b", "#ffc107"]
                    ],
                    i18n: {
                        cancelText: "إلغاء",
                        chooseText: "اختيار اللون",
                        clearText: "مسح اللون",
                        noColorSelectedText: "لم يتم اختيار لون"
                    },

                    change: function (color) {
                        let row = $(this).closest("tr");
                        row.find(".colorDisplay").val(color.toHexString());
                        row.find(".colorHidden").val(color.toHexString());
                    }

                });

            });

            itemsTable.addEventListener('click', function (e) {
                if (e.target.closest('.removeRow')) {
                    const row = e.target.closest('tr');
                    if (itemsTable.rows.length > 1) {
                        row.remove();
                        updateRawMaterialCount();
                        updateRowNumbers();
                    } else {
                        Swal.fire({
                            icon: 'warning',
                            text: 'لا يمكنك حذف جميع الصفوف!',
                            confirmButtonText: 'حسناً',
                            confirmButtonColor: '#d33'
                        });
                    }
                }
            });
        });
    </script>

@endsection
