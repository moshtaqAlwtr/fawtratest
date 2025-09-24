@extends('master')

@section('title')
    إضافة فاتورة إلى أمر شغل
@stop

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-light py-3 border-bottom">
                    <h5 class="mb-0 text-primary">
                        <i class="fas fa-file-invoice me-2"></i>
                        إضافة فاتورة إلى أمر شغل
                    </h5>
                </div>
                @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

                <div class="card-body p-4">
                    <form method="POST" action="{{ route('invoices.supply_store') }}">
                        @csrf
                        <input type="hidden" name="id" value="{{ $id }}">

                        <!-- أمر التوريد -->
                        <div class="mb-4">
                            <label for="supply_order" class="form-label fw-bold text-secondary">
                                <i class="fas fa-clipboard-check me-2"></i>
                                اختر أمر التوريد
                            </label>
                            <select id="supply_order" name="supply_order_id" 
                                    class="form-select form-select-lg border-primary @error('supply_order_id') is-invalid @enderror"
                                    style="padding: 0.75rem 1rem; font-size: 1.05rem;">
                                <option value="" selected disabled>-- اختر أمر التوريد --</option>
                                @foreach($SupplyOrders as $SupplyOrder)
                                    <option value="{{ $SupplyOrder->id }}" {{ old('supply_order_id') == $SupplyOrder->id ? 'selected' : '' }}>
                                        {{ $SupplyOrder->name ?? 'أمر بدون اسم' }} (رقم #{{ $SupplyOrder->id }})
                                    </option>
                                @endforeach
                            </select>
                            @error('supply_order_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- زر الحفظ -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary px-4 py-2 rounded-pill">
                                <i class="fas fa-save me-2"></i>
                                حفظ الفاتورة
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
