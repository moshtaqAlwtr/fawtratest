

                                        @if(request()->query('type') == 'thermal')
                                            {{-- عرض الإيصال الحراري --}}
                                            @include('sales::payment.receipt.pdf_receipt', ['receipt' => $receipt])
                                        @else
                                            {{-- عرض الإيصال A4 (القيمة الافتراضية) --}}
                                            @include('sales::payment.receipt.pdf_repeatA4', ['receipt' => $receipt])
                                        @endif
