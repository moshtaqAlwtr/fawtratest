@extends('master')

@section('title')
      اضافة الهدف الشهري للموظفين
@stop

@section('content')
<div class="card">
    <div class="container p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="mb-0">تحديد الهدف الشهري للموظفين</h4>
            <button type="submit" form="targetsForm" class="btn btn-primary">
                <i class="fas fa-save"></i> حفظ
            </button>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('employee_targets.store') }}" id="targetsForm">
            @csrf
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="40%">الموظف</th>
                            <th width="60%">الهدف الشهري (ريال)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($employees as $employee)
                            <tr>
                                <td>{{ $employee->name }}</td>
                                <td>
                                    <input type="hidden" name="targets[{{ $loop->index }}][user_id]" value="{{ $employee->id }}">
                                    <div class="input-group">
                                        <input type="number" step="0.01" name="targets[{{ $loop->index }}][monthly_target]"
                                            value="{{ optional($employee->target)->monthly_target }}"
                                            class="form-control" placeholder="أدخل الهدف">
                                        <span class="input-group-text">ر.س</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>
@endsection