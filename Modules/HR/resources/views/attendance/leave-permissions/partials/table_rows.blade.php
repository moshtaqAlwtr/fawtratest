
        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        @if(isset($leavePermissions) && !@empty($leavePermissions) && $leavePermissions->count() > 0)
                            <table class="table table-striped" dir="rtl">
                                <thead class="table-light">
                                    <tr>
                                        <th scope="col">إسم الموظف</th>
                                        <th scope="col">الرقم التعريفي للإذن</th>
                                        <th scope="col">التاريخ</th>
                                        <th scope="col">النوع</th>
                                        <th scope="col">اجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($leavePermissions as $leavePermission)
                                        <tr>
                                            <td>{{ $leavePermission->employee->full_name }} # {{ $leavePermission->employee->id }}</td>
                                            <td># {{ $leavePermission->id }}</td>
                                            <td>{{ $leavePermission->start_date }} - {{ $leavePermission->end_date }}</td>
                                            <td>{{ $leavePermission->leave_type == 1 ? 'اجازة اعتيادية' : 'اجازة عرضية' }}</td>
                                            <td>
                                                <div class="btn-group">
                                                    <div class="dropdown">
                                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"id="dropdownMenuButton303" data-toggle="dropdown" aria-haspopup="true"aria-expanded="false"></button>
                                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('leave_permissions.show', $leavePermission->id) }}">
                                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a class="dropdown-item" href="{{ route('leave_permissions.edit', $leavePermission->id) }}">
                                                                    <i class="fa fa-edit me-2 text-success"></i>تعديل
                                                                </a>
                                                            </li>

                                                            <li>
                                                                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $leavePermission->id }}">
                                                                    <i class="fa fa-trash me-2"></i>حذف
                                                                </a>
                                                            </li>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>

                                            <!-- Modal delete -->
                                            <div class="modal fade text-left" id="modal_DELETE{{ $leavePermission->id }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-scrollable" role="document">
                                                    <div class="modal-content">
                                                        <div class="modal-header" style="background-color: #EA5455 !important;">
                                                            <h4 class="modal-title" id="myModalLabel1" style="color: #FFFFFF">حذف أذونات إجازة</h4>
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
                                                            <a href="{{ route('leave_permissions.destroy',$leavePermission->id) }}" class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!--end delete-->

                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <div class="alert alert-danger text-xl-center" role="alert">
                                <p class="mb-0">
                                    لا توجد أذونات إجازة مضافة حتى الان !!
                                </p>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
