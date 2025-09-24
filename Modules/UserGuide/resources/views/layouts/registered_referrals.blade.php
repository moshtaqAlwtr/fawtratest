<!-- Registered Referrals Widget -->
<div class="widget">
    <div class="widget-header widget-header-wrapper">
        <h5 class="heading">الإحالات المسجلة</h5>
        <i class="heading-icon fas fa-users"></i>
    </div>
    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="listing">
        <thead>
            <tr>
                <th>المعرف</th>
                <th>الاسم التجاري</th>
                <th>تاريخ التسجيل</th>
                <th>الخطة</th>
            </tr>
        </thead>
        <tbody>
            {{-- اذا كانت العناصر فارغة  --}}
            {{-- <tr>
             <td colspan="4" style="padding: 60px; text-align: center; color: #999;">
                 <i class="fas fa-receipt" style="font-size: 48px; margin-bottom: 20px; color: #ddd;"></i>
                 <div style="margin-bottom: 15px; text-align: center;">لا توجد احالات حتى الان... </div>
                 <a href="{{ route('myCompany') }}" class="btn btn-primary"
                     style="background: #6366f1; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin: 0 auto;">
                     <i class="fas fa-user-plus"></i> ادع الأصدقاء
                 </a>
             </td>
         </tr> --}}
            <tr>
                <td><span class="ref-id">#REF001</span></td>
                <td>
                    <div class="company-info">
                        <strong>مطعم الذواقة</strong>
                        <small>ahmed.restaurant@email.com</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date">15 يناير 2024</span>
                        <small class="time">منذ 3 أسابيع</small>
                    </div>
                </td>
                <td><span class="plan-badge premium">خطة مميزة</span></td>
            </tr>
            <tr>
                <td><span class="ref-id">#REF002</span></td>
                <td>
                    <div class="company-info">
                        <strong>صالون الجمال الراقي</strong>
                        <small>beauty.salon@email.com</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date">22 يناير 2024</span>
                        <small class="time">منذ أسبوعين</small>
                    </div>
                </td>
                <td><span class="plan-badge basic">خطة أساسية</span></td>
            </tr>
            <tr>
                <td><span class="ref-id">#REF003</span></td>
                <td>
                    <div class="company-info">
                        <strong>متجر الإلكترونيات الحديثة</strong>
                        <small>electronics.store@email.com</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date">28 يناير 2024</span>
                        <small class="time">منذ أسبوع</small>
                    </div>
                </td>
                <td><span class="plan-badge enterprise">خطة الشركات</span></td>
            </tr>
            <tr>
                <td><span class="ref-id">#REF004</span></td>
                <td>
                    <div class="company-info">
                        <strong>عيادة الأسنان المتخصصة</strong>
                        <small>dental.clinic@email.com</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date">2 فبراير 2024</span>
                        <small class="time">منذ 3 أيام</small>
                    </div>
                </td>
                <td><span class="plan-badge premium">خطة مميزة</span></td>
            </tr>
            <tr>
                <td><span class="ref-id">#REF005</span></td>
                <td>
                    <div class="company-info">
                        <strong>صيدلية النور</strong>
                        <small>pharmacy.noor@email.com</small>
                    </div>
                </td>
                <td>
                    <div class="date-info">
                        <span class="date">4 فبراير 2024</span>
                        <small class="time">منذ يوم واحد</small>
                    </div>
                </td>
                <td><span class="plan-badge basic">خطة أساسية</span></td>
            </tr>
        </tbody>
    </table>

    <!-- تذييل الجدول -->
    <div class="table-footer">
        <div class="footer-info">
            <span>إجمالي الإحالات: <strong>5</strong></span>
            <span class="separator">|</span>
            <span>إجمالي العمولات: <strong>2,450 ريال</strong></span>
        </div>
        <div class="footer-actions">
            <a href="#" class="footer-btn">
                <i class="fas fa-share-alt"></i>
                مشاركة رابط الإحالة
            </a>
        </div>
    </div>
</div>
<style>
    /* استجابة للشاشات الصغيرة */
    @media (max-width: 768px) {
        .table-footer {
            flex-direction: column;
            text-align: center;
            padding: 15px 20px;
        }

        .company-info strong {
            font-size: 13px;
        }

        .company-info small {
            font-size: 11px;
        }

        .date-info .date {
            font-size: 12px;
        }

        .plan-badge {
            padding: 4px 10px;
            font-size: 10px;
            min-width: 70px;
        }
    }

    @media (max-width: 480px) {

        .listing th,
        .listing td {
            padding: 8px 6px;
            font-size: 12px;
        }

        .footer-info {
            font-size: 12px;
        }

        .footer-btn {
            padding: 6px 12px;
            font-size: 12px;
        }

        .ref-id {
            padding: 2px 6px;
            font-size: 11px;
        }
    }
</style>
