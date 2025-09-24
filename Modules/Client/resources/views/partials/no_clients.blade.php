{{-- ملف: resources/views/client/partials/no_clients.blade.php --}}
<div class="alert alert-info text-center py-5" role="alert">
    <i class="fas fa-users fa-3x mb-4 text-muted"></i>
    <h4 class="mb-3">لا توجد عملاء متاحون</h4>
    <p class="text-muted mb-4">
        @if(auth()->user()->role === 'employee')
            لا توجد زيارات عملاء مجدولة لك اليوم ({{ $currentDayName ?? '' }}).
        @else
            لا يوجد عملاء مسجلين حالياً أو لا توجد نتائج مطابقة لمعايير البحث.
        @endif
    </p>
    <div class="d-flex justify-content-center gap-2">
        @if(auth()->user()->hasPermissionTo('Add_Client'))
            <a href="{{ route('clients.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>
                إضافة عميل جديد
            </a>
        @endif
        <button type="button" class="btn btn-outline-secondary" onclick="location.reload()">
            <i class="fas fa-sync-alt me-2"></i>
            إعادة تحميل
        </button>
    </div>
</div>
