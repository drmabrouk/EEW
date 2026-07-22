<header class="healthedia-global-header">
    <div class="header-left">
        <div class="brand-logo">
            <span class="logo-main">Healthedia</span>
            <span class="logo-sub">GLOBAL HEALTH ARCHIVE</span>
        </div>
        <nav class="header-nav">
            <a href="<?php echo esc_url(home_url('/research')); ?>" class="nav-item <?php echo is_page('research') || is_front_page() ? 'active' : ''; ?>">Archive Search</a>
            <a href="<?php echo esc_url(home_url('/directory')); ?>" class="nav-item <?php echo is_page('directory') ? 'active' : ''; ?>">Researchers</a>
            <a href="#" class="nav-item">Institutions</a>
            <a href="#" class="nav-item">Scientific Journal</a>
        </nav>
    </div>

    <div class="header-right">
        <a href="<?php echo esc_url(home_url('/id')); ?>" class="workspace-btn">
            <span class="dashicons dashicons-shield"></span> WORKSPACE
        </a>
        <div class="user-controls">
            <button class="icon-btn profile-btn"><span class="dashicons dashicons-admin-users"></span><span class="notification-dot"></span></button>
            <button class="icon-btn notification-btn"><span class="dashicons dashicons-bell"></span><span class="badge-count">2</span></button>
            <button class="icon-btn logout-btn"><span class="dashicons dashicons-external"></span></button>
        </div>
    </div>
</header>
