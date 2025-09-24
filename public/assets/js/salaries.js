$(document).ready(function() {
    // إضافة صف جديد للمستحقات
    $('.add-row-button-addition').on('click', function() {
        var newRow = $('#newRow').clone();
        newRow.removeAttr('id').show();
        $(this).closest('.table-responsive').find('table tbody').append(newRow);
        initializeSelect2();
    });

    // إضافة صف جديد للمستقطعات
    $('.add-row-button-deduction').on('click', function() {
        var newRow = $('#newRow2').clone();
        newRow.removeAttr('id').show();
        $(this).closest('.table-responsive').find('table tbody').append(newRow);
        initializeSelect2();
    });

    // حذف الصف
    $(document).on('click', '.remove-row', function() {
        $(this).closest('tr').remove();
        calculateTotals();
    });

    // تحديث الصيغة والمبلغ عند اختيار البند
    $(document).on('change', '.item-select', function() {
        var row = $(this).closest('tr');
        var selectedOption = $(this).find('option:selected');

        if (selectedOption.val() !== '') {
            row.find('.calculation-input').val(selectedOption.data('calculation'));
            row.find('.amount-input').val(selectedOption.data('amount'));
            calculateTotals();
        }
    });

    // تحديث المجاميع عند تغيير أي مبلغ
    $(document).on('input', 'input[name="basic_amount"], .amount-input', function() {
        calculateTotals();
    });

    // دالة حساب المجاميع
    function calculateTotals() {
        var totalAdditions = 0;
        var totalDeductions = 0;

        // إضافة الراتب الأساسي
        var basicSalary = parseFloat($('input[name="basic_amount"]').val()) || 0;
        totalAdditions += basicSalary;

        // حساب مجموع المستحقات
        $('select[name="addition_type[]"]').each(function() {
            var amount = parseFloat($(this).closest('tr').find('.amount-input').val()) || 0;
            totalAdditions += amount;
        });

        // حساب مجموع المستقطعات
        $('select[name="deduction_type[]"]').each(function() {
            var amount = parseFloat($(this).closest('tr').find('.amount-input').val()) || 0;
            totalDeductions += amount;
        });

        // تحديث الحقول
        updateTotalFields(totalAdditions, totalDeductions);
    }

    // دالة تحديث حقول المجاميع
    function updateTotalFields(totalAdditions, totalDeductions) {
        totalAdditions = parseFloat(totalAdditions).toFixed(2);
        totalDeductions = parseFloat(totalDeductions).toFixed(2);
        var netSalary = (parseFloat(totalAdditions) - parseFloat(totalDeductions)).toFixed(2);

        // تحديث الحقول مع تنسيق الأرقام
        $('input[name="total_salary"]').val(formatNumber(totalAdditions));
        $('input[name="total_deductions"]').val(formatNumber(totalDeductions));
        $('input[name="net_salary"]').val(formatNumber(netSalary));
    }

    // دالة تنسيق الأرقام بالإنجليزي
    function formatNumber(number) {
        return new Intl.NumberFormat('en-US', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        }).format(number);
    }

    // التحقق من المدخلات الرقمية
    $(document).on('input', '.amount-input', function() {
        var value = $(this).val();
        // السماح فقط بالأرقام والنقطة العشرية
        if (!/^\d*\.?\d*$/.test(value)) {
            $(this).val(value.replace(/[^\d.]/g, ''));
        }
        // منع تكرار النقطة العشرية
        if ((value.match(/\./g) || []).length > 1) {
            $(this).val(value.replace(/\.+$/, ''));
        }
    });

    // تهيئة Select2
    function initializeSelect2() {
        if ($.fn.select2) {
            $('.item-select').select2({
                placeholder: "اختر البند",
                allowClear: true,
                width: '100%'
            });
        }
    }



    // تهيئة Select2 عند تحميل الصفحة
    initializeSelect2();

    // تأخير حساب المجاميع حتى اكتمال تحميل الصفحة
    setTimeout(calculateTotals, 500);
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Set duration as default
    document.getElementById('duration_radio').checked = true;
    document.getElementById('enddate_radio').checked = false;

    // Initial toggle of fields
    toggleFields();

    // Add event listeners to radio buttons
    document.getElementById('duration_radio').addEventListener('change', toggleFields);
    document.getElementById('enddate_radio').addEventListener('change', toggleFields);
});

function toggleFields() {
    const durationRadio = document.getElementById('duration_radio');
    const endDateRadio = document.getElementById('enddate_radio');

    // Get all duration inputs
    const durationInputs = document.querySelectorAll('#duration-inputs input, #duration-inputs select');
    const endDateInput = document.getElementById('end_date');

    if (durationRadio.checked) {
        // Enable duration inputs, disable end date
        durationInputs.forEach(input => {
            input.removeAttribute('disabled');
            input.style.backgroundColor = '#ffffff';
        });
        endDateInput.setAttribute('disabled', 'disabled');
        endDateInput.style.backgroundColor = '#e9ecef';
        endDateInput.value = '';
    } else {
        // Enable end date, disable duration inputs
        durationInputs.forEach(input => {
            input.setAttribute('disabled', 'disabled');
            input.style.backgroundColor = '#e9ecef';
        });
        endDateInput.removeAttribute('disabled');
        endDateInput.style.backgroundColor = '#ffffff';
    }
}
