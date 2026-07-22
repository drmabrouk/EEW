<?php

namespace WSHC\Core;

class ModuleManager {
    private $modules = [
        'wshc_news'          => ['name' => 'News & Announcements', 'icon' => 'dashicons-megaphone'],
        'wshc_event'         => ['name' => 'Events & Conferences', 'icon' => 'dashicons-calendar-alt'],
        'wshc_publication'   => ['name' => 'Scientific Publications', 'icon' => 'dashicons-book-alt'],
        'wshc_journal'       => ['name' => 'Journals Management', 'icon' => 'dashicons-book'],
        'wshc_research_cat'  => ['name' => 'Research Categories', 'icon' => 'dashicons-category'],
        'wshc_institution'   => ['name' => 'Research Institutions', 'icon' => 'dashicons-building'],
        'wshc_global_dir'    => ['name' => 'Global Researchers Directory', 'icon' => 'dashicons-networking'],
        'wshc_board'         => ['name' => 'Board of Directors', 'icon' => 'dashicons-businessman'],
        'wshc_committee'     => ['name' => 'Committees', 'icon' => 'dashicons-groups'],
        'wshc_partner'       => ['name' => 'Partners & Sponsors', 'icon' => 'dashicons-store'],
        'wshc_media'         => ['name' => 'Media Center', 'icon' => 'dashicons-format-video'],
        'wshc_download'      => ['name' => 'Downloads Center', 'icon' => 'dashicons-download'],
        'wshc_resource'      => ['name' => 'Resource Library', 'icon' => 'dashicons-archive'],
        'wshc_testimonial'   => ['name' => 'Testimonials', 'icon' => 'dashicons-testimonial'],
        'wshc_success_story' => ['name' => 'Success Stories', 'icon' => 'dashicons-star-filled'],
        'wshc_career'        => ['name' => 'Careers', 'icon' => 'dashicons-portfolio'],
        'wshc_privacy'       => ['name' => 'Privacy Policy', 'icon' => 'dashicons-lock'],
        'wshc_terms'         => ['name' => 'Terms & Conditions', 'icon' => 'dashicons-clipboard'],
        'wshc_cookie'        => ['name' => 'Cookie Policy', 'icon' => 'dashicons-info-outline'],
        'wshc_help'          => ['name' => 'Help Center', 'icon' => 'dashicons-editor-help'],
        'wshc_support'       => ['name' => 'Support Center', 'icon' => 'dashicons-sos'],
        'wshc_sys_logs'      => ['name' => 'System Logs', 'icon' => 'dashicons-media-text'],
        'wshc_act_logs'      => ['name' => 'Activity Logs', 'icon' => 'dashicons-list-view'],
        'wshc_backup'        => ['name' => 'Backup & Restore', 'icon' => 'dashicons-backup'],
        'wshc_import'        => ['name' => 'Import & Export', 'icon' => 'dashicons-database'],
        'wshc_seo'           => ['name' => 'SEO Settings', 'icon' => 'dashicons-search'],
        'wshc_appearance'    => ['name' => 'Appearance Settings', 'icon' => 'dashicons-admin-customizer'],
        'wshc_home_builder'  => ['name' => 'Homepage Builder', 'icon' => 'dashicons-admin-home'],
        'wshc_menu_mgr'      => ['name' => 'Menu Manager', 'icon' => 'dashicons-menu-alt'],
        'wshc_footer_mgr'    => ['name' => 'Footer Manager', 'icon' => 'dashicons-arrow-down-alt'],
        'wshc_banner_mgr'    => ['name' => 'Banner Manager', 'icon' => 'dashicons-images-alt2'],
        'wshc_email_tpl'     => ['name' => 'Email Templates', 'icon' => 'dashicons-email-alt'],
        'wshc_notification'  => ['name' => 'Notification Center', 'icon' => 'dashicons-bell'],
    ];

    public function init() {
        add_action('init', [$this, 'register_module_cpts']);
    }

    public function register_module_cpts() {
        foreach ($this->modules as $slug => $data) {
            register_post_type($slug, [
                'labels' => [
                    'name' => $data['name'],
                    'singular_name' => rtrim($data['name'], 's')
                ],
                'public' => true,
                'show_ui' => false, // UI is managed via frontend dashboard
                'supports' => ['title', 'editor', 'thumbnail', 'custom-fields']
            ]);
        }
    }

    public function get_modules() {
        return $this->modules;
    }
}
