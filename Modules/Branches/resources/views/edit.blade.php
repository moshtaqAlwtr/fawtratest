@extends('master')

@section('title')
    تعديل فرع - {{ $branch->name }}
@stop

@section('content')
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">تعديل فرع</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسية</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('branches.index') }}">الفروع</a></li>
                            <li class="breadcrumb-item active">تعديل</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="content-body">
        <section id="basic-vertical-layouts">
            <div class="row match-height">
                <div class="col-md-12 col-12">
                    <form class="form form-vertical" id="branchForm" action="{{ route('branches.update', $branch->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- عرض الأخطاء -->
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="card">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center flex-wrap">
                                    <div>
                                        <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                                    </div>
                                    <div>
                                        <button type="button" class="btn btn-outline-danger" onclick="cancelForm()">
                                            <i class="fa fa-ban"></i> إلغاء
                                        </button>
                                        <button type="submit" class="btn btn-outline-primary" id="saveBtn">
                                            <i class="fa fa-save"></i> حفظ التعديلات
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">بيانات الفرع الأساسية</h4>
                            </div>
                            <div class="card-content">
                                <div class="card-body">
                                    <div class="form-body">
                                        <div class="row">
                                            <!-- اسم الفرع -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="name">اسم الفرع <span class="text-danger">*</span></label>
                                                    <input type="text" id="name" class="form-control" name="name"
                                                        placeholder="أدخل اسم الفرع" value="{{ old('name', $branch->name) }}" required>
                                                </div>
                                            </div>

                                            <!-- حقل رئيسي -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <div class="custom-control custom-checkbox">
                                                        <input type="checkbox" class="custom-control-input" id="is_main"
                                                            name="is_main" value="1"
                                                            {{ old('is_main', $branch->is_main) ? 'checked' : '' }}>
                                                        <label class="custom-control-label" for="is_main">فرع رئيسي</label>
                                                    </div>
                                                    <small class="text-muted">إذا تم تحديده، سيكون هذا الفرع الرئيسي للنظام</small>
                                                </div>
                                            </div>

                                            <!-- الكود -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="code">الكود <span class="text-danger">*</span></label>
                                                    <input type="text" id="code" class="form-control" name="code"
                                                        value="{{ old('code', $branch->code) }}" readonly required>
                                                </div>
                                            </div>

                                            <!-- هاتف الفرع -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="phone">هاتف الفرع</label>
                                                    <input type="text" id="phone" class="form-control" name="phone"
                                                        placeholder="أدخل هاتف الفرع" value="{{ old('phone', $branch->phone) }}">
                                                </div>
                                            </div>

                                            <!-- الجوال -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="mobile">الجوال</label>
                                                    <input type="text" id="mobile" class="form-control" name="mobile"
                                                        placeholder="أدخل رقم الجوال" value="{{ old('mobile', $branch->mobile) }}">
                                                </div>
                                            </div>

                                            <!-- البلد -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="country">البلد <span class="text-danger">*</span></label>
                                                    <input type="text" id="country" class="form-control" name="country"
                                                        placeholder="أدخل البلد" value="{{ old('country', $branch->country) }}" required>
                                                </div>
                                            </div>

                                            <!-- المدينة -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="city">المدينة <span class="text-danger">*</span></label>
                                                    <input type="text" id="city" class="form-control" name="city"
                                                        placeholder="أدخل المدينة" value="{{ old('city', $branch->city) }}" required>
                                                </div>
                                            </div>

                                            <!-- المنطقة -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="region">المنطقة</label>
                                                    <input type="text" id="region" class="form-control"
                                                        name="region" placeholder="أدخل المنطقة" value="{{ old('region', $branch->region) }}">
                                                </div>
                                            </div>

                                            <!-- العنوان الرئيسي -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address1">العنوان الرئيسي</label>
                                                    <input type="text" id="address1" class="form-control"
                                                        name="address1" placeholder="أدخل العنوان الرئيسي" value="{{ old('address1', $branch->address1) }}">
                                                </div>
                                            </div>

                                            <!-- الحي -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <label for="address2">الحي</label>
                                                    <input type="text" id="address2" class="form-control"
                                                        name="address2" placeholder="أدخل الحي" value="{{ old('address2', $branch->address2) }}">
                                                </div>
                                            </div>

                                            <!-- ساعات العمل -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="work_hours">ساعات العمل</label>
                                                    <textarea id="work_hours" class="form-control" name="work_hours" rows="3"
                                                        placeholder="مثال: من 8:00 صباحاً إلى 5:00 مساءً">{{ old('work_hours', $branch->work_hours) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- وصف الفرع -->
                                            <div class="col-md-6 col-12">
                                                <div class="form-group">
                                                    <label for="description">وصف الفرع</label>
                                                    <textarea id="description" class="form-control" name="description" rows="3"
                                                        placeholder="أدخل وصف الفرع">{{ old('description', $branch->description) }}</textarea>
                                                </div>
                                            </div>

                                            <!-- خريطة تحديد الموقع -->
                                            <div class="col-12">
                                                <div class="form-group">
                                                    <button type="button" class="btn btn-outline-primary mb-2"
                                                        onclick="toggleMap()">
                                                        <i class="feather icon-map"></i>
                                                        {{ $branch->location ? 'تعديل موقع الفرع على الخريطة' : 'تحديد موقع الفرع على الخريطة' }}
                                                    </button>
                                                    <div id="map-container" style="display: none; margin-bottom: 20px;">
                                                        <div class="input-group mb-2">
                                                            <input type="text" id="search-location"
                                                                class="form-control" placeholder="ابحث عن موقع...">
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button"
                                                                    onclick="searchLocation()">
                                                                    <i class="feather icon-search"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        <div id="map"
                                                            style="height: 400px; width: 100%; border: 1px solid #ddd; border-radius: 4px;">
                                                        </div>
                                                        <small class="text-muted">اسحب العلامة لتحديد الموقع بدقة</small>
                                                    </div>
                                                    <input type="hidden" id="latitude" name="latitude"
                                                        value="{{ old('latitude', $branch->location->latitude ?? '') }}">
                                                    <input type="hidden" id="longitude" name="longitude"
                                                        value="{{ old('longitude', $branch->location->longitude ?? '') }}">

                                                    @if ($branch->location)
                                                        <div class="current-location mt-2">
                                                            <p class="mb-0">
                                                                <i class="feather icon-map-pin text-primary"></i>
                                                                الموقع الحالي:
                                                                <span class="text-info">
                                                                    {{ $branch->location->latitude }},
                                                                    {{ $branch->location->longitude }}
                                                                </span>
                                                            </p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
@endsection

@section('scripts')
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Google Maps API -->
    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initMap"
        async defer></script>

    <script>
        let map;
        let marker;
        let geocoder;
        let searchBox;

        // التحقق من النجاح أو الخطأ عند تحميل الصفحة
        @if(session('success'))
            Swal.fire({
                title: 'نجح!',
                text: '{{ session('success') }}',
                icon: 'success',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#28a745'
            });
        @endif

        @if(session('error'))
            Swal.fire({
                title: 'خطأ!',
                text: '{{ session('error') }}',
                icon: 'error',
                confirmButtonText: 'حسناً',
                confirmButtonColor: '#dc3545'
            });
        @endif

        // التعامل مع إرسال النموذج
        document.getElementById('branchForm').addEventListener('submit', function(e) {
            e.preventDefault();

            // التحقق من الحقول المطلوبة
            const requiredFields = ['name', 'country', 'city'];
            let hasError = false;
            let errorMessage = '';

            requiredFields.forEach(field => {
                const input = document.getElementById(field);
                if (!input.value.trim()) {
                    hasError = true;
                    errorMessage += `• ${input.previousElementSibling.textContent.replace('*', '').trim()}\n`;
                }
            });

            if (hasError) {
                Swal.fire({
                    title: 'حقول مطلوبة!',
                    text: 'يرجى ملء الحقول التالية:\n' + errorMessage,
                    icon: 'warning',
                    confirmButtonText: 'حسناً',
                    confirmButtonColor: '#ffc107'
                });
                return;
            }

            // تأكيد الحفظ
            Swal.fire({
                title: 'تأكيد التعديل',
                text: 'هل أنت متأكد من حفظ تعديلات بيانات الفرع؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ التعديلات',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // إظهار رسالة التحميل
                    Swal.fire({
                        title: 'جاري حفظ التعديلات...',
                        text: 'يرجى الانتظار',
                        allowOutsideClick: false,
                        allowEscapeKey: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // إرسال النموذج
                    this.submit();
                }
            });
        });

        // إلغاء النموذج
        function cancelForm() {
            Swal.fire({
                title: 'تأكيد الإلغاء',
                text: 'هل أنت متأكد من إلغاء تعديل الفرع؟ سيتم فقدان جميع التعديلات غير المحفوظة.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، إلغاء',
                cancelButtonText: 'البقاء هنا',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '{{ route("branches.index") }}';
                }
            });
        }

        // تهيئة خرائط جوجل
        function initMap() {
            // الموقع الافتراضي أو الموقع المحفوظ
            const savedLat = document.getElementById('latitude').value;
            const savedLng = document.getElementById('longitude').value;

            const defaultLocation = savedLat && savedLng ? {
                lat: parseFloat(savedLat),
                lng: parseFloat(savedLng)
            } : {
                lat: 24.7136,
                lng: 46.6753
            };

            // إنشاء الخريطة
            map = new google.maps.Map(document.getElementById('map'), {
                center: defaultLocation,
                zoom: savedLat && savedLng ? 15 : 12,
                mapTypeControl: true,
                streetViewControl: false
            });

            // إنشاء العلامة
            marker = new google.maps.Marker({
                position: defaultLocation,
                map: map,
                draggable: true,
                title: "اسحبني لتحديد الموقع"
            });

            // تهيئة خدمة العناوين
            geocoder = new google.maps.Geocoder();

            // تحديث الإحداثيات وعنوان الفرع عند تحريك العلامة
            marker.addListener('dragend', function() {
                updatePosition(marker.getPosition());
                getAddressFromLatLng(marker.getPosition());
            });

            // تحديد الموقع وعنوان الفرع عند النقر على الخريطة
            map.addListener('click', function(event) {
                marker.setPosition(event.latLng);
                updatePosition(event.latLng);
                getAddressFromLatLng(event.latLng);
            });

            // تهيئة مربع البحث
            const input = document.getElementById('search-location');
            searchBox = new google.maps.places.SearchBox(input);

            // عند اختيار نتيجة بحث
            searchBox.addListener('places_changed', function() {
                const places = searchBox.getPlaces();
                if (places.length === 0) return;

                const bounds = new google.maps.LatLngBounds();
                places.forEach(place => {
                    if (!place.geometry) return;

                    if (place.geometry.viewport) {
                        bounds.union(place.geometry.viewport);
                    } else {
                        bounds.extend(place.geometry.location);
                    }

                    marker.setPosition(place.geometry.location);
                    updatePosition(place.geometry.location);
                    getAddressFromPlace(place);
                    map.fitBounds(bounds);
                });
            });
        }

        function updatePosition(latLng) {
            document.getElementById('latitude').value = latLng.lat();
            document.getElementById('longitude').value = latLng.lng();
        }

        function getAddressFromLatLng(latLng) {
            geocoder.geocode({
                'location': latLng
            }, function(results, status) {
                if (status === 'OK') {
                    if (results[0]) {
                        fillAddressFields(results[0]);
                    }
                } else {
                    console.error('فشل في الحصول على العنوان: ' + status);
                }
            });
        }

        function getAddressFromPlace(place) {
            const result = {
                address_components: place.address_components,
                formatted_address: place.formatted_address
            };
            fillAddressFields(result);
        }

        function fillAddressFields(result) {
            const addressComponents = result.address_components;
            let streetNumber = '';
            let route = '';
            let neighborhood = '';
            let locality = '';
            let administrativeArea = '';
            let country = '';

            addressComponents.forEach(component => {
                const types = component.types;
                if (types.includes('street_number')) {
                    streetNumber = component.long_name;
                } else if (types.includes('route')) {
                    route = component.long_name;
                } else if (types.includes('neighborhood') || types.includes('sublocality')) {
                    neighborhood = component.long_name;
                } else if (types.includes('locality')) {
                    locality = component.long_name;
                } else if (types.includes('administrative_area_level_1')) {
                    administrativeArea = component.long_name;
                } else if (types.includes('country')) {
                    country = component.long_name;
                }
            });

            // عرض تأكيد قبل تحديث الحقول
            Swal.fire({
                title: 'تحديث بيانات العنوان',
                text: 'هل تريد تحديث بيانات العنوان تلقائياً من الموقع المحدد؟',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'نعم، حدث',
                cancelButtonText: 'لا، احتفظ بالحالي',
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d'
            }).then((result) => {
                if (result.isConfirmed) {
                    // تحديث الحقول
                    if (streetNumber || route) {
                        document.getElementById('address1').value = [streetNumber, route].filter(Boolean).join(' ');
                    }
                    if (neighborhood) {
                        document.getElementById('address2').value = neighborhood;
                    }
                    if (locality) {
                        document.getElementById('city').value = locality;
                    }
                    if (administrativeArea) {
                        document.getElementById('region').value = administrativeArea;
                    }
                    if (country) {
                        document.getElementById('country').value = country;
                    }

                    // إظهار رسالة نجاح
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'success',
                        title: 'تم تحديث بيانات العنوان',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        }

        function toggleMap() {
            const mapContainer = document.getElementById('map-container');
            if (mapContainer.style.display === 'none') {
                mapContainer.style.display = 'block';
                setTimeout(() => {
                    if (typeof google === 'object' && typeof google.maps === 'object') {
                        if (!map) {
                            initMap();
                        } else {
                            google.maps.event.trigger(map, 'resize');
                            // إذا كان هناك موقع محفوظ، اعرضه
                            const savedLat = document.getElementById('latitude').value;
                            const savedLng = document.getElementById('longitude').value;
                            if (savedLat && savedLng) {
                                const savedLocation = {
                                    lat: parseFloat(savedLat),
                                    lng: parseFloat(savedLng)
                                };
                                map.setCenter(savedLocation);
                                marker.setPosition(savedLocation);
                            }
                        }
                    } else {
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'info',
                            title: 'جاري تحميل الخريطة...',
                            showConfirmButton: false,
                            timer: 2000
                        });
                    }
                }, 100);
            } else {
                mapContainer.style.display = 'none';
            }
        }

        function searchLocation() {
            const input = document.getElementById('search-location');
            if (input.value.trim() === '') {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'warning',
                    title: 'يرجى إدخال موقع للبحث',
                    showConfirmButton: false,
                    timer: 2000
                });
                return;
            }

            // محاكاة البحث (يتم التعامل معه تلقائياً بواسطة SearchBox)
            google.maps.event.trigger(input, 'focus');
            google.maps.event.trigger(input, 'keydown', { keyCode: 13 });
        }

        // إضافة تأثيرات تفاعلية للحقول
        document.addEventListener('DOMContentLoaded', function() {
            // إضافة تأثير للحقول المطلوبة
            const requiredInputs = document.querySelectorAll('input[required], textarea[required]');
            requiredInputs.forEach(input => {
                input.addEventListener('blur', function() {
                    if (!this.value.trim()) {
                        this.classList.add('is-invalid');
                    } else {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });

                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                        this.classList.add('is-valid');
                    }
                });
            });

            // إضافة تأثير لتتبع التغييرات في النموذج
            const formInputs = document.querySelectorAll('#branchForm input, #branchForm textarea, #branchForm select');
            let originalValues = {};

            // حفظ القيم الأصلية
            formInputs.forEach(input => {
                if (input.type === 'checkbox') {
                    originalValues[input.name] = input.checked;
                } else {
                    originalValues[input.name] = input.value;
                }
            });

            // مراقبة التغييرات
            let hasChanges = false;
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    let currentValue = input.type === 'checkbox' ? input.checked : input.value;
                    hasChanges = originalValues[input.name] !== currentValue;

                    if (hasChanges) {
                        document.getElementById('saveBtn').innerHTML = '<i class="fa fa-save"></i> حفظ التعديلات *';
                        document.getElementById('saveBtn').classList.add('btn-warning');
                        document.getElementById('saveBtn').classList.remove('btn-outline-primary');
                    } else {
                        document.getElementById('saveBtn').innerHTML = '<i class="fa fa-save"></i> حفظ التعديلات';
                        document.getElementById('saveBtn').classList.remove('btn-warning');
                        document.getElementById('saveBtn').classList.add('btn-outline-primary');
                    }
                });
            });

            // تحذير عند مغادرة الصفحة مع وجود تغييرات غير محفوظة
            window.addEventListener('beforeunload', function(e) {
                if (hasChanges) {
                    e.preventDefault();
                    e.returnValue = 'لديك تغييرات غير محفوظة. هل أنت متأكد من مغادرة الصفحة؟';
                }
            });
        });
    </script>

    <style>
        .is-invalid {
            border-color: #dc3545;
        }

        .is-valid {
            border-color: #28a745;
        }

        .form-control:focus {
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .custom-control-input:checked ~ .custom-control-label::before {
            background-color: #007bff;
            border-color: #007bff;
        }

        .btn:hover {
            transform: translateY(-1px);
            transition: all 0.2s ease-in-out;
        }

        .card {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .current-location {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 5px;
            border-left: 4px solid #007bff;
        }

        .btn-warning {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(255, 193, 7, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 193, 7, 0); }
        }
    </style>
@endsection