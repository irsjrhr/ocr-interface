<!-- Menu -->
<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
    <div class="app-brand demo" style="height: 85px !important">
        <a href="#" class="app-brand-link">
            <span class="app-brand-logo rounded-circle demo d-flex align-items-center justify-content-center" style="background: var(--bs-primary); width: 46px; height: 46px !important; overflow: hidden; color: white;">
                <i class="ti ti-camera fs-3"></i>
            </span>
            <span class="app-brand-text demo menu-text fw-bold d-flex flex-column ms-2" style="letter-spacing: 1px;">
                <span style="font-size: 18px;">OCR APP</span>
                <small class="mt-1" style="font-size: 11px; letter-spacing: 0.5px;">Intelligent Document</small>
            </span>
        </a>
        <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto">
            <i class="ti menu-toggle-icon d-none d-xl-block ti-sm align-top mb-4"></i>
            <i class="ti ti-x d-block d-xl-none ti-sm align-middle"></i>
        </a>
    </div>

    <div class="menu-inner-shadow"></div>

    <ul class="menu-inner py-1">
        <!-- Dashboards -->
        <li class="menu-item {{ request()->is('dashboard') ? 'active' : '' }}">
            <a href="/dashboard" class="menu-link">
                <i class="menu-icon tf-icons ti ti-home"></i>
                <div>Dashboard</div>
            </a>
        </li>
        
        <!-- Minio Storage -->
        <li class="menu-item {{ request()->is('minio*') ? 'active' : '' }}">
            <a href="/minio" class="menu-link">
                <i class="menu-icon tf-icons ti ti-server"></i>
                <div>Minio Storage</div>
            </a>
        </li>
        
        
        <!-- Dropdown Modul -->
        <li class="menu-item {{ request()->is('ocr*') ? 'active open' : '' }}">
            <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons ti ti-layout-grid"></i>
                <div>OCR Interface</div>
            </a>
            <ul class="menu-sub">
                <li class="menu-item">
                    <a href="#" class="menu-link">
                        <div>Dashboard</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is(['ocr/storage*']) ? 'active' : '' }}">
                    <a href="{{ route('ocr.storage.index') }}" class="menu-link">
                        <div>Storage</div>
                    </a>
                </li>
                <li class="menu-item {{ request()->is(['ocr/simulate-webhook*']) ? 'active' : '' }}">
                    <a href="{{ route('ocr.simulate_webhook.form') }}" class="menu-link">
                        <div>Rekayasa Webhook</div>
                    </a>
                </li>
            </ul>
        </li>
        <li class="menu-item">
            <a href="#" class="menu-link">
                <i class="menu-icon tf-icons ti ti-settings"></i>
                <div>Pengaturan</div>
            </a>
        </li>
    </ul>
</aside>
<!-- / Menu -->
