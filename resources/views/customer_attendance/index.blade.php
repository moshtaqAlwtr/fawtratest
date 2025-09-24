@extends('master')

@section('title')
    سجل حضور العملاء
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">سجل حضور العملاء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="content-body">
    
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-rtl flex-wrap">
                    <div></div>
                    <div>
                       <button type="button" class="btn btn-outline-success" data-toggle="modal" data-target="#addAttendanceModal">
    <i class="fa fa-plus me-2"></i>تسجيل حضور العميل
</button>

                    </div>
                </div>
            </div>
        </div>
        <div class="card">
    <div class="card-content">
        <div class="card-body">
           <form class="form" method="GET" action="{{ route('customer_attendance.index') }}">
    <div class="form-body row">
        <div class="form-group col-md-6">
            <label for="customer_search">البحث بواسطة اسم العميل أو الرقم التعريفي</label>
            <select id="customer_search" name="client_id" class="form-control select2">
                <option value="">اختر</option>
                @foreach($Clients as $client)
                    <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                        {{ $client->trade_name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="employee_search">البحث بواسطة اسم الموظف أو الرقم التعريفي</label>
            <select id="employee_search" name="created_by" class="form-control select2">
                <option value="">اختر</option>
                @foreach(\App\Models\User::all() as $user)
                    <option value="{{ $user->id }}" {{ request('created_by') == $user->id ? 'selected' : '' }}>
                        {{ $user->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group col-md-6">
            <label for="from_date">التاريخ (من)</label>
            <input type="date" id="from_date" class="form-control" name="from_date" value="{{ request('from_date') }}">
        </div>
        <div class="form-group col-md-6">
            <label for="to_date">التاريخ (إلى)</label>
            <input type="date" id="to_date" class="form-control" name="to_date" value="{{ request('to_date') }}">
        </div>
    </div>
    <div class="form-actions mt-3">
        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
        <a href="{{ route('customer_attendance.index') }}" class="btn btn-outline-danger waves-effect waves-light">إلغاء الفلترة</a>
    </div>
</form>

        </div>
    </div>
</div>
<div class="card mt-5">
   
   
    <table class="table table-bordered table-hover">
        <thead class="table-light">
        <tr>
            <th>المعرف</th>
            <th>بيانات العميل</th>
            <th>التاريخ و التوقيت</th>
            <th>أضيفت بواسطة</th>
            <th>ترتيب بواسطة</th>
        </tr>
        </thead>
       <tbody>
    @foreach($ClientAttendances as $attendance)
        <tr>
            <td>{{ $attendance->id }}</td>
            <td>
                {{ $attendance->client ? $attendance->client->trade_name : '-' }}<br>
                {{-- إذا لديك عنوان أو تفاصيل أخرى للعميل أضفها هنا --}}
                <small class="text-muted">#{{ $attendance->client_id }}</small>
            </td>
            <td>
                {{ \Carbon\Carbon::parse($attendance->date)->format('d/m/Y') }}<br>
                <small class="text-muted">{{ \Carbon\Carbon::parse($attendance->date)->format('H:i') }}</small>
            </td>
            <td>
                {{ $attendance->creator ? $attendance->creator->name : '-' }}
            </td>
            <td>
    <div class="btn-group">
        <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#editModal{{ $attendance->id }}">
                <i class="fa fa-edit me-2 text-success"></i>تعديل
            </a>
            <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#deleteModal{{ $attendance->id }}">
                <i class="fa fa-trash me-2"></i>حذف
            </a>
        </div>
    </div>

    <!-- مودال التعديل -->
    <div class="modal fade" id="editModal{{ $attendance->id }}" tabindex="-1" role="dialog" aria-labelledby="editModalLabel{{ $attendance->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('customer_attendance.update', $attendance->id) }}">
            @csrf
            @method('PUT')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="editModalLabel{{ $attendance->id }}">تعديل الحضور</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                    <div class="form-group">
                        <label>اسم العميل</label>
                        <input type="text" class="form-control" value="{{ $attendance->client ? $attendance->client->trade_name : '-' }}" disabled>
                    </div>
                    <div class="form-group">
                        <label>تاريخ ووقت الحضور</label>
                        <input type="datetime-local" name="date" class="form-control" value="{{ \Carbon\Carbon::parse($attendance->date)->format('Y-m-d\TH:i') }}" required>
                    </div>
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
              </div>
            </div>
        </form>
      </div>
    </div>

    <!-- مودال الحذف -->
    <div class="modal fade" id="deleteModal{{ $attendance->id }}" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel{{ $attendance->id }}" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <form method="POST" action="{{ route('customer_attendance.destroy', $attendance->id) }}">
            @csrf
            @method('DELETE')
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel{{ $attendance->id }}">تأكيد الحذف</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
                  <span aria-hidden="true">&times;</span>
                </button>
              </div>
              <div class="modal-body">
                هل أنت متأكد من حذف هذا الحضور؟
              </div>
              <div class="modal-footer">
                <button type="submit" class="btn btn-danger">نعم، احذف</button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
              </div>
            </div>
        </form>
      </div>
    </div>
</td>

        </tr>
    @endforeach
</tbody>

    </table>
    <!-- Modal -->
<div class="modal fade" id="addAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="addAttendanceModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('customer_attendance.store') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="addAttendanceModalLabel">تسجيل حضور عميل</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="إغلاق">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <div class="form-group">
            <label for="client_id">اختر العميل</label>
            <select name="client_id" id="client_id" class="form-control select2" required>
                <option value="">اختر</option>
                @foreach($Clients as $client)
                    <option value="{{ $client->id }}">{{ $client->trade_name }}</option>
                @endforeach
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-success">حفظ</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
        </div>
      </div>
    </form>
  </div>
</div>

</div>
        @endsection