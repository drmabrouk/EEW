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
            echo '<div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
            echo '<h2 style="margin:0;">Site Pages Ledger</h2>';
            echo '<button class="button restore-pages-btn" style="background:#000; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;"><span class="dashicons dashicons-update"></span> Auto-Restore System Pages</button>';
            echo '</div>';

            echo '<div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">';
            echo '<table class="wp-list-table widefat striped" style="border:none; margin:0;">';
            echo '<thead><tr><th style="padding:15px;">Page Title</th><th style="padding:15px;">Status</th><th style="padding:15px; text-align:right;">Actions Controls</th></tr></thead><tbody>';
            foreach ($pages as $p) {
                $status_badge = ($p->post_status === 'publish') ? '<span style="background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Published</span>' : '<span style="background:#fff3e0; color:#f57c00; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Draft</span>';
                echo '<tr>';
                echo '<td style="padding:15px; font-weight:bold;">' . esc_html($p->post_title) . '</td>';
                echo '<td style="padding:15px;">' . $status_badge . '</td>';
                echo '<td style="padding:15px; text-align:right;">';
                if ($p->post_status !== 'publish') {
                    echo '<button class="button page-action-btn" data-id="'.$p->ID.'" data-action="publish" title="Publish"><span class="dashicons dashicons-yes"></span></button> ';
                } else {
                    echo '<button class="button page-action-btn" data-id="'.$p->ID.'" data-action="unpublish" title="Draft"><span class="dashicons dashicons-hidden"></span></button> ';
                }
                echo '<button class="button page-action-btn" data-id="'.$p->ID.'" data-action="duplicate" title="Duplicate"><span class="dashicons dashicons-admin-page"></span></button> ';
                echo '<button class="button page-action-btn" data-id="'.$p->ID.'" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
                echo '</td></tr>';
            }
            echo '</tbody></table></div>';

        } elseif ($module === 'programs' || $module === 'faq') {
            $type = ($module === 'programs') ? 'wshc_program' : 'wshc_faq';
            $title = ($module === 'programs') ? 'Training Programs' : 'FAQ Entries';
            $posts = get_posts(['post_type' => $type, 'post_status' => ['publish', 'draft'], 'posts_per_page' => -1]);

            echo '<div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
            echo '<h2 style="margin:0;">' . esc_html($title) . ' Data Ledger</h2>';
            echo '</div>';

            echo '<div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">';
            echo '<table class="wp-list-table widefat striped" style="border:none; margin:0;">';
            echo '<thead><tr><th style="padding:15px;">Title</th><th style="padding:15px;">Visibility</th><th style="padding:15px; text-align:right;">Registry Actions</th></tr></thead><tbody>';
            foreach ($posts as $p) {
                $status_badge = ($p->post_status === 'publish') ? '<span style="background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Active</span>' : '<span style="background:#fff3e0; color:#f57c00; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Archived</span>';
                echo '<tr>';
                echo '<td style="padding:15px; font-weight:bold;">' . esc_html($p->post_title) . '</td>';
                echo '<td style="padding:15px;">' . $status_badge . '</td>';
                echo '<td style="padding:15px; text-align:right;">';
                if ($p->post_status !== 'publish') {
                    echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="publish" title="Publish"><span class="dashicons dashicons-yes-alt"></span></button> ';
                } else {
                    echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="unpublish" title="Archive"><span class="dashicons dashicons-archive"></span></button> ';
                }
                echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="delete" style="color:#d32f2f;" title="Purge"><span class="dashicons dashicons-trash"></span></button>';
                echo '</td></tr>';
            }
            if (empty($posts)) echo '<tr><td colspan="3" style="padding:30px; text-align:center; color:#888;">Registry is currently empty.</td></tr>';
            echo '</tbody></table></div>';

        } elseif ($module === 'messages') {
            global $wpdb;
            $table = $wpdb->prefix . 'wshc_contact_messages';
            $messages = $wpdb->get_results("SELECT * FROM $table ORDER BY created_at DESC");

            echo '<div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
            echo '<h2 style="margin:0;">Support Communications Inbox</h2>';
            echo '</div>';

            echo '<div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">';
            echo '<table class="wp-list-table widefat striped" style="border:none; margin:0;">';
            echo '<thead><tr><th style="padding:15px;">Sender</th><th style="padding:15px;">Subject</th><th style="padding:15px;">Status</th><th style="padding:15px;">Timestamp</th><th style="padding:15px; text-align:right;">Actions</th></tr></thead><tbody>';
            foreach ($messages as $msg) {
                $status_badge = ($msg->status === 'unread') ? '<span style="background:#e3f2fd; color:#1565c0; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Unread</span>' : '<span style="background:#f5f5f5; color:#757575; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Read</span>';
                echo '<tr style="'.($msg->status === 'unread' ? 'background:#fbfdff;' : '').'">';
                echo '<td style="padding:15px;"><strong>' . esc_html($msg->full_name) . '</strong><br><small style="color:#666;">' . esc_html($msg->email) . '</small></td>';
                echo '<td style="padding:15px;">' . esc_html($msg->subject) . '</td>';
                echo '<td style="padding:15px;">' . $status_badge . '</td>';
                echo '<td style="padding:15px;">' . esc_html($msg->created_at) . '</td>';
                echo '<td style="padding:15px; text-align:right;">';
                if ($msg->status === 'unread') {
                    echo '<button class="button item-action-btn" data-module="messages" data-id="'.$msg->id.'" data-action="read" title="Mark Read"><span class="dashicons dashicons-yes"></span></button> ';
                }
                echo '<button class="button item-action-btn" data-module="messages" data-id="'.$msg->id.'" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
                echo '</td></tr>';
            }
            if (empty($messages)) echo '<tr><td colspan="5" style="padding:30px; text-align:center; color:#888;">Inbox is currently clear.</td></tr>';
            echo '</tbody></table></div>';
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
            // Dynamic resolution for the newly registered 33 modules
            $module_key = 'wshc_' . str_replace('-', '_', $module);
            $module_mgr = new \WSHC\Core\ModuleManager();
            $modules = $module_mgr->get_modules();

            if (array_key_exists($module_key, $modules)) {
                $title = $modules[$module_key]['name'];
                $posts = get_posts(['post_type' => $module_key, 'post_status' => ['publish', 'draft'], 'posts_per_page' => -1]);

                echo '<div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">';
                echo '<h2 style="margin:0;">' . esc_html($title) . ' Management</h2>';
                echo '<button class="button" style="background:#000; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;"><span class="dashicons dashicons-plus"></span> Add New Record</button>';
                echo '</div>';

                echo '<div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">';
                echo '<table class="wp-list-table widefat striped" style="border:none; margin:0;">';
                echo '<thead><tr><th style="padding:15px;">Title</th><th style="padding:15px;">Status</th><th style="padding:15px; text-align:right;">Actions</th></tr></thead><tbody>';
                foreach ($posts as $p) {
                    $status_badge = ($p->post_status === 'publish') ? '<span style="background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Active</span>' : '<span style="background:#fff3e0; color:#f57c00; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Archived</span>';
                    echo '<tr>';
                    echo '<td style="padding:15px; font-weight:bold;">' . esc_html($p->post_title) . '</td>';
                    echo '<td style="padding:15px;">' . $status_badge . '</td>';
                    echo '<td style="padding:15px; text-align:right;">';
                    if ($p->post_status !== 'publish') {
                        echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="publish" title="Publish"><span class="dashicons dashicons-yes-alt"></span></button> ';
                    } else {
                        echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="unpublish" title="Archive"><span class="dashicons dashicons-archive"></span></button> ';
                    }
                    echo '<button class="button item-action-btn" data-module="'.$module.'" data-id="'.$p->ID.'" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>';
                    echo '</td></tr>';
                }
                if (empty($posts)) echo '<tr><td colspan="3" style="padding:30px; text-align:center; color:#888;">The ' . esc_html($title) . ' registry is currently empty. Click "Add New Record" to begin.</td></tr>';
                echo '</tbody></table></div>';
            } else {
                // Failsafe for entirely unknown routes
                echo '<div class="notice notice-error"><p>Module handler not found for route: ' . esc_html($module) . '</p></div>';
            }
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
