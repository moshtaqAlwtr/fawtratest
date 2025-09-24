@props([
    'label',
    'name',
    'type' => 'text',
    'icon' => 'edit',
    'value' => '',
    'required' => false
])

<div class="col-{{ $attributes->get('col', '12') }} mb-3">
    <div class="form-group">
        <label for="{{ $name }}">{{ $label }} @if($required)<span class="text-danger">*</span>@endif</label>
        <div class="position-relative has-icon-left">
            <input type="{{ $type }}" 
                   name="{{ $name }}" 
                   id="{{ $name }}"
                   class="form-control {{ $attributes->get('class', '') }}" 
                   value="{{ old($name, $value) }}"
                   {{ $attributes }}>
            <div class="form-control-position">
                <i class="feather icon-{{ $icon }}"></i>
            </div>
        </div>
    </div>
</div>
