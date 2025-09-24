@if(isset($leave_policies) && !empty($leave_policies) && $leave_policies->count() > 0)
    <table class="table table-striped">
        <thead class="table-light">
            <tr>
                <th scope="col">الاسم</th>
                <th scope="col">أنواع الإجازات</th>
                <th scope="col">الموظفين</th>
                <th scope="col">الحالة</th>
                <th scope="col">اجراء</th>
            </tr>
        </thead>
        <tbody>
            @foreach($leave_policies as $leave_policy)
                <tr>
                    <td>{{ $leave_policy->name }}</td>
                    <td>
                        <span class="badge badge-info">
                            {{ $leave_policy->leaveType()->count() }} نوع
                        </span>
                    </td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary show-employees-modal"
                                data-policy-id="{{ $leave_policy->id }}"
                                data-policy-name="{{ $leave_policy->name ?? '' }}">
                            <i class="fa fa-users me-1"></i>
                            <span class="employees-count-{{ $leave_policy->id }}">جاري التحميل...</span>
                        </button>
                    </td>
                    <td>
                        @if($leave_policy->status == 0)
                            <span class="mr-1 bullet bullet-success bullet-sm"></span>
                            <span class="mail-date">نشط</span>
                        @else
                            <span class="mr-1 bullet bullet-danger bullet-sm"></span>
                            <span class="mail-date">غير نشط</span>
                        @endif
                    </td>
                    <td style="width: 10%">
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button"
                                        id="dropdownMenuButton{{ $leave_policy->id }}"
                                        data-toggle="dropdown"
                                        aria-haspopup="true"
                                        aria-expanded="false"></button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $leave_policy->id }}">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('leave_policy.show', $leave_policy->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('leave_policy.edit', $leave_policy->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('leave_policy.leave_policy_employees', $leave_policy->id) }}">
                                            <i class="fa fa-users me-2 text-info"></i>إدارة الموظفين
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item show-employees-modal"
                                           href="#"
                                           data-policy-id="{{ $leave_policy->id }}"
                                           data-policy-name="{{ $leave_policy->name }}">
                                            <i class="fa fa-list me-2 text-warning"></i>عرض الموظفين
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger"
                                           href="#"
                                           data-toggle="modal"
                                           data-target="#modal_DELETE{{ $leave_policy->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>

                    <!-- Modal delete -->
                    <div class="modal fade text-left"
                         id="modal_DELETE{{ $leave_policy->id }}"
                         tabindex="-1"
                         role="dialog"
                         aria-labelledby="myModalLabel{{ $leave_policy->id }}"
                         aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable" role="document">
                            <div class="modal-content">
                                <div class="modal-header" style="background-color: #EA5455 !important;">
                                    <h4 class="modal-title"
                                        id="myModalLabel{{ $leave_policy->id }}"
                                        style="color: #FFFFFF">
                                        حذف {{ $leave_policy->name }}
                                    </h4>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true" style="color: #FFFFFF">&times;</span>
                                    </button>
                                </div>
                                <div class="modal-body">
                                    <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                                    <p class="text-muted mt-2">
                                        <i class="fa fa-exclamation-triangle text-warning"></i>
                                        سيتم حذف جميع البيانات المرتبطة بهذه السياسة
                                    </p>
                                </div>
                                <div class="modal-footer">
                                    <button type="button"
                                            class="btn btn-light waves-effect waves-light"
                                            data-dismiss="modal">الغاء</button>
                                    <a href="{{ route('leave_policy.delete',$leave_policy->id) }}"
                                       class="btn btn-danger waves-effect waves-light">تأكيد</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!--end delete-->
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- تحميل عدد الموظفين لكل سياسة بعد تحميل الجدول -->
    <script>
        $(document).ready(function() {
            @foreach($leave_policies as $leave_policy)
                // تحميل عدد الموظفين لكل سياسة
                $.ajax({
                    url: '/leave-policies/{{ $leave_policy->id }}/employees-count',
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            $('.employees-count-{{ $leave_policy->id }}').text(response.count + ' موظف');
                        } else {
                            $('.employees-count-{{ $leave_policy->id }}').text('0 موظف');
                        }
                    },
                    error: function() {
                        $('.employees-count-{{ $leave_policy->id }}').text('خطأ');
                    }
                });
            @endforeach
        });
    </script>

@else
    <div class="alert alert-danger text-xl-center" role="alert">
        <p class="mb-0">
            <i class="fa fa-exclamation-triangle me-2"></i>
            لا توجد سياسات إجازة مضافة حتى الان !!
        </p>
        <div class="mt-3">
            <a href="{{ route('leave_policy.create') }}" class="btn btn-primary">
                <i class="fa fa-plus me-2"></i>إضافة سياسة جديدة
            </a>
        </div>
    </div>
@endif