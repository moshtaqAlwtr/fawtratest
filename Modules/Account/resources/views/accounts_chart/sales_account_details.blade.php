@extends('master')

@section('CSS')
<link href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
<style>
    body {
        background-color: #f8f9fa;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    .card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    }
    .card-header {
        border-radius: 15px 15px 0 0;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
        font-weight: bold;
    }
    .table-hover tbody tr:hover {
        background-color: #f1f3f5;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }
    .alert {
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .breadcrumb {
        background-color: transparent;
        padding: 0;
    }
    .breadcrumb-item a {
        color: #007bff;
        text-decoration: none;
        transition: color 0.3s ease;
    }
    .breadcrumb-item a:hover {
        color: #0056b3;
    }
    .breadcrumb-item.active {
        color: #6c757d;
    }
    .modal-content {
        border-radius: 15px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
    }
    .modal-header {
        border-radius: 15px 15px 0 0;
        background: linear-gradient(135deg, #007bff, #0056b3);
        color: white;
    }
    .btn-close-white {
        filter: invert(1);
    }
    .btn-primary {
        background: linear-gradient(135deg, #007bff, #0056b3);
        border: none;
        transition: background 0.3s ease;
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, #0056b3, #003d80);
    }
    .text-danger {
        color: #dc3545 !important;
    }
    .text-success {
        color: #28a745 !important;
    }
    .font-weight-bold {
        font-weight: 600;
    }
</style>
@endsection

@section('content')
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <strong>{{ session('success') }}</strong>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="content-header row mb-4">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <h2 class="content-header-title">تفاصيل الحساب</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                    <li class="breadcrumb-item active" aria-current="page">عرض</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="card shadow-lg">
        <div class="card-header">
            <h4 class="card-title">قائمة تفاصيل الحساب</h4>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>العملية</th>
                            <th>مدين</th>
                            <th>دائن</th>
                            <th>الرصيد بعد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $totalBalance = 0; // متغير تراكمي لحساب الرصيد
                        @endphp
                        @foreach ($journalEntries as $entry)
                            @php
                                $totalBalance += $entry->credit - $entry->debit; // تحديث الرصيد بناءً على العملية الحالية
                            @endphp
                            <tr class="entry-row" data-entry-id="{{ $entry->id }}">
                                <td>
                                    <a href="#" class="font-weight-bold" data-bs-toggle="modal" data-bs-target="#entryDetailsModal{{ $entry->id }}">
                                        {{ $entry->id }} - {{ $entry->created_at }}
                                    </a>
                                    <div class="text-muted">{{ $entry->invoice_number }}</div>
                                    <div class="text-muted">{{ $entry->description }}</div>
                                </td>
                                <td class="text-danger">{{ number_format($entry->debit, 2) }}</td>
                                <td class="text-success">{{ number_format($entry->credit, 2) }}</td>
                                <td class="font-weight-bold">{{ number_format($totalBalance, 2) }}</td>
                            </tr>

                            <!-- Modal for each entry -->
                            <div class="modal fade" id="entryDetailsModal{{ $entry->id }}" tabindex="-1" role="dialog" aria-labelledby="entryDetailsModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="entryDetailsModalLabel">تفاصيل الحساب</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            @include('accounts.journal.pdf', ['entry' => $entry]) <!-- تأكد من أن هذا الملف لا يستخدم journal_id -->
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0-alpha1/js/bootstrap.bundle.min.js"></script>
@endsection
