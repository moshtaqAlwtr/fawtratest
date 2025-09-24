@extends('master')

@section('title')
      اضافة الهدف العام للزيارات
@stop

@section('content')
<div class="container">
    @if(session('success'))
        <div class="alert alert-success mt-3">
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card mt-3">
        <h2 class="card-header">الهدف العام للزيارات</h2>
        <div class="card-body">
            <form method="POST" action="{{ route('target.visitTarget') }}">
                @csrf
                <div class="form-group">
                   
                    <input type="number" step="0.01" class="form-control"
                           id="value" name="value" value="{{ $target->value ?? '' }}" required>
                </div>

                <button type="submit" class="btn btn-primary mt-2">حفظ التغييرات</button>
            </form>
        </div>
    </div>
</div>
@endsection


