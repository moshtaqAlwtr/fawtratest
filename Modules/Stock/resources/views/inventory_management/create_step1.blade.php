@extends('master')

@section('title')
إدارة الجرد
@stop

@section('content')
<div class="content-body">
    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <div class="card">
        <div class="card-body">
            <form id="inventory-form" action="{{ route('inventory.store') }}" method="POST">
                @csrf
                <div class="row align-items-end"> <!-- تمت إضافة align-items-end هنا -->
                    <div class="col-md-4">
                        <div class="form-group mb-0"> <!-- تمت إضافة mb-0 لإزالة الهامش السفلي -->
                            <label for="storehouse_id">المستودع</label>
                            <select name="storehouse_id" id="storehouse_id" class="form-select" required>
                                <option value="">-- اختر المستودع --</option>
                                @foreach($storehouses as $storehouse)
                                    <option value="{{ $storehouse->id }}">{{ $storehouse->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
@php
    $now = \Carbon\Carbon::now()->format('Y-m-d\TH:i'); // تنسيق مناسب لـ input[type="datetime-local"]
@endphp
                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="inventory_time">تاريخ ووقت الجرد</label>
                            <input type="datetime-local" name="inventory_time" value="{{ $now }}" id="inventory_time" class="form-control" required>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group mb-0">
                            <label for="calculation_type">نوع الجرد</label>
                            <select name="calculation_type" id="calculation_type" class="form-select" required>
                                <option value="undated" selected>غير مرتبط بالتاريخ</option>
                                <option value="dated">حسب تاريخ الجرد</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-3">
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        <i class="fas fa-calculator me-2"></i>قم بالجرد
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* إضافة ستايل لتحسين المظهر */
    .form-group {
        margin-bottom: 0; /* إزالة الهامش السفلي للعناصر */
    }
    
    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 500;
    }
    
    .form-select, .form-control {
        height: 40px; /* توحيد ارتفاع العناصر */
        width: 100%;
    }
    
    .row.align-items-end {
        align-items: flex-end; /* محاذاة العناصر من الأسفل */
    }
</style>

@endsection