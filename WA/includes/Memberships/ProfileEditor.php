<?php

namespace WSHC\Memberships;

class ProfileEditor {
    private $languages = [
        'en' => 'English',
        'ar' => 'Arabic',
        'fr' => 'French',
        'de' => 'German'
    ];

    public function init() {
        add_action('show_user_profile', [$this, 'render_profile_editor']);
        add_action('edit_user_profile', [$this, 'render_profile_editor']);
        add_action('personal_options_update', [$this, 'save_profile_data']);
        add_action('edit_user_profile_update', [$this, 'save_profile_data']);
    }

    public function render_profile_editor($user) {
        if (!current_user_can('edit_user', $user->ID)) {
            return;
        }

        echo '<h3>WSHC Multilingual Public Profile (Wikipedia Style)</h3>';
        echo '<p>Fill out the biographical and professional details below. You can save different versions for each supported language. If a language is left blank, the English version will be used as a fallback on the public profile.</p>';

        echo '<table class="form-table wshc-profile-editor-table">';
        foreach ($this->languages as $lang_code => $lang_name) {
            $data = get_user_meta($user->ID, 'wshc_profile_data_' . $lang_code, true) ?: [];

            $bio = $data['biography'] ?? '';
            $career = $data['career'] ?? '';
            $achievements = $data['achievements'] ?? '';
            $publications = $data['publications'] ?? '';

            echo '<tr><th colspan="2" style="background:#f0f0f1; padding:10px;"><strong>' . esc_html($lang_name) . ' Content</strong></th></tr>';

            echo '<tr>';
            echo '<th><label for="wshc_bio_' . $lang_code . '">Biography</label></th>';
            echo '<td><textarea name="wshc_profile[' . $lang_code . '][biography]" id="wshc_bio_' . $lang_code . '" rows="5" cols="50">' . esc_textarea($bio) . '</textarea></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th><label for="wshc_career_' . $lang_code . '">Career & Memberships</label></th>';
            echo '<td><textarea name="wshc_profile[' . $lang_code . '][career]" id="wshc_career_' . $lang_code . '" rows="5" cols="50">' . esc_textarea($career) . '</textarea></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th><label for="wshc_achievements_' . $lang_code . '">Awards & Qualifications</label></th>';
            echo '<td><textarea name="wshc_profile[' . $lang_code . '][achievements]" id="wshc_achievements_' . $lang_code . '" rows="5" cols="50">' . esc_textarea($achievements) . '</textarea></td>';
            echo '</tr>';

            echo '<tr>';
            echo '<th><label for="wshc_pubs_' . $lang_code . '">Publications & Research Interests</label></th>';
            echo '<td><textarea name="wshc_profile[' . $lang_code . '][publications]" id="wshc_pubs_' . $lang_code . '" rows="5" cols="50">' . esc_textarea($publications) . '</textarea></td>';
            echo '</tr>';
        }
        echo '</table>';
    }

    public function save_profile_data($user_id) {
        if (!current_user_can('edit_user', $user_id)) {
            return false;
        }

        if (isset($_POST['wshc_profile']) && is_array($_POST['wshc_profile'])) {
            foreach ($this->languages as $lang_code => $lang_name) {
                if (isset($_POST['wshc_profile'][$lang_code])) {
                    $sanitized_data = [
                        'biography' => wp_kses_post($_POST['wshc_profile'][$lang_code]['biography']),
                        'career' => wp_kses_post($_POST['wshc_profile'][$lang_code]['career']),
                        'achievements' => wp_kses_post($_POST['wshc_profile'][$lang_code]['achievements']),
                        'publications' => wp_kses_post($_POST['wshc_profile'][$lang_code]['publications']),
                    ];
                    update_user_meta($user_id, 'wshc_profile_data_' . $lang_code, $sanitized_data);
                }
            }
        }
    }
}
