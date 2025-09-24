@extends('client')

@section('title')
لوحة التحكم
@stop

@section('css')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<style>
    .profile-img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #007bff;
    }
    .card-stats {
        text-align: center;
        padding: 15px;
        border-radius: 10px;
        background: #f8f9fa;
    }
</style>
@endsection

@section('content')


<div class="container mt-4">
    <div class="card p-4 shadow-sm">
        <div class="text-center">
            <img src="https://via.placeholder.com/100" alt="" class="profile-img">
            <h4 class="mt-2">{{ auth()->user()->name }}</h4>
            <p class="text-muted">📞 {{ auth()->user()->phone }}</p>
        </div>
        <div class="row text-center mt-3">
            <div class="col-md-4">
                <div class="card-stats">
                    <h5>عدد الفواتير</h5>
                    <p class="text-primary fs-4">{{ $invoices_count ?? 0 }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stats">
                    <h5>المبالغ المطلوب دفعها</h5>
                    <p class="text-success fs-4">{{ $invoices_due_value ?? 0 }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card-stats">
                    <h5>عدد المواعيد</h5>
                    <p class="text-danger fs-4">{{ $total_balance ?? 0 }} </p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">الفواتير</div>
                <div class="card-body">
                    <ul class="list-group">
                        @foreach ($invoices as $invoice)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>فاتورة #{{ $invoice->code ?? 0 }}</span>
                                <span class="text-success">{{ $invoice->grand_total ?? 0 }} ريال</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">الدفعات</div>
                <div class="card-body">
                    <ul class="list-group">
                       
                            <li class="list-group-item d-flex justify-content-between">
                                <span>دفعة #</span>
                                <span class="text-primary"> ريال</span>
                            </li>
                      
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection


@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

@endsection
