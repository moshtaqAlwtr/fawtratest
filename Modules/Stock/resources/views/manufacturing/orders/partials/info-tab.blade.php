<div class="tab-pane active" id="info" aria-labelledby="info-tab" role="tabpanel">
    {{-- معلومات أمر التصنيع --}}
    <div class="materials-card">
        <div class="materials-header">
            <strong><i class="fa fa-info-circle"></i> معلومات أمر التصنيع</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <table class="table">
                    <tbody>
                        <tr class="table-light">
                            <td>
                                <div class="d-flex justify-between">
                                    <div class="mr-1">
                                        @if ($order->product->images)
                                            <img src="{{ asset('assets/uploads/product/'.$order->product->images) }}" alt="img" width="100">
                                        @else
                                            <i class="fas fa-image text-muted" style="font-size: 100px"></i>
                                        @endif
                                    </div>
                                    <div class="mr-1">
                                        <p>المنتجات</p>
                                        <p><strong>{{ $order->product->name }}</strong></p>
                                        <p>
                                            <span class="hstack gap-3 d-inline-flex">
                                                <a style="text-decoration: underline" href="{{ route('products.show', $order->product->id) }}" class="text-primary">#<strong>{{ $order->product->serial_number }}</strong> <i class="fa fa-link"></i></a>
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p><small>الكمية المطلوبة</small></p>
                                <strong>{{ $order->quantity }}</strong>
                                @if($order->actual_quantity && $order->actual_quantity != $order->quantity)
                                    <br><small class="text-muted">الفعلية: {{ $order->actual_quantity }}</small>
                                @endif
                            </td>
                            <td>
                                <p><small>تاريخ البداية</small></p>
                                <strong>{{ $order->from_date }}</strong>
                            </td>
                            <td>
                                <p><small>تاريخ النهاية</small></p>
                                <strong>{{ $order->to_date }}</strong>
                                @if($order->finished_at)
                                    <br>
                                    <small class="text-success">
                                        تم الإنهاء: {{ \Carbon\Carbon::parse($order->finished_at)->format('Y-m-d') }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table">
                    <tbody>
                        <tr>
                            <td style="width: 50%">
                                <p><small>الاسم</small></p>
                                <strong>{{ $order->name }}</strong>
                            </td>
                            <td>
                                <p><small>مسار الإنتاج</small></p>
                                <strong>{{ $order->productionPath->name }} <a class="text-primary" style="text-decoration: underline" href="{{ route('manufacturing.paths.show', $order->productionPath->id) }}"># {{ $order->productionPath->code }} <i class="fa fa-link"></i></a></strong>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><small>الحساب</small></p>
                                <strong>{{ $order->account->name }}</strong>
                            </td>
                            <td>
                                <p><small>العميل</small></p>
                                <strong>{{ $order->client->trade_name }} <a class="text-primary" style="text-decoration: underline" href="{{ route('clients.show', $order->client->id) }}"># {{ $order->client->code }} <i class="fa fa-link"></i></a></strong>
                            </td>
                        </tr>
                        <tr>
                            <td style="width: 50%">
                                <p><small>قائمة مواد</small></p>
                                <strong>{{ $order->productionMaterial->name }} <a class="text-primary" style="text-decoration: underline" href="{{ route('Bom.show', $order->productionMaterial->id) }}"># {{ $order->productionMaterial->code }} <i class="fa fa-link"></i></a></strong>
                            </td>
                            <td>
                                <p><small>الموظف</small></p>
                                <strong>{{ $order->employee->full_name }} <a class="text-primary" style="text-decoration: underline" href="{{ route('Bom.show', $order->employee->id) }}"># {{ $order->employee->code }} <i class="fa fa-link"></i></a></strong>
                            </td>
                        </tr>
                        @if($order->isCompleted() && $order->finish_notes)
                        <tr>
                            <td colspan="2">
                                <p><small>ملاحظات الإنهاء</small></p>
                                <div class="alert alert-info">{{ $order->finish_notes }}</div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- المواد الخام --}}
    @include('stock::manufacturing.orders.partials.raw-materials', ['order' => $order])

    {{-- المصروفات --}}
    @include('stock::manufacturing.orders.partials.expenses', ['order' => $order])

    {{-- عمليات التصنيع --}}
    @include('stock::manufacturing.orders.partials.manufacturing-operations', ['order' => $order])

    {{-- المواد الهالكة --}}
    @include('stock::manufacturing.orders.partials.waste-materials', ['order' => $order])

    {{-- إجمالي التكلفة --}}
    <hr>
    <div class="row">
        <div class="form-group col-md-6"></div>
        <div class="form-group col-md-6">
            <div class="d-flex justify-content-between p-1" style="background: #CCF5FA;">
                <strong>التكلفة المبدئية/الفعلية : </strong>
                <strong class="total-cost">{{ number_format($order->last_total_cost, 2) }} ر.س</strong>
            </div>
        </div>
    </div>
</div>