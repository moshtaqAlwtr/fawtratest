{{-- Modal for Add Note --}}
<div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog" aria-labelledby="addNoteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h4 class="modal-title text-white" id="addNoteModalLabel">
                    <i class="fas fa-sticky-note me-2"></i>إضافة ملاحظة جديدة
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addNoteForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="process" class="form-label">نوع الإجراء <span class="text-danger">*</span></label>
                            <select id="process" name="process" class="form-control" required>
                                <option value="">اختر نوع الإجراء</option>
                                <option value="بداية التصنيع">بداية التصنيع</option>
                                <option value="استلام المواد">استلام المواد</option>
                                <option value="مرحلة الإنتاج">مرحلة الإنتاج</option>
                                <option value="فحص الجودة">فحص الجودة</option>
                                <option value="انتهاء التصنيع">انتهاء التصنيع</option>
                                <option value="مشكلة في الإنتاج">مشكلة في الإنتاج</option>
                                <option value="تأخير في التسليم">تأخير في التسليم</option>
                                <option value="تحديث الكمية">تحديث الكمية</option>
                                <option value="ملاحظة عامة">ملاحظة عامة</option>
                                <option value="أخرى">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="noteDate" class="form-label">التاريخ <span class="text-danger">*</span></label>
                            <input type="date" name="date" id="noteDate" class="form-control" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="noteTime" class="form-label">الوقت <span class="text-danger">*</span></label>
                            <input type="time" name="time" id="noteTime" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="attachment" class="form-label">مرفق (اختياري)</label>
                            <input type="file" name="attachment" id="attachment" class="form-control"
                                   accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt">
                            <small class="text-muted">الملفات المدعومة: PDF, DOC, DOCX, JPG, PNG, TXT</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="note" class="form-label">تفاصيل الملاحظة <span class="text-danger">*</span></label>
                        <textarea name="description" id="note" class="form-control" rows="4"
                                  placeholder="اكتب تفاصيل الملاحظة هنا..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times me-1"></i>إلغاء
                    </button>
                    <button type="submit" class="btn btn-info">
                        <i class="fas fa-save me-1"></i>حفظ الملاحظة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Modal for View All Notes --}}
<div class="modal fade" id="viewNotesModal" tabindex="-1" role="dialog" aria-labelledby="viewNotesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <h4 class="modal-title text-white" id="viewNotesModalLabel">
                    <i class="fas fa-list me-2"></i>جميع الملاحظات
                </h4>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {{-- Loading State --}}
                <div id="notesLoading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="sr-only">جاري التحميل...</span>
                    </div>
                    <p class="mt-2">جاري تحميل الملاحظات...</p>
                </div>

                {{-- Notes Content --}}
                <div id="notesContent" style="max-height: 600px; overflow-y: auto;">
                    <!-- سيتم تحميل الملاحظات هنا عبر JavaScript -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success btn-sm" onclick="openAddNoteModal()">
                    <i class="fas fa-plus me-1"></i>إضافة ملاحظة جديدة
                </button>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times me-1"></i>إغلاق
                </button>
            </div>
        </div>
    </div>
</div>


<script>
    // وظائف إدارة الملاحظات لأوامر التصنيع

// فتح نافذة إضافة ملاحظة
function addNoteOrAttachment() {
    // تعيين التاريخ والوقت الحالي
    const now = new Date();
    const currentDate = now.toISOString().split('T')[0];
    const currentTime = now.toTimeString().split(' ')[0].substring(0, 5);

    document.getElementById('noteDate').value = currentDate;
    document.getElementById('noteTime').value = currentTime;

    // فتح النافذة
    $('#addNoteModal').modal('show');
}

// فتح نافذة إضافة ملاحظة من داخل نافذة عرض الملاحظات
function openAddNoteModal() {
    $('#viewNotesModal').modal('hide');
    setTimeout(() => {
        addNoteOrAttachment();
    }, 300);
}

// حفظ الملاحظة
function saveNoteToDatabase(formData, orderId) {
    return fetch(`/manufacturing/orders/${orderId}/add-note`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    });
}

// عرض جميع الملاحظات
function viewAllNotes() {
    $('#viewNotesModal').modal('show');
    $('#notesLoading').show();
    $('#notesContent').html('');

    const orderId = window.manufacturingOrderId; // يجب تعيين هذا المتغير في الصفحة الرئيسية

    fetch(`/manufacturing/orders/${orderId}/get-notes`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            $('#notesLoading').hide();

            if (data.success) {
                let notesHtml = '';

                if (data.notes && data.notes.length > 0) {
                    data.notes.forEach((note, index) => {
                        notesHtml += createNoteCard(note, index);
                    });
                } else {
                    notesHtml = createEmptyNotesMessage();
                }

                $('#notesContent').html(notesHtml);
            } else {
                $('#notesContent').html(`
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        <p class="mb-0">${data.message || 'حدث خطأ في تحميل الملاحظات'}</p>
                    </div>
                `);
            }
        })
        .catch(error => {
            $('#notesLoading').hide();
            console.error('خطأ في جلب الملاحظات:', error);
            $('#notesContent').html(`
                <div class="alert alert-danger text-center">
                    <i class="fas fa-wifi text-danger"></i>
                    <h5>خطأ في الاتصال</h5>
                    <p class="mb-0">تعذر تحميل الملاحظات. يرجى التحقق من الاتصال والمحاولة مرة أخرى.</p>
                </div>
            `);
        });
}

// إنشاء بطاقة ملاحظة
function createNoteCard(note, index) {
    const processName = note.process || 'غير محدد';
    const employeeName = note.employee_name || 'غير محدد';
    const noteDate = note.date || 'غير محدد';
    const noteTime = note.time || 'غير محدد';
    const description = note.description || 'لا يوجد وصف';

    return `
        <div class="card mb-3 border-start border-primary border-3" id="note-${note.id}">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="badge bg-primary me-2">${index + 1}</span>
                            <h6 class="mb-0 text-primary fw-bold">${processName}</h6>
                        </div>
                        <small class="text-muted d-block">
                            <i class="fas fa-user me-1 text-info"></i><strong>الموظف:</strong> ${employeeName}
                        </small>
                        <small class="text-muted d-block">
                            <i class="fas fa-calendar me-1 text-success"></i><strong>التاريخ:</strong> ${noteDate}
                            <i class="fas fa-clock me-2 ms-3 text-warning"></i><strong>الوقت:</strong> ${noteTime}
                        </small>
                    </div>
                    <button class="btn btn-outline-danger btn-sm" onclick="deleteNote(${note.id})"
                            title="حذف الملاحظة">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>

                <div class="mt-3 p-3 bg-light rounded">
                    <p class="mb-0 text-dark">${description}</p>
                </div>

                ${note.has_attachment ? `
                    <div class="mt-3 pt-2 border-top">
                        <a href="${note.attachment_url}" target="_blank"
                           class="btn btn-outline-info btn-sm">
                            <i class="fas fa-paperclip me-1"></i>عرض المرفق
                        </a>
                    </div>
                ` : ''}

                ${note.created_at ? `
                    <div class="mt-2 pt-2 border-top">
                        <small class="text-muted">
                            <i class="fas fa-clock me-1"></i>تم الإنشاء: ${note.created_at}
                        </small>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
}

// إنشاء رسالة عدم وجود ملاحظات
function createEmptyNotesMessage() {
    return `
        <div class="alert alert-info text-center py-4">
            <i class="fas fa-info-circle fs-1 text-info mb-3"></i>
            <h5 class="mb-2">لا توجد ملاحظات حتى الآن</h5>
            <p class="mb-0 text-muted">يمكنك إضافة ملاحظة جديدة من خلال زر "إضافة ملاحظة جديدة"</p>
        </div>
    `;
}

// حذف ملاحظة
function deleteNote(noteId) {
    if (!confirm('هل أنت متأكد من حذف هذه الملاحظة؟')) {
        return;
    }

    fetch(`/manufacturing/orders/notes/${noteId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // إزالة البطاقة من الواجهة
            document.getElementById(`note-${noteId}`).remove();

            // التحقق من وجود ملاحظات أخرى
            const remainingNotes = document.querySelectorAll('[id^="note-"]');
            if (remainingNotes.length === 0) {
                document.getElementById('notesContent').innerHTML = createEmptyNotesMessage();
            }

            showNotification('تم حذف الملاحظة بنجاح', 'success');
        } else {
            showNotification(data.message || 'حدث خطأ أثناء حذف الملاحظة', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('حدث خطأ أثناء حذف الملاحظة', 'error');
    });
}

// عرض إشعار
function showNotification(message, type = 'info') {
    const alertClass = type === 'success' ? 'alert-success' :
                      type === 'error' ? 'alert-danger' : 'alert-info';

    const notification = $(`
        <div class="alert ${alertClass} alert-dismissible fade show position-fixed" style="top: 20px; right: 20px; z-index: 9999; max-width: 400px;">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            ${message}
        </div>
    `);

    $('body').append(notification);

    // إزالة الإشعار تلقائياً بعد 5 ثواني
    setTimeout(() => {
        notification.alert('close');
    }, 5000);
}

// تهيئة أحداث النموذج
$(document).ready(function() {
    // معالجة إرسال نموذج إضافة الملاحظة
    $('#addNoteForm').on('submit', function(e) {
        e.preventDefault();

        const formData = new FormData(this);
        const orderId = window.manufacturingOrderId;

        // إظهار رسالة تحميل
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>جاري الحفظ...').prop('disabled', true);

        saveNoteToDatabase(formData, orderId)
            .then(data => {
                if (data.success) {
                    // إغلاق النافذة
                    $('#addNoteModal').modal('hide');

                    // إعادة تعيين النموذج
                    this.reset();

                    // إظهار رسالة نجاح
                    showNotification('تم إضافة الملاحظة بنجاح', 'success');

                    // تحديث عدد الملاحظات في التبويب إذا كان موجوداً
                    updateNotesCount();

                } else {
                    showNotification(data.message || 'حدث خطأ أثناء حفظ الملاحظة', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showNotification('حدث خطأ أثناء الاتصال بالخادم', 'error');
            })
            .finally(() => {
                // إعادة تفعيل الزر
                submitBtn.html(originalText).prop('disabled', false);
            });
    });

    // تنظيف النموذج عند إغلاق النافذة
    $('#addNoteModal').on('hidden.bs.modal', function() {
        $('#addNoteForm')[0].reset();
    });
});

// تحديث عدد الملاحظات
function updateNotesCount() {
    const orderId = window.manufacturingOrderId;

    fetch(`/manufacturing/orders/${orderId}/notes-count`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // تحديث عدد الملاحظات في الواجهة
                const notesTab = document.querySelector('#activities-tab');
                if (notesTab) {
                    const currentText = notesTab.textContent;
                    const newText = currentText.replace(/\(\d+\)/, `(${data.count})`);
                    if (!currentText.includes('(')) {
                        notesTab.textContent = `${currentText} (${data.count})`;
                    } else {
                        notesTab.textContent = newText;
                    }
                }
            }
        })
        .catch(error => console.error('Error updating notes count:', error));
}

// تصدير الدوال للاستخدام العام
window.addNoteOrAttachment = addNoteOrAttachment;
window.viewAllNotes = viewAllNotes;
window.deleteNote = deleteNote;
window.openAddNoteModal = openAddNoteModal;
</script>