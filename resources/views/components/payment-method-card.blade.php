@props(['title', 'icon', 'index', 'checked' => false])

<div class="col-md-4 mb-4">
    <div class="card payment-card border" role="button" style="cursor: pointer;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center p-3">
                <div class="icon-wrapper">
                    <i class="{{ $icon }}"></i>
                </div>
                <div class="text-end">
                    <h5 class="mb-0 fw-bold">{{ $title }}</h5>
                    @if ($index !== null)
                        <small class="text-muted">الترتيب: {{ $index }}</small>
                    @endif
                </div>
            </div>

            <div class="card-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <div x-data="{ isChecked: {{ $checked ? 'true' : 'false' }} }">
                    <div class="custom-control custom-switch custom-switch-success">
                        <input type="checkbox" class="custom-control-input" :id="'switch-' + {{ $index }}"
                            x-model="isChecked"
                            x-on:change="$refs.hiddenInput.value = isChecked ? 'active' : 'inactive'"
                        >
                        <label class="custom-control-label" :for="'switch-' + {{ $index }}"></label>
                    </div>

                    <input type="hidden" x-ref="hiddenInput" name="payments[{{ $index }}][status]" 
                        :value="isChecked ? 'active' : 'inactive'">
                </div>
            </div>
        </div>
    </div>
</div>




<style>
.payment-card {
    transition: all 0.3s ease;
    border-radius: 12px !important;
    overflow: hidden;
}

.payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    border-color: var(--bs-primary) !important;
}

.card-body {
    padding: 0 !important;
}

.icon-wrapper {
    background-color: rgba(0, 0, 0, 0.05);
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
}

.icon-wrapper i {
    font-size: 2rem;
}

.card-footer {
    border-top: none !important;
    background: #f8f9fa !important;
}

.custom-switch .custom-control-input:checked ~ .custom-control-label::before {
    background-color: #28c76f !important;
    border-color: #28c76f !important;
}

.custom-control-input {
    width: 45px !important;
    height: 22px !important;
}

.action-icon {
    color: #6e7881;
    transition: all 0.2s ease;
    font-size: 1.2rem;
}

.action-icon:hover {
    color: var(--bs-primary);
    transform: scale(1.1);
}

.custom-switch .custom-control-label::before {
    height: 22px !important;
    width: 45px !important;
    border-radius: 34px !important;
}

.custom-switch .custom-control-label::after {
    height: 18px !important;
    width: 18px !important;
    border-radius: 50% !important;
}
</style>
