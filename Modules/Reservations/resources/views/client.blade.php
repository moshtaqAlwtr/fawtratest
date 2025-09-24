@extends('master')

@section('title')
    أدارة الحجوزات
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">حجوزات العميل : {{$Client->first_name ?? ""}} </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @include('layouts.alerts.error')
    @include('layouts.alerts.success')
    <div class="content-body">

      
        <div class="card">
   
</div>


    <div class="card my-5">
        <div class="card-body">
        <!-- شريط الترتيب -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <ul class="nav nav-tabs" id="sortTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all" type="button" role="tab" aria-controls="all" aria-selected="true">الكل</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="today-tab" data-bs-toggle="tab" data-bs-target="#today" type="button" role="tab" aria-controls="today" aria-selected="false">اليوم</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="week-tab" data-bs-toggle="tab" data-bs-target="#week" type="button" role="tab" aria-controls="week" aria-selected="false">الأسبوع</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="month-tab" data-bs-toggle="tab" data-bs-target="#month" type="button" role="tab" aria-controls="month" aria-selected="false">الشهر</button>
                </li>
            </ul>

            <!-- أزرار العرض -->
            <div class="btn-group" role="group" aria-label="View Toggle">
                <button type="button" class="btn btn-light">
                    <i class="bi bi-grid-3x3-gap-fill"></i> <!-- رمز الشبكة -->
                </button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-list-ul"></i> <!-- رمز القائمة -->
                </button>
            </div>
        </div>

        <!-- بطاقة بيانات -->
        <div class="card">
            <div class="card-body">
                @foreach ($bookings as $booking)
                <div class="row">
                    <div class="col-auto">
                        <!-- صورة افتراضية -->
                        <div style="width: 50px; height: 50px; background-color: #f0f0f0; border-radius: 5px;"></div>
                    </div>
                    <div class="col">
                        <h6>بيانات العميل</h6>
                        <p class="mb-1">{{$booking->client->first_name ?? ""}}</p>
                        <p class="mb-1">الخدمة :{{$booking->product->name ?? ""}}</p>
                    </div>
                    <div class="col-auto text-end">
                        <p class="mb-1">الوقت من {{$booking->start_time ?? 0}} الى {{$booking->end_time ?? 0 }}</p>
                        <p class="text-muted small mb-0">16:45:00</p>
                        
                        @if($booking->status == "confirm")
                            <span class="badge bg-warning text-dark">مؤكد</span>
                        @elseif ($booking->status == "review")
                            <span class="badge bg-warning text-dark">تحت المراجعة</span>
                        @elseif ($booking->status == "bill")
                            <span class="badge bg-warning text-dark">حولت للفاتورة</span>
                        @elseif ($booking->status == "cancel")
                            <span class="badge bg-warning text-dark">تم الالغاء</span>  
                        @else
                            <span class="badge bg-warning text-dark">تم</span> 
                        @endif
            
                        <a href="{{ route('Reservations.show', $booking->id) }}" class="badge bg-danger text-dark">عرض</a> 
                        <a href="{{ route('Reservations.edit', $booking->id) }}" class="btn btn-sm btn-primary">
                            <i class="fa fa-edit"></i> تعديل
                        </a>
                    </div>
                </div>
               
                <!-- Horizontal line after each customer's data -->
                <hr>
            @endforeach
            
            </div>
        </div>
    </div>
</div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">




@endsection