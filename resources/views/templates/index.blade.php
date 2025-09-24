@extends('master')

@section('title')
  تصاميم الفواتير وعروض الاسعار
@endsection
<style>
    .card {
    transition: all 0.3s ease;
    border: 1px solid rgba(0,0,0,0.125);
}
.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
.card-img-top {
    border-bottom: 1px solid rgba(0,0,0,0.125);
}
</style>
@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h1>قوالب الفواتير</h1>
        </div>
        <!--<div class="col-md-6 text-left">-->
        <!--    <a href="#" class="btn btn-primary" data-toggle="modal" data-target="#newTemplateModal">-->
        <!--        إضافة قالب جديد-->
        <!--    </a>-->
        <!--</div>-->
    </div>

    <div class="row">
        @foreach($templates as $template)
       <div class="col-md-4 mb-4">
    <div class="card h-100">
        <!-- صورة القالب (افتراضية إذا لم توجد صورة) -->
        <div class="card-img-top" style="height: 150px; background-color: #f8f9fa; display: flex; align-items: center; justify-content: center;">
            @if($template->thumbnail)
                <img src="{{ asset('storage/' . $template->thumbnail) }}" 
                     alt="{{ $template->name }}"
                     style="height: 100%; width: 100%; object-fit: cover;">
            @else
                <div class="text-center p-3">
                    <i class="fas fa-file-invoice fa-3x text-secondary mb-2"></i>
                    <p class="text-muted mb-0">{{ $template->name }}</p>
                </div>
            @endif
        </div>

        <div class="card-body d-flex flex-column">
            <h5 class="card-title">{{ $template->name }}</h5>
            
            <div class="mt-auto">
                <div class="btn-group w-100">
                    <a href="{{ route('templates.edit', $template) }}" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i> تعديل
                    </a>
                    
                    @if(!$template->is_default)
                    <form action="{{ route('templates.destroy', $template) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash"></i> حذف
                        </button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
        @endforeach
    </div>
</div>

<!-- Modal لإضافة قالب جديد -->

@endsection