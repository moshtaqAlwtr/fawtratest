
{{-- resources/views/client/partials/credit_limit_modal.blade.php --}}
<div class="modal fade" id="creditLimitModal" tabindex="-1" aria-labelledby="creditLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="creditLimitModalLabel">تعديل الحد الائتماني</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('clients.update_credit_limit') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="credit_limit" class="form-label">
                            الحد الائتماني الحالي:
                            <span id="current_credit_limit">{{ $creditLimit->value ?? 'غير محدد' }}</span>
                        </label>
                        <input type="number" class="form-control" id="credit_limit" name="value"
                               value="{{ $creditLimit->value ?? '' }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
