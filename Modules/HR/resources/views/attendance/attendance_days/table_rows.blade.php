{{-- إنشاء ملف منفصل: resources/views/hr/attendance/attendance_days/table_rows.blade.php --}}

@if(isset($attendance_days) && !empty($attendance_days) && $attendance_days->count() > 0)
    @foreach($attendance_days as $attendance_day)
        <tr>
            <td>{{ $attendance_day->employee->full_name }} #{{ $attendance_day->employee->id }}</td>
            <td>{{ \Carbon\Carbon::parse($attendance_day->attendance_date)->locale('ar')->translatedFormat('l, d/m/Y') }}</td>
            @if($attendance_day->status == 'present')
                <td>{{ $attendance_day->login_time }}</td>
                <td>{{ $attendance_day->logout_time }}</td>
                @php
                    $loginTime = \Carbon\Carbon::parse($attendance_day->login_time);
                    $logoutTime = \Carbon\Carbon::parse($attendance_day->logout_time);
                    $totalDuration = $loginTime->diff($logoutTime);
                @endphp
                <td>{{ $totalDuration->h }} ساعة و {{ $totalDuration->i }} دقيقة</td>
            @else
                <td>--</td>
                <td>--</td>
                <td>--</td>
            @endif
            <td>
                @if($attendance_day->status == 'present')
                    <span class="mr-1 bullet bullet-success bullet-sm"></span><span class="mail-date">حاضر</span>
                @elseif($attendance_day->status == 'absent')
                    <span class="mr-1 bullet bullet-secondary bullet-sm"></span><span class="mail-date">يوم اجازة (No Shift)</span>
                @else
                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><span class="mail-date">غياب</span>
                @endif
            </td>
            <td>
                <div class="btn-group">
                    <div class="dropdown">
                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button" id="dropdownMenuButton{{ $attendance_day->id }}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $attendance_day->id }}">
                            <li>
                                <a class="dropdown-item" href="{{ route('attendanceDays.show', $attendance_day->id) }}">
                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="{{ route('attendanceDays.edit', $attendance_day->id) }}">
                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $attendance_day->id }}">
                                    <i class="fa fa-trash me-2"></i>حذف
                                </a>
                            </li>
                        </div>
                    </div>
                </div>
            </td>

            <!-- Modal delete -->
            <div class="modal fade text-left" id="modal_DELETE{{ $attendance_day->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $attendance_day->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-scrollable" role="document">
                    <div class="modal-content">
                        <div class="modal-header" style="background-color: #EA5455 !important;">
                            <h4 class="modal-title" id="myModalLabel{{ $attendance_day->id }}" style="color: #FFFFFF">حذف يوم الحضور</h4>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true" style="color: #DC3545">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <strong>
                                هل انت متاكد من انك تريد الحذف ؟
                            </strong>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">الغاء</button>
                            <a href="{{ route('attendanceDays.delete', $attendance_day->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                        </div>
                    </div>
                </div>
            </div>
            <!--end delete-->

        </tr>
    @endforeach
@else
    <tr>
        <td colspan="7">
            <div class="alert alert-danger text-xl-center" role="alert">
                <p class="mb-0">
                    لا توجد سجلات حضور مطابقة للبحث !!
                </p>
            </div>
        </td>
    </tr>
@endif
