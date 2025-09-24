/**
 * ملف الوظائف المساعدة
 */

/**
 * الحصول على شارة الحالة
 */
function getStatusBadge(status) {
    const statuses = {
        'new': '<span class="badge badge-info">جديد</span>',
        'in_progress': '<span class="badge badge-warning">قيد التنفيذ</span>',
        'completed': '<span class="badge badge-success">مكتمل</span>',
        'on_hold': '<span class="badge badge-secondary">متوقف</span>'
    };
    return statuses[status] || status;
}

/**
 * الحصول على شارة الأولوية
 */
function getPriorityBadge(priority) {
    const priorities = {
        'low': '<span class="badge badge-light">منخفض</span>',
        'medium': '<span class="badge badge-primary">متوسط</span>',
        'high': '<span class="badge badge-warning">عالي</span>',
        'urgent': '<span class="badge badge-danger">عاجل</span>'
    };
    return priorities[priority] || priority;
}

/**
 * الحصول على إعدادات الحالة
 */
function getStatusConfig(status) {
    const configs = {
        'not_started': {
            color: '#6c757d',
            text: 'لم تبدأ',
            icon: 'fa-clock'
        },
        'in_progress': {
            color: '#ffc107',
            text: 'قيد التنفيذ',
            icon: 'fa-play-circle'
        },
        'completed': {
            color: '#28a745',
            text: 'مكتملة',
            icon: 'fa-check-circle'
        },
        'overdue': {
            color: '#dc3545',
            text: 'متأخرة',
            icon: 'fa-exclamation-circle'
        }
    };
    return configs[status] || configs['not_started'];
}

/**
 * الحصول على إعدادات الأولوية
 */
function getPriorityConfig(priority) {
    const configs = {
        'low': {
            color: '#28a745',
            text: 'منخفضة',
            icon: 'fa-arrow-down'
        },
        'medium': {
            color: '#17a2b8',
            text: 'متوسطة',
            icon: 'fa-minus'
        },
        'high': {
            color: '#ffc107',
            text: 'عالية',
            icon: 'fa-arrow-up'
        },
        'urgent': {
            color: '#dc3545',
            text: 'عاجلة',
            icon: 'fa-exclamation'
        }
    };
    return configs[priority] || configs['medium'];
}

/**
 * تنسيق المبالغ المالية
 */
function formatMoney(amount) {
    return new Intl.NumberFormat('ar-SA', {
        style: 'currency',
        currency: 'SAR',
        minimumFractionDigits: 0
    }).format(amount || 0);
}

/**
 * تنسيق التاريخ
 */
function formatDate(date) {
    if (!date) return '';
    return new Date(date).toLocaleDateString('ar-SA');
}

/**
 * تأمين النصوص من HTML
 */
function escapeHtml(text) {
    if (!text) return '';
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return String(text).replace(/[&<>"']/g, m => map[m]);
}
