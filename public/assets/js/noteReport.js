$(document).ready(function() {
    // تهيئة Toastr للإشعارات
    toastr.options = {
        "positionClass": "toast-top-left",
        "rtl": true,
        "timeOut": 3000,
        "progressBar": true,
        "closeButton": true
    };

    // تهيئة Select2
    $('.select2').select2({
        placeholder: "اختر مجموعة",
        allowClear: true,
        dir: "rtl"
    });

    // تهيئة أدوات التلميح
    $('[data-toggle="tooltip"]').tooltip({
        placement: 'top'
    });

    // متغيرات التحكم في الفترات الزمنية
    let currentWeekOffset = 0;
    const weeksPerPage = 8;
    let isLoading = false;

    // إظهار/إخفاء تواريخ الأسابيع
    $('.toggle-week-dates').click(function() {
        $('.week-dates').toggle();
        $(this).toggleClass('btn-primary btn-outline-primary');
        const isVisible = $('.week-dates').is(':visible');
        toastr.info(isVisible ? 'تم إظهار تواريخ الأسابيع' : 'تم إخفاء تواريخ الأسابيع');
    });

    // فلترة حسب اسم العميل
    $('#client-search').on('keyup', debounce(function() {
        const searchText = $(this).val().toLowerCase().trim();
        let visibleRows = 0;

        $('.client-row').each(function() {
            const clientName = $(this).data('client').toLowerCase();
            const clientArea = $(this).find('.client-area').text().toLowerCase();
            const searchMatch = clientName.includes(searchText) || clientArea.includes(searchText);
            $(this).toggle(searchMatch);
            if (searchMatch) visibleRows++;
        });

        if (searchText.length > 0) {
            toastr.info(`عرض ${visibleRows} عميل من نتائج البحث`);
        } else {
            toastr.clear();
        }
    }, 300));

    // فلترة حسب المجموعة
    $('#group-filter').change(function() {
        const groupId = $(this).val();
        let visibleGroups = 0;

        $('.group-section').each(function() {
            const isTargetGroup = !groupId || $(this).attr('id') === groupId;
            $(this).toggleClass('d-none', !isTargetGroup);
            if (isTargetGroup) visibleGroups++;
        });

        if (groupId) {
            toastr.info(`تم عرض مجموعة ${$('#group-filter option:selected').text()}`);
        } else {
            toastr.info('تم إظهار جميع المجموعات');
        }
    });

    // فلترة حسب النشاط
    $('input[name="activity"]').change(function() {
        const filter = $(this).val();
        let visibleRows = 0;

        $('.client-row').each(function() {
            const hasActivity = $(this).find('.activity-cell[data-has-activity="1"]').length > 0;
            let showRow = true;

            if (filter === 'has-activity') {
                showRow = hasActivity;
            } else if (filter === 'no-activity') {
                showRow = !hasActivity;
            }

            $(this).toggle(showRow);
            if (showRow) visibleRows++;
        });

        toastr.info(`عرض ${visibleRows} عميل بعد تطبيق الفلتر`);
    });

    // التصدير إلى Excel
    $('#export-excel').click(function() {
        try {
            // إنشاء ورقة عمل Excel
            const wb = XLSX.utils.book_new();
            const wsData = [];

            // إضافة العناوين
            const headers = ['العميل', 'الحالة', 'المنطقة'];
            $('.week-header th:not(:first-child):not(:last-child)').each(function() {
                headers.push($(this).find('.week-number').text());
            });
            headers.push('إجمالي النشاط');
            wsData.push(headers);

            // إضافة بيانات العملاء
            $('.client-row:visible').each(function() {
                const row = [];
                const clientName = $(this).find('.client-name').text().trim();
                const clientStatus = $(this).data('status');
                const clientArea = $(this).find('.client-area').text().trim();

                row.push(clientName, clientStatus, clientArea);

                $(this).find('.activity-cell').each(function() {
                    row.push($(this).data('has-activity') === '1' ? 'نعم' : 'لا');
                });

                row.push($(this).find('.total-activities').text().trim());
                wsData.push(row);
            });

            // تحويل البيانات إلى ورقة عمل
            const ws = XLSX.utils.aoa_to_sheet(wsData);

            // إضافة ورقة العمل إلى الكتاب
            XLSX.utils.book_append_sheet(wb, ws, "تحليل الزيارات");

            // تنزيل الملف
            const date = new Date().toISOString().split('T')[0];
            XLSX.writeFile(wb, `تحليل_الزيارات_${date}.xlsx`);

            toastr.success('تم تصدير البيانات بنجاح');
        } catch (error) {
            console.error('Export error:', error);
            toastr.error('حدث خطأ أثناء التصدير');
        }
    });

    // عرض تفاصيل الملاحظات
    $(document).on('click', '.show-notes', function(e) {
        e.preventDefault();
        showNotesModal($(this).data('client'), $(this).data('notes'));
    });

    // تحميل الأسابيع السابقة
    $('#prev-period').click(function() {
        if (isLoading) return;
        currentWeekOffset += weeksPerPage;
        loadWeeks();
    });

    // تحميل الأسابيع التالية
    $('#next-period').click(function() {
        if (isLoading) return;
        currentWeekOffset = Math.max(0, currentWeekOffset - weeksPerPage);
        loadWeeks();
    });

    // عرض شاشة التحميل
    function showLoading() {
        if (isLoading) return;
        isLoading = true;

        $('body').append(`
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">جاري التحميل...</span>
                </div>
                <div class="mt-3 text-primary font-weight-bold">جاري تحميل البيانات...</div>
            </div>
        `);
    }

    // إخفاء شاشة التحميل
    function hideLoading() {
        isLoading = false;
        $('.loading-overlay').fadeOut(400, function() {
            $(this).remove();
        });
    }

    // تحميل بيانات الأسابيع
    function loadWeeks() {
        showLoading();

        $.ajax({
            url: '/analysis/weeks-data',
            type: 'GET',
            data: {
                offset: currentWeekOffset,
                limit: weeksPerPage
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    updateWeeksHeader(response.weeks);
                    updateClientsData(response.clients, response.weeks);
                    updatePeriodDisplay(response.weeks);
                    toastr.success('تم تحديث البيانات بنجاح');
                } else {
                    toastr.error(response.message || 'حدث خطأ في جلب البيانات');
                }
            },
            error: function(xhr) {
                toastr.error('حدث خطأ في الاتصال بالخادم');
                console.error('Error:', xhr.responseText);
            },
            complete: hideLoading
        });
    }

    // تحديث رأس الجدول بالأسابيع الجديدة
    function updateWeeksHeader(weeks) {
        const $weekHeader = $('.week-header');
        $weekHeader.empty();

        // عمود اسم العميل
        $weekHeader.append('<th style="min-width: 200px">العميل</th>');

        // أعمدة الأسابيع
        weeks.forEach(week => {
            $weekHeader.append(`
                <th class="text-center">
                    <div class="week-number">${week.week_number}</div>
                    <div class="week-dates small text-muted">${week.start_date} - ${week.end_date}</div>
                </th>
            `);
        });

        // عمود الإجمالي
        $weekHeader.append('<th class="text-center">الإجمالي</th>');
    }

    // تحديث بيانات العملاء
    function updateClientsData(clients, weeks) {
        const $tableBody = $('#clients-table tbody');
        $tableBody.empty();

        if (!clients || clients.length === 0) {
            $tableBody.append('<tr><td colspan="' + (weeks.length + 2) + '" class="text-center py-5">لا توجد بيانات</td></tr>');
            return;
        }

        clients.forEach(client => {
            const $row = $(`
                <tr class="client-row" data-client="${client.name}" data-status="${client.status}">
                    <td>
                        <div class="client-name font-weight-bold">${client.name}</div>
                        <div class="client-area text-muted">${client.area}</div>
                    </td>
            `);

            // إضافة خلايا النشاط
            weeks.forEach(week => {
                const hasActivity = client.activities && client.activities[week.id];
                $row.append(`
                    <td class="activity-cell text-center" data-has-activity="${hasActivity ? 1 : 0}">
                        ${hasActivity ? '<i class="fas fa-check text-success"></i>' : '<i class="fas fa-times text-muted"></i>'}
                    </td>
                `);
            });

            // إضافة خلية الإجمالي
            $row.append(`
                <td class="text-center total-activities">
                    <span class="badge badge-pill badge-primary">${client.total_activities || 0}</span>
                </td>
            `);

            $tableBody.append($row);
        });
    }

    // تحديث عرض الفترة الحالية
    function updatePeriodDisplay(weeks) {
        if (weeks.length > 0) {
            const startWeek = weeks[0].month_week || `الأسبوع ${weeks[0].week_number}`;
            const endWeek = weeks[weeks.length - 1].month_week || `الأسبوع ${weeks[weeks.length - 1].week_number}`;
            $('#current-period').text(`${startWeek} - ${endWeek}`);
        }
    }

    // عرض نافذة الملاحظات
    function showNotesModal(clientName, notesData) {
        let notesHtml = '<div class="text-right">';
        notesHtml += `<h5 class="mb-3">ملاحظات للعميل: ${clientName}</h5>`;

        if (!notesData || notesData.length === 0) {
            notesHtml += '<div class="alert alert-info">لا توجد ملاحظات مسجلة</div>';
        } else {
            const notes = typeof notesData === 'string' ? JSON.parse(notesData) : notesData;

            notes.forEach(note => {
                notesHtml += `
                    <div class="card mb-3 border-left-primary">
                        <div class="card-body py-2">
                            <div class="d-flex justify-content-between">
                                <h6 class="card-subtitle mb-1 text-muted">
                                    ${note.date || 'غير محدد'} - ${note.time || 'غير محدد'}
                                </h6>
                                <small class="text-muted">${note.created_at || ''}</small>
                            </div>
                            <p class="card-text mt-2 mb-1">${note.description || 'لا يوجد وصف'}</p>
                            <div class="d-flex mt-2">
                                <span class="badge badge-light mr-2">
                                    <i class="fas fa-info-circle mr-1"></i> ${note.status || 'غير محدد'}
                                </span>
                                <span class="badge badge-light">
                                    <i class="fas fa-tasks mr-1"></i> ${note.process || 'غير محدد'}
                                </span>
                            </div>
                        </div>
                    </div>
                `;
            });
        }

        notesHtml += '</div>';

        Swal.fire({
            title: 'تفاصيل الملاحظات',
            html: notesHtml,
            width: '800px',
            confirmButtonText: 'تم',
            showCloseButton: true,
            customClass: {
                container: 'rtl-container'
            }
        });
    }

    // دالة للمساعدة في تأخير الأحداث
    function debounce(func, wait) {
        let timeout;
        return function() {
            const context = this, args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(() => {
                func.apply(context, args);
            }, wait);
        };
    }

    // تحميل البيانات الأولية
    loadWeeks();
});
