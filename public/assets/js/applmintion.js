// Add this before your other scripts
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    }
});

function remove_disabled() {
    // Your existing code
}

function disableForm(flag) {
    // Your existing code
}

function remove_disabled_ckeckbox() {
    // Your existing code
}

function changeAppointmentStatus(appointmentId, status) {
    $.ajax({
        url: '/appointments/update-status',
        method: 'POST',
        data: {
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            appointment_id: appointmentId,
            status: status
        },
        success: function(response) {
            console.log('Status update response:', response);

            if (response.success) {
                // Update the status badge dynamically
                let appointmentCard = $(`.appointment-item[data-appointment-id="${appointmentId}"]`);
                let statusBadge = appointmentCard.find('.status-badge');

                // Update status badge
                statusBadge.removeClass()
                      .addClass(`badge status-badge ${response.status_color}`)
                      .text(response.status_text);

                toastr.success(response.message || 'تم تحديث الحالة بنجاح', 'نجاح');
            } else {
                toastr.error(response.message || 'حدث خطأ غير متوقع', 'خطأ');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating appointment status:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });

            let errorMessage = 'حدث خطأ أثناء تحديث الموعد';

            try {
                let response = JSON.parse(xhr.responseText);
                if (response.errors) {
                    errorMessage = Object.values(response.errors).join(', ');
                } else if (response.message) {
                    errorMessage = response.message;
                }
            } catch(e) {}

            toastr.error(errorMessage, 'خطأ');
        }
    });
}

function confirmDelete(appointmentId) {
    // Your existing code
}

function submitCompletedAppointment(appointmentId) {
    var form = document.getElementById('noteForm' + appointmentId);
    var formData = new FormData(form);

    // Add the appointment ID to the form data
    formData.append('appointment_id', appointmentId);
    formData.append('status', APPOINTMENT_STATUS_COMPLETED);

    $.ajax({
        url: '/appointments/update-status',
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            console.log('Appointment update response:', response);

            if (response.success) {
                // Update the status badge and dropdown item dynamically
                let appointmentCard = $(`.appointment-item[data-appointment-id="${appointmentId}"]`);
                let statusBadge = appointmentCard.find('.status-badge');

                // Update status badge
                statusBadge.removeClass()
                      .addClass(`badge status-badge ${response.status_color}`)
                      .text(response.status_text);

                // Close the modal
                $('#noteModal' + appointmentId).modal('hide');

                toastr.success(response.message, 'نجاح');
            } else {
                toastr.error(response.message || 'حدث خطأ غير متوقع', 'خطأ');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error updating appointment:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });

            let errorMessage = 'حدث خطأ أثناء تحديث الموعد';

            try {
                let response = JSON.parse(xhr.responseText);
                if (response.errors) {
                    errorMessage = Object.values(response.errors).join(', ');
                } else if (response.message) {
                    errorMessage = response.message;
                }
            } catch(e) {}

            toastr.error(errorMessage, 'خطأ');
        }
    });
}

// دالة جلب تفاصيل الموعد الكاملة
function fetchFullAppointmentDetails(appointmentId) {
    $.ajax({
        url: `/appointments/${appointmentId}/full-details`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const details = response.data;

                // تحديث عناصر تبويبة الموعد
                $('#appointment-details-tab .client-name').text(details.client_name);
                $('#appointment-details-tab .employee-name').text(details.employee_name);
                $('#appointment-details-tab .appointment-date').text(details.date);
                $('#appointment-details-tab .appointment-time').text(details.time);
                $('#appointment-details-tab .appointment-duration').text(details.duration);

                // تحديث الحالة
                const statusBadge = $('#appointment-details-tab .appointment-status');
                statusBadge.text(details.status_text);
                statusBadge.removeClass().addClass(`badge ${details.status_color}`);

                // تحديث نوع الإجراء والملاحظات
                $('#appointment-details-tab .action-type').text(details.action_type);
                $('#appointment-details-tab .appointment-notes').text(details.notes);

                // تحديث الإجراءات
                const proceduresList = $('#appointment-details-tab .procedures-list');
                proceduresList.empty();

                details.procedures.forEach(procedure => {
                    proceduresList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${procedure.name}
                            <span class="badge bg-primary rounded-pill">${procedure.price} ر.س</span>
                        </li>
                    `);
                });

                // تحديث إجمالي سعر الإجراءات
                $('#appointment-details-tab .total-procedures-price').text(`${details.total_procedures_price} ر.س`);

                // عرض التبويبة
                $('#appointment-details-tab').tab('show');
            } else {
                toastr.error(response.message || 'حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching appointment details:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });

            toastr.error('حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
        }
    });
}

// دالة عرض تفاصيل الموعد في نافذة منبثقة
function showAppointmentDetailsModal(appointmentId) {
    $.ajax({
        url: `/appointments/${appointmentId}/full-details`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const details = response.data;

                // تحديث عناصر النافذة المنبثقة
                $('#modal-client-name').text(details.client_name);
                $('#modal-employee-name').text(details.employee_name);
                $('#modal-appointment-date').text(details.date);
                $('#modal-appointment-time').text(details.time);
                $('#modal-appointment-duration').text(details.duration);

                // تحديث الحالة
                const statusBadge = $('#modal-appointment-status');
                statusBadge.text(details.status_text);
                statusBadge.removeClass().addClass(`badge ${details.status_color}`);

                // تحديث نوع الإجراء والملاحظات
                $('#modal-action-type').text(details.action_type);
                $('#modal-appointment-notes').text(details.notes);

                // تحديث الإجراءات
                const proceduresList = $('#modal-procedures-list');
                proceduresList.empty();

                details.procedures.forEach(procedure => {
                    proceduresList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${procedure.name}
                            <span class="badge bg-primary rounded-pill">${procedure.price} ر.س</span>
                        </li>
                    `);
                });

                // تحديث إجمالي سعر الإجراءات
                $('#modal-total-procedures-price').text(`${details.total_procedures_price} ر.س`);

                // فتح النافذة المنبثقة
                $('#appointmentDetailsModal').modal('show');
            } else {
                toastr.error(response.message || 'حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching appointment details:', {
                status: status,
                error: error,
                responseText: xhr.responseText
            });

            toastr.error('حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
        }
    });
}

// دالة عرض تفاصيل الموعد
function showAppointmentDetails(appointmentId) {
    $.ajax({
        url: `/appointments/${appointmentId}/full-details`,
        method: 'GET',
        success: function(response) {
            if (response.success) {
                const details = response.data;

                // تحديث عناصر النافذة
                $('#modal-client-name').text(details.client_name);
                $('#modal-employee-name').text(details.employee_name);
                $('#modal-appointment-date').text(details.date);
                $('#modal-appointment-time').text(details.time);

                // تحديث الحالة
                $('#modal-appointment-status').text(details.status_text)
                    .removeClass()
                    .addClass(`badge ${details.status_color}`);

                // تحديث نوع الإجراء والملاحظات
                $('#modal-action-type').text(details.action_type);
                $('#modal-appointment-notes').text(details.notes || 'لا توجد ملاحظات');

                // تحديث الإجراءات
                const proceduresList = $('#modal-procedures-list');
                proceduresList.empty();

                details.procedures.forEach(procedure => {
                    proceduresList.append(`
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            ${procedure.name}
                            <span class="badge bg-primary rounded-pill">${procedure.price} ر.س</span>
                        </li>
                    `);
                });

                // تحديث الإجمالي
                $('#modal-total-price').text(`${details.total_procedures_price} ر.س`);

                // فتح النافذة
                $('#appointmentDetailsModal').modal('show');
            } else {
                toastr.error(response.message || 'حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error fetching appointment details:', error);
            toastr.error('حدث خطأ أثناء جلب تفاصيل الموعد', 'خطأ');
        }
    });
}

// Dropdown positioning fix
document.addEventListener('DOMContentLoaded', function() {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        const dropdownToggle = dropdown.querySelector('.dropdown-toggle');
        const dropdownMenu = dropdown.querySelector('.dropdown-menu');

        dropdownToggle.addEventListener('click', function(e) {
            e.stopPropagation();

            // Close other open dropdowns
            document.querySelectorAll('.dropdown-menu.show').forEach(menu => {
                if (menu !== dropdownMenu) {
                    menu.classList.remove('show');
                }
            });

            dropdownMenu.classList.toggle('show');

            // Positioning logic
            const toggleRect = dropdownToggle.getBoundingClientRect();
            const menuRect = dropdownMenu.getBoundingClientRect();

            dropdownMenu.style.position = 'fixed';
            dropdownMenu.style.top = `${toggleRect.bottom + window.scrollY}px`;
            dropdownMenu.style.left = `${toggleRect.left + window.scrollX}px`;
        });
    });

    // Close dropdowns when clicking outside
    document.addEventListener('click', function(e) {
        const dropdowns = document.querySelectorAll('.dropdown-menu.show');
        dropdowns.forEach(dropdown => {
            if (!dropdown.closest('.dropdown').contains(e.target)) {
                dropdown.classList.remove('show');
            }
        });
    });
});

// Rest of your existing code remains the same
