@extends('master')

@section('title')
    إدارة العملاء
@stop

@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/indexclient.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/map-styles.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/hidden-clients.css') }}">
@stop

@section('content')
    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">إدارة العملاء</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <!-- بطاقة الإجراءات -->
        @include('client::partials.action_card')

        <!-- حاوي الخريطة -->
        @include('client::partials.map_container')

        <!-- كونتينر العملاء المخفيين -->
        @include('client::partials.hidden_clients_container')

        <!-- بطاقة البحث -->
        @include('client::partials.search_card')

        <!-- جدول العملاء -->
        <div class="card">
            <div class="card-body position-relative">
                <!-- Loading Overlay -->
                <div id="loadingOverlay" class="loading-overlay" style="display: none;">
                    <div class="text-center">
                        <div class="dot-pulse mb-3"></div>
                        <h5 class="text-primary mb-2">جارٍ تحديث البيانات...</h5>
                        <p class="text-muted small">سيتم عرض النتائج خلال لحظات</p>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
                    <!-- شريط الترقيم -->
                    @include('client::partials.pagination_controls')

                    <!-- اختيار عدد العملاء -->
                    @include('client::partials.per_page_selector')
                </div>

                <div id="clientsContainer">
                    @include('client::partials.client_cards')
                </div>
            </div>
        </div>

        <!-- Modal إضافة حد ائتماني -->
        @include('client::partials.credit_limit_modal')
    </div>

    <!-- Scripts Configuration -->
    <script>
        // تعريف الراوتات في الجافاسكريبت
        window.clientRoutes = {
            index: "{{ route('clients.index') }}",
            show: "{{ route('clients.show', '') }}/",
            hideFromMap: "{{ route('clients.hideFromMap', ':id') }}",
            showInMap: "{{ route('clients.showInMap', ':id') }}",
            getHiddenClients: "{{ route('clients.getHiddenClients') }}",
            getMapData: "{{ route('clients.getMapData') }}"
        };

        // CSRF Token
        window.csrfToken = "{{ csrf_token() }}";

        // Google Maps API Key
        window.googleMapsApiKey = "{{ env('GOOGLE_MAPS_API_KEY') }}";
    </script>
@endsection

@section('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/@googlemaps/markerclusterer/dist/index.min.js"></script>

    <!-- Core App Scripts -->
    <script src="{{ asset('assets/js/app-core.js') }}"></script>
    <script src="{{ asset('assets/js/search-manager.js') }}"></script>
    <script src="{{ asset('assets/js/pagination-manager.js') }}"></script>
    <script src="{{ asset('assets/js/map-manager.js') }}"></script>
    <script src="{{ asset('assets/js/hidden-clients-manager.js') }}"></script>
    <script src="{{ asset('assets/js/app-init.js') }}"></script>
@endsection
