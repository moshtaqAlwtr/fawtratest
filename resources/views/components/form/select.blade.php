@props([
    'label',
    'name',
    'icon' => 'chevron-down',
    'required' => false
])

<div class="col-{{ $attributes->get('col', '12') }} mb-3">
    <div class="form-group">
        <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
        <div class="position-relative has-icon-left">
            <select class="form-control {{ $attributes->get('class', '') }}" 
                    id="{{ $name }}" 
                    name="{{ $name }}"
                    {{ $attributes }}>
                {{ $slot }}
            </select>
            <div class="form-control-position">
                <i class="feather icon-{{ $icon }}"></i>
            </div>
        </div>
    </div>
</div>
