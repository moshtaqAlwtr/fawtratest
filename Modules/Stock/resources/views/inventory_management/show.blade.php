@extends('master')

@section('title', 'ورقة الجرد')

@section('content')
<div class="card">
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="mb-0">ورقة الجرد #{{ $adjustment->reference_number }}</h4>
        <div>
           @if($adjustment->status == "draft")
            <a href="{{route('inventory.adjustment', $adjustment->id)}}" class="btn btn-primary btn-sm">
                <i class="fas fa-check-circle"></i> تسوية
            </a>
            @else
             <a href="{{route('inventory.Canceladjustment', $adjustment->id)}}" class="btn btn-danger btn-sm">
                <i class="fas fa-check-circle"></i> الغاء التسوية
            </a>
            @endif
        </div>
    </div>
</div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="row mb-4">
                <!-- جدول معلومات الجرد -->
                <div class="col-md-6">
                    <table class="table table-bordered small">
                        <thead class="table-light">
                            <tr>
                                <th colspan="2" class="text-center">معلومات الجرد</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <th>المخزن</th>
                                <td>{{ $adjustment->storeHouse->name }}</td>
                            </tr>
                            <tr>
                                <th>التاريخ</th>
                                <td>{{ $adjustment->created_at->format('d/m/Y') }}</td>
                            </tr>
                            <tr>
                                <th>الوقت</th>
                                <td>{{ $adjustment->created_at->format('H:i:s') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- جدول معلومات أخرى -->
                <div class="col-md-6">
                    <table class="table table-bordered small">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">معلومات أخرى</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $adjustment->notes ?? 'لا توجد ملاحظات' }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- جدول المنتجات -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-primary text-center">
                        <tr>
                            <th>#</th>
                            <th>كود المنتج</th>
                            <th>اسم المنتج</th>
                            <th>العدد بالمخزون</th>
                            <th>العدد بالبرنامج</th>
                            <th>نقص/زيادة</th>
                            <th>الصورة</th>
                            <th>ملاحظة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($adjustment->items as $item)
                        <tr class="text-center">
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->product->code ?? 'N/A' }}</td>
                            <td>{{ $item->product->name }}</td>
                            <td>{{ $item->quantity_in_stock }}</td>
                            <td>{{ $item->quantity_in_system }}</td>
                            <td class="{{ $item->quantity_difference > 0 ? 'text-success' : ($item->quantity_difference < 0 ? 'text-danger' : '') }}">
                                {{ $item->quantity_difference > 0 ? '+' : '' }}{{ $item->quantity_difference }}
                            </td>
                            <td>
    @if($item->image)
        <img src="{{ asset($item->image) }}"
             width="50" height="50"
             class="img-thumbnail"
             data-bs-toggle="modal"
             data-bs-target="#imageModal{{ $item->id }}">
    @else
        <span class="text-muted">لا يوجد</span>
    @endif
</td>
@if($item->image)
<div class="modal fade" id="imageModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-body text-center">
        <img src="{{ asset($item->image) }}" class="img-fluid" style="max-height: 80vh;">
      </div>
    </div>
  </div>
</div>
@endif


                            <td>{{ $item->note ?? 'لا توجد ملاحظات' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
    .table th,
    .table td {
        vertical-align: middle;
        text-align: center;
    }

    .table th {
        white-space: nowrap;
    }

    .text-success, .text-danger {
        font-weight: bold;
    }

    .btn-sm i {
        margin-left: 4px;
    }
</style>
@endsection
