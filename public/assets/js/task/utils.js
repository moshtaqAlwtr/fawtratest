/**
 * الدوال المساعدة العامة
 */

class Utils {
    static formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        return date.toLocaleDateString('ar-SA', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit'
        });
    }

    static showToast(type, message) {
        const icons = {
            'success': 'success',
            'error': 'error',
            'warning': 'warning',
            'info': 'info'
        };

        Swal.fire({
            icon: icons[type] || 'info',
            title: message,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: type === 'error' ? 4000 : 2500,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });
    }

    static getStatusName(status) {
        const statuses = {
            'not_started': 'لم تبدأ',
            'in_progress': 'قيد التنفيذ',
            'completed': 'مكتملة',
            'overdue': 'متأخرة'
        };
        return statuses[status] || status;
    }

    static getPriorityName(priority) {
        const priorities = {
            'low': 'منخفضة',
            'medium': 'متوسطة',
            'high': 'عالية',
            'urgent': 'عاجلة'
        };
        return priorities[priority] || priority;
    }

    static debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    static showLoading(show = true) {
        if (show) {
            $('body').append('<div class="page-loading-overlay"><div class="spinner-border text-primary"></div></div>');
        } else {
            $('.page-loading-overlay').remove();
        }
    }

    static confirmAction(title, text) {
        return Swal.fire({
            title: title,
            text: text,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'نعم',
            cancelButtonText: 'إلغاء'
        });
    }
}