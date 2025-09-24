{{-- resources/views/purchases/supplier_management/partials/suppliers_table.blade.php --}}

@if ($suppliers->count() > 0)
    <table class="table">
        <thead>
            <tr>
                <th style="width: 5%">
                    <input type="checkbox" class="form-check-input" id="selectAll">
                </th>
                <th>الاسم</th>
                <th>الموقع</th>
                <th>رقم المورد</th>
                <th>رقم الجوال</th>
                <th>البريد الإلكتروني</th>
                <th>الحالة</th>
                <th style="width: 10%">خيارات</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($suppliers as $supplier)
                <tr>
                    <td>
                        <input type="checkbox" class="form-check-input supplier-checkbox" value="{{ $supplier->id }}">
                    </td>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="avatar me-2" style="background-color: #6B4423; width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                <span class="avatar-content">{{ substr($supplier->trade_name, 0, 1) }}</span>
                            </div>
                            <div>
                                <div class="fw-bold">{{ $supplier->trade_name }}</div>
                                <div class="text-muted small">#{{ $supplier->id }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @if($supplier->full_address)
                            <i class="fa fa-map-marker text-muted me-1"></i>
                            {{ Str::limit($supplier->full_address, 30) }}
                        @else
                            <span class="text-muted">غير محدد</span>
                        @endif
                    </td>
                    <td>{{ $supplier->number_suply ?: 'غير محدد' }}</td>
                    <td>{{ $supplier->mobile ?: 'غير محدد' }}</td>
                    <td>{{ $supplier->email ?: 'غير محدد' }}</td>
                    <td>
                        @if ($supplier->status == 1)
                            <button onclick="changeStatus({{ $supplier->id }}, 0, '{{ $supplier->trade_name }}')"
                                    class="btn btn-success btn-sm d-inline-flex align-items-center justify-content-center px-3"
                                    style="min-width: 90px;"
                                    title="اضغط لإيقاف المورد">
                                نشط <i class="fa fa-check ms-1"></i>
                            </button>
                        @else
                            <button onclick="changeStatus({{ $supplier->id }}, 1, '{{ $supplier->trade_name }}')"
                                    class="btn btn-outline-secondary btn-sm d-inline-flex align-items-center justify-content-center px-3"
                                    style="min-width: 90px;"
                                    title="اضغط لتفعيل المورد">
                                موقوف <i class="fa fa-ban ms-1"></i>
                            </button>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group">
                            <div class="dropdown">
                                <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm" type="button"
                                        id="dropdownMenuButton{{ $supplier->id }}"
                                        data-bs-toggle="dropdown" aria-expanded="false">
                                </button>
                                <div class="dropdown-menu dropdown-menu-end"
                                     aria-labelledby="dropdownMenuButton{{ $supplier->id }}">
                                    <a class="dropdown-item" href="{{ route('SupplierManagement.show', $supplier->id) }}">
                                        <i class="fa fa-eye me-2 text-primary"></i>عرض
                                    </a>
                                    <a class="dropdown-item" href="{{ route('SupplierManagement.edit', $supplier->id) }}">
                                        <i class="fa fa-edit me-2 text-success"></i>تعديل
                                    </a>
                                    <a class="dropdown-item text-danger" href="#" data-bs-toggle="modal"
                                       data-bs-target="#deleteModal{{ $supplier->id }}">
                                        <i class="fa fa-trash me-2"></i>حذف
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Modal delete -->
                        <div class="modal fade" id="deleteModal{{ $supplier->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header bg-danger text-white">
                                        <h5 class="modal-title">حذف المورد</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <p>هل أنت متأكد من حذف المورد "{{ $supplier->trade_name }}"؟</p>
                                        <small class="text-muted">هذا الإجراء لا يمكن التراجع عنه.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                        <form action="{{ route('SupplierManagement.destroy', $supplier->id) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger">حذف</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    @php
        $hasSearchParams = request()->filled([
            'employee_search', 'supplier_number', 'email', 'mobile', 'phone', 
            'address', 'postal_code', 'currency', 'status', 'tax_number', 
            'commercial_registration', 'created_by'
        ]);
    @endphp
    
    @if($hasSearchParams)
        <div class="alert alert-warning text-center" role="alert">
            <i class="fa fa-search fa-2x mb-2"></i>
            <h5>لا توجد نتائج</h5>
            <p class="mb-0">لا توجد نتائج تطابق معايير البحث المحددة</p>
            <button type="button" class="btn btn-outline-primary mt-2" id="clearSearchBtn">
                <i class="fa fa-refresh me-1"></i>
                إعادة تعيين البحث
            </button>
        </div>
    @else
        <div class="alert alert-light text-center" role="alert">
            <i class="fa fa-search fa-2x mb-2 text-muted"></i>
            <h5>ابدأ البحث</h5>
            <p class="mb-0">استخدم نموذج البحث أعلاه للعثور على الموردين</p>
            <a href="{{ route('SupplierManagement.create') }}" class="btn btn-success mt-2">
                <i class="fa fa-plus me-1"></i>
                أضف مورد جديد
            </a>
        </div>
    @endif
@endif

<script>
    // تفعيل checkbox "تحديد الكل"
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.supplier-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
