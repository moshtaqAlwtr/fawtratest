/**
 * Map Manager - إدارة الخريطة
 * يدير جميع العمليات المتعلقة بالخريطة
 */

class MapManager {
    constructor() {
        this.map = null;
        this.infoWindow = null;
        this.currentUserMarker = null;
        this.allMarkers = [];
        this.markerCluster = null;
        this.isMapLoaded = false;
        this.isInitialized = false;

        // إعدادات الخريطة
        this.defaultCenter = { lat: 24.7136, lng: 46.6753 }; // الرياض
        this.retryAttempts = 0;
        this.maxRetryAttempts = 3;
    }

    /**
     * تهيئة الخريطة
     */
    init() {
        this.initializeMapState();
        this.bindEvents();
    }

    /**
     * ربط الأحداث
     */
    bindEvents() {
        // زر الخريطة
        $('#toggleMapButton').on('click', (e) => {
            e.preventDefault();
            this.toggleMap();
        });

        // زر التكبير/التصغير
        $('#fullscreenButton').on('click', (e) => {
            e.preventDefault();
            this.toggleFullscreen();
        });

        // معالج تغيير حجم الشاشة
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (this.map && this.isMapLoaded) {
                    this.handleResize();
                }
            }, 250);
        });
    }

    /**
     * تعيين الحالة الافتراضية للخريطة
     */
    initializeMapState() {
        // تعيين الحالة الافتراضية للخريطة مغلقة
        localStorage.setItem('mapOpen', 'false');

        const toggleButton = $('#toggleMapButton');
        const mapContainer = $('#mapContainer');
        const actionCard = $('#actionCard');

        // إخفاء الخريطة بشكل افتراضي
        mapContainer.addClass('map-hidden').removeClass('map-show');
        actionCard.addClass('map-closed').removeClass('map-open');
        toggleButton.removeClass('map-active')
            .attr('data-tooltip', 'عرض الخريطة')
            .attr('title', 'عرض الخريطة')
            .find('i').removeClass('fa-map').addClass('fa-map-marked-alt');
    }

    /**
     * تبديل حالة الخريطة
     */
    toggleMap() {
        const isMapOpen = localStorage.getItem('mapOpen') === 'true';
        const newState = !isMapOpen;

        localStorage.setItem('mapOpen', newState);

        const toggleButton = $('#toggleMapButton');
        const mapContainer = $('#mapContainer');
        const actionCard = $('#actionCard');

        // إضافة تأثير الموجة
        const ripple = $('<div class="map-toggle-effect"></div>');
        toggleButton.append(ripple);
        ripple.addClass('active');
        setTimeout(() => ripple.remove(), 600);

        if (newState) {
            this.showMap(toggleButton, mapContainer, actionCard);
        } else {
            this.hideMap(toggleButton, mapContainer, actionCard);
        }
    }

    /**
     * إظهار الخريطة
     */
    showMap(toggleButton, mapContainer, actionCard) {
        actionCard.addClass('map-open').removeClass('map-closed');
        setTimeout(() => mapContainer.removeClass('map-hidden').addClass('map-show'), 200);

        toggleButton.addClass('map-active')
            .attr('data-tooltip', 'إخفاء الخريطة')
            .attr('title', 'إخفاء الخريطة')
            .find('i').removeClass('fa-map-marked-alt').addClass('fa-map');

        if (!this.isMapLoaded) {
            this.loadGoogleMaps();
        } else if (this.map) {
            setTimeout(() => {
                google.maps.event.trigger(this.map, 'resize');
                this.map.setCenter(this.defaultCenter);
                this.fitMapToMarkers();
            }, 700);
        }
    }

    /**
     * إخفاء الخريطة
     */
    hideMap(toggleButton, mapContainer, actionCard) {
        mapContainer.removeClass('map-show').addClass('map-hidden');
        setTimeout(() => actionCard.addClass('map-closed').removeClass('map-open'), 300);

        toggleButton.removeClass('map-active')
            .attr('data-tooltip', 'عرض الخريطة')
            .attr('title', 'عرض الخريطة')
            .find('i').removeClass('fa-map').addClass('fa-map-marked-alt');
    }

    /**
     * تحميل Google Maps
     */
    loadGoogleMaps() {
        if (typeof google !== 'undefined' && google.maps) {
            setTimeout(() => this.initializeMap(), 500);
            return;
        }

        // انتظار تحميل Google Maps
        this.waitForGoogleMaps();
    }

    /**
     * انتظار تحميل Google Maps
     */
    waitForGoogleMaps() {
        const checkGoogleMaps = setInterval(() => {
            if (typeof google !== 'undefined' && google.maps) {
                clearInterval(checkGoogleMaps);
                setTimeout(() => this.initializeMap(), 500);
            }
        }, 500);

        setTimeout(() => {
            clearInterval(checkGoogleMaps);
            if (!this.isMapLoaded) {
                console.error('❌ فشل في تحميل Google Maps');
                this.showMapError();
            }
        }, 15000);
    }

    /**
     * تهيئة الخريطة
     */
    initializeMap() {
        const mapElement = document.getElementById('map');
        if (!mapElement) {
            console.error('❌ عنصر الخريطة غير موجود');
            return;
        }

        if (typeof google === 'undefined' || !google.maps) {
            if (this.retryAttempts < this.maxRetryAttempts) {
                this.retryAttempts++;
                setTimeout(() => this.initializeMap(), 1000);
            } else {
                this.showMapError();
            }
            return;
        }

        try {
            const isMobile = window.innerWidth <= 768;
            const isTablet = window.innerWidth > 768 && window.innerWidth <= 992;

            const mapOptions = {
                zoom: isMobile ? 9 : isTablet ? 10 : 11,
                center: this.defaultCenter,
                mapTypeId: google.maps.MapTypeId.ROADMAP,
                zoomControl: true,
                mapTypeControl: true,
                scaleControl: false,
                streetViewControl: false,
                rotateControl: false,
                fullscreenControl: true,
                gestureHandling: isMobile ? 'cooperative' : 'greedy',
                styles: [],
                // إعدادات إضافية للتوافق مع جميع الأجهزة
                disableDefaultUI: false,
                clickableIcons: false,
                keyboardShortcuts: false
            };

            this.map = new google.maps.Map(mapElement, mapOptions);

            google.maps.event.addListenerOnce(this.map, 'idle', () => {
                this.isMapLoaded = true;
                this.isInitialized = true;
                google.maps.event.trigger(this.map, 'resize');

                // تحميل بيانات الخريطة
                if (window.searchManager) {
                    this.loadMapData(window.searchManager.getCurrentFilters());
                }

                // إضافة زر التوسيط
                setTimeout(() => {
                    this.addCenterMapButton();
                }, 1000);
            });

            this.infoWindow = new google.maps.InfoWindow({
                maxWidth: isMobile ? 250 : 300,
                disableAutoPan: false
            });

            // معالج الأخطاء
            google.maps.event.addListener(this.map, 'tilesloaded', () => {
                console.log('✅ تم تحميل الخريطة بنجاح');
            });

        } catch (error) {
            console.error('❌ خطأ في تهيئة الخريطة:', error);
            this.isMapLoaded = false;
            this.showMapError();
        }
    }

    /**
     * معالج تغيير حجم الشاشة
     */
    handleResize() {
        google.maps.event.trigger(this.map, 'resize');
        this.map.setCenter(this.defaultCenter);

        const newIsMobile = window.innerWidth <= 768;
        this.map.setOptions({
            gestureHandling: newIsMobile ? 'cooperative' : 'greedy'
        });

        setTimeout(() => this.fitMapToMarkers(), 300);
    }

    /**
     * تحميل بيانات الخريطة
     */
    loadMapData(params = {}) {
        if (!this.isMapLoaded || !this.map) return;

        $.ajax({
            url: window.clientRoutes.getMapData,
            method: 'GET',
            data: params,
            success: (response) => {
                if (response && response.clients && Array.isArray(response.clients)) {
                    this.updateMapMarkers(response.clients);
                } else {
                    this.clearMapMarkers();
                }
            },
            error: (xhr) => {
                console.error('خطأ في تحميل بيانات الخريطة:', xhr);
            }
        });
    }

    /**
     * تحديث علامات الخريطة
     */
    updateMapMarkers(clientsData) {
        this.clearMapMarkers();

        if (!clientsData || clientsData.length === 0) {
            this.hideEmptyClusters();
            return;
        }

        this.allMarkers = [];
        const validClients = clientsData.filter(client =>
            client.lat && client.lng &&
            parseFloat(client.lat) !== 0 &&
            parseFloat(client.lng) !== 0
        );

        // إذا لم تكن هناك عملاء صالحين، أخف الكتل
        if (validClients.length === 0) {
            this.hideEmptyClusters();
            return;
        }

        validClients.forEach((client, index) => {
            const lat = parseFloat(client.lat);
            const lng = parseFloat(client.lng);

            if (isNaN(lat) || isNaN(lng)) return;

            try {
                const marker = this.createMarker(client, index, lat, lng);
                if (marker) {
                    this.allMarkers.push({
                        marker: marker,
                        clientName: (client.trade_name || '').toLowerCase(),
                        clientCode: String(client.code || '').toLowerCase(),
                        clientData: client
                    });
                }
            } catch (error) {
                console.error('خطأ في إنشاء العلامة للعميل:', client, error);
            }
        });

        if (this.allMarkers.length > 0) {
            this.initializeMarkerCluster();
            this.fitMapToMarkers();
        } else {
            this.hideEmptyClusters();
        }
    }

    /**
     * إنشاء علامة العميل
     */
    createMarker(client, index, lat, lng) {
        const displayCode = client.code ? String(client.code).substring(0, 6) : 'N/A';
        const statusColor = this.getSafeColor(client.statusColor);

        const marker = new google.maps.Marker({
            position: { lat: lat, lng: lng },
            map: this.map,
            title: `${client.trade_name || 'غير محدد'} (${client.code || 'غير محدد'})`,
            icon: {
                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                    <svg xmlns="http://www.w3.org/2000/svg" width="60" height="40" viewBox="0 0 60 40">
                        <defs>
                            <filter id="shadow${index}" x="-50%" y="-50%" width="200%" height="200%">
                                <feDropShadow dx="0" dy="2" stdDeviation="3" flood-opacity="0.3"/>
                            </filter>
                        </defs>
                        <rect x="5" y="5" width="50" height="30" rx="15" ry="15"
                              fill="${statusColor}" opacity="0.9" filter="url(#shadow${index})"/>
                        <text x="30" y="23" font-family="Arial, sans-serif" font-size="12" font-weight="bold"
                              fill="white" text-anchor="middle" dominant-baseline="middle">
                            ${displayCode}
                        </text>
                    </svg>
                `),
                scaledSize: new google.maps.Size(60, 40),
                anchor: new google.maps.Point(30, 20)
            },
            optimized: true // تحسين الأداء
        });

        marker.addListener('click', () => this.showClientInfo(marker, client));

        return marker;
    }

    /**
     * تهيئة تجميع العلامات
     */
    initializeMarkerCluster() {
        if (typeof markerClusterer !== 'undefined' && this.allMarkers.length > 0) {
            this.markerCluster = new markerClusterer.MarkerClusterer({
                map: this.map,
                markers: this.allMarkers.map(m => m.marker),
                renderer: {
                    render: ({ count, position }) => {
                        // لا تعرض الكتلة إذا كان العدد 0
                        if (count === 0) return null;

                        const clusterColor = '#4285F4';
                        let circleSize = 45, fontSize = 14;

                        if (count >= 100) {
                            circleSize = 65;
                            fontSize = 16;
                        } else if (count >= 50) {
                            circleSize = 55;
                            fontSize = 15;
                        } else if (count >= 20) {
                            circleSize = 50;
                            fontSize = 14;
                        }

                        return new google.maps.Marker({
                            position,
                            icon: {
                                url: 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent(`
                                    <svg xmlns="http://www.w3.org/2000/svg" width="${circleSize}" height="${circleSize}" viewBox="0 0 ${circleSize} ${circleSize}">
                                        <defs>
                                            <filter id="clusterShadow" x="-50%" y="-50%" width="200%" height="200%">
                                                <feDropShadow dx="0" dy="2" stdDeviation="4" flood-opacity="0.3"/>
                                            </filter>
                                        </defs>
                                        <circle cx="${circleSize/2}" cy="${circleSize/2}" r="${(circleSize-6)/2}"
                                                fill="${clusterColor}" opacity="0.95" filter="url(#clusterShadow)"/>
                                        <text x="${circleSize/2}" y="${circleSize/2 + fontSize/3}" font-family="Arial, sans-serif"
                                              font-size="${fontSize}" font-weight="bold" fill="white" text-anchor="middle"
                                              dominant-baseline="middle">${count}</text>
                                    </svg>
                                `),
                                scaledSize: new google.maps.Size(circleSize, circleSize),
                                anchor: new google.maps.Point(circleSize / 2, circleSize / 2)
                            }
                        });
                    }
                }
            });
        }
    }

    /**
     * إخفاء الكتل الفارغة
     */
    hideEmptyClusters() {
        if (this.markerCluster) {
            this.markerCluster.clearMarkers();
        }

        // إخفاء جميع العلامات الموجودة
        if (this.allMarkers && this.allMarkers.length > 0) {
            this.allMarkers.forEach(item => {
                if (item.marker) {
                    item.marker.setVisible(false);
                }
            });
        }
    }

    /**
     * مسح علامات الخريطة
     */
    clearMapMarkers() {
        if (this.markerCluster) {
            this.markerCluster.clearMarkers();
            this.markerCluster = null;
        }

        this.allMarkers.forEach(item => {
            if (item.marker) item.marker.setMap(null);
        });

        this.allMarkers = [];
    }

    /**
     * فلترة العلامات
     */
    filterMarkers(searchValue) {
        let anyVisible = false;
        let visibleMarkers = [];

        this.allMarkers.forEach(item => {
            const isVisible = !searchValue ||
                item.clientName.includes(searchValue) ||
                item.clientCode.includes(searchValue);

            item.marker.setVisible(isVisible);

            if (isVisible) {
                anyVisible = true;
                visibleMarkers.push(item);

                if (item.clientName === searchValue || item.clientCode === searchValue) {
                    this.showClientInfo(item.marker, item.clientData);
                    this.map.panTo(item.marker.getPosition());
                    this.map.setZoom(window.innerWidth <= 768 ? 14 : 15);
                }
            }
        });

        // تحديث MarkerClusterer بالعلامات المرئية فقط
        if (this.markerCluster) {
            this.markerCluster.clearMarkers();
            if (visibleMarkers.length > 0) {
                this.markerCluster.addMarkers(visibleMarkers.map(item => item.marker));
            }
        }

        // إذا لم تكن هناك علامات مرئية، اعرض رسالة
        if (!anyVisible && searchValue) {
            this.showNoResultsMessage(searchValue);
        }
    }

    /**
     * عرض معلومات العميل
     */
    showClientInfo(marker, clientData) {
        if (this.infoWindow) this.infoWindow.close();

        const statusColor = this.getSafeColor(clientData.statusColor);
        const isMobile = window.innerWidth <= 768;

        const contentString = `
            <div style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; width: ${isMobile ? '250px' : '300px'};">
                <div style="background: ${statusColor}; color: white; padding: ${isMobile ? '12px' : '16px'}; text-align: center; border-radius: 8px 8px 0 0;">
                    <h6 style="margin: 0; font-size: ${isMobile ? '16px' : '18px'}; font-weight: 700;">
                        ${clientData.trade_name}
                    </h6>
                    <div style="margin-top: 5px; font-size: ${isMobile ? '12px' : '14px'};">${clientData.code}</div>
                    <div style="margin-top: 3px; font-size: ${isMobile ? '10px' : '12px'}; opacity: 0.9;">الحالة: ${clientData.status || 'غير محدد'}</div>
                </div>

                <div style="padding: ${isMobile ? '12px' : '16px'}; background: white; border-radius: 0 0 8px 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    <div style="display: grid; gap: ${isMobile ? '6px' : '8px'};">
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-phone" style="color: #6c757d; width: ${isMobile ? '16px' : '20px'}; font-size: ${isMobile ? '12px' : '14px'};"></i>
                            <span style="margin-right: 8px; color: #6c757d; font-size: ${isMobile ? '11px' : '14px'};">الهاتف:</span>
                            <a href="tel:${clientData.phone}" style="color: ${statusColor}; text-decoration: none; font-size: ${isMobile ? '11px' : '14px'};">
                                ${clientData.phone}
                            </a>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-building" style="color: #6c757d; width: ${isMobile ? '16px' : '20px'}; font-size: ${isMobile ? '12px' : '14px'};"></i>
                            <span style="margin-right: 8px; color: #6c757d; font-size: ${isMobile ? '11px' : '14px'};">الفرع:</span>
                            <span style="font-size: ${isMobile ? '11px' : '14px'};">${clientData.branch}</span>
                        </div>
                        <div style="display: flex; align-items: center;">
                            <i class="fas fa-map-marker-alt" style="color: #6c757d; width: ${isMobile ? '16px' : '20px'}; font-size: ${isMobile ? '12px' : '14px'};"></i>
                            <span style="margin-right: 8px; color: #6c757d; font-size: ${isMobile ? '11px' : '14px'};">العنوان:</span>
                            <span style="font-size: ${isMobile ? '11px' : '14px'};">${clientData.address}</span>
                        </div>
                    </div>

                    <div style="display: flex; gap: ${isMobile ? '6px' : '8px'}; margin-top: ${isMobile ? '12px' : '16px'};">
                        <button onclick="window.location.href='${window.clientRoutes.show}${clientData.id}'"
                            style="flex: 1; padding: ${isMobile ? '6px' : '8px'}; background: ${statusColor}; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: ${isMobile ? '11px' : '14px'};">
                            <i class="fas fa-info-circle"></i> التفاصيل
                        </button>
                        <button onclick="window.mapManager.openDirections(${marker.getPosition().lat()}, ${marker.getPosition().lng()})"
                            style="flex: 1; padding: ${isMobile ? '6px' : '8px'}; background: white; color: ${statusColor}; border: 1px solid ${statusColor}; border-radius: 4px; cursor: pointer; font-size: ${isMobile ? '11px' : '14px'};">
                            <i class="fas fa-directions"></i> الاتجاهات
                        </button>
                    </div>
                </div>
            </div>
        `;

        this.infoWindow.setContent(contentString);
        this.infoWindow.open(this.map, marker);
    }

    /**
     * إضافة زر توسيط الخريطة
     */
    addCenterMapButton() {
        // إنشاء زر التوسيط
        const centerButton = document.createElement('button');
        centerButton.innerHTML = '<i class="fas fa-crosshairs"></i>';
        centerButton.title = 'توسيط الخريطة';
        centerButton.className = 'map-center-button';

        // إضافة تأثيرات التفاعل
        centerButton.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
            this.style.transform = 'scale(1.05)';
            this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.3)';
        });

        centerButton.addEventListener('mouseleave', function() {
            this.style.backgroundColor = 'white';
            this.style.transform = 'scale(1)';
            this.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        });

        // وظيفة التوسيط
        centerButton.addEventListener('click', () => {
            this.centerMapToMarkers();

            // تأثير بصري للنقر
            centerButton.style.transform = 'scale(0.95)';
            setTimeout(() => {
                centerButton.style.transform = 'scale(1)';
            }, 150);
        });

        // إضافة الزر للخريطة
        const mapContainer = document.getElementById('map');
        if (mapContainer) {
            mapContainer.appendChild(centerButton);
        }
    }

    /**
     * توسيط الخريطة على العلامات
     */
    centerMapToMarkers() {
        if (!this.allMarkers || this.allMarkers.length === 0) {
            // العودة إلى المركز الافتراضي (الرياض)
            this.map.setCenter(this.defaultCenter);
            this.map.setZoom(window.innerWidth <= 768 ? 9 : 11);
            return;
        }

        // الحصول على العلامات المرئية فقط
        const visibleMarkers = this.allMarkers.filter(item =>
            item.marker && item.marker.getVisible && item.marker.getVisible()
        );

        if (visibleMarkers.length === 0) {
            // العودة إلى المركز الافتراضي
            this.map.setCenter(this.defaultCenter);
            this.map.setZoom(window.innerWidth <= 768 ? 9 : 11);
            return;
        }

        if (visibleMarkers.length === 1) {
            // علامة واحدة - التوسيط عليها
            const position = visibleMarkers[0].marker.getPosition();
            this.map.setCenter(position);
            this.map.setZoom(window.innerWidth <= 768 ? 14 : 15);
        } else {
            // عدة علامات - احتواء جميع العلامات المرئية
            this.fitMapToVisibleMarkers(visibleMarkers);
        }

        // إغلاق أي نافذة معلومات مفتوحة
        if (this.infoWindow) {
            this.infoWindow.close();
        }

        // مسح البحث
        const searchInput = document.getElementById('clientSearch');
        if (searchInput) {
            searchInput.value = '';
        }
    }

    /**
     * ضبط الخريطة لتتسع لجميع العلامات
     */
    fitMapToMarkers() {
        if (!this.allMarkers || this.allMarkers.length === 0) return;

        const bounds = new google.maps.LatLngBounds();
        let hasValidMarkers = false;

        this.allMarkers.forEach(item => {
            if (item.marker && item.marker.getVisible && item.marker.getVisible()) {
                const position = item.marker.getPosition();
                if (position) {
                    bounds.extend(position);
                    hasValidMarkers = true;
                }
            }
        });

        if (hasValidMarkers) {
            this.fitBounds(bounds);
        }
    }

    /**
     * ضبط الخريطة للعلامات المرئية
     */
    fitMapToVisibleMarkers(visibleMarkers) {
        const bounds = new google.maps.LatLngBounds();

        visibleMarkers.forEach(item => {
            const position = item.marker.getPosition();
            if (position) {
                bounds.extend(position);
            }
        });

        this.fitBounds(bounds);
    }

    /**
     * ضبط حدود الخريطة
     */
    fitBounds(bounds) {
        const maxZoom = window.innerWidth <= 768 ? 13 : 14;

        const listener = google.maps.event.addListenerOnce(this.map, 'bounds_changed', () => {
            if (this.map.getZoom() > maxZoom) {
                this.map.setZoom(maxZoom);
            }
        });

        this.map.fitBounds(bounds);
    }

    /**
     * عرض رسالة عدم وجود نتائج
     */
    showNoResultsMessage(searchValue) {
        if (this.infoWindow) {
            this.infoWindow.setContent(`
                <div style="padding: 20px; text-align: center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
                    <i class="fas fa-search" style="font-size: 32px; color: #6c757d; margin-bottom: 10px;"></i>
                    <h6 style="color: #495057; margin-bottom: 8px;">لا توجد نتائج</h6>
                    <p style="color: #6c757d; margin: 0; font-size: 14px;">
                        لم يتم العثور على عملاء مطابقين لـ "<strong>${searchValue}</strong>"
                    </p>
                </div>
            `);
            this.infoWindow.setPosition(this.map.getCenter());
            this.infoWindow.open(this.map);

            // إخفاء الرسالة بعد 3 ثواني
            setTimeout(() => {
                if (this.infoWindow) this.infoWindow.close();
            }, 3000);
        }
    }

    /**
     * عرض خطأ الخريطة
     */
    showMapError() {
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = `
                <div class="map-error-container">
                    <div class="map-error-card">
                        <i class="fas fa-exclamation-triangle" style="font-size: 48px; color: #dc3545; margin-bottom: 16px;"></i>
                        <h5 style="color: #dc3545; margin-bottom: 12px; font-weight: 600;">خطأ في تحميل الخريطة</h5>
                        <p style="color: #6c757d; margin-bottom: 20px; line-height: 1.5;">تعذر تحميل خريطة Google Maps. يرجى التحقق من اتصال الإنترنت أو إعادة المحاولة لاحقاً.</p>
                        <button onclick="window.mapManager.retryLoadMap()" class="btn btn-primary" style="background: linear-gradient(135deg, #4285f4, #1a73e8); border: none; padding: 10px 20px; border-radius: 8px; font-weight: 600;">
                            <i class="fas fa-sync-alt me-2"></i>إعادة المحاولة
                        </button>
                    </div>
                </div>
            `;
        }
    }

    /**
     * إعادة محاولة تحميل الخريطة
     */
    retryLoadMap() {
        const mapElement = document.getElementById('map');
        if (mapElement) {
            mapElement.innerHTML = `
                <div class="map-loading-container">
                    <div class="dot-pulse mb-3"></div>
                    <p style="color: #4285f4;">جارٍ تحميل الخريطة...</p>
                </div>
            `;
        }

        this.isMapLoaded = false;
        this.retryAttempts = 0;

        // إعادة تحميل سكريبت Google Maps
        const existingScript = document.querySelector('script[src*="maps.googleapis.com"]');
        if (existingScript) {
            existingScript.remove();
        }

        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${window.googleMapsApiKey}&libraries=places&callback=window.initMap`;
        script.async = true;
        script.defer = true;
        script.onerror = () => {
            console.error('❌ فشل في إعادة تحميل Google Maps');
            this.showMapError();
        };
        document.head.appendChild(script);
    }

    /**
     * فتح الاتجاهات
     */
    openDirections(lat, lng) {
        if (lat === 0 || lng === 0) {
            alert('لا يوجد إحداثيات متاحة لهذا العميل');
            return;
        }
        window.open(`https://www.google.com/maps?q=${lat},${lng}&z=17`, '_blank');
    }

    /**
     * إخفاء/إظهار علامة العميل
     */
    hideClientFromMap(clientId) {
        const markerItem = this.allMarkers.find(item =>
            item.clientData && item.clientData.id == clientId
        );

        if (markerItem && markerItem.marker) {
            markerItem.marker.setVisible(false);

            // تحديث MarkerClusterer إذا كان موجوداً
            if (this.markerCluster) {
                this.markerCluster.removeMarker(markerItem.marker);
            }
        }
    }

    showClientInMap(clientId) {
        const markerItem = this.allMarkers.find(item =>
            item.clientData && item.clientData.id == clientId
        );

        if (markerItem && markerItem.marker) {
            markerItem.marker.setVisible(true);

            // تحديث MarkerClusterer إذا كان موجوداً
            if (this.markerCluster) {
                this.markerCluster.addMarker(markerItem.marker);
            }
        }
    }

    /**
     * الحصول على لون آمن
     */
    getSafeColor(color) {
        const validHex = /^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/;
        if (color && validHex.test(color)) return color;

        const standardColors = ['red', 'green', 'blue', 'yellow', 'orange', 'purple', 'pink', 'brown', 'black', 'white', 'gray', 'grey'];
        if (color && standardColors.includes(color.toLowerCase())) return color;

        if (color && color.startsWith('rgb')) return color;

        return '#4361ee';
    }

    /**
     * تنظيف الموارد
     */
    cleanup() {
        this.clearMapMarkers();
        if (this.infoWindow) {
            this.infoWindow.close();
        }
        this.allMarkers = [];
        this.isMapLoaded = false;
        this.isInitialized = false;
    }

    /**
     * تبديل وضع ملء الشاشة للخريطة
     */
    toggleFullscreen() {
        const mapContainer = document.getElementById('mapContainer');
        const fullscreenButton = document.getElementById('fullscreenButton');
        const fullscreenIcon = fullscreenButton ? fullscreenButton.querySelector('i') : null;
        
        if (!mapContainer) return;
        
        // Check if we're entering or exiting fullscreen
        const isEnteringFullscreen = !mapContainer.classList.contains('fullscreen');
        
        if (isEnteringFullscreen) {
            // Enter fullscreen mode
            mapContainer.classList.add('fullscreen');
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
            
            if (fullscreenIcon) {
                fullscreenIcon.classList.remove('fa-expand');
                fullscreenIcon.classList.add('fa-compress');
            }
            
            // Force fullscreen on mobile browsers that support it
            if (mapContainer.requestFullscreen) {
                mapContainer.requestFullscreen().catch(err => {
                    console.error('Error attempting to enable fullscreen:', err);
                });
            } else if (mapContainer.webkitRequestFullscreen) { /* Safari */
                mapContainer.webkitRequestFullscreen();
            } else if (mapContainer.msRequestFullscreen) { /* IE11 */
                mapContainer.msRequestFullscreen();
            }
        } else {
            // Exit fullscreen mode
            mapContainer.classList.remove('fullscreen');
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
            
            if (fullscreenIcon) {
                fullscreenIcon.classList.remove('fa-compress');
                fullscreenIcon.classList.add('fa-expand');
            }
            
            // Exit fullscreen if browser API was used
            if (document.exitFullscreen) {
                document.exitFullscreen();
            } else if (document.webkitExitFullscreen) { /* Safari */
                document.webkitExitFullscreen();
            } else if (document.msExitFullscreen) { /* IE11 */
                document.msExitFullscreen();
            }
        }
        
        // Force a resize and recenter of the map
        this.forceMapResize();
    }
    
    /**
     * Force a map resize and recenter
     */
    forceMapResize() {
        if (window.google && window.google.maps && this.map) {
            // Small delay to ensure the container has resized
            setTimeout(() => {
                // Trigger resize event
                google.maps.event.trigger(this.map, 'resize');
                
                // Recenter the map
                if (this.map.center) {
                    const center = this.map.getCenter();
                    this.map.setCenter(center);
                }
                
                // Additional check for mobile viewport height
                const mapElement = document.getElementById('map');
                if (mapElement) {
                    const vh = window.innerHeight * 0.01;
                    mapElement.style.height = `calc(${100 * vh}px - 0px)`;
                }
            }, 100);
        }
    }

    // Getters للوصول للحالة
    getIsMapLoaded() {
        return this.isMapLoaded;
    }

    getAllMarkers() {
        return this.allMarkers;
    }
}

// تصدير الكلاس
window.MapManager = MapManager;
