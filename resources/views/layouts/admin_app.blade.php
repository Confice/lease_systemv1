<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="{{ asset('sneat/assets/') }}/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />
    
    <title>@yield('title', 'LeaseEase X StoreEdge')</title>
    <meta name="description" content="@yield('meta_description', 'LeaseEase Property Management')" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
      rel="stylesheet"
    />

    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/fonts/boxicons.css') }}" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/core.css') }}" class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/css/theme-default.css') }}" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/demo.css') }}" />
    <!-- Custom Theme Override -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/custom-theme-override.css') }}" />
    <!-- Sidebar Toggle CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/css/sidebar-toggle.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('sneat/assets/vendor/js/helpers.js') }}"></script>
    <script src="{{ asset('sneat/assets/js/config.js') }}"></script>
    @stack('styles')
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar layout-menu-fixed">
      <div class="layout-container">
        <!-- Sidebar -->
        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo d-flex align-items-center position-relative">
            <a href="{{ url('/') }}" class="app-brand-link d-flex align-items-center">
              <span class="app-brand-logo demo">
                <img src="{{ asset('sneat/assets/img/favicon/favicon.ico') }}" alt="Logo" style="width: 30px; height: auto;">
              </span>
              <div class="ms-2">
                <span class="app-brand-text demo menu-text fw-bolder" style="text-transform: uppercase;">
                  LEASE
                </span><br>
                <small class="text-muted">LeaseEase X StoreEdge</small>
              </div>
            </a>
            <!-- Sidebar Toggle Button - Circular with arrow icon -->
            <button class="sidebar-toggle-btn" id="sidebar-toggle-btn" aria-label="Toggle Sidebar">
              <i class="bx bx-chevron-left"></i>
            </button>
          </div>

          <div class="menu-inner-shadow"></div>

          <!-- Sidebar Menu -->
          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
              <a href="{{ url('/admins/dashboard') }}" class="menu-link" title="Dashboard">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Dashboard">Dashboard</div>
              </a>
            </li>

            <!-- Marketplace Map -->
            <li class="menu-item {{ request()->is('admins/marketplace*') || request()->is('marketplace*') ? 'active' : '' }}">
              <a href="{{ route('admins.marketplace.index') }}" class="menu-link" title="Marketplace Map">
                <i class="menu-icon tf-icons bx bx-map-alt"></i>
                <div data-i18n="Marketplace Map">Marketplace Map</div>
              </a>
            </li>

            <!-- Leases -->
            <li class="menu-item {{ request()->is('leases*') ? 'active' : '' }}">
              <a href="{{ route('admins.leases.index') }}" class="menu-link" title="Leases">
                <i class="menu-icon tf-icons bx bx-file"></i>
                <div data-i18n="Leases">Leases</div>
              </a>
            </li>

            <!-- Prospective Tenants -->
            <li class="menu-item {{ request()->is('tenants/prospective') ? 'active' : '' }}">
              <a href="{{ route('admins.prospective-tenants.index') }}" class="menu-link" title="Prospective Tenants">
                <i class="menu-icon tf-icons bx bx-user"></i>
                <div data-i18n="Prospective Tenants">Prospective Tenants</div>
              </a>
            </li>

            <!-- Stalls -->
            <li class="menu-item {{ request()->is('stalls') ? 'active' : '' }}">
              <a href="{{ url('/stalls') }}" class="menu-link" title="Stalls">
                <i class="menu-icon tf-icons bx bx-store"></i>
                <div data-i18n="Stalls">Stalls</div>
              </a>
            </li>

            <!-- Bills -->
            <li class="menu-item {{ request()->is('admins/bills*') ? 'active' : '' }}">
              <a href="{{ route('admins.bills.index') }}" class="menu-link" title="Bills">
                <i class="menu-icon tf-icons bx bx-credit-card"></i>
                <div data-i18n="Bills">Bills</div>
              </a>
            </li>

            <!-- Tenant Feedback -->
            <li class="menu-item {{ request()->is('tenant-feedback') ? 'active' : '' }}">
              <a href="{{ url('/tenant-feedback') }}" class="menu-link" title="Tenant Feedback">
                <i class="menu-icon tf-icons bx bx-message-dots"></i>
                <div data-i18n="Tenant Feedback">Tenant Feedback</div>
              </a>
            </li>

            <!-- Analytics -->
            <li class="menu-item {{ request()->is('analytics') ? 'active' : '' }}">
              <a href="{{ url('/analytics') }}" class="menu-link" title="Analytics">
                <i class="menu-icon tf-icons bx bx-bar-chart-alt-2"></i>
                <div data-i18n="Analytics">Analytics</div>
              </a>
            </li>

            <!-- Users -->
            <li class="menu-item {{ request()->is('users') ? 'active' : '' }}">
              <a href="{{ url('/users') }}" class="menu-link" title="Users">
                <i class="menu-icon tf-icons bx bx-group"></i>
                <div data-i18n="Users">Users</div>
              </a>
            </li>

            <!-- Activity Logs -->
            <li class="menu-item {{ request()->is('activity-logs') ? 'active' : '' }}">
              <a href="{{ url('/activity-logs') }}" class="menu-link" title="Activity Logs">
                <i class="menu-icon tf-icons bx bx-list-check"></i>
                <div data-i18n="Activity Logs">Activity Logs</div>
              </a>
            </li>

            <!-- Archived Items -->
            <li class="menu-item {{ request()->is('archived-items*') ? 'active' : '' }}">
              <a href="{{ route('admins.archived-items.index') }}" class="menu-link" title="Archived Items">
                <i class="menu-icon tf-icons bx bx-archive"></i>
                <div data-i18n="Archived Items">Archived Items</div>
              </a>
            </li>
          </ul>
        </aside>
        <!-- /Sidebar -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <nav class="layout-navbar container-fluid navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center w-100" id="navbar-collapse">
              
              <!-- Module Title -->
              <div class="navbar-nav align-items-center flex-grow-1">
                <div class="nav-item">
                  <h5 class="mb-0 fw-bold">@yield('page-title', 'Dashboard')</h5>
                </div>
              </div>
              <!-- /Module Title -->

              <ul class="navbar-nav flex-row align-items-center">
                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" id="profileDropdown">
                    <div class="avatar avatar-online">
                      @php
                        $user = Auth::user();
                        $firstName = $user->firstName ?? '';
                        $lastName = $user->lastName ?? '';
                        $initials = strtoupper(substr($firstName, 0, 1) . substr($lastName, 0, 1));
                      @endphp
                      <div class="avatar-initial rounded-circle bg-label-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #EFEFEA; background-color: #7F9267 !important;">
                        {{ $initials ?: 'U' }}
                      </div>
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <div class="avatar-initial rounded-circle bg-label-primary" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-weight: bold; color: #EFEFEA; background-color: #7F9267 !important;">
                                {{ $initials ?: 'U' }}
                              </div>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block" id="userNameDisplay">Guest</span>
                            <small class="text-dark">{{ Auth::user()->role ?? 'User' }}</small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li><div class="dropdown-divider"></div></li>
                    <li><a class="dropdown-item" href="#"><i class="bx bx-user me-2"></i><span class="align-middle">My Profile</span></a></li>
                    <li><a class="dropdown-item" href="#"><i class="bx bx-cog me-2"></i><span class="align-middle">Settings</span></a></li>
                    <li>
                      <a class="dropdown-item" href="{{ route('logout') }}"
                         onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                      <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                          @csrf
                      </form>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <div class="container-fluid flex-grow-1 container-p-y">
              
              <!-- Flash Messages -->
              @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
              @endif
              @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
              @endif

              <!-- Breadcrumb (if needed) -->
              @hasSection('breadcrumb')
                <div class="d-flex justify-content-between align-items-center mb-3">
                  @yield('breadcrumb')
                </div>
              @endif

              @yield('content')
            </div>
            <div class="content-backdrop fade"></div>
          </div>
          <!-- / Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
    
    <!-- Core JS -->
    <script src="{{ asset('sneat/assets/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('sneat/assets/vendor/js/menu.js') }}"></script>

    <!-- Main JS -->
    <script src="{{ asset('sneat/assets/js/main.js') }}"></script>

    @stack('scripts')

    <!-- Global SweetAlert Configuration (loads after all scripts, including SweetAlert) -->
    <script src="{{ asset('sneat/assets/js/sweetalert-global-config.js') }}"></script>

    <!-- Optional: GitHub buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    
    <!-- Profile Dropdown Script -->
    <script>
      $(document).ready(function() {
        $('#profileDropdown').on('click', function() {
          const userNameDisplay = $('#userNameDisplay');
          const currentText = userNameDisplay.text().trim();
          
          // If it says "Guest", change it to first name
          if (currentText === 'Guest') {
            @php
              $user = Auth::user();
              $firstName = $user->firstName ?? 'Guest';
            @endphp
            userNameDisplay.text('{{ $firstName }}');
          }
        });
      });
    </script>
    
    <!-- Prevent dropdown items from turning white on click -->
    <style>
      .dropdown-item:active,
      .dropdown-item:focus,
      .dropdown-item.active {
        color: #000000 !important;
        background-color: #EFEFEA !important;
      }
      .dropdown-item:hover {
        color: #000000 !important;
        background-color: #EFEFEA !important;
      }
      .dropdown-item i,
      .dropdown-item span {
        color: inherit !important;
      }
    </style>
  </body>
</html>
