document.addEventListener("DOMContentLoaded", function () {
    const ctx = document.getElementById("chart").getContext("2d");

    // إنشاء التدرج اللوني
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, "rgba(0, 191, 255, 1)"); // لون بداية التدرج
    gradient.addColorStop(1, "rgba(72, 61, 139, 0.5)"); // لون نهاية التدرج

    // بيانات المخطط (بيانات افتراضية للاختبار)
    const chartData = {
        labels: ["اليوم", "1 - 30 يوم", "31 - 60 يوم", "61 - 90 يوم", "91 - 120 يوم", "+120 يوم"],
        datasets: [
            {
                label: "إجمالي الديون (SAR)",
                data: [1000, 2000, 1500, 3000, 2500, 4000], // بيانات افتراضية
                backgroundColor: gradient,
                borderColor: "#4a00e0",
                borderWidth: 1,
                hoverBackgroundColor: "rgba(0, 191, 255, 0.8)", // لون عند التمرير
                hoverBorderColor: "#4a00e0",
            },
            {
                label: "عدد الفواتير لكل موظف",
                data: [10, 20, 15, 30, 25, 40], // بيانات افتراضية
                backgroundColor: "rgba(255, 99, 132, 0.5)",
                borderColor: "#ff6384",
                borderWidth: 1,
                hoverBackgroundColor: "rgba(255, 99, 132, 0.8)", // لون عند التمرير
                hoverBorderColor: "#ff6384",
            },
        ],
    };

    // خيارات المخطط
    const chartOptions = {
        responsive: true,
        plugins: {
            title: {
                display: true,
                text: "تقرير أعمار الديون (حساب الأستاذ)",
                font: {
                    size: 18,
                    weight: "bold",
                },
            },
            tooltip: {
                enabled: true,
                mode: "index",
                intersect: false,
                backgroundColor: "rgba(0, 0, 0, 0.7)",
                titleFont: {
                    size: 16,
                    weight: "bold",
                },
                bodyFont: {
                    size: 14,
                },
                footerFont: {
                    size: 12,
                },
            },
            legend: {
                display: true,
                position: "top",
                labels: {
                    font: {
                        size: 14,
                    },
                },
            },
        },
        scales: {
            x: {
                grid: {
                    display: true,
                    color: "rgba(0, 0, 0, 0.1)",
                },
                title: {
                    display: true,
                    text: "فترات الأعمار",
                    font: {
                        size: 14,
                        weight: "bold",
                    },
                },
            },
            y: {
                ticks: {
                    callback: function (value) {
                        if (value >= 1000) {
                            return value / 1000 + "K"; // تقسيم على الألف وإضافة K
                        }
                        return value;
                    },
                    font: {
                        size: 14,
                    },
                },
                grid: {
                    display: true,
                    color: "rgba(0, 0, 0, 0.1)",
                },
                title: {
                    display: true,
                    text: "المبلغ (SAR)",
                    font: {
                        size: 14,
                        weight: "bold",
                    },
                },
                beginAtZero: true,
            },
        },
        animation: {
            duration: 1000, // مدة الرسوم المتحركة
            easing: "easeInOutQuad", // نوع الرسوم المتحركة
        },
    };

    // إنشاء المخطط
    const myChart = new Chart(ctx, {
        type: "bar",
        data: chartData,
        options: chartOptions,
    });
});
