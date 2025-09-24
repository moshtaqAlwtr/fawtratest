<div class="widget">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">إعدادات الدفع</h5>
        <i class="heading-icon fas fa-file-invoice-dollar"></i>
    </div>

    <div class="widget-content rtl-text">
        <!-- رسائل النجاح والخطأ -->
        <div class="alert alert-success" role="alert" style="display: none;">
            <i class="fas fa-check-circle"></i>
            <span class="alert-message"></span>
        </div>

        <div class="alert alert-danger" role="alert" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i>
            <span class="alert-message"></span>
        </div>

        <!-- بطاقات الدفع -->
        <div class="section">
            <div class="section-header">
                <h4>البطاقات المحفوظة</h4>
                <button class="btn-add" id="addCardBtn">
                    <i class="fas fa-plus"></i> إضافة بطاقة
                </button>
            </div>

            <div class="cards-container">
                <!-- بطاقة 1 -->
                <div class="payment-card visa">
                    <div class="card-top">
                        <div class="card-type">
                            <i class="fab fa-cc-visa"></i>
                            <span>Visa</span>
                        </div>
                        <span class="default-badge">افتراضية</span>
                    </div>
                    <div class="card-number">**** **** **** 1234</div>
                    <div class="card-bottom">
                        <span class="card-holder">أحمد محمد</span>
                        <span class="card-expiry">12/25</span>
                    </div>
                    <div class="card-actions">
                        <button class="btn-edit">تعديل</button>
                        <button class="btn-delete">حذف</button>
                    </div>
                </div>

                <!-- بطاقة 2 -->
                <div class="payment-card mastercard">
                    <div class="card-top">
                        <div class="card-type">
                            <i class="fab fa-cc-mastercard"></i>
                            <span>Mastercard</span>
                        </div>
                    </div>
                    <div class="card-number">**** **** **** 5678</div>
                    <div class="card-bottom">
                        <span class="card-holder">أحمد محمد</span>
                        <span class="card-expiry">08/26</span>
                    </div>
                    <div class="card-actions">
                        <button class="btn-edit">تعديل</button>
                        <button class="btn-delete">حذف</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- إعدادات الدفع -->
        <div class="section">
            <h4>إعدادات عامة</h4>
            <form id="paymentSettingsForm">
                <div class="settings-grid">
                    <div class="setting-item">
                        <label class="switch">
                            <input type="checkbox" name="auto_payment">
                            <span class="slider"></span>
                        </label>
                        <div class="setting-info">
                            <h5>الدفع التلقائي</h5>
                            <p>خصم تلقائي من البطاقة الافتراضية</p>
                        </div>
                    </div>

                    <div class="setting-item">
                        <label class="switch">
                            <input type="checkbox" name="email_notifications">
                            <span class="slider"></span>
                        </label>
                        <div class="setting-info">
                            <h5>إشعارات البريد</h5>
                            <p>تلقي إشعارات عمليات الدفع</p>
                        </div>
                    </div>

                    <div class="setting-item full-width">
                        <label for="currency">العملة المفضلة</label>
                        <select id="currency" name="currency">
                            <option value="SAR">ريال سعودي (SAR)</option>
                            <option value="USD">دولار أمريكي (USD)</option>
                            <option value="EUR">يورو (EUR)</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> حفظ الإعدادات
                </button>
            </form>
        </div>

        <!-- معلومات الأمان -->
        <div class="security-info">
            <div class="security-header">
                <i class="fas fa-shield-alt"></i>
                <h5>الأمان والحماية</h5>
            </div>
            <div class="security-points">
                <div class="point">
                    <i class="fas fa-lock"></i>
                    <span>تشفير SSL لجميع البيانات</span>
                </div>
                <div class="point">
                    <i class="fas fa-eye-slash"></i>
                    <span>عدم حفظ رقم CVV</span>
                </div>
                <div class="point">
                    <i class="fas fa-bell"></i>
                    <span>إشعارات فورية للعمليات</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- نافذة إضافة بطاقة -->
<div class="modal" id="addCardModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5>إضافة بطاقة جديدة</h5>
                <button type="button" class="close" id="closeModal">&times;</button>
            </div>
            <form id="addCardForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cardNumber">رقم البطاقة</label>
                        <input type="text" id="cardNumber" name="card_number" placeholder="1234 5678 9012 3456"
                            maxlength="19" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cardHolder">اسم حامل البطاقة</label>
                            <input type="text" id="cardHolder" name="card_holder"
                                placeholder="الاسم كما يظهر على البطاقة" required>
                        </div>
                        <div class="form-group">
                            <label for="cvv">CVV</label>
                            <input type="text" id="cvv" name="cvv" placeholder="123" maxlength="4"
                                required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="expiryMonth">شهر الانتهاء</label>
                            <select id="expiryMonth" name="expiry_month" required>
                                <option value="">الشهر</option>
                                <option value="01">01</option>
                                <option value="02">02</option>
                                <option value="03">03</option>
                                <option value="04">04</option>
                                <option value="05">05</option>
                                <option value="06">06</option>
                                <option value="07">07</option>
                                <option value="08">08</option>
                                <option value="09">09</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="expiryYear">سنة الانتهاء</label>
                            <select id="expiryYear" name="expiry_year" required>
                                <option value="">السنة</option>
                                <option value="2024">2024</option>
                                <option value="2025">2025</option>
                                <option value="2026">2026</option>
                                <option value="2027">2027</option>
                                <option value="2028">2028</option>
                                <option value="2029">2029</option>
                                <option value="2030">2030</option>
                                <option value="2031">2031</option>
                                <option value="2032">2032</option>
                                <option value="2033">2033</option>
                                <option value="2034">2034</option>
                            </select>
                        </div>
                    </div>

                    <div class="checkbox-group">
                        <label class="checkbox">
                            <input type="checkbox" name="set_default">
                            <span class="checkmark"></span>
                            تعيين كبطاقة افتراضية
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" id="cancelBtn">إلغاء</button>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-save"></i> حفظ البطاقة
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addCardModal');
        const addCardBtn = document.getElementById('addCardBtn');
        const closeModal = document.getElementById('closeModal');
        const cancelBtn = document.getElementById('cancelBtn');
        const addCardForm = document.getElementById('addCardForm');
        const cardNumberInput = document.getElementById('cardNumber');

        // فتح النافذة
        addCardBtn.addEventListener('click', function() {
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        });

        // إغلاق النافذة
        function closeModalFunc() {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
            addCardForm.reset();
        }

        closeModal.addEventListener('click', closeModalFunc);
        cancelBtn.addEventListener('click', closeModalFunc);

        // إغلاق عند الضغط خارج النافذة
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeModalFunc();
            }
        });

        // إغلاق بمفتاح Escape
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && modal.style.display === 'flex') {
                closeModalFunc();
            }
        });

        // تنسيق رقم البطاقة
        cardNumberInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\s/g, '').replace(/[^0-9]/gi, '');
            let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
            e.target.value = formattedValue;
        });

        // التحقق من نوع البطاقة
        cardNumberInput.addEventListener('input', function(e) {
            const value = e.target.value.replace(/\s/g, '');
            const cardPreview = document.querySelector('.card-preview');

            // تحديد نوع البطاقة حسب الرقم
            if (value.startsWith('4')) {
                // Visa
                e.target.style.borderColor = '#1a1f71';
            } else if (value.startsWith('5') || (value.startsWith('2') && value.length >= 2 && parseInt(
                    value.substring(0, 2)) >= 22 && parseInt(value.substring(0, 2)) <= 27)) {
                // Mastercard
                e.target.style.borderColor = '#eb001b';
            } else {
                e.target.style.borderColor = '#ddd';
            }
        });

        // معالجة إرسال النموذج
        addCardForm.addEventListener('submit', function(e) {
            e.preventDefault();

            // محاكاة حفظ البطاقة
            const formData = new FormData(addCardForm);
            const cardNumber = formData.get('card_number');
            const cardHolder = formData.get('card_holder');
            const expiryMonth = formData.get('expiry_month');
            const expiryYear = formData.get('expiry_year');
            const setDefault = formData.get('set_default');

            // التحقق من صحة البيانات
            if (!cardNumber || !cardHolder || !expiryMonth || !expiryYear) {
                showAlert('error', 'يرجى ملء جميع الحقول المطلوبة');
                return;
            }

            // محاكاة نجاح العملية
            setTimeout(() => {
                showAlert('success', 'تم إضافة البطاقة بنجاح');
                closeModalFunc();

                // يمكنك هنا إضافة كود لإضافة البطاقة للقائمة
                addCardToList(cardNumber, cardHolder, expiryMonth, expiryYear, setDefault);
            }, 1000);
        });

        // معالجة حفظ الإعدادات
        document.getElementById('paymentSettingsForm').addEventListener('submit', function(e) {
            e.preventDefault();
            showAlert('success', 'تم حفظ الإعدادات بنجاح');
        });

        // دالة عرض الرسائل
        function showAlert(type, message) {
            const alert = document.querySelector(`.alert-${type}`);
            const messageEl = alert.querySelector('.alert-message');

            messageEl.textContent = message;
            alert.style.display = 'flex';

            setTimeout(() => {
                alert.style.display = 'none';
            }, 3000);
        }

        // دالة إضافة بطاقة جديدة للقائمة
        function addCardToList(cardNumber, cardHolder, expiryMonth, expiryYear, setDefault) {
            const cardsContainer = document.querySelector('.cards-container');
            const lastFour = cardNumber.slice(-4);
            const cardType = cardNumber.startsWith('4') ? 'visa' : 'mastercard';
            const cardIcon = cardNumber.startsWith('4') ? 'fa-cc-visa' : 'fa-cc-mastercard';
            const cardName = cardNumber.startsWith('4') ? 'Visa' : 'Mastercard';

            const cardHTML = `
            <div class="payment-card ${cardType}">
                <div class="card-top">
                    <div class="card-type">
                        <i class="fab ${cardIcon}"></i>
                        <span>${cardName}</span>
                    </div>
                    ${setDefault ? '<span class="default-badge">افتراضية</span>' : ''}
                </div>
                <div class="card-number">**** **** **** ${lastFour}</div>
                <div class="card-bottom">
                    <span class="card-holder">${cardHolder}</span>
                    <span class="card-expiry">${expiryMonth}/${expiryYear.slice(-2)}</span>
                </div>
                <div class="card-actions">
                    <button class="btn-edit">تعديل</button>
                    <button class="btn-delete">حذف</button>
                </div>
            </div>
        `;

            cardsContainer.insertAdjacentHTML('beforeend', cardHTML);
        }

        // معالجة حذف البطاقات
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-delete')) {
                if (confirm('هل أنت متأكد من حذف هذه البطاقة؟')) {
                    e.target.closest('.payment-card').remove();
                    showAlert('success', 'تم حذف البطاقة بنجاح');
                }
            }
        });
    });
</script>

<style>
    /* الإعدادات العامة */
    .widget-header {
        margin-bottom: 25px;
        padding-bottom: 15px;
        border-bottom: 1px solid #e9ecef;
    }

    .widget-content {
        padding: 20px;
        background: white;
        border-radius: 8px;
        direction: rtl;
        text-align: right;
    }
</style>
