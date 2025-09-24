@extends('master')

@section('title', 'رابط غير صحيح')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 4rem;"></i>
                    </div>

                    <h3 class="card-title text-danger mb-3">رابط غير صحيح</h3>

                    <p class="card-text text-muted mb-4">
                        {{ $message ?? 'رابط الدعوة غير صحيح أو لا يمكن العثور عليه.' }}
                    </p>

                    <div class="alert alert-info" role="alert">
                        <i class="fas fa-info-circle me-2"></i>
                        تأكد من نسخ الرابط كاملاً من البريد الإلكتروني، أو تواصل مع الشخص الذي أرسل لك الدعوة.
                    </div>

                    <a href="{{ url('/') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-home me-2"></i>
                        العودة للصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
