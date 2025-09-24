{{-- تبويب سجل النشاطات --}}
<div class="tab-pane" id="activities" aria-labelledby="activities-tab" role="tabpanel">
    <div class="row mt-4">
        <div class="col-12">
            {{-- Loading للوغز --}}
            <div id="logsLoading" class="text-center p-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">جاري تحميل السجلات...</span>
                </div>
                <p class="mt-2">جاري تحميل سجل النشاطات...</p>
            </div>

            {{-- محتوى السجلات --}}
            <div id="logsContent">
                @if (isset($logs) && count($logs) > 0)
                    @php
                        $previousDate = null;
                    @endphp

                    @foreach ($logs as $date => $dayLogs)
                        @php
                            $currentDate = \Carbon\Carbon::parse($date);
                            $diffInDays = $previousDate ? $previousDate->diffInDays($currentDate) : 0;
                        @endphp

                        @if ($diffInDays > 7)
                            <div class="timeline-date">
                                <h4>{{ $currentDate->format('Y-m-d') }}</h4>
                            </div>
                        @endif

                        <div class="timeline-day">{{ $currentDate->translatedFormat('l') }}</div>

                        <ul class="timeline">
                            @foreach ($dayLogs as $log)
                                @if ($log)
                                    <li class="timeline-item">
                                        <div class="timeline-content">
                                            <div class="time">
                                                <i class="far fa-clock"></i>
                                                {{ $log->created_at->format('H:i:s') }}
                                            </div>
                                            <div>
                                                <strong>{{ $log->user->name ?? 'مستخدم غير معروف' }}</strong>
                                                {!! Str::markdown($log->description ?? 'لا يوجد وصف') !!}
                                                <div class="text-muted">
                                                    {{ $log->user->branch->name ?? 'فرع غير معروف' }}
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>

                        @php
                            $previousDate = $currentDate;
                        @endphp
                    @endforeach
                @else
                    <div class="alert alert-info text-center" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        <p class="mb-0">لا توجد سجلات نشاط حتى الآن!</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>