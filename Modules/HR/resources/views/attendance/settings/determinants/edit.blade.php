@extends('master')

@section('title', 'تعديل محدد حضور')

@section('content')
    <!-- Header Section -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل محدد حضور</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="#">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('attendance_determinants.index') }}">محددات الحضور</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <label>الحقول التي عليها علامة <span style="color: red">*</span> إلزامية</label>
                </div>
                <div>
                    <a href="{{ route('attendance_determinants.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i> إلغاء
                    </a>
                    <button type="button" id="updateBtn" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i> تحديث
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger" role="alert">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- Form Section -->
    <div class="card">
        <div class="card-body">
            <form id="AttendanceDeterminantForm" method="POST" action="{{ route('attendance_determinants.update', $attendance_determinants->id) }}">
                @csrf
                @method('PUT')

                <!-- Basic Information -->
                <h6 class="mb-2 p-1" style="background: #f8f8f8">المعلومات الأساسية</h6>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="name" class="form-label">الاسم <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name"
                               value="{{ old('name', $attendance_determinants->name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">الحالة <span class="text-danger">*</span></label>
                        <select name="status" id="status" class="form-control" required>
                            <option value="0" {{ old('status', $attendance_determinants->status) == 0 ? 'selected' : '' }}>نشط</option>
                            <option value="1" {{ old('status', $attendance_determinants->status) == 1 ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>

                <!-- Employee Image Section -->
                <h6 class="mb-2 p-1" style="background: #f8f8f8">التقاط صورة الموظف</h6>
                <div class="row mb-3">
                    <div class="col-md-6 mt-1">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" value="1" name="capture_employee_image" id="capture_employee_image"
                                       {{ old('capture_employee_image', $attendance_determinants->capture_employee_image) == 1 ? 'checked' : '' }}>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">تفعيل التقاط صورة الموظف</span>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                        <label for="image_investigation" class="form-label">التحقق من الصورة</label>
                        <select name="image_investigation" id="image_investigation" class="form-control">
                            <option value="1" {{ old('image_investigation', $attendance_determinants->image_investigation) == 1 ? 'selected' : '' }}>مطلوب</option>
                            <option value="2" {{ old('image_investigation', $attendance_determinants->image_investigation) == 2 ? 'selected' : '' }}>اختياري</option>
                        </select>
                    </div>
                </div>

                <!-- IP Verification Section -->
                <h6 class="mb-2 p-1" style="background: #f8f8f8">التحقق من عنوان IP</h6>
                <div class="row mb-3">
                    <div class="col-md-6 mt-1">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" value="1" name="enable_ip_verification" id="enable_ip_verification"
                                       {{ old('enable_ip_verification', $attendance_determinants->enable_ip_verification) == 1 ? 'checked' : '' }}>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">تفعيل التحقق من عنوان IP</span>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                        <label for="ip_investigation" class="form-label">التحقق من IP</label>
                        <select name="ip_investigation" id="ip_investigation" class="form-control">
                            <option value="1" {{ old('ip_investigation', $attendance_determinants->ip_investigation) == 1 ? 'selected' : '' }}>مطلوب</option>
                            <option value="2" {{ old('ip_investigation', $attendance_determinants->ip_investigation) == 2 ? 'selected' : '' }}>اختياري</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3" id="ip_addresses_section">
                        <label for="allowed_ips" class="form-label">عناوين IP المسموحة</label>
                        <textarea name="allowed_ips" id="allowed_ips" class="form-control" rows="3"
                                  placeholder="أدخل عناوين IP مفصولة بفواصل أو أسطر جديدة">{{ old('allowed_ips', is_array($attendance_determinants->allowed_ips) ? implode(', ', $attendance_determinants->allowed_ips) : $attendance_determinants->allowed_ips) }}</textarea>
                        <small class="form-text text-muted">مثال: 192.168.1.1, 192.168.1.2</small>
                    </div>
                </div>

                <!-- Location Verification Section -->
                <h6 class="mb-2 p-1" style="background: #f8f8f8">التحقق من الموقع الجغرافي</h6>
                <div class="row mb-3">
                    <div class="col-md-6 mt-1">
                        <fieldset>
                            <div class="vs-checkbox-con vs-checkbox-primary">
                                <input type="checkbox" value="1" name="enable_location_verification" id="enable_location_verification"
                                       {{ old('enable_location_verification', $attendance_determinants->enable_location_verification) == 1 ? 'checked' : '' }}>
                                <span class="vs-checkbox">
                                    <span class="vs-checkbox--check">
                                        <i class="vs-icon feather icon-check"></i>
                                    </span>
                                </span>
                                <span class="">تفعيل التحقق من الموقع</span>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-6">
                        <label for="location_investigation" class="form-label">التحقق من الموقع</label>
                        <select name="location_investigation" id="location_investigation" class="form-control">
                            <option value="1" {{ old('location_investigation', $attendance_determinants->location_investigation) == 1 ? 'selected' : '' }}>مطلوب</option>
                            <option value="2" {{ old('location_investigation', $attendance_determinants->location_investigation) == 2 ? 'selected' : '' }}>اختياري</option>
                        </select>
                    </div>

                    <div class="col-md-6 mt-3" id="radius_section">
                        <label for="radius" class="form-label">نطاق الموقع</label>
                        <input type="number" class="form-control" id="radius" name="radius"
                               min="1" step="0.01" value="{{ old('radius', $attendance_determinants->radius) }}">
                    </div>

                    <div class="col-md-6 mt-3" id="radius_type_section">
                        <label for="radius_type" class="form-label">وحدة القياس</label>
                        <select name="radius_type" id="radius_type" class="form-control">
                            <option value="1" {{ old('radius_type', $attendance_determinants->radius_type) == 1 ? 'selected' : '' }}>متر</option>
                            <option value="2" {{ old('radius_type', $attendance_determinants->radius_type) == 2 ? 'selected' : '' }}>كيلومتر</option>
                        </select>
                    </div>

                    <div class="col-md-12 mt-3" id="map_section">
                        <label class="form-label">تحديد الموقع على الخريطة</label>
                        <div id="map" style="width: 100%; height: 400px; border-radius: 8px;"></div>
                        <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude', $attendance_determinants->latitude) }}">
                        <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude', $attendance_determinants->longitude) }}">
                        <small class="form-text text-muted">اضغط على الخريطة لتحديد الموقع أو اسحب المؤشر</small>
                    </div>
                </div>

            </form>
        </div>
    </div>

@endsection

@section('scripts')
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        let map;
        let marker;

        function initMap() {
            // Get saved coordinates or use default (Riyadh)
            const latitude = {{ $attendance_determinants->latitude ?? 24.7136 }};
            const longitude = {{ $attendance_determinants->longitude ?? 46.6753 }};
            const center = { lat: latitude, lng: longitude };

            // Create map
            map = new google.maps.Map(document.getElementById("map"), {
                zoom: 15,
                center: center,
                mapTypeId: 'roadmap'
            });

            // Create marker
            marker = new google.maps.Marker({
                position: center,
                map: map,
                draggable: true,
                title: 'موقع العمل'
            });

            // Update hidden fields
            function updateCoordinates(lat, lng) {
                document.getElementById("latitude").value = lat;
                document.getElementById("longitude").value = lng;
            }

            // Map click event
            map.addListener("click", function (event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();

                marker.setPosition(event.latLng);
                updateCoordinates(lat, lng);
            });

            // Marker drag event
            marker.addListener("dragend", function (event) {
                const lat = event.latLng.lat();
                const lng = event.latLng.lng();
                updateCoordinates(lat, lng);
            });

            // Set initial coordinates
            updateCoordinates(center.lat, center.lng);
        }

        $(document).ready(function() {
            // Toggle sections based on checkboxes
            function toggleSections() {
                // IP Section
                if ($('#enable_ip_verification').is(':checked')) {
                    $('#ip_addresses_section').slideDown();
                } else {
                    $('#ip_addresses_section').slideUp();
                }

                // Location Section
                if ($('#enable_location_verification').is(':checked')) {
                    $('#radius_section, #radius_type_section, #map_section').slideDown();
                    // Initialize map if not already initialized
                    if (typeof google !== 'undefined' && google.maps && !map) {
                        setTimeout(initMap, 100);
                    }
                } else {
                    $('#radius_section, #radius_type_section, #map_section').slideUp();
                }
            }

            // Initial toggle
            toggleSections();

            // Checkbox change events
            $('#enable_ip_verification').change(toggleSections);
            $('#enable_location_verification').change(function() {
                toggleSections();
                if ($(this).is(':checked')) {
                    setTimeout(function() {
                        if (typeof google !== 'undefined' && google.maps) {
                            initMap();
                        }
                    }, 300);
                }
            });

            // Form submission
            $('#updateBtn').on('click', function(e) {
                e.preventDefault();

                // Validation
                if (!$('#name').val().trim()) {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى إدخال اسم محدد الحضور',
                        icon: 'error',
                        confirmButtonText: 'موافق'
                    });
                    return;
                }

                // Show confirmation
                Swal.fire({
                    title: 'تأكيد التحديث',
                    text: 'هل أنت متأكد من أنك تريد تحديث محدد الحضور؟',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'نعم، حدث',
                    cancelButtonText: 'إلغاء',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        Swal.fire({
                            title: 'جارٍ التحديث...',
                            text: 'يرجى الانتظار',
                            allowOutsideClick: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit form
                        $('#AttendanceDeterminantForm').submit();
                    }
                });
            });
        });
    </script>

    <script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyB6Hsnt5MiyjXtrGT5q-5KUj09XmLPV5So&callback=initMap">
    </script>
@endsection
