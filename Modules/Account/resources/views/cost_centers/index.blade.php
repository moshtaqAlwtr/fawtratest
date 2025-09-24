<!DOCTYPE html>
<html class="loading" lang="en" data-textdirection="rtl">
<!-- BEGIN: Head-->

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
    <meta name="description" content="Vuexy admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Vuexy admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="PIXINVENT">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>مراكز التكلفة</title>
    <link rel="apple-touch-icon" href="../../../app-assets/images/ico/apple-icon-120.png">
    <link rel="shortcut icon" type="image/x-icon" href="../../../app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600" rel="stylesheet">

    <!-- BEGIN: Vendor CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/vendors/css/vendors-rtl.min.css">
    <!-- END: Vendor CSS-->

    <!-- BEGIN: Theme CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/bootstrap-extended.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/colors.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/components.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/dark-layout.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/themes/semi-dark-layout.css">

    <!-- BEGIN: Page CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/menu/menu-types/vertical-menu.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/core/colors/palette-gradient.css">
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/pages/app-chat.css">
    <!-- END: Page CSS-->

    <!-- BEGIN: Custom CSS-->
    <link rel="stylesheet" type="text/css" href="../../../app-assets/css-rtl/custom-rtl.css">
    <link rel="stylesheet" type="text/css" href="../../../assets/css/style-rtl.css">
    <!-- END: Custom CSS-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="{{ asset('assets/fonts/Cairo/stylesheet.css') }}">
    <style>
        body, h1, h2, h3, h4, h5, h6,.navigation,.header-navbar,.breadcrumb {
            font-family: 'Cairo';
        }
        #tree li {
            margin-bottom: 10px;
        }
        #tree ul {
            padding-right: 20px;
        }
        /* #jstree li a .jstree-icon {
            margin-left: 5px;
        } */
        .profile-picture-header {
            width: 40px;
            height: 40px;
            background-color: #7367F0;
            color: white;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        table {
            max-height: 400px;
            overflow-y: auto;  /* إضافة شريط تمرير رأسي */
            display: block;
        }
        .table-container {
            max-height: 400px; /* تحديد ارتفاع الحاوية */
            overflow-y: auto;  /* إضافة شريط التمرير */
        }
        #table-body tr {
            cursor: pointer;
        }
        #table-body tr:hover {
            background-color: #f0f8ff;
        }
    </style>

</head>
<!-- END: Head-->

<!-- BEGIN: Body-->

<body class="vertical-layout vertical-menu-modern content-left-sidebar chat-application navbar-floating footer-static   menu-collapsed" data-open="click" data-menu="vertical-menu-modern" data-col="content-left-sidebar">

    <!-- BEGIN: Header-->
        @include('layouts.header')
    <!-- END: Header-->

    <!-- BEGIN: Main Menu-->
        @include('layouts.sidebar')
    <!-- END: Main Menu-->

    <!-- BEGIN: Content-->
    <div class="app-content content">
        <div class="content-overlay"></div>
        <div class="header-navbar-shadow"></div>
        <div class="content-area-wrapper">
            <div class="sidebar-left">
                <div class="sidebar">
                    <!-- User Chat profile area -->
                    <div class="chat-profile-sidebar">
                        <header class="chat-profile-header">
                            <span class="close-icon">
                                <i class="feather icon-x"></i>
                            </span>
                        </header>
                    </div>
                    <!--/ User Chat profile area -->

                    <!-- Chat Sidebar area -->
                    <div class="sidebar-content card">
                        <span class="sidebar-close-icon">
                            <i class="feather icon-x"></i>
                        </span>
                        <div class="chat-fixed-search" style="position: absolute">
                            <div class="d-flex align-items-center">
                                <fieldset class="form-group position-relative has-icon-left mx-1 my-0 w-50">
                                    <input type="text" class="form-control"  placeholder="بحث">
                                    <div class="form-control-position">
                                        <i class="feather icon-search"></i>
                                    </div>
                                </fieldset>
                                <fieldset class="form-group position-relative mx-1 my-0 w-50">
                                    <select name="" class="form-control select2" id="">
                                        <option value="all">كل الفروع</option>
                                        <option value="online">Online</option>
                                        <option value="offline">Offline</option>
                                        <option value="away">Away</option>
                                        <option value="busy">Busy</option>
                                    </select>
                                </fieldset>
                            </div>
                        </div>
                        <div id="users-list" class="list-group position-relative" style="margin-top: 5rem">
                            <!-- 3 setup a container element -->
                            <div id="tree">
                                <!-- tree will be rendered here -->
                            </div>
                        </div>
                    </div>
                    <!--/ Chat Sidebar area -->

                </div>
            </div>
            <div class="content-right">
                <div class="content-wrapper">
                    <div class="content-header row">
                    </div>
                    <div class="content-body">
                        <div class="chat-overlay"></div>
                        <section class="chat-app-window">

                            <div class="card" style="height: calc(var(--vh, 1vh) * 100 - 13rem)">
                                <div class="card-content">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div>
                                                <span></span>
                                            </div>
                                            <div></div>
                                        </div>
                                        <hr>

                                        <div class="">
                                            <table class="table" dir="rtl">
                                                <tbody id="table-body">

                                                </tbody>
                                            </table>
                                        </div>

                                        <button id="addAccountModalButton" class="btn btn-outline-info btn-sm waves-effect waves-light" data-toggle="modal" data-target="#info-modal-account">
                                            <i class="fa fa-plus-circle me-2"></i>أضف حساب
                                        </button>

                                        <!-- Modal -->
                                        <div class="modal fade text-left" id="info-modal-account" tabindex="-1" role="dialog" aria-labelledby="myModalLabel130" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                        <h5 class="modal-title" id="myModalLabel130">أضف حساب</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="addAccountForm" action="/cost_centers/store_account" method="POST">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="name-info-vertical">الاسم</label>
                                                                        <input type="text" id="accountName" class="form-control" name="name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="code-id-vertical">الكود</label>
                                                                        <input type="number" id="accountCode" class="form-control" name="code">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-vertical">مركز التكلفة الرئيسي</label>
                                                                        <select name="parent_id" id="" class="form-control">
                                                                            <option value="">اختر مركز تكلفة</option>
                                                                            @foreach ($accounts as $account)
                                                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <div class="vs-checkbox-con vs-checkbox-primary mt-2">
                                                                            <input type="checkbox" name="is_main" value="1">
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span class="">مركز تكلفة رئيسي ؟</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                        <button type="submit" class="btn btn-info" form="addAccountForm">حفظ</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- End Modal -->

                                        <!--- Edit Modal--->
                                        <div class="modal fade" id="editAccountModal" tabindex="-1" role="dialog" aria-labelledby="editAccountModalLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-info white">
                                                        <h5 class="modal-title" id="myModalLabel130">تعديل الحساب</h5>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times;</span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="editAccountForm">
                                                            @csrf
                                                            <div class="row">
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="name-info-vertical">الاسم</label>
                                                                        <input type="text" id="accountName" class="form-control" name="name">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="code-id-vertical">الكود</label>
                                                                        <input type="number" id="accountCode" class="form-control" name="code">
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <label for="account-vertical">مركز التكلفة الرئيسي</label>
                                                                        <select name="parent_id" id="" class="form-control">
                                                                            <option value="">اختر مركز تكلفة</option>
                                                                            @foreach ($accounts as $account)
                                                                                <option value="{{ $account->id }}">{{ $account->name }}</option>
                                                                            @endforeach
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-6">
                                                                    <div class="form-group">
                                                                        <div class="vs-checkbox-con vs-checkbox-primary mt-2">
                                                                            <input type="checkbox" name="is_main" value="1">
                                                                            <span class="vs-checkbox">
                                                                                <span class="vs-checkbox--check">
                                                                                    <i class="vs-icon feather icon-check"></i>
                                                                                </span>
                                                                            </span>
                                                                            <span class="">مركز تكلفة رئيسي ؟</span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">إغلاق</button>
                                                        <button type="submit" class="btn btn-info" form="editAccountForm">حفظ التعديلات</button>
                                                    </div>

                                                </div>
                                            </div>
                                        </div>
                                        <!--- Edit Modal--->

                                    </div>
                                </div>
                            </div>

                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- END: Content-->

    <div class="sidenav-overlay"></div>
    <div class="drag-target"></div>

    <!-- BEGIN: Footer-->
        @include('layouts.footer')
    <!-- END: Footer-->


    <!-- BEGIN: Vendor JS-->
    <script src="../../../app-assets/vendors/js/vendors.min.js"></script>
    <!-- BEGIN Vendor JS-->

    <!-- BEGIN: Theme JS-->
    <script src="../../../app-assets/js/core/app-menu.js"></script>
    <script src="../../../app-assets/js/core/app.js"></script>
    <script src="../../../app-assets/js/scripts/components.js"></script>
    <!-- END: Theme JS-->

    <!-- BEGIN: Page JS-->
    <script src="../../../app-assets/js/scripts/pages/app-chat.js"></script>
    <!-- END: Page JS-->

    <!-- 5 include the minified jstree source -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        $(document).ready(function() {
            // جلب بيانات الشجرة من الـ API
            $('#tree').jstree({
                'core': {
                    'themes': {
                            'rtl': true,
                            // 'dots': false,
                            'icons': true
                        },

                    'data': {
                        'url': '/cost_centers/tree', // رابط الـ API
                        'dataType': 'json'
                    }
                }
            });

        });

        $(document).ready(function () {
            // جلب الآباء عند تحميل الصفحة
            $.ajax({
                url: '/cost_centers/parents', // رابط الـ API لجلب الآباء
                type: 'GET',
                success: function (parents) {
                    const tableBody = $('#table-body');
                    tableBody.empty(); // تفريغ الجدول

                    // إضافة الآباء إلى الجدول
                    parents.forEach(parent => {
                        tableBody.append(`
                            <tr data-node-id="${parent.id}" class="table-active">
                                <td style="width: 3%">
                                    <i class="feather icon-folder" style="font-size: 30px"></i>
                                </td>
                                <td>
                                    <strong>${parent.name}</strong><br>
                                    <small>${parent.code} #</small>
                                </td>

                                <td style="width: 10%">
                                    <div class="operation">
                                        <a id="edit" href="#" class="edit-button"><i class="fa fa-edit mr-1"></i></a>
                                        <a id="delete" href="#" class="text-danger" onclick="confirmDelete('${parent.id}')">
                                            <i class="fa fa-trash mr-1"></i>
                                        </a>
                                    </div>
                                </td>

                            </tr>
                        `);
                    });
                }
            });

            // التعامل مع اختيار عقدة من الشجرة
            $('#tree').on('select_node.jstree', function (e, data) {
                const nodeId = data.node.id; // ID العقدة المختارة

                // جلب بيانات الأبناء
                $.ajax({
                    url: `/cost_centers/${nodeId}/children`, // استعلام عن أبناء العقدة
                    type: 'GET',
                    success: function (children) {
                        const tableBody = $('#table-body');
                        tableBody.empty(); // تفريغ الجدول

                        children.forEach(child => {
                            tableBody.append(`
                                <tr data-node-id="${child.id}" class="table-active">
                                    <td style="width: 3%">
                                        <i class="feather icon-folder" style="font-size: 30px"></i>
                                    </td>
                                    <td>
                                        <strong>${child.name}</strong><br>
                                        <small>${child.code} #</small>
                                    </td>

                                    <td style="width: 10%">
                                        <div class="operation">
                                            <a id="edit" href="#" class="edit-button"><i class="fa fa-edit mr-1"></i></a>
                                            <a id="delete" href="#" class="text-danger" onclick="confirmDelete('${child.id}')">
                                                <i class="fa fa-trash mr-1"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            `);
                        });
                    }
                });
            });

        });

        // حدث عند الضغط على صف في الجدول
        $('#table-body').on('click', 'tr', function () {
            const nodeId = $(this).data('node-id'); // الحصول على ID العقدة من الصف

            if (nodeId) {
                // فتح وتحديد العقدة في الشجرة
                $('#tree').jstree('deselect_all'); // إلغاء تحديد أي عقدة محددة
                $('#tree').jstree('select_node', nodeId); // تحديد العقدة
                $('#tree').jstree('open_node', nodeId); // فتح العقدة
            }
        });

        $('#table-body').on('click', '.operation', function (event) {
            event.stopPropagation(); // منع الحدث من الانتقال إلى الصف
        });
    </script>

    <script>
        $(document).ready(function () {
            // فتح المودال وتعبئة الحقول
            $('#addAccountModalButton').on('click', function () {
                const selectedNode = $('#tree').jstree('get_selected', true)[0]; // الحصول على العقدة المحددة
                const parentId = selectedNode ? selectedNode.id : null;

                if (parentId) {
                    // إذا تم تحديد عقدة
                    $('select[name="parent_id"]').val(parentId); // ضبط الحساب الرئيسي
                    $('select[name="type"]').val('sub'); // افتراضيًا، الحساب فرعي

                    // جلب الكود الجديد
                    generateSequentialCode(parentId).done(function (response) {
                        $('#accountCode').val(response.nextCode); // تعيين الكود الجديد
                    }).fail(function () {
                        console.error('فشل جلب الكود الجديد');
                    });

                    // جلب تفاصيل الحساب لتحديد النوع
                    getAccountDetails(parentId).done(function (response) {
                        if (response.success) {
                            const mainAccountName = response.category;

                            if (['الأصول', 'الدخل'].includes(mainAccountName)) {
                                $('#customRadio1').prop('checked', true); // النوع دائن
                            } else if (['الخصوم', 'المصروفات'].includes(mainAccountName)) {
                                $('#customRadio2').prop('checked', true); // النوع مدين
                            }
                        } else {
                            console.error('فشل جلب تفاصيل الحساب');
                        }
                    }).fail(function () {
                        console.error('فشل في الاتصال بالـ API لجلب التفاصيل');
                    });

                } else {
                    // إذا لم يتم تحديد أي عقدة
                    $('select[name="parent_id"]').val(''); // لا يوجد حساب رئيسي
                    $('select[name="type"]').val('main'); // الحساب رئيسي
                    $('#customRadio1').prop('checked', true); // النوع الافتراضي دائن
                    $('#accountCode').val(1); // الكود يبدأ بـ 1
                }

                // فتح المودال
                $('#info-modal-account').modal('show');
            });

            // ضبط الكود بشكل تلقائي عند تغيير الحقل
            $('select[name="parent_id"]').on('change', function () {
                const parentId = $(this).val();

                if (parentId) {
                    generateSequentialCode(parentId).done(function (response) {
                        $('#accountCode').val(response.nextCode); // تعيين الكود الجديد
                    }).fail(function () {
                        console.error('فشل جلب الكود الجديد');
                    });
                } else {
                    $('#accountCode').val(1); // افتراضيًا، اجعل الكود يبدأ بـ 1
                }
            });

            // دالة لجلب تفاصيل الحساب
            function getAccountDetails(parentId) {
                return $.ajax({
                    url: `/cost_centers/${parentId}/details`, // رابط API لجلب تفاصيل الحساب
                    type: 'GET',
                    dataType: 'json',
                });
            }

            // دالة لتوليد الكود الجديد
            function generateSequentialCode(parentId) {
                return $.ajax({
                    url: `/cost_centers/${parentId}/next-code`, // رابط API لجلب الكود الجديد
                    type: 'GET',
                    dataType: 'json',
                });
            }

            // ADD ACCOUNT ###################################################################################

            // عند تقديم النموذج
            $('#addAccountForm').on('submit', function (e) {
                e.preventDefault();

                const formData = $(this).serialize();

                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    data: formData,
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                title: 'تم بنجاح!',
                                text: response.message || 'تمت العملية بنجاح.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false,
                            });

                            // تحديث الشجرة
                            $('#tree').jstree('refresh');

                            // إعادة تحميل الحسابات الرئيسية في المودال
                            updateParentAccounts();

                            // إغلاق المودال
                            setTimeout(function () {
                                $('#info-modal-account').removeClass('show');
                                $('body').removeClass('modal-open');
                                $('.modal-backdrop').remove();
                            });

                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: response.message || 'حدث خطأ أثناء العملية.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function (xhr) {
                        if (xhr.status === 422) {
                            // عرض الأخطاء
                            const errors = xhr.responseJSON.errors;
                            let errorMessages = '';

                            for (const field in errors) {
                                if (errors.hasOwnProperty(field)) {
                                    errorMessages += `${errors[field].join('<br>')}<br>`;
                                }
                            }

                            Swal.fire({
                                title: 'خطأ في التحقق!',
                                html: errorMessages,
                                icon: 'error',
                            });
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: 'حدث خطأ أثناء التنفيذ. الرجاء المحاولة مرة أخرى.',
                                icon: 'error',
                            });
                        }
                    }
                });
            });


        });
    </script>

    <script>
        function updateParentAccounts() {
            $.ajax({
                url: '/cost_centers/parents', // رابط API لجلب الحسابات الرئيسية
                type: 'GET',
                success: function (accounts) {
                    const parentSelect = $('select[name="parent_id"]');
                    parentSelect.empty(); // تفريغ الخيارات
                    parentSelect.append('<option value="">لا يوجد حساب رئيسي</option>');

                    accounts.forEach(account => {
                        parentSelect.append(`<option value="${account.id}">${account.name}</option>`);
                    });
                },
            });
        }

    </script>

    <script>
        function confirmDelete(parentId) {
            event.preventDefault();

            Swal.fire({
                title: 'هل أنت متأكد؟',
                text: 'لن تتمكن من استعادة هذا العنصر بعد الحذف!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'نعم، احذفه!',
                cancelButtonText: 'إلغاء'
            }).then((result) => {
                if (result.isConfirmed) {
                    // استدعاء AJAX لتنفيذ الحذف
                    $.ajax({
                        url: `/cost_centers/${parentId}/delete`,
                        type: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        success: function(response) {
                            Swal.fire({
                                title: 'تم الحذف!',
                                text: response.message || 'تم حذف العنصر بنجاح.',
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            });
                            $(`tr[data-node-id="${parentId}"]`).remove();
                            $('#tree').jstree('refresh');
                        },
                        error: function(xhr) {
                            console.error(xhr.responseText); // فحص الخطأ
                            Swal.fire({
                                title: 'خطأ!',
                                text: xhr.responseJSON?.message || 'حدث خطأ أثناء الحذف. الرجاء المحاولة مرة أخرى.',
                                icon: 'error',
                                timer: 2000,
                                showConfirmButton: false
                            });
                        }
                    });
                }
            });
        }
    </script>

    <script>
        let openedNodes = [];
        let selectedNode = null;

        // احفظ الحالة قبل التحديث
        $('#tree').on('state_ready.jstree', function (e, data) {
            openedNodes = $('#tree').jstree('get_opened');
            selectedNode = $('#tree').jstree('get_selected')[0];
        });

        $(document).ready(function () {
            $('#tree').on('loaded.jstree', function () {
                // إعادة فتح العقد المحفوظة
                if (openedNodes.length) {
                    $('#tree').jstree('open_node', openedNodes);
                }

                // تحديد العقدة المحفوظة
                if (selectedNode) {
                    $('#tree').jstree('select_node', selectedNode);
                }
            });
        });

        function reloadPageWithTreeState() {
            openedNodes = $('#tree').jstree('get_opened'); // احفظ العقد المفتوحة
            selectedNode = $('#tree').jstree('get_selected')[0]; // احفظ العقدة المحددة

            // خزّن الحالة في localStorage
            localStorage.setItem('openedNodes', JSON.stringify(openedNodes));
            localStorage.setItem('selectedNode', selectedNode);

            // أعد تحميل الصفحة
            location.reload();
        }

        $(document).ready(function () {
            // استرجاع الحالة من localStorage
            let savedOpenedNodes = JSON.parse(localStorage.getItem('openedNodes')) || [];
            let savedSelectedNode = localStorage.getItem('selectedNode') || null;

            $('#tree').on('loaded.jstree', function () {
                // إعادة فتح العقد المحفوظة
                if (savedOpenedNodes.length) {
                    $('#tree').jstree('open_node', savedOpenedNodes);
                }

                // تحديد العقدة المحفوظة
                if (savedSelectedNode) {
                    $('#tree').jstree('select_node', savedSelectedNode);
                }

                // تنظيف البيانات المحفوظة
                localStorage.removeItem('openedNodes');
                localStorage.removeItem('selectedNode');
            });
        });

        // إضافة حدث عند الضغط على زر التعديل
        $('#table-body').on('click', '.edit-button', function () {
            const nodeId = $(this).closest('tr').data('node-id'); // الحصول على ID العنصر
            $('#editAccountModal').data('node-id', nodeId);

            if (nodeId) {
                // جلب بيانات العنصر
                $.ajax({
                    url: `/cost_centers/${nodeId}/edit`, // رابط API لجلب بيانات العنصر
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            // تعبئة المودال بالبيانات
                            $('#editAccountModal input[name="name"]').val(response.data.name);
                            $('#editAccountModal input[name="code"]').val(response.data.code);
                            $('#editAccountModal select[name="parent_id"]').val(response.data.parent_id);
                            $(`#editAccountModal input[name="is_main"][value="${response.data.is_main}"]`).prop('checked', true);

                            // عرض المودال
                            $('#editAccountModal').modal('show');
                        } else {
                            Swal.fire({
                                title: 'خطأ!',
                                text: response.message || 'حدث خطأ أثناء جلب البيانات.',
                                icon: 'error',
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            title: 'خطأ!',
                            text: 'حدث خطأ أثناء جلب البيانات.',
                            icon: 'error',
                        });
                    }
                });
            }
        });

        // احفظ تعديلات الحساب
        $('#editAccountForm').on('submit', function (e) {
            e.preventDefault();

            const nodeId = $('#editAccountModal').data('node-id');
            if (!nodeId) {
                Swal.fire({
                    title: 'خطأ!',
                    text: 'رقم الحساب غير موجود.',
                    icon: 'error',
                });
                return;
            }

            const formData = $(this).serialize();

            $.ajax({
                url: `/cost_centers/${nodeId}/update`,
                type: 'PUT',
                data: formData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            title: 'تم التعديل!',
                            text: response.message || 'تم تعديل البيانات بنجاح.',
                            icon: 'success',
                            timer: 2000,
                            showConfirmButton: false,
                        });

                        const row = $('#table-body').find(`tr[data-node-id="${nodeId}"]`);
                        if (row.length) {
                            row.html(`
                                <td>${response.data.code}</td>
                                <td>${response.data.name}</td>
                                <td>${response.data.balance}</td>
                                <td>${response.data.balance_type === 'credit' ? 'دائن' : 'مدين'}</td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-button">تعديل</button>
                                </td>
                            `);
                        } else {
                            console.error('لم يتم العثور على الصف المطلوب.');
                        }

                        // إغلاق المودال
                        setTimeout(function () {
                            $('#editAccountModal').removeClass('show');
                            $('body').removeClass('modal-open');
                            $('.modal-backdrop').remove();
                        });

                        // تحديث الشجرة
                        $('#tree').jstree('refresh');

                    } else {
                        Swal.fire({
                            title: 'خطأ!',
                            text: response.message || 'حدث خطأ أثناء التعديل.',
                            icon: 'error',
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        title: 'خطأ!',
                        text: 'حدث خطأ أثناء التعديل.',
                        icon: 'error',
                    });
                }
            });
        });

    </script>


</body>
<!-- END: Body-->

</html>
