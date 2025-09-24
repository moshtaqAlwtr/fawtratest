@props([
    'title' => '',
    'tools' => ''
])

<div class="card">
    @if($title)
    <div class="card-header">
        <h4 class="card-title">{{ $title }}</h4>
        @if($tools)
        <div class="card-tools">
            {{ $tools }}
        </div>
        @endif
    </div>
    @endif
    <div class="card-content">
        <div class="card-body">
            {{ $slot }}
        </div>
    </div>
</div>
