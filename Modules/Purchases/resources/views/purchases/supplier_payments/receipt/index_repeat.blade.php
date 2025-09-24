

                                        @if(request()->query('type') == 'thermal')
                                            {{-- عرض الإيصال الحراري --}}
                                            @include('purchases::purchases.supplier_payments.receipt.pdf_receipt', ['receipt' => $receipt])
                                        @else
                                            {{-- عرض الإيصال A4 (القيمة الافتراضية) --}}
                                            @include('purchases::purchases.supplier_payments.receipt.pdf_repeatA4', ['receipt' => $receipt])
                                        @endif
