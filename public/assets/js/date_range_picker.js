
// دالة لتحديد الفترة المحددة وعرضها في حقل الإدخال
function setDateRange(range) {
    const today = new Date();
    let fromDate, toDate;

    switch (range) {
        case 'الأسبوع الماضي':
            fromDate = new Date(today);
            fromDate.setDate(today.getDate() - 7);
            toDate = new Date(today);
            break;
        case 'الشهر الأخير':
            fromDate = new Date(today);
            fromDate.setMonth(today.getMonth() - 1);
            toDate = new Date(today);
            break;
        case 'من أول الشهر حتى اليوم':
            fromDate = new Date(today.getFullYear(), today.getMonth(), 1);
            toDate = new Date(today);
            break;
        case 'السنة الماضية':
            fromDate = new Date(today.getFullYear() - 1, 0, 1);
            toDate = new Date(today.getFullYear() - 1, 11, 31);
            break;
        case 'من أول السنة حتى اليوم':
            fromDate = new Date(today.getFullYear(), 0, 1);
            toDate = new Date(today);
            break;
        case 'تاريخ محدد':
            fromDate = 'تاريخ محدد';
            toDate = '';
            break;
        case 'كل التواريخ قبل':
            fromDate = 'كل التواريخ قبل';
            toDate = '';
            break;
        case 'كل التواريخ بعد':
            fromDate = 'كل التواريخ بعد';
            toDate = '';
            break;
        default:
            fromDate = '';
            toDate = '';
    }

    // عرض النتيجة في حقل الإدخال
    if (fromDate instanceof Date && toDate instanceof Date) {
        document.getElementById('selectedDateRange').value =
            `من ${formatDate(fromDate)} إلى ${formatDate(toDate)}`;
    } else {
        document.getElementById('selectedDateRange').value = fromDate;
    }
}

// دالة لتحويل التاريخ إلى تنسيق yyyy-mm-dd
function formatDate(date) {
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const day = String(date.getDate()).padStart(2, '0');
    return `${year}-${month}-${day}`;
}
