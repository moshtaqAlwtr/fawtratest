<!-- Payments Widget -->
<div class="widget">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">المدفوعات</h5>
        <i class="heading-icon fas fa-money-check-alt"></i>
    </div>

    <!-- إضافة ملخص المدفوعات -->
    <div class="payments-summary">
        <div class="summary-card">
            <div class="summary-icon">
                <i class="fas fa-credit-card"></i>
            </div>
            <div class="summary-content">
                <div class="summary-amount">45,750 ر.س</div>
                <div class="summary-label">إجمالي المدفوعات</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon pending">
                <i class="fas fa-clock"></i>
            </div>
            <div class="summary-content">
                <div class="summary-amount">3,200 ر.س</div>
                <div class="summary-label">في الانتظار</div>
            </div>
        </div>
        <div class="summary-card">
            <div class="summary-icon success">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="summary-content">
                <div class="summary-amount">42,550 ر.س</div>
                <div class="summary-label">مكتملة</div>
            </div>
        </div>
    </div>

    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="listing">
        <thead>
            <tr>
                <th>رقم الدفعة</th>
                <th>حساب</th>
                <th>التاريخ</th>
                <th>المبلغ</th>
                <th>الحالة</th>
                <th>الإجراءات</th>
            </tr>
        </thead>
        <tbody>
            {{-- اذا كانت العناصر فارغة  --}}
            {{-- <tr>
                <td colspan="4" style="padding: 60px; text-align: center; color: #999;">
                    <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                    <div>لا توجد مدفوعات حتى الآن</div>
                </td>
            </tr> --}}
            <tr>
                <td class="payment-id">#PAY-2024-001</td>
                <td>
                    <div class="account-info">
                        <strong>شركة التقنية المتقدمة</strong>
                        <small>فاتورة رقم INV-2024-156</small>
                    </div>
                </td>
                <td>2024-12-15 10:30</td>
                <td class="amount-cell">
                    <span class="amount">2,500.00 ر.س</span>
                    <small class="payment-method">تحويل بنكي</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr class="row-alternate">
                <td class="payment-id">#PAY-2024-002</td>
                <td>
                    <div class="account-info">
                        <strong>مؤسسة الإبداع التجارية</strong>
                        <small>فاتورة رقم INV-2024-142</small>
                    </div>
                </td>
                <td>2024-12-14 14:15</td>
                <td class="amount-cell">
                    <span class="amount">1,850.00 ر.س</span>
                    <small class="payment-method">بطاقة ائتمان</small>
                </td>
                <td>
                    <span class="status-badge pending">في الانتظار</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action cancel" title="إلغاء الدفعة">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="payment-id">#PAY-2024-003</td>
                <td>
                    <div class="account-info">
                        <strong>شركة النهضة للخدمات</strong>
                        <small>فاتورة رقم INV-2024-135</small>
                    </div>
                </td>
                <td>2024-12-13 09:45</td>
                <td class="amount-cell">
                    <span class="amount">5,200.00 ر.س</span>
                    <small class="payment-method">شيك بنكي</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr class="row-alternate">
                <td class="payment-id">#PAY-2024-004</td>
                <td>
                    <div class="account-info">
                        <strong>مكتب الاستشارات الهندسية</strong>
                        <small>فاتورة رقم INV-2024-128</small>
                    </div>
                </td>
                <td>2024-12-12 16:20</td>
                <td class="amount-cell">
                    <span class="amount">3,750.00 ر.س</span>
                    <small class="payment-method">نقداً</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="payment-id">#PAY-2024-005</td>
                <td>
                    <div class="account-info">
                        <strong>شركة البناء الحديث</strong>
                        <small>فاتورة رقم INV-2024-115</small>
                    </div>
                </td>
                <td>2024-12-11 11:10</td>
                <td class="amount-cell">
                    <span class="amount">7,800.00 ر.س</span>
                    <small class="payment-method">تحويل بنكي</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr class="row-alternate">
                <td class="payment-id">#PAY-2024-006</td>
                <td>
                    <div class="account-info">
                        <strong>معهد التدريب التقني</strong>
                        <small>فاتورة رقم INV-2024-098</small>
                    </div>
                </td>
                <td>2024-12-10 13:45</td>
                <td class="amount-cell">
                    <span class="amount">1,350.00 ر.س</span>
                    <small class="payment-method">بطاقة ائتمان</small>
                </td>
                <td>
                    <span class="status-badge pending">في الانتظار</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action cancel" title="إلغاء الدفعة">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="payment-id">#PAY-2024-007</td>
                <td>
                    <div class="account-info">
                        <strong>شركة التسويق الرقمي</strong>
                        <small>فاتورة رقم INV-2024-089</small>
                    </div>
                </td>
                <td>2024-12-09 08:30</td>
                <td class="amount-cell">
                    <span class="amount">4,650.00 ر.س</span>
                    <small class="payment-method">تحويل بنكي</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr class="row-alternate">
                <td class="payment-id">#PAY-2024-008</td>
                <td>
                    <div class="account-info">
                        <strong>مطعم الأصالة الشعبي</strong>
                        <small>فاتورة رقم INV-2024-076</small>
                    </div>
                </td>
                <td>2024-12-08 19:15</td>
                <td class="amount-cell">
                    <span class="amount">925.00 ر.س</span>
                    <small class="payment-method">نقداً</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr>
                <td class="payment-id">#PAY-2024-009</td>
                <td>
                    <div class="account-info">
                        <strong>عيادة الطب الحديث</strong>
                        <small>فاتورة رقم INV-2024-065</small>
                    </div>
                </td>
                <td>2024-12-07 12:00</td>
                <td class="amount-cell">
                    <span class="amount">2,100.00 ر.س</span>
                    <small class="payment-method">بطاقة ائتمان</small>
                </td>
                <td>
                    <span class="status-badge failed">فشلت</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action retry" title="إعادة المحاولة">
                            <i class="fas fa-redo"></i>
                        </button>
                    </div>
                </td>
            </tr>

            <tr class="row-alternate">
                <td class="payment-id">#PAY-2024-010</td>
                <td>
                    <div class="account-info">
                        <strong>محل الإلكترونيات المتطورة</strong>
                        <small>فاتورة رقم INV-2024-052</small>
                    </div>
                </td>
                <td>2024-12-06 15:30</td>
                <td class="amount-cell">
                    <span class="amount">6,420.00 ر.س</span>
                    <small class="payment-method">شيك بنكي</small>
                </td>
                <td>
                    <span class="status-badge completed">مكتملة</span>
                </td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action view" title="عرض التفاصيل">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn-action download" title="تحميل الإيصال">
                            <i class="fas fa-download"></i>
                        </button>
                        <button class="btn-action print" title="طباعة">
                            <i class="fas fa-print"></i>
                        </button>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- إضافة التنقل بين الصفحات -->
    <div class="table-footer">
        <div class="footer-info">
            عرض 1 إلى 10 من 89 دفعة
        </div>
        <div class="footer-actions">
            <button class="footer-btn">
                <i class="fas fa-plus"></i>
                إضافة دفعة جديدة
            </button>
            <button class="footer-btn secondary">
                <i class="fas fa-file-export"></i>
                تصدير التقرير
            </button>
        </div>
        <div class="pagination-controls">
            <button class="pagination-btn" disabled>السابق</button>
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <button class="pagination-btn">...</button>
            <button class="pagination-btn">9</button>
            <button class="pagination-btn">التالي</button>
        </div>
    </div>
</div>

<style>
    /* استجابة للشاشات الصغيرة */
    @media (max-width: 768px) {
        .payments-summary {
            flex-direction: column;
            gap: 10px;
        }

        .table-footer {
            flex-direction: column;
            align-items: stretch;
            text-align: center;
        }

        .footer-actions {
            justify-content: center;
            order: 3;
        }

        .pagination-controls {
            justify-content: center;
            order: 2;
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        .listing th,
        .listing td {
            padding: 8px 4px;
            font-size: 12px;
        }
    }
</style>
