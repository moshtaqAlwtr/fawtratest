<div class="btn-group">
    <div class="dropdown">
        <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
            type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"></button>
        <div class="dropdown-menu">
            <a class="dropdown-item" href="{{ route('clients.show', $client->id) }}">
                <i class="fa fa-eye me-2 text-primary"></i>عرض
            </a>
            @if (auth()->user()->hasPermissionTo('Edit_Client'))
                <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                    <i class="fa fa-pencil-alt me-2 text-success"></i>تعديل
                </a>
            @endif
            <a class="dropdown-item" href="{{ route('clients.send_info', $client->id) }}">
                <i class="fa fa-pencil-alt me-2 text-success"></i> إرسال بيانات الدخول
            </a>
            <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                <i class="fa fa-copy me-2 text-info"></i>نسخ
            </a>
            @if (auth()->user()->hasPermissionTo('Delete_Client'))
                <a class="dropdown-item text-danger" href="#" data-toggle="modal" data-target="#modal_DELETE{{ $client->id }}">
                    <i class="fa fa-trash-alt me-2"></i>حذف
                </a>
            @endif
            <a class="dropdown-item" href="{{ route('clients.edit', $client->id) }}">
                <i class="fa fa-file-invoice me-2 text-warning"></i>كشف حساب
            </a>
        </div>
    </div>
</div>
