<div class="widget">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">كشف الحساب</h5>
        <i class="heading-icon fas fa-file-invoice-dollar"></i>
    </div>

    <!-- إضافة ملخص الحساب -->
    <div class="account-summary">
        <div class="summary-item">
            <span class="summary-label">إجمالي الإيداعات</span>
            <span class="summary-value positive">15,750.00 ر.س</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">إجمالي السحوبات</span>
            <span class="summary-value negative">8,230.00 ر.س</span>
        </div>
        <div class="summary-item">
            <span class="summary-label">الرصيد الحالي</span>
            <span class="summary-value current">7,520.00 ر.س</span>
        </div>
    </div>

    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="listing">
        <thead>
            <tr>
                <th>الرقم المرجعي</th>
                <th>التاريخ</th>
                <th>النوع</th>
                <th>ائتمان</th>
                <th>مدين</th>
                <th>الوصف</th>
            </tr>
        </thead>
        <tbody>
            {{-- اذا كانت العناصر فارغة  --}}
            {{-- <tr>
            <td colspan="6" style="padding: 60px; text-align: center; color: #999;">
                <i class="fas fa-chart-line"
                    style="font-size: 48px; margin-bottom: 20px; color: #ddd; display: block;"></i>
                <div style="margin-bottom: 15px; text-align: center;">لا توجد مدفوعات الإحالة حتى الآن</div>
                <div style="margin-bottom: 20px; text-align: center;">ادع اصدقائك الان</div>
                <a href="{{ route('myCompany') }}" class="btn btn-primary"
                    style="background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 0 auto;">
                    <i class="fas fa-user-plus"></i> ادع الأصدقاء
                </a>
            </td>
        </tr> --}}
            <tr>
                <td class="ref-number">REF-2024-001</td>
                <td>2024-12-15 14:30</td>
                <td><span class="transaction-type deposit">إيداع</span></td>
                <td class="credit-amount">2,500.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>إيداع نقدي من العميل محمد أحمد الشمري</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-002</td>
                <td>2024-12-14 10:15</td>
                <td><span class="transaction-type withdrawal">سحب</span></td>
                <td class="credit-amount">-</td>
                <td class="debit-amount">1,200.00 ر.س</td>
                <td>دفع فاتورة كهرباء المكتب الرئيسي</td>
            </tr>

            <tr>
                <td class="ref-number">REF-2024-003</td>
                <td>2024-12-13 16:45</td>
                <td><span class="transaction-type commission">عمولة</span></td>
                <td class="credit-amount">750.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>عمولة إحالة من شركة النخبة للاستشارات</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-004</td>
                <td>2024-12-12 09:20</td>
                <td><span class="transaction-type transfer">تحويل</span></td>
                <td class="credit-amount">-</td>
                <td class="debit-amount">500.00 ر.س</td>
                <td>تحويل إلى حساب التوفير الاحتياطي</td>
            </tr>

            <tr>
                <td class="ref-number">REF-2024-005</td>
                <td>2024-12-11 13:30</td>
                <td><span class="transaction-type deposit">إيداع</span></td>
                <td class="credit-amount">3,200.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>إيداع بنكي من البنك الأهلي السعودي</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-006</td>
                <td>2024-12-10 11:00</td>
                <td><span class="transaction-type fee">رسوم</span></td>
                <td class="credit-amount">-</td>
                <td class="debit-amount">75.00 ر.س</td>
                <td>رسوم إدارية شهرية للحساب الجاري</td>
            </tr>

            <tr>
                <td class="ref-number">REF-2024-007</td>
                <td>2024-12-09 15:45</td>
                <td><span class="transaction-type commission">عمولة</span></td>
                <td class="credit-amount">1,100.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>عمولة مبيعات شهر نوفمبر 2024</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-008</td>
                <td>2024-12-08 08:30</td>
                <td><span class="transaction-type withdrawal">سحب</span></td>
                <td class="credit-amount">-</td>
                <td class="debit-amount">850.00 ر.س</td>
                <td>سحب نقدي من الصراف الآلي - فرع الملك فهد</td>
            </tr>

            <tr>
                <td class="ref-number">REF-2024-009</td>
                <td>2024-12-07 12:15</td>
                <td><span class="transaction-type deposit">إيداع</span></td>
                <td class="credit-amount">4,500.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>إيداع مبيعات الأسبوع الأول من ديسمبر</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-010</td>
                <td>2024-12-06 17:20</td>
                <td><span class="transaction-type transfer">تحويل</span></td>
                <td class="credit-amount">2,800.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>تحويل من حساب التوفير الشخصي</td>
            </tr>

            <tr>
                <td class="ref-number">REF-2024-011</td>
                <td>2024-12-05 14:00</td>
                <td><span class="transaction-type withdrawal">سحب</span></td>
                <td class="credit-amount">-</td>
                <td class="debit-amount">1,350.00 ر.س</td>
                <td>دفع راتب الموظف عبدالعزيز الغامدي</td>
            </tr>

            <tr class="row-alternate">
                <td class="ref-number">REF-2024-012</td>
                <td>2024-12-04 10:45</td>
                <td><span class="transaction-type commission">عمولة</span></td>
                <td class="credit-amount">925.00 ر.س</td>
                <td class="debit-amount">-</td>
                <td>عمولة شراكة مع مؤسسة الرائد التجارية</td>
            </tr>
        </tbody>
    </table>

    <!-- إضافة التنقل بين الصفحات -->
    <div class="pagination">
        <span class="pagination-info">عرض 1 إلى 12 من 156 سجل</span>
        <div class="pagination-controls">
            <button class="pagination-btn" disabled>السابق</button>
            <button class="pagination-btn active">1</button>
            <button class="pagination-btn">2</button>
            <button class="pagination-btn">3</button>
            <button class="pagination-btn">...</button>
            <button class="pagination-btn">13</button>
            <button class="pagination-btn">التالي</button>
        </div>
    </div>
</div>

<style>
    /* تصميم الجدول */
    .listing {
        width: 100%;
        border-collapse: collapse;
        margin: 0;
    }

    .listing thead tr {
        background: #f8f9fa;
    }

    .listing th {
        padding: 15px 12px;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        font-size: 14px;
    }

    .listing td {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        font-size: 13px;
        color: #495057;
    }
</style>
