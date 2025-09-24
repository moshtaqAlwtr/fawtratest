@extends('master')

@section('title')
الأدوار الوظيفية
@stop
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/sitting.css') }}">
@endsection
@section('content')

<div class="content-header row">
    <div class="content-header-left col-md-9 col-12 mb-2">
        <div class="row breadcrumbs-top">
            <div class="col-12">
                <h2 class="content-header-title float-left mb-0">الأدوار الوظيفية </h2>
                <div class="breadcrumb-wrapper col-12">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                        </li>
                        <li class="breadcrumb-item active">أضافة
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <form id="permissions_form" action="{{ route('managing_employee_roles.store') }}" method="POST">
        @csrf
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="d-flex justify-content-between ">
                    <input type="text" name="role_name" placeholder="اسم الدور الوظيفي" required>
                    <div class="vs-checkbox-con vs-checkbox-primary px-md-1">
                        <ul class="list-unstyled mb-0">
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input user-radio" name="customRadio" id="customRadio1" checked="" value="user">
                                        <label class="custom-control-label" for="customRadio1">مستخدم</label>
                                    </div>
                                </fieldset>
                            </li>
                            <li class="d-inline-block mr-2">
                                <fieldset>
                                    <div class="custom-control custom-radio">
                                        <input type="radio" class="custom-control-input employee-radio" name="customRadio" id="customRadio2" value="employee">
                                        <label class="custom-control-label" for="customRadio2">موظف</label>
                                    </div>
                                </fieldset>
                            </li>
                        </ul>
                    </div>
                    <div class="vs-checkbox-con vs-checkbox-primary px-md-1" id="admin">
                        <input type="checkbox" id="adminCheckbox">
                        <span class="vs-checkbox">
                            <span class="vs-checkbox--check">
                                <i class="vs-icon feather icon-check"></i>
                            </span>
                        </span>
                        <span class="">مدير (أدمن )</span>
                    </div>
                </div>
                <div>
                    <a href="{{ route('managing_employee_roles.index') }}" class="btn btn-outline-danger">
                        <i class="fa fa-ban"></i>الغاء
                    </a>
                    <button type="submit" form="permissions_form" class="btn btn-outline-primary">
                        <i class="fa fa-save"></i>حفظ
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row">

        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="app-manager-sidebar">
                        <div class="app-manager-dropdown-app">
                            <div>التطبيقات ومجموعات الصلاحيات</div>

                            <!-- زر كل التطبيقات -->
                            <div class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2 active" role="tab" aria-controls="all-apps" aria-selected="true">
                                <div class="d-flex align-items-center">
                                    <i class="mdi mdi-apps app-manager-dropdown-icon"></i>
                                    <span class="ms-2">كل التطبيقات</span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <label class="app-manager-role-btn">
                                        <span class="app-manager-role-label" data-role-count="218">0/218</span>
                                        <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="all-apps">
                                        <span class="app-manager-role-control"></span>
                                    </label>
                                </div>
                            </div>

                            <!-- زر إدارة علاقات العملاء -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2"
                            data-bs-toggle="tab"
                            data-bs-target="#plugin-group-5"
                            type="button"
                            role="tab"
                            aria-controls="plugin-group-5"
                            aria-selected="false"
                            id="hrButton">
                        <div class="d-flex align-items-center">
                            <i class="mdi mdi-account-tie app-manager-dropdown-icon"></i>
                            <span class="ms-2">إدارة علاقات العملاء</span>
                        </div>
                        <div class="d-flex align-items-center">
                            <label class="app-manager-role-btn">
                                <span class="app-manager-role-label" data-role-count="26">0/26</span>
                                <input class="app-manager-role-check" type="checkbox" value="" data-role-check-all="plugin-group-5">
                                <span class="app-manager-role-control"></span>
                            </label>
                        </div>
                    </button>

                            <!-- زر الموارد البشرية -->
                            <button class="app-manager-dropdown-app nav-link btn btn-outline-primary w-100 mb-2"
        id="crmButton">
    <div class="d-flex align-items-center">
        <i class="mdi mdi-account-multiple-outline app-manager-dropdown-icon" style="color: #F37535;"></i>
        <span class="ms-2">الموارد البشرية</span>
    </div>
    <div class="d-flex align-items-center">
        <label class="app-manager-role-btn">
            <span class="app-manager-role-label" id="activeCountHR">0/49</span>
            <input class="app-manager-role-check" type="checkbox">
            <span class="app-manager-role-control"></span>
        </label>
    </div>
</button>



                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-8">

            <div id="userContent"><!--End User Content-->



                <!-- النقاط والأرصدة -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

  <div class="col-md-12"                         <!-- قسم النقاط والأرصدة -->
<div class="d-flex justify-content-between align-items-center flex-wrap mb-3">
    <div class="d-flex align-items-center">
        <div class="vs-checkbox-con vs-checkbox-primary me-3">
            <input type="checkbox" id="SelectAllPointsBalances" class="permission-main-checkbox hr-checkbox">
            <span class="vs-checkbox">
                <span class="vs-checkbox--check">
                    <i class="vs-icon feather icon-check"></i>
                </span>
            </span>
            <strong>النقاط والأرصدة</strong>
        </div>
    </div>

    <div class="d-flex align-items-center">
        <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
        <strong class="panel-role-active-count" data-role-count="4">
            <span id="activeCountPointsBalances">0</span>/4
        </strong>
    </div>
</div>


                            <div class="panel-body">
                                <div class="row">
                                    <!-- الزر الأول والثاني في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_packages_manage" type="checkbox" class="select-all-points-balances permission-main-checkbox hr-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة الباقات</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_recharge_manage" type="checkbox" class="select-all-points-balances permission-main-checkbox hr-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة شحن الأرصدة</span>
                                        </div>
                                    </div>

                                    <!-- الزر الثالث والرابع في عمود -->
                                    <div class="col-md-6 col-lg-6">
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_usage_manage" type="checkbox" class="select-all-points-balances permission-main-checkbox hr-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة استهلاك الأرصدة</span>
                                        </div>
                                        <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                            <input name="points_credits_credit_settings_manage" type="checkbox" class="select-all-points-balances permission-main-checkbox hr-checkbox">
                                            <span class="vs-checkbox">
                                                <span class="vs-checkbox--check">
                                                    <i class="vs-icon feather icon-check"></i>
                                                </span>
                                            </span>
                                            <span class="">إدارة إعدادات الأرصدة</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- العضوية -->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">

                            <div class="d-flex justify-content-between align-items-center flex-wrap mb-3">

                                <div class="d-flex align-items-center">
                                    <div class="vs-checkbox-con vs-checkbox-primary me-3">
                                        <input type="checkbox" id="SelectAllMemberships" class="permission-main-checkbox hrButton">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <strong class="">العضوية </strong>
                                    </div>
                                </div>

                                <div class="d-flex align-items-center">
                                    <span class="panel-role-active-title me-2">الصلاحيات النشطة:</span>
                                    <strong class="panel-role-active-count" data-role-count="2"><span id="activeCountMemberships">0</span>/2</strong>
                                </div>
                            </div>
                            <div class="panel-body">
                                <div class="l-flex-row">

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="membership_management" type="checkbox" class="select-all-memberships permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة العضويات</span>
                                    </div>

                                    <div class="vs-checkbox-con vs-checkbox-primary mb-1">
                                        <input name="membership_setting_management" type="checkbox" class="select-all-memberships permission-main-checkbox crm-checkbox">
                                        <span class="vs-checkbox">
                                            <span class="vs-checkbox--check">
                                                <i class="vs-icon feather icon-check"></i>
                                            </span>
                                        </span>
                                        <span class="">أدارة أعدادات العضوية</span>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>







            </form>

        </div><!-- end col-md-8 -->

    </div>

</div>

@endsection

@section('scripts')

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // تعريف الأقسام مع معرفات العناصر الخاصة بها
        const sections = [

            {
                selectAllId: 'SelectAllOrganizationalStructure',
                checkboxesClass: 'select-all-organizational-structure',
                activeCountId: 'activeCountOrganizationalStructure',
            },
            {
                selectAllId: 'SelectAllSalaries',
                checkboxesClass: 'select-all-salaries',
                activeCountId: 'activeCountSalaries',
            },
            {
                selectAllId: 'SelectAllStaffAttendance',
                checkboxesClass: 'select-all-staff-attendance',
                activeCountId: 'activeCountStaffAttendance',
            },
            {
                selectAllId: 'SelectAllOrders',
                checkboxesClass: 'select-all-orders',
                activeCountId: 'activeCountOrders',
            },
            {
                selectAllId: 'SelectAllInventoryManagement',
                checkboxesClass: 'select-all-inventory-management',
                activeCountId: 'activeCountInventoryManagement',
            },
            {
                selectAllId: 'SelectAllProcurementCycle',
                checkboxesClass: 'select-all-procurement-cycle',
                activeCountId: 'activeCountProcurementCycle',
            },
            {
                selectAllId: 'SelectAllSupplyOrderManagement',
                checkboxesClass: 'select-all-supply-order-management',
                activeCountId: 'activeCountSupplyOrderManagement',
            },
            {
                selectAllId: 'SelectAllTrackTime',
                checkboxesClass: 'select-all-track-time',
                activeCountId: 'activeCountTrackTime',
            },
            {
                selectAllId: 'SelectAllRentalUnitManagement',
                checkboxesClass: 'select-all-rental-unit-management',
                activeCountId: 'activeCountRentalUnitManagement',
            },
            {
                selectAllId: 'SelectAllGeneralAccountsDailyRestrictions',
                checkboxesClass: 'select-all-general-accounts-daily-restrictions',
                activeCountId: 'activeCountGeneralAccountsDailyRestrictions',
            },
            {
                selectAllId: 'SelectAllFinance',
                checkboxesClass: 'select-all-finance',
                activeCountId: 'activeCountFinance',
            },
            {
                selectAllId: 'SelectAllSettings',
                checkboxesClass: 'select-all-settings',
                activeCountId: 'activeCountSettings',
            },
            {
                selectAllId: 'SelectAllCheckCycle',
                checkboxesClass: 'select-all-check-cycle',
                activeCountId: 'activeCountCheckCycle',
            },
            {
                selectAllId: 'SelectAllCustomerAttendance',
                checkboxesClass: 'select-all-customer-attendance',
                activeCountId: 'activeCountCustomerAttendance',
            },
            {
                selectAllId: 'SelectAllOnlineStore',
                checkboxesClass: 'select-all-online-store',
                activeCountId: 'activeCountOnlineStore',
            },
            {
                selectAllId: 'SelectAllTargetedSalesCommissions',
                checkboxesClass: 'targeted-sales-commissions',
                activeCountId: 'activeCountTargetedSalesCommissions',
            },
            {
                selectAllId: 'SelectAllOrdersEmployee',
                checkboxesClass: 'select-all-orders-employee',
                activeCountId: 'activeCountOrdersEmployee',
            },
            {
                selectAllId: 'SelectAllStaffAttendanceEmployee',
                checkboxesClass: 'select-all-staff-attendance-employee',
                activeCountId: 'activeCountStaffAttendanceEmployee',
            },
            {
                selectAllId: 'SelectAllSalariesEmployee',
                checkboxesClass: 'select-all-salaries-employee',
                activeCountId: 'activeCountSalariesEmployee',
            }
        ];

        // تحديث عدد الـ checkboxes المحددة
        function updateCheckedCount(checkboxes, activeCountElement) {
            const checkedCount = checkboxes.filter(checkbox => checkbox.checked).length;
            activeCountElement.textContent = checkedCount; // تحديث الرقم في واجهة المستخدم
        }

        // تحديث الأعداد لجميع الأقسام (للمدير "أدمن")
        function updateAllSectionsCounts() {
            sections.forEach(section => {
                const checkboxes = Array.from(document.querySelectorAll(`.${section.checkboxesClass}`));
                const activeCountElement = document.getElementById(section.activeCountId);
                updateCheckedCount(checkboxes, activeCountElement);
            });
        }

        // إضافة الأحداث ومعالجة الأقسام بشكل موحد
        sections.forEach(section => {
            const selectAll = document.getElementById(section.selectAllId);
            const checkboxes = Array.from(document.querySelectorAll(`.${section.checkboxesClass}`));
            const activeCountElement = document.getElementById(section.activeCountId);

            // حدث اختيار "تحديد الكل"
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                updateCheckedCount(checkboxes, activeCountElement);
            });

            // إضافة حدث عند تغيير أي checkbox
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function () {
                    updateCheckedCount(checkboxes, activeCountElement);
                });
            });

            // استدعاء الوظيفة لتحديث العدد عند التحميل
            updateCheckedCount(checkboxes, activeCountElement);
        });

        // مدير (أدمن)
        const adminCheckbox = document.getElementById('adminCheckbox');
        const permissionCheckboxes = document.querySelectorAll('.permission-main-checkbox');

        adminCheckbox.addEventListener('change', function () {
            const isChecked = adminCheckbox.checked;
            permissionCheckboxes.forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            updateAllSectionsCounts(); // تحديث الأعداد لجميع الأقسام
        });

        // استدعاء التحديث عند التحميل
        updateAllSectionsCounts();
    });

    // --------------------------------------------------------------------
    document.addEventListener('DOMContentLoaded', function () {
        // العناصر
        const employeeRadio = document.getElementById('customRadio2');
        const userRadio = document.getElementById('customRadio1');
        const employeeContent = document.getElementById('employeeContent');
        const userContent = document.getElementById('userContent');
        const admin = document.getElementById('admin');

        // تغيير العرض بناءً على الاختيار
        function toggleContent() {
            if (employeeRadio.checked) {
                employeeContent.style.display = 'block';
                userContent.style.display = 'none';
                admin.style.display = 'none';
            } else if (userRadio.checked) {
                employeeContent.style.display = 'none';
                userContent.style.display = 'block';
                admin.style.display = '';
            }
        }

        // إضافة الأحداث
        employeeRadio.addEventListener('change', toggleContent);
        userRadio.addEventListener('change', toggleContent);

        // استدعاء الوظيفة عند التحميل
        toggleContent();
    });

</script>
<script>
    // وظيفة لتحديث العداد بناءً على حالة الشيك بوكس
    function updateLabel(checkboxes, labelId) {
        let checkedCount = Array.from(checkboxes).filter(checkbox => checkbox.checked).length;
        document.getElementById(labelId).innerText = `${checkedCount}/${checkboxes.length}`;
    }

    // عند الضغط على زر إدارة علاقات العملاء (CRM)
    document.getElementById("crmButton").addEventListener("click", function() {
        let checkboxes = document.querySelectorAll('.crm-checkbox');

        // تحديث حالة الشيك بوكس: تفعيل/إلغاء تفعيل جميع الشيك بوكس
        let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        checkboxes.forEach(checkbox => checkbox.checked = !allChecked);

        // تحديث العداد بعد التغيير
        updateLabel(checkboxes, "activeCountPointsBalances");
    });

    // عند الضغط على زر إدارة علاقات الموظفين (HR)
    document.getElementById("hrButton").addEventListener("click", function() {
        let checkboxes = document.querySelectorAll('.hr-checkbox');

        // تحديث حالة الشيك بوكس: تفعيل/إلغاء تفعيل جميع الشيك بوكس
        let allChecked = Array.from(checkboxes).every(checkbox => checkbox.checked);
        checkboxes.forEach(checkbox => checkbox.checked = !allChecked);

        // تحديث العداد بعد التغيير
        updateLabel(checkboxes, "activeCountPointsBalances");
    });
</script>

  <script>
document.addEventListener("DOMContentLoaded", function () {
    // زر تحديد جميع الشيك بوكس الخاصة بالنقاط والأرصدة
    document.getElementById("SelectAllPointsBalances").addEventListener("change", function () {
        toggleCheckboxes(".select-all-points-balances", "activeCountPointsBalances", this.checked);
    });

    // زر تحديد جميع الشيك بوكس الخاصة بالعضوية
    document.getElementById("SelectAllMemberships").addEventListener("change", function () {
        toggleCheckboxes(".select-all-memberships", "activeCountMemberships", this.checked);
    });

    function toggleCheckboxes(selector, counterId, isChecked) {
        let checkboxes = document.querySelectorAll(selector);
        checkboxes.forEach(cb => cb.checked = isChecked);
        updateCounter(checkboxes, counterId);
    }

    function updateCounter(checkboxes, counterId) {
        let checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
        let totalCount = checkboxes.length;
        document.getElementById(counterId).innerText = `${checkedCount}/${totalCount}`;
    }

    // تحديث العداد عند تغيير حالة أي شيك بوكس فردي
    document.querySelectorAll(".permission-main-checkbox").forEach(checkbox => {
        checkbox.addEventListener("change", function () {
            let parentSection = this.closest(".card-body");
            let counter = parentSection.querySelector(".panel-role-active-count span");
            if (counter) {
                let allCheckboxes = parentSection.querySelectorAll(".permission-main-checkbox");
                updateCounter(allCheckboxes, counter.id);
            }
        });
    });
});

    </script>

@endsection

