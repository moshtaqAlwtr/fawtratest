{{-- resources/views/stock/products/partials/activity_logs.blade.php --}}
@if($logs->count() > 0)
    <ul class="activity-timeline timeline-left list-unstyled">
        @foreach($logs as $log)
            <li>
                <!-- أيقونة النشاط -->
                <div class="timeline-icon bg-{{ $log->type_log == 'create' ? 'success' : 'warning' }}">
                    <i class="feather icon-{{ $log->type_log == 'create' ? 'plus' : 'edit' }} font-medium-2"></i>
                </div>

                <!-- معلومات النشاط -->
                <div class="timeline-info">
                    <p class="mb-1">
                        <!-- المستخدم والتاريخ -->
                        <span class="badge badge-pill badge-dark">{{ $log->user->name ?? "مستخدم غير معروف" }}</span>
                        قام بـ {{ $log->type_log == 'create' ? 'إضافة' : 'تعديل' }} منتج:
                        <span class="badge badge-pill badge-dark">{{ $log->Product->name ?? "" }} {{ $log->Product->code ?? "" }}</span>
                        بتاريخ <span class="badge badge-pill badge-dark">{{ $log->created_at->format('Y-m-d H:i') ?? "" }}</span>
                    </p>

                    <!-- تفاصيل النشاط -->
                    <div class="details mt-2">
                        @if($log->type_log == 'create')
                            <!-- تفاصيل الإضافة -->
                            <div class="mb-2">
                                <span class="badge badge-pill badge-success">الاسم: {{ $log->Product->name ?? "" }}</span>
                                <span class="badge badge-pill badge-success">سعر البيع: {{ $log->Product->sale_price ?? "" }}</span>
                                <span class="badge badge-pill badge-success">سعر الشراء: {{ $log->Product->purchase_price ?? "" }}</span>
                                <span class="badge badge-pill badge-success">الرقم التسلسلي: {{ $log->Product->serial_number ?? "" }}</span>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-pill badge-success">الوصف: {{ $log->Product->description ?? "لا يوجد" }}</span>
                                <span class="badge badge-pill badge-success">الضريبة: القيمة المضافة</span>
                                <span class="badge badge-pill badge-success">تتبع المخزن: نعم</span>
                            </div>
                            <div class="mb-2">
                                <span class="badge badge-pill badge-success">متوسط السعر: {{ $log->Product->min_sale_price ?? "غير محدد" }}</span>
                                <span class="badge badge-pill badge-success">
                                    النوع:
                                    @switch($log->Product->type)
                                        @case('products') منتج @break
                                        @case('services') خدمة @break
                                        @case('compiled') منتج تجميعي @break
                                        @default غير محدد
                                    @endswitch
                                </span>
                                <span class="badge badge-pill badge-success">
                                    نوع الخصم:
                                    @switch($log->Product->discount_type)
                                        @case(1) نسبة مئوية @break
                                        @case(2) قيمة ثابتة @break
                                        @default غير محدد
                                    @endswitch
                                </span>
                                <span class="badge badge-pill badge-success">
                                    الحالة:
                                    @switch($log->Product->status)
                                        @case(1) نشط @break
                                        @case(2) موقوف @break
                                        @case(3) غير نشط @break
                                        @default غير محدد
                                    @endswitch
                                </span>
                            </div>
                        @else
                            <!-- تفاصيل التعديل -->
                            <div class="mb-2">
                                <span class="badge badge-pill badge-success">
                                    {{ $log->Product->name ?? '' }}
                                </span>
                                <span class="mx-2">→</span>
                                <span class="badge badge-pill badge-secondary text-decoration-line-through">
                                    {{ $log->old_value ?? '' }}
                                </span>
                            </div>
                        @endif
                    </div>
                </div>
            </li>
            <hr>
        @endforeach
    </ul>

    {{-- Pagination --}}
@if($logs->hasPages())
    <nav aria-label="Activity logs pagination">
        <ul class="pagination justify-content-center">

            {{-- First Page Link --}}
            @if ($logs->onFirstPage())
                <li class="page-item disabled"><span class="page-link">الأول</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadActivityLogs(1)">الأول</a>
                </li>
            @endif

            {{-- Previous Page Link --}}
            @if ($logs->onFirstPage())
                <li class="page-item disabled"><span class="page-link">السابق</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadActivityLogs({{ $logs->currentPage() - 1 }})">السابق</a>
                </li>
            @endif

            {{-- Current Page --}}
            <li class="page-item active"><span class="page-link">{{ $logs->currentPage() }}</span></li>

            {{-- Next Page Link --}}
            @if ($logs->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadActivityLogs({{ $logs->currentPage() + 1 }})">التالي</a>
                </li>
            @else
                <li class="page-item disabled"><span class="page-link">التالي</span></li>
            @endif

            {{-- Last Page Link --}}
            @if ($logs->currentPage() == $logs->lastPage())
                <li class="page-item disabled"><span class="page-link">الأخير</span></li>
            @else
                <li class="page-item">
                    <a class="page-link" href="#" onclick="loadActivityLogs({{ $logs->lastPage() }})">الأخير</a>
                </li>
            @endif

        </ul>
    </nav>
@endif

@else
    <!-- رسالة إذا لم توجد سجلات -->
    <div class="alert alert-danger text-center" role="alert">
        <p class="mb-0">لا توجد عمليات مضافة حتى الآن!</p>
    </div>
@endif
