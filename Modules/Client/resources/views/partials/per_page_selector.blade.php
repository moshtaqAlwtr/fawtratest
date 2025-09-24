{{-- resources/views/client/partials/per_page_selector.blade.php --}}
<div class="d-flex align-items-center mb-2 mb-md-0">
    <label for="perPageSelect" class="me-2">أظهر</label>
    <select id="perPageSelect" class="form-select form-select-sm w-auto" style="min-width: 80px;">
        <option value="10" {{ request('perPage', 50) == 10 ? 'selected' : '' }}>10</option>
        <option value="25" {{ request('perPage', 50) == 25 ? 'selected' : '' }}>25</option>
        <option value="50" {{ request('perPage', 50) == 50 ? 'selected' : '' }}>50</option>
        <option value="100" {{ request('perPage', 50) == 100 ? 'selected' : '' }}>100</option>
    </select>
    <span class="ms-2">مدخلات</span>
</div>
