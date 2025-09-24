{{-- المصروفات --}}
@php
    $hasExpenses = $order->manufacturOrdersItem->filter(function($item) {
        return $item->expensesAccount;
    })->count() > 0;
@endphp
@if($hasExpenses)
<div class="materials-card">
    <div class="materials-header">
        <strong><i class="fa fa-money-bill"></i> المصروفات</strong>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="thead-light">
                    <tr>
                        <th><i class="fa fa-building me-1"></i>الحساب</th>
                        <th><i class="fa fa-tags me-1"></i>نوع المصروف</th>
                        <th><i class="fa fa-coins me-1"></i>المبلغ</th>
                        <th><i class="fa fa-route me-1"></i>مسار الإنتاج الفرعي</th>
                        <th><i class="fa fa-comment me-1"></i>الوصف</th>
                        <th><i class="fa fa-chart-line me-1"></i>المجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->manufacturOrdersItem as $item)
                        @if($item->expensesAccount)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fa fa-building text-info me-2"></i>
                                    <strong>{{ $item->expensesAccount->name }}</strong>
                                </div>
                            </td>
                            <td>
                                @if($item->expenses_cost_type == 1)
                                    <span class="badge badge-primary">مبلغ ثابت</span>
                                @elseif($item->expenses_cost_type == 2)
                                    <span class="badge badge-warning">بناءً على الكمية</span>
                                @elseif($item->expenses_cost_type == 3)
                                    <span class="badge badge-info">معادلة</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-light">
                                    {{ number_format($item->expenses_price, 2) }} ر.س
                                </span>
                            </td>
                            <td>
                                <span class="text-muted">
                                    {{ $item->expensesProductionStage->stage_name ?? 'غير محدد' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $item->expenses_description ?? '-' }}
                                </small>
                            </td>
                            <td>
                                <strong class="text-danger">
                                    {{ number_format($item->expenses_total, 2) }} ر.س
                                </strong>
                            </td>
                        </tr>
                        @endif
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="table-warning">
                        <td colspan="5" class="text-right"><strong>إجمالي المصروفات:</strong></td>
                        <td>
                            <strong class="text-danger">
                                {{ number_format($order->manufacturOrdersItem->where('expensesAccount')->sum('expenses_total'), 2) }} ر.س
                            </strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endif