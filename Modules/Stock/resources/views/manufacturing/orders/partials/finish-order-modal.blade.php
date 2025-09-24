{{-- Modal إنهاء أمر التصنيع --}}
@if(!$order->isCompleted() && $order->status !== 'closed')
<div class="modal fade" id="finishOrderModal" tabindex="-1" role="dialog" aria-labelledby="finishOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #28a745;">
                <h4 class="modal-title text-white" id="finishOrderModalLabel">
                    <i class="fa fa-check-circle me-2"></i>إنهاء أمر التصنيع
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('manufacturing.orders.finish', $order->id) }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fa fa-info-circle me-2"></i>
                        <strong>ملاحظة:</strong> سيتم تحديث المخزون وإضافة المنتج النهائي والمواد الهالكة للمستودعات المحددة.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="main_warehouse">
                                    <i class="fa fa-warehouse text-primary me-1"></i>
                                    مستودع المنتج الرئيسي <span class="text-danger">*</span>
                                </label>
                                <select name="main_warehouse_id" id="main_warehouse" class="form-control" required>
                                    <option value="">اختر المستودع</option>
                                    @foreach($storehouse as $storehous)
                                        <option value="{{ $storehous->id }}">{{ $storehous->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="waste_warehouse">
                                    <i class="fa fa-trash text-warning me-1"></i>
                                    مستودع المواد الهالكة <span class="text-danger">*</span>
                                </label>
                                <select name="waste_warehouse_id" id="waste_warehouse" class="form-control" required>
                                    <option value="">اختر المستودع</option>
                                    @foreach($storehouse as $storehous)
                                        <option value="{{ $storehous->id }}">{{ $storehous->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="delivery_date">
                                    <i class="fa fa-calendar text-success me-1"></i>
                                    تاريخ التسليم <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="delivery_date" id="delivery_date"
                                       class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="actual_quantity">
                                    <i class="fa fa-calculator text-info me-1"></i>
                                    الكمية الفعلية المنتجة
                                </label>
                                <input type="number" name="actual_quantity" id="actual_quantity"
                                       class="form-control" value="{{ $order->quantity }}"
                                       min="0" step="0.01" placeholder="الكمية المنتجة فعلياً">
                                <small class="text-muted">الكمية المطلوبة: {{ $order->quantity }}</small>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="notes">
                                    <i class="fa fa-sticky-note text-secondary me-1"></i>
                                    ملاحظات الإنهاء
                                </label>
                                <textarea name="notes" id="notes" class="form-control" rows="3"
                                          placeholder="أي ملاحظات إضافية حول عملية الإنهاء..."></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="border-top pt-3 mt-3">
                        <h6 class="text-muted">
                            <i class="fa fa-info-circle me-1"></i>معلومات الأمر:
                        </h6>
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted">المنتج:</small><br>
                                <strong>{{ $order->product->name }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">الكمية المطلوبة:</small><br>
                                <strong>{{ $order->quantity }}</strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted">التكلفة المقدرة:</small><br>
                                <strong>{{ number_format($order->last_total_cost, 2) }} ر.س</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fa fa-times me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fa fa-check me-1"></i>تأكيد إنهاء الأمر
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif