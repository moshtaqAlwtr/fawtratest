@extends('master')

@section('title')
    توجيه الحسابات
@stop

@section('css')
@section('css')
<style>
    .account-routing-container {
        max-width: 1100px;
        margin: auto;
        margin-top: 20px;
    }

    .routing-box {
        background-color: #ffffff; /* أبيض واضح */
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 1rem;
        margin-bottom: 20px;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05); /* ظل خفيف */
    }

    .routing-title {
        font-weight: bold;
        font-size: 16px;
        margin-bottom: 15px;
        color: #333;
    }

    .form-label {
        font-weight: 600;
        font-size: 14px;
    }

    .form-group {
        margin-bottom: 12px;
    }

    .form-control {
        font-size: 14px;
    }

    .action-buttons {
        display: flex;
        justify-content: flex-start;
        gap: 10px;
        margin-top: 1.5rem;
    }

    .btn-save {
        background-color: #28a745;
        color: #fff;
    }

    .btn-cancel {
        background-color: #6c757d;
        color: #fff;
    }
</style>
@endsection

@endsection

@section('content')
<div class="account-routing-container">
    <form action="#" method="POST">
        @csrf

        <div class="row">
            {{-- المبيعات --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">المبيعات</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>تعيين تلقائي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">حساب رئيسي</label>
                        <select class="form-control">
                            <option>411 - المبيعات</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- العملاء --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">العملاء</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>تعيين تلقائي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">حساب رئيسي</label>
                        <select class="form-control">
                            <option>العملاء</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- خصم مسموح به --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">خصم مسموح به</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>إلغاء الحسابات</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- المرتجعات --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">المرتجعات</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>تعيين تلقائي</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">حساب رئيسي</label>
                        <select class="form-control">
                            <option>412 - مردودات المبيعات</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- توجيه التسوية --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">توجيه التسوية</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>إلغاء الحسابات</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- حساب مبيعات المنتج --}}
            <div class="col-md-6">
                <div class="routing-box">
                    <div class="routing-title">حساب مبيعات المنتج</div>
                    <div class="form-group">
                        <label class="form-label">نوع التوجيه</label>
                        <select class="form-control">
                            <option>بلا توجيه</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        {{-- الأزرار --}}
        <div class="action-buttons">
            <button type="submit" class="btn btn-save">
                <i class="fa fa-save me-1"></i> حفظ
            </button>
            <a href="#" class="btn btn-cancel">
                <i class="fa fa-times me-1"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
