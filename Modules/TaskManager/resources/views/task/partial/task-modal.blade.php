<!-- Modal إضافة/تعديل المهمة المحسن -->
<div class="modal fade" id="taskModal" tabindex="-1" role="dialog" aria-labelledby="taskModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content border-0 shadow-lg">
            <!-- رأس النافذة المحسن -->
            <div class="modal-header bg-gradient-primary text-white border-0 position-relative overflow-hidden">
                <div class="modal-header-content d-flex align-items-center">
                    <div class="modal-icon me-3">
                        <i class="feather icon-plus" id="taskModalIcon" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h4 class="modal-title mb-0" id="taskModalTitle">إضافة مهمة جديدة</h4>
                        <p class="mb-0 opacity-75" style="font-size: 14px;">املأ البيانات التالية لإنشاء مهمة جديدة</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>

                <!-- تأثير بصري -->
                <div class="header-decoration position-absolute"
                     style="top: -50%; right: -10%; width: 200px; height: 200px;
                            background: rgba(255,255,255,0.1); border-radius: 50%;
                            transform: rotate(45deg);"></div>
            </div>

            <!-- محتوى النافذة -->
            <div class="modal-body p-0" id="taskModalBody">
                <!-- شريط التقدم -->
                <div class="progress" style="height: 4px; border-radius: 0;">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: 33%" id="formProgress"></div>
                </div>

                <!-- نموذج المهمة -->
                <form id="taskForm" enctype="multipart/form-data" class="p-4">
                    @csrf
                    <input type="hidden" name="id" id="task_id">

                    <!-- تنبيهات الأخطاء -->
                    <div class="alert alert-danger d-none" id="formErrors"></div>

                    <!-- خطوات النموذج -->
                    <div class="form-steps">
                        <!-- الخطوة الأولى: المعلومات الأساسية -->
                        <div class="step-content active" data-step="1">
                            <div class="step-header mb-4">
                                <h5 class="text-primary mb-1">
                                    <i class="feather icon-info me-2"></i>
                                    المعلومات الأساسية
                                </h5>
                                <p class="text-muted mb-0">أدخل التفاصيل الأساسية للمهمة</p>
                            </div>

                            <div class="row">
                                <!-- اختيار المشروع -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-folder text-primary"></i>
                                        المشروع <span class="text-danger">*</span>
                                    </label>
                                    <select name="project_id" id="project_id" class="form-select select2" required>
                                        <option value="">اختر المشروع</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" data-color="{{ $project->color ?? '#7367f0' }}">
                                                {{ $project->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="form-text">اختر المشروع الذي تنتمي إليه هذه المهمة</div>
                                </div>

                                <!-- المهمة الرئيسية -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-git-branch text-info"></i>
                                        المهمة الرئيسية
                                    </label>
                                    <select name="parent_task_id" id="parent_task_id" class="form-select select2">
                                        <option value="">لا يوجد (مهمة رئيسية)</option>
                                    </select>
                                    <div class="form-text">اختر المهمة الرئيسية إذا كانت هذه مهمة فرعية</div>
                                </div>
                            </div>

                            <!-- عنوان المهمة -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="feather icon-edit text-success"></i>
                                    عنوان المهمة <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg"
                                       placeholder="أدخل عنوان واضح وموجز للمهمة" required maxlength="255">
                                <div class="form-text">
                                    <span id="titleCounter">0</span>/255 حرف
                                </div>
                            </div>

                            <!-- الوصف -->
                            <div class="mb-3">
                                <label class="form-label fw-bold">
                                    <i class="feather icon-file-text text-warning"></i>
                                    وصف المهمة
                                </label>
                                <textarea name="description" id="description" class="form-control"
                                          rows="4" placeholder="اكتب وصفاً تفصيلياً للمهمة..."></textarea>
                                <div class="form-text">اشرح متطلبات وأهداف المهمة بالتفصيل</div>
                            </div>
                        </div>

                        <!-- الخطوة الثانية: التفاصيل والجدولة -->
                        <div class="step-content" data-step="2">
                            <div class="step-header mb-4">
                                <h5 class="text-primary mb-1">
                                    <i class="feather icon-calendar me-2"></i>
                                    التفاصيل والجدولة
                                </h5>
                                <p class="text-muted mb-0">حدد الحالة والأولوية والتواريخ</p>
                            </div>

                            <div class="row">
                                <!-- الحالة -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-flag text-primary"></i>
                                        حالة المهمة <span class="text-danger">*</span>
                                    </label>
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="not_started">لم تبدأ</option>
                                        <option value="in_progress">قيد التنفيذ</option>
                                        <option value="completed">مكتملة</option>
                                        <option value="overdue">متأخرة</option>
                                    </select>
                                </div>

                                <!-- الأولوية -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-alert-triangle text-danger"></i>
                                        مستوى الأولوية <span class="text-danger">*</span>
                                    </label>
                                    <select name="priority" id="priority" class="form-select" required>
                                        <option value="low" data-color="#28c76f">منخفضة</option>
                                        <option value="medium" data-color="#ff9f43">متوسطة</option>
                                        <option value="high" data-color="#ea5455">عالية</option>
                                        <option value="urgent" data-color="#e83e8c">عاجلة</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <!-- تاريخ البدء -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-play-circle text-success"></i>
                                        تاريخ البدء
                                    </label>
                                    <input type="date" name="start_date" id="start_date" class="form-control">
                                    <div class="form-text">التاريخ المتوقع لبدء العمل في المهمة</div>
                                </div>

                                <!-- تاريخ الانتهاء -->
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-stop-circle text-danger"></i>
                                        تاريخ الانتهاء
                                    </label>
                                    <input type="date" name="due_date" id="due_date" class="form-control">
                                    <div class="form-text">الموعد النهائي المطلوب لإنجاز المهمة</div>
                                </div>
                            </div>

                            <div class="row">
                                <!-- الميزانية -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-dollar-sign text-success"></i>
                                        الميزانية المقدرة
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="budget" id="budget" class="form-control"
                                               step="0.01" placeholder="0.00">
                                        <span class="input-group-text">ر.س</span>
                                    </div>
                                </div>

                                <!-- الساعات المقدرة -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-clock text-info"></i>
                                        الساعات المقدرة
                                    </label>
                                    <div class="input-group">
                                        <input type="number" name="estimated_hours" id="estimated_hours"
                                               class="form-control" step="0.5" placeholder="0">
                                        <span class="input-group-text">ساعة</span>
                                    </div>
                                </div>

                                <!-- نسبة الإنجاز -->
                                <div class="col-md-4 mb-3">
                                    <label class="form-label fw-bold">
                                        <i class="feather icon-trending-up text-primary"></i>
                                        نسبة الإنجاز
                                    </label>
                                    <div class="progress-input-container">
                                        <input type="range" name="completion_percentage" id="completion_percentage"
                                               class="form-range" min="0" max="100" value="0"
                                               oninput="updateProgressDisplay(this.value)">
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="text-muted">0%</span>
                                            <span class="badge bg-primary" id="progressDisplay">0%</span>
                                            <span class="text-muted">100%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- الخطوة الثالثة: التعيين والمرفقات -->
                        <div class="step-content" data-step="3">
                            <div class="step-header mb-4">
                                <h5 class="text-primary mb-1">
                                    <i class="feather icon-users me-2"></i>
                                    التعيين والمرفقات
                                </h5>
                                <p class="text-muted mb-0">اختر المستخدمين وأرفق الملفات اللازمة</p>
                            </div>

                            <!-- تعيين المستخدمين -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="feather icon-user-plus text-primary"></i>
                                    تعيين المستخدمين
                                </label>
                                <select name="assigned_users[]" id="assigned_users" class="form-select select2" multiple>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}" data-avatar="{{ $user->avatar ?? '/default-avatar.png' }}">
                                            {{ $user->name }} - {{ $user->email }}
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">اختر المستخدمين المسؤولين عن تنفيذ هذه المهمة</div>

                                <!-- عرض المستخدمين المختارين -->
                                <div id="selectedUsers" class="mt-3"></div>
                            </div>

                            <!-- إرفاق الملفات -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    <i class="feather icon-paperclip text-info"></i>
                                    إرفاق ملفات
                                </label>
                                <div class="file-upload-area border border-dashed border-2 rounded p-4 text-center"
                                     style="border-color: #ddd !important; background: #f8f9fa;">
                                    <input type="file" name="files[]" id="files" class="d-none" multiple accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.gif">
                                    <div class="upload-content">
                                        <i class="feather icon-upload text-muted mb-2" style="font-size: 48px;"></i>
                                        <h6 class="text-muted">اسحب الملفات هنا أو انقر للاختيار</h6>
                                        <p class="text-muted small mb-0">الحد الأقصى: 10 ميجابايت لكل ملف</p>
                                        <button type="button" class="btn btn-outline-primary btn-sm mt-2" onclick="$('#files').click()">
                                            اختيار ملفات
                                        </button>
                                    </div>
                                </div>

                                <!-- قائمة الملفات المختارة -->
                                <div id="selectedFiles" class="mt-3"></div>

                                <!-- الملفات الموجودة (في حالة التعديل) -->
                                <div id="existingFiles" class="mt-3"></div>
                            </div>

                            <!-- إعدادات إضافية -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="sendNotifications" name="send_notifications" checked>
                                        <label class="form-check-label fw-bold" for="sendNotifications">
                                            <i class="feather icon-bell text-warning"></i>
                                            إرسال إشعارات للمستخدمين المعينين
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="isUrgent" name="is_urgent">
                                        <label class="form-check-label fw-bold" for="isUrgent">
                                            <i class="feather icon-zap text-danger"></i>
                                            مهمة عاجلة
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- أزرار التنقل بين الخطوات -->
                    <div class="step-navigation d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary" id="prevStep" onclick="previousStep()" disabled>
                            <i class="feather icon-chevron-right me-2"></i>السابق
                        </button>

                        <div class="step-indicators d-flex gap-2">
                            <span class="step-indicator active" data-step="1"></span>
                            <span class="step-indicator" data-step="2"></span>
                            <span class="step-indicator" data-step="3"></span>
                        </div>

                        <button type="button" class="btn btn-primary" id="nextStep" onclick="nextStep()">
                            التالي<i class="feather icon-chevron-left ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>

            <!-- ذيل النافذة -->
            <div class="modal-footer bg-light border-0 d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="feather icon-x me-2"></i>إلغاء
                </button>
                <div class="action-buttons">
                    <button type="button" class="btn btn-outline-primary me-2" onclick="saveAsDraft()">
                        <i class="feather icon-save me-2"></i>حفظ كمسودة
                    </button>
                    <button type="submit" form="taskForm" class="btn btn-primary" id="saveTaskBtn">
                        <i class="feather icon-check me-2"></i>حفظ المهمة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* تنسيقات النافذة المنبثقة */
.modal-xl {
    max-width: 1200px;
}

.modal-content {
    border-radius: 15px;
    overflow: hidden;
}

.modal-header.bg-gradient-primary {
    background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%) !important;
    position: relative;
}

.modal-header-content {
    position: relative;
    z-index: 2;
}

.header-decoration {
    opacity: 0.3;
}

/* تنسيقات خطوات النموذج */
.step-content {
    display: none;
    animation: fadeInUp 0.5s ease;
}

.step-content.active {
    display: block;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.step-header {
    border-bottom: 2px solid #f0f0f0;
    padding-bottom: 15px;
}

.step-indicators {
    display: flex;
    gap: 8px;
}

.step-indicator {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #e0e0e0;
    transition: all 0.3s ease;
    cursor: pointer;
}

.step-indicator.active {
    background: #7367f0;
    transform: scale(1.2);
}

.step-indicator.completed {
    background: #28c76f;
}

/* تحسينات الحقول */
.form-label {
    display: flex;
    align-items: center;
    margin-bottom: 8px;
}

.form-label i {
    margin-left: 8px;
    font-size: 16px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #ddd;
    padding: 12px 16px;
    transition: all 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #7367f0;
    box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.1);
}

.form-control-lg {
    padding: 16px 20px;
    font-size: 16px;
}

/* تنسيقات رفع الملفات */
.file-upload-area {
    transition: all 0.3s ease;
    cursor: pointer;
}

.file-upload-area:hover {
    border-color: #7367f0 !important;
    background: #f0f0ff !important;
}

.file-upload-area.drag-over {
    border-color: #28c76f !important;
    background: #f0fff0 !important;
}

.selected-file-item {
    background: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 12px;
    margin-bottom: 8px;
    display: flex;
    justify-content: between;
    align-items: center;
}

.file-info {
    display: flex;
    align-items: center;
    flex-grow: 1;
}

.file-icon {
    width: 40px;
    height: 40px;
    background: #7367f0;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    margin-left: 12px;
}

/* تنسيقات المستخدمين المختارين */
.selected-user-item {
    display: inline-flex;
    align-items: center;
    background: #f0f0ff;
    border: 1px solid #7367f0;
    border-radius: 20px;
    padding: 6px 12px;
    margin: 4px;
    font-size: 14px;
}

.user-avatar {
    width: 24px;
    height: 24px;
    border-radius: 50%;
    margin-left: 8px;
}

/* تحسينات شريط التقدم */
.form-range {
    height: 8px;
}

.form-range::-webkit-slider-thumb {
    background: #7367f0;
    border: 2px solid white;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* تأثيرات الأزرار */
.btn {
    border-radius: 8px;
    padding: 10px 20px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.btn-primary {
    background: linear-gradient(135deg, #7367f0 0%, #9c88ff 100%);
    border: none;
}

.btn-outline-primary {
    border-color: #7367f0;
    color: #7367f0;
}

.btn-outline-primary:hover {
    background: #7367f0;
    border-color: #7367f0;
}

/* تحسينات Select2 */
.select2-container--default .select2-selection--multiple {
    border-radius: 8px;
    border: 1px solid #ddd;
    min-height: 45px;
    padding: 4px 8px;
}

.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: #7367f0;
    border: none;
    border-radius: 15px;
    color: white;
    padding: 4px 12px;
    margin: 2px;
}

/* تحسينات responsive */
@media (max-width: 768px) {
    .modal-xl {
        max-width: 95%;
        margin: 10px auto;
    }

    .modal-body {
        padding: 20px 15px;
    }

    .step-navigation {
        flex-direction: column;
        gap: 15px;
    }

    .step-indicators {
        order: -1;
    }
}
</style>

<script>
let currentStep = 1;
const totalSteps = 3;

$(document).ready(function() {
    // تفعيل Select2 المحسن
    initializeSelect2();

    // تفعيل رفع الملفات بالسحب والإفلات
    initializeDragAndDrop();

    // مراقبة تغييرات الحقول
    initializeFieldWatchers();

    // حفظ المهمة عند إرسال النموذج
    $('#taskForm').on('submit', function(e) {
        e.preventDefault();
        saveTask();
    });
});

// تفعيل Select2 مع تحسينات
function initializeSelect2() {
    $('#project_id, #parent_task_id').select2({
        dropdownParent: $('#taskModal'),
        dir: 'rtl',
        theme: 'default',
        width: '100%'
    });

    $('#assigned_users').select2({
        dropdownParent: $('#taskModal'),
        dir: 'rtl',
        theme: 'default',
        width: '100%',
        templateResult: formatUser,
        templateSelection: formatUserSelection
    });
}

// تنسيق عرض المستخدمين في القائمة
function formatUser(user) {
    if (!user.id) return user.text;

    const avatar = $(user.element).data('avatar') || '/default-avatar.png';
    return $(`
        <div class="d-flex align-items-center">
            <img src="${avatar}" class="rounded-circle me-2" style="width: 32px; height: 32px; object-fit: cover;">
            <div>
                <div>${user.text.split(' - ')[0]}</div>
                <small class="text-muted">${user.text.split(' - ')[1]}</small>
            </div>
        </div>
    `);
}

// تنسيق عرض المستخدمين المختارين
function formatUserSelection(user) {
    return user.text.split(' - ')[0];
}

// تفعيل السحب والإفلات للملفات
function initializeDragAndDrop() {
    const uploadArea = $('.file-upload-area');

    uploadArea.on('click', function() {
        $('#files').click();
    });

    uploadArea.on('dragover', function(e) {
        e.preventDefault();
        $(this).addClass('drag-over');
    });

    uploadArea.on('dragleave', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');
    });

    uploadArea.on('drop', function(e) {
        e.preventDefault();
        $(this).removeClass('drag-over');

        const files = e.originalEvent.dataTransfer.files;
        handleFileSelection(files);
    });

    $('#files').on('change', function() {
        handleFileSelection(this.files);
    });
}

// معالجة اختيار الملفات
function handleFileSelection(files) {
    const selectedFilesContainer = $('#selectedFiles');
    selectedFilesContainer.empty();

    Array.from(files).forEach((file, index) => {
        if (file.size > 10 * 1024 * 1024) { // 10MB
            showMiniToast(`الملف ${file.name} كبير جداً (أكثر من 10 ميجابايت)`, 'error');
            return;
        }

        const fileItem = createFileItem(file, index);
        selectedFilesContainer.append(fileItem);
    });

    updateFormProgress();
}

// إنشاء عنصر الملف
function createFileItem(file, index) {
    const fileExtension = file.name.split('.').pop().toLowerCase();
    const fileIcon = getFileIcon(fileExtension);
    const fileSize = formatFileSize(file.size);

    return $(`
        <div class="selected-file-item">
            <div class="file-info">
                <div class="file-icon">
                    <i class="feather icon-${fileIcon}"></i>
                </div>
                <div>
                    <div class="fw-bold">${file.name}</div>
                    <small class="text-muted">${fileSize}</small>
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                <i class="feather icon-x"></i>
            </button>
        </div>
    `);
}

// الحصول على أيقونة الملف
function getFileIcon(extension) {
    const icons = {
        'pdf': 'file-text',
        'doc': 'file-text',
        'docx': 'file-text',
        'xls': 'grid',
        'xlsx': 'grid',
        'jpg': 'image',
        'jpeg': 'image',
        'png': 'image',
        'gif': 'image'
    };
    return icons[extension] || 'file';
}

// تنسيق حجم الملف
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// إزالة ملف
function removeFile(index) {
    const filesInput = document.getElementById('files');
    const dt = new DataTransfer();

    Array.from(filesInput.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });

    filesInput.files = dt.files;
    handleFileSelection(filesInput.files);
}

// مراقبة تغييرات الحقول
function initializeFieldWatchers() {
    // عداد الأحرف للعنوان
    $('#title').on('input', function() {
        const length = $(this).val().length;
        $('#titleCounter').text(length);

        if (length > 200) {
            $('#titleCounter').addClass('text-danger');
        } else {
            $('#titleCounter').removeClass('text-danger');
        }
    });

    // تحديث عرض النسبة
    $('#completion_percentage').on('input', function() {
        updateProgressDisplay(this.value);
    });

    // تحديث المستخدمين المختارين
    $('#assigned_users').on('change', function() {
        updateSelectedUsersDisplay();
    });

    // مراقبة تغييرات المشروع لتحديث المهام الرئيسية
    $('#project_id').on('change', function() {
        updateParentTasks($(this).val());
    });
}

// تحديث عرض نسبة الإنجاز
function updateProgressDisplay(value) {
    const badge = $('#progressDisplay');
    badge.text(value + '%');

    // تغيير اللون حسب النسبة
    badge.removeClass('bg-danger bg-warning bg-success bg-primary');
    if (value < 25) {
        badge.addClass('bg-danger');
    } else if (value < 50) {
        badge.addClass('bg-warning');
    } else if (value < 75) {
        badge.addClass('bg-primary');
    } else {
        badge.addClass('bg-success');
    }
}

// تحديث عرض المستخدمين المختارين
function updateSelectedUsersDisplay() {
    const selectedContainer = $('#selectedUsers');
    selectedContainer.empty();

    $('#assigned_users option:selected').each(function() {
        const userId = $(this).val();
        const userName = $(this).text().split(' - ')[0];
        const userAvatar = $(this).data('avatar') || '/default-avatar.png';

        const userItem = $(`
            <span class="selected-user-item">
                <img src="${userAvatar}" class="user-avatar" alt="${userName}">
                ${userName}
                <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="removeUser('${userId}')">
                    <i class="feather icon-x" style="font-size: 12px;"></i>
                </button>
            </span>
        `);

        selectedContainer.append(userItem);
    });
}

// إزالة مستخدم
function removeUser(userId) {
    const select = $('#assigned_users');
    const values = select.val() || [];
    const newValues = values.filter(id => id !== userId);
    select.val(newValues).trigger('change');
}

// تحديث المهام الرئيسية حسب المشروع
function updateParentTasks(projectId) {
    if (!projectId) {
        $('#parent_task_id').empty().append('<option value="">لا يوجد (مهمة رئيسية)</option>');
        return;
    }

    $.get(`/projects/${projectId}/tasks`, function(response) {
        const select = $('#parent_task_id');
        select.empty().append('<option value="">لا يوجد (مهمة رئيسية)</option>');

        if (response.success && response.tasks.length > 0) {
            response.tasks.forEach(task => {
                select.append(`<option value="${task.id}">${task.title}</option>`);
            });
        }
    });
}

// التنقل بين الخطوات
function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            currentStep++;
            showStep(currentStep);
            updateStepNavigation();
            updateFormProgress();
        } else {
            // إرسال النموذج
            $('#taskForm').submit();
        }
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        updateStepNavigation();
        updateFormProgress();
    }
}

function showStep(step) {
    $('.step-content').removeClass('active');
    $(`.step-content[data-step="${step}"]`).addClass('active');

    $('.step-indicator').removeClass('active completed');
    for (let i = 1; i <= step; i++) {
        if (i < step) {
            $(`.step-indicator[data-step="${i}"]`).addClass('completed');
        } else if (i === step) {
            $(`.step-indicator[data-step="${i}"]`).addClass('active');
        }
    }
}

function updateStepNavigation() {
    $('#prevStep').prop('disabled', currentStep === 1);

    if (currentStep === totalSteps) {
        $('#nextStep').html('<i class="feather icon-save me-2"></i>حفظ المهمة');
    } else {
        $('#nextStep').html('التالي<i class="feather icon-chevron-left ms-2"></i>');
    }
}

function updateFormProgress() {
    const progress = (currentStep / totalSteps) * 100;
    $('#formProgress').css('width', progress + '%');
}

// التحقق من صحة الخطوة الحالية
function validateCurrentStep() {
    let isValid = true;
    const currentStepElement = $(`.step-content[data-step="${currentStep}"]`);

    // مسح الأخطاء السابقة
    currentStepElement.find('.is-invalid').removeClass('is-invalid');
    currentStepElement.find('.invalid-feedback').remove();

    // التحقق من الحقول المطلوبة
    currentStepElement.find('[required]').each(function() {
        if (!$(this).val() || $(this).val().trim() === '') {
            $(this).addClass('is-invalid');
            $(this).after('<div class="invalid-feedback">هذا الحقل مطلوب</div>');
            isValid = false;
        }
    });

    // تحققات إضافية حسب الخطوة
    if (currentStep === 2) {
        // التحقق من التواريخ
        const startDate = $('#start_date').val();
        const dueDate = $('#due_date').val();

        if (startDate && dueDate && new Date(startDate) > new Date(dueDate)) {
            $('#due_date').addClass('is-invalid');
            $('#due_date').after('<div class="invalid-feedback">تاريخ الانتهاء يجب أن يكون بعد تاريخ البدء</div>');
            isValid = false;
        }
    }

    if (!isValid) {
        showMiniToast('يرجى تصحيح الأخطاء قبل المتابعة', 'error');
    }

    return isValid;
}

// حفظ كمسودة
function saveAsDraft() {
    const formData = new FormData($('#taskForm')[0]);
    formData.append('is_draft', '1');

    $.ajax({
        url: '/tasks/save-draft',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            if (response.success) {
                showMiniToast('تم حفظ المسودة بنجاح', 'success');
            }
        },
        error: function() {
            showMiniToast('فشل في حفظ المسودة', 'error');
        }
    });
}
</script>
