@extends('master')

@section('title')
    عرض تفاصيل الأصل
@stop

@section('content')
    <div class="content-body">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if(session('success_message'))
                            <div class="alert alert-success">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-check-circle text-success me-2"></i>
                                    <strong>{{ session('success_message') }}</strong>
                                </div>
                                @if(session('success_details'))
                                    <div class="mt-2">
                                        @foreach(session('success_details') as $label => $value)
                                            <div>{{ $label }}: {{ $value }}</div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-3">
                                    <h4 class="mb-0">{{ $asset->name }}</h4>
                                    <span class="badge rounded-pill bg-{{ $asset->status == 2 ? 'success' : ($asset->status == 3 ? 'warning' : 'info') }} ms-2">
                                        @if($asset->status == 2)
                                            تم البيع
                                        @elseif($asset->status == 3)
                                            مهلك
                                        @else
                                            في الخدمة
                                        @endif
                                    </span>
                                </div>

                               <div class="row g-3">
    <div class="col-sm-6">
        <p class="mb-0">
            <strong>حساب الأستاذ:</strong>
           <a href="{{ route('journal.generalLedger', ['account_id' => $asset->asset_account]) }}">
    {{ $asset->account->name ?? 'عرض الحساب' }}
</a>

        </p>
    </div>
</div>

                            </div>

                            <div class="col-md-4 text-end">
                                <div class="btn-group">
                                    @if($asset->status != 2)
                                    <a href="{{ route('Assets.showSell', $asset->id) }}" class="btn btn-outline-primary btn-sm">
                                        <i class="fas fa-dollar-sign"></i> بيع الأصل
                                    </a>
                                    @endif
                                    <a href="{{ route('Assets.generatePdf', $asset->id) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center flex-wrap">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex gap-2">
                                    <!-- تفاصيل الأصل -->
                                    <ul>
                                        <!-- تعديل -->
                                        <a href="{{ route('Assets.edit', $asset->id) }}"
                                            class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #007bff; color: #007bff;">
                                            <i class="fas fa-edit me-1"></i> تعديل
                                        </a>

                                        <!-- طباعة -->
                                        <a href="#" class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #6c757d; color: #6c757d;">
                                            <i class="fas fa-print me-1"></i> طباعة
                                        </a>

                                        <!-- PDF -->
                                        <a href="{{ route('assets.generatePdf', $asset->id) }}"
                                            class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #dc3545; color: #dc3545;">
                                            <i class="fas fa-file-pdf me-1"></i> PDF
                                        </a>

                                      
                                       
                                        <!-- حذف -->
                                        <a href="" class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #dc3545; color: #dc3545;">
                                            <i class="fas fa-trash-alt me-1"></i> حذف
                                        </a>

                                        <!-- اعادة تقييم -->
                                        <a href="" class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #17a2b8; color: #17a2b8;">
                                            <i class="fas fa-redo-alt me-1"></i> اعادة تقييم
                                        </a>

                                        @if($asset->status != 2)
                                        <!-- بيع -->
                                        <a href="{{ route('Assets.showSell', $asset->id) }}" class="btn btn-sm d-inline-flex align-items-center"
                                            style="border: 2px solid #28a745; color: #28a745;">
                                            <i class="fas fa-dollar-sign me-1"></i> بيع
                                        </a>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-body">
                                <ul class="nav nav-tabs mt-3" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link active" id="details-tab" data-toggle="tab" href="#details" role="tab"
                                            aria-controls="details" aria-selected="true">معلومات </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="journal-entries-tab" data-toggle="tab" href="#journal-entries"
                                            role="tab" aria-controls="journal-entries" aria-selected="false">الحركات
                                            </a>
                                    </li>

                                    <li class="nav-item">
                                        <a class="nav-link" id="activity-log-tab" data-toggle="tab" href="#activity-log"
                                            role="tab" aria-controls="activity-log" aria-selected="false">سجل النشاطات</a>
                                    </li>
                                </ul>

                                <div class="tab-content mt-3">
                                    <!-- تفاصيل الأصل -->
                                    <div class="tab-pane fade show active" id="details" role="tabpanel" aria-labelledby="details-tab">
                                        <div class="col-md-12">
                                            <div class="card">
                                                <div class="tab-pane fade show active" style="background: lightslategray; min-height: 100vh; padding: 20px;">
                                                    <div class="card shadow" style="max-width: 600px; margin: 20px auto;">
                                                        <div class="card-body bg-white p-4" style="min-height: 400px; overflow: auto;">
                                                            <div style="transform: scale(0.8); transform-origin: top center;">
                                                                @include('Accounts.asol.pdf', ['asset' => $asset])
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                    </div>


                                    <!-- الحركات -->
                                    <div class="tab-pane fade" id="journal-entries" role="tabpanel"
                                        aria-labelledby="journal-entries-tab">
                                      <div class="card">
    <div class="card-body p-0">
        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>العملية</th>
                        <th>مدين</th>
                        <th>دائن</th>
                        <th>الرصيد بعد</th>
                        <th>التاريخ</th>
                        <th>الاجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($operationsPaginator as $operation)
                        <tr>
                            <td>{{ $operation['operation'] }}</td>
                            <td>{{ number_format($operation['deposit'], 2) }}</td>
                            <td>{{ number_format($operation['withdraw'], 2) }}</td>
                            <td>{{ number_format($operation['balance_after'], 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($operation['date'])->format('Y-m-d') }}</td>

                            <td>   
                                <div class="btn-group">
                                    <div class="dropdown">
                                        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        </button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{ route('journal.show', $operation['journalEntry']) }}">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
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
    </div>
</div>

                                    </div>



                                    <!-- سجل النشاطات -->
                                    <div class="tab-pane fade" id="activity-log" role="tabpanel" aria-labelledby="activity-log-tab">
                                        <div class="bg-light p-3">
                                            <div class="d-flex align-items-center gap-2">
                                                <button class="btn btn-primary btn-sm px-3">اليوم</button>
                                            </div>
                                        </div>

                                        <div class="activity-list p-3">
                                            @if($asset->created_at)
                                                <div class="activity-item bg-white p-3 rounded mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="activity-icon bg-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                            <i class="fas fa-plus text-white" style="font-size: 12px;"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="activity-text">
                                                                    <span>
                                                                        {{ $asset->created_by ? $asset->created_by->name : 'النظام' }}
                                                                        أضاف أصل جديد # {{ $asset->id }}
                                                                        تحت حساب {{ $asset->account ? $asset->account->name : '' }}،
                                                                        سعر الشراء {{ number_format($asset->purchase_price, 2) }} {{ $asset->currency }}
                                                                        @if($asset->source_account)
                                                                            من حساب {{ $asset->source_account->name }}،
                                                                        @endif
                                                                        القيمة الحالية {{ number_format($asset->current_value, 2) }} {{ $asset->currency }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="activity-meta mt-1">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-user-circle me-1"></i>
                                                                    {{ $asset->created_by ? $asset->created_by->name : 'النظام' }} -
                                                                </small>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-building me-1"></i>
                                                                    {{ $asset->branch ? $asset->branch->name : 'الفرع الرئيسي' }} -
                                                                </small>
                                                                <small class="text-muted">{{ $asset->created_at->format('H:i:s') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif

                                            @if($asset->updated_at && $asset->updated_at != $asset->created_at)
                                                <div class="activity-item bg-white p-3 rounded mb-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="activity-icon bg-info rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 30px; height: 30px;">
                                                            <i class="fas fa-edit text-white" style="font-size: 12px;"></i>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <div class="d-flex justify-content-between align-items-center">
                                                                <div class="activity-text">
                                                                    <span>
                                                                        {{ $asset->updated_by ? $asset->updated_by->name : 'النظام' }}
                                                                        قام بتحديث الأصل # {{ $asset->id }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="activity-meta mt-1">
                                                                <small class="text-muted">
                                                                    <i class="fas fa-user-circle me-1"></i>
                                                                    {{ $asset->updated_by ? $asset->updated_by->name : 'النظام' }} -
                                                                </small>
                                                                <small class="text-muted">
                                                                    <i class="fas fa-building me-1"></i>
                                                                    {{ $asset->branch ? $asset->branch->name : 'الفرع الرئيسي' }} -
                                                                </small>
                                                                <small class="text-muted">{{ $asset->updated_at->format('H:i:s') }}</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header bg-lighten-5">
                        <h6 class="text-dark mb-0">المرفقات</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <!-- الصورة -->
                            <div class="col-md-4 text-end">
                                <img src="{{ asset('storage/' . $asset->attachments) }}" alt="مرفق"
                                     class="img-thumbnail" style="max-height: 150px; max-width: 100%;">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Modal بيع الأصل -->

@endsection

@section('scripts')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="{{ asset('assets/js/applmintion.js') }}"></script>
@endsection
