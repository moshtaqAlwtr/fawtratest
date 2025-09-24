<div class="card-title">
    <div class="d-flex justify-content-between align-items-center flex-wrap p-1">
        <div class="d-flex flex-wrap gap-2">
            <!-- الأزرار الأساسية (تظهر دائماً) -->
            <a href="{{ route('manufacturing.orders.edit', $order->id) }}" class="btn btn-outline-primary btn-sm action-btn">
                <i class="fa fa-edit"></i> تعديل
            </a>

            <a class="btn btn-sm btn-outline-danger action-btn" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $order->id }}">
                <i class="fa fa-trash me-2"></i> حذف
            </a>

            <a href="" class="btn btn-outline-secondary btn-sm action-btn">
                <i class="fa fa-copy"></i> نسخ
            </a>

            <!-- زر إضافة ملاحظة أو مرفق -->
            <div class="dropdown">
                <button class="btn btn-outline-info btn-sm action-btn dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    الملاحظات <i class="fa fa-paperclip ms-1"></i>
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="addNoteOrAttachment()">
                            <i class="fa fa-plus me-2 text-success"></i>إضافة ملاحظة جديدة</a>
                    </li>
                    <li><a class="dropdown-item" href="#" onclick="viewAllNotes()">
                            <i class="fa fa-list me-2 text-info"></i>عرض جميع الملاحظات</a>
                    </li>
                </ul>
            </div>

            {{-- الأزرار حسب حالة الأمر --}}
            @if($order->status == 'in_progress')
                {{-- أزرار الحالة "في التقدم" --}}
                <a href="" class="btn btn-outline-success btn-sm action-btn">
                    <i class="fa fa-arrow-down"></i> استلام الخامات
                </a>

                <a href="" class="btn btn-outline-warning btn-sm action-btn">
                    <i class="fa fa-undo"></i> إرجاع المواد
                </a>

                <a href="#" class="btn btn-outline-success btn-sm action-btn" data-toggle="modal" data-target="#finishOrderModal">
                    <i class="fa fa-check"></i> إنهاء الأمر
                </a>

            @elseif($order->status == 'completed')
                {{-- أزرار الحالة "مكتملة" --}}
                <form method="POST" action="{{ route('manufacturing.orders.undo-completion', $order->id) }}" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-outline-warning btn-sm action-btn confirm-undo">
                        <i class="fa fa-undo"></i> التراجع عن الإنهاء
                    </button>
                </form>

                <form method="POST" action="{{ route('manufacturing.orders.close', $order->id) }}" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-outline-dark btn-sm action-btn confirm-close">
                        <i class="fa fa-lock"></i> إغلاق
                    </button>
                </form>

            @elseif($order->status == 'closed')
                {{-- أزرار الحالة "مغلقة" --}}
                <form method="POST" action="{{ route('manufacturing.orders.reopen', $order->id) }}" class="d-inline">
                    @csrf
                    <button type="button" class="btn btn-outline-primary btn-sm action-btn confirm-reopen">
                        <i class="fa fa-unlock"></i> إعادة الفتح
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>