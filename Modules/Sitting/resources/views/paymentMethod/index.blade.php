@extends('master')

@section('content')
    <div class="content-body">


        <div class="card">
            <form id="paymentStatusForm" action="{{ route('update.payment.status') }}" method="POST">
                @csrf
                @method('PUT')
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                    </div>
          
                    <div> 
                        <a  href="{{ route('PaymentMethods.create') }}" class="btn btn-outline-primary">
                            <i class="fas fa-plus-circle"></i> اضافة وسيلة دفع
                        </a>

                        <button class="btn btn-outline-success">
                            <i class="fas fa-save"></i> حفظ
                        </button>

                    </div>
                </div>
            </div>
        </div>
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')
    
        <x-layout.card title="">

            <div class="row">
                <div class="col-12">
                    <h4 class="fw-bold text-dark mb-4 mt-4 pb-2 border-bottom">خيارات الدفع</h4>
                </div>
                @foreach ($payments as $payment)
                <x-payment-method-card  
                    title="{{ $payment->name }}" 
                    icon="fas fa-wallet text-success" 
                    index="{{ $payment->id }}"  
                    :checked="$payment->status === 'active'"
                />
            @endforeach
            
    
            
                <!-- وسائل الدفع الإلكترونية -->
                <div class="col-12">
                    <h4 class="fw-bold text-dark mb-4 mt-4 pb-2 border-bottom">وسائل الدفع الإلكترونية</h4>
                </div>
                 @foreach ($payments_electronics as $payments_electronic )
                     
                 <x-payment-method-card  
                 title="{{ $payments_electronic->name }}" 
                
                 icon="fas fa-credit-card text-primary" 
                 link="https://paymob.com"
                 index="{{ $payments_electronic->id }}"  
                 :checked="$payments_electronic->status === 'active'"
             />
                
                    @endforeach
            </div>
        </x-layout.card>
    </div>
@endsection

@section('offcanvas')
<!-- نافذة إضافة وسيلة دفع -->


@endsection

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // تحديث الحقل المخفي عند تغيير حالة السويتش
        document.querySelectorAll('.status-switch').forEach(function(switchElement) {
            switchElement.addEventListener('change', function() {
                const paymentId = this.dataset.paymentId;
                const hiddenInput = document.querySelector(`input[name="payments[${paymentId}][status]"]`);
                hiddenInput.value = this.checked ? 'active' : 'inactive';
            });
        });
    });
</script>
@section('scripts')
<script>
    $('#exampleModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) // Button that triggered the modal
        var recipient = button.data('whatever') // Extract info from data-* attributes
        // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
        // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
        var modal = $(this)
        modal.find('.modal-title').text('New message to ' + recipient)
        modal.find('.modal-body input').val(recipient)
      })
      </script>
@endsection
