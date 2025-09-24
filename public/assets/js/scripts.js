(function(window, undefined) {
  'use strict';

  /*
  NOTE:
  ------
  PLACE HERE YOUR OWN JAVASCRIPT CODE IF NEEDED
  WE WILL RELEASE FUTURE UPDATES SO IN ORDER TO NOT OVERWRITE YOUR JAVASCRIPT CODE PLEASE CONSIDER WRITING YOUR SCRIPT HERE.  */

})(window);


document.addEventListener('DOMContentLoaded', function () {
    // تهيئة Select2 للعناصر الموجودة
    initializeSelect2();

    // دالة لتهيئة Select2
    function initializeSelect2() {
        $('.select2').select2();
        initializeItemSelects();
    }

    // دالة خاصة لتهيئة select2 في الجدول
    function initializeItemSelects() {
        $('.item-select').each(function() {
            // تدمير أي نسخة سابقة من select2
            if ($(this).hasClass('select2-hidden-accessible')) {
                $(this).select2('destroy');
            }
            // تهيئة select2 جديد
            $(this).select2({
                width: '100%',
                dropdownParent: $(this).parent() // مهم: يجعل القائمة المنسدلة داخل الخلية
            });
        });
    }

    // إضافة صف جديد
    document.getElementById('add-row').addEventListener('click', function(e) {
        e.preventDefault();

        const tableBody = document.querySelector('#items-table tbody');
        const firstRow = document.querySelector('.item-row');

        if (firstRow) {
            // تدمير Select2 من جميع الصفوف قبل الاستنساخ
            $('.item-select').each(function() {
                if ($(this).hasClass('select2-hidden-accessible')) {
                    $(this).select2('destroy');
                }
            });

            // استنساخ الصف
            const newRow = firstRow.cloneNode(true);

            // تفريغ القيم في الصف الجديد
            newRow.querySelectorAll('input').forEach(input => {
                input.value = '';
            });

            newRow.querySelectorAll('select').forEach(select => {
                select.selectedIndex = 0;
            });

            // إضافة الصف الجديد
            tableBody.appendChild(newRow);

            // إعادة تهيئة Select2 لجميع الصفوف
            initializeItemSelects();
        }
    });

    // حذف صف
    document.querySelector('#items-table').addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.preventDefault();

            const row = e.target.closest('tr');
            const totalRows = document.querySelectorAll('.item-row').length;

            if (totalRows > 1) {
                // تدمير Select2 قبل إزالة الصف
                $(row).find('.item-select').select2('destroy');
                row.remove();
                calculateTotal();
            } else {
                alert('لا يمكن حذف جميع الصفوف. يجب أن يكون هناك صف واحد على الأقل.');
            }
        }
    });

    // حساب المجموع للصف
    function calculateRowTotal(row) {
        const unitPrice = parseFloat(row.querySelector('.unit-price').value) || 0;
        const quantity = parseFloat(row.querySelector('.quantity').value) || 0;
        const discount = parseFloat(row.querySelector('.discount').value) || 0;
        const tax1 = parseFloat(row.querySelector('.tax1').value) || 0;
        const tax2 = parseFloat(row.querySelector('.tax2').value) || 0;

        let total = unitPrice * quantity;
        total -= (total * (discount / 100));
        total += (total * (tax1 / 100));
        total += (total * (tax2 / 100));

        row.querySelector('.row-total').textContent = total.toFixed(2) + ' رس';
        return total;
    }

    // حساب المجموع الكلي
    function calculateTotal() {
        let total = 0;
        document.querySelectorAll('.item-row').forEach(row => {
            total += calculateRowTotal(row);
        });
        document.getElementById('total-amount').textContent = total.toFixed(2) + ' رس';
    }

    // إضافة مستمعي الأحداث لحساب المجاميع
    document.querySelector('#items-table').addEventListener('input', function(e) {
        if (e.target.matches('.unit-price, .quantity, .discount')) {
            calculateTotal();
        }
    });

    document.querySelector('#items-table').addEventListener('change', function(e) {
        if (e.target.matches('.tax1, .tax2')) {
            calculateTotal();
        }
    });
});



    $(document).ready(function(){
        var counter = 0;
        $(document).on("click",".addeventmore",function(){
            var whole_extra_item_add = $('#whole_extra_item_add').html();
            $(this).closest(".add_item").append(whole_extra_item_add);
            counter++;
        });
        $(document).on("click",'.removeeventmore',function(event){
            $(this).closest(".delete_whole_extra_item_add").remove();
            counter -= 1
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        // تفعيل التبديل بين الأقسام الرئيسية
        const sectionDiscount = document.getElementById('section-discount');
        const sectionDeposit = document.getElementById('section-deposit');
        const sectionShipping = document.getElementById('section-shipping');
        const sectionDocuments = document.getElementById('section-documents');

        // التبويبات الرئيسية
        document.querySelectorAll('.card-header-tabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // إزالة الكلاس active من جميع التبويبات الرئيسية
                document.querySelectorAll('.card-header-tabs .nav-link').forEach(t => t.classList.remove('active'));

                // إضافة الكلاس active للتبويبة المحددة
                this.classList.add('active');

                // إخفاء جميع الأقسام
                sectionDiscount.classList.add('d-none');
                sectionDeposit.classList.add('d-none');
                sectionShipping.classList.add('d-none');
                sectionDocuments.classList.add('d-none');

                // إظهار القسم المناسب حسب التبويبة المختارة
                if (this.id === 'tab-discount') {
                    sectionDiscount.classList.remove('d-none');
                } else if (this.id === 'tab-deposit') {
                    sectionDeposit.classList.remove('d-none');
                } else if (this.id === 'tab-documents') {
                    sectionDocuments.classList.remove('d-none');
                } else if (this.id === 'tab-shipping') {
                    sectionShipping.classList.remove('d-none');
                }
            });
        });

        // التبويبات الداخلية للمستندات
        const newDocumentTab = document.getElementById('tab-new-document');
        const uploadedDocumentsTab = document.getElementById('tab-uploaded-documents');
        const newDocumentContent = document.getElementById('content-new-document');
        const uploadedDocumentsContent = document.getElementById('content-uploaded-documents');

        // تفعيل التبديل بين تبويبات المستندات
        document.querySelectorAll('#section-documents .nav-tabs .nav-link').forEach(tab => {
            tab.addEventListener('click', function (e) {
                e.preventDefault();

                // إزالة الكلاس active من جميع التبويبات الداخلية
                document.querySelectorAll('#section-documents .nav-tabs .nav-link').forEach(t => {
                    t.classList.remove('active');
                });

                // إضافة الكلاس active للتبويبة المحددة
                this.classList.add('active');

                // إخفاء كل المحتويات
                newDocumentContent.classList.add('d-none');
                uploadedDocumentsContent.classList.add('d-none');

                // إظهار المحتوى المناسب
                if (this.id === 'tab-new-document') {
                    newDocumentContent.classList.remove('d-none');
                    newDocumentContent.classList.add('active');
                    uploadedDocumentsContent.classList.remove('active');
                } else if (this.id === 'tab-uploaded-documents') {
                    uploadedDocumentsContent.classList.remove('d-none');
                    uploadedDocumentsContent.classList.add('active');
                    newDocumentContent.classList.remove('active');
                }
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        tinymce.init({
            selector: '#tinyMCE',
            directionality: 'rtl',
            language: 'ar',
            height: 300,
            menubar: true,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'code', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
                'bold italic forecolor | alignleft aligncenter ' +
                'alignright alignjustify | bullist numlist outdent indent | ' +
                'removeformat | help',
            content_style: `
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    direction: rtl;
                    text-align: right;
                }
            `,
            setup: function (editor) {
                editor.on('init', function () {
                    editor.getContainer().style.transition = "border-color 0.15s ease-in-out";
                });
            },
            // تخصيص القوائم
            menu: {
                file: { title: 'ملف', items: 'newdocument restoredraft | preview | print' },
                edit: { title: 'تحرير', items: 'undo redo | cut copy paste pastetext | selectall | searchreplace' },
                view: { title: 'عرض', items: 'code | visualaid visualchars visualblocks | preview fullscreen' },
                insert: { title: 'إدراج', items: 'image link media template codesample inserttable | charmap emoticons hr | pagebreak nonbreaking anchor toc | insertdatetime' },
                format: { title: 'تنسيق', items: 'bold italic underline strikethrough superscript subscript codeformat | formats blockformats fontformats fontsizes align | forecolor backcolor | removeformat' },
                tools: { title: 'أدوات', items: 'spellchecker spellcheckerlanguage | code wordcount' },
                table: { title: 'جدول', items: 'inserttable | cell row column | advtablesort | tableprops deletetable' }
            },
            // تخصيص الستايل
            content_css: [
                '//fonts.googleapis.com/css?family=Cairo:400,700',
                '//cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css'
            ],
            font_formats:
                "Cairo=cairo,sans-serif;" +
                "Andale Mono=andale mono,times;" +
                "Arial=arial,helvetica,sans-serif;" +
                "Arial Black=arial black,avant garde;" +
                "Times New Roman=times new roman,times",
            // إزالة العلامة المائية
            branding: false,
            promotion: false
        });
    });

document.addEventListener('DOMContentLoaded', function() {
    // تهيئة المودال
    var myModal = new bootstrap.Modal(document.getElementById('customFieldsModal'));

    // إضافة مستمعي الأحداث للأزرار
    document.querySelectorAll('.modal-footer .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('btn-success')) {
                console.log('تم الضغط على زر الحفظ');
                // أضف هنا كود الحفظ
            } else if (this.classList.contains('btn-danger')) {
                console.log('تم الضغط على زر عدم الحفظ');
                // أضف هنا كود عدم الحفظ
            }
            myModal.hide();
        });
    });
});
document.addEventListener('DOMContentLoaded', function() {
    const paidCheck = document.getElementById('paidCheck');
    const paymentFields = document.getElementById('paymentFields');

    paidCheck.addEventListener('change', function() {
        if (this.checked) {
            paymentFields.style.display = 'block';
        } else {
            paymentFields.style.display = 'none';
        }
    });
});




// استخدام الخرائط
let map;
let marker;

// دالة لتبديل ظهور/إخفاء الخريطة
function toggleMap() {
    const mapContainer = document.getElementById('map-container');
    if (mapContainer.style.display === 'none') {
        mapContainer.style.display = 'block';
        initMap();
    } else {
        mapContainer.style.display = 'none';
    }
}

// دالة تهيئة الخريطة
function initMap() {
    // إحداثيات افتراضية (السعودية)
    const defaultLocation = {
        lat: 24.7136,
        lng: 46.6753
    };

    map = new google.maps.Map(document.getElementById('map'), {
        center: defaultLocation,
        zoom: 8
    });

    // إضافة علامة يمكن تحريكها
    marker = new google.maps.Marker({
        position: defaultLocation,
        map: map,
        draggable: true
    });

    // الاستماع لحدث تغيير موقع العلامة
    google.maps.event.addListener(marker, 'dragend', function() {
        const position = marker.getPosition();
        // يمكنك هنا تحديث حقول العنوان بناءً على الموقع الجديد
        updateAddress(position.lat(), position.lng());
    });
}

// دالة لتحديث العنوان بناءً على الإحداثيات
function updateAddress(lat, lng) {
    const geocoder = new google.maps.Geocoder();
    const latlng = {
        lat: lat,
        lng: lng
    };

    geocoder.geocode({
        location: latlng
    }, (results, status) => {
        if (status === 'OK' && results[0]) {
            const addressComponents = results[0].address_components;

            // تحديث حقول العنوان
            for (const component of addressComponents) {
                if (component.types.includes('street_number') || component.types.includes('route')) {
                    document.getElementById('street1').value = component.long_name;
                }
                if (component.types.includes('locality')) {
                    document.getElementById('city').value = component.long_name;
                }
                if (component.types.includes('administrative_area_level_1')) {
                    document.getElementById('region').value = component.long_name;
                }
                if (component.types.includes('postal_code')) {
                    document.getElementById('postal-code').value = component.long_name;
                }
            }
        }
    });
}



let contactCounter = 0;

document.addEventListener('DOMContentLoaded', function() {
    // الحصول على زر الإضافة وحاوية الحقول
    const addButton = document.querySelector('.إضافة');
    const contactContainer = document.querySelector('.contact-fields-container');

    // إضافة مستمع حدث للزر
    if (addButton) {
        addButton.addEventListener('click', addContactFields);
    }

    // دالة إضافة حقول جديدة
    let contactCounter = 0; // عداد الحقول

    // دالة لإضافة حقول جهة اتصال جديدة
    function addContactFields() {
        contactCounter++;

        const newFieldsGroup = document.createElement('div'); // إنشاء عنصر جديد
        newFieldsGroup.className = 'contact-fields-group bg-white p-3 mb-2 rounded border'; // إضافة صفوف التصميم
        newFieldsGroup.setAttribute('data-group', contactCounter); // تعيين معرف فريد

        // HTML الخاص بالحقول
        newFieldsGroup.innerHTML = `
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>الاسم الأول</label>
                    <input type="text" class="form-control" name="contacts[${contactCounter}][first_name]" placeholder="الاسم الأول" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label>الاسم الأخير</label>
                    <input type="text" class="form-control" name="contacts[${contactCounter}][last_name]" placeholder="الاسم الأخير" required>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>البريد الإلكتروني</label>
                    <input type="email" class="form-control" name="contacts[${contactCounter}][email]" placeholder="البريد الإلكتروني" required>
                </div>
                <div class="col-md-6 mb-2">
                    <label>الهاتف</label>
                    <input type="tel" class="form-control" name="contacts[${contactCounter}][phone]" placeholder="الهاتف">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <label>جوال</label>
                    <input type="tel" class="form-control" name="contacts[${contactCounter}][mobile]" placeholder="جوال">
                </div>
                <div class="col-md-6 mb-2 text-end">
                    <label>&nbsp;</label>
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeContactFields(${contactCounter})">
                        حذف
                    </button>
                </div>
            </div>
        `;

        const contactContainer = document.getElementById('contactContainer'); // الحاوية التي ستحتوي الحقول
        if (contactContainer) {
            contactContainer.appendChild(newFieldsGroup); // إضافة الحقول الجديدة إلى الحاوية
        }
    }

    // دالة لحذف حقل جهة اتصال
    function removeContactFields(counter) {
        const fieldGroup = document.querySelector(`.contact-fields-group[data-group="${counter}"]`); // العثور على الحقل
        if (fieldGroup) {
            fieldGroup.remove(); // حذف الحقل
        }
    }

});

// دالة حذف مجموعة الحقول
function removeContactFields(groupId) {
    const fieldsGroup = document.querySelector(`.contact-fields-group[data-group="${groupId}"]`);
    if (fieldsGroup) {
        fieldsGroup.remove();
    }
}
$(document).ready(function() {
    // Initialize Summernote
    $('.summernote').summernote({
        height: 200,
        toolbar: [
            ['style', ['style']],
            ['font', ['bold', 'underline', 'italic']],
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['insert', ['link']],
        ]
    });

    // Initialize Datepicker
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose: true,
        todayHighlight: true
    });

    // Initialize Timepicker
    $('.timepicker').timepicker({
        showMeridian: false,
        defaultTime: false
    });

    // Initialize Select2
    $('.select2').select2();
});

function toggleRecurringFields(checkbox) {
    var recurringFields = document.getElementById('recurring-fields');
    recurringFields.style.display = checkbox.checked ? 'block' : 'none';
}

function toggleStaffFields(checkbox) {
    var staffFields = document.getElementById('staff-fields');
    staffFields.style.display = checkbox.checked ? 'block' : 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    // تهيئة المودال
    var myModal = new bootstrap.Modal(document.getElementById('customFieldsModal'));

    // إضافة مستمعي الأحداث للأزرار
    document.querySelectorAll('.modal-footer .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('btn-success')) {
                console.log('تم الضغط على زر الحفظ');
                // أضف هنا كود الحفظ
            } else if (this.classList.contains('btn-danger')) {
                console.log('تم الضغط على زر عدم الحفظ');
                // أضف هنا كود عدم الحفظ
            }
            myModal.hide();
        });
    });
});


// التبديل بين الأقسام
document.querySelectorAll('.nav-link').forEach(tab => {
    tab.addEventListener('click', function(e) {
        e.preventDefault();

        // إخفاء جميع الأقسام
        document.querySelectorAll('.card-body > div').forEach(section => {
            section.classList.add('d-none');
        });

        // إزالة "active" من جميع التبويبات
        document.querySelectorAll('.nav-link').forEach(link => {
            link.classList.remove('active');
        });

        // عرض القسم المرتبط بالتبويبة
        const target = document.querySelector(this.getAttribute('data-target'));
        target.classList.remove('d-none');

        // إضافة "active" إلى التبويبة الحالية
        this.classList.add('active');
    });
});
