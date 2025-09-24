@if(isset($paths) && !empty($paths) && $paths->count() > 0)
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>الاسم والكود</th>
                <th style="width: 15%;">الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paths as $path)
                <tr>
                    <td>
                        <div>{{ $path->name }}</div>
                        <small class="text-muted">#{{ $path->code }}</small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" type="button"
                                        data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu">
                                    <li>
                                        <a class="dropdown-item" href="{{ route('manufacturing.paths.show', $path->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('manufacturing.paths.edit', $path->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="#"
                                           data-toggle="modal" data-target="#modal_DELETE{{ $path->id }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </li>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>

                <!-- Modal delete -->
                <div class="modal fade text-left" id="modal_DELETE{{ $path->id }}" tabindex="-1"
                     role="dialog" aria-labelledby="myModalLabel{{ $path->id }}" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                        <div class="modal-content">
                            <div class="modal-header" style="background-color: #EA5455 !important;">
                                <h4 class="modal-title" id="myModalLabel{{ $path->id }}" style="color: #FFFFFF">
                                    حذف {{ $path->name }}
                                </h4>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true" style="color: #DC3545">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <strong>هل انت متاكد من انك تريد الحذف ؟</strong>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-light waves-effect waves-light" data-dismiss="modal">
                                    الغاء
                                </button>
                                <a href="{{ route('manufacturing.paths.delete', $path->id) }}"
                                   class="btn btn-danger waves-effect waves-light">
                                    تأكيد
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end delete-->

            @endforeach
        </tbody>
    </table>
@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا توجد مسارات انتاج تطابق معايير البحث المحددة.</p>
    </div>
@endif
