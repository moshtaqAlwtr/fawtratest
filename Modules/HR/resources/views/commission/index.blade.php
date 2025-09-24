@extends('master')


@section('title')
    قواعد العمولة
@stop

@section('content')
   
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    <div class="content-body">
        <div class="container-fluid">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <!-- مربع اختيار الكل -->


                        <!-- المجموعة الأفقية: Combobox و Dropdown -->
                        <div class="d-flex align-items-center">

                        </div>

                        <!-- الجزء الخاص بالتصفح -->
                        <div class="d-flex align-items-center">
                            <!-- زر الصفحة السابقة -->
                            <button class="btn btn-outline-secondary btn-sm" aria-label="الصفحة السابقة">
                                <i class="fa fa-angle-right"></i>
                            </button>


                            <!-- زر الصفحة التالية -->
                            <button class="btn btn-outline-secondary btn-sm" aria-label="الصفحة التالية">
                                <i class="fa fa-angle-left"></i>
                            </button>
                        </div>

                        <!-- الأزرار الإضافية -->
                        <a href="{{ route('commission.create') }}" class="btn btn-success btn-sm d-flex align-items-center">
                            <i class="fa fa-plus me-2"></i> اضافة قاعدة عمولة جديدة
                        </a>

                    </div>
                </div>

            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-body">
                        <h4 class="card-title">بحث</h4>
                    </div>

                    <div class="card-body">
                        <form class="form" method="GET" action="{{ route('invoices.index') }}">
                            <div class="form-body row">
                                <!-- 1. اسم قاعدة العمولة -->
                                <div class="form-group col-md-4">
                                    <label for="client_id">اسم قاعدة العمولة</label>
                                    <select name="client_id" class="form-control" id="client_id">
                                        <option value="">أي العميل</option>
                                       
                                    </select>
                                </div>

                                <!-- 2. رقم الفاتورة -->
                                <div class="form-group col-md-4">
                                    <label for="invoice_number">الموظفين</label>
                                    <input type="text" id="invoice_number" class="form-control"
                                        placeholder="الموظفين" name="invoice_number"
                                        value="{{ request('invoice_number') }}">
                                </div>

                                <!-- 3. حالة الفاتورة -->
                                <div class="form-group col-md-4">
                                    <label for="status">الحالة</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">الحالة</option>
                                        
                                       
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="status">الفترة</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">الفترة</option>
                                        
                                       
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="status">نوع الهدف</label>
                                    <select name="status" class="form-control" id="status">
                                        <option value="">نوع الهدف</option>
                                        
                                       
                                    </select>
                                </div>
                            </div>

                         
                        </form>

                    </div>

                </div>

            </div>


            <div class="card">
              

                <!-- قائمة الفواتير -->
                <div class="table-responsive">
                    <table class="table table-hover table-bordered">
                        <thead class="bg-light">
                            <tr>
                                <th>قواعد العمولة</th>
                                <th>الفترة</th>
                                <th>الهدف</th>
                                <th>الحالة</th>
                                <th style="width: 100px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($commissions as  $commission )
                                
                         
                                <tr class="align-middle">
                                   
                                    <td>
                                        <div class="mb-1">
                                            
                                            <strong>{{$commission->name ?? ""}}</strong>
                                        </div>       
                                    </td>
                                    <td>  
                                        @if($commission->commission_calculation == "monthly")
                                        {{$commission->name ?? ""}}
                                        <strong>شهري</strong>
                                        @elseif($commission->commission_calculation == "yearly")
                                        <strong>سنوي</strong>
                                        @else
                                        <strong>ربع سنوي </strong>
                                        @endif
                                     </td>
                                    <td> 
                                       
                                        <strong>{{$commission->value ?? ""}}</strong>
                                    
                                    </td>

                                    
                                    <td> 
                                       @if ($commission->status == "active")
                                       <strong>نشط</strong>
                                       @else
                                       <strong>غير نشط </strong>
                                       @endif
                                       
                                    
                                    </td>
                                   

                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-sm bg-gradient-info fa fa-ellipsis-v" type="button"
                                                id="dropdownMenuButton" data-toggle="dropdown"
                                                aria-haspopup="true" aria-expanded="false">
                                            </button>
                                            <div class="dropdown-menu dropdown-menu-end">
                                                <a class="dropdown-item" href="{{ route('commission.edit', $commission->id) }}">
                                                    <i class="fa fa-edit me-2 text-success"></i> تعديل
                                                </a>
                                                
                                                <a class="dropdown-item"
                                                    href="">
                                                    <i class="fa fa-eye me-2 text-primary"></i>عرض
                                                </a>
                                               
                                                
                                                {{-- <form action=""
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item text-danger">
                                                        <i class="fa fa-trash me-2"></i>حذف
                                                    </button>
                                                </form> --}}
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
@endsection

@section('scripts')

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        function filterInvoices(status) {
            const currentUrl = new URL(window.location.href);
            if (status) {
                currentUrl.searchParams.set('status', status);
            } else {
                currentUrl.searchParams.delete('status');
            }
            window.location.href = currentUrl.toString();
        }

        function filterInvoices(status) {
            window.location.href = "{{ route('invoices.index') }}" + (status ? "?status=" + status : "");
        }
    </script>
@endsection

