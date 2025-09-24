<aside class="sidebar-light">
    <!-- MOBILE CLOSE BUTTON -->
    <div class="mobile-close-sidebar-panel w-100 h-100" onclick="closeMobileMenu()" id="mobile_close_panel"></div>

    <!-- SIDEBAR TOGGLE -->
    <a id="sidebarToggle" class="text-lightest sidebarToggleBtn" href="javascript:;" style="z-index: 100;">
        <span class="material-symbols-rounded">keyboard_arrow_left</span>
    </a>

    <!-- MAIN SIDEBAR CONTENT -->
    <div class="main-sidebar d-flex flex-column align-items-center justify-content-between" id="mobile_menu_collapse">
        <!-- LOGO -->
        <div class="mt-4 mb-3 d-flex justify-content-center">
            <img src="https://altab.flowdo.net/user-uploads/app-logo/713a8c95c7cd38a61fc5092dce631cac.png"
                 height="40" width="40" style="width:40px;height:40px;border-radius:12px"
                 alt="Company Logo">
        </div>

        <!-- SIDEBAR MENU -->
        <div class="sidebar-menu h-100 w-100" id="sideMenuScroll">
            <ul class="list-unstyled">
                <!-- DASHBOARD -->
                <li class="accordionItem closeIt">
                    <div class="d-flex flex-column justify-content-center align-items-center my-2">
                        <a class="nav-item text-lightest f-15 sidebar-text-color" href="dashboard">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21.5 10.9V4.1C21.5 2.6 20.86 2 19.27 2H15.23C13.64 2 13 2.6 13 4.1V10.9C13 12.4 13.64 13 15.23 13H19.27C20.86 13 21.5 12.4 21.5 10.9Z"/>
                                <path d="M11 13.1V19.9C11 21.4 10.36 22 8.77 22H4.73C3.14 22 2.5 21.4 2.5 19.9V13.1C2.5 11.6 3.14 11 4.73 11H8.77C10.36 11 11 11.6 11 13.1Z"/>
                                <path opacity="0.4" d="M21.5 19.9V17.1C21.5 15.6 20.86 15 19.27 15H15.23C13.64 15 13 15.6 13 17.1V19.9C13 21.4 13.64 22 15.23 22H19.27C20.86 22 21.5 21.4 21.5 19.9Z"/>
                                <path opacity="0.4" d="M11 6.9V4.1C11 2.6 10.36 2 8.77 2H4.73C3.14 2 2.5 2.6 2.5 4.1V6.9C2.5 8.4 3.14 9 4.73 9H8.77C10.36 9 11 8.4 11 6.9Z"/>
                            </svg>
                        </a>
                        <span class="f-10">اللوحة</span>
                    </div>
                </li>

                <!-- CLIENTS -->
                <li class="accordionItem closeIt">
                    <div class="d-flex flex-column justify-content-center align-items-center my-2">
                        <a class="nav-item text-lightest f-15 sidebar-text-color" href="clients">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path opacity="0.4" d="M11.5 7.41V22H19.92C21.08 22 22.03 21.07 22.03 19.93V5.09C22.03 2.47 20.07 1.28 17.68 2.45L13.25 4.64C12.29 5.11 11.5 6.36 11.5 7.41Z"/>
                                <path d="M2 15.05V19.5C2 20.88 3.12 22 4.5 22H11.5V10.42L11.03 10.52L6.99 11.42L6.51 11.53L4.47 11.99C3.98 12.09 3.53 12.26 3.14 12.51C3.14 12.52 3.13 12.52 3.13 12.52C3.03 12.59 2.93 12.67 2.84 12.76C2.38 13.22 2.08 13.89 2.01 14.87C2.01 14.93 2 14.99 2 15.05Z"/>
                            </svg>
                        </a>
                        <span class="f-10">العملاء</span>
                    </div>
                </li>

                <!-- PROJECTS -->
                <li class="accordionItem closeIt">
                    <div class="d-flex flex-column justify-content-center align-items-center my-2">
                        <a class="nav-item text-lightest f-15 sidebar-text-color" href="projects">
                            <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M13.3111 14.75H5.03356C3.36523 14.75 2.30189 12.9625 3.10856 11.4958L5.24439 7.60911L7.24273 3.96995C8.07689 2.45745 10.2586 2.45745 11.0927 3.96995L13.1002 7.60911L14.0627 9.35995L15.2361 11.4958C16.0427 12.9625 14.9794 14.75 13.3111 14.75Z"/>
                                <path fill-opacity="0.3" d="M21.1667 15.2083C21.1667 18.4992 18.4992 21.1667 15.2083 21.1667C11.9175 21.1667 9.25 18.4992 9.25 15.2083C9.25 15.0525 9.25917 14.9058 9.26833 14.75H13.3108C14.9792 14.75 16.0425 12.9625 15.2358 11.4958L14.0625 9.36C14.4292 9.28666 14.8142 9.25 15.2083 9.25C18.4992 9.25 21.1667 11.9175 21.1667 15.2083Z"/>
                            </svg>
                        </a>
                        <span class="f-10">المشاريع</span>
                    </div>
                </li>
            </ul>
        </div>

        <!-- USER PROFILE SECTION -->
        <div class="sidebar-brand-box dropdown d-flex flex-column align-items-center cursor-pointer my-4">
            <div class="dropdown-toggle sidebar-brand d-flex align-items-center justify-content-between w-100"
                 type="link" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <div class="sidebar-brand-logo" style="border: 2px solid transparent;outline: 2px solid var(--header_color);">
                    <img src="https://altab.flowdo.net/user-uploads/avatar/ar/13.png"
                         height="40" width="40" style="width:34px;height:35px"
                         alt="User Avatar">
                </div>
            </div>

            <!-- USER DROPDOWN MENU -->
            <div class="dropdown-menu dropdown-menu-right sidebar-brand-dropdown ml-3"
                 aria-labelledby="dropdownMenuLink" tabindex="0">
                <div class="d-flex justify-content-between align-items-center profile-box">
                    <div class="profileInfo d-flex align-items-center mr-1 flex-wrap">
                        <div class="profileImg mr-2">
                            <img class="h-100" src="https://altab.flowdo.net/user-uploads/avatar/ar/13.png"
                                 alt="User Name">
                        </div>
                        <div class="ProfileData">
                            <h3 class="f-15 f-w-500 text-dark">محمد فالح العتيبي</h3>
                            <p class="mb-0 f-12 text-dark-grey">مدير النظام</p>
                        </div>
                    </div>
                    <a href="profile" data-toggle="tooltip" data-original-title="الملف الشخصي">
                        <i class="side-icon bi bi-pencil-square"></i>
                    </a>
                </div>

                <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="invite">
                    <span>إرسال دعوة</span>
                    <i class="side-icon bi bi-person-plus"></i>
                </a>

                <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="javascript:;">
                    <label for="dark-theme-toggle">الوضع الداكن</label>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="dark-theme-toggle">
                        <label class="custom-control-label f-14" for="dark-theme-toggle"></label>
                    </div>
                </a>

                <a class="dropdown-item d-flex justify-content-between align-items-center f-15 text-dark" href="logout">
                    تسجيل خروج <i class="side-icon bi bi-power"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- EXPANDED SIDEBAR MENU (FOR DESKTOP) -->
    <div class="main-sidebar side-panel" style="z-index: 2;" id="mobile_menu_collapse2">
        <div class="sidebar-menu" id="sideMenuScroll">
            <div class="d-flex justify-content-between mb-3 mt-3 px-3 border-bottom">
                <span class="f-18 font-weight-bold">القائمة الرئيسية</span>
            </div>

            <ul class="list-unstyled">
                <li class="accordionItem closeIt">
                    <a class="nav-item text-lightest f-13 sidebar-text-color" href="projects" title="المشاريع">
                        <i class="bi bi-folder2-open mr-2"></i>
                        <span class="pl-3">المشاريع</span>
                    </a>
                </li>
                <li class="accordionItem closeIt">
                    <a class="nav-item text-lightest f-13 sidebar-text-color" href="tasks" title="المهام">
                        <i class="bi bi-list-task mr-2"></i>
                        <span class="pl-3">المهام</span>
                    </a>
                </li>
                <li class="accordionItem closeIt">
                    <a class="nav-item text-lightest f-13 sidebar-text-color" href="timelogs" title="السجلات الزمنية">
                        <i class="bi bi-clock-history mr-2"></i>
                        <span class="pl-3">السجلات الزمنية</span>
                    </a>
                </li>
                <li class="accordionItem closeIt">
                    <a class="nav-item text-lightest f-13 sidebar-text-color" href="reports" title="التقارير">
                        <i class="bi bi-bar-chart-line mr-2"></i>
                        <span class="pl-3">التقارير</span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>

<script>
// Mobile Menu Functions
function openMobileMenu() {
    document.getElementById('mobile_menu_collapse').classList.add('show');
    document.getElementById('mobile_close_panel').classList.add('show');
}

function closeMobileMenu() {
    document.getElementById('mobile_menu_collapse').classList.remove('show');
    document.getElementById('mobile_close_panel').classList.remove('show');
}

// Dark Mode Toggle
document.getElementById('dark-theme-toggle')?.addEventListener('change', function() {
    document.body.classList.toggle('dark-mode', this.checked);
    localStorage.setItem('darkMode', this.checked);
});

// Initialize dark mode from localStorage
if (localStorage.getItem('darkMode') === 'true') {
    document.getElementById('dark-theme-toggle').checked = true;
    document.body.classList.add('dark-mode');
}
</script>
