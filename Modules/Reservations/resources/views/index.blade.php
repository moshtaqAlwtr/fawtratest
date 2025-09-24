@extends('master')

@section('title')
    إدارة الحجوزات
@stop

@section('content')
    <!-- Custom Styles -->
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #34495e;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --info-color: #3498db;
            --dark-color: #2c3e50;
            --light-color: #ecf0f1;
            --white-color: #ffffff;
            --gray-light: #f8f9fa;
            --gray-medium: #dee2e6;
            --gray-dark: #6c757d;
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.15);
        }

        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .content-header-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 2.5rem;
        }

        .breadcrumb {
            background: transparent;
            padding: 0;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: "←";
            color: var(--primary-color);
            font-weight: bold;
        }

        .card {
            background: var(--white-color);
            border: 1px solid var(--gray-medium);
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-color);
        }

        .card-header {
            background: var(--white-color);
            color: var(--primary-color);
            border-bottom: 2px solid var(--gray-medium);
            font-weight: 600;
            padding: 1.5rem;
        }

        .btn {
            border-radius: 10px;
            padding: 12px 25px;
            font-weight: 600;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-outline-success {
            background: var(--white-color);
            color: var(--success-color);
            border: 2px solid var(--success-color);
        }

        .btn-outline-success:hover {
            background: var(--success-color);
            color: var(--white-color);
            border-color: var(--success-color);
        }

        .btn-outline-primary {
            background: var(--white-color);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            color: var(--white-color);
            border-color: var(--primary-color);
        }

        .btn-primary {
            background: var(--primary-color);
            color: var(--white-color);
            border: 2px solid var(--primary-color);
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-warning {
            background: var(--white-color);
            color: var(--warning-color);
            border: 2px solid var(--warning-color);
        }

        .btn-outline-warning:hover {
            background: var(--warning-color);
            color: var(--white-color);
            border-color: var(--warning-color);
        }

        .form-control {
            border: 2px solid var(--gray-medium);
            border-radius: 10px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
            background: var(--white-color);
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
            background: var(--white-color);
        }

        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 8px;
        }

        .nav-tabs {
            border: none;
            background: var(--white-color);
            border-radius: 10px;
            padding: 10px;
            box-shadow: var(--shadow-sm);
        }

        .nav-tabs .nav-link {
            border: 1px solid var(--gray-medium);
            border-radius: 8px;
            margin: 0 5px;
            padding: 12px 25px;
            font-weight: 600;
            color: var(--dark-color);
            transition: all 0.3s ease;
            background: var(--white-color);
        }

        .nav-tabs .nav-link.active {
            background: var(--primary-color);
            color: var(--white-color);
            border-color: var(--primary-color);
            box-shadow: var(--shadow);
        }

        .nav-tabs .nav-link:hover {
            background: var(--gray-light);
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .table {
            background: var(--white-color);
            border-radius: 15px;
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--gray-medium);
        }

        .table thead th {
            background: var(--primary-color);
            color: var(--white-color);
            border: none;
            font-weight: 600;
            padding: 20px 15px;
            font-size: 16px;
        }

        .table tbody td {
            padding: 20px 15px;
            border: none;
            border-bottom: 1px solid var(--gray-medium);
            vertical-align: middle;
            background: var(--white-color);
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background: var(--gray-light);
            transform: scale(1.005);
        }

        .badge {
            padding: 8px 15px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 12px;
            letter-spacing: 0.5px;
        }

        .badge.bg-warning {
            background: var(--warning-color) !important;
            color: var(--white-color);
        }

        .badge.bg-success {
            background: var(--success-color) !important;
            color: var(--white-color);
        }

        .badge.bg-danger {
            background: var(--danger-color) !important;
            color: var(--white-color);
        }

        .badge.bg-info {
            background: var(--info-color) !important;
            color: var(--white-color);
        }

        .btn-group .btn {
            border-radius: 8px;
            margin: 0 2px;
        }

        .btn-light {
            background: var(--white-color);
            border: 1px solid var(--gray-medium);
            color: var(--dark-color);
        }

        .btn-light:hover {
            background: var(--gray-light);
            border-color: var(--primary-color);
        }

        .avatar-placeholder {
            width: 60px;
            height: 60px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white-color);
            font-weight: bold;
            font-size: 20px;
            box-shadow: var(--shadow);
            border: 3px solid var(--white-color);
        }

        .stats-card {
            background: var(--white-color);
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            box-shadow: var(--shadow);
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s ease forwards;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid var(--gray-medium);
        }

        .breadcrumb-item a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .breadcrumb-item a:hover {
            color: var(--secondary-color);
        }

        .breadcrumb-item.active {
            color: var(--gray-dark);
        }
    </style>

    <!-- Header Section -->
    <div class="content-header row mb-4">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">
                        <i class="fas fa-calendar-check me-3"></i>إدارة الحجوزات
                    </h2>
                    <div class="breadcrumb-wrapper col-12 mt-3">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="#" class="text-decoration-none">
                                    <i class="fas fa-home me-1"></i>الرئيسية
                                </a>
                            </li>
                            <li class="breadcrumb-item active">عرض الحجوزات</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.error')
    @include('layouts.alerts.success')

    <!-- Stats and Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card fade-in-up">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div class="stats-card me-3 mb-2 mb-md-0">
                            <div class="stats-number">{{ $bookings->count() }}</div>
                            <div>إجمالي الحجوزات</div>
                        </div>
                        <div class="d-flex gap-3">
                            <a href="{{ route('Reservations.create') }}" class="btn btn-outline-success">
                                <i class="fa fa-plus me-2"></i>أضف حجز
                            </a>
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-primary">
                                <i class="fa fa-calendar-alt me-2"></i>المواعيد المحجوزة
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Form -->
    <div class="card mb-4 fade-in-up">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-search me-2"></i>نموذج البحث
            </h5>
        </div>
        <div class="card-body">
            <form id="clientForm" action="{{ route('Reservations.filter') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row g-4">
                    <!-- رقم -->
                    <div class="col-md-4">
                        <label for="employee-search" class="form-label">
                            <i class="fas fa-hashtag me-1"></i>رقم:
                        </label>
                        <input type="text" class="form-control" id="employee-search" name="employee"
                            placeholder="البحث بالرقم" value="{{ request('employee') }}">
                    </div>

                    <!-- العميل -->
                    <div class="col-md-4">
                        <label for="client-search" class="form-label">
                            <i class="fas fa-user me-1"></i>العميل:
                        </label>
                        <input type="text" class="form-control" id="client-search" name="client_id"
                            placeholder="البحث باسم العميل" value="{{ request('client_id') }}">
                    </div>

                    <!-- الموظف -->
                    <div class="col-md-4">
                        <label for="employee-name" class="form-label">
                            <i class="fas fa-user-tie me-1"></i>الموظف:
                        </label>
                        <input type="text" class="form-control" id="employee-name" name="employee"
                            placeholder="البحث باسم الموظف" value="{{ request('employee') }}">
                    </div>

                    <!-- الفترة من -->
                    <div class="col-md-4">
                        <label for="date_from" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>الفترة من:
                        </label>
                        <input type="date" name="date_from" id="date_from" value="{{ request('date_from') }}"
                            class="form-control">
                    </div>

                    <!-- الفترة إلى -->
                    <div class="col-md-4">
                        <label for="date_to" class="form-label">
                            <i class="fas fa-calendar-alt me-1"></i>الفترة إلى:
                        </label>
                        <input type="date" name="date_to" id="date_to" value="{{ request('date_to') }}"
                            class="form-control">
                    </div>

                    <!-- الحالة -->
                    <div class="col-md-4">
                        <label for="group_by" class="form-label">
                            <i class="fas fa-flag me-1"></i>الحالة:
                        </label>
                        <select class="form-control" id="group_by" name="status">
                            <option value="">أختر</option>
                            <option value="confirm" {{ request('status') == 'confirm' ? 'selected' : '' }}>تأكيد</option>
                            <option value="review" {{ request('status') == 'review' ? 'selected' : '' }}>تحت المراجعة
                            </option>
                            <option value="bill" {{ request('status') == 'bill' ? 'selected' : '' }}>حولت لفاتورة
                            </option>
                            <option value="cancel" {{ request('status') == 'cancel' ? 'selected' : '' }}>تم الالغاء
                            </option>
                            <option value="done" {{ request('status') == 'done' ? 'selected' : '' }}>تم</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-start mt-4 gap-3">
                    <button type="submit" class="btn btn-primary waves-effect waves-light">
                        <i class="fas fa-search me-2"></i>بحث
                    </button>
                    <button type="reset" class="btn btn-outline-warning waves-effect waves-light">
                        <i class="fas fa-undo me-2"></i>إلغاء
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabs and View Controls -->
    <div id="listView">
        <div class="card my-4 fade-in-up">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
                    <ul class="nav nav-tabs" id="sortTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="all-tab" data-bs-toggle="tab" data-bs-target="#all"
                                type="button" role="tab">
                                <i class="fas fa-list me-2"></i>الكل
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending"
                                type="button" role="tab">
                                <i class="fas fa-clock me-2"></i>قيد الانتظار
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="confirmed-tab" data-bs-toggle="tab" data-bs-target="#confirmed"
                                type="button" role="tab">
                                <i class="fas fa-check-circle me-2"></i>مؤكد
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="cancelled-tab" data-bs-toggle="tab" data-bs-target="#cancelled"
                                type="button" role="tab">
                                <i class="fas fa-times-circle me-2"></i>ملغي
                            </button>
                        </li>
                    </ul>
                    <div class="btn-group" role="group">
                        <button type="button" id="listViewBtn" class="btn btn-primary"> <i class="bi bi-list-ul"></i>
                        </button>
                        <button type="button" id="calendarViewBtn" class="btn btn-light"> <i
                                class="bi bi-grid-3x3-gap-fill"></i> </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-user-circle me-2"></i>
                                        <span>العميل</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-cogs me-2"></i>
                                        <span>الخدمة</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        <span>التاريخ</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-clock me-2"></i>
                                        <span>الوقت</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2"></i>
                                        <span>الحالة</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-dollar-sign me-2"></i>
                                        <span>الإجمالي</span>
                                    </div>
                                </th>
                                <th scope="col">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-tools me-2"></i>
                                        <span>العمليات</span>
                                    </div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($bookings as $booking)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-placeholder me-3">
                                                {{ strtoupper(substr($booking->client->trade_name, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $booking->client->trade_name }}</div>
                                                <div class="text-muted">{{ $booking->client->phone ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>{{ $booking->product->name }}</td>
                                    <td>{{ $booking->appointment_date ?? '-' }}</td>
                                    <td>{{ $booking->start_time ?? '-' }}</td>
                                    <td>
                                        <span>
                                            @if ($booking->status == 'bill')
                                                <span class="badge bg-warning">حولت لفاتورة</span>
                                            @elseif ($booking->status == 'done')
                                                <span class="badge bg-success"> مكتمل</span>
                                            @elseif ($booking->status == 'confirm')
                                                <span class="badge bg-info">مقبول</span>
                                            @elseif ($booking->status == 'review')
                                                <span class="badge bg-danger">تحت المراجعة </span>
                                                @else
                                                <span class="badge bg-danger">ملغي</span>
                                            @endif

                                        </span>
                                    </td>
                                    <td>${{ number_format($booking->total_price, 2) }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('Reservations.show', $booking->id) }}"
                                                class="btn btn-sm btn-outline-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('Reservations.edit', $booking->id) }}"
                                                class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('Reservations.destroy', $booking->id) }}"
                                                method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                                    onclick="return confirm('هل أنت متأكد؟')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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

    <div id="calendarView" style="display: none;">
        @include('reservations.partial.calender')
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const listView = document.getElementById('listView');
            const calendarView = document.getElementById('calendarView');
            const listViewBtn = document.getElementById('listViewBtn');
            const calendarViewBtn = document.getElementById('calendarViewBtn');

            listViewBtn.addEventListener('click', function() {
                listView.style.display = 'block';
                calendarView.style.display = 'none';
                listViewBtn.classList.remove('btn-light');
                listViewBtn.classList.add('btn-primary');
                calendarViewBtn.classList.remove('btn-primary');
                calendarViewBtn.classList.add('btn-light');
            });

            calendarViewBtn.addEventListener('click', function() {
                listView.style.display = 'none';
                calendarView.style.display = 'block';
                calendarViewBtn.classList.remove('btn-light');
                calendarViewBtn.classList.add('btn-primary');
                listViewBtn.classList.remove('btn-primary');
                listViewBtn.classList.add('btn-light');
            });
        });
    </script>

    <script>
        // Animation on page load
        document.addEventListener('DOMContentLoaded', function() {
            const elements = document.querySelectorAll('.fade-in-up');
            elements.forEach((element, index) => {
                setTimeout(() => {
                    element.style.animationDelay = `${index * 0.1}s`;
                }, index * 100);
            });
        });

        // Form reset
        document.querySelector('button[type="reset"]').addEventListener('click', function() {
            setTimeout(() => {
                document.getElementById('clientForm').reset();
            }, 100);
        });
    </script>

@endsection
