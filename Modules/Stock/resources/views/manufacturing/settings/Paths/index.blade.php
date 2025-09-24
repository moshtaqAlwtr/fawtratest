@extends('master')

@section('title')
مسارات الأنتاج
@stop

@section('content')

    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">مسارات الأنتاج</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a></li>
                            <li class="breadcrumb-item active">عرض</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('layouts.alerts.success')
    @include('layouts.alerts.error')

    <div class="card">
        <div class="card-content">
            <div class="card-body">
                <div class="card-title">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>بحث</div>
                        <div>
                            <a href="{{ route('manufacturing.paths.create') }}" class="btn btn-outline-success waves-effect waves-light">
                                <i class="fa fa-plus me-2"></i>أضف مسار الإنتاج
                            </a>
                        </div>
                    </div>
                </div>

                <form id="filterForm">
                    <div class="row mb-3 mt-3">
                        <div class="col">
                            <input type="text" name="search" id="searchInput" class="form-control" placeholder="البحث بواسطة الاسم أو الكود">
                        </div>
                        <div class="col">
                            <select class="form-control select2" name="production_stage_id" id="productionStageSelect">
                                <option value="">اسم المرحلة الإنتاجية</option>
                                @foreach ($production_stages as $production_stage)
                                    <option value="{{ $production_stage->id }}">
                                        {{ $production_stage->stage_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-auto">
                            <button type="button" id="resetFilter" class="btn btn-secondary waves-effect waves-light">إعادة تعيين</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div id="loadingSpinner" class="text-center d-none">
        <div class="spinner-border text-primary" role="status">
            <span class="sr-only">جاري التحميل...</span>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body" id="pathsTableContainer">
            <!-- Table content will be loaded here -->
        </div>
    </div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let searchTimeout;

    // Initial load
    loadPaths();

    // Search input with debounce
    $('#searchInput').on('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            loadPaths();
        }, 500); // Wait 500ms after user stops typing
    });

    // Production stage select change
    $('#productionStageSelect').on('change', function() {
        loadPaths();
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#filterForm')[0].reset();
        $('#productionStageSelect').val('').trigger('change'); // Reset select2
        loadPaths();
    });

    function loadPaths() {
        showLoading();

        let formData = {
            search: $('#searchInput').val(),
            production_stage_id: $('#productionStageSelect').val(),
            _token: $('meta[name="csrf-token"]').attr('content')
        };

        $.ajax({
            url: '{{ route("manufacturing.paths.ajax") }}',
            type: 'POST',
            data: formData,
            success: function(response) {
                $('#pathsTableContainer').html(response);
                hideLoading();
            },
            error: function(xhr, status, error) {
                hideLoading();
                console.error('Error:', error);
                $('#pathsTableContainer').html(
                    '<div class="alert alert-danger text-center" role="alert">' +
                    '<p class="mb-0">حدث خطأ أثناء تحميل البيانات. يرجى المحاولة مرة أخرى.</p>' +
                    '</div>'
                );
            }
        });
    }

    function showLoading() {
        $('#loadingSpinner').removeClass('d-none');
        $('#pathsTableContainer').addClass('opacity-50');
    }

    function hideLoading() {
        $('#loadingSpinner').addClass('d-none');
        $('#pathsTableContainer').removeClass('opacity-50');
    }

    // Handle delete modal after Ajax load
    $(document).on('click', '[data-toggle="modal"]', function() {
        let target = $(this).attr('data-target');
        $(target).modal('show');
    });
});
</script>
@endpush
