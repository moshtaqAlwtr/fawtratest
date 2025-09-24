<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">
                    أوامر التصنيع
                    @if($order->status === 'closed')
                        <span class="status-badge status-closed">
                            <i class="fa fa-lock"></i>
                            مغلق
                        </span>
                    @elseif($order->status === 'completed')
                        <span class="status-badge status-completed">
                            <i class="fa fa-check-circle"></i>
                            مكتمل
                        </span>
                    @elseif($order->status == 'in_progress')
                        <span class="status-badge status-in-progress">
                            <i class="fa fa-clock"></i>
                            قيد التنفيذ
                        </span>
                    @else
                        <span class="status-badge status-active">
                            <i class="fa fa-play"></i>
                            نشط
                        </span>
                    @endif
                </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                        <li class="breadcrumb-item active">{{ $order->name }} | <small class="text-muted">#{{ $order->code }}</small></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>