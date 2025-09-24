@extends('master')

@section('content')
    <style>
        .collection-report {
            background: #f8f9fa;
            min-height: 100vh;
            padding: 2rem 0;
        }

        .report-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
        }

        .report-header {
            background: linear-gradient(135deg, #e4e4e4 0%, #f5f8fb 100%);
            color: white;
            padding: 2rem;
            margin: -1.5rem -1.5rem 2rem -1.5rem;
            border-radius: 12px 12px 0 0;
            text-align: center;
        }

        .report-title {
            font-size: 1.8rem;
            font-weight: 600;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .search-section {
            background: #f8f9fb;
            padding: 1.5rem;
            border-radius: 8px;
            border: 1px solid #e9ecef;
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 0.75rem;
            transition: all 0.3s ease;
            font-size: 0.95rem;
        }

        .form-control:focus {
            border-color: #2c3e50;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
            outline: none;
        }

        .btn-search {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: white;
            border: none;
            padding: 0.75rem 2rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.3);
        }

        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.4);
            color: white;
        }

        .info-alert {
            background: linear-gradient(135deg, #3498db 0%, #5dade2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
            font-weight: 500;
            text-align: center;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .table {
            margin-bottom: 0;
            font-size: 0.95rem;
        }

        .table thead th {
            background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
            color: rgb(5, 5, 5);
            border: none;
            padding: 1rem;
            font-weight: 600;
            text-align: center;
            border-bottom: 3px solid #1a252f;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
        }

        .table tbody td {
            padding: 1rem;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #e9ecef;
            font-weight: 500;
        }

        .table tbody tr:nth-child(even) {
            background-color: #f8f9fb;
        }

        .employee-name {
            font-weight: 600;
            color: #2c3e50;
        }

        .amount {
            font-family: 'Arial', monospace;
            font-weight: 600;
        }

        .amount.positive {
            color: #27ae60;
        }

        .amount.negative {
            color: #e74c3c;
        }

        .amount.total {
            color: #2c3e50;
        }

        .total-row {
            background: linear-gradient(135deg, #ecf0f1 0%, #bdc3c7 100%) !important;
            font-weight: 700;
            border-top: 3px solid #dbdbdb;
        }

        .total-row td {
            padding: 1.2rem 1rem;
            color: #2c3e50;
            font-size: 1.1rem;
        }

        @media (max-width: 768px) {
            .collection-report {
                padding: 1rem 0;
            }

            .report-card {
                margin: 0 10px;
            }

            .report-header {
                padding: 1.5rem;
                margin: -1rem -1rem 1.5rem -1rem;
            }

            .report-title {
                font-size: 1.5rem;
            }

            .search-section {
                padding: 1rem;
            }

            .btn-search {
                width: 100%;
                margin-top: 1rem;
            }

            .table-container {
                overflow-x: auto;
            }

            .table {
                min-width: 800px;
            }
        }
    </style>

    <div class="collection-report">
        <div class="container">
            <div class="card report-card">
                <div class="card-body" style="padding: 1.5rem;">

                    <div class="report-header">
                        <h4 class="report-title">تقرير التحصيل اليومي/الفترات</h4>
                    </div>

                    <div class="search-section">
                        <form method="GET">
                            <div class="row">
                                <div class="col-md-3 form-group">
                                    <label class="form-label">بحث بيوم محدد</label>
                                    <input type="date" name="date" value="{{ $selectedDate }}" class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">من تاريخ</label>
                                    <input type="date" name="from_date" value="{{ $fromDate }}" class="form-control">
                                </div>
                                <div class="col-md-3 form-group">
                                    <label class="form-label">إلى تاريخ</label>
                                    <input type="date" name="to_date" value="{{ $toDate }}" class="form-control">
                                </div>
                                <div class="col-md-3 form-group d-flex align-items-end">
                                    <button type="submit" class="btn btn-search">
                                        <i class="fas fa-search me-2"></i>عرض
                                    </button>
                                    <a href="{{ route('daily_closing_entry') }}" class="btn btn-search">
                                        <i class="fas fa-window-close me-2"></i>إلغاء
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    @if ($isRangeSearch)
                        <div class="alert info-alert">
                            <i class="fas fa-calendar-alt me-2"></i>
                            يتم عرض النتائج للفترة من {{ $fromDate }} إلى {{ $toDate }}
                        </div>
                    @else
                        <div class="alert info-alert">
                            <i class="fas fa-calendar-day me-2"></i>
                            يتم عرض النتائج ليوم {{ $selectedDate }}
                        </div>
                    @endif

                    <div class="table-container">
                        <table class="table" id="collectionTable">
                            <thead>
                                <tr>
                                    <th>اسم الموظف</th>
                                    <th>المدفوعات</th>
                                    <th>سندات القبض</th>
                                    <th>سندات الصرف</th>
                                    <th>الإجمالي قبل الصرف</th>
                                    <th>المحصل النهائي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalPayments = 0;
                                    $totalReceipts = 0;
                                    $totalBeforeExpenses = 0;
                                    $totalExpenses = 0;
                                    $finalTotal = 0;
                                @endphp

                                @foreach ($cards as $card)
                                    @php
                                        $beforeExpenses = $card['payments'] + $card['receipts'];
                                        $final = $beforeExpenses - $card['expenses'];

                                        $totalPayments += $card['payments'];
                                        $totalReceipts += $card['receipts'];
                                        $totalBeforeExpenses += $beforeExpenses;
                                        $totalExpenses += $card['expenses'];
                                        $finalTotal += $final;
                                    @endphp
                                    <tr>
                                        <td class="employee-name">{{ $card['name'] }}</td>
                                        <td class="amount positive">{{ number_format($card['payments'], 2) }}</td>
                                        <td class="amount positive">{{ number_format($card['receipts'], 2) }}</td>
                                        <td class="amount negative">{{ number_format($card['expenses'], 2) }}</td>
                                        <td class="amount total">{{ number_format($beforeExpenses, 2) }}</td>
                                        <td class="amount total">{{ number_format($final, 2) }}</td>
                                    </tr>
                                @endforeach

                                {{-- صف الإجمالي --}}

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>الإجمالي</th>
                                    <th class="amount positive">{{ number_format($totalPayments, 2) }}</th>
                                    <th class="amount positive">{{ number_format($totalReceipts, 2) }}</th>
                                    <th class="amount negative">{{ number_format($totalExpenses, 2) }}</th>
                                    <th class="amount total">{{ number_format($totalBeforeExpenses, 2) }}</th>
                                    <th class="amount total">{{ number_format($finalTotal, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#collectionTable').DataTable({
                language: {
                    url: "//cdn.datatables.net/plug-ins/1.10.25/i18n/Arabic.json"
                },
                responsive: true,
                pageLength: 25,
                order: [
                    [0, 'asc']
                ],
                columnDefs: [{
                    targets: [1, 2, 3, 4, 5],
                    className: 'text-center'
                }],
                footerCallback: function(row, data, start, end, display) {
                    // يمكن إضافة منطق إضافي للتذييل هنا إذا لزم الأمر
                }
            });
        });
    </script>
@endpush
