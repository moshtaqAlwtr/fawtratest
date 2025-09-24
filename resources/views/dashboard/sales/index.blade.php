@extends('master')

@section('title')
    لوحة التحكم
@stop
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

@section('css')
    <style>
        .ficon {

                font-size: 16px;
                margin-left: 8px;
            }
            .ml-auto a {
                display: inline-block;
                margin: 7px 10px;
                width: 100%;
                padding: 4px;
            }
            .chart-container {
    width: 100%;
    height: auto;
}

@media (max-width: 576px) {
    canvas {
        max-width: 100% !important;
        height: auto !important;
    }
}

            font-size: 16px;
            margin-left: 8px;
        }

        .ml-auto a {
            display: inline-block;
            margin: 7px 10px;
            width: 100%;
            padding: 4px;
        }

    </style>


<style>
    .branch-card {
        border: 1px solid #eee;
        border-radius: 8px;
        padding: 15px;
        background: #fff;
    }

    .attention-item {
        background-color: #fff9f9;
        border-left: 3px solid #ff6b6b;
        transition: all 0.3s ease;
    }

    .attention-item:hover {
        background-color: #fff0f0;
        transform: translateY(-2px);
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }

    .attention-list {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 5px;
    }

    /* Scrollbar styling */
    .attention-list::-webkit-scrollbar {
        width: 5px;
    }

    .attention-list::-webkit-scrollbar-thumb {
        background: #ddd;
        border-radius: 10px;
    }

    .smaller {
        font-size: 0.8em;
    }
</style>
    <style>
.district-performance-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    overflow: hidden;
    width: 250px;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.district-header {
    background-color: #f8f9fa;
    padding: 12px 16px;
    font-weight: bold;
    font-size: 16px;
    border-bottom: 1px solid #e0e0e0;
}

.district-main {
    padding: 16px;
    text-align: center;
    background-color: #ffffff;
}

.district-name {
    font-size: 24px;
    font-weight: bold;
    color: #333;
}

.district-secondary {
    background-color: #f8f9fa;
    border-top: 1px solid #e0e0e0;
}

.district-sub {
    display: flex;
    justify-content: space-between;
    padding: 12px 16px;
    border-bottom: 1px solid #e0e0e0;
}

.district-sub:last-child {
    border-bottom: none;
}

.district-sub-name {
    font-size: 14px;
    color: #555;
}

.district-sub-percentage {
    font-weight: bold;
    color: #28a745; /* اللون الأخضر للنسبة */
}

.district-sub-count {
    font-weight: bold;
    color: #333;
}

/* التصميم للفئة C */
.district-sub-name:contains("C") {
    color: #dc3545; /* اللون الأحمر للفئة C */
}
</style>
@endsection

@section('content')


    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-between align-items-center mb-1">
                <div class="mr-1">
                    <p><span>{{ \Carbon\Carbon::now()->translatedFormat('l، d F Y') }}</span></p>
                    <h4 class="content-header-title float-left mb-0"> أهلاً <strong
                            style="color: #2C2C2C">{{ auth()->user()->name }} ، </strong> مرحباً بعودتك!</h4>
                </div>
                <div class="ml-auto bg-rgba-success">
                    <a href="" class="text-success"><i class="ficon feather icon-globe"></i> <span>الذهاب إلى
                            الموقع</span></a>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">

        <section id="dashboard-ecommerce">
            <div class="row">
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column align-items-start pb-0">
                            <div class="avatar bg-rgba-primary p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-users text-primary font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700 mt-1">{{ $ClientCount ?? 0 }}</h2>
                            <p class="mb-0">العملاء</p>
                        </div>
                        <div class="card-content">
                            <div id="line-area-chart-1"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column align-items-start pb-0">
                            <div class="avatar bg-rgba-success p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-credit-card text-success font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700 mt-1"> {{ number_format($Invoice, 2) ?? 0 }}</h2>
                            <p class="mb-0">المبيعات</p>
                        </div>
                        <div class="card-content">
                            <div id="line-area-chart-2"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column align-items-start pb-0">
                            <div class="avatar bg-rgba-danger p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-shopping-cart text-danger font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700 mt-1">{{ $Visit ?? 0 }}</h2>
                            <p class="mb-0">الزيارات</p>
                        </div>
                        <div class="card-content">
                            <div id="line-area-chart-3"></div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3 col-sm-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex flex-column align-items-start pb-0">
                            <div class="avatar bg-rgba-warning p-50 m-0">
                                <div class="avatar-content">
                                    <i class="feather icon-package text-warning font-medium-5"></i>
                                </div>
                            </div>
                            <h2 class="text-bold-700 mt-1">97.5K</h2>
                            <p class="mb-0">الطلبات الواردة</p>
                        </div>
                        <div class="card-content">
                            <div id="line-area-chart-4"></div>
                        </div>
                    </div>
                </div>
            </div>




<div class="row g-3">
    @if ($branchesPerformance->count() >= 3)
  <div class="col-md-4">
    <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
            <!-- العنوان مع زر عرض الكل بجواره -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                    ⭐ أفضل الفروع أداءً
                </h5>
                <a href="{{route('statistics.group')}}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list me-1"></i> عرض الكل
                </a>
            </div>

            @foreach ($branchesPerformance->take(3) as $index => $branch)
              @php
                $max = $branchesPerformance->max('total_collected') ?: 1;
                $percentage = round($branch->total_collected / $max * 100, 2);
                $colors = ['#d8a700', '#a2a6b1', '#a14f03'];
                $color = $colors[$index] ?? '#ccc';
              @endphp

              <div class="mb-4 position-relative">
                  <div class="d-flex justify-content-between align-items-center mb-2">
                      <div class="fw-bold fs-6 text-truncate">{{ $branch->branch_name }}</div>
                      <span class="badge rounded-circle text-white fw-bold"
                          style="background-color: {{ $color }}; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                          {{ $index + 1 }}
                      </span>
                  </div>

                  <div class="progress mb-1" style="height: 8px; direction: rtl; background-color: #eee;">
                      <div class="progress-bar"
                          role="progressbar"
                          style="width: {{ min($percentage, 100) }}%;"
                          aria-valuenow="{{ $percentage }}"
                          aria-valuemin="0"
                          aria-valuemax="100">
                      </div>
                  </div>

                  <div class="text-end mb-2 text-muted small">
                      {{ $percentage }}٪ من التحصيل الأعلى
                  </div>

                  <div class="text-muted small">
                      🔹 المدفوعات: <strong>{{ number_format($branch->payments) }}</strong> ر.س<br>
                      🔹 السندات: <strong>{{ number_format($branch->receipts) }}</strong> ر.س<br>
                      🔸 الإجمالي: <strong>{{ number_format($branch->total_collected) }}</strong> ر.س
                  </div>

                  <!-- زر عرض التفاصيل -->

              </div>
            @endforeach
        </div>
    </div>
</div>
    @endif

    @if ($regionPerformance->count() >= 3)
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">

 <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                    🗺️ أفضل المجموعات أداءً
                </h5>
                <a href="{{route('statistics.groupall')}}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list me-1"></i> عرض الكل
                </a>
            </div>
                @foreach ($regionPerformance->take(3) as $index => $region)
                @php
                    $max = $regionPerformance->max('total_collected') ?: 1;
                    $percent = round($region->total_collected / $max * 100, 2);
                    $colors = ['#d8a700', '#a2a6b1', '#a14f03'];
                    $color = $colors[$index] ?? '#ccc';
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold fs-6 text-truncate">{{ $region->region_name }}</div>
                        <span class="badge text-white fw-bold rounded-circle"
                            style="background-color: {{ $color }}; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            {{ $index + 1 }}
                        </span>
                    </div>

                    <div class="progress mb-1" style="height: 8px; direction: rtl; background-color: #eee;">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: {{ $percent }}%; background-color: {{ $color }};"
                            aria-valuenow="{{ $percent }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <div class="text-end mb-2 text-muted small">
                        {{ $percent }}٪ من الأعلى
                    </div>

                    <div class="text-muted small">
                        🔹 المدفوعات: <strong>{{ number_format($region->payments) }}</strong> ر.س<br>
                        🔹 السندات: <strong>{{ number_format($region->receipts) }}</strong> ر.س<br>
                        🔸 الإجمالي: <strong>{{ number_format($region->total_collected) }}</strong> ر.س
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if ($neighborhoodPerformance->count() >= 3)
    <div class="col-md-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0">
                     🏘️ أفضل الأحياء أداءً
                </h5>
                <a href="{{route('statistics.neighborhood')}}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list me-1"></i> عرض الكل
                </a>
            </div>
                @foreach ($neighborhoodPerformance->take(3) as $index => $neigh)
                @php
                    $max = $neighborhoodPerformance->max('total_collected') ?: 1;
                    $percent = round($neigh->total_collected / $max * 100, 2);
                    $colors = ['#d8a700', '#a2a6b1', '#a14f03'];
                    $color = $colors[$index] ?? '#ccc';
                @endphp

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-bold fs-6 text-truncate">{{ $neigh->neighborhood_name }}</div>
                        <span class="badge text-white fw-bold rounded-circle"
                            style="background-color: {{ $color }}; width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            {{ $index + 1 }}
                        </span>
                    </div>

                    <div class="progress mb-1" style="height: 8px; direction: rtl; background-color: #eee;">
                        <div class="progress-bar"
                            role="progressbar"
                            style="width: {{ $percent }}%; background-color: {{ $color }};"
                            aria-valuenow="{{ $percent }}"
                            aria-valuemin="0" aria-valuemax="100">
                        </div>
                    </div>

                    <div class="text-end mb-2 text-muted small">
                        {{ $percent }}٪ من الأعلى
                    </div>

                    <div class="text-muted small">
                        🔹 المدفوعات: <strong>{{ number_format($neigh->payments) }}</strong> ر.س<br>
                        🔹 السندات: <strong>{{ number_format($neigh->receipts) }}</strong> ر.س<br>
                        🔸 الإجمالي: <strong>{{ number_format($neigh->total_collected) }}</strong> ر.س
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif
</div>

<br>
<br>
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body text-center">
        <h5 class="fw-bold mb-3">📊 متوسط تحصيل الفروع</h5>
        <div class="display-6 text-primary fw-bold">
            {{ number_format($averageBranchCollection) }} <small class="fs-5">ريال</small>
        </div>
        <p class="text-muted mt-2">متوسط إجمالي التحصيل على مستوى الفروع</p>
    </div>
</div>




<div class="container py-4">
    <div class="card shadow-sm border-0">
        <!-- Header Section Inside Card -->
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary">أداء الموظفين مقارنة بالهدف الشهري</h4>
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center me-3">
                        <span class="badge bg-success me-2" style="width: 12px; height: 12px; border-radius: 50%;"></span>
                        <small>تحقيق 100%+</small>
                    </div>
                    <div class="d-flex align-items-center me-3">
                        <span class="badge bg-warning me-2" style="width: 12px; height: 12px; border-radius: 50%;"></span>
                        <small>80% - 99%</small>
                    </div>
                    <div class="d-flex align-items-center">
                        <span class="badge bg-danger me-2" style="width: 12px; height: 12px; border-radius: 50%;"></span>
                        <small>أقل من 80%</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table Section -->
       <div class="card-body p-0">
           <form method="GET" class="mb-3">
    <div class="row g-2 align-items-center">
        <div class="col-auto">
            <label for="month" class="form-label">اختر الشهر:</label>
        </div>
        <div class="col-auto">
            <input type="month" name="month" id="month" class="form-control" value="{{ $month }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary">عرض</button>
        </div>
    </div>
</form>

    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th width="25%">الموظف</th>
                     <th width="15%">العملاء</th>
                    <th width="15%" class="text-end">المبالغ المحصله</th>
                    <th width="15%" class="text-end">الهدف</th>
                    <th width="25%">النسبة</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($cards as $card)
                <tr>
                    <td>
                        <strong>{{ $card['name'] }}</strong>
                        <div class="text-muted small mt-1">
                            المدفوعات: {{ number_format($card['payments']) }} ريال<br>
                            السندات: {{ number_format($card['receipts']) }} ريال<br>

                            الإجمالي: {{ number_format($card['total']) }} / الهدف: {{ number_format($card['target']) }} ريال
                        </div>
                    </td>
                     <td class="text-end">{{ $card['clients_count'] }}</td>
                    <td class="text-end">{{ number_format($card['total']) }} ريال</td>
                    <td class="text-end">{{ number_format($card['target']) }} ريال</td>
                    <td>
                        <div class="d-flex align-items-center">
                            <span class="text-success me-2">{{ $card['percentage'] }}%</span>
                            <div class="progress" style="width: 100%; height: 8px;">
                                <div class="progress-bar bg-success"
                                     role="progressbar"
                                     style="width: {{ $card['percentage'] }}%;"
                                     aria-valuenow="{{ $card['percentage'] }}"
                                     aria-valuemin="0"
                                     aria-valuemax="100">
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

<style>
    .progress-bar {
        background-color: #28a745 !important; /* اللون الأخضر */
        transition: width 0.6s ease;
    }
    .text-success {
        color: #28a745 !important; /* اللون الأخضر للنص */
        font-weight: bold;
    }
</style>
    </div>
</div>




         <div class="row">
              <div class="col-md-12 col-12">
                  <div class="accordion mb-3" id="summaryAccordion">
    <div class="row mb-3">
    <div class="col-md-4 col-12">
        <div class="card text-center shadow-sm border-success">
            <div class="card-body">
                <h5 class="text-success">إجمالي المبيعات</h5>
                <h3 class="fw-bold">{{ number_format($totalSales, 2) }} ريال</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card text-center shadow-sm border-primary">
            <div class="card-body">
                <h5 class="text-primary">إجمالي المدفوعات</h5>
                <h3 class="fw-bold">{{ number_format($totalPayments, 2) }} ريال</h3>
            </div>
        </div>
    </div>

    <div class="col-md-4 col-12">
        <div class="card text-center shadow-sm border-warning">
            <div class="card-body">
                <h5 class="text-warning">إجمالي سندات القبض</h5>
                <h3 class="fw-bold">{{ number_format($totalReceipts, 2) }} ريال</h3>
            </div>
        </div>
    </div>
</div>



</div>
 <div class="card">
    <div class="card-header">
        <h4 class="card-title">مبيعات المجموعات</h4>
    </div>
    <div class="card-body">
        <div class="chart-container" style="position: relative; width: 100%;">
            <canvas id="group-sales-chart"></canvas>
        </div>
    </div>
</div>

</div>



<!-- السكربت يوضع خارج البلوك -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('group-sales-chart').getContext('2d');

        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($groupChartData->pluck('region')) !!},
                datasets: [
    {
        label: 'المبيعات',
        data: {!! json_encode($groupChartData->pluck('sales')) !!},
        backgroundColor: 'rgba(54, 162, 235, 0.7)',
        borderColor: 'rgba(54, 162, 235, 1)',
        borderWidth: 1
    },
    {
        label: 'المدفوعات',
        data: {!! json_encode($groupChartData->pluck('payments')) !!},
        backgroundColor: 'rgba(75, 192, 192, 0.7)',
        borderColor: 'rgba(75, 192, 192, 1)',
        borderWidth: 1
    },
    {
        label: 'سندات القبض',
        data: {!! json_encode($groupChartData->pluck('receipts')) !!},
        backgroundColor: 'rgba(255, 159, 64, 0.7)',
        borderColor: 'rgba(255, 159, 64, 1)',
        borderWidth: 1
    }
]

            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'المبلغ (ريال)'
                        }
                    }
                }
            }
        });
    });
</script>


                <!--<div class="col-lg-4 col-12">-->
                <!--    <div class="card chat-application">-->
                <!--        <div class="card-header">-->
                <!--            <h4 class="card-title">الدردشة</h4>-->
                <!--        </div>-->
                <!--        <div class="chat-app-window">-->
                <!--            <div class="user-chats">-->
                <!--                <div class="chats">-->
                <!--                    <div class="chat">-->
                <!--                        <div class="chat-avatar">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>كعكة السمسم</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat chat-left">-->
                <!--                        <div class="chat-avatar mt-50">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>فطيرة التفاح</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat">-->
                <!--                        <div class="chat-avatar">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>كعكة الشوكولاتة</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat chat-left">-->
                <!--                        <div class="chat-avatar mt-50">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>دونات</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat">-->
                <!--                        <div class="chat-avatar mt-50">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>حلوى عرق السوس</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat chat-left">-->
                <!--                        <div class="chat-avatar mt-50">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>حلوى التوفي</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat">-->
                <!--                        <div class="chat-avatar">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="right" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-2.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>فطيرة التفاح</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                    <div class="chat chat-left">-->
                <!--                        <div class="chat-avatar mt-50">-->
                <!--                            <a class="avatar m-0" data-toggle="tooltip" href="#" data-placement="left" title="" data-original-title="">-->
                <!--                                <img src="../../../app-assets/images/portrait/small/avatar-s-5.jpg" alt="avatar" height="40" width="40" />-->
                <!--                            </a>-->
                <!--                        </div>-->
                <!--                        <div class="chat-body">-->
                <!--                            <div class="chat-content">-->
                <!--                                <p>كعكة البسكويت</p>-->
                <!--                            </div>-->
                <!--                        </div>-->
                <!--                    </div>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--            <div class="chat-footer">-->
                <!--                <div class="card-body d-flex justify-content-around pt-0">-->
                <!--                    <input type="text" class="form-control mr-50" placeholder="اكتب رسالتك">-->
                <!--                    <button type="button" class="btn btn-icon btn-primary"><i class="feather icon-navigation"></i></button>-->
                <!--                </div>-->
                <!--            </div>-->
                <!--        </div>-->
                <!--    </div>-->
                <!--</div>-->
                <div class="col-md-12 col-12">

            <div class="row">
                <div class="col-lg-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-end">
                            <h4>مبيعات المجموعات</h4>
                            <div class="dropdown chart-dropdown">

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem1">
                                    <a class="dropdown-item" href="#">آخر 28 يوم</a>
                                    <a class="dropdown-item" href="#">الشهر الماضي</a>
                                    <a class="dropdown-item" href="#">العام الماضي</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body pt-0">
                                <div id="sales-chart" class="mb-1"></div>
                                @foreach ($groups as $group)
                                    <div class="chart-info d-flex justify-content-between mb-1">
                                        <div class="series-info d-flex align-items-center">
                                            <i class="feather icon-layers font-medium-2 text-primary"></i>
                                            <span class="text-bold-600 mx-50">{{ $group->Region->name ?? '' }}</span>
                                            <span> - {{ number_format($group->total_sales, 2) }} ريال</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                {{-- <div class="col-lg-4 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-end">
                            <h4>مبيعات المجموعات</h4>
                            <div class="dropdown chart-dropdown">

                                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownItem1">
                                    <a class="dropdown-item" href="#">آخر 28 يوم</a>
                                    <a class="dropdown-item" href="#">الشهر الماضي</a>
                                    <a class="dropdown-item" href="#">العام الماضي</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body pt-0">
                                <div id="sales-chart" class="mb-1"></div>
                                @foreach ($groups as $group)
                                    <div class="chart-info d-flex justify-content-between mb-1">
                                        <div class="series-info d-flex align-items-center">
                                            <i class="feather icon-layers font-medium-2 text-primary"></i>
                                            <span class="text-bold-600 mx-50">{{ $group->Region->name ?? '' }}</span>
                                            <span> - {{ number_format($group->total_sales, 2) }} ريال</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div> --}}

                <div class="col-lg-6 col-12">

                    <div class="card">
                        <div class="card-header d-flex justify-content-between pb-0">
                            <h4 class="card-title">مبيعات الموظفين</h4>
                            <div class="dropdown chart-dropdown">

                            </div>
                        </div>
                        <div class="card-content">
                            <div class="card-body py-0">
                                <div id="customer-charts">
                                    <script>
                                        document.addEventListener("DOMContentLoaded", function() {
                                            var options = {
                                                series: @json($chartData->pluck('percentage')),
                                                chart: {
                                                    type: 'donut',
                                                    height: 300
                                                },
                                                labels: @json($chartData->pluck('name')),
                                                colors: ['#007bff', '#ffc107', '#dc3545', '#28a745'],
                                                legend: {
                                                    position: 'bottom'
                                                },
                                                dataLabels: {
                                                    formatter: function(val) {
                                                        return val.toFixed(2) + "%";
                                                    }
                                                }
                                            };

                                            var chart = new ApexCharts(document.querySelector("#customer-charts"), options);
                                            chart.render();
                                        });
                                    </script>


                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-8 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-end">
                            <h4 class="card-title">الإيرادات</h4>
                            <p class="font-medium-5 mb-0"><i class="feather icon-settings text-muted cursor-pointer"></i>
                            </p>
                        </div>
                        <div class="card-content">
                            <div class="card-body pb-0">
                                <div class="d-flex justify-content-start">
                                    <div class="mr-2">
                                        <p class="mb-50 text-bold-600">هذا الشهر</p>
                                        <h2 class="text-bold-400">
                                            <sup class="font-medium-1">$</sup>
                                            <span class="text-success">86,589</span>
                                        </h2>
                                    </div>
                                    <div>
                                        <p class="mb-50 text-bold-600">الشهر الماضي</p>
                                        <h2 class="text-bold-400">
                                            <sup class="font-medium-1">$</sup>
                                            <span>73,683</span>
                                        </h2>
                                    </div>

                                </div>
                                <div id="revenue-chart"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-end">
                            <h4 class="mb-0">نظرة عامة على الأهداف</h4>
                            <p class="font-medium-5 mb-0"><i
                                    class="feather icon-help-circle text-muted cursor-pointer"></i></p>
                        </div>
                        <div class="card-content">
                            <div class="card-body px-0 pb-0">
                                <div id="goal-overview-chart" class="mt-75"></div>
                                <div class="row text-center mx-0">
                                    <div class="col-6 border-top border-right d-flex align-items-between flex-column py-1">
                                        <p class="mb-50">مكتمل</p>
                                        <p class="font-large-1 text-bold-700">786,617</p>
                                    </div>
                                    <div class="col-6 border-top d-flex align-items-between flex-column py-1">
                                        <p class="mb-50">قيد التقدم</p>
                                        <p class="font-large-1 text-bold-700">13,561</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">


                <div class="col-md-12 col-12">

</div>

<!-- السكربت يوضع خارج البلوك -->


            </div>
            <div class="row">
              <div class="col-lg-4 col-12">

</div>

                <div class="col-md-4 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">إحصائيات المتصفحات</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-25">
                                    <div class="browser-info">
                                        <p class="mb-25">جوجل كروم</p>
                                        <h4>73%</h4>
                                    </div>
                                    <div class="stastics-info text-right">
                                        <span>800 <i class="feather icon-arrow-up text-success"></i></span>
                                        <span class="text-muted d-block">13:16</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-primary mb-2">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="73" aria-valuemin="73"
                                        aria-valuemax="100" style="width:73%"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-25">
                                    <div class="browser-info">
                                        <p class="mb-25">أوبرا</p>
                                        <h4>8%</h4>
                                    </div>
                                    <div class="stastics-info text-right">
                                        <span>-200 <i class="feather icon-arrow-down text-danger"></i></span>
                                        <span class="text-muted d-block">13:16</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-primary mb-2">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="8" aria-valuemin="8"
                                        aria-valuemax="100" style="width:8%"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-25">
                                    <div class="browser-info">
                                        <p class="mb-25">فايرفوكس</p>
                                        <h4>19%</h4>
                                    </div>
                                    <div class="stastics-info text-right">
                                        <span>100 <i class="feather icon-arrow-up text-success"></i></span>
                                        <span class="text-muted d-block">13:16</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-primary mb-2">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="19" aria-valuemin="19"
                                        aria-valuemax="100" style="width:19%"></div>
                                </div>
                                <div class="d-flex justify-content-between mb-25">
                                    <div class="browser-info">
                                        <p class="mb-25">إنترنت إكسبلورر</p>
                                        <h4>27%</h4>
                                    </div>
                                    <div class="stastics-info text-right">
                                        <span>-450 <i class="feather icon-arrow-down text-danger"></i></span>
                                        <span class="text-muted d-block">13:16</span>
                                    </div>
                                </div>
                                <div class="progress progress-bar-primary mb-50">
                                    <div class="progress-bar" role="progressbar" aria-valuenow="27" aria-valuemin="27"
                                        aria-valuemax="100" style="width:27%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-8 col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title">الاحتفاظ بالعملاء</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div id="client-retention-chart">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">

            </div>
        </section>


    </div>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        if (!navigator.geolocation) {
            console.error("❌ المتصفح لا يدعم ميزة تحديد الموقع الجغرافي.");
            return;
        }

        // متغيرات لتخزين الإحداثيات السابقة
        let previousLatitude = null;
        let previousLongitude = null;

        // طلب الوصول إلى الموقع
        requestLocationAccess();

        function requestLocationAccess() {
            navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
                if (result.state === "granted") {
                    // إذا كان الإذن ممنوحًا مسبقًا، ابدأ بمتابعة الموقع
                    watchEmployeeLocation();
                } else if (result.state === "prompt") {
                    // إذا لم يكن الإذن ممنوحًا، اطلبه من المستخدم
                    navigator.geolocation.getCurrentPosition(
                        function () {
                            watchEmployeeLocation();
                        },
                        function (error) {
                            console.error("❌ خطأ في الحصول على الموقع:", error);
                        }
                    );
                } else {
                    console.error("⚠️ الوصول إلى الموقع محظور! يرجى تغييره من إعدادات المتصفح.");
                }
            });
        }

        // دالة لمتابعة تغييرات الموقع
        function watchEmployeeLocation() {
            navigator.geolocation.watchPosition(
                function (position) {
                    const latitude = position.coords.latitude;
                    const longitude = position.coords.longitude;

                    console.log("📍 الإحداثيات الجديدة:", latitude, longitude);

                    // التحقق من تغيير الموقع
                    if (latitude !== previousLatitude || longitude !== previousLongitude) {
                        console.log("🔄 الموقع تغير، يتم التحديث...");

                        // إرسال البيانات إلى السيرفر
                        fetch("{{ route('visits.storeEmployeeLocation') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ latitude, longitude })
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error("❌ خطأ في الشبكة");
                            }
                            return response.json();
                        })
                        .then(data => {
                            console.log("✅ تم تحديث الموقع بنجاح:", data);
                        })
                        .catch(error => {
                            console.error("❌ خطأ في تحديث الموقع:", error);
                        });

                        // تحديث الإحداثيات السابقة
                        previousLatitude = latitude;
                        previousLongitude = longitude;
                    } else {
                        console.log("⏹️ الموقع لم يتغير.");
                    }
                },
                function (error) {
                    console.error("❌ خطأ في متابعة الموقع:", error);
                },
                {
                    enableHighAccuracy: true, // دقة عالية
                    timeout: 5000, // انتظار 5 ثواني
                    maximumAge: 0 // لا تستخدم بيانات موقع قديمة
                }
            );
        }
    });
</script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var options = {
        chart: {
            type: 'bar',
            height: 350
        },
        series: [{
            name: 'المبيعات',
            data: @json($groups->pluck('total_sales'))
        }],
        xaxis: {
            categories: @json($groups->pluck('Region.name'))
        }
    };

    var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
    chart.render();
});
</script>

@endsection

