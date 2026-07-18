<?php

namespace WSHC\Programs;

class ProgramManager {
    public function init() {
        add_action('init', [$this, 'register_cpts']);
        add_shortcode('wshc_training_programs', [$this, 'render_programs_page']);
    }

    public function register_cpts() {
        register_post_type('wshc_program', [
            'labels' => [
                'name' => 'Training Programs',
                'singular_name' => 'Program'
            ],
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => ['title', 'editor', 'thumbnail', 'excerpt', 'custom-fields']
        ]);

        register_taxonomy('wshc_program_category', 'wshc_program', [
            'labels' => [
                'name' => 'Categories'
            ],
            'hierarchical' => true,
            'show_admin_column' => true,
        ]);
    }

    public function render_programs_page() {
        $programs = get_posts([
            'post_type' => 'wshc_program',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);

        if (empty($programs)) {
            return '<p>No training programs are currently available.</p>';
        }

        ob_start();
        echo '<div class="wshc-training-programs">';
        foreach ($programs as $prog) {
            $registration_link = get_post_meta($prog->ID, 'wshc_registration_link', true);
            $resource_link = get_post_meta($prog->ID, 'wshc_resource_link', true);

            echo '<div class="program-card">';
            if (has_post_thumbnail($prog->ID)) {
                echo '<div class="program-thumbnail">' . get_the_post_thumbnail($prog->ID, 'medium') . '</div>';
            }
            echo '<h3>' . esc_html($prog->post_title) . '</h3>';
            echo '<div class="program-excerpt">' . wpautop(esc_html($prog->post_excerpt)) . '</div>';

            echo '<div class="program-actions">';
            if ($registration_link) {
                echo '<a href="' . esc_url($registration_link) . '" class="button program-register-btn">Register Now</a> ';
            }
            if ($resource_link) {
                echo '<a href="' . esc_url($resource_link) . '" class="button program-resource-btn">Download Resources</a>';
            }
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }
}
