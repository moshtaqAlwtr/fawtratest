{{-- ملف: resources/views/stock/products/partials/products_list.blade.php --}}

@if (isset($products) && !empty($products) && count($products) > 0)
    <div class="table">
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th style="width: 5%">
                        <input type="checkbox" class="form-check-input" id="selectAll">
                    </th>
                    <th>اسم المنتج</th>
                    <th>المنشئ والتاريخ</th>
                    <th>الحالة والمخزون</th>
                    <th>الأسعار</th>
                    <th>التصنيف والنوع</th>
                    <th style="width: 10%">خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $product)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input product-checkbox" value="{{ $product->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="avatar me-2" style="background-color: #4B6584">
                                    <span class="avatar-content">{{ Str::upper(substr($product->name, 0, 1)) }}</span>
                                </div>
                                <div>
                                    <strong>{{ $product->name }}</strong>
                                    <div class="text-muted small">#{{ $product->serial_number }}</div>
                                    @if($product->barcode)
                                        <div class="text-muted small">
                                            <i class="fa fa-barcode"></i> {{ $product->barcode }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ \Carbon\Carbon::parse($product->created_at)->format('Y-m-d H:i') }}<br>
                            <small class="text-muted">أضيف بواسطة: {{ $product->user->name ?? 'غير محدد' }}</small>
                        </td>
                        <td>
                            {{-- حالة المنتج والمخزون --}}
                            @if($product->type == "products" || $product->type == "compiled")
                                @if ($product->totalQuantity() > 0)
                                    <span class="badge bg-success">في المخزن</span>
                                    <br>
                                    <small class="text-success">
                                        <i class="fa fa-cubes"></i> {{ number_format($product->totalQuantity()) }} متاح
                                    </small>
                                @else
                                    <span class="badge bg-danger">مخزون نفد</span>
                                    <br>
                                    <small class="text-danger">
                                        <i class="fa fa-cubes"></i> {{ number_format($product->totalQuantity()) }} متاح
                                    </small>
                                @endif
                            @else
                                {{-- للخدمات --}}
                                <span class="badge {{ $product->status == 0 ? 'bg-success' : ($product->status == 1 ? 'bg-danger' : 'bg-secondary') }}">
                                    {{ $product->status == 0 ? 'نشط' : ($product->status == 1 ? 'موقوف' : 'غير نشط') }}
                                </span>
                                <br>
                                <small class="text-muted">خدمة</small>
                            @endif
                        </td>
                        <td>
                            @php
                                $currency = $account_setting->currency ?? 'SAR';
                                $currencySymbol = $currency == 'SAR' || empty($currency)
                                    ? '<img src="' . asset('assets/images/Saudi_Riyal.svg') . '" alt="ريال سعودي" width="15" style="vertical-align: middle;">'
                                    : $currency;
                            @endphp

                            @if ($product->purchase_price)
                                <div class="mb-1">
                                    <i class="fa fa-shopping-cart text-primary"></i>
                                    <small>{{ number_format($product->purchase_price, 2) }} {!! $currencySymbol !!}</small>
                                    <br><small class="text-muted">سعر الشراء</small>
                                </div>
                            @endif

                            @if ($product->sale_price)
                                <div>
                                    <i class="fa fa-tag text-success"></i>
                                    <small>{{ number_format($product->sale_price, 2) }} {!! $currencySymbol !!}</small>
                                    <br><small class="text-muted">سعر البيع</small>
                                </div>
                            @endif

                            @if (!$product->purchase_price && !$product->sale_price)
                                <small class="text-muted">غير محدد</small>
                            @endif
                        </td>
                        <td>
                            {{-- التصنيف والنوع --}}
                            @if($product->category)
                                <span class="badge bg-info mb-1">{{ $product->category->name ?? 'غير محدد' }}</span>
                                <br>
                            @endif

                            @if($product->brand)
                                <span class="badge bg-secondary mb-1">{{ $product->brand }}</span>
                                <br>
                            @endif

                            <small class="text-muted">
                                {{ $product->type == 'products' ? 'منتج' : ($product->type == 'services' ? 'خدمة' : ($product->type == 'compiled' ? 'منتج تجميعي' : 'غير محدد')) }}
                            </small>

                            {{-- نوع التتبع --}}
                            @if($product->track_inventory !== null)
                                <br>
                                <small class="text-muted">
                                    تتبع:
                                    @switch($product->track_inventory)
                                        @case(0)
                                            الرقم التسلسلي
                                            @break
                                        @case(1)
                                            رقم الشحنة
                                            @break
                                        @case(2)
                                            تاريخ الانتهاء
                                            @break
                                        @case(3)
                                            رقم الشحنة وتاريخ الانتهاء
                                            @break
                                        @case(4)
                                            الكمية فقط
                                            @break
                                        @default
                                            غير محدد
                                    @endswitch
                                </small>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                            type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-end">
                                        <a class="dropdown-item" href="{{ route('products.show', $product->id) }}">
                                            <i class="fa fa-eye me-2 text-primary"></i>عرض
                                        </a>
                                        <a class="dropdown-item" href="{{ route('products.edit', $product->id) }}">
                                            <i class="fa fa-edit me-2 text-success"></i>تعديل
                                        </a>
                                        @if($product->type == 'products')
                                            <a class="dropdown-item" href="#">
                                                <i class="fa fa-warehouse me-2 text-info"></i>إدارة المخزون
                                            </a>
                                        @endif
                                        <a class="dropdown-item" href="#" target="_blank">
                                            <i class="fa fa-print me-2 text-info"></i>طباعة الباركود
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:void(0)"
                                           onclick="confirmDelete({{ $product->id }}, '{{ $product->name }}')">
                                            <i class="fa fa-trash me-2"></i>حذف
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- الترقيم --}}
    <div class="d-flex justify-content-between align-items-center mt-3">
        <div class="text-muted">
            عرض {{ $products->firstItem() ?? 0 }} إلى {{ $products->lastItem() ?? 0 }}
            من {{ $products->total() }} منتج
        </div>
        <div>
            {{ $products->appends(request()->query())->links('pagination::bootstrap-5') }}
        </div>
    </div>

@else
    <div class="alert alert-info text-center" role="alert">
        <i class="fa fa-info-circle me-2"></i>
        <p class="mb-0">لا توجد منتجات تطابق معايير البحث</p>
    </div>
@endif

<style>
/* تحسينات إضافية للجدول */
.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 16px;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
    transition: background-color 0.15s ease-in-out;
}

.dropdown-menu {
    border-radius: 10px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    border: none;
    min-width: 180px;
}

.dropdown-item {
    padding: 10px 15px;
    transition: all 0.3s ease;
    border-radius: 5px;
    margin: 2px 5px;
}

.dropdown-item:hover {
    background: linear-gradient(135deg, #f8f9ff, #e3f2fd);
    transform: translateX(5px);
}

.badge {
    font-size: 0.75rem;
    padding: 0.25rem 0.5rem;
}

.pagination .page-link {
    border-radius: 8px;
    margin: 0 2px;
    border: 2px solid #e0e0e0;
    color: #667eea;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
    color: white;
    transform: translateY(-2px);
}

.pagination .page-item.active .page-link {
    background: linear-gradient(135deg, #667eea, #764ba2);
    border-color: #667eea;
}

/* تحسين عرض الأسعار */
.price-section {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

/* تحسين شكل التصنيفات */
.category-badges {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

/* تأثيرات الانيميشن */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.table tbody tr {
    animation: fadeInUp 0.6s ease-out;
}

/* تحسين شكل checkbox */
.form-check-input:checked {
    background-color: #667eea;
    border-color: #667eea;
}

.form-check-input:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
}
</style>

<script>
// JavaScript للتحكم في تحديد كل المنتجات
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const productCheckboxes = document.querySelectorAll('.product-checkbox');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            productCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });
    }

    // تحديث حالة "تحديد الكل" عند تغيير حالة المنتجات الفردية
    productCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.product-checkbox:checked').length;
            const totalCount = productCheckboxes.length;

            selectAllCheckbox.checked = checkedCount === totalCount;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        });
    });
});
</script>
