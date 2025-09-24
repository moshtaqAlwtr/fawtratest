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
                        <th>محطة العمل</th>
                        <th>نوع التكلفة</th>
                        <th>وقت التشغيل</th>
                        <th>مسار الإنتاج الفرعي</th>
                        <th>الوصف</th>
                        <th>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->manufacturOrdersItem as $item)
                        @if($item->workStation)
                        <tr>
                            <td>{{ $item->workStation->name }}</td>
                            <td>
                                @if($item->manu_cost_type == 1)
                                مبلغ ثابت
                                @elseif($item->manu_cost_type == 2)
                                بناءً على الكمية
                                @elseif($item->manu_cost_type == 3)
                                معادلة
                                @endif
                            </td>
                            <td>{{ $item->operating_time }}</td>
                            <td>{{ $item->workshopProductionStage->stage_name ?? 'غير محدد' }}</td>
                            <td>{{ $item->manu_description }}</td>
                            <td>{{ number_format($item->manu_total_cost, 2) }} ر.س</td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif