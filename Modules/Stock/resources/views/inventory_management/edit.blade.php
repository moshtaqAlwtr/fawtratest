@extends('master')

@section('title', 'تنفيذ الجرد')

@section('content')
<div class="card">
<div class="container mt-4">
    <!-- أزرار -->
    <div class="d-flex justify-content-between mb-3">
        <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addProductModal">إضافة منتج</button>
        <button class="btn btn-secondary" id="addAllBtn">إضافة كل المنتجات</button>
    </div>
 

    <!-- نموذج الحفظ -->
    <form id="final-form" action="{{ route('inventory.save_final', $adjustment->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>المنتج</th>
                    <th>الكمية بالنظام</th>
                    <th>الكمية الفعلية</th>
                    <th>الفارق</th>
                    <th>صورة</th>
                    <th>ملاحظات</th>
                    <th>إجراء</th>
                </tr>
            </thead>
            <tbody id="products-body">
                <!-- الصفوف تُضاف هنا -->
            </tbody>
        </table>

        <button type="submit" class="btn btn-primary">تم</button>
    </form>
</div>
</div>
<!-- Modal لإضافة منتج -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="productForm" onsubmit="addProductToTable(event)">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">إضافة منتج</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label>المنتج</label>
                        <select class="form-select" id="productSelect" required>
                            <option disabled selected>اختر المنتج</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" 
                                        data-name="{{ $product->name }}" 
                                        
                                        data-system="{{ $product->current_quantity }}">
                                    {{ $product->name }} ({{ $product->current_quantity }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>الكمية الفعلية</label>
                        <input type="number" class="form-control" id="realQty" required>
                    </div>
                    <div class="mb-3">
                        <label>ملاحظات</label>
                        <textarea class="form-control" id="note"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">حفظ ومتابعة</button>
                </div>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
  // تعديل دالة addProductToTable
function addProductToTable(event) {
    event.preventDefault();
    
    const select = document.getElementById('productSelect');
    const selected = select.options[select.selectedIndex];
    const productId = selected.value;

    const row = document.createElement('tr');
    row.innerHTML = `
        <td>
            ${selected.dataset.name}
            <input type="hidden" name="items[${productId}][product_id]" value="${productId}">
        </td>
        <td>
            ${selected.dataset.system}
            <input type="hidden" name="items[${productId}][quantity_in_system]" value="${selected.dataset.system}">
        </td>
        <td>
            <input type="number" name="items[${productId}][quantity_in_stock]" class="form-control" required>
        </td>
        <td class="difference-cell">0</td>
        <td>
            <input type="file" name="items[${productId}][image]" class="form-control">
        </td>
        <td>
            <textarea name="items[${productId}][note]" class="form-control"></textarea>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">إزالة</button>
        </td>
    `;

    document.getElementById('products-body').appendChild(row);
    document.getElementById('productForm').reset();
    const modal = bootstrap.Modal.getInstance(document.getElementById('addProductModal'));
    modal.hide();
}

// تعديل زر إضافة كل المنتجات
document.getElementById('addAllBtn').addEventListener('click', function() {
    @foreach($products as $product)
        const productId = {{ $product->id }};
        const existing = document.querySelector(`input[name="items[${productId}][product_id]"]`);
        
        if (!existing) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>
                    {{ $product->name }}
                    <input type="hidden" name="items[${productId}][product_id]" value="${productId}">
                </td>
                <td>
                    {{ $product->current_quantity }}
                    <input type="hidden" name="items[${productId}][quantity_in_system]" value="{{ $product->current_quantity }}">
                </td>
                <td>
                    <input type="number" name="items[${productId}][quantity_in_stock]" class="form-control" required>
                </td>
                <td class="difference-cell">0</td>
                <td>
                    <input type="file" name="items[${productId}][image]" class="form-control">
                </td>
                <td>
                    <textarea name="items[${productId}][note]" class="form-control"></textarea>
                </td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">إزالة</button>
                </td>
            `;
            document.getElementById('products-body').appendChild(row);
        }
    @endforeach
});

// حساب الفارق تلقائياً
document.addEventListener('input', function(e) {
    if (e.target.name && e.target.name.includes('quantity_in_stock')) {
        const row = e.target.closest('tr');
        const systemQty = parseFloat(row.querySelector('input[name*="quantity_in_system"]').value);
        const realQty = parseFloat(e.target.value);
        const difference = realQty - systemQty;
        
        const differenceCell = row.querySelector('.difference-cell');
        differenceCell.textContent = difference > 0 ? '+' + difference : difference;
        differenceCell.className = 'difference-cell ' + 
            (difference > 0 ? 'text-success' : difference < 0 ? 'text-danger' : '');
    }
});
</script>
@endsection