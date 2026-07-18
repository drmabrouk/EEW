<?php

namespace WSHC\Admin;

class AdminMenu {
    public function init() {
        add_action('admin_menu', [$this, 'register_menus']);
    }

    public function register_menus() {
        add_menu_page(
            'Plugin Information',
            'WSHC Plugin Info',
            'manage_options',
            'wshc_plugin_info',
            [$this, 'render_info_page'],
            'dashicons-info',
            100
        );
    }

    public function render_info_page() {
        echo '<div class="wrap">';
        echo '<h1>WSHC Plugin Information</h1>';
        echo '<p>This page documents all available shortcodes, features, and instructions on how to use them.</p>';
        echo '<h2>Available Shortcodes</h2>';
        echo '<table class="widefat striped">';
        echo '<thead><tr><th>Shortcode</th><th>Description</th></tr></thead>';
        echo '<tbody>';
        echo '<tr><td><code>[wshc_dashboard]</code></td><td>Displays the comprehensive member dashboard. Should be placed on the Dashboard page.</td></tr>';
        echo '<tr><td><code>[wshc_login_form]</code></td><td>Displays the login form. Should be placed on the Login page.</td></tr>';
        echo '<tr><td><code>[wshc_registration_form]</code></td><td>Displays the registration form. Use this on your Register page.</td></tr>';
        echo '<tr><td><code>[wshc_forgot_password_form]</code></td><td>Displays the forgot password / password reset form.</td></tr>';
        echo '<tr><td><code>[wshc_scientific_engine]</code></td><td>Displays the Scientific Research Engine & Repository.</td></tr>';
        echo '<tr><td><code>[wshc_members_directory]</code></td><td>Displays the Official Members Directory.</td></tr>';
        echo '<tr><td><code>[wshc_verification]</code></td><td>Displays the Verification Portal.</td></tr>';
        echo '</tbody>';
        echo '</table>';
        echo '<h2>Features Integrated into WordPress Admin Panel</h2>';
        echo '<ul>';
        echo '<li><strong>User Management:</strong> Create, edit, suspend, and delete users from the system seamlessly.</li>';
        echo '<li><strong>Public Member Profiles:</strong> Every published member automatically gets a public profile page accessible via <code>domain.com/username</code>.</li>';
        echo '<li><strong>Page Auto-generation:</strong> All required pages are created on plugin installation and recreated if missing.</li>';
        echo '</ul>';
        echo '</div>';
    }
}
