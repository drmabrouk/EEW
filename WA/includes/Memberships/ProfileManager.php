<?php

namespace WSHC\Memberships;

class ProfileManager {
    public function init() {
        add_action('init', [$this, 'add_rewrite_rules']);
        add_filter('query_vars', [$this, 'add_query_vars']);
        add_filter('template_include', [$this, 'template_redirect'], 99);
    }

    public function add_rewrite_rules() {
        // Rewrite rule for member profiles: domain.com/username
        // We match any non-reserved path.
        // It's safer to put this at the bottom or top? Let's use 'bottom' so it doesn't break standard pages.
        add_rewrite_rule('^([^/]+)/?$', 'index.php?wshc_member_profile=$matches[1]', 'bottom');
    }

    public function add_query_vars($vars) {
        $vars[] = 'wshc_member_profile';
        return $vars;
    }

    public function template_redirect($template) {
        $username = get_query_var('wshc_member_profile');
        if (!empty($username)) {
            // Exclude common page slugs and default WP structures to prevent conflicts
            $reserved_slugs = ['login', 'id', 'verify', 'directory', 'research', 'wp-admin', 'wp-login.php'];
            if (in_array($username, $reserved_slugs)) {
                return $template;
            }

            // Check if user exists and is a member
            $user = get_user_by('login', $username);
            if ($user) {
                // Verify user is a member tier and not suspended
                if (get_user_meta($user->ID, 'wshc_suspended', true)) {
                    return $template; // Or show suspended notice
                }

                $member_roles = ['wshc_member', 'wshc_research_member', 'wshc_practitioner_member', 'wshc_fellowship_member', 'wshc_scientific_reviewer', 'wshc_programs_manager', 'wshc_regional_coordinator', 'wshc_secretary_general'];

                $is_member = false;
                foreach ($user->roles as $role) {
                    if (in_array($role, $member_roles)) {
                        $is_member = true;
                        break;
                    }
                }

                if ($is_member) {
                    // Set global variables for the template
                    global $wshc_profile_user;
                    $wshc_profile_user = $user;

                    // Locate and load custom profile template
                    $profile_template = WSHC_PLUGIN_DIR . 'templates/portal/member-profile.php';
                    if (file_exists($profile_template)) {
                        global $wp_query;
                        $wp_query->is_404 = false;
                        status_header(200);
                        return $profile_template;
                    }
                }
            }
        }
        return $template;
    }
}
