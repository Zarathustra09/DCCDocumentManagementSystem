<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <img src="{{asset('logo.png')}}" alt="DMS Logo" class="app-brand-logo">
        </a>

        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
            <i class="bx bx-chevron-left bx-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>
    <li class="menu-header small text-uppercase">
        <span class="menu-header-text">Dashboard</span>
    </li>
    <ul class="menu-inner py-1">
        <!-- Dashboard -->
        <li class="menu-item {{Route::is('home*') ? 'active' : ''}}">
            <a href="{{route('home')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-alt"></i>
                <div data-i18n="Analytics">Overview</div>
            </a>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Document Management</span>
        </li>
        <li class="menu-item {{Route::is('folders.*') ? 'active' : ''}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div data-i18n="Account Settings">My Documents</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{Route::is('folders.*') ? 'active' : ''}}">                    <a href="{{route('folders.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Account">All Documents</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-star"></i>
                        <div data-i18n="Account">Favorites</div>
                    </a>
                </li>
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-time"></i>
                        <div data-i18n="Connections">Recent</div>
                    </a>
                </li>

                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-trash"></i>
                        <div data-i18n="Staff">Trash</div>
                    </a>
                </li>
            </ul>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Collaboration</span>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-share-alt"></i>
                <div data-i18n="Patient Records">Shared with me</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-comment"></i>
                <div data-i18n="Patient Records">Comments</div>
            </a>
        </li>

        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tools</span>
        </li>

        @can('manage users')
          <li class="menu-item">
              <a href="{{route('admin.roles.index')}}" class="menu-link">
                  <i class="menu-icon tf-icons bx bx-user-check"></i>
                  <div data-i18n="Patient Records">Roles</div>
              </a>
          </li>
        @endcan

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-search-alt"></i>
                <div data-i18n="Patient Records">Search</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-cog"></i>
                <div data-i18n="Patient Records">Settings</div>
            </a>
        </li>

        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons bx bx-chart"></i>
                <div data-i18n="Patient Records">Analytics</div>
            </a>
        </li>
    </ul>
</aside>
