<?php
/**
 * Member Profile Template
 */
global $wshc_profile_user;
$user = $wshc_profile_user;

if (!$user) {
    status_header(404);
    echo 'Profile not found.';
    die();
}

global $wpdb;
$table_apps = $wpdb->prefix . 'wshc_membership_applications';
$app = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_apps WHERE user_id = %d AND status = 'approved' ORDER BY created_at DESC LIMIT 1", $user->ID));

$full_name = $app ? $app->full_name : $user->display_name;
$degree = $app ? $app->degree : '';

// Process title
if ($degree && (stripos($degree, 'PhD') !== false || stripos($degree, 'Doctor') !== false || stripos($degree, 'MD') !== false)) {
    if (stripos($full_name, 'Dr.') === false) {
        $full_name = 'Dr. ' . $full_name;
    }
}

$major = $app ? $app->major : (get_user_meta($user->ID, 'wshc_specialization', true) ?: 'General Council');
$nationality = $app ? $app->nationality : (get_user_meta($user->ID, 'wshc_nationality', true) ?: 'Global');
$membership_id = get_user_meta($user->ID, 'wshc_membership_id', true) ?: 'LGC-'.(5000 + $user->ID);

$role_names = [
    'wshc_member'               => 'Official Member',
    'wshc_research_member'      => 'Research Member',
    'wshc_practitioner_member'  => 'Practitioner Member',
    'wshc_fellowship_member'    => 'Fellowship Member',
    'wshc_scientific_reviewer'  => 'Scientific Reviewer',
    'wshc_programs_manager'     => 'Programs Manager',
    'wshc_regional_coordinator' => 'Regional Coordinator',
    'wshc_secretary_general'    => 'Secretary-General',
];
$primary_role = !empty($user->roles) ? $user->roles[0] : '';
$category = isset($role_names[$primary_role]) ? $role_names[$primary_role] : 'Council Member';

// Handle missing class in frontend theme environments gracefully
if (class_exists('\WSHC\Utils\CountryPicker')) {
    $flag_emoji = \WSHC\Utils\CountryPicker::get_flag($nationality);
} else {
    $flag_emoji = '';
}

// Social Sharing Logic
$current_url = home_url('/' . $user->user_login);
$share_text = urlencode('View ' . $full_name . '\'s profile on the Official Council Roster.');

get_header();
?>

<div class="wshc-profile-container" style="max-width: 800px; margin: 40px auto; font-family: sans-serif;">
    <div class="profile-header" style="display: flex; align-items: center; border-bottom: 2px solid #eaeaea; padding-bottom: 20px; margin-bottom: 20px;">
        <div class="profile-avatar" style="margin-right: 20px;">
            <?php echo get_avatar($user->ID, 120); ?>
        </div>
        <div class="profile-title">
            <h1 style="margin: 0; font-size: 28px;"><?php echo esc_html($full_name); ?></h1>
            <p style="margin: 5px 0; color: #666; font-size: 16px;"><?php echo esc_html($category); ?> &bull; #<?php echo esc_html($membership_id); ?></p>
        </div>
    </div>

    <div class="profile-details">
        <p><strong>Specialization:</strong> <?php echo esc_html($major); ?></p>
        <p><strong>Nationality:</strong> <?php echo esc_html($flag_emoji) . ' ' . esc_html($nationality); ?></p>
        <?php if ($degree): ?>
            <p><strong>Degree:</strong> <?php echo esc_html($degree); ?></p>
        <?php endif; ?>
        <?php if ($app && $app->institution): ?>
            <p><strong>Institution:</strong> <?php echo esc_html($app->institution); ?></p>
        <?php endif; ?>
        <?php if ($app && $app->job_title): ?>
            <p><strong>Job Title:</strong> <?php echo esc_html($app->job_title); ?></p>
        <?php endif; ?>
    </div>

    <div class="profile-share" style="margin-top: 30px;">
        <h3 style="margin-bottom: 10px;">Share Profile</h3>
        <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url($current_url); ?>&text=<?php echo esc_attr($share_text); ?>" target="_blank" style="padding: 10px 15px; background: #1DA1F2; color: white; text-decoration: none; border-radius: 5px; margin-right: 10px;">Share on Twitter</a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_url($current_url); ?>&title=<?php echo esc_attr($share_text); ?>" target="_blank" style="padding: 10px 15px; background: #0077B5; color: white; text-decoration: none; border-radius: 5px;">Share on LinkedIn</a>
    </div>
</div>

<?php
get_footer();
