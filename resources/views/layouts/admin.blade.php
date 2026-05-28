<!doctype html>

<html
    lang="en"
    class="light-style layout-navbar-fixed layout-menu-fixed layout-compact"
    dir="ltr"
    data-theme="theme-default"
    data-assets-path="../../assets/"
    data-template="vertical-menu-template"
    data-style="light">
<head>
    <meta charset="utf-8" />
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <title>Dashboard - Analytics | Materialize - Material Design HTML Admin Template</title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&ampdisplay=swap"
        rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="{{asset('main/assets/vendor/fonts/remixicon/remixicon.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/fonts/flag-icons.css')}}" />

    <!-- Menu waves for no-customizer fix -->
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/node-waves/node-waves.css')}}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{asset('main/assets/vendor/css/rtl/core.css')}}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/css/rtl/theme-default.css')}}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{asset('main/assets/css/demo.css')}}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/typeahead-js/typeahead.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/apex-charts/apex-charts.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/swiper/swiper.css')}}" />

    <!-- Page CSS -->
    <link rel="stylesheet" href="{{asset('main/assets/vendor/css/pages/cards-statistics.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/css/pages/cards-analytics.css')}}" />

    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/quill/katex.css')}}" />
    <link rel="stylesheet" href="{{asset('main/assets/vendor/libs/quill/editor.css')}}" />

    <!-- Helpers -->
    <script src="{{asset('assets/assets/vendor/js/helpers.js')}}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->
    <!--? Template customizer: To hide customizer set displayCustomizer value false in config.js.  -->
    <script src="{{asset('main/assets/vendor/js/template-customizer.js')}}"></script>
    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{asset('main/assets/js/config.js')}}"></script>
</head>

<body>
<!-- Layout wrapper -->
<div class="layout-wrapper layout-content-navbar">
    <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
            <div class="app-brand demo">
                <a href="index.html" class="app-brand-link">
              <span class="app-brand-logo demo">
                <span style="color: var(--bs-primary)">
                  <svg width="268" height="150" viewBox="0 0 38 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path
                        d="M30.0944 2.22569C29.0511 0.444187 26.7508 -0.172113 24.9566 0.849138C23.1623 1.87039 22.5536 4.14247 23.5969 5.92397L30.5368 17.7743C31.5801 19.5558 33.8804 20.1721 35.6746 19.1509C37.4689 18.1296 38.0776 15.8575 37.0343 14.076L30.0944 2.22569Z"
                        fill="currentColor" />
                    <path
                        d="M30.171 2.22569C29.1277 0.444187 26.8274 -0.172113 25.0332 0.849138C23.2389 1.87039 22.6302 4.14247 23.6735 5.92397L30.6134 17.7743C31.6567 19.5558 33.957 20.1721 35.7512 19.1509C37.5455 18.1296 38.1542 15.8575 37.1109 14.076L30.171 2.22569Z"
                        fill="url(#paint0_linear_2989_100980)"
                        fill-opacity="0.4" />
                    <path
                        d="M22.9676 2.22569C24.0109 0.444187 26.3112 -0.172113 28.1054 0.849138C29.8996 1.87039 30.5084 4.14247 29.4651 5.92397L22.5251 17.7743C21.4818 19.5558 19.1816 20.1721 17.3873 19.1509C15.5931 18.1296 14.9843 15.8575 16.0276 14.076L22.9676 2.22569Z"
                        fill="currentColor" />
                    <path
                        d="M14.9558 2.22569C13.9125 0.444187 11.6122 -0.172113 9.818 0.849138C8.02377 1.87039 7.41502 4.14247 8.45833 5.92397L15.3983 17.7743C16.4416 19.5558 18.7418 20.1721 20.5361 19.1509C22.3303 18.1296 22.9391 15.8575 21.8958 14.076L14.9558 2.22569Z"
                        fill="currentColor" />
                    <path
                        d="M14.9558 2.22569C13.9125 0.444187 11.6122 -0.172113 9.818 0.849138C8.02377 1.87039 7.41502 4.14247 8.45833 5.92397L15.3983 17.7743C16.4416 19.5558 18.7418 20.1721 20.5361 19.1509C22.3303 18.1296 22.9391 15.8575 21.8958 14.076L14.9558 2.22569Z"
                        fill="url(#paint1_linear_2989_100980)"
                        fill-opacity="0.4" />
                    <path
                        d="M7.82901 2.22569C8.87231 0.444187 11.1726 -0.172113 12.9668 0.849138C14.7611 1.87039 15.3698 4.14247 14.3265 5.92397L7.38656 17.7743C6.34325 19.5558 4.04298 20.1721 2.24875 19.1509C0.454514 18.1296 -0.154233 15.8575 0.88907 14.076L7.82901 2.22569Z"
                        fill="currentColor" />
                    <defs>
                      <linearGradient
                          id="paint0_linear_2989_100980"
                          x1="5.36642"
                          y1="0.849138"
                          x2="10.532"
                          y2="24.104"
                          gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-opacity="1" />
                        <stop offset="1" stop-opacity="0" />
                      </linearGradient>
                      <linearGradient
                          id="paint1_linear_2989_100980"
                          x1="5.19475"
                          y1="0.849139"
                          x2="10.3357"
                          y2="24.1155"
                          gradientUnits="userSpaceOnUse">
                        <stop offset="0" stop-opacity="1" />
                        <stop offset="1" stop-opacity="0" />
                      </linearGradient>
                    </defs>
                  </svg>
                </span>
              </span>
                    <span class="app-brand-text demo menu-text fw-semibold ms-2">Materialize</span>
                </a>

                <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M8.47365 11.7183C8.11707 12.0749 8.11707 12.6531 8.47365 13.0097L12.071 16.607C12.4615 16.9975 12.4615 17.6305 12.071 18.021C11.6805 18.4115 11.0475 18.4115 10.657 18.021L5.83009 13.1941C5.37164 12.7356 5.37164 11.9924 5.83009 11.5339L10.657 6.707C11.0475 6.31653 11.6805 6.31653 12.071 6.707C12.4615 7.09747 12.4615 7.73053 12.071 8.121L8.47365 11.7183Z"
                            fill-opacity="0.9" />
                        <path
                            d="M14.3584 11.8336C14.0654 12.1266 14.0654 12.6014 14.3584 12.8944L18.071 16.607C18.4615 16.9975 18.4615 17.6305 18.071 18.021C17.6805 18.4115 17.0475 18.4115 16.657 18.021L11.6819 13.0459C11.3053 12.6693 11.3053 12.0587 11.6819 11.6821L16.657 6.707C17.0475 6.31653 17.6805 6.31653 18.071 6.707C18.4615 7.09747 18.4615 7.73053 18.071 8.121L14.3584 11.8336Z"
                            fill-opacity="0.4" />
                    </svg>
                </a>
            </div>

            <div class="menu-inner-shadow"></div>

            <ul class="menu-inner py-1">
                <!-- Apps & Pages -->
                <li class="menu-header mt-5">
                    <span class="menu-header-text" data-i18n="Apps & Pages">Apps &amp; Pages</span>
                </li>
                <li class="menu-item">
                    <a href="{{route('admin.news.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-news-line"></i>
                        <div data-i18n="Calendar">Новости</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="{{route('admin.users.tracking')}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-group-line"></i>
                        <div data-i18n="Email">Пользователи</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{route('admin.support.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-wechat-line"></i>
                        <div data-i18n="Chat">Обращения</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="{{route('admin.subscriptions.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons ri-mastercard-line"></i>
                        <div data-i18n="Chat">Подписки</div>
                    </a>
                </li>
            </ul>
        </aside>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
            <!-- Navbar -->

            <nav
                class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme"
                id="layout-navbar">
                <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0 d-xl-none">
                    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
                        <i class="ri-menu-fill ri-22px"></i>
                    </a>
                </div>

                <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                    <!-- Search -->
                    <div class="navbar-nav align-items-center">

                    </div>
                    <!-- /Search -->

                    <ul class="navbar-nav flex-row align-items-center ms-auto">


                        <!-- User -->
                        <li class="nav-item navbar-dropdown dropdown-user dropdown">
                            <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                                <div class="avatar avatar-online">
                                    <img src="{{asset('main/assets/img/avatars/1.png')}}" alt class="rounded-circle" />
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <div class="dropdown-divider"></div>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="pages-profile-user.html">
                                        <i class="ri-user-3-line ri-22px me-3"></i><span class="align-middle">My Profile</span>
                                    </a>
                                </li>

                                <li>
                                    <div class="d-grid px-4 pt-2 pb-1">
                                        <form action="{{route('logout')}}" method="post">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger d-flex border-none outline-none">
                                                <small class="align-middle">Выйти</small>
                                                <i class="ri-logout-box-r-line ms-2 ri-16px"></i>
                                            </button>
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </li>
                        <!--/ User -->
                    </ul>
                </div>

                <!-- Search Small Screens -->
                <div class="navbar-search-wrapper search-input-wrapper d-none">
                    <input
                        type="text"
                        class="form-control search-input container-xxl border-0"
                        placeholder="Search..."
                        aria-label="Search..." />
                    <i class="ri-close-fill search-toggler cursor-pointer"></i>
                </div>
            </nav>

            <!-- / Navbar -->

            @yield('content')
        </div>
        <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>

    <!-- Drag Target Area To SlideIn Menu On Small Screens -->
    <div class="drag-target"></div>
</div>
<!-- / Layout wrapper -->

<!-- Core JS -->
<!-- build:js assets/vendor/js/core.js -->
<script src="{{asset('main/assets/vendor/libs/jquery/jquery.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/popper/popper.js')}}"></script>
<script src="{{asset('main/assets/vendor/js/bootstrap.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/node-waves/node-waves.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/hammer/hammer.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/i18n/i18n.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/typeahead-js/typeahead.js')}}"></script>
<script src="{{asset('main/assets/vendor/js/menu.js')}}"></script>


<!-- Vendors JS -->
<script src="{{asset('main/assets/vendor/libs/apex-charts/apexcharts.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/swiper/swiper.js')}}"></script>

<!-- Main JS -->
<script src="{{asset('main/assets/js/main.js')}}"></script>

<!-- Page JS -->
<script src="{{asset('main/assets/js/dashboards-analytics.js')}}"></script>


<script src="{{asset('main/assets/vendor/libs/quill/katex.js')}}"></script>
<script src="{{asset('main/assets/vendor/libs/quill/quill.js')}}"></script>
<script src="{{asset('main/assets/js/forms-editors.js')}}"></script>

<script>
    setInterval(function() {
        fetch('/online-users')
            .then(response => response.json())
            .then(data => {
                // Обновляем счетчик онлайн
                $('.online-count').text(data.count);

                // Обновляем статусы в таблице
                data.users.forEach(user => {
                    $(`#user-${user.id}-status`).html(`
                    <span class="badge bg-success me-2">
                        <i class="ri-circle-fill me-1" style="font-size: 8px;"></i> Онлайн
                    </span>
                    <div class="small text-muted">${user.last_activity}</div>
                `);
                });
            });
    }, 30000); // Обновляем каждые 30 секунд
</script>

@stack('scripts')
</body>
</html>
