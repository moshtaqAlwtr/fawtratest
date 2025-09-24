// self.addEventListener('install', (event) => {
//     console.log('⚡ Service Worker تم تثبيته');
//     self.skipWaiting();
// });

// self.addEventListener('activate', (event) => {
//     console.log('✅ Service Worker مفعل');
//     self.clients.claim();
// });

// // تسجيل حدث Background Sync
// self.addEventListener('sync', (event) => {
//     if (event.tag === 'update-location') {
//         console.log('🔄 Background Sync: محاولة تحديث الموقع...');
//         event.waitUntil(updateLocation());
//     }
// });

// async function updateLocation() {
//     try {
//         const clients = await self.clients.matchAll({ includeUncontrolled: true });

//         if (clients.length === 0) {
//             console.warn('⚠️ لا يوجد عملاء نشطين للحصول على الموقع.');
//             return;
//         }

//         // إرسال طلب للصفحة المفتوحة للحصول على الموقع
//         clients[0].postMessage({ action: 'getLocation' });

//     } catch (error) {
//         console.error('❌ خطأ في تحديث الموقع في الخلفية:', error);
//     }
// }
let previousLatitude = null;
let previousLongitude = null;
const minDistance = 10; // الحد الأدنى للمسافة قبل إرسال التحديث

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // نصف قطر الأرض بالأمتار
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat / 2) * Math.sin(dLat / 2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon / 2) * Math.sin(dLon / 2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
    return R * c;
}

function sendLocation(latitude, longitude) {
    fetch('/store-location', {
        method: 'POST',
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "PUT_CSRF_TOKEN_HERE"
        },
        body: JSON.stringify({ latitude, longitude })
    }).then(response => response.json())
      .then(data => console.log("✅ تم تحديث الموقع:", data))
      .catch(error => console.error("❌ خطأ في تحديث الموقع:", error));
}

function trackLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.watchPosition(position => {
            const { latitude, longitude } = position.coords;
            if (!previousLatitude || !previousLongitude ||
                calculateDistance(previousLatitude, previousLongitude, latitude, longitude) >= minDistance) {
                sendLocation(latitude, longitude);
                previousLatitude = latitude;
                previousLongitude = longitude;
            }
        }, error => console.error("❌ خطأ في تحديد الموقع:", error), {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 5000
        });
    } else {
        console.error("❌ المتصفح لا يدعم تحديد الموقع.");
    }
}

trackLocation();
