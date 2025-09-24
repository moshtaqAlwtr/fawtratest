



@extends('master')

@section('title')
      اضافة الهدف الشهري العام
@stop

@section('content')
<div class="card">
<div class="container">
    <h2>الهدف العام</h2>
    
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('target.update') }}">
        @csrf
        <div class="form-group">
            <label for="value">  الهدف الشهري العام:</label>
            <input type="number" step="0.01" class="form-control" 
                   id="value" name="value" value="{{ $target->value ?? '' }}" required>
        </div>
        
      
        
        <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
    </form>
</div>
</div>
@endsection