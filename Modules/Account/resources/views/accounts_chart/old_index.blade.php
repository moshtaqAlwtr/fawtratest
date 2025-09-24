@extends('master')

@section('title')
    دليل الحسابات
@stop

@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/tree-style.css') }}">
@endsection

@section('content')
    <!-- العنوان -->
    <div class="content-header row">
        <div class="content-header-left col-md-9 col-12 mb-2">
            <div class="row breadcrumbs-top">
                <div class="col-12">
                    <h2 class="content-header-title float-left mb-0">دليل الحسابات</h2>
                    <div class="breadcrumb-wrapper col-12">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="">الرئيسيه</a>
                            </li>
                            <li class="breadcrumb-item active">عرض
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif






    <div class="app-container">
        <!-- Sidebar -->
        <div class="card" style="height:90vh;">
            <div class="row">
                <div class="input-group col-6">
                    <input type="text" class="form-control" placeholder="ابحث">

                </div>
                <div class="col-6">
                    <select class="form-select select2" style="width: 200px;">
                        <option selected>فرع العربية</option>
                        <option>الفرع الرئيسي</option>
                        <option>الفرع الثاني</option>
                    </select>
                </div>


            </div>

            <ul id="sidebar-menu">




                <!-- Dashboard -->
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-folder-open"></i>
                        <span>الاصول </span>
                    </a>
                    <ul class="dropdown-menu">

                        <li>
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-folder-open"></i>
                                <span>الاصول الثابتة </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="sidebar-link" data-id="5">
                                        <i class="fas fa-file"></i>
                                        <span>مباني </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-link" data-id="6">
                                        <i class="fas fa-file"></i>
                                        <span> مستودعات</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link" data-id="3">
                                <i class="fas fa-folder-open"></i>
                                <span>الاصول المتدوالة </span>
                            </a>
                        </li>
                    </ul>
                </li>
                <!-- Settings -->
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-folder-open"></i>
                        <span>الخصوم </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="sidebar-link" data-id="3">
                                <i class="fas fa-folder-open"></i>
                                <span> الخصوم المتدوالة</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="dropdown-toggle">
                                <i class="fas fa-folder-open"></i>
                                <span> راس المال وحقوق الملكية </span>
                            </a>
                            <ul class="dropdown-menu">
                                <li>
                                    <a href="#" class="sidebar-link" data-id="5">
                                        <i class="fas fa-lock"></i>
                                        <span>
                                            حقوق الملكية
                                        </span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#" class="sidebar-link" data-id="6">
                                        <i class="fas fa-mobile-alt"></i>
                                        <span>تطبيقات الجوال</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </li>
                <!-- Reports -->
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-folder-open"></i>
                        <span>المصروفات </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="sidebar-link" data-id="8">
                                <i class="fas fa-file-alt"></i>
                                <span> مصروفات اخرى</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link" data-id="9">
                                <i class="fas fa-file-alt"></i>
                                <span> مصروفات الاهلاك</span>
                            </a>
                        </li>
                    </ul>
                </li>
                <li>
                    <a href="#" class="dropdown-toggle">
                        <i class="fas fa-folder-open"></i>
                        <span>الايرادات </span>
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a href="#" class="sidebar-link" data-id="8">
                                <i class="fas fa-file-alt"></i>
                                <span> مبيعات </span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="sidebar-link" data-id="9">
                                <i class="fas fa-file-alt"></i>
                                <span> المرتجعات</span>
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
        <!-- Main Content -->
        <div id="main-content" class="main-content">
            <div class="accounts-list">
                <!-- سيتم تحديث هذا القسم ديناميكياً عبر JavaScript -->
            </div>
            <div class="add-account-link">
                <a href="#" class="add-account-btn" data-toggle="modal" data-target="#addAccountModal">
                    <i class="fas fa-plus-circle"></i>
                    أضف حساب
                </a>
            </div>
        </div>

        <!-- Modal إضافة حساب -->


        <form id="addAccountForm" action="#" method="POST">
            <div class="modal fade" id="addAccountModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title">أضف حساب</h5>
                            <button type="button" class="close" data-dismiss="modal">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <form id="addAccountForm">
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">اسم الحساب</label>
                                        <input type="text" class="form-control" name="name">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">الكود</label>
                                        <input type="text" class="form-control" name="code">
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label">حساب رئيسي</label>
                                        <select class="form-control" name="name">
                                            @foreach ($allAccounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                </div>

                                <div class="row mb-3">
                                    <div class="col-12">
                                        <label class="form-label">النوع</label>
                                        <div class="d-flex gap-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="normal_balance"
                                                    id="debit" checked name="normal_balance	">
                                                <label class="form-check-label" for="debit">
                                                    مدين
                                                </label>
                                            </div>
                                            <div class="form-check">
                                                <input class="form-check-input" type="radio" name="normal_balance"
                                                    id="credit">
                                                <label class="form-check-label" for="credit">
                                                    دائن
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col-12">
                                        <label class="form-label">تحكم كامل</label>
                                        <select class="form-control">
                                            <option>الكل</option>
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-success">حفظ</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection

@section('scripts')
    <script src="https://kit.fontawesome.com/597cb1f685.js" crossorigin="anonymous"></script>
    <script>
        // دالة لتنسيق الأرقام بالتنسيق العربي مع خانتين عشريتين
        function formatNumber(num) {
            return new Intl.NumberFormat('ar-SA', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            }).format(num);
        }

        // عند اكتمال تحميل الصفحة
        document.addEventListener('DOMContentLoaded', () => {
            // الحصول على عنصر قائمة الحسابات في الصفحة
            const accountsList = document.querySelector('.accounts-list');

            // دالة لتحديث محتوى قائمة الحسابات
            function updateContent(title, items) {

                // تحويل مصفوفة العناصر إلى HTML
                accountsList.innerHTML = items.map(item => `
                <div class="account-item" onclick="handleItemClick(this, '${item.id}', '${item.name}', ${item.hasSubmenu})">
                    <div class="account-details">
                        <!-- اسم الحساب وأيقونته ورقمه -->
                        <div class="account-name">
                            <i class="fas ${item.icon} text-primary"></i>
                            <span>${item.name}</span>
                            <span class="account-number">#${item.id}</span>
                        </div>
                        <!-- معلومات الحساب (الرصيد والنوع) -->
                        <div class="account-info">
                            <div class="account-balance">${formatNumber(0.00)}</div>
                            <div class="account-type">${item.type}</div>
                        </div>
                        <!-- أزرار الإجراءات (عرض، تعديل، حذف) -->
                        <div class="account-actions">
                            <div class="btn-group">
                                <div class="dropdown">
                                    <button class="btn bg-gradient-info fa fa-ellipsis-v mr-1 mb-1 btn-sm"
                                        type="button" onclick="event.stopPropagation()" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false"></button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton303">
                                        <li><a class="dropdown-item" href="#" onclick="event.stopPropagation()">
                                                <i class="fa fa-eye me-2 text-primary"></i>عرض
                                            </a></li>
                                        <li><a class="dropdown-item" href="#" onclick="event.stopPropagation()">
                                                <i class="fa fa-edit me-2 text-success"></i>تعديل
                                            </a></li>
                                        <li><a class="dropdown-item text-danger" href="#" onclick="event.stopPropagation()">
                                                <i class="fa fa-trash me-2"></i>حذف
                                            </a></li>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                `).join('');
            }

            // دالة معالجة النقر على العنصر
            window.handleItemClick = function(element, itemId, itemName, hasSubmenu) {
                // البحث عن العنصر في القائمة الجانبية
                const sidebarLinks = document.querySelectorAll('#sidebar-menu a');
                let targetElement = null;

                // البحث عن العنصر المطابق في القائمة الجانبية
                for (const link of sidebarLinks) {
                    if (link.querySelector('span')?.textContent.trim() === itemName) {
                        targetElement = link;
                        break;
                    }
                }

                if (targetElement) {
                    // البحث عن القائمة الفرعية للعنصر
                    const parentLi = targetElement.closest('li');
                    const submenu = parentLi.querySelector('.dropdown-menu');

                    if (submenu) {
                        // استخراج العناصر الفرعية
                        const subItems = Array.from(submenu.children)
                            .filter(li => li.querySelector('a'))
                            .map(li => {
                                const link = li.querySelector('a');
                                const subSubmenu = li.querySelector('.dropdown-menu');
                                return {
                                    name: link.querySelector('span').textContent.trim(),
                                    icon: subSubmenu ? 'fa-folder' : 'fa-file-alt',
                                    id: link.dataset.id || '00',
                                    type: subSubmenu ? 'فرعي' : 'حساب',
                                    hasSubmenu: !!subSubmenu
                                };
                            });

                        // تحديث العرض بالعناصر الفرعية
                        if (subItems.length > 0) {
                            updateContent(itemName, subItems);
                        }
                    }
                }
            };

            // تحديث معالج النقر على القوائم المنسدلة
            const dropdownToggles = document.querySelectorAll('.dropdown-toggle');
            dropdownToggles.forEach(toggle => {
                toggle.addEventListener('click', event => {
                    event.preventDefault();
                    const submenu = toggle.nextElementSibling;

                    if (submenu) {
                        submenu.classList.toggle('visible');
                        const directItems = Array.from(submenu.children)
                            .filter(li => li.querySelector('a'))
                            .map(li => {
                                const link = li.querySelector('a');
                                const hasSubmenu = li.querySelector('.dropdown-menu') !== null;
                                return {
                                    name: link.querySelector('span').textContent.trim(),
                                    icon: hasSubmenu ? 'fa-folder' : 'fa-file-alt',
                                    id: link.dataset.id || '00',
                                    type: hasSubmenu ? 'فرعي' : 'حساب',
                                    hasSubmenu: hasSubmenu
                                };
                            });

                        updateContent(toggle.querySelector('span').textContent.trim(), directItems);
                    }
                });
            });

            // الحصول على القوائم الرئيسية وعرضها عند تحميل الصفحة
            const mainMenuItems = Array.from(document.querySelectorAll('#sidebar-menu > li > a'))
                .map(link => ({
                    name: link.querySelector('span').textContent.trim(),
                    icon: 'fa-folder-open',
                    type: 'حساب رئيسي',
                    id: '0'
                }));

            // عرض القوائم الرئيسية عند تحميل الصفحة
            updateContent('القائمة الرئيسية', mainMenuItems);
        });

    </script>

@endsection
