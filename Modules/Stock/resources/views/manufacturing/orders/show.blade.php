@extends('master')

@section('title')
أوامر التصنيع
@stop

@section('css')
    @include('stock::manufacturing.orders.partials.styles')
@endsection

@section('content')
    {{-- Header Section --}}
    @include('stock::manufacturing.orders.partials.header', ['order' => $order])

    <div class="content-body">
        @include('layouts.alerts.error')
        @include('layouts.alerts.success')

        <div class="card">
            {{-- Action Buttons --}}
            @include('stock::manufacturing.orders.partials.action-buttons', ['order' => $order])

            <div class="card-body">
                {{-- Navigation Tabs --}}
                @include('stock::manufacturing.orders.partials.nav-tabs')

                <div class="tab-content">
                    {{-- معلومات Tab --}}
                    @include('stock::manufacturing.orders.partials.info-tab', ['order' => $order])

                    {{-- المواد المستلمة Tab --}}
                    @include('stock::manufacturing.orders.partials.received-materials-tab', ['order' => $order])

                    {{-- مرتجعات المواد Tab --}}
                    @include('stock::manufacturing.orders.partials.returned-materials-tab', ['order' => $order])

                    {{-- سجل النشاطات Tab --}}
                    @include('stock::manufacturing.orders.partials.activities-tab', ['logs' => $logs ?? []])
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @include('stock::manufacturing.orders.partials.delete-modal', ['order' => $order])
    @include('stock::manufacturing.orders.partials.finish-order-modal', ['order' => $order, 'storehouse' => $storehouse ?? []])

@endsection

@section('scripts')
    @include('stock::manufacturing.orders.partials.scripts')
@endsection