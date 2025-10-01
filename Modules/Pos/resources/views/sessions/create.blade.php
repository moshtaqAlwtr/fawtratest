
@extends('master')

@section('title')
بدء جلسة نقطة بيع جديدة
@stop

@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">بدء جلسة نقطة بيع جديدة</h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ url('/') }}">الرئيسية</a></li>
                        <li class="breadcrumb-item active">جلسة جديدة</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- عرض رسائل الخطأ --}}
@if ($errors->any())
    <div class="alert alert-danger">
        <h6><i class="fa fa-exclamation-triangle"></i> يرجى تصحيح الأخطاء التالية:</h6>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger">
        <i class="fa fa-exclamation-triangle"></i> {{ session('error') }}
    </div>
@endif


<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="card-title mb-0">
                    <i class="fa fa-play-circle me-2"></i>
                    بدء جلسة جديدة
                </h4>
            </div>
            
            <form action="{{ route('pos.session.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    
                    <!-- معلومات الموظف -->
                    <div class="alert alert-info">
                        <strong><i class="fa fa-user me-2"></i>الموظف:</strong> {{ Auth::user()->name }}
                        <br>
                        <strong><i class="fa fa-clock me-2"></i>الوقت:</strong> {{ now()->format('Y-m-d H:i:s') }}
                    </div>

                    <div class="row mb-3">
                        <!-- الجهاز -->
                        <div class="col-md-6">
                            <label for="device_id" class="form-label">
                                <i class="fa fa-desktop me-2"></i>الجهاز <span class="text-danger">*</span>
                            </label>
                            <select id="device_id" 
                                    name="device_id" 
                                    class="form-control @error('device_id') is-invalid @enderror"
                                    required>
                                <option value="">اختر الجهاز</option>
                                @foreach($devices as $device)
                                    <option value="{{ $device->id }}" 
                                            {{ old('device_id') == $device->id ? 'selected' : '' }}>
                                        {{ $device->device_name }} 
                                        @if($device->store)
                                            ({{ $device->store->name }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('device_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- الوردية -->
                        <div class="col-md-6">
                            <label for="shift_id" class="form-label">
                                <i class="fa fa-clock me-2"></i>الوردية <span class="text-danger">*</span>
                            </label>
                            <select id="shift_id" 
                                    name="shift_id" 
                                    class="form-control @error('shift_id') is-invalid @enderror"
                                    required>
                                <option value="">اختر الوردية</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}" 
                                            {{ old('shift_id') == $shift->id ? 'selected' : '' }}>
                                        {{ $shift->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('shift_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- الرصيد الافتتاحي -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label for="opening_balance" class="form-label">
                                <i class="fa fa-money me-2"></i>الرصيد الافتتاحي (ريال) <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   id="opening_balance" 
                                   name="opening_balance" 
                                   class="form-control @error('opening_balance') is-invalid @enderror" 
                                   placeholder="0.00"
                                   step="0.01"
                                   min="0"
                                   value="{{ old('opening_balance', '0') }}"
                                   required>
                            @error('opening_balance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">المبلغ النقدي الموجود في الصندوق عند بداية الجلسة</small>
                        </div>
                    </div>

                    <!-- تحذير -->
                    <div class="alert alert-warning">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <strong>تنبيه:</strong> لا يمكنك إنشاء جلسة جديدة إذا كانت لديك جلسة نشطة. يجب إغلاق الجلسة الحالية أولاً.
                    </div>
                </div>

                <div class="card-footer text-center">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa fa-play me-2"></i>بدء الجلسة
                    </button>
                    <a href="{{ url('/') }}" class="btn btn-secondary btn-lg ms-2">
                        <i class="fa fa-times me-2"></i>إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection