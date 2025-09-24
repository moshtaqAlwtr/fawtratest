@extends('master')

@section('title')
    عمولات المبيعات
@stop

@section('content')
    <div class="content-body" style="font-size: 1.1rem;">
        <div class="content-header row">
            
        </div>

        <div class="content-header row">
            <div class="content-header-left col-md-9 col-12 mb-2">
                <div class="row breadcrumbs-top">
                    <div class="col-12">
                        <h2 class="content-header-title float-left mb-0"> عمولات المبيعات</h2>
                        <div class="breadcrumb-wrapper col-12">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                                <li class="breadcrumb-item active">عرض</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">


                    <div class="d-flex align-items-center gap-3">
                      <div class="d-flex align-items-center gap-3">
    <div class="btn-group">
        <a href="{{ $SalesCommissions->previousPageUrl() }}" class="btn btn-light border {{ $SalesCommissions->onFirstPage() ? 'disabled' : '' }}">
            <i class="fa fa-chevron-right"></i>
        </a>
        <a href="{{ $SalesCommissions->nextPageUrl() }}" class="btn btn-light border {{ $SalesCommissions->hasMorePages() ? '' : 'disabled' }}">
            <i class="fa fa-chevron-left"></i>
        </a>
    </div>
    <span class="mx-2">
        {{ $SalesCommissions->firstItem() ?? 0 }} -
        {{ $SalesCommissions->lastItem() ?? 0 }}
        من {{ $SalesCommissions->total() }}
    </span>
    <div class="input-group" style="width: 150px">
        <input type="text" class="form-control text-center"
               value="صفحة {{ $SalesCommissions->currentPage() }} من {{ $SalesCommissions->lastPage() }}"
               readonly>
    </div>
</div>
 
                    </div>

                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <h4 class="card-title">بحث</h4>
                </div>

                <div class="card-body">
                  <form method="GET" action="{{ route('SalesCommission.index') }}" class="form">
    <div class="form-body row">
        <div class="form-group col-md-3">
            <label for="feedback1" class=""> البحث بواسطة الموظف </label>
            <input type="text" id="feedback1" class="form-control"
                placeholder="البحث بواسطة الموظف" name="name" value="{{ request('name') }}">
        </div>

        <div class="form-group col-md-3">
            <label for="feedback2" class=""> قواعد العمولة </label>
            <select id="feedback2" class="form-control" name="commission_rule_id">
                <option value="">قواعد العمولة </option>
                {{-- ضع هنا قائمة قواعد العمولة --}}
                @foreach(\App\Models\Commission::all() as $commission)
                    <option value="{{ $commission->id }}" {{ request('commission_rule_id') == $commission->id ? 'selected' : '' }}>
                        {{ $commission->name }}
                    </option>
                @endforeach
            </select>
        </div>

       
    
    
        <div class="form-group col-md-3">
            <label> تايخ العملية التجارية (من )</label>
            <input type="date" class="form-control" name="date_from" value="{{ request('date_from') }}">
        </div>
        <div class="form-group col-md-3">
            <label> تايخ العملية التجارية (الى )</label>
            <input type="date" class="form-control" name="date_to" value="{{ request('date_to') }}">
        </div>
    </div>
    <div class="form-actions">
        <button type="submit" class="btn btn-primary mr-1 waves-effect waves-light">بحث</button>
        <a href="{{ route('SalesCommission.index') }}" class="btn btn-outline-warning waves-effect waves-light">الغاء الفلتر</a>
    </div>
</form>


                </div>

            </div>

        </div>


        <div class="row" id="table-hover-row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table" style="font-size: 1.1rem;">
                                <thead class="">
                                    <tr>

                                        <th>المعرف</th>
                                        <th>موظف</th>
                                        <th>العملية</th>
                                        <th>مبلغ المبيعات</th>
                                        <th>كمية المبيعات</th>
                                        <th>عمولة</th>
                                        <th>ترتيب بواسطة</th>
                                        <th style="width: 50px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($SalesCommissions as $SalesCommission)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="form-check mb-0">
                                                    <input class="form-check-input" type="checkbox" style="width: 15px; height: 15px;">
                                                </div>
                                                <span style="margin-top: 20px">{{ $loop->iteration }}</span> <!-- الترقيم التسلسلي -->
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="avatar avatar-sm bg-secondary">
                                                    <span class="avatar-content"></span>
                                                </div>
                                                {{$SalesCommission->employee->name ?? ""}}
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>فاتورة #{{$SalesCommission->invoice_number ?? ""}}</span>
                                                <small class="text-muted">{{ $SalesCommission->created_at ? $SalesCommission->created_at->format('Y-m-d') : '' }}</small>
                                            </div>
                                        </td>
                                        <td>{{ $SalesCommission->sales_amount ? number_format($SalesCommission->sales_amount * 1.15, 2) : '' }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{$SalesCommission->sales_quantity ?? ""}}</span>
                                                <small class="text-muted">عناصر</small>
                                            </div>
                                        </td>
                                        <td>{{ $SalesCommission->sales_amount && $SalesCommission->ratio ? number_format((($SalesCommission->sales_amount * 1.15) * $SalesCommission->ratio) / 100, 2) : '' }}</td>
                                        <td>
                                            <div class="btn-group">
                                                <div class="dropdown">
                                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                                        type="button" id="dropdownMenuButton303" data-toggle="dropdown"
                                                        aria-haspopup="true" aria-expanded="false">
                                                    </button>
                                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('SalesCommission.show', $SalesCommission->id) }}">
                                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                            </a>
                                                        </li>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
