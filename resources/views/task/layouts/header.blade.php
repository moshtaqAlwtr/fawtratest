<header class="main-header clearfix bg-white" id="header">
    <!-- NAVBAR LEFT (MOBILE MENU COLLAPSE) -->
    <div class="navbar-left float-right d-flex align-items-center">
        <!-- PAGE TITLE -->
        <div class="page-title d-none d-lg-flex">
            <div class="page-heading">
                <h2 class="mb-0 pr-3 text-dark f-18 font-weight-bold d-flex align-items-center">
                    <span class="d-inline-block text-truncate mw-300" id="page-title">المشاريع</span>
                </h2>
            </div>
        </div>

        <!-- MOBILE MENU TOGGLE -->
        <div class="d-block d-lg-none menu-collapse cursor-pointer position-relative" onclick="openMobileMenu()">
            <div class="mc-wrap">
                <div class="mcw-line"></div>
                <div class="mcw-line center"></div>
                <div class="mcw-line"></div>
            </div>
        </div>
    </div>

    <!-- NAVBAR RIGHT (SEARCH, ADD, NOTIFICATION, LOGOUT) -->
    <div class="page-header-right left d-flex align-items-center justify-content-end">
        <!-- TIMER -->
        <span id="timer-clock">
            <span class="border rounded f-14 py-2 px-2 d-none d-sm-block mr-3">
                <span id="active-timer" class="mr-2">00:00:00</span>
                <a href="javascript:;" class="resume-active-timer mr-1 border-right" data-toggle="tooltip" data-original-title="استئناف">
                    <i class="fa fa-play-circle text-primary"></i>
                </a>
                <a href="javascript:;" class="stop-active-timer" data-toggle="tooltip" data-original-title="إيقاف الموقت">
                    <i class="fa fa-stop-circle text-danger"></i>
                </a>
            </span>
        </span>

        <!-- NAV ITEMS -->
        <ul class="d-flex align-items-center mb-0">
            <!-- CHAT -->
            <li data-toggle="tooltip" data-placement="top" title="المحادثات" class="d-none d-sm-block">
                <div class="d-flex align-items-center">
                    <a href="messages.html" class="d-block header-icon-box">
                        <i class="bi bi-chat-right-text f-16 text-dark-grey"></i>
                    </a>
                </div>
            </li>

            <!-- SEARCH -->
            <li data-toggle="tooltip" data-placement="top" title="بحث" class="d-none d-sm-block">
                <div class="d-flex align-items-center">
                    <a href="javascript:;" class="d-block header-icon-box open-search">
                        <i class="bi bi-search f-16 text-dark-grey"></i>
                    </a>
                </div>
            </li>

            <!-- STICKY NOTES -->
            <li data-toggle="tooltip" data-placement="top" title="الملاحظات" class="d-none d-sm-block">
                <div class="d-flex align-items-center">
                    <a href="sticky-notes.html" class="d-block header-icon-box openRightModal">
                        <i class="bi bi-sticky f-16 text-dark-grey"></i>
                    </a>
                </div>
            </li>

            <!-- TIMER -->
            <li data-toggle="tooltip" data-placement="top" title="بدء المؤقت">
                <div class="add_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box" type="link" id="show-active-timer" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-stopwatch f-16 text-dark-grey"></i>
                        <span class="badge badge-primary active-timer-count position-absolute">0</span>
                    </a>
                </div>
            </li>

            <!-- ADD BUTTON -->
            <li data-toggle="tooltip" data-placement="top" title="إضافة">
                <div class="add_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box" type="link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-plus-circle f-16 text-dark-grey"></i>
                    </a>
                    <!-- DROPDOWN MENU -->
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink" tabindex="0">
                        <a class="dropdown-item f-14 text-dark openRightModal" href="projects/create">
                            <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> مشروع جديد
                        </a>
                        <a class="dropdown-item f-14 text-dark openRightModal" href="tasks/create">
                            <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة مهمة
                        </a>
                        <a class="dropdown-item f-14 text-dark openRightModal" href="clients/create">
                            <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة عميل
                        </a>
                        <a class="dropdown-item f-14 text-dark openRightModal" href="employees/create">
                            <i class="bi bi-plus-square f-w-500 mr-2 f-11"></i> إضافة موظف
                        </a>
                    </div>
                </div>
            </li>

            <!-- NOTIFICATIONS -->
            <li title="الإشعارات">
                <div class="notification_box dropdown">
                    <a class="d-block dropdown-toggle header-icon-box show-user-notifications" type="link" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="bi bi-bell f-16 text-dark-grey"></i>
                        <span class="badge badge-primary unread-notifications-count position-absolute">0</span>
                    </a>
                    <!-- NOTIFICATION DROPDOWN -->
                    <div class="dropdown-menu dropdown-menu-right notification-dropdown border-0 shadow-lg py-0 bg-additional-grey" tabindex="0">
                        <div class="d-flex px-3 justify-content-between align-items-center border-bottom-grey py-1 bg-white">
                            <p class="f-14 mb-0 text-dark f-w-500">الإشعارات</p>
                            <div class="f-12">
                                <a href="javascript:;" class="text-dark-grey mark-notification-read">تعيين كمقروء</a> |
                                <a href="notifications" class="text-dark-grey">عرض الكل</a>
                            </div>
                        </div>
                        <div id="notification-list" class="p-2">
                            <!-- Notifications will load here -->
                        </div>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</header>

<script>
// Timer Functionality
function updateTimer() {
    const timerElement = document.getElementById('active-timer');
    if (!timerElement) return;

    let time = timerElement.textContent.split(':');
    let hours = parseInt(time[0]);
    let minutes = parseInt(time[1]);
    let seconds = parseInt(time[2]);

    seconds++;

    if (seconds >= 60) {
        seconds = 0;
        minutes++;
    }

    if (minutes >= 60) {
        minutes = 0;
        hours++;
    }

    timerElement.textContent =
        `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

// Start timer only if element exists
if (document.getElementById('active-timer')) {
    setInterval(updateTimer, 1000);
}
</script>
