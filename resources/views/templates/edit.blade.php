@extends('master')

@section('title')
  تصاميم الفواتير وعروض الأسعار
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h4>تعديل قالب: {{ $template->name }}</h4>
                    <a href="{{ route('templates.reset', $template) }}" class="btn btn-sm btn-outline-secondary">
                        استعادة الافتراضي
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('templates.update', $template) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>اسم القالب</label>
                            <input type="text" name="name" value="{{ $template->name }}" class="form-control">
                        </div>

                        <div class="form-group mt-3">
                            <label for="templateContent">محتوى القالب</label>
                            <textarea name="content" id="templateContent" class="form-control" rows="10">{!! $template->content !!}</textarea>
                        </div>

                        <div class="mt-3">
                            <button type="submit" class="btn btn-success">💾 حفظ التغييرات</button>
                            <button type="button" id="previewBtn" class="btn btn-primary">👁️ معاينة</button>
                        </div>
                    </form>

                    <hr>

                    <h5>المعاينة:</h5>
                    <div id="previewArea" class="border p-3" style="background-color: #f9f9f9;"></div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4>المتغيرات المتاحة</h4>
                </div>
                <div class="card-body">
                    <ul class="list-group">
                        <li class="list-group-item">
                            <code>{ $invoice->id }</code> - رقم الفاتورة
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->trade_name }</code> - اسم العميل (الاسم التجاري)
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->first_name . ' ' . $invoice->client->last_name }</code> - اسم العميل (كامل)
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->street1 }</code> - عنوان العميل
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->code }</code> - كود العميل
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->tax_number }</code> - الرقم الضريبي
                        </li>
                        <li class="list-group-item">
                            <code>{ $invoice->client->phone }</code> - هاتف العميل
                        </li>
                        <li class="list-group-item">
                            <code>{ str_pad($invoice->id, 5, '0', STR_PAD_LEFT) }</code> - رقم الفاتورة (مُنسق)
                        </li>
                        <li class="list-group-item">
                            <code>{ \Carbon\Carbon::parse($invoice->invoice_date)->format('d/m/Y H:i') }</code> - تاريخ الفاتورة
                        </li>
                        <li class="list-group-item">
                            <code>{ $item->item }</code> - اسم المنتج (في الحلقة)
                        </li>
                        <li class="list-group-item">
                            <code>{ $item->quantity }</code> - الكمية (في الحلقة)
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($item->unit_price, 2) }</code> - سعر الوحدة (في الحلقة)
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($item->total, 2) }</code> - المجموع (في الحلقة)
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($invoice->grand_total, 2) }</code> - المجموع الكلي
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($invoice->total_discount, 2) }</code> - قيمة الخصم
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($invoice->shipping_cost, 2) }</code> - تكلفة الشحن
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($invoice->advance_payment, 2) }</code> - الدفعة المقدمة
                        </li>
                        <li class="list-group-item">
                            <code>{ number_format($invoice->due_value, 2) {</code> - المبلغ المستحق
                        </li>
                        <li class="list-group-item">
                            <code>{ $qrCodeSvg }</code> - رمز QR
                        </li>
                        <!-- المتغيرات المشروطة -->
                        <li class="list-group-item list-group-item-info">
                            <strong>المتغيرات المشروطة:</strong>
                        </li>
                        <li class="list-group-item">
                            <code>if($invoice->client->phone)...endif</code> - عرض هاتف العميل إذا موجود
                        </li>
                        <li class="list-group-item">
                            <code>if($invoice->total_discount > 0)...endif</code> - عرض الخصم إذا كان أكبر من الصفر
                        </li>
                        <li class="list-group-item">
                            <code>if($invoice->shipping_cost > 0)...endif</code> - عرض تكلفة الشحن إذا كانت أكبر من الصفر
                        </li>
                        <li class="list-group-item">
                            <code>if($invoice->advance_payment > 0)...endif</code> - عرض الدفعة المقدمة إذا كانت أكبر من الصفر
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.tiny.cloud/1/58lh78ur0azb8wa2ediw9s0mby3caposfnr4sp9il5j3z6ka/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
<script>
    // تفعيل TinyMCE
    tinymce.init({
        selector: '#templateContent',
        height: 400,
        directionality: 'rtl',
        plugins: 'code table lists',
        toolbar: 'undo redo | bold italic | alignleft aligncenter alignright | bullist numlist | code',
        language: 'ar'
    });

    // زر المعاينة
    document.getElementById('previewBtn').addEventListener('click', function() {
    const content = tinymce.get('templateContent').getContent();
    const previewArea = document.getElementById('previewArea');
    
    previewArea.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin"></i> جارِ المعاينة...</div>';
    
    fetch('{{ route("template.preview") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ 
            content: content,
            _token: '{{ csrf_token() }}'
        })
    })
    .then(async response => {
        const data = await response.json();
        
        if (!response.ok) {
            throw new Error(data.error || 'Request failed');
        }
        
        if (data.html) {
            previewArea.innerHTML = data.html;
            
            // معالجة أي scripts في المحتوى المعاين
            this.reloadScripts(previewArea);
        } else {
            throw new Error(data.error || 'No HTML content received');
        }
    })
    .catch(error => {
        console.error('Preview error:', error);
        this.showPreviewError(previewArea, error);
    });
});

function reloadScripts(container) {
    container.querySelectorAll('script').forEach(script => {
        const newScript = document.createElement('script');
        Array.from(script.attributes).forEach(attr => {
            newScript.setAttribute(attr.name, attr.value);
        });
        newScript.text = script.text;
        script.parentNode.replaceChild(newScript, script);
    });
}

function showPreviewError(container, error) {
    container.innerHTML = `
        <div class="alert alert-danger">
            <h5><i class="fas fa-exclamation-triangle"></i> خطأ في المعاينة</h5>
            <div class="error-details">
                <p>${error.message}</p>
                <button class="btn btn-sm btn-warning mt-2" onclick="retryPreview()">
                    <i class="fas fa-sync-alt"></i> إعادة المحاولة
                </button>
            </div>
        </div>
    `;
}

function retryPreview() {
    document.getElementById('previewBtn').click();
}
</script>
@endsection
