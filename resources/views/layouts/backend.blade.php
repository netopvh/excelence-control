<!doctype html>
<html lang="{{ config('app.locale') }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">

    <title>Sistema de Controle</title>

    <meta name="description" content="Sistema para controle de produção">
    <meta name="author" content="Rubens Coelho">
    <meta name="robots" content="index, follow">

    <!-- Open Graph Meta -->
    <meta property="og:title" content="Sistema para controle de produção">
    <meta property="og:site_name" content="Excelence brindes">
    <meta property="og:description" content="Sistema para controle de produção">
    <meta property="og:type" content="website">
    <meta property="og:url" content="">
    <meta property="og:image" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="api-token" content="{{ Auth::user()->api_token }}">
    <meta name="user" content="{{ Auth::user()->id }}">
    <meta name="base-url" content="{{ url('/') }}">
    @yield('meta')
    <!-- Icons -->
    <link rel="shortcut icon" href="{{ asset('media/favicons/favicon.png') }}">
    <link rel="icon" sizes="192x192" type="image/png" href="{{ asset('media/favicons/favicon-192x192.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('media/favicons/apple-touch-icon-180x180.png') }}">

    <!-- Modules -->
    @yield('css')
    @vite('resources/sass/main.scss')

    <!-- Alternatively, you can also include a specific color theme after the main stylesheet to alter the default color theme of the template -->
    {{-- @vite(['resources/sass/main.scss', 'resources/sass/codebase/themes/corporate.scss', 'resources/js/codebase/app.js']) --}}

</head>

<body>
    <!-- Page Container -->
    <!--
    Available classes for #page-container:

    SIDEBAR & SIDE OVERLAY

      'sidebar-r'                                 Right Sidebar and left Side Overlay (default is left Sidebar and right Side Overlay)
      'sidebar-mini'                              Mini hoverable Sidebar (screen width > 991px)
      'sidebar-o'                                 Visible Sidebar by default (screen width > 991px)
      'sidebar-o-xs'                              Visible Sidebar by default (screen width < 992px)
      'sidebar-dark'                              Dark themed sidebar

      'side-overlay-hover'                        Hoverable Side Overlay (screen width > 991px)
      'side-overlay-o'                            Visible Side Overlay by default

      'enable-page-overlay'                       Enables a visible clickable Page Overlay (closes Side Overlay on click) when Side Overlay opens

      'side-scroll'                               Enables custom scrolling on Sidebar and Side Overlay instead of native scrolling (screen width > 991px)

    HEADER

      ''                                          Static Header if no class is added
      'page-header-fixed'                         Fixed Header

    HEADER STYLE

      ''                                          Classic Header style if no class is added
      'page-header-modern'                        Modern Header style
      'page-header-dark'                          Dark themed Header (works only with classic Header style)
      'page-header-glass'                         Light themed Header with transparency by default
                                                  (absolute position, perfect for light images underneath - solid light background on scroll if the Header is also set as fixed)
      'page-header-glass page-header-dark'        Dark themed Header with transparency by default
                                                  (absolute position, perfect for dark images underneath - solid dark background on scroll if the Header is also set as fixed)

    MAIN CONTENT LAYOUT

      ''                                          Full width Main Content if no class is added
      'main-content-boxed'                        Full width Main Content with a specific maximum width (screen width > 1200px)
      'main-content-narrow'                       Full width Main Content with a percentage width (screen width > 1200px)

    DARK MODE

      'sidebar-dark page-header-dark dark-mode'   Enable dark mode (light sidebar/header is not supported with dark mode)
  -->
    <div id="page-container" class="sidebar-o enable-page-overlay side-scroll main-content-narrow">
        <!-- Side Overlay-->
        <aside id="side-overlay">
            <!-- Side Header -->
            <div class="content-header">
                <!-- User Avatar -->
                <a class="img-link me-2" href="javascript:void(0)">
                    <img class="img-avatar img-avatar32" src="{{ asset('media/avatars/avatar15.jpg') }}" alt="">
                </a>
                <!-- END User Avatar -->

                <!-- User Info -->
                <a class="link-fx text-body-color-dark fw-semibold fs-sm" href="javascript:void(0)">
                    {{ Auth::user()->name }}
                </a>
                <!-- END User Info -->

                <!-- Close Side Overlay -->
                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                <button type="button" class="btn btn-sm btn-alt-danger ms-auto" data-toggle="layout"
                    data-action="side_overlay_close">
                    <i class="fa fa-fw fa-times"></i>
                </button>
                <!-- END Close Side Overlay -->
            </div>
            <!-- END Side Header -->

            <!-- Side Content -->
            <div class="content-side">
                <p>
                    Content..
                </p>
            </div>
            <!-- END Side Content -->
        </aside>

        <nav id="sidebar">
            <!-- Sidebar Content -->
            <div class="sidebar-content">
                <!-- Side Header -->
                <div class="content-header justify-content-lg-center">
                    <!-- Logo -->
                    <div class="p-4">
                        <a href="{{ route('dashboard.index') }}">
                            <img src="{{ asset('media/various/logon.png') }}" alt="" class="img-fluid w-100">
                        </a>
                    </div>
                    <!-- END Logo -->

                    <!-- Options -->
                    <div>
                        <!-- Close Sidebar, Visible only on mobile screens -->
                        <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                        <button type="button" class="btn btn-sm btn-alt-danger d-lg-none" data-toggle="layout"
                            data-action="sidebar_close">
                            <i class="fa fa-fw fa-times"></i>
                        </button>
                        <!-- END Close Sidebar -->
                    </div>
                    <!-- END Options -->
                </div>
                <!-- END Side Header -->

                <!-- Sidebar Scrolling -->
                <div class="js-sidebar-scroll">
                    <!-- Side User -->
                    <div class="content-side content-side-user px-0 py-0">
                        <!-- Visible only in mini mode -->
                        <div class="smini-visible-block animated fadeIn px-3">
                            <img class="img-avatar img-avatar32" src="{{ asset('media/avatars/avatar15.jpg') }}"
                                alt="">
                        </div>
                        <!-- END Visible only in mini mode -->

                        <!-- Visible only in normal mode -->
                        <div class="smini-hidden text-center mx-auto">
                            <a class="img-link" href="javascript:void(0)">
                                <img class="img-avatar" src="{{ asset('media/avatars/avatar15.jpg') }}" alt="">
                            </a>
                            <ul class="list-inline mt-3 mb-0">
                                <li class="list-inline-item">
                                    <a class="link-fx text-dual fs-sm fw-semibold text-uppercase"
                                        href="javascript:void(0)">{{ Auth::user()->name }}</a>
                                </li>
                                <li class="list-inline-item">
                                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                                    <a class="link-fx text-dual" data-toggle="layout" data-action="dark_mode_toggle"
                                        href="javascript:void(0)">
                                        <i class="fa fa-moon"></i>
                                    </a>
                                </li>
                                <li class="list-inline-item">
                                    <a class="link-fx text-dual" href="javascript:void(0)">
                                        <i class="fa fa-sign-out-alt"></i>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <!-- END Visible only in normal mode -->
                    </div>
                    <div class="content-side content-side-full">
                        <ul class="nav-main">
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard'], ' active') }}"
                                    href="{{ route('dashboard.index') }}">
                                    <i class="nav-main-link-icon fa-solid fa-chart-simple"></i>
                                    <span class="nav-main-link-name">Indicadores</span>
                                </a>
                            </li>
                            <li class="nav-main-heading">Geral</li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard.order.*'], ' active') }}"
                                    href="{{ route('dashboard.order.index') }}">
                                    <i class="nav-main-link-icon fa fa-envelope-open"></i>
                                    <span class="nav-main-link-name">Pedidos</span>
                                </a>
                            </li>
                            @role('admin')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ active(['dashboard.customer.*'], ' active') }}"
                                        href="{{ route('dashboard.customer.index') }}">
                                        <i class="nav-main-link-icon fa fa-users"></i>
                                        <span class="nav-main-link-name">Clientes</span>
                                    </a>
                                </li>
                            @endrole
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard.purchase.*'], ' active') }}"
                                    href="{{ route('dashboard.purchase.index') }}">
                                    <i class="nav-main-link-icon fa fa-shopping-cart"></i>
                                    <span class="nav-main-link-name">Compras</span>
                                </a>
                            </li>
                            @role('producao')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ active(['dashboard.production.*'], ' active') }}"
                                        href="{{ route('dashboard.production.index') }}">
                                        <i class="nav-main-link-icon fa fa-industry"></i>
                                        <span class="nav-main-link-name">Produção</span>
                                    </a>
                                </li>
                            @endrole
                            <li class="nav-main-heading">Financeiro</li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard.entry.*'], ' active') }}"
                                    href="{{ route('dashboard.entry.index') }}">
                                    <i class="nav-main-link-icon fa fa-cash-register"></i>
                                    <span class="nav-main-link-name">Lançamentos</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard.expense.*'], ' active') }}"
                                    href="{{ route('dashboard.expense.index') }}">
                                    <i class="nav-main-link-icon fa fa-file-invoice"></i>
                                    <span class="nav-main-link-name">Contas a Pagar</span>
                                </a>
                            </li>
                            <li class="nav-main-item">
                                <a class="nav-main-link{{ active(['dashboard.income.*'], ' active') }}"
                                    href="{{ route('dashboard.income.index') }}">
                                    <i class="nav-main-link-icon fa fa-file-invoice-dollar"></i>
                                    <span class="nav-main-link-name">Contas a Receber</span>
                                </a>
                            </li>
                            <li class="nav-main-heading">Administração</li>
                            @role('admin')
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ active(['dashboard.user.index'], ' active') }}"
                                        href="{{ route('dashboard.user.index') }}">
                                        <i class="nav-main-link-icon fa fa-user-lock"></i>
                                        <span class="nav-main-link-name">Usuários</span>
                                    </a>
                                </li>
                                <li class="nav-main-item">
                                    <a class="nav-main-link{{ active(['dashboard.import.index'], ' active') }}"
                                        href="{{ route('dashboard.import.index') }}">
                                        <i class="nav-main-link-icon fa fa-object-group"></i>
                                        <span class="nav-main-link-name">Importar Dados</span>
                                    </a>
                                </li>
                            @endrole
                            <li class="nav-main-item">
                                <a class="nav-main-link" href="#">
                                    <i class="nav-main-link-icon fa fa-newspaper"></i>
                                    <span class="nav-main-link-name">Relatórios</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                    <!-- END Side Navigation -->
                </div>
                <!-- END Sidebar Scrolling -->
            </div>
            <!-- Sidebar Content -->
        </nav>
        <!-- END Sidebar -->

        <!-- Header -->
        <header id="page-header">
            <!-- Header Content -->
            <div class="content-header">
                <!-- Left Section -->
                <div class="space-x-1">
                    <!-- Toggle Sidebar -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout"
                        data-action="sidebar_toggle">
                        <i class="fa fa-fw fa-bars"></i>
                    </button>
                    <!-- END Toggle Sidebar -->

                    <!-- Open Search Section -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout"
                        data-action="header_search_on">
                        <i class="fa fa-fw fa-search"></i>
                    </button>
                    <!-- END Open Search Section -->
                </div>
                <!-- END Left Section -->

                <!-- Right Section -->
                <div class="space-x-1">
                    <!-- User Dropdown -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-user-dropdown"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-user d-sm-none"></i>
                            <span class="d-none d-sm-inline-block fw-semibold">{{ Auth::user()->name }}</span>
                            <i class="fa fa-angle-down opacity-50 ms-1"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-md dropdown-menu-end p-0"
                            aria-labelledby="page-header-user-dropdown">
                            <div class="px-2 py-3 bg-body-light rounded-top">
                                <h5 class="h6 text-center mb-0">
                                    {{ Auth::user()->name }}
                                </h5>
                            </div>
                            <div class="p-2">
                                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="javascript:void(0)">
                                    <span>Perfil</span>
                                    <i class="fa fa-fw fa-user opacity-25"></i>
                                </a>
                                {{-- <a class="dropdown-item d-flex align-items-center justify-content-between"
                                    href="javascript:void(0)">
                                    <span>Inbox</span>
                                    <i class="fa fa-fw fa-envelope-open opacity-25"></i>
                                </a>
                                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="javascript:void(0)">
                                    <span>Invoices</span>
                                    <i class="fa fa-fw fa-file opacity-25"></i>
                                </a> --}}
                                <div class="dropdown-divider"></div>

                                <!-- Toggle Side Overlay -->
                                <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="javascript:void(0)">
                                    <span>Configurações</span>
                                    <i class="fa fa-fw fa-wrench opacity-25"></i>
                                </a>
                                <!-- END Side Overlay -->

                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item d-flex align-items-center justify-content-between space-x-1"
                                    href="javascript:void(0)"
                                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                    <span>Sair</span>
                                    <i class="fa fa-fw fa-sign-out-alt opacity-25"></i>
                                </a>
                                <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                    style="display: none;">
                                    @csrf
                                </form>
                            </div>
                        </div>
                    </div>
                    <!-- END User Dropdown -->

                    <!-- Notifications -->
                    <div class="dropdown d-inline-block">
                        <button type="button" class="btn btn-sm btn-alt-secondary" id="page-header-notifications"
                            data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-flag"></i>
                            <span class="text-primary">&bull;</span>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-end p-0"
                            aria-labelledby="page-header-notifications">
                            <div class="px-2 py-3 bg-body-light rounded-top">
                                <h5 class="h6 text-center mb-0">
                                    Notificações
                                </h5>
                            </div>
                            <ul class="nav-items my-2 fs-sm">
                                <li>
                                    <a class="text-dark d-flex py-2" href="javascript:void(0)">
                                        <div class="flex-shrink-0 me-2 ms-3">
                                            <i class="fa fa-fw fa-check text-success"></i>
                                        </div>
                                        <div class="flex-grow-1 pe-2">
                                            <p class="fw-medium mb-1">Configuração realizada
                                            </p>
                                            <div class="text-muted">15 min ago</div>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                            <div class="p-2 bg-body-light rounded-bottom">
                                <a class="dropdown-item text-center mb-0" href="javascript:void(0)">
                                    <i class="fa fa-fw fa-flag opacity-50 me-1"></i> Ver tudo
                                </a>
                            </div>
                        </div>
                    </div>
                    <!-- END Notifications -->

                    <!-- Toggle Side Overlay -->
                    <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                    {{-- <button type="button" class="btn btn-sm btn-alt-secondary" data-toggle="layout"
                        data-action="side_overlay_toggle">
                        <i class="fa fa-fw fa-stream"></i>
                    </button> --}}
                    <!-- END Toggle Side Overlay -->
                </div>
                <!-- END Right Section -->
            </div>
            <!-- END Header Content -->

            <!-- Header Search -->
            <div id="page-header-search" class="overlay-header bg-body-extra-light">
                <div class="content-header">
                    <form class="w-100" action="/dashboard" method="POST">
                        @csrf
                        <div class="input-group">
                            <!-- Close Search Section -->
                            <!-- Layout API, functionality initialized in Template._uiApiLayout() -->
                            <button type="button" class="btn btn-secondary" data-toggle="layout"
                                data-action="header_search_off">
                                <i class="fa fa-fw fa-times"></i>
                            </button>
                            <!-- END Close Search Section -->
                            <input type="text" class="form-control" placeholder="Search or hit ESC.."
                                id="page-header-search-input" name="page-header-search-input">
                            <button type="submit" class="btn btn-secondary">
                                <i class="fa fa-fw fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- END Header Search -->

            <!-- Header Loader -->
            <div id="page-header-loader" class="overlay-header bg-primary">
                <div class="content-header">
                    <div class="w-100 text-center">
                        <i class="far fa-sun fa-spin text-white"></i>
                    </div>
                </div>
            </div>
            <!-- END Header Loader -->
        </header>
        <!-- END Header -->

        <!-- Main Container -->
        <main id="main-container">
            @yield('content')
        </main>
        <!-- END Main Container -->

        <!-- Footer -->
        <footer id="page-footer">
            <div class="content py-3">
                <div class="row fs-sm">
                    <div class="col-md-12 order-sm-1 py-1 text-center text-sm-start">
                        Todos os direitos reservados &copy; Desenvolvido por Rubens Coelho | Excelence Brindes - <span
                            data-toggle="year-copy"></span>
                    </div>
                </div>
            </div>
        </footer>
        <!-- END Footer -->
    </div>
    <!-- END Page Container -->
    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>
    <script src="{{ asset('js/plugins/bootstrap-notify/bootstrap-notify.min.js') }}"></script>
    @vite('resources/js/codebase/app.js')
    @yield('js')
</body>

</html>
