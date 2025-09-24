@extends('master')

@section('title')
    إعدادات الفاتورة الإلكترونية العامة
@stop

@section('content')
<style>
    .card-general-setting {
        border-radius: 10px;
        border: 2px solid #e0e6ef;
        background: #fff;
        padding: 0;
        margin-top: 18px;
    }
    .card-header-setting {
        background: #f7faff;
        border-bottom: 1px solid #e0e6ef;
        border-radius: 10px 10px 0 0;
        font-size: 1.14rem;
        font-weight: bold;
        color: #26588c;
        padding: 18px 28px 12px 20px;
    }
    .setting-checkbox-row {
        padding: 24px 34px;
        font-size: 1.07rem;
    }
    .form-check-label {
        font-weight: 500;
        margin-right: 8px;
        color: #313a4c;
    }
    .form-check-input {
        margin-top: 4px;
        width: 20px;
        height: 20px;
        accent-color: #1976d2;
    }
    /* أزرار الحفظ والإلغاء */
    .toolbar-save {
        margin-bottom: 12px;
        margin-top: 5px;
    }
    .toolbar-save .btn {
        min-width: 90px;
        font-weight: bold;
        margin-left: 8px;
        border-radius: 6px;
    }
    .btn-save {
        background-color: #1976d2 !important;
        color: #fff !important;
    }
    .btn-save:hover {
        background-color: #1251a6 !important;
        color: #fff !important;
    }
</style>

<div class="container py-3">
    <form method="POST" action="">
        @csrf

        <!-- أزرار الحفظ في الأعلى -->
        <div class="toolbar-save d-flex justify-content-start">
            <button type="submit" class="btn btn-save">
                <i class="fa fa-save"></i> حفظ
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fa fa-times"></i> إلغاء
            </a>
        </div>

        <!-- كارد الإعدادات -->
        <div class="card card-general-setting">
            <div class="card-header-setting">
                إعدادات الفاتورة الإلكترونية العامة
            </div>
            <div class="setting-checkbox-row d-flex align-items-center">
                <div class="form-check mb-0">
                    <input class="form-check-input"
                        type="checkbox"
                        name="auto_send_after_create"
                        id="auto_send_after_create"
                        value="1"
                        {{ isset($settings['auto_send_after_create']) && $settings['auto_send_after_create'] == '1' ? 'checked' : '' }}>
                    <label class="form-check-label" for="auto_send_after_create">
                        إرسال الفواتير تلقائيًا بعد الإنشاء
                    </label>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
