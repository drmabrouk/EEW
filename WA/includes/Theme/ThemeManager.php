<?php

namespace WSHC\Theme;

class ThemeManager {
    public function init() {
        add_filter('template_include', [$this, 'force_global_layout'], 99);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_theme_assets']);
    }

    public function force_global_layout($template) {
        // If it's the home page, an explicit plugin page, or search results, hijack the layout
        if (is_front_page() || is_home() || is_page()) {
            $global_template = WSHC_PLUGIN_DIR . 'templates/global/app-layout.php';
            if (file_exists($global_template)) {
                return $global_template;
            }
        }
        return $template;
    }

    public function enqueue_theme_assets() {
        wp_enqueue_style('wshc-theme-style', WSHC_PLUGIN_URL . 'assets/css/theme.css', [], '1.0.0');
        // Include dashicons for icons if not already present
        wp_enqueue_style('dashicons');
    }
}
