{{-- عمليات التصنيع --}}
@php
    $hasManufacturing = $order->manufacturOrdersItem->filter(function($item) {
        return $item->workStation;
    })->count() > 0;
@endphp
@if($hasManufacturing)
<div class="materials-card">
    <div class="materials-header">
        <strong><i class="fa fa-cogs"></i> عمليات التصنيع</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fa fa-industry me-1"></i>محطة العمل</th>
                        <th><i class="fa fa-tags me-1"></i>نوع التكلفة</th>
                        <th><i class="fa fa-clock me-1"></i>وقت التشغيل</th>
                        <th><i class="fa fa-route me-1"></i>مسار الإنتاج الفرعي</th>
                        <th><i class="fa fa-comment me-1"></i>الوصف</th>
                        <th><i class="fa fa-chart-line me-1"></i>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->manufacturOrdersItem as $item)
                        @if($item->workStation)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-industry text-warning me-2"></i>
                                    <strong>{{ $item->workStation->name }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($item->manu_cost_type == 1)
                                    <span class="badge badge-primary">مبلغ ثابت</span>
                                @elseif($item->manu_cost_type == 2)
                                    <span class="badge badge-warning">بناءً على الكمية</span>
                                @elseif($item->manu_cost_type == 3)
                                    <span class="badge badge-info">معادلة</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    {{ $item->operating_time ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $item->workshopProductionStage->stage_name ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $item->manu_description ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <strong class="text-primary">
                                    {{ number_format($item->manu_total_cost, 2) }} ر.س
                                </strong>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-primary">
                        <td colspan="5" class="text-right"><strong>إجمالي تكلفة التصنيع:</strong></td>
                        <td>
                            <strong class="text-primary">
                                {{ number_format($order->manufacturOrdersItem->where('workStation')->sum('manu_total_cost'), 2) }} ر.س
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif