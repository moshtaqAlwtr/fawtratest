// public/assets/js/background-geolocation.js
if ('serviceWorker' in navigator) {
    navigator.serviceWorker.register('/service-worker.js')
        .then((registration) => {
            console.log('Service Worker registered with scope:', registration.scope);
        })
        .catch((error) => {
            console.error('Service Worker registration failed:', error);
        });
}

document.addEventListener('DOMContentLoaded', function () {
    if (!navigator.geolocation) {
        alert("❌ المتصفح لا يدعم ميزة تحديد الموقع الجغرافي.");
        return;
    }

    // متغيرات لتخزين الإحداثيات السابقة
    let previousLatitude = null;
    let previousLongitude = null;

    // طلب الوصول إلى الموقع
    requestLocationAccess();

    function requestLocationAccess() {
        navigator.permissions.query({ name: 'geolocation' }).then(function (result) {
            if (result.state === "granted") {
                // إذا كان الإذن ممنوحًا مسبقًا، ابدأ بمتابعة الموقع
                watchEmployeeLocation();
            } else if (result.state === "prompt") {
                // إذا لم يكن الإذن ممنوحًا، اطلبه من المستخدم
                navigator.geolocation.getCurrentPosition(
                    function () {
                        watchEmployeeLocation();
                    },
                    function (error) {
                        alert("⚠️ يرجى السماح بالوصول إلى الموقع عند ظهور الطلب.");
                        console.error("❌ خطأ في الحصول على الموقع:", error);
                    }
                );
            } else {
                alert("⚠️ الوصول إلى الموقع محظور! يرجى تغييره من إعدادات المتصفح.");
            }
        });
    }

    // دالة لمتابعة تغييرات الموقع
    function watchEmployeeLocation() {
        navigator.geolocation.watchPosition(
            function (position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                console.log("📍 الإحداثيات الجديدة:", latitude, longitude);

                // التحقق من تغيير الموقع
                if (latitude !== previousLatitude || longitude !== previousLongitude) {
                    console.log("🔄 الموقع تغير، يتم التحديث...");

                    // إرسال البيانات إلى السيرفر
                    fetch("{{ route('visits.storeEmployeeLocation') }}", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": "{{ csrf_token() }}"
                        },
                        body: JSON.stringify({ latitude, longitude })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("❌ خطأ في الشبكة");
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log("✅ تم تحديث الموقع بنجاح:", data);
                    })
                    .catch(error => {
                        console.error("❌ خطأ في تحديث الموقع:", error);
                    });

                    // تحديث الإحداثيات السابقة
                    previousLatitude = latitude;
                    previousLongitude = longitude;
                } else {
                    console.log("⏹️ الموقع لم يتغير.");
                }
            },
            function (error) {
                console.error("❌ خطأ في متابعة الموقع:", error);
            },
            {
                enableHighAccuracy: true, // دقة عالية
                timeout: 5000, // انتظار 5 ثواني
                maximumAge: 0 // لا تستخدم بيانات موقع قديمة
            }
        );
    }
});