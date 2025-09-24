@extends('master')

@section('title', 'انتهت صلاحية الدعوة')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg border-0 text-center">
                <div class="card-body p-5">
                    <div class="mb-4">
                        <i class="fas fa-clock text-warning" style="font-size: 4rem;"></i>
                    </div>

                    <h3 class="card-title text-warning mb-3">انتهت صلاحية الدعوة</h3>

                    <p class="card-text text-muted mb-4">
                        نعتذر، لقد انتهت صلاحية دعوة المشروع.
                        الدعوات صالحة لمدة 7 أيام فقط من تاريخ الإرسال.
                    </p>

                    <div class="alert alert-warning" role="alert">
                        <i class="fas fa-lightbulb me-2"></i>
                        <strong>لا تقلق!</strong> يمكنك التواصل مع الشخص الذي أرسل لك الدعوة لطلب إرسال دعوة جديدة.
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