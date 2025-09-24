@extends('master')

@section('title')
    إعدادات الحسابات العامة
@stop

@section('css')
<style>
    .settings-box {
        max-width: 800px;
        margin: 30px auto;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 20px;
    }

    .settings-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .setting-group {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
    }

    .setting-item {
        flex: 1 1 calc(50% - 10px);
        background-color: #f8f9fa;
        padding: 15px;
        border-radius: 6px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-weight: bold;
        font-size: 14px;
    }

    .form-switch {
        display: flex;
        align-items: center;
    }

    .form-switch input {
        width: 45px;
        height: 22px;
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

@section('content')
<div class="settings-box shadow-sm">
    {{-- رأس الصفحة --}}
    <div class="settings-header">
        <h5 class="mb-0">الإعدادات العامة</h5>
        <div>
            <button type="submit" class="btn btn-save me-2">
                <i class="fa fa-save"></i> حفظ
            </button>
            <a href="#" class="btn btn-cancel">
                <i class="fa fa-times"></i> إلغاء
            </a>
        </div>
    </div>

    {{-- إعدادات --}}
    <form action="#" method="POST">
        @csrf
        <div class="setting-group">
            <div class="setting-item">
                عرض مركز التكلفة في القيود اليومية
                <label class="form-switch">
                    <input type="checkbox" name="show_cost_center" checked>
                </label>
            </div>

            <div class="setting-item">
                عرض الضريبة في القيود اليومية
                <label class="form-switch">
                    <input type="checkbox" name="show_tax" checked>
                </label>
            </div>

            <div class="setting-item">
                تحديث أسعار العملات في القيود اليومية
                <label class="form-switch">
                    <input type="checkbox" name="update_exchange_rate">
                </label>
            </div>

            <div class="setting-item">
                تعيين وسوم إلى حركات القيود
                <label class="form-switch">
                    <input type="checkbox" name="assign_tags">
                </label>
            </div>
        </div>
    </form>
</div>
@endsection
