<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo">
        <a href="#" class="app-brand-link">
            <img src="{{asset('logo.png')}}" alt="Logo Here" class="app-brand-logo">
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
        <li class="menu-item {{Route::is('home*') ? 'active' : ''}}" id="menu-home">
            <a href="{{route('home')}}" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-alt"></i>
                <div data-i18n="Analytics">Home</div>
            </a>
        </li>


        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Document Management</span>
        </li>


      @can('approve document registration')
       <li class="menu-item {{ Route::is('document-registry.list') || Route::is('dcn.index') ? 'active' : '' }}">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                  <i class="menu-icon tf-icons bx bx-archive"></i>
                  <div data-i18n="Patient Records">Registry Management</div>
              </a>
              <ul class="menu-sub">
                  <li class="menu-item {{ Route::is('document-registry.list') ? 'active' : '' }}">
                      <a href="{{ route('document-registry.list') }}" class="menu-link">
                          <i class="menu-icon tf-icons bx bx-list-ul"></i>
                          <div data-i18n="Tracking Registration">Registration Tracking</div>
                      </a>
                  </li>
                  <li class="menu-item">
                      <a href="{{route('dcn.index')}}" class="menu-link">
                          <i class="menu-icon tf-icons bx bx-cog"></i>
                          <div data-i18n="DCN Control">Assign DCN</div>
                      </a>
                  </li>

              </ul>
          </li>
      @endcan




        @canany(['view category', 'view customer'])
            <li class="menu-item {{ Route::is('categories.*') || Route::is('customers.*') ? 'active' : '' }}">
                <a href="javascript:void(0);" class="menu-link menu-toggle">
                    <i class="menu-icon tf-icons bx bx-data"></i>
                    <div data-i18n="Data Management">Data Management</div>
                </a>
                <ul class="menu-sub">
                    @can('view category')
                        <li class="menu-item {{ Route::is('categories.*') ? 'active' : '' }}">
                            <a href="{{ route('categories.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-category"></i>
                                <div data-i18n="Categories">Categories</div>
                            </a>
                        </li>
                    @endcan
                    @can('view customer')
                        <li class="menu-item {{ Route::is('customers.*') ? 'active' : '' }}">
                            <a href="{{ route('customers.index') }}" class="menu-link">
                                <i class="menu-icon tf-icons bx bx-user"></i>
                                <div data-i18n="Customers">Customers</div>
                            </a>
                        </li>
                    @endcan
                </ul>
            </li>
        @endcanany


        @can('submit document for approval')
            <li class="menu-item {{Route::is('document-registry.index') || Route::is('document-registry.create') ? 'active' : ''}}" id="menu-my-registrations">
                <a href="{{route('document-registry.index')}}" class="menu-link">
                    <i class="menu-icon tf-icons bx bx-upload"></i>
                    <div data-i18n="Patient Records">My Registrations</div>
                </a>
            </li>
        @endcan

        <li class="menu-item {{Route::is('folders.*') ? 'active' : ''}}" id="menu-my-documents">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-folder"></i>
                <div data-i18n="Account Settings">My Documents</div>
            </a>
            <ul class="menu-sub">


                <li class="menu-item {{Route::is('folders.*') ? 'active' : ''}}">
                    <a href="{{route('folders.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-file"></i>
                        <div data-i18n="Account">All Documents</div>
                    </a>
                </li>

{{--                <li class="menu-item">--}}
{{--                    <a href="#" class="menu-link">--}}
{{--                        <i class="menu-icon tf-icons bx bx-star"></i>--}}
{{--                        <div data-i18n="Account">Favorites</div>--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--                <li class="menu-item">--}}
{{--                    <a href="#" class="menu-link">--}}
{{--                        <i class="menu-icon tf-icons bx bx-time"></i>--}}
{{--                        <div data-i18n="Connections">Recent</div>--}}
{{--                    </a>--}}
{{--                </li>--}}

{{--                <li class="menu-item">--}}
{{--                    <a href="#" class="menu-link">--}}
{{--                        <i class="menu-icon tf-icons bx bx-trash"></i>--}}
{{--                        <div data-i18n="Staff">Trash</div>--}}
{{--                    </a>--}}
{{--                </li>--}}
            </ul>
        </li>








{{--        <li class="menu-header small text-uppercase">--}}
{{--            <span class="menu-header-text">Collaboration</span>--}}
{{--        </li>--}}

{{--        <li class="menu-item">--}}
{{--            <a href="#" class="menu-link">--}}
{{--                <i class="menu-icon tf-icons bx bx-share-alt"></i>--}}
{{--                <div data-i18n="Patient Records">Shared with me</div>--}}
{{--            </a>--}}
{{--        </li>--}}

{{--        <li class="menu-item">--}}
{{--            <a href="#" class="menu-link">--}}
{{--                <i class="menu-icon tf-icons bx bx-comment"></i>--}}
{{--                <div data-i18n="Patient Records">Comments</div>--}}
{{--            </a>--}}
{{--        </li>--}}
        @can('manage users')
        <li class="menu-header small text-uppercase">
            <span class="menu-header-text">Tools</span>
        </li>


            <li class="menu-item {{Route::is('roles.*') || Route::is('admin.users.*') ? 'active' : ''}}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-shield"></i>
                <div data-i18n="Account Settings">Roles & Permissions</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item {{Route::is('admin.users.*') ? 'active' : ''}}">
                    <a href="{{route('admin.users.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-user"></i>
                        <div data-i18n="Account">User Permissions</div>
                    </a>
                </li>

                <li class="menu-item {{Route::is('roles.*') ? 'active' : ''}}">
                    <a href="{{route('roles.index')}}" class="menu-link">
                        <i class="menu-icon tf-icons bx bx-id-card"></i>
                        <div data-i18n="Account">Role Permissions</div>
                    </a>
                </li>

            </ul>
            </li>



{{--          <li class="menu-item {{Route::is('admin.roles.*') ? 'active' : ''}}">--}}
{{--              <a href="{{route('admin.roles.index')}}" class="menu-link">--}}
{{--                  <i class="menu-icon tf-icons bx bx-user-check"></i>--}}
{{--                  <div data-i18n="Patient Records">Roles</div>--}}
{{--              </a>--}}
{{--          </li>--}}
        @endcan



{{--        <li class="menu-item">--}}
{{--            <a href="#" class="menu-link">--}}
{{--                <i class="menu-icon tf-icons bx bx-search-alt"></i>--}}
{{--                <div data-i18n="Patient Records">Search</div>--}}
{{--            </a>--}}
{{--        </li>--}}

{{--        <li class="menu-item">--}}
{{--            <a href="#" class="menu-link">--}}
{{--                <i class="menu-icon tf-icons bx bx-cog"></i>--}}
{{--                <div data-i18n="Patient Records">Settings</div>--}}
{{--            </a>--}}
{{--        </li>--}}

{{--        <li class="menu-item">--}}
{{--            <a href="#" class="menu-link">--}}
{{--                <i class="menu-icon tf-icons bx bx-chart"></i>--}}
{{--                <div data-i18n="Patient Records">Analytics</div>--}}
{{--            </a>--}}
{{--        </li>--}}
    </ul>
</aside>
