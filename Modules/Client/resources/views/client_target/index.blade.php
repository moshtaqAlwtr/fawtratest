



@extends('master')

@section('title')
      اضافة الهدف  العام
@stop

@section('content')
<div class="card">
<div class="container">
    <h2>الهدف العام للعملاء</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('target.client.update') }}">
        @csrf
        <div class="form-group">
            <label for="value">الهدف العام للعملاء:</label>
            <input type="number" step="0.01" class="form-control" 
                   id="value" name="value" value="{{ $target->value ?? '' }}" required>
        </div>
        
      
        
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
</div>
</div>
@endsection