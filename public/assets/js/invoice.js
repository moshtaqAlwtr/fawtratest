$(document).ready(function() {
    let rowCount = 1;
    let products = [];

    // تحميل المنتجات من العنصر المخفي
    try {
        products = JSON.parse($('#products-data').val() || '[]');
    } catch (e) {
        console.error('Error parsing products data:', e);
    }

    // دالة للتحكم في ظهور الصفوف
    function toggleRows() {
        // صف الخصومات
        const totalDiscount = parseFloat($('input[name="discount_amount"]').val()) || 0;
        const itemDiscounts = $('.discount-value').toArray().some(el => parseFloat($(el).val()) > 0);
        $('#total-discount').closest('tr').toggle(totalDiscount > 0 || itemDiscounts);

        // صف الضرائب
        const hasTax = $('#methodSelect').val() == 1 ||
                      $('.item-row').toArray().some(row => {
                          const tax1 = parseFloat($(row).find('input[name$="[tax_1]"]').val()) || 0;
                          const tax2 = parseFloat($(row).find('input[name$="[tax_2]"]').val()) || 0;
                          return tax1 > 0 || tax2 > 0;
                      });
        $('#total-tax').closest('tr').toggle(hasTax);

        // صف تكلفة الشحن
        const shippingCost = parseFloat($('input[name="shipping_cost"]').val()) || 0;
        $('#shipping-cost').closest('tr').toggle(shippingCost > 0);

        // صف الدفعة القادمة
        const advancedPayment = parseFloat($('#advanced-payment').val()) || 0;
        $('#next-payment').closest('tr').toggle(advancedPayment > 0);

        // صف المبلغ المستحق - يظهر فقط إذا كانت هناك دفعة مقدمة
        $('#due-value').closest('tr').toggle(advancedPayment > 0);
    }

    // إضافة صف جديد
    function addNewRow() {
        let newRow = $('.item-row:first').clone();

        // تحديث أسماء الحقول
        newRow.find('select, input').each(function() {
            let name = $(this).attr('name');
            if (name) {
                $(this).attr('name', name.replace('[0]', '[' + rowCount + ']'));
            }
        });

        // إعادة تعيين القيم
        newRow.find('.product-select').val('').trigger('change');
        newRow.find('.item-description').val('');
        newRow.find('.quantity').val('1');
        newRow.find('.price').val('0');
        newRow.find('.discount-value').val('0');
        newRow.find('.discount-type').val('amount');
        newRow.find('input[name$="[tax_1]"]').val('15');
        newRow.find('input[name$="[tax_2]"]').val('0');
        newRow.find('.row-total').text('0.00');

        // إضافة الصف الجديد
        $('.item-row:last').after(newRow);

        // إزالة تهيئة select2 القديمة
        newRow.find('.product-select').select2('destroy');

        // تهيئة select2 للصف الجديد
        newRow.find('.product-select').select2({
            placeholder: "اختر منتج",
            allowClear: true
        });

        rowCount++;
        updateTotals();
    }

    // إضافة صف جديد عند النقر على الزر
    $('#add-row').click(function() {
        addNewRow();
    });

    // تحديث السعر عند اختيار المنتج
    $(document).on('change', '.product-select', function() {
        const selectedOption = $(this).find(':selected');
        const price = selectedOption.data('price');
        const row = $(this).closest('tr');
        row.find('.price').val(price).trigger('change');
        row.find('.item-description').val(selectedOption.data('description') || '');
    });

    // تحديث المجاميع
    function updateTotals() {
        let subtotal = 0;
        let totalVAT = 0;
        let totalDiscount = 0;

        // حساب الضرائب بناءً على tax_type
        const taxType = $('#methodSelect').val();
        const taxRate = taxType == 1 ? 0.15 : 0;

        // حساب مجاميع الصفوف
        $('.item-row').each(function() {
            const quantity = parseFloat($(this).find('.quantity').val()) || 0;
            const price = parseFloat($(this).find('.price').val()) || 0;
            const rowTotal = quantity * price;

            // حساب الخصم للصف
            const discountValue = parseFloat($(this).find('.discount-value').val()) || 0;
            const discountType = $(this).find('.discount-type').val();
            let rowDiscount = 0;

            if (discountType === 'percentage') {
                rowDiscount = (rowTotal * discountValue) / 100;
            } else {
                rowDiscount = discountValue;
            }

            const rowAfterDiscount = rowTotal - rowDiscount;

            // حساب الضرائب للصف
            const tax1 = parseFloat($(this).find('input[name$="[tax_1]"]').val()) || 0;
            const tax2 = parseFloat($(this).find('input[name$="[tax_2]"]').val()) || 0;
            const tax1Amount = rowAfterDiscount * (tax1 / 100);
            const tax2Amount = rowAfterDiscount * (tax2 / 100);

            subtotal += rowTotal;
            totalDiscount += rowDiscount;
            totalVAT += tax1Amount + tax2Amount;

            $(this).find('.row-total').text((rowAfterDiscount + tax1Amount + tax2Amount).toFixed(2));
        });

        // حساب الخصم الإضافي
        const additionalDiscountValue = parseFloat($('input[name="discount_amount"]').val()) || 0;
        const additionalDiscountType = $('select[name="discount_type"]').val();
        let additionalDiscount = 0;

        if (additionalDiscountType === 'percentage') {
            additionalDiscount = (subtotal * additionalDiscountValue) / 100;
        } else {
            additionalDiscount = additionalDiscountValue;
        }

        totalDiscount += additionalDiscount;

        // حساب تكلفة الشحن وضريبته
        const shippingCost = parseFloat($('input[name="shipping_cost"]').val()) || 0;
        const shippingTax = taxType == 1 ? shippingCost * 0.15 : 0;
        totalVAT += shippingTax;

        // حساب المبلغ بعد الخصم
        const amountAfterDiscount = subtotal - totalDiscount;

        // حساب الإجمالي مع الضريبة والشحن
        const totalWithTax = amountAfterDiscount + totalVAT + shippingCost;

        // حساب الدفعة المقدمة
        const advancedPaymentValue = parseFloat($('#advanced-payment').val()) || 0;
        const advancedPaymentType = $('#amount').val();
        let advancedPayment = 0;

        if (advancedPaymentType === '2') { // نسبة مئوية
            advancedPayment = (totalWithTax * advancedPaymentValue) / 100;
        } else {
            advancedPayment = advancedPaymentValue;
        }

        // تحديث العرض في الجدول
        $('#subtotal').text(subtotal.toFixed(2));
        $('#total-discount').text(totalDiscount.toFixed(2));
        $('#total-tax').text(totalVAT.toFixed(2));
        $('#shipping-cost').text(shippingCost.toFixed(2));
        $('#grand-total').text(totalWithTax.toFixed(2));
        $('#next-payment').text(advancedPayment.toFixed(2));
        $('#due-value').text((totalWithTax - advancedPayment).toFixed(2));

        // تحديث ظهور الصفوف
        toggleRows();
    }

    // حذف صف
    $(document).on('click', '.remove-row', function(e) {
        e.preventDefault();
        if ($('.item-row').length > 1) {
            $(this).closest('tr.item-row').remove();
            updateTotals();
        } else {
            alert('يجب أن يكون هناك صف واحد على الأقل');
        }
    });

    // تحديث المجموع عند تغيير أي قيمة
    $(document).on('change keyup',
        '.quantity, .price, .discount-value, .discount-type, ' +
        'input[name$="[tax_1]"], input[name$="[tax_2]"], input[name="shipping_cost"], ' +
        'input[name="discount_amount"], select[name="discount_type"], #advanced-payment, #amount, #methodSelect',
        updateTotals
    );

    // تهيئة select2 للصف الأول
    $('.product-select').select2({
        placeholder: "اختر منتج",
        allowClear: true
    });

    // إخفاء الصفوف عند تحميل الصفحة
    toggleRows();

    // التحديث الأولي للمجاميع
    updateTotals();
});

// تهيئة المودال وإدارة التبويبات
document.addEventListener('DOMContentLoaded', function() {
    // التبويبات الرئيسية
    const mainTabs = document.querySelectorAll('.card-header-tabs .nav-link');
    mainTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            mainTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('.card-body > div[id^="section-"]').forEach(section => {
                section.classList.add('d-none');
            });

            const targetId = this.id.replace('tab-', 'section-');
            const targetSection = document.getElementById(targetId);
            if (targetSection) {
                targetSection.classList.remove('d-none');
            }
        });
    });

    // التبويبات في قسم المستندات
    const documentTabs = document.querySelectorAll('#section-documents .nav-tabs .nav-link');
    documentTabs.forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            documentTabs.forEach(t => t.classList.remove('active'));
            this.classList.add('active');

            document.querySelectorAll('#section-documents .inner-tab-content').forEach(content => {
                content.classList.add('d-none');
            });

            const targetId = 'inner-' + this.id;
            const targetContent = document.getElementById(targetId);
            if (targetContent) {
                targetContent.classList.remove('d-none');
            }
        });
    });

    // إعداد المودال
    var myModal = new bootstrap.Modal(document.getElementById('customFieldsModal'));

    document.querySelectorAll('.modal-footer .btn').forEach(button => {
        button.addEventListener('click', function() {
            if (this.classList.contains('btn-success')) {
                console.log('تم الضغط على زر الحفظ');
            } else if (this.classList.contains('btn-danger')) {
                console.log('تم الضغط على زر عدم الحفظ');
            }
            myModal.hide();
        });
    });

    // إدارة حقول الدفع
    const paidCheckbox = document.getElementById('paidCheck');
    const paymentFields = document.getElementById('paymentFields');

    if (paidCheckbox && paymentFields) {
        paidCheckbox.addEventListener('change', function() {
            paymentFields.style.display = this.checked ? 'block' : 'none';
        });
    }

});
document.querySelectorAll('.toggle-check').forEach((checkbox) => {
    checkbox.addEventListener('change', function() {
        const paymentFields = this.closest('.card-body').querySelector('.payment-fields');
        if (this.checked) {
            paymentFields.style.display = 'block'; // إظهار الحقول
        } else {
            paymentFields.style.display = 'none'; // إخفاء الحقول
        }
    });
});

$(document).ready(function() {
    // فلترة العملاء
    $('#searchClient').on('keyup', function() {
        var query = $(this).val();

        if (query.length > 0) {
            $('#loadingIcon').removeClass('d-none'); // عرض أيقونة التحميل

            $.ajax({
                url: "{{ route('invoices.index') }}",
                type: "GET",
                data: {
                    query: query,
                    type: 'client' // نحدد نوع البحث
                },
                success: function(response) {
                    $('#accountResults').html(response.options);
                },
                complete: function() {
                    $('#loadingIcon').addClass(
                        'd-none'); // إخفاء أيقونة التحميل بعد الانتهاء
                }
            });
        } else {
            $('#accountResults').html('');
            $('#loadingIcon').addClass('d-none'); // إخفاء الأيقونة إذا لم يكن هناك نص
        }
    });

    // فلترة الموظفين
    $('#searchEmployee').on('keyup', function() {
        var query = $(this).val();

        if (query.length > 0) {
            $('#loadingIconEmployee').removeClass('d-none'); // عرض أيقونة التحميل

            $.ajax({
                url: "{{ route('invoices.index') }}",
                type: "GET",
                data: {
                    query: query,
                    type: 'employee' // نحدد نوع البحث
                },
                success: function(response) {
                    $('#employeeResults').html(response.options);
                },
                complete: function() {
                    $('#loadingIconEmployee').addClass(
                        'd-none'); // إخفاء أيقونة التحميل بعد الانتهاء
                }
            });
        } else {
            $('#employeeResults').html('');
            $('#loadingIconEmployee').addClass('d-none'); // إخفاء الأيقونة إذا لم يكن هناك نص
        }
    });

    // فلترة المنتجات
    $('#searchProduct').on('keyup', function() {
        var query = $(this).val();

        if (query.length > 0) {
            $('#loadingIconProduct').removeClass('d-none'); // عرض أيقونة التحميل

            $.ajax({
                url: "{{ route('invoices.index') }}",
                type: "GET",
                data: {
                    query: query,
                    type: 'product' // نحدد نوع البحث
                },
                success: function(response) {
                    $('#productResults').html(response.options);
                },
                complete: function() {
                    $('#loadingIconProduct').addClass(
                        'd-none'); // إخفاء أيقونة التحميل بعد الانتهاء
                }
            });
        } else {
            $('#productResults').html('');
            $('#loadingIconProduct').addClass('d-none'); // إخفاء الأيقونة إذا لم يكن هناك نص
        }
    });

    // عند اختيار عنصر، يتم إدخاله في الحقل وإخفاء القائمة
    $(document).on('click', '.account-item', function() {
        $('#searchClient').val($(this).text());
        $('#accountResults').html('');
    });

    $(document).on('click', '.employee-item', function() {
        $('#searchEmployee').val($(this).text());
        $('#employeeResults').html('');
    });

    $(document).on('click', '.product-item', function() {
        $('#searchProduct').val($(this).text());
        $('#productResults').html('');
    });
});

