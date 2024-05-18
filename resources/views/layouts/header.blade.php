<div class="header-main px-3 px-lg-4">
    <!-- <a id="menuSidebar" href="#" class="menu-link me-3 me-lg-4"><i class="ri-menu-2-fill"></i></a> -->

    <div class="me-auto thc-breadcrum">
        @yield('breadcrum')
    </div>
    <div class="dropdown dropdown-skin d-flex">
        @yield('breadcrum-btn')
    </div>

    <div class="dropdown dropdown-profile ms-3 ms-xl-4 ">
        <a href="" class="dropdown-link" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <div class="avatar online"><img src="{{ asset('assets/img/img6.jpg') }}" alt=""></div>
        </a>
        <div class="dropdown-menu dropdown-menu-end mt-10-f">
            <div class="dropdown-menu-body">
                <div class="avatar avatar-xl online mb-3"><img src="{{ asset('assets/img/img6.jpg') }}" alt=""></div>
                <h5 class="mb-1 text-dark fw-semibold">{{Auth::user()->name}}</h5>
                <nav class="nav">
                    <!-- Showing error while ADMIN role active -->
                    @php
                    if(!Auth()->user()->isAdmin())
                    {
                        $user_roles =  auth()->user()->roles;
                        $roles_names = [];
                        foreach($user_roles as $role)
                        {
                          $roles_names[] = strtolower($role->name);
                        }
                        // FIND USER ROLES attached to URL
                        $url = str_replace(URL('/'), '', URL::current());
                        $url = trim(rtrim(ltrim(trim($url),'/'),'/'));
                        $parts = explode('/', $url);

                        // SELECTED ROLE
                        $current_role = isset($parts[0]) ? $parts[0] : '';

                        $roleCount = count($roles_names);

                        if($roleCount > 1)
                        {
                            foreach($roles_names as $role)
                            {
                              if( strtolower($role) != strtolower($current_role) )
                              {
                                echo "<a href='/". strtolower($role) . "/dashboard'><i class='ri-toggle-line'></i>" . ucwords($role) . "</a>";
                              }
                            }
                        }
                    }
                    @endphp
                </nav>
            </div><!-- dropdown-menu-body -->
        </div><!-- dropdown-menu -->
    </div><!-- dropdown -->
</div><!-- header-main -->

<style type="text/css">
    .thc-breadcrum {
        color: #212128;
        font-size: 24px;
        font-style: normal;
        font-weight: 500;
        line-height: 22px;
    }
    .helpdesk-icon {
        width: 50px !important;
        height: 54px !important;
    }
    .helpdesk-icon i {
        font-size: 29px;
    }
    .card-one .card-body {
        padding: 8px 10px !important;
    }
</style>
