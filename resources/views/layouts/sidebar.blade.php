<!-- Sidebar -->
<div id="sidebar">
    <div class="top-bar d-flex align-items-center justify-content-between ">
        <span class="sidebar-logo fw-bold fs-5 text-white">NetExplorer</span>
        <button class="toggle-btn" id="toggleSidebar">
            <i class="bi bi-list"></i>
        </button>
    </div>


    <ul>
        <li class="nav-item {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}">
            <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center">
                <i class="fa-solid fa-chart-line me-2"></i>
                <span> {{ __('Dashboard') }} </span>
            </a>
        </li>
        <li class="nav-item {{ Route::currentRouteName() == 'devices' ? 'active' : '' }}">
            <a href="{{ route('devices.index') }}" class="nav-link d-flex align-items-center">
                <i class="bi bi-gear"></i>
                <span> {{ __('Devices') }} </span>
            </a>
        </li>
        <li class="nav-item {{ Route::currentRouteName() == 'devices' ? 'active' : '' }}">
            <a href="{{ route('pools.index') }}" class="nav-link d-flex align-items-center">
                <i class="bi bi-gear"></i>
                <span> {{ __('Pools') }} </span>
            </a>
        </li>
        <li class="nav-item {{ Route::currentRouteName() == 'settings' ? 'active' : '' }}">
            <a href="{{ route('settings') }}" class="nav-link d-flex align-items-center">
                <i class="bi bi-gear"></i>
                <span> {{ __('Settings') }} </span>
            </a>
        </li>

        <!-- Move Logout to the end -->
        <li class="mt-auto"><i class="bi bi-box-arrow-right"></i> <span>Logout</span></li>
    </ul>

</div>

