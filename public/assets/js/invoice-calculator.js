/**
 * نظام حساب الفواتير المحسن - مُصحح ومحدث
 * إصلاح مشاكل كارد المورد والإيداع
 * @version 2.2 - إصلاح الأخطاء الجديدة
 */

class InvoiceCalculator {
    constructor(options = {}) {
        this.options = {
            currency: 'ر.س',
            locale: 'ar',
            calculateDelay: 200,
            autoSave: true,
            autoSaveInterval: 300000, // 5 دقائق
            ...options
        };

        this.calculateTimer = null;
        this.hasUnsavedChanges = false;
        this.isInitialized = false;

        // ربط السياق
        this.init = this.init.bind(this);
        this.calculateTotals = this.calculateTotals.bind(this);
        this.debouncedCalculate = this.debouncedCalculate.bind(this);
    }

    /**
     * تهيئة النظام
     */
    init() {
        if (this.isInitialized) return;

        try {
            this.setupNumberInputs();
            this.bindEvents();
            this.loadUserPreferences();

            // التحقق من وجود مورد محدد مسبقاً وإظهار كارد الرصيد
            this.checkPreselectedSupplier();

            this.calculateTotals();

            if (this.options.autoSave) {
                this.setupAutoSave();
            }

            this.isInitialized = true;
            console.log('✅ نظام حساب الفواتير جاهز');
            this.showNotification('نظام الفواتير جاهز للاستخدام', 'success', 2000);
        } catch (error) {
            console.error('خطأ في تهيئة النظام:', error);
            this.showNotification('حدث خطأ في تهيئة النظام', 'error');
        }
    }

    /**
     * التحقق من المورد المختار مسبقاً - مُصحح
     */
/**
 * التحقق من المورد المختار مسبقاً - مُصحح
 */
checkPreselectedSupplier() {
    const supplierSelect = document.getElementById('clientSelect');
    const balanceCard = document.getElementById('supplierBalanceCard');

    // إخفاء الكارد بشكل افتراضي دائماً عند فتح الصفحة
    if (balanceCard) {
        balanceCard.style.display = 'none';
    }

    // لا نعرض كارد الرصيد عند فتح الصفحة حتى لو كان هناك مورد محدد
    // سيتم إظهاره فقط عند اختيار المورد من القائمة
}

    /**
     * إعداد حقول الأرقام لتحويل الأرقام العربية
     */
    setupNumberInputs() {
        const numberInputs = document.querySelectorAll('input[type="number"]');

        numberInputs.forEach(input => {
            // تطبيق التنسيق
            input.style.direction = 'ltr';
            input.style.textAlign = 'left';
            input.style.fontFamily = 'Arial, sans-serif';

            // إزالة المستمعين السابقين لتجنب التكرار
            input.removeEventListener('input', this.handleNumberInput);
            input.removeEventListener('keypress', this.validateNumberInput);

            // إضافة المستمعين الجدد
            input.addEventListener('input', (e) => this.handleNumberInput(e));
            input.addEventListener('keypress', (e) => this.validateNumberInput(e));

            // تحويل القيم الموجودة
            if (input.value) {
                this.convertArabicToEnglish(input);
            }
        });
    }

    /**
     * معالجة إدخال الأرقام
     */
    handleNumberInput(e) {
        this.convertArabicToEnglish(e.target);
    }

    /**
     * تحويل الأرقام العربية إلى إنجليزية
     */
    convertArabicToEnglish(input) {
        if (!input.value) return;

        const arabicNumbers = '٠١٢٣٤٥٦٧٨٩';
        const englishNumbers = '0123456789';

        let value = input.value.toString();

        // تحويل الأرقام العربية
        for (let i = 0; i < arabicNumbers.length; i++) {
            const regex = new RegExp(arabicNumbers[i], 'g');
            value = value.replace(regex, englishNumbers[i]);
        }

        // تنظيف القيمة
        value = value.replace(/[^\d.-]/g, '');

        // التأكد من نقطة عشرية واحدة فقط
        const parts = value.split('.');
        if (parts.length > 2) {
            value = parts[0] + '.' + parts.slice(1).join('');
        }

        // التأكد من إشارة سالب واحدة في البداية فقط
        if (value.indexOf('-') > 0) {
            value = value.replace(/-/g, '');
        }

        input.value = value;
    }

    /**
     * التحقق من صحة إدخال الأرقام
     */
    validateNumberInput(e) {
        const allowedKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '.', '-'];
        const arabicNumbers = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        const controlKeys = ['Backspace', 'Delete', 'Tab', 'Enter', 'ArrowLeft', 'ArrowRight', 'Home', 'End'];

        if (allowedKeys.includes(e.key) ||
            arabicNumbers.includes(e.key) ||
            controlKeys.includes(e.key) ||
            e.ctrlKey || e.altKey) {
            return true;
        }

        e.preventDefault();
        return false;
    }

    /**
     * ربط الأحداث - مُصحح
     */
    bindEvents() {
        // أحداث الحساب الفوري
        document.addEventListener('input', (e) => {
            if (this.isCalculationField(e.target)) {
                this.convertArabicToEnglish(e.target);
                this.debouncedCalculate();
                this.hasUnsavedChanges = true;
            }
        });

        // أحداث التغيير - مُصحح لتشمل المورد
        document.addEventListener('change', (e) => {
            if (this.isCalculationField(e.target) ||
                e.target.classList.contains('product-select') ||
                e.target.classList.contains('tax-select') ||
                e.target.classList.contains('discount-type') ||
                e.target.id === 'clientSelect') { // إضافة معالج المورد

                this.handleFieldChange(e.target);
                this.debouncedCalculate();
                this.hasUnsavedChanges = true;
            }
        });

        // أحداث النقر
        document.addEventListener('click', (e) => this.handleClick(e));

        // اختصارات لوحة المفاتيح
        document.addEventListener('keydown', (e) => this.handleKeyboard(e));

        // تحذير من المغادرة
        window.addEventListener('beforeunload', (e) => {
            if (this.hasUnsavedChanges) {
                e.preventDefault();
                e.returnValue = 'لديك تغييرات غير محفوظة. هل تريد المغادرة؟';
                return e.returnValue;
            }
        });

        // إرسال النموذج
        const form = document.getElementById('invoice-form');
        if (form) {
            form.addEventListener('submit', () => {
                this.hasUnsavedChanges = false;
            });
        }

        // أحداث checkbox للدفع - مُصححة
        this.bindPaymentEvents();
    }

    /**
     * ربط أحداث الدفع - مُصحح للإيداع
     */
    bindPaymentEvents() {
        // الدفع المقدم - مُصحح
        const advanceToggle = document.querySelector('.advance-payment-toggle');
        if (advanceToggle) {
            advanceToggle.addEventListener('change', (e) => {
                const fields = document.querySelector('.advance-payment-fields');
                if (e.target.checked) {
                    fields.style.display = 'block';
                } else {
                    fields.style.display = 'none';
                    // مسح قيمة الدفعة المقدمة عند إلغاء التفعيل
                    const advanceInput = document.querySelector("[name='advance_payment']");
                    if (advanceInput) {
                        advanceInput.value = '0';
                    }
                }
                this.debouncedCalculate();
            });
        }

        // الدفع الكامل
        const fullPaymentToggle = document.querySelector('.payment-toggle');
        if (fullPaymentToggle) {
            fullPaymentToggle.addEventListener('change', (e) => {
                const fields = document.querySelector('.full-payment-fields');
                const amountInput = document.getElementById('paid-amount-input');

                if (e.target.checked) {
                    fields.style.display = 'block';
                    // تعيين المبلغ الكامل كقيمة مدفوعة
                    if (amountInput) {
                        const grandTotal = this.getFloatValue(document.getElementById('grand-total'));
                        amountInput.value = grandTotal.toFixed(2);
                        this.convertArabicToEnglish(amountInput);
                    }
                } else {
                    fields.style.display = 'none';
                    // إعادة تعيين المبلغ المدفوع
                    if (amountInput && !advanceToggle?.checked) {
                        amountInput.value = '0';
                    }
                }
                this.debouncedCalculate();
            });
        }

        // مراقبة تغيير حقل الدفعة المقدمة - مُصحح
        const advancePaymentInput = document.querySelector("[name='advance_payment']");
        if (advancePaymentInput) {
            advancePaymentInput.addEventListener('input', () => {
                this.convertArabicToEnglish(advancePaymentInput);
                this.debouncedCalculate();
            });
        }

        // تحديث المبلغ المدفوع عند تغيير الإجمالي (للدفع الكامل)
        const observer = new MutationObserver(() => {
            const fullPaymentToggle = document.querySelector('.payment-toggle');
            const amountInput = document.getElementById('paid-amount-input');

            if (fullPaymentToggle?.checked && amountInput) {
                const grandTotal = this.getFloatValue(document.getElementById('grand-total'));
                amountInput.value = grandTotal.toFixed(2);
                this.convertArabicToEnglish(amountInput);
            }
        });

        const grandTotalElement = document.getElementById('grand-total');
        if (grandTotalElement) {
            observer.observe(grandTotalElement, { childList: true, characterData: true, subtree: true });
        }
    }

    /**
     * التحقق من حقول الحساب - مُصحح
     */
    isCalculationField(element) {
        const calculationClasses = ['quantity', 'price', 'discount-amount', 'discount-percentage'];
        const calculationNames = ['discount_amount', 'adjustment_value', 'paid_amount', 'shipping_cost', 'advance_payment'];
        const calculationIds = ['paid-amount-input', 'advance-payment', 'debugAdvancePayment'];

        return calculationClasses.some(cls => element.classList.contains(cls)) ||
               calculationNames.some(name => element.name === name) ||
               calculationIds.some(id => element.id === id) ||
               element.classList.contains('tax-select');
    }

    /**
     * معالجة تغيير الحقول - مُصحح
     */
    handleFieldChange(element) {
        if (element.classList.contains('product-select')) {
            this.updateProductPrice(element);
        }

        // إصلاح معالج اختيار المورد
        if (element.id === 'clientSelect') {
            this.showSupplierBalance(element);
        }

        if (element.classList.contains('discount-type')) {
            this.toggleDiscountType(element);
        }

        if (element.classList.contains('tax-select')) {
            this.updateTaxHiddenField(element);
        }
    }

    /**
     * معالجة النقرات
     */
    handleClick(e) {
        // إضافة صف جديد
        if (e.target.closest('#add-row')) {
            e.preventDefault();
            this.addNewRow();
        }

        // حذف صف
        if (e.target.closest('.remove-row')) {
            e.preventDefault();
            this.removeRow(e.target.closest('.item-row'));
        }

        // التبويبات
        const tabButtons = ['tab-discount', 'tab-deposit', 'tab-shipping', 'tab-documents'];
        if (tabButtons.includes(e.target.id)) {
            e.preventDefault();
            this.switchTab(e.target.id);
        }

        // التبويبات الفرعية
        if (e.target.id === 'tab-new-document' || e.target.id === 'tab-uploaded-documents') {
            e.preventDefault();
            this.switchDocumentTab(e.target.id);
        }
    }

    /**
     * معالجة لوحة المفاتيح
     */
    handleKeyboard(e) {
        // Ctrl+S للحفظ
        if (e.ctrlKey && e.key === 's') {
            e.preventDefault();
            document.getElementById('invoice-form')?.submit();
        }

        // F2 لإضافة صف جديد
        if (e.key === 'F2') {
            e.preventDefault();
            this.addNewRow();
        }

        // F3 للمعاينة السريعة
        if (e.key === 'F3') {
            e.preventDefault();
            this.showQuickPreview();
        }

        // Escape لإلغاء العمليات
        if (e.key === 'Escape') {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        }
    }

    /**
     * حساب مؤجل لتحسين الأداء
     */
    debouncedCalculate() {
        clearTimeout(this.calculateTimer);
        this.calculateTimer = setTimeout(() => {
            this.calculateTotals();
        }, this.options.calculateDelay);
    }

    /**
     * الحساب الرئيسي للإجماليات - مُصحح للإيداع
     */
    calculateTotals() {
        try {
            let subtotal = 0;
            let totalItemDiscount = 0;
            let totalItemTax = 0;
            let taxDetails = {};

            // مسح الصفوف الديناميكية السابقة
            document.querySelectorAll('.dynamic-tax-row').forEach(row => row.remove());

            // حساب كل صف من العناصر
            document.querySelectorAll('.item-row').forEach(row => {
                const itemCalc = this.calculateItemRow(row);

                subtotal += itemCalc.subtotal;
                totalItemDiscount += itemCalc.discount;
                totalItemTax += itemCalc.tax;

                // دمج تفاصيل الضرائب
                Object.keys(itemCalc.taxDetails).forEach(taxName => {
                    if (!taxDetails[taxName]) {
                        taxDetails[taxName] = 0;
                    }
                    taxDetails[taxName] += itemCalc.taxDetails[taxName];
                });

                // تحديث إجمالي الصف
                this.updateRowTotal(row, itemCalc.rowTotal);
            });

            // حساب الخصم الإضافي
            const additionalDiscount = this.calculateAdditionalDiscount(subtotal);

            // حساب التسوية
            const adjustmentAmount = this.calculateAdjustment();

            // حساب الشحن
            const shipping = this.calculateShipping(taxDetails);

            // حساب الإجمالي قبل الدفعة المقدمة
            const totalDiscount = totalItemDiscount + additionalDiscount;
            const totalTax = totalItemTax + shipping.tax;
            const grandTotal = subtotal - totalDiscount + adjustmentAmount + shipping.cost + totalTax;

            // حساب الدفعة المقدمة - مُصحح
            const advancePayment = this.calculateAdvancePayment(grandTotal);

            // حساب المبلغ المدفوع
            const paidAmount = this.calculatePaidAmount();

            // حساب المبلغ المتبقي - مُصحح
            const remainingAmount = grandTotal - paidAmount - advancePayment;

            // تحديث العرض - مُصحح
            this.updateDisplay({
                subtotal: subtotal,
                totalDiscount: totalDiscount,
                shippingCost: shipping.cost,
                advancePayment: advancePayment,
                paidAmount: paidAmount,
                grandTotal: grandTotal,
                remainingAmount: remainingAmount
            });

            // إضافة الصفوف الديناميكية
            this.addDynamicRows({
                additionalDiscount: additionalDiscount,
                adjustmentAmount: adjustmentAmount,
                taxDetails: taxDetails
            });

        } catch (error) {
            console.error('خطأ في الحسابات:', error);
            this.showNotification('حدث خطأ في الحسابات', 'error');
        }
    }

    /**
     * حساب صف عنصر واحد
     */
    calculateItemRow(row) {
        const quantity = this.getFloatValue(row.querySelector('.quantity')) || 0;
        const unitPrice = this.getFloatValue(row.querySelector('.price')) || 0;
        const subtotal = quantity * unitPrice;

        // حساب خصم العنصر
        const discount = this.calculateItemDiscount(row, subtotal);

        // حساب ضرائب العنصر
        const taxResult = this.calculateItemTaxes(row, subtotal - discount);

        const rowTotal = subtotal - discount + taxResult.totalTax;

        return {
            subtotal: subtotal,
            discount: discount,
            tax: taxResult.totalTax,
            taxDetails: taxResult.taxDetails,
            rowTotal: rowTotal
        };
    }

    /**
     * حساب خصم العنصر
     */
    calculateItemDiscount(row, subtotal) {
        const discountType = row.querySelector('.discount-type')?.value || 'amount';

        if (discountType === 'percentage') {
            const percentage = this.getFloatValue(row.querySelector('.discount-percentage')) || 0;
            return Math.min((subtotal * percentage) / 100, subtotal);
        } else {
            const amount = this.getFloatValue(row.querySelector('.discount-amount')) || 0;
            return Math.min(amount, subtotal);
        }
    }

    /**
     * حساب ضرائب العنصر
     */
    calculateItemTaxes(row, taxableAmount) {
        let totalTax = 0;
        let taxDetails = {};

        // الضريبة الأولى
        const tax1Result = this.calculateSingleTax(
            row.querySelector("[name^='items'][name$='[tax_1]']"),
            taxableAmount
        );

        // الضريبة الثانية
        const tax2Result = this.calculateSingleTax(
            row.querySelector("[name^='items'][name$='[tax_2]']"),
            taxableAmount
        );

        if (tax1Result.amount > 0) {
            totalTax += tax1Result.amount;
            taxDetails[tax1Result.name] = (taxDetails[tax1Result.name] || 0) + tax1Result.amount;
        }

        if (tax2Result.amount > 0) {
            totalTax += tax2Result.amount;
            taxDetails[tax2Result.name] = (taxDetails[tax2Result.name] || 0) + tax2Result.amount;
        }

        return { totalTax: totalTax, taxDetails: taxDetails };
    }

    /**
     * حساب ضريبة واحدة
     */
    calculateSingleTax(taxSelect, amount) {
        if (!taxSelect || !taxSelect.value) {
            return { amount: 0, name: '', type: '' };
        }

        const selectedOption = taxSelect.options[taxSelect.selectedIndex];
        const taxValue = parseFloat(selectedOption.value) || 0;
        const taxName = selectedOption.dataset.name || selectedOption.text;
        const taxType = selectedOption.dataset.type || 'excluded';

        let taxAmount = 0;

        if (taxValue > 0) {
            if (taxType === 'included') {
                // ضريبة متضمنة في السعر
                taxAmount = amount - (amount / (1 + (taxValue / 100)));
            } else {
                // ضريبة مضافة على السعر
                taxAmount = (amount * taxValue) / 100;
            }
        }

        return {
            amount: taxAmount,
            name: taxName,
            type: taxType
        };
    }

    /**
     * حساب الخصم الإضافي
     */
    calculateAdditionalDiscount(subtotal) {
        const discountAmount = this.getFloatValue(document.querySelector("[name='discount_amount']")) || 0;
        const discountType = document.querySelector("[name='discount_type']")?.value || 'amount';

        if (discountAmount <= 0) return 0;

        if (discountType === 'percentage') {
            return Math.min((subtotal * discountAmount) / 100, subtotal);
        } else {
            return Math.min(discountAmount, subtotal);
        }
    }

    /**
     * حساب التسوية
     */
    calculateAdjustment() {
        const adjustmentValue = this.getFloatValue(document.querySelector("[name='adjustment_value']")) || 0;
        const adjustmentType = document.querySelector("[name='adjustment_type']")?.value || 'discount';

        if (adjustmentValue <= 0) return 0;

        return adjustmentType === 'discount' ? -adjustmentValue : adjustmentValue;
    }

    /**
     * حساب الدفعة المقدمة - مُصحح للعرض
     */
    calculateAdvancePayment(grandTotal) {
        const advanceAmount = this.getFloatValue(document.querySelector("[name='advance_payment']")) ||
                              this.getFloatValue(document.getElementById('debugAdvancePayment')) || 0;
        const advanceType = document.querySelector("[name='advance_payment_type']")?.value || 'amount';
        const isAdvancePaid = document.querySelector('.advance-payment-toggle')?.checked ||
                              document.querySelector("[name='is_advance_paid']")?.checked || false;

        if (!isAdvancePaid || advanceAmount <= 0) return 0;

        let calculatedAdvance = 0;
        if (advanceType === 'percentage') {
            calculatedAdvance = Math.min((grandTotal * advanceAmount) / 100, grandTotal);
        } else {
            calculatedAdvance = Math.min(advanceAmount, grandTotal);
        }

        return calculatedAdvance;
    }

    /**
     * حساب المبلغ المدفوع
     */
    calculatePaidAmount() {
        const paidAmountInput = document.getElementById('paid-amount-input');
        return this.getFloatValue(paidAmountInput) || 0;
    }

    /**
     * حساب الشحن
     */
    calculateShipping(taxDetails) {
        const shippingCost = this.getFloatValue(document.querySelector("[name='shipping_cost']")) || 0;
        let shippingTax = 0;

        if (shippingCost > 0) {
            const shippingTaxSelect = document.querySelector("[name='shipping_tax_id']");

            if (shippingTaxSelect && shippingTaxSelect.value) {
                const selectedOption = shippingTaxSelect.options[shippingTaxSelect.selectedIndex];
                const taxRate = parseFloat(selectedOption.dataset.rate) || 0;
                const taxName = selectedOption.text + " (شحن)";

                if (taxRate > 0) {
                    shippingTax = (shippingCost * taxRate) / 100;

                    // إضافة ضريبة الشحن لتفاصيل الضرائب
                    if (!taxDetails[taxName]) {
                        taxDetails[taxName] = 0;
                    }
                    taxDetails[taxName] += shippingTax;
                }
            }
        }

        return { cost: shippingCost, tax: shippingTax };
    }

    /**
     * الحصول على قيمة رقمية من عنصر
     */
    getFloatValue(element) {
        if (!element) return 0;
        const value = element.textContent || element.value || '0';
        return parseFloat(value.replace(/[^\d.-]/g, '')) || 0;
    }

    /**
     * تحديث إجمالي الصف
     */
    updateRowTotal(row, total) {
        const totalElement = row.querySelector('.row-total');
        if (totalElement) {
            totalElement.textContent = total.toFixed(2);
        }
    }

    /**
     * تحديث العرض - مُصحح للإيداع
     */
    updateDisplay(data) {
        this.updateElement('subtotal', data.subtotal);
        this.updateElement('total-discount', data.totalDiscount);
        this.updateElement('shipping-cost', data.shippingCost);
        this.updateElement('advance-payment', data.advancePayment);
        this.updateElement('grand-total', data.grandTotal);
        this.updateElement('remaining-amount', data.remainingAmount);

        // تحديث المبلغ المدفوع في الجدول
        if (data.paidAmount > 0) {
            this.updateElement('paid-amount-display', data.paidAmount);
        } else {
            this.updateElement('paid-amount-display', 0);
        }
    }

    /**
     * تحديث عنصر في DOM
     */
    updateElement(id, value) {
        const element = document.getElementById(id);
        if (element) {
            element.textContent = value.toFixed(2);
        }
    }

    /**
     * إضافة الصفوف الديناميكية
     */
    addDynamicRows(data) {
        const taxRowsContainer = document.getElementById('tax-rows');
        if (!taxRowsContainer) return;

        const insertPoint = taxRowsContainer.querySelector('tr:last-child');

        // إضافة صف الخصم الإضافي
        if (data.additionalDiscount > 0) {
            this.insertTaxRow(insertPoint, 'خصم إضافي', data.additionalDiscount, 'text-danger', '-');
        }

        // إضافة صف التسوية
        if (data.adjustmentAmount !== 0) {
            const adjustmentLabel = data.adjustmentAmount > 0 ? 'إضافة' : 'خصم';
            const cssClass = data.adjustmentAmount > 0 ? 'text-success' : 'text-danger';
            const sign = data.adjustmentAmount > 0 ? '+' : '-';
            this.insertTaxRow(insertPoint, adjustmentLabel, Math.abs(data.adjustmentAmount), cssClass, sign);
        }

        // إضافة صفوف الضرائب
        Object.entries(data.taxDetails).forEach(([taxName, taxAmount]) => {
            if (taxAmount > 0) {
                this.insertTaxRow(insertPoint, taxName, taxAmount, 'text-info', '+');
            }
        });
    }

    /**
     * إدراج صف ضريبة
     */
    insertTaxRow(insertPoint, label, amount, cssClass, sign) {
        const row = document.createElement('tr');
        row.classList.add('dynamic-tax-row');
        row.innerHTML = `
            <td colspan="7" class="text-right">${label}</td>
            <td><span class="${cssClass}">${sign}${amount.toFixed(2)}</span> ${this.options.currency}</td>
            <td></td>
        `;
        insertPoint.parentNode.insertBefore(row, insertPoint);
    }

    /**
     * إضافة صف جديد
     */
    addNewRow() {
        const table = document.querySelector('#items-table tbody');
        const rowCount = table.children.length;
        const firstRow = table.children[0];

        if (!firstRow) return;

        const newRow = firstRow.cloneNode(true);

        // تحديث أسماء الحقول
        newRow.querySelectorAll('input, select').forEach(input => {
            if (input.name) {
                input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);

                // إعادة تعيين القيم
                if (input.type !== 'hidden') {
                    if (input.classList.contains('quantity')) {
                        input.value = '1';
                    } else if (input.type === 'number') {
                        input.value = '0';
                    } else {
                        input.value = '';
                    }
                }
            }
        });

        // إعادة تهيئة Select2 إذا كان متوفراً
        const select2Elements = newRow.querySelectorAll('.select2');
        select2Elements.forEach(element => {
            if (typeof $ !== 'undefined' && $.fn.select2) {
                $(element).removeClass('select2-hidden-accessible').next().remove().end().select2();
            }
        });

        table.appendChild(newRow);
        this.setupNumberInputs(); // إعادة تهيئة حقول الأرقام للصف الجديد
        this.calculateTotals();

        this.showNotification('تم إضافة صف جديد', 'success');
    }

    /**
     * حذف صف
     */
    removeRow(row) {
        const totalRows = document.querySelectorAll('.item-row').length;

        if (totalRows <= 1) {
            this.showNotification('لا يمكن حذف جميع العناصر', 'warning');
            return;
        }

        row.remove();
        this.calculateTotals();
        this.showNotification('تم حذف الصف', 'info');
    }

    /**
     * تحديث سعر المنتج
     */
    updateProductPrice(selectElement) {
        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        const row = selectElement.closest('.item-row');
        const priceInput = row.querySelector('.price');

        if (priceInput) {
            priceInput.value = price;
            this.convertArabicToEnglish(priceInput);
            this.calculateTotals();
        }
    }

    /**
     * عرض رصيد المورد - مُصحح
     */
    showSupplierBalance(selectElement) {
        const balanceCard = document.getElementById('supplierBalanceCard');

        // التحقق من أن المورد تم اختياره فعلياً وليس مجرد القيمة الافتراضية
        if (!selectElement ||
            !selectElement.value ||
            selectElement.value === '' ||
            selectElement.value === '0' ||
            selectElement.selectedIndex === 0) {
            if (balanceCard) {
                balanceCard.style.display = 'none';
            }
            return;
        }

        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const supplierName = selectedOption.text;
        const supplierBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;

        const nameElement = document.getElementById('supplierName');
        const balanceElement = document.getElementById('supplierBalance');
        const statusElement = document.getElementById('balanceStatus');

        if (nameElement) nameElement.textContent = supplierName;
        if (balanceElement) balanceElement.textContent = Math.abs(supplierBalance).toFixed(2);

        if (statusElement && balanceElement) {
            if (supplierBalance > 0) {
                this.setBalanceStatus(statusElement, balanceElement, 'دائن', '#4CAF50');
            } else if (supplierBalance < 0) {
                this.setBalanceStatus(statusElement, balanceElement, 'مدين', '#f44336');
            } else {
                this.setBalanceStatus(statusElement, balanceElement, 'متوازن', '#FFC107');
            }
        }

        // إظهار الكارد مع تأثير
        if (balanceCard) {
            balanceCard.style.display = 'block';
            balanceCard.style.opacity = '0';
            balanceCard.style.transform = 'translateY(-20px)';

            setTimeout(() => {
                balanceCard.style.transition = 'all 0.3s ease';
                balanceCard.style.opacity = '1';
                balanceCard.style.transform = 'translateY(0)';
            }, 10);
        }
    }

    /**
     * تعيين حالة الرصيد
     */
    setBalanceStatus(statusElement, balanceElement, text, color) {
        statusElement.textContent = text;
        statusElement.style.color = color;
        balanceElement.style.color = color;
    }

    /**
     * تبديل نوع الخصم
     */
    toggleDiscountType(selectElement) {
        const row = selectElement.closest('.item-row');
        const discountAmountInput = row.querySelector('.discount-amount');
        const discountPercentageInput = row.querySelector('.discount-percentage');

        if (selectElement.value === 'percentage') {
            if (discountAmountInput) discountAmountInput.style.display = 'none';
            if (discountPercentageInput) discountPercentageInput.style.display = 'block';
        } else {
            if (discountAmountInput) discountAmountInput.style.display = 'block';
            if (discountPercentageInput) discountPercentageInput.style.display = 'none';
        }
    }

    /**
     * تحديث حقل الضريبة المخفي
     */
    updateTaxHiddenField(selectElement) {
        const row = selectElement.closest('.item-row');
        const taxType = selectElement.getAttribute('data-target');
        const hiddenInput = row.querySelector(`input[name^="items"][name$="[${taxType}_id]"]`);

        if (hiddenInput) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            hiddenInput.value = selectedOption.getAttribute('data-id') || '';
        }
    }

    /**
     * تبديل التبويبات
     */
    switchTab(tabId) {
        // إزالة الفئة النشطة من جميع التبويبات
        document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-section').forEach(section => section.classList.add('d-none'));

        // إضافة الفئة النشطة للتبويب المحدد
        const targetTab = document.getElementById(tabId);
        if (targetTab) targetTab.classList.add('active');

        // إظهار القسم المقابل
        const sectionMap = {
            'tab-discount': 'section-discount',
            'tab-deposit': 'section-deposit',
            'tab-shipping': 'section-shipping',
            'tab-documents': 'section-documents'
        };

        const targetSection = sectionMap[tabId];
        if (targetSection) {
            const section = document.getElementById(targetSection);
            if (section) section.classList.remove('d-none');
        }
    }

    /**
     * تبديل تبويبات المستندات
     */
    switchDocumentTab(tabId) {
        document.querySelectorAll('#tab-new-document, #tab-uploaded-documents').forEach(tab =>
            tab.classList.remove('active'));
        document.querySelectorAll('#content-new-document, #content-uploaded-documents').forEach(content =>
            content.classList.add('d-none'));

        const targetTab = document.getElementById(tabId);
        if (targetTab) targetTab.classList.add('active');

        if (tabId === 'tab-new-document') {
            const content = document.getElementById('content-new-document');
            if (content) content.classList.remove('d-none');
        } else {
            const content = document.getElementById('content-uploaded-documents');
            if (content) content.classList.remove('d-none');
        }
    }

    /**
     * معاينة سريعة
     */
    showQuickPreview() {
        const supplierSelect = document.getElementById('clientSelect');
        const supplier = supplierSelect ? supplierSelect.options[supplierSelect.selectedIndex].text : 'غير محدد';
        const itemsCount = document.querySelectorAll('.item-row').length;
        const subtotal = this.getFloatValue(document.getElementById('subtotal'));
        const discount = this.getFloatValue(document.getElementById('total-discount'));
        const total = this.getFloatValue(document.getElementById('grand-total'));
        const advancePayment = this.getFloatValue(document.getElementById('advance-payment'));
        const paidAmount = this.getFloatValue(document.getElementById('paid-amount-display'));
        const remainingAmount = this.getFloatValue(document.getElementById('remaining-amount'));

        const message = `
            <div class="text-right" style="direction: rtl;">
                <p><strong>المورد:</strong> ${supplier}</p>
                <p><strong>عدد العناصر:</strong> ${itemsCount}</p>
                <p><strong>المجموع الفرعي:</strong> ${subtotal.toFixed(2)} ر.س</p>
                <p><strong>إجمالي الخصومات:</strong> ${discount.toFixed(2)} ر.س</p>
                <p><strong>الدفعة المقدمة:</strong> ${advancePayment.toFixed(2)} ر.س</p>
                <p><strong>المبلغ المدفوع:</strong> ${paidAmount.toFixed(2)} ر.س</p>
                <p><strong>المجموع الكلي:</strong> ${total.toFixed(2)} ر.س</p>
                <p><strong>المبلغ المتبقي:</strong> ${remainingAmount.toFixed(2)} ر.س</p>
            </div>
        `;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'معاينة سريعة',
                html: message,
                icon: 'info',
                confirmButtonText: 'إغلاق'
            });
        } else {
            alert('معاينة سريعة:\n' +
                  'المورد: ' + supplier + '\n' +
                  'عدد العناصر: ' + itemsCount + '\n' +
                  'المجموع الفرعي: ' + subtotal.toFixed(2) + ' ر.س\n' +
                  'إجمالي الخصومات: ' + discount.toFixed(2) + ' ر.س\n' +
                  'الدفعة المقدمة: ' + advancePayment.toFixed(2) + ' ر.س\n' +
                  'المبلغ المدفوع: ' + paidAmount.toFixed(2) + ' ر.س\n' +
                  'المجموع الكلي: ' + total.toFixed(2) + ' ر.س\n' +
                  'المبلغ المتبقي: ' + remainingAmount.toFixed(2) + ' ر.س');
        }
    }

    /**
     * عرض الإشعارات
     */
    showNotification(message, type = 'info', duration = 3000) {
        console.log(`${type.toUpperCase()}: ${message}`);

        // إذا كان SweetAlert متوفر
        if (typeof Swal !== 'undefined') {
            const iconMap = {
                'success': 'success',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };

            Swal.fire({
                text: message,
                icon: iconMap[type] || 'info',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: duration,
                timerProgressBar: true
            });
        } else {
            // إنشاء إشعار مخصص
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} notification-toast`;
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 9999;
                min-width: 250px;
                max-width: 400px;
                opacity: 0;
                transform: translateX(100%);
                transition: all 0.3s ease;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                padding: 15px;
                margin-bottom: 10px;
            `;

            const iconMap = {
                'success': 'check-circle',
                'error': 'exclamation-circle',
                'warning': 'exclamation-triangle',
                'info': 'info-circle'
            };

            notification.innerHTML = `
                <i class="fa fa-${iconMap[type] || 'info-circle'}" style="margin-left: 8px;"></i>
                ${message}
                <button type="button" class="close" onclick="this.parentElement.remove()" style="background: none; border: none; font-size: 18px; margin-right: 10px; cursor: pointer;">
                    <span>&times;</span>
                </button>
            `;

            document.body.appendChild(notification);

            // تأثير الظهور
            setTimeout(() => {
                notification.style.opacity = '1';
                notification.style.transform = 'translateX(0)';
            }, 100);

            // الإخفاء التلقائي
            setTimeout(() => {
                notification.style.opacity = '0';
                notification.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (notification.parentNode) {
                        notification.remove();
                    }
                }, 300);
            }, duration);
        }
    }

    /**
     * التحقق من صحة النموذج
     */
    validateForm() {
        const errors = [];

        // التحقق من المورد
        const supplier = document.getElementById('clientSelect')?.value;
        if (!supplier) {
            errors.push('يجب اختيار المورد');
        }

        // التحقق من وجود عناصر صحيحة
        let hasValidItems = false;
        document.querySelectorAll('.item-row').forEach(row => {
            const product = row.querySelector('.product-select')?.value;
            const quantity = this.getFloatValue(row.querySelector('.quantity'));
            const price = this.getFloatValue(row.querySelector('.price'));

            if (product && quantity > 0 && price > 0) {
                hasValidItems = true;
            }
        });

        if (!hasValidItems) {
            errors.push('يجب إضافة عنصر واحد صحيح على الأقل');
        }

        if (errors.length > 0) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    title: 'خطأ في البيانات',
                    html: errors.join('<br>'),
                    icon: 'error',
                    confirmButtonText: 'حسناً'
                });
            } else {
                alert('خطأ في البيانات:\n' + errors.join('\n'));
            }
            return false;
        }

        return true;
    }

    /**
     * حفظ كمسودة
     */
    saveAsDraft() {
        if (!this.validateForm()) return;

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'جاري حفظ المسودة...',
                text: 'يرجى الانتظار',
                allowOutsideClick: false,
                showConfirmButton: false,
                willOpen: () => {
                    Swal.showLoading();
                }
            });
        }

        const form = document.getElementById('invoice-form');
        const formData = new FormData(form);
        formData.append('is_draft', '1');

        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('تم حفظ الفاتورة كمسودة بنجاح', 'success');
                this.hasUnsavedChanges = false;
            } else {
                this.showNotification('حدث خطأ أثناء حفظ المسودة', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            this.showNotification('حدث خطأ أثناء حفظ المسودة', 'error');
        })
        .finally(() => {
            if (typeof Swal !== 'undefined') {
                Swal.close();
            }
        });
    }

    /**
     * مسح جميع العناصر
     */
    clearAllItems() {
        const confirmMessage = 'هل أنت متأكد؟ سيتم مسح جميع العناصر من الفاتورة';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'سيتم مسح جميع العناصر من الفاتورة',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، امسح الكل',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.performClearAllItems();
                }
            });
        } else {
            if (confirm(confirmMessage)) {
                this.performClearAllItems();
            }
        }
    }

    /**
     * تنفيذ مسح جميع العناصر
     */
    performClearAllItems() {
        // الاحتفاظ بصف واحد فقط
        const allRows = document.querySelectorAll('.item-row');
        for (let i = 1; i < allRows.length; i++) {
            allRows[i].remove();
        }

        // مسح بيانات الصف الأول
        if (allRows[0]) {
            allRows[0].querySelectorAll('input, select').forEach(input => {
                if (input.type !== 'hidden') {
                    if (input.classList.contains('quantity')) {
                        input.value = '1';
                    } else if (input.type === 'number') {
                        input.value = '0';
                    } else {
                        input.value = '';
                    }
                }
            });
        }

        this.calculateTotals();
        this.showNotification('تم مسح جميع العناصر بنجاح', 'success');
    }

    /**
     * تحميل إعدادات المستخدم
     */
    loadUserPreferences() {
        try {
            const preferences = localStorage.getItem('invoicePreferences');
            if (preferences) {
                const parsed = JSON.parse(preferences);

                if (parsed.defaultSupplier) {
                    const supplierSelect = document.getElementById('clientSelect');
                    if (supplierSelect) {
                        supplierSelect.value = parsed.defaultSupplier;
                        this.showSupplierBalance(supplierSelect);
                    }
                }

                if (parsed.defaultTerms) {
                    const termsField = document.querySelector('[name="terms"]');
                    if (termsField) termsField.value = parsed.defaultTerms;
                }

                if (parsed.defaultShippingCost) {
                    const shippingField = document.querySelector('[name="shipping_cost"]');
                    if (shippingField) {
                        shippingField.value = parsed.defaultShippingCost;
                        this.convertArabicToEnglish(shippingField);
                    }
                }
            }
        } catch (error) {
            console.warn('تعذر تحميل الإعدادات المحفوظة:', error);
        }
    }

    /**
     * حفظ إعدادات المستخدم
     */
    saveUserPreferences() {
        try {
            const preferences = {
                defaultSupplier: document.getElementById('clientSelect')?.value || '',
                defaultTerms: document.querySelector('[name="terms"]')?.value || '',
                defaultShippingCost: document.querySelector('[name="shipping_cost"]')?.value || '0'
            };

            localStorage.setItem('invoicePreferences', JSON.stringify(preferences));
        } catch (error) {
            console.warn('تعذر حفظ الإعدادات:', error);
        }
    }

    /**
     * إعداد الحفظ التلقائي
     */
    setupAutoSave() {
        setInterval(() => {
            if (this.hasUnsavedChanges && this.validateForm()) {
                this.autoSave();
            }
        }, this.options.autoSaveInterval);

        // حفظ الإعدادات عند تغيير القيم المهمة
        document.addEventListener('change', (e) => {
            if (e.target.matches('#clientSelect, [name="terms"], [name="shipping_cost"]')) {
                this.saveUserPreferences();
            }
        });
    }

    /**
     * الحفظ التلقائي
     */
    autoSave() {
        const form = document.getElementById('invoice-form');
        if (!form) return;

        const formData = new FormData(form);
        formData.append('auto_save', '1');

        fetch('/invoices/auto-save', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                this.showNotification('تم الحفظ التلقائي', 'success', 2000);
                this.hasUnsavedChanges = false;
            }
        })
        .catch(error => {
            console.warn('فشل الحفظ التلقائي:', error);
        });
    }

    /**
     * تدمير المثيل وتنظيف الذاكرة
     */
    destroy() {
        // إزالة المستمعين
        document.removeEventListener('input', this.handleFieldChange);
        document.removeEventListener('change', this.handleFieldChange);
        document.removeEventListener('click', this.handleClick);
        document.removeEventListener('keydown', this.handleKeyboard);

        // مسح المؤقتات
        if (this.calculateTimer) {
            clearTimeout(this.calculateTimer);
        }

        this.isInitialized = false;
        console.log('تم تدمير مثيل حاسبة الفواتير');
    }
}

// إنشاء مثيل عام للاستخدام
let invoiceCalculator = null;

// تهيئة النظام عند تحميل الصفحة
document.addEventListener('DOMContentLoaded', function() {
    invoiceCalculator = new InvoiceCalculator({
        currency: 'ر.س',
        locale: 'ar',
        calculateDelay: 200,
        autoSave: true
    });

    invoiceCalculator.init();

    // جعل الوظائف متاحة عالمياً للاستخدام من HTML
    window.saveAsDraft = () => invoiceCalculator.saveAsDraft();
    window.clearAllItems = () => invoiceCalculator.clearAllItems();
    window.showQuickPreview = () => invoiceCalculator.showQuickPreview();
    window.showSupplierBalance = (element) => invoiceCalculator.showSupplierBalance(element);
    window.updateHiddenInput = (element) => invoiceCalculator.updateTaxHiddenField(element);

    // إضافة معالجات للخيارات المتقدمة - مُصحح
    setupAdvancedOptions();
});

// تنظيف عند مغادرة الصفحة
window.addEventListener('beforeunload', function() {
    if (invoiceCalculator) {
        invoiceCalculator.destroy();
    }
});

/**
 * إعداد الخيارات المتقدمة - مُصحح
 */
function setupAdvancedOptions() {
    // معالجة checkbox الدفع الكامل
    const fullPaymentCheck = document.getElementById('full-payment-check');
    const fullPaymentFields = document.querySelector('.full-payment-fields');
    const paidAmountInput = document.getElementById('paid-amount-input');

    if (fullPaymentCheck) {
        fullPaymentCheck.addEventListener('change', function() {
            if (this.checked) {
                // إظهار حقول الدفع الكامل
                if (fullPaymentFields) {
                    fullPaymentFields.style.display = 'block';
                }

                // تعيين المبلغ المدفوع كاملاً
                if (paidAmountInput) {
                    const grandTotal = parseFloat(document.getElementById('grand-total').textContent) || 0;
                    paidAmountInput.value = grandTotal.toFixed(2);
                    paidAmountInput.readOnly = true;
                }

                // إخفاء خيارات الدفعة المقدمة
                const advancePaymentCheck = document.querySelector('.advance-payment-toggle');
                if (advancePaymentCheck) {
                    advancePaymentCheck.checked = false;
                    advancePaymentCheck.disabled = true;

                    // إخفاء حقول الدفعة المقدمة
                    const advancePaymentFields = document.querySelector('.advance-payment-fields');
                    if (advancePaymentFields) {
                        advancePaymentFields.style.display = 'none';
                    }

                    // مسح قيمة الدفعة المقدمة
                    const advanceInput = document.querySelector("[name='advance_payment']");
                    if (advanceInput) {
                        advanceInput.value = '0';
                    }
                }

            } else {
                // إخفاء حقول الدفع الكامل
                if (fullPaymentFields) {
                    fullPaymentFields.style.display = 'none';
                }

                // إعادة تعيين المبلغ المدفوع
                if (paidAmountInput) {
                    paidAmountInput.value = '0';
                    paidAmountInput.readOnly = false;
                }

                // إعادة تفعيل خيارات الدفعة المقدمة
                const advancePaymentCheck = document.querySelector('.advance-payment-toggle');
                if (advancePaymentCheck) {
                    advancePaymentCheck.disabled = false;
                }
            }

            // إعادة حساب الإجماليات
            if (invoiceCalculator) {
                invoiceCalculator.calculateTotals();
            }
        });
    }

    // معالجة checkbox الدفعة المقدمة - مُصحح
    const advancePaymentCheck = document.querySelector('.advance-payment-toggle');
    const advancePaymentFields = document.querySelector('.advance-payment-fields');

    if (advancePaymentCheck) {
        advancePaymentCheck.addEventListener('change', function() {
            if (this.checked) {
                // إظهار حقول الدفعة المقدمة
                if (advancePaymentFields) {
                    advancePaymentFields.style.display = 'block';
                }

                // التأكد من أن الدفع الكامل غير مفعل
                if (fullPaymentCheck) {
                    fullPaymentCheck.checked = false;
                    fullPaymentCheck.disabled = true;
                }

                // إخفاء حقول الدفع الكامل
                if (fullPaymentFields) {
                    fullPaymentFields.style.display = 'none';
                }

                // مسح قيمة المبلغ المدفوع
                if (paidAmountInput) {
                    paidAmountInput.value = '0';
                    paidAmountInput.readOnly = false;
                }

            } else {
                // إخفاء حقول الدفعة المقدمة
                if (advancePaymentFields) {
                    advancePaymentFields.style.display = 'none';
                }

                // إعادة تفعيل خيار الدفع الكامل
                if (fullPaymentCheck) {
                    fullPaymentCheck.disabled = false;
                }

                // مسح قيمة الدفعة المقدمة
                const advanceInput = document.querySelector("[name='advance_payment']");
                if (advanceInput) {
                    advanceInput.value = '0';
                }
            }

            // إعادة حساب الإجماليات
            if (invoiceCalculator) {
                invoiceCalculator.calculateTotals();
            }
        });
    }

    // تحديث المبلغ المدفوع عند تغيير المجموع الكلي - مُصحح
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList' || mutation.type === 'characterData') {
                if (fullPaymentCheck && fullPaymentCheck.checked && paidAmountInput) {
                    const grandTotal = parseFloat(document.getElementById('grand-total').textContent) || 0;
                    paidAmountInput.value = grandTotal.toFixed(2);
                }
            }
        });
    });

    // مراقبة تغييرات المجموع الكلي
    const grandTotalElement = document.getElementById('grand-total');
    if (grandTotalElement) {
        observer.observe(grandTotalElement, {
            childList: true,
            characterData: true,
            subtree: true
        });
    }
}

// دوال مساعدة إضافية

/**
 * دالة مساعدة لإظهار رصيد المورد - محدثة
 */
function showSupplierBalance(selectElement) {
    if (invoiceCalculator && invoiceCalculator.showSupplierBalance) {
        invoiceCalculator.showSupplierBalance(selectElement);
    } else {
        // نسخة احتياطية
        const balanceCard = document.getElementById('supplierBalanceCard');

        if (!selectElement || !selectElement.value || selectElement.value === '') {
            if (balanceCard) {
                balanceCard.style.display = 'none';
            }
            return;
        }

        const selectedOption = selectElement.options[selectElement.selectedIndex];
        const supplierName = selectedOption.text;
        const supplierBalance = parseFloat(selectedOption.getAttribute('data-balance')) || 0;

        const nameElement = document.getElementById('supplierName');
        const balanceElement = document.getElementById('supplierBalance');
        const statusElement = document.getElementById('balanceStatus');

        if (nameElement) nameElement.textContent = supplierName;
        if (balanceElement) balanceElement.textContent = Math.abs(supplierBalance).toFixed(2);

        if (statusElement && balanceElement) {
            if (supplierBalance > 0) {
                statusElement.textContent = 'دائن';
                statusElement.style.color = '#4CAF50';
                balanceElement.style.color = '#4CAF50';
            } else if (supplierBalance < 0) {
                statusElement.textContent = 'مدين';
                statusElement.style.color = '#f44336';
                balanceElement.style.color = '#f44336';
            } else {
                statusElement.textContent = 'متوازن';
                statusElement.style.color = '#FFC107';
                balanceElement.style.color = '#FFC107';
            }
        }

        if (balanceCard) {
            balanceCard.style.display = 'block';
        }
    }
}

/**
 * دالة مسح جميع البنود - محدثة
 */
function clearAllItems() {
    if (invoiceCalculator && invoiceCalculator.clearAllItems) {
        invoiceCalculator.clearAllItems();
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'تأكيد المسح',
                text: 'هل أنت متأكد من مسح جميع البنود؟',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'نعم، امسح الكل',
                cancelButtonText: 'إلغاء',
                confirmButtonColor: '#d33'
            }).then((result) => {
                if (result.isConfirmed) {
                    performClearAllItems();
                }
            });
        } else {
            if (confirm('هل أنت متأكد من مسح جميع البنود؟')) {
                performClearAllItems();
            }
        }
    }
}

function performClearAllItems() {
    const itemRows = document.querySelectorAll('.item-row');
    itemRows.forEach((row, index) => {
        if (index === 0) {
            // مسح بيانات الصف الأول فقط
            row.querySelectorAll('input, select').forEach(input => {
                if (input.type !== 'hidden') {
                    if (input.classList.contains('quantity')) {
                        input.value = '1';
                    } else if (input.type === 'number') {
                        input.value = '0';
                    } else {
                        input.value = '';
                    }
                }
            });
        } else {
            // حذف باقي الصفوف
            row.remove();
        }
    });

    // إعادة حساب الإجماليات
    if (invoiceCalculator && invoiceCalculator.calculateTotals) {
        invoiceCalculator.calculateTotals();
    }

    // إظهار رسالة نجاح
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'تم المسح!',
            text: 'تم مسح جميع البنود بنجاح',
            icon: 'success',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

/**
 * دالة معاينة سريعة - محدثة
 */
function showQuickPreview() {
    if (invoiceCalculator && invoiceCalculator.showQuickPreview) {
        invoiceCalculator.showQuickPreview();
    } else {
        const supplierSelect = document.getElementById('clientSelect');
        const supplierName = supplierSelect ? supplierSelect.options[supplierSelect.selectedIndex].text : 'غير محدد';

        const grandTotal = document.getElementById('grand-total');
        const totalAmount = grandTotal ? grandTotal.textContent : '0.00';

        const itemRows = document.querySelectorAll('.item-row');
        let itemsCount = 0;
        itemRows.forEach(row => {
            const productSelect = row.querySelector('.product-select');
            if (productSelect && productSelect.value) {
                itemsCount++;
            }
        });

        const advancePayment = document.getElementById('advance-payment');
        const advanceValue = advancePayment ? advancePayment.textContent : '0.00';

        const paidAmount = document.getElementById('paid-amount-display');
        const paidValue = paidAmount ? paidAmount.textContent : '0.00';

        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: '📋 معاينة سريعة للفاتورة',
                html: `
                    <div class="text-right" style="direction: rtl;">
                        <p><strong>المورد:</strong> ${supplierName}</p>
                        <p><strong>عدد البنود:</strong> ${itemsCount}</p>
                        <p><strong>المبلغ الإجمالي:</strong> ${totalAmount} ر.س</p>
                        <p><strong>الدفعة المقدمة:</strong> ${advanceValue} ر.س</p>
                        <p><strong>المبلغ المدفوع:</strong> ${paidValue} ر.س</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'حسناً',
                width: '500px'
            });
        } else {
            alert(`معاينة الفاتورة:\nالمورد: ${supplierName}\nعدد البنود: ${itemsCount}\nالمبلغ الإجمالي: ${totalAmount} ر.س\nالدفعة المقدمة: ${advanceValue} ر.س\nالمبلغ المدفوع: ${paidValue} ر.س`);
        }
    }
}

/**
 * دالة حفظ كمسودة - محدثة
 */
function saveAsDraft() {
    if (invoiceCalculator && invoiceCalculator.saveAsDraft) {
        invoiceCalculator.saveAsDraft();
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                title: 'حفظ كمسودة',
                text: 'هل تريد حفظ الفاتورة كمسودة؟',
                icon: 'info',
                showCancelButton: true,
                confirmButtonText: 'نعم، احفظ',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    const draftInput = document.createElement('input');
                    draftInput.type = 'hidden';
                    draftInput.name = 'is_draft';
                    draftInput.value = '1';
                    document.getElementById('invoice-form').appendChild(draftInput);
                    document.getElementById('invoice-form').submit();
                }
            });
        } else {
            if (confirm('هل تريد حفظ الفاتورة كمسودة؟')) {
                const draftInput = document.createElement('input');
                draftInput.type = 'hidden';
                draftInput.name = 'is_draft';
                draftInput.value = '1';
                document.getElementById('invoice-form').appendChild(draftInput);
                document.getElementById('invoice-form').submit();
            }
        }
    }
}

/**
 * دالة تحديث الحقول المخفية للضرائب
 */
function updateHiddenInput(selectElement) {
    if (invoiceCalculator && invoiceCalculator.updateTaxHiddenField) {
        invoiceCalculator.updateTaxHiddenField(selectElement);
    } else {
        const row = selectElement.closest('.item-row');
        const taxType = selectElement.getAttribute('data-target');
        const hiddenInput = row.querySelector(`input[name^="items"][name$="[${taxType}_id]"]`);

        if (hiddenInput) {
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            hiddenInput.value = selectedOption.getAttribute('data-id') || '';
        }
    }
}

// إضافة معالجات إضافية للتحقق من صحة النموذج
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('invoice-form');
    if (form) {
        form.addEventListener('submit', function(e) {
            // التحقق من المورد
            const supplierSelect = document.getElementById('clientSelect');
            if (!supplierSelect || !supplierSelect.value) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى اختيار المورد',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى اختيار المورد');
                }
                return false;
            }

            // التحقق من وجود منتجات
            const itemRows = document.querySelectorAll('.item-row');
            if (itemRows.length === 0) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى إضافة منتج واحد على الأقل',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى إضافة منتج واحد على الأقل');
                }
                return false;
            }

            // التحقق من وجود منتجات محددة
            let hasValidItems = false;
            itemRows.forEach(function(row) {
                const productSelect = row.querySelector('.product-select');
                const quantity = row.querySelector('.quantity');
                const price = row.querySelector('.price');

                if (productSelect && productSelect.value &&
                    quantity && parseFloat(quantity.value) > 0 &&
                    price && parseFloat(price.value) > 0) {
                    hasValidItems = true;
                }
            });

            if (!hasValidItems) {
                e.preventDefault();
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'يرجى تحديد منتج واحد صحيح على الأقل مع كمية وسعر صحيحين',
                        icon: 'error',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يرجى تحديد منتج واحد صحيح على الأقل مع كمية وسعر صحيحين');
                }
                return false;
            }
        });
    }

    // إضافة معالج لزر إضافة صف جديد
    const addRowBtn = document.getElementById('add-row');
    if (addRowBtn) {
        addRowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (invoiceCalculator && invoiceCalculator.addNewRow) {
                invoiceCalculator.addNewRow();
            } else {
                // إضافة صف احتياطية
                addNewItemRow();
            }
        });
    }

    // إضافة معالج لأزرار حذف الصفوف
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-row')) {
            e.preventDefault();
            const row = e.target.closest('.item-row');
            const allRows = document.querySelectorAll('.item-row');

            if (allRows.length > 1) {
                row.remove();
                if (invoiceCalculator && invoiceCalculator.calculateTotals) {
                    invoiceCalculator.calculateTotals();
                }
            } else {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'تنبيه',
                        text: 'يجب أن يحتوي الجدول على صف واحد على الأقل',
                        icon: 'warning',
                        confirmButtonText: 'حسناً'
                    });
                } else {
                    alert('يجب أن يحتوي الجدول على صف واحد على الأقل');
                }
            }
        }
    });
});

/**
 * دالة إضافة صف جديد احتياطية
 */
function addNewItemRow() {
    const tableBody = document.querySelector('#items-table tbody');
    if (!tableBody) return;

    const existingRows = tableBody.querySelectorAll('.item-row');
    const rowCount = existingRows.length;
    const firstRow = existingRows[0];

    if (!firstRow) return;

    const newRow = firstRow.cloneNode(true);

    // تحديث أسماء الحقول
    newRow.querySelectorAll('input, select').forEach(input => {
        if (input.name) {
            input.name = input.name.replace(/\[\d+\]/, `[${rowCount}]`);

            // إعادة تعيين القيم
            if (input.type !== 'hidden') {
                if (input.classList.contains('quantity')) {
                    input.value = '1';
                } else if (input.type === 'number') {
                    input.value = '0';
                } else {
                    input.value = '';
                }
            }
        }
    });

    // إعادة تعيين إجمالي الصف
    const rowTotal = newRow.querySelector('.row-total');
    if (rowTotal) {
        rowTotal.textContent = '0.00';
    }

    tableBody.appendChild(newRow);

    // إعادة حساب الإجماليات
    if (invoiceCalculator && invoiceCalculator.calculateTotals) {
        invoiceCalculator.calculateTotals();
    }
}
