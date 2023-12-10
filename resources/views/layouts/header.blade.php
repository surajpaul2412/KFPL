<div class="header-main px-3 px-lg-4">
    <!-- <a id="menuSidebar" href="#" class="menu-link me-3 me-lg-4"><i class="ri-menu-2-fill"></i></a> -->

    <div class="me-auto thc-breadcrum">
        @yield('breadcrum')
    </div>
    <div class="dropdown dropdown-skin d-flex">
        @yield('breadcrum-btn')
    </div>

    <div class="dropdown dropdown-profile ms-3 ms-xl-4 d-none">
        <a href="" class="dropdown-link" data-bs-toggle="dropdown" data-bs-auto-close="outside">
            <div class="avatar online"><img src="{{ asset('assets/img/img1.jpg') }}" alt=""></div>
        </a>
        <div class="dropdown-menu dropdown-menu-end mt-10-f">
            <div class="dropdown-menu-body">
                <div class="avatar avatar-xl online mb-3"><img src="{{ asset('assets/img/img1.jpg') }}" alt=""></div>
                <h5 class="mb-1 text-dark fw-semibold">Shaira Diaz</h5>
                <p class="fs-sm text-secondary">Premium Member</p>

                <nav class="nav">
                    <a href=""><i class="ri-edit-2-line"></i> My Profile</a>
                    <a href=""><i class="ri-user-settings-line"></i> Settings</a>
                    <a href=""><i class="ri-logout-box-r-line"></i> Log Out</a>
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
</style>