@extends('master')
@section('title', 'إدارة مصادر الطلب')
@section('content')
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    .order-table th, .order-table td { text-align: center; vertical-align: middle; }
    .order-table tbody tr { cursor: move; transition: background .18s; }
    .order-table .active-radio { margin-top: 2px; }
    .order-table .delete-row { color: #e23636; cursor: pointer; font-size: 1.2rem; }
    .add-row { color: #1976d2; cursor: pointer; }
    .table-wrapper { background: #fff; border-radius: 12px; box-shadow: 0 1px 6px #eee; padding: 16px 8px 10px 8px; }
    .custom-switch { position: relative; display: inline-block; width: 46px; height: 25px; }
    .custom-switch input { display:none; }
    .slider { position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: #ddd; transition: .4s; border-radius: 16px;}
    .slider:before { position: absolute; content: ""; height: 20px; width: 20px; left: 3px; bottom: 2.5px; background-color: white; transition: .4s; border-radius: 50%; }
    input:checked + .slider { background-color: #1976d2; }
    input:checked + .slider:before { transform: translateX(20px);}
</style>

<div class="container py-3">
    <form method="POST" action="{{ route('order_sources.storeOrUpdate') }}" id="orderSourcesForm">
        @csrf

        <div class="d-flex mb-3">
            <button type="submit" class="btn btn-success me-2">
                <i class="fa fa-save"></i> حفظ
            </button>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="fa fa-times"></i> إلغاء
            </a>
        </div>

        <div class="table-wrapper">
            <h5 class="mb-3 text-primary" style="font-weight:bold;">مصادر الطلب</h5>
            <table class="table order-table">
                <thead>
                    <tr>
                        <th style="width:40px;"></th>
                        <th>الاسم <span class="text-danger">*</span></th>
                        <th>الحالة</th>
                        <th style="width:60px;">إجراء</th>
                    </tr>
                </thead>
                <tbody id="orderSourcesBody">
                    @foreach($sources as $i => $src)
                        <tr data-id="{{ $src->id }}">
                            <td class="drag-handle"><i class="fa fa-bars text-secondary"></i></td>
                            <td>
                                <input type="hidden" name="ids[]" value="{{ $src->id }}">
                                <input type="text" name="name[]" value="{{ $src->name }}" class="form-control" required>
                                <input type="hidden" name="sort_order[]" value="{{ $src->sort_order }}">
                            </td>
                            <td class="active-radio">
                                <label class="me-2">
                                    <input type="radio" name="active[{{ $i }}]" value="1" {{ $src->active ? 'checked' : '' }}> نشط
                                </label>
                                <label>
                                    <input type="radio" name="active[{{ $i }}]" value="0" {{ !$src->active ? 'checked' : '' }}> غير نشط
                                </label>
                            </td>
                            <td>
                                <span class="delete-row" onclick="deleteRow({{ $src->id }}, this)" title="حذف"><i class="fa fa-trash"></i></span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <button type="button" class="btn btn-outline-primary add-row mt-2" onclick="addRow()">
                <i class="fa fa-plus"></i> إضافة
            </button>

            <div class="row mt-4 align-items-center">
                <!-- مصدر الطلب الافتراضي -->
                <div class="col-md-5">
                    <label><b>مصدر الطلب الافتراضي</b></label>
                    <select name="default_order_source_id" class="form-control">
                        <option value="">اختر المصدر</option>
                        @foreach($sources as $src)
                            <option value="{{ $src->id }}" {{ (isset($default_id) && $default_id == $src->id) ? 'selected' : '' }}>
                                {{ $src->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- الزامي -->
                <div class="col-md-4 mt-3 mt-md-0">
                    <label class="me-2"><b>إلزامي</b></label>
                    <label class="custom-switch">
                        <input type="checkbox" name="order_source_mandatory" value="1" {{ isset($is_mandatory) && $is_mandatory == '1' ? 'checked' : '' }}>
                        <span class="slider"></span>
                    </label>
                </div>
            </div>

        </div>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function(){
    $("#orderSourcesBody").sortable({
        handle: ".drag-handle",
        update: function( event, ui ) {
            let order = [];
            $("#orderSourcesBody tr").each(function(){
                order.push($(this).attr('data-id'));
            });
            // يمكن إرسال الترتيب فورًا أو حفظه بعد الضغط على حفظ النموذج
            // $.post("{{ route('order_sources.sort') }}", {order: order, _token: "{{ csrf_token() }}"});
        }
    }).disableSelection();
});

function addRow() {
    let count = $('#orderSourcesBody tr').length;
    let row = `<tr>
        <td class="drag-handle"><i class="fa fa-bars text-secondary"></i></td>
        <td>
            <input type="hidden" name="ids[]" value="">
            <input type="text" name="name[]" class="form-control" required>
            <input type="hidden" name="sort_order[]" value="${count}">
        </td>
        <td class="active-radio">
            <label class="me-2">
                <input type="radio" name="active[${count}]" value="1" checked> نشط
            </label>
            <label>
                <input type="radio" name="active[${count}]" value="0"> غير نشط
            </label>
        </td>
        <td>
            <span class="delete-row" onclick="this.closest('tr').remove()" title="حذف"><i class="fa fa-trash"></i></span>
        </td>
    </tr>`;
    $('#orderSourcesBody').append(row);
}

function deleteRow(id, el) {
    if (confirm('هل أنت متأكد من الحذف؟')) {
        fetch('{{ url("/order-sources") }}/' + id, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': '{{ csrf_token() }}'}
        }).then(resp => resp.json()).then(data => {
            if (data.status === 'deleted') {
                el.closest('tr').remove();
            }
        });
    }
}
</script>
@endsection
