@extends('master')

@section('title')
    احصائيات هدف العملاء
@stop

@section('content')
 <style>
.hover-effect:hover {
    background-color: #f8f9fa;
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    transition: all 0.2s ease;
}

.progress {
    background-color: #e9ecef;
    border-radius: 10px;
}

.progress-bar {
    border-radius: 10px;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.table-responsive {
    border-radius: 8px;
    overflow: hidden;
}

#nameFilter:focus, #groupFilter:focus, #sortFilter:focus {
    border-color: #86b7fe;
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}


</style>

<style>
    /* تنسيق DataTables */
    #clientsTable1_filter input {
        border-radius: 5px;
        padding: 5px 10px;
        border: 1px solid #ddd;
    }
    
    /* تنسيق البادجات */
    .badge.bg-success { background-color: #28a745!important; }
    .badge.bg-warning { background-color: #ffc107!important; color: #212529!important; }
    .badge.bg-danger { background-color: #dc3545!important; }
    
    /* تأثيرات الصفوف */
    #clientsTable1 tbody tr:hover {
        background-color: #f8f9fa;
        transition: background-color 0.2s;
    }
    
    /* شريط التقدم */
    .progress-bar {
        transition: width 0.6s ease;
    }
    
    /* تكييف DataTables مع التصميم العربي */
    .dataTables_wrapper .dataTables_paginate .paginate_button {
        margin: 0 3px;
        padding: 5px 10px;
        border-radius: 4px;
    }
</style>

<style>
    .form-label {
        font-weight: 500;
        margin-bottom: 0.5rem;
        color: #495057;
    }
    
    .form-control, .form-select {
        border-radius: 4px;
        padding: 8px 12px;
        border: 1px solid #ced4da;
        transition: border-color 0.15s ease-in-out;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
    
    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        font-weight: 500;
    }
    
    .card {
        border: 1px solid rgba(0, 0, 0, 0.125);
        border-radius: 6px;
        box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    }
</style>
<div class="card-body">
    <!-- الجزء العلوي: البحث السريع والتصفية -->
    <div class="card p-3 mb-4">
        <div class="row g-3 align-items-end">
            <!-- حقل البحث -->
            <div class="col-md-4 col-12">
                <label for="nameFilter" class="form-label">البحث السريع</label>
                <input type="text" id="nameFilter" class="form-control" placeholder="ابحث  بالاسم او المبلغ...">

               
            </div>

            <!-- فلترة الفئة -->
          
            <!-- ترتيب النتائج -->
            <div class="col-md-6 col-12">
                <label for="sortFilter" class="form-label">ترتيب النتائج</label>
                <select id="sortFilter" class="form-control">
                    <option value="high">الأعلى تحصيلاً</option>
                    <option value="low">الأقل تحصيلاً</option>
                </select>
            </div>

            <!-- زر الإعادة -->
            <div class="col-md-2 col-12 d-grid">
                <button id="resetFilters" class="btn btn-outline-secondary">
                    <i class="fas fa-undo me-1"></i> إعادة تعيين
                </button>
            </div>
        </div>
    </div>

    <!-- الجزء السفلي: تصفية حسب التاريخ -->
   <div class="card p-3 mb-4">
    <form method="GET" action="{{ route('statistics.groupall') }}" id="yearFilterForm">
        <div class="row g-3 align-items-end">
            <div class="col-md-4 col-12">
                <label for="year" class="form-label">السنة</label>
                <select name="year" id="year" class="form-control">
                    @php
                        $currentYear = now()->year;
                        $startYear = 2022; // غيّرها حسب أقدم سنة عندك في البيانات
                    @endphp
                    @for ($y = $currentYear; $y >= $startYear; $y--)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2 col-12 d-grid">
                <button class="btn btn-primary w-100" type="submit">
                    <i class="fas fa-filter me-1"></i> تطبيق
                </button>
            </div>
            <div class="col-md-2 col-12 d-grid">
                <a href="{{ route('statistics.groupall') }}" class="btn btn-outline-danger w-100">
                    <i class="fas fa-times me-1"></i> إلغاء
                </a>
            </div>
        </div>
    </form>
</div>

</div>


<div class="card">
    <div class="card-body">
        <h5 class="text-center mb-4 fw-bold">📊 إحصائيات تحصيل المجموعات</h5>
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="fas fa-info-circle me-2"></i>
    <div>
        <strong>شرح الرموز:</strong>
        <ul class="mb-0 mt-1 ps-4">
            <li><span class="text-success">↑</span> زيادة في التحصيل مقارنة بالشهر السابق</li>
            <li><span class="text-danger">↓</span> انخفاض في التحصيل مقارنة بالشهر السابق</li>
            <li><span class="text-muted">→</span> لا تغيير في التحصيل مقارنة بالشهر السابق</li>
        </ul>
    </div>
</div>

        @if($regionPerformance->count())
        <div class="table-responsive" style="overflow-x: auto;">
            <table id="clientsTable1" class="table table-bordered table-striped">

            
               <thead>
    <tr>
        <th>المجموعة</th>
        @for($m = 1; $m <= 12; $m++)
            <th>{{ \Carbon\Carbon::create()->month($m)->locale('ar')->translatedFormat('F') }}</th>
        @endfor
          <th>الإجمالي</th>
    </tr>
</thead>
<tbody>
@foreach ($regionPerformance as $region)
    <tr>
        <td>{{ $region->region_name }}</td>
        @php
            $previous = null;
        @endphp
        
        @for ($m = 1; $m <= 12; $m++)
            @php
                $current = $region->monthly[$m];
                $icon = '';
                $color = '';

                if (!is_null($previous)) {
                    if ($current > $previous) {
                        $icon = '↑'; $color = 'text-success';
                    } elseif ($current < $previous) {
                        $icon = '↓'; $color = 'text-danger';
                    } else {
                        $icon = '→'; $color = 'text-muted';
                    }
                }

                $previous = $current;
            @endphp
            <td>
                {{ number_format($current) }}
                @if ($icon)
                    <span class="{{ $color }}" style="font-size: 14px;">{{ $icon }}</span>
                @endif
            </td>
        @endfor
        <td class="fw-bold">{{ $region->total_collected }}</td>
    </tr>
@endforeach
</tbody>


            </table>
        </div>
        @else
            <div class="alert alert-info text-center mt-4">
                لا توجد بيانات متاحة لعرض التحصيل.
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    var table = $('#clientsTable1').DataTable({
        paging: false,
        info: false,
        searching: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json'
        },
        order: [[13, 'desc']], // الترتيب حسب عمود الإجمالي
        columnDefs: [
            {
                targets: 13, // عمود الإجمالي
                type: 'num',
                render: function(data, type, row) {
                    if (type === 'sort' || type === 'type') {
                        return parseFloat(data.replace(/,/g, '')) || 0;
                    }
                    return data;
                }
            }
        ],
        initComplete: function() {
            $('.dataTables_filter input').attr('placeholder', 'ابحث هنا...');
        }
    });

    // البحث السريع
    $('#nameFilter').on('keyup', function() {
        table.search(this.value).draw();
    });

    // الترتيب
    $('#sortFilter').on('change', function() {
        if (this.value === 'high') {
            table.order([13, 'desc']).draw();
        } else {
            table.order([13, 'asc']).draw();
        }
    });

    // إعادة التصفية
    $('#resetFilters').click(function() {
        $('#nameFilter').val('');
        $('#sortFilter').val('high');
        table.search('').order([13, 'desc']).draw();
    });
});

</script>

<script>
    
        function handleRowClick(event, url) {
            let target = event.target;

            // السماح بالنقر على العناصر التالية بدون تحويل
            if (target.tagName.toLowerCase() === 'a' ||
                target.closest('.dropdown-menu') ||
                target.closest('.btn') ||
                target.closest('.form-check-input')) {
                return;
            }

            // تحويل المستخدم لصفحة العميل عند الضغط على الصف
            window.location = url;
        }
</script>



@section('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    var table = $('#clientsTable1').DataTable({
        dom: '<"top"f>rt<"bottom"lip><"clear">',
        language: {
            url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json'
        },
        order: [[13, 'desc']],
        initComplete: function() {
            $('.dataTables_filter input').attr('placeholder', 'ابحث هنا...');
        }
    });

    $('#nameFilter').on('keyup', function() {
        table.search(this.value).draw();
    });

    $('#sortFilter').on('change', function() {
        const index = 13;
        if (this.value === 'high') {
            table.order([index, 'desc']).draw();
        } else {
            table.order([index, 'asc']).draw();
        }
    });

    $('#resetFilters').click(function() {
        $('#nameFilter').val('');
        $('#sortFilter').val('high');
        table.search('').order([13, 'desc']).draw();
    });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // إعادة تعيين كل الفلاتر
    document.getElementById('resetFilters').addEventListener('click', function() {
        // إعادة تعيين الفلاتر العلوية
        document.getElementById('nameFilter').value = '';
        document.getElementById('groupFilter').value = '';
        document.getElementById('sortFilter').value = 'high';
        
        // إعادة تعيين فلاتر التاريخ
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        
        // إرسال الفورم لتطبيق التغييرات
        document.getElementById('dateFilterForm').submit();
    });

    // إعادة تعيين فلاتر التاريخ فقط
    document.getElementById('resetDateFilter').addEventListener('click', function() {
        document.getElementById('date_from').value = '';
        document.getElementById('date_to').value = '';
        document.getElementById('dateFilterForm').submit();
    });
});
</script>


@endsection
@endsection

