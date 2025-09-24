<!-- resources/views/hr/attendance/leave_requests/partials/table.blade.php -->
<div class="card mt-4">
    <div class="table-responsive">
        <table class="table table-striped table-hover table-bordered" dir="rtl">
            <thead class="table-light">
                <tr>
                    <th scope="col">الموظف</th>
                    <th scope="col">تاريخ الطلب</th>
                    <th scope="col">نوع الإجازة</th>
                    <th scope="col">المدة</th>
                    <th scope="col">الفترة</th>
                    <th scope="col">الحالة</th>
                    <th scope="col">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @if(isset($leaveRequests) && $leaveRequests->count() > 0)
                    @foreach($leaveRequests as $request)
                        <tr>
                            <td>
                                {{ $request->employee->full_name }}
                                #{{ $request->employee->id }}
                            </td>
                            <td>{{ \Carbon\Carbon::parse($request->created_at)->locale('ar')->translatedFormat('l, d/m/Y') }}</td>
                            <td>{{ $request->leaveType->name }}</td>
                            <td>{{ $request->days_count }} أيام</td>
                            <td>
                                من {{ \Carbon\Carbon::parse($request->start_date)->format('d/m/Y') }}
                                الى {{ \Carbon\Carbon::parse($request->end_date)->format('d/m/Y') }}
                            </td>
                            <td>
                                @if($request->status == 'approved')
                                    <span class="mr-1 bullet bullet-success bullet-sm"></span><span class="mail-date">موافق عليه</span>
                                @elseif($request->status == 'pending')
                                    <span class="mr-1 bullet bullet-secondary bullet-sm"></span><span class="mail-date">تحت المراجعة</span>
                                @elseif($request->status == 'rejected')
                                    <span class="mr-1 bullet bullet-danger bullet-sm"></span><span class="mail-date">مرفوض</span>
                                @else
                                    <span class="mr-1 bullet bullet-dark bullet-sm"></span><span class="mail-date">غير محدد</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                            type="button" id="dropdownMenuButton{{ $request->id }}"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $request->id }}">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('attendance.leave_requests.show', $request->id) }}">
                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                </a>
                                            </li>
                                            @if($request->status == 'pending')
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('attendance.leave_requests.edit', $request->id) }}">
                                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                    </a>
                                                </li>
                                            @endif
                                            @if(in_array($request->status, ['pending','rejected']) && (auth()->id() == $request->employee_id || auth()->user()->hasRole('admin')))
                                                <li>
                                                    <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $request->id }}">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </a>
                                                </li>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Modal delete -->
                            <div class="modal fade text-left" id="modal_DELETE{{ $request->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel{{ $request->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header" style="background-color: #EA5455 !important;">
                                            <h4 class="modal-title" id="myModalLabel{{ $request->id }}" style="color: #FFFFFF">حذف طلب إجازة</h4>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <strong>
                                                هل أنت متأكد من رغبتك في الحذف ؟
                                            </strong>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">إلغاء</button>
                                            <a href="{{ route('attendance.leave_requests.destroy', $request->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
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
                                    لا توجد طلبات إجازة مطابقة للبحث !!
                                </p>
                            </div>
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
