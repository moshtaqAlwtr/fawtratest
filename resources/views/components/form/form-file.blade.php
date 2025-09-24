@props([
    'label',
    'name',
    'multiple' => false,
    'accept' => '*/*'
])

<div class="col-{{ $attributes->get('col', '12') }} mb-3">
    <div class="form-group">
        <label for="{{ $name }}">{{ $label }}</label>
        <input type="file" name="{{ $name }}" id="{{ $name }}" class="d-none" {{ $multiple ? 'multiple' : '' }} accept="{{ $accept }}">
        <div class="upload-area border rounded p-3 text-center position-relative" onclick="document.getElementById('{{ $name }}').click()">
            <div class="d-flex align-items-center justify-content-center gap-2">
                <i class="fas fa-cloud-upload-alt text-primary"></i>
                <span class="text-primary">اضغط هنا</span>
                <span>أو</span>
                <span class="text-primary">اختر من جهازك</span>
            </div>
            <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                <i class="fas fa-file-alt fs-3 text-secondary"></i>
            </div>
        </div>
    </div>
</div>
