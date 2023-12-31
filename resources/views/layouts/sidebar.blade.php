<div class="sidebar">
    <div class="sidebar-header">
        <a href="{{ url('/') }}" class="">
            <img src="{{ asset('assets/img/logo.jpg') }}">
        </a>
    </div><!-- sidebar-header -->
    <div id="sidebarMenu" class="sidebar-body">
        <div class="nav-group show">
            <ul class="nav nav-sidebar">
                @if(auth()->user()->isAdmin())
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ Request::is('admin/dashboard*') ? 'active' : '' }}"><i class="ri-home-5-line"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('employees.index') }}" class="nav-link {{ Request::is('admin/employees*') ? 'active' : '' }}"><i class="ri-group-line"></i> <span>Employee Management</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.tickets.index') }}" class="nav-link {{ Request::is('admin/tickets*') ? 'active' : '' }}"><i class="ri-flag-2-line"></i> <span>Tickets</span></a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link has-sub {{ Request::is('admin/amcs*') ? 'active' : '' }} {{ Request::is('admin/securities*') ? 'active' : '' }}"><i class="ri-user-2-line"></i> <span>AMC Manager</span></a>
                    <nav class="nav nav-sub" style="{{ Request::is('admin/amcs*') || Request::is('admin/securities*') ? 'display: block;' : 'display: none;' }}">
                        <a href="{{ route('amcs.index') }}" class="nav-sub-link">AMC</a>
                        <a href="{{ route('securities.index') }}" class="nav-sub-link">Security</a>
                    </nav>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link"><i class="ri-notification-4-line"></i> <span>Alerts</span></a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link"><i class="ri-alarm-warning-line"></i> <span>Disputes</span></a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link"><i class="ri-funds-box-line"></i> <span>Reports</span></a>
                </li>
                <li class="nav-item">
                    <a href="" class="nav-link"><i class="ri-article-line"></i> <span>Templates</span></a>
                </li>
                @endif
                @if(auth()->user()->isTrader())
                <li class="nav-item">
                    <a href="{{ route('trader.dashboard') }}" class="nav-link {{ Request::is('trader/dashboard*') ? 'active' : '' }}"><i class="ri-home-5-line"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('trader.tickets.index') }}" class="nav-link {{ Request::is('trader/tickets*') ? 'active' : '' }}"><i class="ri-flag-2-line"></i> <span>Tickets</span></a>
                </li>
                @endif
                @if(auth()->user()->isOps())
                <li class="nav-item">
                    <a href="{{ route('ops.dashboard') }}" class="nav-link {{ Request::is('ops/dashboard*') ? 'active' : '' }}"><i class="ri-home-5-line"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('ops.tickets.index') }}" class="nav-link {{ Request::is('ops/tickets*') ? 'active' : '' }}"><i class="ri-flag-2-line"></i> <span>Tickets</span></a>
                </li>
                @endif
                @if(auth()->user()->isAccounts())
                <li class="nav-item">
                    <a href="{{ route('accounts.dashboard') }}" class="nav-link {{ Request::is('accounts/dashboard*') ? 'active' : '' }}"><i class="ri-home-5-line"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('accounts.tickets.index') }}" class="nav-link {{ Request::is('accounts/tickets*') ? 'active' : '' }}"><i class="ri-flag-2-line"></i> <span>Tickets</span></a>
                </li>
                @endif
                @if(auth()->user()->isDealer())
                <li class="nav-item">
                    <a href="{{ route('dealer.dashboard') }}" class="nav-link {{ Request::is('dealer/dashboard*') ? 'active' : '' }}"><i class="ri-home-5-line"></i> <span>Dashboard</span></a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('dealer.tickets.index') }}" class="nav-link {{ Request::is('dealer/tickets*') ? 'active' : '' }}"><i class="ri-flag-2-line"></i> <span>Tickets</span></a>
                </li>
                @endif
            </ul>
        </div><!-- nav-group -->
    </div><!-- sidebar-body -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-top">
            <div class="sidebar-footer-thumb">
                <img src="{{ asset('assets/img/img1.jpg') }}" alt="">
            </div><!-- sidebar-footer-thumb -->
            <div class="sidebar-footer-body">
                <p>{{Auth::user()->roles[0]->name}}</p>
                <h6><a class="text-capitalize" href="{{ url('/pages/profile') }}">{{Auth::user()->name}}</a></h6>
                
            </div><!-- sidebar-footer-body -->
            <a id="sidebarFooterMenu" href="" class="dropdown-link"><i class="ri-arrow-down-s-line"></i></a>
        </div><!-- sidebar-footer-top -->
        <div class="sidebar-footer-menu">
            <nav class="nav">
                <a href=""><i class="ri-edit-2-line"></i> My Profile</a>
                <a href=""><i class="ri-user-settings-line"></i> Settings</a>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ri-logout-box-r-line"></i> Log Out
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </nav>
        </div><!-- sidebar-footer-menu -->
    </div><!-- sidebar-footer -->
</div><!-- sidebar -->