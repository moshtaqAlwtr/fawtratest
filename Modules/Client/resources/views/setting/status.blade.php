@extends('master')

@section('title')
  اضافة حالات العملاء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">  اضافة حالات العملاء</h2>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <a href="{{ route('clients.index') }}" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i> الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary" id="saveAllChanges">
                            <i class="fa fa-save"></i> حفظ التغييرات
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
                                    <th>اللون</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody id="statusTable">
                                @foreach($statuses as $status)
                                    <tr data-status-id="{{ $status->id }}">
                                        <td>
                                            <input type="text" class="form-control form-control-lg status-name" name="name" value="{{ $status->name }}" />
                                        </td>
                                        <td>
                                            <div class="custom-dropdown">
                                                <div class="dropdown-toggle" style="background-color: {{ $status->color }};"></div>
                                                <div class="dropdown-menu">
                                                    <div class="color-options">
                                                        @foreach(['#009688', '#4CAF50', '#F44336', '#FF9800', '#2196F3', '#9C27B0', '#673AB7', '#3F51B5', '#00BCD4', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF5722', '#795548', '#9E9E9E', '#607D8B', '#000000', '#FFFFFF'] as $color)
                                                            <div class="color-option" style="background-color: {{ $color }};" data-value="{{ $color }}"></div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <select class="form-control status-state" name="state">
                                                <option value="open" {{ $status->state == 'open' ? 'selected' : '' }}>مفتوح</option>
                                                <option value="closed" {{ $status->state == 'closed' ? 'selected' : '' }}>مغلق</option>
                                            </select>
                                        </td>
                                        <td>
                                            <button class="btn btn-warning btn-sm edit-btn" data-status-id="{{ $status->id }}">
                                                <i class="feather icon-edit"></i> تعديل
                                            </button>
                                            <button class="btn btn-danger btn-sm delete-btn" data-status-id="{{ $status->id }}">
                                                <i class="feather icon-trash"></i> حذف
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
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

    <!-- Edit Status Modal -->
    <div class="modal fade" id="editStatusModal" tabindex="-1" role="dialog" aria-labelledby="editStatusModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editStatusModalLabel">تعديل الحالة</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editStatusForm">
                        <div class="form-group">
                            <label for="editStatusName">اسم الحالة</label>
                            <input type="text" class="form-control" id="editStatusName" placeholder="اسم الحالة">
                        </div>
                        <div class="form-group">
                            <label for="editStatusColor">اللون</label>
                            <div class="custom-dropdown">
                                <div class="dropdown-toggle" id="editColorDisplay" style="background-color: #009688;"></div>
                                <div class="dropdown-menu">
                                    <div class="color-options">
                                        @foreach(['#009688', '#4CAF50', '#F44336', '#FF9800', '#2196F3', '#9C27B0', '#673AB7', '#3F51B5', '#00BCD4', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF5722', '#795548', '#9E9E9E', '#607D8B', '#000000', '#FFFFFF'] as $color)
                                            <div class="color-option" style="background-color: {{ $color }};" data-value="{{ $color }}"></div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="editStatusState">الحالة</label>
                            <select class="form-control" id="editStatusState">
                                <option value="open">مفتوح</option>
                                <option value="closed">مغلق</option>
                            </select>
                        </div>
                        <input type="hidden" id="editStatusId">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-primary" id="saveEditStatus">حفظ التغييرات</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Toggle dropdown visibility
        document.querySelectorAll('.dropdown-toggle').forEach(function(toggle) {
            toggle.addEventListener('click', function() {
                const menu = this.nextElementSibling;
                menu.classList.toggle('show');
            });
        });

        // Handle color selection
        function setupDropdowns() {
            document.querySelectorAll('.color-option').forEach(function(option) {
                option.addEventListener('click', function() {
                    const selectedColor = this.getAttribute('data-value');
                    const display = this.closest('.custom-dropdown').querySelector('.dropdown-toggle');
                    display.style.backgroundColor = selectedColor;
                    this.closest('.dropdown-menu').classList.remove('show'); // Hide options after selection
                });
            });
        }

        // Adding new status row
        document.getElementById('addNewStatus').addEventListener('click', function() {
            let newRow = `
                <tr>
                    <td>
                        <input type="text" class="form-control form-control-lg status-name" name="name" placeholder="اسم الحالة" />
                    </td>
                    <td>
                        <div class="custom-dropdown">
                            <div class="dropdown-toggle" style="background-color: #009688;"></div>
                            <div class="dropdown-menu">
                                <div class="color-options">
                                    @foreach(['#009688', '#4CAF50', '#F44336', '#FF9800', '#2196F3', '#9C27B0', '#673AB7', '#3F51B5', '#00BCD4', '#8BC34A', '#CDDC39', '#FFEB3B', '#FFC107', '#FF5722', '#795548', '#9E9E9E', '#607D8B', '#000000', '#FFFFFF'] as $color)
                                        <div class="color-option" style="background-color: {{ $color }};" data-value="{{ $color }}"></div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <select class="form-control status-state" name="state">
                            <option value="open">مفتوح</option>
                            <option value="closed">مغلق</option>
                        </select>
                    </td>
                    <td>
                        <button class="btn btn-danger btn-sm delete-btn">
                            <i class="feather icon-trash"></i> حذف
                        </button>
                    </td>
                </tr>
            `;
            document.getElementById('statusTable').insertAdjacentHTML('beforeend', newRow);
            setupDropdowns(); // Reinitialize dropdowns for new row
        });

        // Save all changes
        document.getElementById('saveAllChanges').addEventListener('click', function() {
            const rows = document.querySelectorAll('#statusTable tr');
            const data = [];

            rows.forEach(row => {
                const statusId = row.getAttribute('data-status-id');
                const name = row.querySelector('.status-name').value;
                const color = row.querySelector('.dropdown-toggle').style.backgroundColor;
                const state = row.querySelector('.status-state').value;

                data.push({
                    id: statusId,
                    name: name,
                    color: color,
                    state: state,
                });
            });

            // Send AJAX request to save all changes
            fetch('{{ route("statuses.updateAll") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ statuses: data }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تم حفظ التغييرات بنجاح');
                    window.location.reload(); // Reload the page
                } else {
                    alert('حدث خطأ أثناء حفظ التغييرات');
                }
            });
        });

        // Save new status
        document.getElementById('addNewStatus').addEventListener('click', function() {
            const name = document.querySelector('#statusTable tr:last-child .status-name').value;
            const color = document.querySelector('#statusTable tr:last-child .dropdown-toggle').style.backgroundColor;
            const state = document.querySelector('#statusTable tr:last-child .status-state').value;

            fetch('{{ route("statuses.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({ name: name, color: color, state: state }),
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('تم إضافة الحالة بنجاح');
                    window.location.reload(); // Reload the page
                } else {
                    alert('حدث خطأ أثناء إضافة الحالة');
                }
            });
        });

        // Initial setup for dropdowns
        setupDropdowns();
    </script>
@endsection

<style>
.custom-dropdown {
    position: relative;
}

.dropdown-toggle {
    width: 40px; /* Adjust width as needed */
    height: 40px; /* Adjust height as needed */
    border: 1px solid #ccc;
    cursor: pointer;
}

.dropdown-menu {
    display: none;
    position: absolute;
    top: 100%;
    left: 0;
    background-color: white;
    border: 1px solid #ccc;
    z-index: 1000;
}

.dropdown-menu.show {
    display: block;
}

.color-options {
    display: flex;
    flex-wrap: wrap;
    gap: 5px;
}

.color-option {
    width: 20px;
    height: 20px;
    cursor: pointer;
    border: 1px solid #ccc;
}
</style>
