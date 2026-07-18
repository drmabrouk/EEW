<?php
/**
 * Wikipedia-style Member Profile Template
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

// Multilingual Logic
$supported_languages = [
    'en' => 'English',
    'ar' => 'Arabic',
    'fr' => 'French',
    'de' => 'German'
];

$requested_lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : 'en';
if (!array_key_exists($requested_lang, $supported_languages)) {
    $requested_lang = 'en';
}

$profile_data = get_user_meta($user->ID, 'wshc_profile_data_' . $requested_lang, true);

// Fallback to English if empty
if (empty($profile_data['biography']) && $requested_lang !== 'en') {
    $profile_data = get_user_meta($user->ID, 'wshc_profile_data_en', true);
}

$bio = !empty($profile_data['biography']) ? wpautop($profile_data['biography']) : '<p>No biography provided.</p>';
$career = !empty($profile_data['career']) ? wpautop($profile_data['career']) : '';
$achievements = !empty($profile_data['achievements']) ? wpautop($profile_data['achievements']) : '';
$publications = !empty($profile_data['publications']) ? wpautop($profile_data['publications']) : '';

// Social Sharing Logic
$current_url = home_url('/' . $user->user_login);
$share_text = urlencode('View ' . $full_name . '\'s profile on the Official Council Roster.');

// Translation Labels
$labels = [
    'en' => ['bio' => 'Biography', 'career' => 'Career & Memberships', 'awards' => 'Awards & Qualifications', 'pubs' => 'Publications & Research'],
    'ar' => ['bio' => 'السيرة الذاتية', 'career' => 'المسيرة المهنية والعضويات', 'awards' => 'الجوائز والمؤهلات', 'pubs' => 'المنشورات والأبحاث'],
    'fr' => ['bio' => 'Biographie', 'career' => 'Carrière et Affiliations', 'awards' => 'Prix et Qualifications', 'pubs' => 'Publications et Recherches'],
    'de' => ['bio' => 'Biografie', 'career' => 'Karriere & Mitgliedschaften', 'awards' => 'Auszeichnungen & Qualifikationen', 'pubs' => 'Publikationen & Forschung'],
];
$l = $labels[$requested_lang];
$dir = ($requested_lang === 'ar') ? 'rtl' : 'ltr';

get_header();
?>

<div class="wshc-wiki-profile" style="max-width: 1000px; margin: 40px auto; font-family: sans-serif; display: flex; flex-wrap: wrap; gap: 30px; direction: <?php echo $dir; ?>;">

    <!-- Language Switcher -->
    <div style="width: 100%; display: flex; justify-content: flex-end; gap: 10px; margin-bottom: 20px;">
        <?php foreach ($supported_languages as $code => $name): ?>
            <a href="?lang=<?php echo esc_attr($code); ?>" style="padding: 5px 10px; background: <?php echo ($requested_lang === $code) ? '#0073aa' : '#eee'; ?>; color: <?php echo ($requested_lang === $code) ? '#fff' : '#333'; ?>; text-decoration: none; border-radius: 3px;">
                <?php echo esc_html($name); ?>
            </a>
        <?php endforeach; ?>
    </div>

    <!-- Main Content Area -->
    <div class="wiki-main-content" style="flex: 1; min-width: 60%;">
        <h1 style="font-size: 32px; border-bottom: 1px solid #a2a9b1; padding-bottom: 5px; margin-top: 0;"><?php echo esc_html($full_name); ?></h1>

        <div class="wiki-section" style="margin-top: 20px;">
            <h2 style="font-size: 22px; border-bottom: 1px solid #a2a9b1; padding-bottom: 5px;"><?php echo esc_html($l['bio']); ?></h2>
            <?php echo wp_kses_post($bio); ?>
        </div>

        <?php if ($career): ?>
        <div class="wiki-section" style="margin-top: 30px;">
            <h2 style="font-size: 22px; border-bottom: 1px solid #a2a9b1; padding-bottom: 5px;"><?php echo esc_html($l['career']); ?></h2>
            <?php echo wp_kses_post($career); ?>
        </div>
        <?php endif; ?>

        <?php if ($achievements): ?>
        <div class="wiki-section" style="margin-top: 30px;">
            <h2 style="font-size: 22px; border-bottom: 1px solid #a2a9b1; padding-bottom: 5px;"><?php echo esc_html($l['awards']); ?></h2>
            <?php echo wp_kses_post($achievements); ?>
        </div>
        <?php endif; ?>

        <?php if ($publications): ?>
        <div class="wiki-section" style="margin-top: 30px;">
            <h2 style="font-size: 22px; border-bottom: 1px solid #a2a9b1; padding-bottom: 5px;"><?php echo esc_html($l['pubs']); ?></h2>
            <?php echo wp_kses_post($publications); ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Infobox / Sidebar -->
    <div class="wiki-infobox" style="width: 300px; background: #f8f9fa; border: 1px solid #a2a9b1; padding: 15px; text-align: center; height: fit-content;">
        <div style="font-weight: bold; font-size: 18px; margin-bottom: 10px; background: #eaecf0; padding: 5px;"><?php echo esc_html($full_name); ?></div>
        <div style="margin-bottom: 15px;">
            <?php echo get_avatar($user->ID, 250); ?>
        </div>
        <table style="width: 100%; text-align: left; font-size: 14px; border-collapse: collapse;">
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">Category</th>
                <td style="padding: 5px;"><?php echo esc_html($category); ?></td>
            </tr>
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">ID</th>
                <td style="padding: 5px;">#<?php echo esc_html($membership_id); ?></td>
            </tr>
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">Specialty</th>
                <td style="padding: 5px;"><?php echo esc_html($major); ?></td>
            </tr>
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">Nationality</th>
                <td style="padding: 5px;"><?php echo esc_html($flag_emoji) . ' ' . esc_html($nationality); ?></td>
            </tr>
            <?php if ($degree): ?>
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">Degree</th>
                <td style="padding: 5px;"><?php echo esc_html($degree); ?></td>
            </tr>
            <?php endif; ?>
            <?php if ($app && $app->institution): ?>
            <tr style="border-top: 1px solid #eaecf0;">
                <th style="padding: 5px;">Institution</th>
                <td style="padding: 5px;"><?php echo esc_html($app->institution); ?></td>
            </tr>
            <?php endif; ?>
        </table>

        <div class="profile-share" style="margin-top: 20px; text-align: center;">
            <p style="font-weight: bold; margin-bottom: 10px;">Share</p>
            <a href="https://twitter.com/intent/tweet?url=<?php echo esc_url($current_url); ?>&text=<?php echo esc_attr($share_text); ?>" target="_blank" style="display: block; padding: 8px; background: #1DA1F2; color: white; text-decoration: none; border-radius: 3px; margin-bottom: 5px; font-size: 14px;">Twitter</a>
            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo esc_url($current_url); ?>&title=<?php echo esc_attr($share_text); ?>" target="_blank" style="display: block; padding: 8px; background: #0077B5; color: white; text-decoration: none; border-radius: 3px; font-size: 14px;">LinkedIn</a>
        </div>
    </div>
</div>

<style>
    @media (max-width: 768px) {
        .wshc-wiki-profile {
            flex-direction: column;
        }
        .wiki-infobox {
            width: 100%;
            order: -1;
        }
    }
</style>

<?php
get_footer();
