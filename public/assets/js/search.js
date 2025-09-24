function toggleSearchText(button) {
    const buttonText = button.querySelector('.button-text');
    const advancedFields = document.querySelectorAll('.advanced-field');

    if (buttonText.textContent.trim() === 'متقدم') {
        buttonText.textContent = 'بحث بسيط';
        advancedFields.forEach(field => field.style.display = 'block');
    } else {
        buttonText.textContent = 'متقدم';
        advancedFields.forEach(field => field.style.display = 'none');
    }
}

function toggleSearchFields(button) {
    const searchForm = document.getElementById('searchForm');
    const buttonText = button.querySelector('.hide-button-text');
    const icon = button.querySelector('i');

    if (buttonText.textContent === 'اخفاء') {
        searchForm.style.display = 'none';
        buttonText.textContent = 'اظهار';
        icon.classList.remove('fa-times');
        icon.classList.add('fa-eye');
    } else {
        searchForm.style.display = 'block';
        buttonText.textContent = 'اخفاء';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-times');
    }
}



document.addEventListener('DOMContentLoaded', function () {
    // تنفيذ الانتقال عند النقر على الصف
    document.querySelectorAll('tr[data-href]').forEach(function (row) {
        row.addEventListener('click', function (e) {
            // تأكد من أن النقر لم يكن على زر القائمة المنسدلة
            if (!e.target.closest('.dropdown')) {
                window.location.href = this.dataset.href;
            }
        });
    });

    // فلترة الفواتير
    window.filterInvoices = function (status) {
        // إزالة الفئة النشطة من جميع الأزرار
        document.querySelectorAll('.card-header button').forEach(button => {
            button.classList.remove('active');
        });

        // إضافة الفئة النشطة للزر المحدد
        document.querySelector(`.card-header button[onclick="filterInvoices('${status}')"]`).classList.add('active');

        // تنفيذ الفلترة
        const rows = document.querySelectorAll('#invoiceTableBody tr');
        let visibleRows = 0;

        rows.forEach(row => {
            const invoiceStatus = row.getAttribute('data-status');
            if (status === 'all') {
                row.style.display = '';
                visibleRows++;
            } else if (status === 'late' && invoiceStatus == 3) {
                row.style.display = '';
                visibleRows++;
            } else if (status === 'due' && invoiceStatus == 2) {
                row.style.display = '';
                visibleRows++;
            } else if (status === 'unpaid' && invoiceStatus == 3) {
                row.style.display = '';
                visibleRows++;
            } else if (status === 'draft' && invoiceStatus == 4) {
                row.style.display = '';
                visibleRows++;
            } else if (status === 'overpaid' && invoiceStatus == 0) {
                row.style.display = '';
                visibleRows++;
            } else {
                row.style.display = 'none';
            }
        });

        // عرض رسالة إذا لم توجد فواتير
        const noInvoicesMessage = document.getElementById('noInvoicesMessage');
        if (visibleRows === 0) {
            noInvoicesMessage.style.display = 'block';
        } else {
            noInvoicesMessage.style.display = 'none';
        }
    };
});
