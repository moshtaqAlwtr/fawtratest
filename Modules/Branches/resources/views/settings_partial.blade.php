
@extends('master')

@section('title')
أعدادات الفروع
@stop

@section('content')

<div id="settings-container">
    @if (isset($settings))
<div class="col-md-6 mb-3">
    <div class="vs-checkbox-con vs-checkbox-primary">
        <input type="checkbox" name="share_cost_center" value="1" 
        {{ isset($settings['share_cost_center']) && $settings['share_cost_center'] ? 'checked' : '' }}>
        <span class="vs-checkbox">
            <span class="vs-checkbox--check">
                <i class="vs-icon feather icon-check"></i>
            </span>
        </span>
        <span class="">مشاركة مركز التكلفة بين الفروع</span>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="vs-checkbox-con vs-checkbox-primary">
        <input type="checkbox" name="share_cost_center" value="1" 
        {{ isset($settings['share_cost_center']) && $settings['share_cost_center'] ? 'checked' : '' }}>
        <span class="vs-checkbox">
            <span class="vs-checkbox--check">
                <i class="vs-icon feather icon-check"></i>
            </span>
        </span>
        <span class="">مشاركة العملاء بين الفروع</span>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="vs-checkbox-con vs-checkbox-primary">
        <input type="checkbox" name="share_products" value="1" 
            {{ isset($settings) && $settings->share_products ? 'checked' : '' }}>
        <span class="vs-checkbox">
            <span class="vs-checkbox--check">
                <i class="vs-icon feather icon-check"></i>
            </span>
        </span>
        <span class="">مشاركة المنتجات بين الفروع</span>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="vs-checkbox-con vs-checkbox-primary">
        <input type="checkbox" name="share_suppliers" value="1" 
            {{ isset($settings) && $settings->share_suppliers ? 'checked' : '' }}>
        <span class="vs-checkbox">
            <span class="vs-checkbox--check">
                <i class="vs-icon feather icon-check"></i>
            </span>
        </span>
        <span class="">مشاركة الموردين بين الفروع</span>
    </div>
</div>
<div class="col-md-6 mb-3">
    <div class="vs-checkbox-con vs-checkbox-primary">
        <input type="checkbox" name="account_tree" value="1" 
            {{ isset($settings) && $settings->account_tree ? 'checked' : '' }}>
        <span class="vs-checkbox">
            <span class="vs-checkbox--check">
                <i class="vs-icon feather icon-check"></i>
            </span>
        </span>
        <span class="">تخصيص الحسابات في شجرة الحسابات لكل فرع</span>
    </div>
</div>
@endif
</div>
@endsection