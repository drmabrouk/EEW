<header class="healthedia-global-header">
    <div class="header-left">
        <div class="brand-logo">
            <span class="logo-main">Healthedia</span>
            <span class="logo-sub">GLOBAL HEALTH ARCHIVE</span>
        </div>
        <nav class="header-nav">
            <a href="<?php echo esc_url(home_url('/research')); ?>" class="nav-item <?php echo is_page('research') || is_front_page() ? 'active' : ''; ?>">Archive Search</a>
            <a href="<?php echo esc_url(home_url('/directory')); ?>" class="nav-item <?php echo is_page('directory') ? 'active' : ''; ?>">Researchers</a>
            <a href="<?php echo esc_url(home_url('/institutions')); ?>" class="nav-item <?php echo is_page('institutions') ? 'active' : ''; ?>">Institutions</a>
            <a href="#" class="nav-item">Scientific Journal</a>
        </nav>
    </div>

    <div class="header-right">
        <div class="user-controls">
            <a href="<?php echo esc_url(home_url('/id')); ?>" class="icon-btn workspace-btn"><span class="dashicons dashicons-filter"></span></a>
            <a href="<?php echo esc_url(home_url('/id?section=profile')); ?>" class="icon-btn profile-btn"><span class="dashicons dashicons-admin-users"></span><span class="notification-dot"></span></a>
            <a href="<?php echo esc_url(home_url('/id?section=messages')); ?>" class="icon-btn notification-btn"><span class="dashicons dashicons-bell"></span></a>
            <a href="<?php echo wp_logout_url(home_url()); ?>" class="icon-btn logout-btn"><span class="dashicons dashicons-external"></span></a>
        </div>
    </div>
</header>
