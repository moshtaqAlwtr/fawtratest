@extends('master')

@section('title')
    اضافة عملية الدفع
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">اضافة عملية دفع </h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <form action="{{ route('salary-advance.store-payments', $id) }}
" method="POST" enctype="multipart/form-data">
        @csrf
        <!-- عرض الأخطاء -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
                    <div>
                        <a href="" class="btn btn-outline-danger">
                            <i class="fa fa-ban"></i>الغاء
                        </a>
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="fa fa-save"></i> اضافة عملية الدفع
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-body">
                <!-- الحقول -->
                <div class="row mb-3">
                   <div class="col-md-4">
                        <label for="payment_method" class="form-label">الاقساط <span style="color: red">*</span></label>
                        <select name="installmentId" class="form-control" id="payment_method" required>
                            <option value="">اختر القسط </option>
                            @foreach ($InstallmentPayments as $InstallmentPayment)
                            <option value="{{$InstallmentPayment->id}}">{{$InstallmentPayment->amount}} - {{$InstallmentPayment->due_date}}</option>
                            @endforeach


                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="date" class="form-label">تاريخ الدفع <span style="color: red">*</span></label>
                        <input type="date" id="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                      <div class="col-md-4">
                        <label for="treasury_id" class="form-label">الخزينة المستخدمة </label>
                    
                          <input type="text"   class="form-control" placeholder="رقم المعرف"
                            value="{{$mainTreasuryAccount->name ?? ""}}" readonly>
                  
                </div>
                </div>
              
                    
                  
           
      

             
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    function getInvoiceDetails(invoiceId) {
        fetch(`/payments/invoice-details/${invoiceId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // تحديث النموذج بتفاصيل الفاتورة
                    document.getElementById('remaining_amount').textContent = data.data.remaining_amount;
                    document.getElementById('client_name').textContent = data.data.client_name;
                    document.getElementById('invoice_total').textContent = data.data.grand_total;
                    document.getElementById('total_paid').textContent = data.data.total_paid;
                } else {
                    alert('خطأ في جلب تفاصيل الفاتورة');
                }
            })
            .catch(error => console.error('Error:', error));
    }
</script>
@endsection
