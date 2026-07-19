<?php

namespace WSHC\Dashboard;

/**
 * Manage the system dashboard.
 */
class DashboardManager {
    /**
     * Initialize dashboard hooks.
     */
    public function init() {
        add_shortcode('wshc_dashboard', [$this, 'render_dashboard']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_dashboard_assets']);
        add_action('wp_ajax_wshc_revert_log', [$this, 'handle_revert_log']);
        add_action('wp_ajax_wshc_dashboard_load_module', [$this, 'handle_load_module']);
        add_action('wp_ajax_wshc_dashboard_page_action', [$this, 'handle_page_action']);
        add_action('wp_ajax_wshc_dashboard_item_action', [$this, 'handle_item_action']);
        add_action('wp_ajax_wshc_dashboard_restore_pages', [$this, 'handle_restore_pages']);
    }

    public function handle_restore_pages() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        \WSHC\Core\Activator::create_pages();
        wp_send_json_success(['message' => 'Required system pages have been successfully restored.']);
    }

    public function handle_page_action() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $action = sanitize_text_field($_POST['page_action']);
        $post_id = intval($_POST['post_id']);

        if ($action === 'delete') {
            wp_delete_post($post_id, true);
            wp_send_json_success(['message' => 'Page deleted.']);
        } elseif ($action === 'publish') {
            wp_update_post(['ID' => $post_id, 'post_status' => 'publish']);
            wp_send_json_success(['message' => 'Page published.']);
        } elseif ($action === 'unpublish') {
            wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
            wp_send_json_success(['message' => 'Page unpublished.']);
        } elseif ($action === 'duplicate') {
            $post = get_post($post_id);
            if ($post) {
                wp_insert_post([
                    'post_title' => $post->post_title . ' (Copy)',
                    'post_content' => $post->post_content,
                    'post_status' => 'draft',
                    'post_type' => 'page'
                ]);
                wp_send_json_success(['message' => 'Page duplicated.']);
            }
        }
        wp_send_json_error(['message' => 'Invalid action.']);
    }

    public function handle_item_action() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $action = sanitize_text_field($_POST['item_action']);
        $post_id = intval($_POST['post_id']);
        $module = sanitize_text_field($_POST['module']);

        if ($module === 'messages') {
            global $wpdb;
            $table = $wpdb->prefix . 'wshc_contact_messages';
            if ($action === 'delete') {
                $wpdb->delete($table, ['id' => $post_id]);
            } elseif ($action === 'read') {
                $wpdb->update($table, ['status' => 'read'], ['id' => $post_id]);
            }
            wp_send_json_success(['message' => 'Message updated.']);
        } else {
            // Generic CPT actions (FAQ, Programs)
            if ($action === 'delete') {
                wp_delete_post($post_id, true);
                wp_send_json_success(['message' => 'Item deleted.']);
            } elseif ($action === 'publish') {
                wp_update_post(['ID' => $post_id, 'post_status' => 'publish']);
                wp_send_json_success(['message' => 'Item published.']);
            } elseif ($action === 'unpublish') {
                wp_update_post(['ID' => $post_id, 'post_status' => 'draft']);
                wp_send_json_success(['message' => 'Item unpublished.']);
            }
        }
        wp_send_json_error(['message' => 'Action failed.']);
    }

    public function handle_load_module() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');
        if (!current_user_can('manage_options')) wp_send_json_error();

        $module = sanitize_text_field($_POST['module']);
        ob_start();

        if ($module === 'pages') {
            $pages = get_pages(['post_status' => ['publish', 'draft']]);
            echo '<button class="button restore-pages-btn" style="margin-bottom:20px; padding:10px; background:#000; color:#fff; cursor:pointer;">Restore Missing System Pages</button>';
            echo '<table class="wp-list-table widefat striped"><thead><tr><th>Title</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
            foreach ($pages as $p) {
                echo '<tr>';
                echo '<td>' . esc_html($p->post_title) . '</td>';
                echo '<td>' . esc_html($p->post_status) . '</td>';
                echo '<td><button class="button page-action-btn" data-id="'.$p->ID.'" data-action="publish">Publish</button> <button class="button page-action-btn" data-id="'.$p->ID.'" data-action="unpublish">Unpublish</button> <button class="button page-action-btn" data-id="'.$p->ID.'" data-action="duplicate">Duplicate</button> <button class="button page-action-btn" data-id="'.$p->ID.'" data-action="delete" style="color:red;">Delete</button></td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        } elseif ($module === 'programs' || $module === 'faq') {
            $type = ($module === 'programs') ? 'wshc_program' : 'wshc_faq';
            $posts = get_posts(['post_type' => $type, 'post_status' => ['publish', 'draft'], 'posts_per_page' => -1]);
            echo '<table class="wp-list-table widefat striped"><thead><tr><th>Title</th><th>Status</th><th>Actions</th></tr></thead><tbody>';
            foreach ($posts as $p) {
                echo '<tr>';
                echo '<td>' . esc_html($p->post_title) . '</td>';
                echo '<td>' . esc_html($p->post_status) . '</td>';
                echo '<td><button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="publish">Publish</button> <button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="unpublish">Unpublish</button> <button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="delete" style="color:red;">Delete</button></td>';
                echo '</tr>';
            }
            if (empty($posts)) echo '<tr><td colspan="3">No items found.</td></tr>';
            echo '</tbody></table>';
        } elseif ($module === 'messages') {
            global $wpdb;
            $table = $wpdb->prefix . 'wshc_contact_messages';
            $messages = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");
            echo '<table class="wp-list-table widefat striped"><thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead><tbody>';
            foreach ($messages as $msg) {
                echo '<tr>';
                echo '<td>' . esc_html($msg->full_name) . '</td>';
                echo '<td>' . esc_html($msg->email) . '</td>';
                echo '<td>' . esc_html($msg->subject) . '</td>';
                echo '<td>' . esc_html($msg->status) . '</td>';
                echo '<td>' . esc_html($msg->created_at) . '</td>';
                echo '<td><button class="button item-action-btn" data-module="messages" data-id="'.$msg->id.'" data-action="read">Mark Read</button> <button class="button item-action-btn" data-module="messages" data-id="'.$msg->id.'" data-action="delete" style="color:red;">Delete</button></td>';
                echo '</tr>';
            }
            if (empty($messages)) echo '<tr><td colspan="6">No messages found.</td></tr>';
            echo '</tbody></table>';
        } elseif ($module === 'shortcodes') {
            echo '<div class="wrap" style="background:#fff; padding:20px; border-radius:8px; border:1px solid #ddd;">';
            echo '<h2>Available Shortcodes</h2>';
            echo '<p>This page documents all available shortcodes integrated directly into your theme.</p>';
            echo '<table class="wp-list-table widefat striped">';
            echo '<thead><tr><th>Shortcode</th><th>Description</th></tr></thead>';
            echo '<tbody>';
            echo '<tr><td><code>[wshc_dashboard]</code></td><td>Displays the comprehensive member dashboard. Should be placed on the Dashboard page.</td></tr>';
            echo '<tr><td><code>[wshc_login_form]</code></td><td>Displays the login form.</td></tr>';
            echo '<tr><td><code>[wshc_registration_form]</code></td><td>Displays the registration form.</td></tr>';
            echo '<tr><td><code>[wshc_forgot_password_form]</code></td><td>Displays the forgot password / password reset form.</td></tr>';
            echo '<tr><td><code>[wshc_scientific_engine]</code></td><td>Displays the Scientific Research Engine & Repository.</td></tr>';
            echo '<tr><td><code>[wshc_members_directory]</code></td><td>Displays the Official Members Directory.</td></tr>';
            echo '<tr><td><code>[wshc_verification]</code></td><td>Displays the Verification Portal.</td></tr>';
            echo '<tr><td><code>[wshc_contact_us]</code></td><td>Displays the Contact Us form and details.</td></tr>';
            echo '<tr><td><code>[wshc_training_programs]</code></td><td>Displays published Training Programs.</td></tr>';
            echo '<tr><td><code>[wshc_about_us]</code></td><td>Displays the standard About Us layout.</td></tr>';
            echo '<tr><td><code>[wshc_faq]</code></td><td>Displays the FAQ accordion sections.</td></tr>';
            echo '</tbody>';
            echo '</table>';
            echo '</div>';
        } else {
            echo '<div style="padding:20px; background:#fff; border-radius:8px; border:1px solid #ddd;">';
            echo '<h3 style="margin-top:0;">' . esc_html(ucwords(str_replace('-', ' ', $module))) . ' Hub</h3>';
            echo '<p>Manage settings, configurations, and records for ' . esc_html(ucwords(str_replace('-', ' ', $module))) . ' directly from this unified interface.</p>';
            echo '<div style="display:flex; gap:20px; margin-top:20px;">
                    <div style="background:#f9f9f9; padding:20px; flex:1; border:1px solid #eee; border-radius:8px; text-align:center;"><h2 style="margin:0; font-size:36px;">0</h2><p style="margin:5px 0 0; color:#666;">Total Records</p></div>
                    <div style="background:#f9f9f9; padding:20px; flex:1; border:1px solid #eee; border-radius:8px; text-align:center;"><h2 style="margin:0; font-size:36px; color:#4CAF50;">Active</h2><p style="margin:5px 0 0; color:#666;">System Status</p></div>
                  </div>';
            echo '</div>';
        }

        $html = ob_get_clean();
        wp_send_json_success(['html' => $html]);
    }

    /**
     * Handle log reversion AJAX.
     */
    public function handle_revert_log() {
        check_ajax_referer('wshc_dashboard_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Permission denied.']);
        }

        $log_id = intval($_POST['log_id']);
        if (\WSHC\UserManagement\ActivityLogger::revert_action($log_id)) {
            wp_send_json_success(['message' => 'Action successfully rolled back.']);
        } else {
            wp_send_json_error(['message' => 'Failed to revert action.']);
        }
    }

    /**
     * Enqueue dashboard assets.
     */
    public function enqueue_dashboard_assets() {
        if (is_page('id')) {
            wp_enqueue_style('dashicons');
            wp_enqueue_style('wshc-style', WSHC_PLUGIN_URL . 'assets/css/style.css', [], '1.0.0');
            wp_enqueue_style('wshc-dashboard-style', WSHC_PLUGIN_URL . 'assets/css/dashboard.css', [], '1.0.0');

            // Research Assets
            wp_enqueue_style('wshc-research-style', WSHC_PLUGIN_URL . 'assets/css/research.css', [], '1.1.0');
            wp_enqueue_script('wshc-research-js', WSHC_PLUGIN_URL . 'assets/js/research.js', ['jquery'], '1.1.0', true);

            wp_enqueue_script('wshc-dashboard-js', WSHC_PLUGIN_URL . 'assets/js/dashboard.js', ['jquery'], '1.0.0', true);
            wp_localize_script('wshc-dashboard-js', 'wshc_dashboard_obj', [
                'ajaxurl'         => admin_url('admin-ajax.php'),
                'nonce'           => wp_create_nonce('wshc_dashboard_nonce'),
                'current_user_id' => get_current_user_id(),
                'logout_url'      => wp_logout_url(home_url('/login')),
            ]);
        }
    }

    /**
     * Render the dashboard layout.
     */
    public function render_dashboard() {
        if (!is_user_logged_in()) {
            wp_redirect(home_url('/wshc-login'));
            exit;
        }

        if (!current_user_can('read')) {
            wp_die('Access denied.');
        }

        $current_section = isset($_GET['section']) ? sanitize_text_field($_GET['section']) : 'dashboard-overview';
        $stats = $this->get_dashboard_stats();

        ob_start();
        $this->load_template('dashboard/layout', [
            'stats'           => $stats,
            'current_section' => $current_section
        ]);
        return ob_get_clean();
    }

    /**
     * Get statistics for the dashboard.
     */
    private function get_dashboard_stats() {
        $user_count = count_users();

        $suspended_query = new \WP_User_Query([
            'meta_key'   => 'wshc_suspended',
            'meta_value' => '1',
            'count_total' => true
        ]);
        $suspended_count = $suspended_query->get_total();

        global $wpdb;
        $table = $wpdb->prefix . 'wshc_membership_applications';
        $institutional_members = $wpdb->get_var("SELECT COUNT(*) FROM $table WHERE status = 'approved'");

        return [
            'total_users'           => $user_count['total_users'],
            'suspended_users'       => $suspended_count,
            'active_users'          => $user_count['total_users'] - $suspended_count,
            'institutional_members' => $institutional_members,
            'recent_logs'           => \WSHC\UserManagement\ActivityLogger::get_logs(null, 10),
        ];
    }

    /**
     * Load a dashboard template.
     */
    public function load_template($name, $args = []) {
        extract($args);
        $path = WSHC_PLUGIN_DIR . 'templates/' . $name . '.php';
        if (file_exists($path)) {
            include $path;
        }
    }
}
