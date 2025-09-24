// سكريبت اختبار نظام المهام
// ضعه في console المتصفح للتأكد من أن كل شيء يعمل

console.log('🔍 بدء فحص نظام المهام...\n');

// 1. فحص jQuery
if (typeof $ !== 'undefined') {
    console.log('✅ jQuery محمّل بنجاح');
} else {
    console.error('❌ jQuery غير محمّل!');
}

// 2. فحص CSRF Token
const csrfToken = $('meta[name="csrf-token"]').attr('content');
if (csrfToken) {
    console.log('✅ CSRF Token موجود:', csrfToken.substring(0, 20) + '...');
} else {
    console.error('❌ CSRF Token غير موجود!');
}

// 3. فحص SweetAlert2
if (typeof Swal !== 'undefined') {
    console.log('✅ SweetAlert2 محمّل بنجاح');
} else {
    console.error('❌ SweetAlert2 غير محمّل!');
}

// 4. فحص Select2
if (typeof $.fn.select2 !== 'undefined') {
    console.log('✅ Select2 محمّل بنجاح');
} else {
    console.error('❌ Select2 غير محمّل!');
}

// 5. فحص النموذج
if ($('#taskForm').length > 0) {
    console.log('✅ نموذج المهمة موجود');
} else {
    console.error('❌ نموذج المهمة غير موجود!');
}

// 6. فحص أعمدة Kanban
const columns = $('.kanban-column').length;
if (columns === 4) {
    console.log('✅ أعمدة Kanban موجودة (4 أعمدة)');
} else {
    console.warn(`⚠️ عدد أعمدة Kanban: ${columns} (يجب أن يكون 4)`);
}

// 7. فحص بطاقات المهام
const taskCards = $('.task-card').length;
console.log(`📋 عدد بطاقات المهام: ${taskCards}`);

// 8. فحص وظائف السحب والإفلات
let dragDropEnabled = true;
$('.task-card').each(function() {
    if (!$(this).attr('draggable')) {
        dragDropEnabled = false;
    }
});

if (dragDropEnabled && taskCards > 0) {
    console.log('✅ السحب والإفلات مُفعّل');
} else if (taskCards === 0) {
    console.log('ℹ️ لا توجد مهام لاختبار السحب والإفلات');
} else {
    console.error('❌ السحب والإفلات غير مُفعّل!');
}

// 9. اختبار إنشاء مهمة (محاكاة)
console.log('\n🧪 اختبار إنشاء مهمة...');

function testTaskCreation() {
    const testData = {
        _token: csrfToken,
        project_id: 1, // تأكد من وجود مشروع برقم 1
        title: 'مهمة اختبار - ' + new Date().toLocaleString('ar-SA'),
        description: 'هذه مهمة اختبار تم إنشاؤها من console',
        priority: 'medium',
        start_date: new Date().toISOString().split('T')[0],
        due_date: new Date(Date.now() + 7*24*60*60*1000).toISOString().split('T')[0],
        budget: 1000,
        estimated_hours: 10
    };

    console.log('📤 إرسال البيانات:', testData);

    $.ajax({
        url: '/tasks',
        method: 'POST',
        data: testData,
        success: function(response) {
            console.log('✅ نجح الاختبار!');
            console.log('📥 الرد:', response);
        },
        error: function(xhr) {
            console.error('❌ فشل الاختبار!');
            console.error('Status:', xhr.status);
            console.error('Response:', xhr.responseJSON || xhr.responseText);
        }
    });
}

// 10. فحص المسارات
console.log('\n🛣️ فحص المسارات المتاحة...');

const routes = [
    { url: '/tasks', method: 'GET', name: 'قائمة المهام' },
    { url: '/tasks', method: 'POST', name: 'إنشاء مهمة' },
    { url: '/tasks/1', method: 'GET', name: 'عرض مهمة' },
    { url: '/tasks/1/update-status', method: 'POST', name: 'تحديث حالة' }
];

console.log('المسارات المطلوبة:');
routes.forEach(route => {
    console.log(`  ${route.method} ${route.url} - ${route.name}`);
});

// 11. ملخص الفحص
console.log('\n📊 ملخص الفحص:');
console.log('─────────────────────────────────────');

const checks = [
    { name: 'jQuery', status: typeof $ !== 'undefined' },
    { name: 'CSRF Token', status: !!csrfToken },
    { name: 'SweetAlert2', status: typeof Swal !== 'undefined' },
    { name: 'Select2', status: typeof $.fn.select2 !== 'undefined' },
    { name: 'نموذج المهمة', status: $('#taskForm').length > 0 },
    { name: 'أعمدة Kanban', status: columns === 4 },
    { name: 'السحب والإفلات', status: dragDropEnabled }
];

checks.forEach(check => {
    console.log(`${check.status ? '✅' : '❌'} ${check.name}`);
});

console.log('─────────────────────────────────────');

// 12. تعليمات الاختبار
console.log('\n📝 للاختبار اليدوي:');
console.log('1. قم باستدعاء: testTaskCreation()');
console.log('2. افتح Network tab وراقب الطلبات');
console.log('3. تحقق من الأخطاء في Console');
console.log('4. تحقق من ملف laravel.log');

console.log('\n✨ انتهى الفحص!\n');
