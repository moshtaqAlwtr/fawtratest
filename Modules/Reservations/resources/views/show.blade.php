@extends('master')

@section('title')
   عرض الخدمة
@stop

@section('content')
<style>
    body {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        min-height: 100vh;
        color: #2c3e50;
    }

    .content-header {
        background: rgba(255, 255, 255, 0.8);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.05);
    }

    .content-header-title {
        color: #495057;
        font-weight: 600;
        font-size: 1.8rem;
    }

    .breadcrumb {
        background: transparent;
        padding: 0;
        margin: 0;
    }

    .breadcrumb-item a {
        color: #6c757d;
        text-decoration: none;
    }

    .breadcrumb-item.active {
        color: #495057;
    }

    .card {
        background: rgba(255, 255, 255, 0.95);
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 25px rgba(0, 0, 0, 0.08);
        backdrop-filter: blur(10px);
        margin-bottom: 2rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 35px rgba(0, 0, 0, 0.12);
    }

    .card-body {
        padding: 2rem;
    }

    .avatar {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 600;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .avatar-md {
        width: 48px;
        height: 48px;
        font-size: 1.2rem;
    }

    .bg-light-primary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }

    .bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
    }

    .badge {
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-size: 0.85rem;
        font-weight: 500;
    }

    .bg-warning {
        background: linear-gradient(135deg, #ffc107 0%, #ffb300 100%) !important;
    }

    .btn {
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
        border: none;
    }

    .btn-danger {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(220, 53, 69, 0.4);
    }

    .dropdown-menu {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: none;
        border-radius: 12px;
        box-shadow: 0 8px 35px rgba(0, 0, 0, 0.15);
        padding: 0.5rem 0;
    }

    .dropdown-item {
        padding: 0.75rem 1.5rem;
        color: #495057;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: rgba(108, 117, 125, 0.1);
        color: #495057;
    }

    .nav-tabs {
        border-bottom: 2px solid rgba(108, 117, 125, 0.1);
    }

    .nav-link {
        color: #6c757d;
        border: none;
        padding: 1rem 1.5rem;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    .nav-link.active {
        color: #495057;
        background: rgba(108, 117, 125, 0.1);
        border-radius: 8px 8px 0 0;
    }

    .tab-content {
        background: rgba(255, 255, 255, 0.5);
        border-radius: 0 0 16px 16px;
    }

    .text-muted {
        color: #6c757d !important;
    }

    .h5, .h6 {
        color: #495057;
    }

    .text-primary {
        color: #495057 !important;
    }

    .text-success {
        color: #28a745 !important;
    }

    .vr {
        background-color: rgba(108, 117, 125, 0.3);
    }

    .timeline {
        background: rgba(248, 249, 250, 0.5);
        border-radius: 12px;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* تأثيرات إضافية */
    .fa-circle {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.6; }
        100% { opacity: 1; }
    }

    /* تحسين التجاوب */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.5rem;
        }

        .content-header {
            padding: 1rem;
        }
    }
</style>

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">عرض</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-body">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="avatar avatar-md bg-light-primary">
                        <span class="avatar-content fs-4">ت</span>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <h5 class="mb-0 fw-bolder">حجز العميل</h5>
                                <small class="text-muted">#1</small>
                            </div>
                            <div class="vr mx-2"></div>
                            <div class="d-flex align-items-center">
                                <small class="text-success">
                                    <i class="fa fa-circle me-1" style="font-size: 8px;"></i>

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
                       <!-- Example single danger button -->

                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex gap-2">
                    <form action="{{ route('reservations.updateStatus', $booking->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                    <div class="btn-group">
                        <button type="button" class="btn btn-danger dropdown-toggle" id="statusButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            الحالة
                        </button>
                        <div class="dropdown-menu">
                            <button type="submit" name="status" value="confirm" class="dropdown-item">تأكيد</button>
                            <button type="submit" name="status" value="review" class="dropdown-item">تحت المراجعة</button>
                            <button type="submit" name="status" value="bill" class="dropdown-item">حولت لفاتورة</button>
                            <button type="submit" name="status" value="cancel" class="dropdown-item">تم الإلغاء</button>
                            <button type="submit" name="status" value="done" class="dropdown-item">تم</button>
                        </div>
                    </div>


                    </form>
                </div>
            </div>
        </div>
    </div>


    <div class="card">

        <div class="card-body">
            <ul class="nav nav-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#details" role="tab">
                        <span>التفاصيل</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#activity" role="tab">
                        <span>سجل النشاطات</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content p-3">
                <!-- تبويب التفاصيل -->
                <div class="tab-pane active" id="details" role="tabpanel">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="d-flex align-items-center gap-3 mb-4">
                                                <div class="avatar avatar-md bg-secondary"
                                                    style="width: 42px; height: 42px;">
                                                    <span class="avatar-content" style="font-size: 1rem;">م</span>
                                                </div>
                                                <div>
                                                    <h4 class="mb-0" style="font-size: 1.1rem;">{{$booking->client->first_name ?? ""}}<span
                                                            class="text-muted" style="font-size: 0.9rem;"></span></h4>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-muted mb-2">تاريخ العملية التجارية</h6>
                                                    <p class="h5">{{$booking->appointment_date ?? ""}}</p>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-muted mb-2">الخدمة</h6>
                                                    <p class="h5">
                                                        <a href="#" class="text-primary">{{$booking->product->name ?? ""}}</a>


                                                    </p>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-muted mb-2">اجمالي الوقت</h6>
                                                    <p class="h5">
                                                        @php
                                                        use Carbon\Carbon;

                                                        $startTime = $booking->start_time ? Carbon::parse($booking->start_time) : null;
                                                        $endTime = $booking->end_time ? Carbon::parse($booking->end_time) : null;

                                                        $minutesDifference = ($startTime && $endTime) ? $endTime->diffInMinutes($startTime) : 0;
                                                    @endphp

                                                    <p>وقت البدء: {{ $booking->start_time ?? "غير محدد" }}</p>
                                                    <p>وقت الانتهاء: {{ $booking->end_time ?? "غير محدد" }}</p>
                                                    <p>عدد الدقائق: {{ $minutesDifference }}</p>

                                                    </p>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-muted mb-2">وقت البداية </h6>
                                                    <p class="h5">{{ $booking->start_time ?? "غير محدد" }} </p>
                                                </div>
                                                <div class="col-md-4 mb-4">
                                                    <h6 class="text-muted mb-2">ملاحظات</h6>
                                                    <p class="h5 text-success"></p>
                                                </div>
                                            </div>




                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- تبويب سجل النشاطات -->
                <div class="tab-pane" id="activity" role="tabpanel">
                    <div class="timeline p-4">
                        <!-- يمكن إضافة سجل النشاطات هنا -->
                        <p class="text-muted text-center"></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
$(document).ready(function() {
    $(".status-option").click(function(event) {
        event.preventDefault();

        var statusValue = $(this).data("value");  // الحصول على القيمة الجديدة للحالة
        var statusText = $(this).text();  // الحصول على نص الحالة لعرضه في الزر
        var reservationId = $("#reservationId").val();  // جلب ID الحجز

        $("#statusButton").text(statusText);  // تغيير نص الزر
        $("#statusInput").val(statusValue);  // تحديث القيمة المخفية

        // إرسال الحالة إلى الخادم عبر Ajax
        $.ajax({
            url: "/reservations/update-status/" + reservationId,
            type: "PUT",
            data: {
                status: statusValue,
                _token: "{{ csrf_token() }}"  // تأمين الطلب بـ CSRF Token
            },
            success: function(response) {
                alert("تم تحديث الحالة بنجاح!");
            },
            error: function(xhr, status, error) {
                alert("حدث خطأ أثناء التحديث، حاول مرة أخرى.");
            }
        });
    });
});
</script>