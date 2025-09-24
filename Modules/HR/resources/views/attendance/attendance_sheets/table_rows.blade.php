{{-- resources/views/hr/attendance/attendance_sheets/partials/attendance_table.blade.php --}}

@if(isset($attendanceSheets) && !empty($attendanceSheets) && $attendanceSheets->count() > 0)
    <table class="table table-striped" dir="rtl">
        <thead class="table-light">
            <tr>
                <th scope="col">موظف</th>
                <th scope="col">من</th>
                <th scope="col">إلى</th>
                <th scope="col">أيام الحضور</th>
                <th scope="col">ساعات العمل الواقعية</th>
                <th scope="col">الحالة</th>
                <th scope="col">إجراء</th>
            </tr>
        </thead>
        <tbody>
            @php
                $uniqueEmployees = collect();
                foreach ($attendanceSheets as $attendanceSheet) {
                    foreach ($attendanceSheet->employees as $employee) {
                        $uniqueEmployees->push([
                            'id' => $attendanceSheet->id,
                            'employee_id' => $employee->id,
                            'full_name' => $employee->full_name,
                            'from_date' => $attendanceSheet->from_date,
                            'to_date' => $attendanceSheet->to_date,
                            'status' => $attendanceSheet->status,
                            'attendance_days' => $attendanceSheet->attendance_days ?? 0,
                            'actual_work_hours' => $attendanceSheet->actual_work_hours ?? 0,
                            'total_days' => $attendanceSheet->total_days ?? 0,
                            'total_hours' => $attendanceSheet->total_hours ?? 0,
                        ]);
                    }
                }
                $uniqueEmployees = $uniqueEmployees->unique('id');
            @endphp

            @foreach ($uniqueEmployees as $employee)
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar bg-light-primary me-2">
                                <i class="fas fa-user"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $employee['full_name'] }}</h6>
                                <small class="text-muted">ID: {{ $employee['employee_id'] }}</small>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">
                            {{ \Carbon\Carbon::parse($employee['from_date'])->format('Y/m/d') }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-light text-dark">
                            {{ \Carbon\Carbon::parse($employee['to_date'])->format('Y/m/d') }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold text-primary">
                            {{ $employee['attendance_days'] }}/{{ $employee['total_days'] }}
                        </span>
                    </td>
                    <td>
                        <span class="fw-bold text-success">
                            {{ $employee['actual_work_hours'] }}/{{ $employee['total_hours'] }}
                        </span>
                    </td>
                    <td>
                        @if($employee['status'] == 0)
                            <span class="status-badge status-pending">
                                <i class="fas fa-clock"></i>
                                تحت المراجعة
                            </span>
                        @else
                            <span class="status-badge status-approved">
                                <i class="fas fa-check-circle"></i>
                                موافق عليه
                            </span>
                        @endif
                    </td>
                       <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('attendance_sheets.show', $employee['id']) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('attendance_sheets.edit', $employee['id']) }}">
                                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $employee['id'] }}">
                                                                    <i class="fa fa-trash me-2"></i>حذف
                                                                </a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Pagination -->
    @if(method_exists($attendanceSheets, 'links'))
        <div class="d-flex justify-content-center mt-4">
            {{ $attendanceSheets->links() }}
        </div>
    @endif

@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <p class="mb-0">لا توجد دفاتر حضور مضافة حتى الآن!</p>
        <a href="{{ route('attendance_sheets.create') }}" class="btn btn-primary mt-3">
            <i class="fa fa-plus me-2"></i>أضف دفتر حضور جديد
        </a>
    </div>
@endif
