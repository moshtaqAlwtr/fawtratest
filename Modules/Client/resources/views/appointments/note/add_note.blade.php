@extends('master')

@section('title', 'إضافة ملاحظة أو مرفق')

@section('content')
    <div class="container mt-4">
        <form onsubmit="return validateAttachments()" id="clientForm" action="{{ route('clients.addnotes') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="current_latitude" id="current_latitude">
            <input type="hidden" name="current_longitude" id="current_longitude">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            @if (session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <label>الحقول التي عليها علامة <span style="color: red">*</span> الزامية</label>
                        </div>
                        <div>
                            <a href="{{ route('appointments.index') }}" class="btn btn-outline-danger">
                                <i class="fa fa-ban"></i>الغاء
                            </a>
                            <button type="submit" class="btn btn-outline-primary">
                                <i class="fa fa-save"></i>حفظ
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Section -->
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <!-- Date and Time -->
                    <div class="row mb-3">
                        <div class="form-group col-md-6">
                            <label for="action_type">نوع الإجراء</label>
                            <select class="form-control" id="action_type" name="process" required>
                                <option value="">اختر نوع الإجراء</option>
                                <option value="add_new" class="text-primary">+ تعديل قائمة الإجراءات</option>
                            </select>
                            <input type="hidden" name="client_id" value="{{ $id }}">
                        </div>
                    </div>

                    <!-- New Fields -->
                    <div class="row mb-3">
                        <!-- عدد العهدة -->
                        <div class="form-group col-md-4">
                            <label for="deposit_count" class="form-label">عدد العهدة الموجودة</label>
                            <input type="number" class="form-control" id="deposit_count" name="deposit_count" min="0" required>
                        </div>

                        <!-- نوع الموقع -->
                        <div class="form-group col-md-4">
                            <label for="site_type" class="form-label">نوع الموقع</label>
                            <select class="form-control" id="site_type" name="site_type" required>
                                <option value="">اختر نوع الموقع</option>
                                <option value="independent_booth">بسطة مستقلة</option>
                                <option value="grocery">بقالة</option>
                                <option value="supplies">تموينات</option>
                                <option value="markets">أسواق</option>
                                <option value="station">محطة</option>
                            </select>
                        </div>

                        <!-- عدد استندات المنافسين -->
                        <div class="form-group col-md-4">
                            <label for="competitor_documents" class="form-label">عدد استندات المنافسين</label>
                            <input type="number" class="form-control" id="competitor_documents" name="competitor_documents" min="0" required>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label for="notes" class="form-label">ملاحظة</label>
                        <textarea class="form-control" name="description" rows="4" required></textarea>
                    </div>

                    <!-- Enhanced Attachments Section -->
                    <div class="col-md-12 col-12 mb-3">
                        <div class="form-group">
                            <label for="attachments" class="form-label">المرفقات <span class="text-danger">*</span></label>
                            <input type="file" name="attachments[]" multiple id="attachments" class="form-control d-none"
                                accept="image/*,video/*,.pdf,.doc,.docx,.xlsx,.txt"
                                onchange="handleFileSelection(event)" required>

                            <!-- Upload Area -->
                            <div class="upload-area border rounded p-4 text-center position-relative bg-light"
                                onclick="document.getElementById('attachments').click()" style="cursor: pointer;">
                                <div class="d-flex flex-column align-items-center justify-content-center gap-2">
                                    <i class="fas fa-cloud-upload-alt fa-2x text-primary"></i>
                                    <p class="mb-0 text-primary fw-bold">اضغط هنا أو اختر من جهازك</p>
                                    <small class="text-muted">يتم ضغط الصور تلقائياً لتوفير مساحة التخزين</small>
                                    <small class="text-info">يمكنك رفع صور، فيديوهات، وملفات PDF/Word/Excel</small>
                                </div>
                                <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                                    <i class="fas fa-file-alt fs-3 text-secondary"></i>
                                </div>
                            </div>

                            <!-- Compression Settings -->
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <label for="imageQuality" class="form-label small">جودة ضغط الصور</label>
                                    <select id="imageQuality" class="form-select form-select-sm">
                                        <option value="0.8">عالية جداً (80%)</option>
                                        <option value="0.6" selected>عالية (60%)</option>
                                        <option value="0.4">متوسطة (40%)</option>
                                        <option value="0.2">منخفضة (20%)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="maxImageSize" class="form-label small">الحد الأقصى لحجم الصورة</label>
                                    <select id="maxImageSize" class="form-select form-select-sm">
                                        <option value="1920">1920px (عالية الدقة)</option>
                                        <option value="1280" selected>1280px (دقة متوسطة)</option>
                                        <option value="800">800px (دقة منخفضة)</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div id="compressionProgress" class="mt-3 d-none">
                                <label class="form-label small">تقدم ضغط الملفات:</label>
                                <div class="progress" style="height: 6px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated bg-success"
                                         role="progressbar" style="width: 0%"></div>
                                </div>
                                <small class="text-muted mt-1 d-block">يتم ضغط الملفات...</small>
                            </div>

                            <!-- File Preview -->
                            <div id="selected-files" class="mt-3"></div>

                            <!-- Compression Summary -->
                            <div id="compressionSummary" class="mt-3 d-none">
                                <div class="alert alert-success border-0">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <small class="text-muted d-block">الحجم الأصلي</small>
                                            <span class="fw-bold" id="originalSize">0 MB</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">بعد الضغط</small>
                                            <span class="fw-bold text-success" id="compressedSize">0 MB</span>
                                        </div>
                                        <div class="col-4">
                                            <small class="text-muted d-block">نسبة التوفير</small>
                                            <span class="fw-bold text-primary" id="savingsPercentage">0%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" name="share_with_work" id="shareWithWork">
                        <label class="form-check-label" for="shareWithWork">مشاركة مع العمل</label>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    @parent
    <style>
        .upload-area {
            border: 2px dashed #007bff;
            transition: all 0.3s ease;
        }
        .upload-area:hover {
            border-color: #0056b3;
            background-color: #f1f3f4 !important;
        }
        .file-item {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 10px;
            padding: 12px;
            background: #f8f9fa;
        }
        .image-preview {
            max-width: 60px;
            max-height: 60px;
            object-fit: cover;
            border-radius: 4px;
        }
    </style>

    <script>
        let processedFiles = [];
        let totalOriginalSize = 0;
        let totalCompressedSize = 0;

        $(document).ready(function() {
            // تحميل الإجراءات من localStorage أو استخدام القائمة الافتراضية
            let procedures = JSON.parse(localStorage.getItem('procedures')) || [
                'متابعة', 'تدقيق', 'مراجعة', 'اجتماع', 'زيارة', 'ملاحظة'
            ];

            // باقي الكود الموجود...
            updateSelectOptions();

            function updateSelectOptions() {
                let selectHtml = '<option value="">اختر نوع الإجراء</option>';
                procedures.forEach(proc => {
                    selectHtml += `<option value="${proc}">${proc}</option>`;
                });
                selectHtml += '<option value="add_new" class="text-primary">+ تعديل قائمة الإجراءات</option>';
                $('#action_type').html(selectHtml);
            }
        });

        // Handle file selection with compression
        async function handleFileSelection(event) {
            const files = Array.from(event.target.files);
            if (files.length === 0) return;

            showProgress(true);
            processedFiles = [];
            totalOriginalSize = 0;
            totalCompressedSize = 0;

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                updateProgress((i / files.length) * 100, `معالجة ${file.name}...`);

                const processedFile = await processFile(file);
                if (processedFile) {
                    processedFiles.push(processedFile);
                }
            }

            updateProgress(100, 'تم الانتهاء من معالجة جميع الملفات');
            setTimeout(() => {
                showProgress(false);
                previewSelectedFiles();
                updateCompressionSummary();
            }, 500);
        }

        // Process individual file
        async function processFile(file) {
            totalOriginalSize += file.size;

            if (file.type.startsWith('image/')) {
                return await compressImage(file);
            } else if (file.type.startsWith('video/')) {
                // For videos, validate size but don't compress
                if (file.size > 50 * 1024 * 1024) { // 50MB limit
                    alert(`الفيديو ${file.name} كبير جداً. الحد الأقصى 50 ميجابايت.`);
                    return null;
                }
                totalCompressedSize += file.size;
                return {
                    file: file,
                    originalSize: file.size,
                    compressedSize: file.size,
                    type: 'video',
                    preview: null
                };
            } else {
                // Documents (PDF, DOC, etc.)
                if (file.size > 10 * 1024 * 1024) { // 10MB limit
                    alert(`الملف ${file.name} كبير جداً. الحد الأقصى 10 ميجابايت.`);
                    return null;
                }
                totalCompressedSize += file.size;
                return {
                    file: file,
                    originalSize: file.size,
                    compressedSize: file.size,
                    type: 'document',
                    preview: null
                };
            }
        }

        // Compress image function
        function compressImage(file) {
            return new Promise((resolve) => {
                const canvas = document.createElement('canvas');
                const ctx = canvas.getContext('2d');
                const img = new Image();

                img.onload = function() {
                    const quality = parseFloat(document.getElementById('imageQuality').value);
                    const maxSize = parseInt(document.getElementById('maxImageSize').value);

                    // Calculate new dimensions
                    let { width, height } = img;
                    const ratio = Math.min(maxSize / width, maxSize / height);

                    if (ratio < 1) {
                        width *= ratio;
                        height *= ratio;
                    }

                    canvas.width = width;
                    canvas.height = height;

                    // Draw and compress
                    ctx.drawImage(img, 0, 0, width, height);

                    canvas.toBlob((blob) => {
                        totalCompressedSize += blob.size;

                        // Create preview
                        const previewUrl = URL.createObjectURL(blob);

                        // Create new file from blob
                        const compressedFile = new File([blob], file.name, {
                            type: file.type,
                            lastModified: Date.now()
                        });

                        resolve({
                            file: compressedFile,
                            originalSize: file.size,
                            compressedSize: blob.size,
                            type: 'image',
                            preview: previewUrl
                        });
                    }, file.type, quality);
                };

                img.src = URL.createObjectURL(file);
            });
        }

        // Updated preview function
        function previewSelectedFiles() {
            const preview = document.getElementById('selected-files');
            preview.innerHTML = '';

            if (processedFiles.length === 0) return;

            processedFiles.forEach((item, index) => {
                const fileDiv = document.createElement('div');
                fileDiv.className = 'file-item';

                const savings = item.originalSize > 0 ?
                    ((item.originalSize - item.compressedSize) / item.originalSize * 100).toFixed(1) : 0;
                const savingsClass = savings > 0 ? 'text-success' : 'text-muted';

                fileDiv.innerHTML = `
                    <div class="row align-items-center">
                        <div class="col-2">
                            ${item.preview ?
                                `<img src="${item.preview}" class="image-preview" alt="معاينة">` :
                                `<i class="fas ${getFileIcon(item.type)} fa-2x text-secondary"></i>`
                            }
                        </div>
                        <div class="col-7">
                            <h6 class="mb-1 text-truncate">${item.file.name}</h6>
                            <small class="text-muted">${getFileTypeText(item.type)}</small>
                            <div class="mt-1">
                                <span class="badge bg-primary">${formatFileSize(item.compressedSize)}</span>
                                ${savings > 0 ? `<span class="badge bg-success ms-1">-${savings}%</span>` : ''}
                            </div>
                        </div>
                        <div class="col-2">
                            <small class="text-muted d-block">أصلي: ${formatFileSize(item.originalSize)}</small>
                            ${savings > 0 ? `<small class="${savingsClass}">توفير: ${formatFileSize(item.originalSize - item.compressedSize)}</small>` : ''}
                        </div>
                        <div class="col-1">
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeFile(${index})">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                `;

                preview.appendChild(fileDiv);
            });

            // Update the original file input with compressed files
            updateFileInput();
        }

        // Update file input with compressed files
        function updateFileInput() {
            const dt = new DataTransfer();
            processedFiles.forEach(item => {
                dt.items.add(item.file);
            });
            document.getElementById('attachments').files = dt.files;
        }

        // Update compression summary
        function updateCompressionSummary() {
            if (processedFiles.length === 0) {
                document.getElementById('compressionSummary').classList.add('d-none');
                return;
            }

            const summary = document.getElementById('compressionSummary');
            const savings = totalOriginalSize > 0 ?
                ((totalOriginalSize - totalCompressedSize) / totalOriginalSize * 100).toFixed(1) : 0;

            document.getElementById('originalSize').textContent = formatFileSize(totalOriginalSize);
            document.getElementById('compressedSize').textContent = formatFileSize(totalCompressedSize);
            document.getElementById('savingsPercentage').textContent = savings + '%';

            summary.classList.remove('d-none');
        }

        // Utility functions
        function showProgress(show) {
            const progress = document.getElementById('compressionProgress');
            if (show) {
                progress.classList.remove('d-none');
            } else {
                progress.classList.add('d-none');
            }
        }

        function updateProgress(percentage, message) {
            const progressBar = document.querySelector('.progress-bar');
            const progressText = document.querySelector('#compressionProgress small');

            progressBar.style.width = percentage + '%';
            progressText.textContent = message;
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        function getFileIcon(type) {
            const icons = {
                'image': 'fa-image',
                'video': 'fa-video',
                'document': 'fa-file-alt'
            };
            return icons[type] || 'fa-file';
        }

        function getFileTypeText(type) {
            const types = {
                'image': 'صورة',
                'video': 'فيديو',
                'document': 'مستند'
            };
            return types[type] || 'ملف';
        }

        function removeFile(index) {
            const item = processedFiles[index];
            totalOriginalSize -= item.originalSize;
            totalCompressedSize -= item.compressedSize;

            processedFiles.splice(index, 1);
            previewSelectedFiles();
            updateCompressionSummary();
        }

        // Enhanced validation function
        function validateAttachments() {
            if (processedFiles.length === 0) {
                alert('يرجى إرفاق ملف واحد على الأقل قبل إرسال النموذج.');
                return false;
            }

            // Check file sizes after compression
            const oversizedFiles = processedFiles.filter(item => {
                if (item.type === 'image' && item.compressedSize > 5 * 1024 * 1024) return true; // 5MB for images
                if (item.type === 'video' && item.compressedSize > 50 * 1024 * 1024) return true; // 50MB for videos
                if (item.type === 'document' && item.compressedSize > 10 * 1024 * 1024) return true; // 10MB for documents
                return false;
            });

            if (oversizedFiles.length > 0) {
                alert('بعض الملفات كبيرة جداً حتى بعد الضغط. يرجى اختيار ملفات أصغر.');
                return false;
            }

            return true;
        }

        // Auto-compression when settings change
        document.getElementById('imageQuality').addEventListener('change', function() {
            if (processedFiles.length > 0) {
                const imageFiles = processedFiles.filter(item => item.type === 'image');
                if (imageFiles.length > 0) {
                    // Re-compress images with new settings
                    handleFileSelection({ target: { files: imageFiles.map(item => item.file) } });
                }
            }
        });

        document.getElementById('maxImageSize').addEventListener('change', function() {
            if (processedFiles.length > 0) {
                const imageFiles = processedFiles.filter(item => item.type === 'image');
                if (imageFiles.length > 0) {
                    // Re-compress images with new settings
                    handleFileSelection({ target: { files: imageFiles.map(item => item.file) } });
                }
            }
        });
    </script>
@endsection