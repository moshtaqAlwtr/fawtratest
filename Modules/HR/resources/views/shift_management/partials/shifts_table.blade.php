@if (@isset($shifts) && !@empty($shifts) && count($shifts) > 0)
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>النوع</th>
                    <th>أيام العمل</th>
                    <th>أيام العطل</th>
                    <th style="width: 10%">إجراء</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shifts as $shift)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar bg-light-primary mr-1">
                                    <div class="avatar-content">
                                        <i class="fa fa-clock text-primary font-medium-3"></i>
                                    </div>
                                </div>
                                <div class="d-flex flex-column">
                                    <span class="font-weight-bolder">{{ $shift->name }}</span>
                                    <small class="text-muted">
                                        تم الإنشاء: {{ $shift->created_at ? $shift->created_at->format('Y-m-d') : 'غير محدد' }}
                                    </small>
                                </div>
                            </div>
                        </td>

                        <td>
                            <span class="badge shift-type-badge {{ $shift->type == 1 ? 'shift-type-basic' : 'shift-type-advanced' }}">
                                {{ $shift->type == 1 ? 'أساسي' : 'متقدم' }}
                            </span>
                        </td>

                        <td>
                            @php
                                $workingDays = $shift->days->where('working_day', 1)->pluck('day')->toArray();
                                $workingDaysNames = [
                                    'sunday' => 'الأحد',
                                    'monday' => 'الإثنين',
                                    'tuesday' => 'الثلاثاء',
                                    'wednesday' => 'الأربعاء',
                                    'thursday' => 'الخميس',
                                    'friday' => 'الجمعة',
                                    'saturday' => 'السبت',
                                ];

                                $workingDaysTranslated = array_map(function($day) use ($workingDaysNames) {
                                    return $workingDaysNames[$day] ?? $day;
                                }, $workingDays);
                            @endphp

                            <div class="d-flex flex-wrap">
                                @foreach ($workingDaysTranslated as $day)
                                    <span class="badge badge-success mr-1 mb-1">{{ $day }}</span>
                                @endforeach
                            </div>
                            <small class="text-muted">{{ count($workingDays) }} من 7 أيام</small>
                        </td>

                        <td>
                            @php
                                $allDays = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
                                $daysOff = array_diff($allDays, $workingDays);
                                $daysNames = [
                                    'sunday' => 'الأحد',
                                    'monday' => 'الإثنين',
                                    'tuesday' => 'الثلاثاء',
                                    'wednesday' => 'الأربعاء',
                                    'thursday' => 'الخميس',
                                    'friday' => 'الجمعة',
                                    'saturday' => 'السبت',
                                ];

                                $daysOffTranslated = array_map(function($day) use ($daysNames) {
                                    return $daysNames[$day] ?? $day;
                                }, $daysOff);
                            @endphp

                            @if(count($daysOff) > 0)
                                <div class="d-flex flex-wrap">
                                    @foreach ($daysOffTranslated as $day)
                                        <span class="badge badge-danger mr-1 mb-1">{{ $day }}</span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted">لا توجد أيام عطل</span>
                            @endif
                        </td>

                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                            type="button"
                                            id="dropdownMenuButton{{ $shift->id }}"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton{{ $shift->id }}">
                                        <a class="dropdown-item" href="{{ route('shift_management.show', $shift->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('shift_management.edit', $shift->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item text-danger delete-shift-btn"
                                           href="#"
                                           data-shift-id="{{ $shift->id }}"
                                           data-shift-name="{{ $shift->name }}">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-3">
            <i class="fa fa-search text-muted" style="font-size: 48px;"></i>
        </div>
        <h5 class="text-muted">لا توجد نتائج</h5>
        <p class="text-muted">
            @if(request()->has('keywords') || request()->has('type') || request()->has('days'))
                لم يتم العثور على ورديات تطابق معايير البحث المحددة
            @else
                لا توجد ورديات مضافة حتى الآن
            @endif
        </p>
        @if(!request()->has('keywords') && !request()->has('type') && !request()->has('days'))
            <a href="{{ route('shift_management.create') }}" class="btn btn-primary">
                <i class="fa fa-plus"></i> أضف أول وردية
            </a>
        @endif
    </div>
@endif
