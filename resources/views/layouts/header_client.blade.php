<nav class="header-navbar navbar-expand-lg navbar navbar-with-menu floating-nav navbar-light navbar-shadow">
    <div class="navbar-wrapper">
        <div class="navbar-container content" style="background-color: {{ $backgroundColorr ?? '#ffffff' }};">

            <div class="navbar-collapse" id="navbar-mobile">
                <div class="mr-auto float-left bookmark-wrapper d-flex align-items-center">
                    <ul class="nav navbar-nav">
                        <li class="nav-item mobile-menu d-xl-none mr-auto"><a class="nav-link nav-menu-main menu-toggle hidden-xs" href="#"><i class="ficon feather icon-menu"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav bookmark-icons">
                        <!-- li.nav-item.mobile-menu.d-xl-none.mr-auto-->
                        <!--   a.nav-link.nav-menu-main.menu-toggle.hidden-xs(href='#')-->
                        <!--     i.ficon.feather.icon-menu-->
                        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-todo.html" data-toggle="tooltip" data-placement="top" title="Todo"><i class="ficon feather icon-check-square"></i></a></li>
                        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-chat.html" data-toggle="tooltip" data-placement="top" title="Chat"><i class="ficon feather icon-message-square"></i></a></li>
                        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-email.html" data-toggle="tooltip" data-placement="top" title="Email"><i class="ficon feather icon-mail"></i></a></li>
                        <li class="nav-item d-none d-lg-block"><a class="nav-link" href="app-calender.html" data-toggle="tooltip" data-placement="top" title="Calendar"><i class="ficon feather icon-calendar"></i></a></li>
                    </ul>
                    <ul class="nav navbar-nav">
                        <li class="nav-item d-none d-lg-block"><a class="nav-link bookmark-star"><i class="ficon feather icon-star warning"></i></a>
                            <div class="bookmark-input search-input">
                                <div class="bookmark-input-icon"><i class="feather icon-search primary"></i></div>
                                <input class="form-control input" type="text" placeholder="Explore Vuexy..." tabindex="0" data-search="template-list">
                                <ul class="search-list search-list-bookmark"></ul>
                            </div>
                            <!-- select.bookmark-select-->
                            <!--   option Chat-->
                            <!--   option email-->
                            <!--   option todo-->
                            <!--   option Calendar-->
                        </li>
                    </ul>
                </div>
                <ul class="nav navbar-nav float-right">
                    <li class="dropdown dropdown-language nav-item">
                        <a class="dropdown-toggle nav-link" id="dropdown-flag" href="#" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="ficon feather icon-globe"></i><span class="selected-language"></span></a>
                        <div class="dropdown-menu" aria-labelledby="dropdown-flag">
                            @foreach(LaravelLocalization::getSupportedLocales() as $localeCode => $properties)
                            <a class="dropdown-item" hreflang="{{ $localeCode }}" href="{{ LaravelLocalization::getLocalizedURL($localeCode, null, [], true) }}" data-language="{{ $localeCode }}">
                                @if ($localeCode == 'ar')
                                    <i class="flag-icon flag-icon-sa"></i> {{ $properties['native'] }} <!-- علم السعودية -->
                                @elseif ($localeCode == 'ur')
                                    <i class="flag-icon flag-icon-pk"></i> {{ $properties['native'] }} <!-- علم باكستان -->
                                @elseif ($localeCode == 'hi')
                                    <i class="flag-icon flag-icon-in"></i> {{ $properties['native'] }} <!-- علم الهند -->
                                @elseif ($localeCode == 'bn')
                                    <i class="flag-icon flag-icon-bd"></i> {{ $properties['native'] }} <!-- علم بنغلاديش -->
                                @else
                                    <i class="flag-icon flag-icon-us"></i> {{ $properties['native'] }} <!-- علم الولايات المتحدة -->
                                @endif
                            </a>
                        @endforeach

                        </div>
                    </li>


                    <li class="nav-item d-none d-lg-block"><a class="nav-link nav-link-expand"><i class="ficon feather icon-maximize"></i></a></li>
                    <li class="nav-item nav-search"><a class="nav-link nav-link-search"><i class="ficon feather icon-search"></i></a>
                        <div class="search-input">
                            <div class="search-input-icon"><i class="feather icon-search primary"></i></div>
                            <input class="input" type="text" placeholder="Explore Vuexy..." tabindex="-1" data-search="template-list">
                            <div class="search-input-close"><i class="feather icon-x"></i></div>
                            <ul class="search-list search-list-main"></ul>
                        </div>
                    </li>
                    <li class="dropdown dropdown-notification nav-item"><a class="nav-link nav-link-label" href="#" data-toggle="dropdown"><i class="ficon feather icon-shopping-cart"></i><span class="badge badge-pill badge-primary badge-up cart-item-count">6</span></a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-cart dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <div class="dropdown-header m-0 p-2">
                                    <h3 class="white"><span class="cart-item-count">6</span><span class="mx-50">Items</span></h3><span class="notification-title">In Your Cart</span>
                                </div>
                            </li>
                            <li class="scrollable-container media-list"><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img src="../../../app-assets/images/pages/eCommerce/4.png" width="75" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Apple - Apple Watch Series 1 42mm Space Gray Aluminum Case Black Sport Band - Space Gray Aluminum</span><span class="item-desc font-small-2 text-truncate d-block"> Durable, lightweight aluminum cases in silver, space gray,gold, and rose gold. Sport Band in a variety of colors. All the features of the original Apple Watch, plus a new dual-core processor for faster performance. All models run watchOS 3. Requires an iPhone 5 or later to run this device.</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $299</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img class="mt-1 pl-50" src="../../../app-assets/images/pages/eCommerce/dell-inspirion.jpg" width="100" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Apple - Macbook® (Latest Model) - 12" Display - Intel Core M5 - 8GB Memory - 512GB Flash Storage - Space Gray</span><span class="item-desc font-small-2 text-truncate d-block"> MacBook delivers a full-size experience in the lightest and most compact Mac notebook ever. With a full-size keyboard, force-sensing trackpad, 12-inch Retina display,1 sixth-generation Intel Core M processor, multifunctional USB-C port, and now up to 10 hours of battery life,2 MacBook features big thinking in an impossibly compact form.</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $1599.99</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img src="../../../app-assets/images/pages/eCommerce/7.png" width="88" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Sony - PlayStation 4 Pro Console</span><span class="item-desc font-small-2 text-truncate d-block"> PS4 Pro Dynamic 4K Gaming & 4K Entertainment* PS4 Pro gets you closer to your game. Heighten your experiences. Enrich your adventures. Let the super-charged PS4 Pro lead the way.** GREATNESS AWAITS</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $399.99</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img src="../../../app-assets/images/pages/eCommerce/10.png" width="75" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Beats by Dr. Dre - Geek Squad Certified Refurbished Beats Studio Wireless On-Ear Headphones - Red</span><span class="item-desc font-small-2 text-truncate d-block"> Rock out to your favorite songs with these Beats by Dr. Dre Beats Studio Wireless GS-MH8K2AM/A headphones that feature a Beats Acoustic Engine and DSP software for enhanced clarity. ANC (Adaptive Noise Cancellation) allows you to focus on your tunes.</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $379.99</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img class="mt-1 pl-50" src="../../../app-assets/images/pages/eCommerce/sony-75class-tv.jpg" width="100" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Sony - 75" Class (74.5" diag) - LED - 2160p - Smart - 3D - 4K Ultra HD TV with High Dynamic Range - Black</span><span class="item-desc font-small-2 text-truncate d-block"> This Sony 4K HDR TV boasts 4K technology for vibrant hues. Its X940D series features a bold 75-inch screen and slim design. Wires remain hidden, and the unit is easily wall mounted. This television has a 4K Processor X1 and 4K X-Reality PRO for crisp video. This Sony 4K HDR TV is easy to control via voice commands.</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $4499.99</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a><a class="cart-item" href="app-ecommerce-details.html">
                                    <div class="media">
                                        <div class="media-left d-flex justify-content-center align-items-center"><img class="mt-1 pl-50" src="../../../app-assets/images/pages/eCommerce/canon-camera.jpg" width="70" alt="Cart Item"></div>
                                        <div class="media-body"><span class="item-title text-truncate text-bold-500 d-block mb-50">Nikon - D810 DSLR Camera with AF-S NIKKOR 24-120mm f/4G ED VR Zoom Lens - Black</span><span class="item-desc font-small-2 text-truncate d-block"> Shoot arresting photos and 1080p high-definition videos with this Nikon D810 DSLR camera, which features a 36.3-megapixel CMOS sensor and a powerful EXPEED 4 processor for clear, detailed images. The AF-S NIKKOR 24-120mm lens offers shooting versatility. Memory card sold separately.</span>
                                            <div class="d-flex justify-content-between align-items-center mt-1"><span class="align-middle d-block">1 x $4099.99</span><i class="remove-cart-item feather icon-x danger font-medium-1"></i></div>
                                        </div>
                                    </div>
                                </a></li>
                            <li class="dropdown-menu-footer"><a class="dropdown-item p-1 text-center text-primary" href="app-ecommerce-checkout.html"><i class="feather icon-shopping-cart align-middle"></i><span class="align-middle text-bold-600">Checkout</span></a></li>
                            <li class="empty-cart d-none p-2">Your Cart Is Empty.</li>
                        </ul>
                    </li>
                    <li class="dropdown dropdown-notification nav-item">
                        <a class="nav-link nav-link-label" href="#" data-toggle="dropdown">
                            <i class="ficon feather icon-bell"></i>
                            <span class="badge badge-pill badge-primary badge-up" id="notification-count">0</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-media dropdown-menu-right">
                            <li class="dropdown-menu-header">
                                <div class="dropdown-header m-0 p-2">
                                    <h3 class="white" id="notification-title">إشعارات جديدة</h3>
                                    <span class="notification-title">التنبيهات</span>
                                </div>
                            </li>
                            <li class="scrollable-container media-list" id="notification-list">
                                <p class="text-center p-2">لا يوجد إشعارات جديدة</p>
                            </li>
                            <li class="dropdown-menu-footer">
                                <a class="dropdown-item p-1 text-center" href="{{ route('notifications.index') }}">عرض كل الإشعارات</a>
                            </li>
                        </ul>
                    </li>
                    
                    <script>
                       $(document).ready(function () {
    function fetchNotifications() {
        $.ajax({
            url: "{{ route('notifications.unread') }}", // جلب الإشعارات
            method: "GET",
            success: function (response) {
                let notifications = response.notifications;
                let count = notifications.length;
                $('#notification-count').text(count);
                $('#notification-title').text(count + " إشعارات جديدة");

                let notificationList = $('#notification-list');
                notificationList.empty();

                if (count > 0) {
                    notifications.forEach(notification => {
                        let listItem = `
                            <a class="d-flex justify-content-between notification-item" 
                                href="javascript:void(0)" 
                                data-id="${notification.id}">
                                <div class="media d-flex align-items-start">
                                    <div class="media-left">
                                        <i class="feather icon-bell font-medium-5 primary"></i>
                                    </div>
                                    <div class="media-body">
                                        <h6 class="primary media-heading">${notification.title}</h6>
                                        <small class="notification-text">${notification.description}</small>
                                    </div>
                                    <small>
                                        <time class="media-meta">${new Date(notification.created_at).toLocaleString()}</time>
                                    </small>
                                </div>
                            </a>
                        `;
                        notificationList.append(listItem);
                    });
                } else {
                    notificationList.append('<p class="text-center p-2">لا يوجد إشعارات جديدة</p>');
                }
            }
        });
    }

    fetchNotifications();

    // تحديث الإشعار عند النقر عليه
    $(document).on('click', '.notification-item', function () {
        let notificationId = $(this).data('id');
        
        $.ajax({
            url: "{{ route('notifications.markAsRead') }}", // استدعاء API التحديث
            method: "POST",
            data: {
                _token: "{{ csrf_token() }}",
                id: notificationId
            },
            success: function () {
                fetchNotifications(); // تحديث قائمة الإشعارات بعد القراءة
            }
        });
    });
});

                    </script>
                    
                    <li class="dropdown dropdown-user nav-item">
                        <a class="dropdown-toggle nav-link dropdown-user-link" href="#" data-toggle="dropdown" aria-expanded="false">
                            <div class="user-nav d-sm-flex d-none">
                                <span class="user-name text-bold-600">{{ auth()->user()->name ?? "" }}</span>
                                <span class="user-status">
                                    متصل 
                                    @if(auth()->user()->branch_id)
                                        - {{ auth()->user()->currentBranch()->name ?? 'بدون فرع' }}
                                    @endif
                                </span>
                            </div>
                            <span>
                                @php
                                    $firstLetter = mb_substr(auth()->user()->name, 0, 1, "UTF-8");
                                @endphp
                                <div class="profile-picture-header">{{ $firstLetter }}</div>
                            </span>
                            <i class="feather icon-chevron-down"></i> <!-- 🔽 رمز الدروب داون -->
                        </a>
                    
                        <div class="dropdown-menu dropdown-menu-right">
                    
                            <div class="dropdown-divider"></div>
                    
                           
                                <span class="dropdown-item font-weight-bold">🔹 الفروع:</span>
                             
                                    <a class="dropdown-item branch-item" 
                                       href="{{ route('clients.profile') }}">
                                        <i class="feather icon-map-pin"></i> تعديل الملف الشخصي
                                      
                                    </a>
                            
                    
                            <div class="dropdown-divider"></div>
                    
                            <!-- زر تسجيل الخروج -->
                            <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                                @csrf
                                <button type="submit" class="dropdown-item"><i class="feather icon-power"></i> تسجيل خروج</button>
                            </form>
                        </div>
                    </li>
                    
                    
                </ul>
            </div>
        </div>
    </div>
</nav>
<ul class="main-search-list-defaultlist d-none">
    <li class="d-flex align-items-center"><a class="pb-25" href="#">
            <h6 class="text-primary mb-0">Files</h6>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="../../../app-assets/images/icons/xls.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Two new item submitted</p><small class="text-muted">Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;17kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="../../../app-assets/images/icons/jpg.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">52 JPG file Generated</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;11kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="../../../app-assets/images/icons/pdf.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">25 PDF File Uploaded</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;150kb</small>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100" href="#">
            <div class="d-flex">
                <div class="mr-50"><img src="../../../app-assets/images/icons/doc.png" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna_Strong.doc</p><small class="text-muted">Web Designer</small>
                </div>
            </div><small class="search-data-size mr-50 text-muted">&apos;256kb</small>
        </a></li>
    <li class="d-flex align-items-center"><a class="pb-25" href="#">
            <h6 class="text-primary mb-0">Members</h6>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-8.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">{{ auth()->user()->name }}</p><small class="text-muted">UI designer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-1.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Michal Clark</p><small class="text-muted">FontEnd Developer</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-14.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Milena Gibson</p><small class="text-muted">Digital Marketing Manager</small>
                </div>
            </div>
        </a></li>
    <li class="auto-suggestion d-flex align-items-center cursor-pointer"><a class="d-flex align-items-center justify-content-between py-50 w-100" href="#">
            <div class="d-flex align-items-center">
                <div class="avatar mr-50"><img src="../../../app-assets/images/portrait/small/avatar-s-6.jpg" alt="png" height="32"></div>
                <div class="search-data">
                    <p class="search-data-title mb-0">Anna Strong</p><small class="text-muted">Web Designer</small>
                </div>
            </div>
        </a></li>
</ul>
<ul class="main-search-list-defaultlist-other-list d-none">
    <li class="auto-suggestion d-flex align-items-center justify-content-between cursor-pointer"><a class="d-flex align-items-center justify-content-between w-100 py-50">
            <div class="d-flex justify-content-start"><span class="mr-75 feather icon-alert-circle"></span><span>No results found.</span></div>
        </a></li>
</ul>
