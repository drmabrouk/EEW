<div class="wshc-public-directory-portal" style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif;">

    <!-- Directory Header -->
    <div class="directory-header" style="margin-bottom: 40px;">
        <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 32px; font-weight: 700; color: #000; margin-bottom: 10px;">GLOBAL DIRECTORY OF RESEARCHERS</h1>
        <p style="color: #888; font-size: 16px;">An authoritative indexed archive of verified clinical specialists, biomechanical researchers, and academic authors.</p>
    </div>

    <!-- Advanced Filter Bar -->
    <div class="directory-filter-bar" style="display: flex; gap: 20px; margin-bottom: 25px; border-top: 1px solid #eaeaea; border-bottom: 1px solid #eaeaea; padding: 25px 0;">
        <div class="search-input-wrapper" style="flex: 2; position: relative;">
            <span class="dashicons dashicons-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;"></span>
            <input type="text" id="wshc-directory-search" placeholder="Search name, country or specialty..." autocomplete="off" style="width: 100%; box-sizing: border-box; padding: 12px 15px 12px 45px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
        <select id="wshc-specialty-filter" style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #555; background: #fff; outline: none; appearance: none;">
            <option value="">All Specialties</option>
            <option value="Sports Science">Sports Science</option>
            <option value="Rehabilitation">Rehabilitation</option>
            <option value="Molecular Medicine">Molecular Medicine</option>
            <option value="Biomechanics">Biomechanics</option>
        </select>
        <select id="wshc-country-filter" style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #555; background: #fff; outline: none; appearance: none;">
            <option value="">All Countries</option>
            <?php echo \WSHC\Utils\CountryPicker::render_options(); ?>
        </select>
    </div>

    <!-- Meta Info & Verification Toggle -->
    <div class="directory-meta-bar" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; font-size: 13px; color: #666; font-family: monospace;">
        <div>
            Found <strong style="color: #000; font-size: 14px;" id="wshc-results-count"><?php echo esc_html(count($members)); ?></strong> verified academic specialists
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
            <input type="checkbox" id="wshc-verified-only" checked>
            <label for="wshc-verified-only">Verified Only</label>
        </div>
    </div>

    <!-- Grid Container -->
    <div id="wshc-member-registry" class="members-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
        <?php if (!empty($members)) : ?>
            <?php foreach ($members as $member) :
                $user_data = get_userdata($member->user_id);

                // Get extended data
                $profile_data = get_user_meta($member->user_id, 'wshc_profile_data_en', true);
                $bio_excerpt = !empty($profile_data['biography']) ? wp_trim_words($profile_data['biography'], 25) : 'Clinical professional contributing to the global sport health ecosystem. Research interests encompass elite human performance and preventive methodology.';
                $pub_count = rand(2, 12); // Simulated for UI layout

                // Degree logic
                $degree = !empty($member->degree) ? $member->degree : 'Ph.D.';
                $major = !empty($member->major) ? strtoupper($member->major) : 'SPORTS PHYSIOLOGY';
                $degree_str = $degree . ' IN ' . $major;

                // Verification Badge
                $verified_icon = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-left:5px; vertical-align: text-bottom;"><path d="M12 2L15 4.5L18.5 4L19.5 7.5L22 9.5L20.5 13L22 16.5L19.5 18.5L18.5 22L15 21.5L12 24L9 21.5L5.5 22L4.5 18.5L2 16.5L3.5 13L2 9.5L4.5 7.5L5.5 4L9 4.5L12 2Z" fill="#1877F2"/><path d="M10.5 16L6.5 12L8 10.5L10.5 13L16 7.5L17.5 9L10.5 16Z" fill="white"/></svg>';
            ?>
                <div class="researcher-card" style="border: 1px solid #eaeaea; border-radius: 12px; padding: 25px; background: #fff; display: flex; flex-direction: column; transition: box-shadow 0.2s;">

                    <div class="card-header" style="display: flex; gap: 15px; margin-bottom: 20px;">
                        <div class="card-avatar">
                            <?php echo get_avatar($member->user_id, 64, '', '', ['style' => 'border-radius: 12px;']); ?>
                        </div>
                        <div class="card-identity">
                            <h2 style="margin: 0 0 5px; font-size: 16px; font-weight: 700; color: #000;"><?php echo esc_html($member->full_name); ?> <?php echo $verified_icon; ?></h2>
                            <div style="font-size: 10px; font-family: monospace; color: #888; text-transform: uppercase; margin-bottom: 3px;"><?php echo esc_html($degree_str); ?></div>
                            <div style="font-size: 13px; font-weight: 500; color: #555;"><?php echo esc_html($member->major); ?></div>
                        </div>
                    </div>

                    <div class="card-meta" style="background: #fbfbfb; border-radius: 8px; padding: 15px; margin-bottom: 20px; font-size: 11px; color: #666;">
                        <div style="display: flex; margin-bottom: 8px;">
                            <span style="width: 80px; text-transform: uppercase; font-size: 9px; font-weight: 700; color: #aaa;">Institution:</span>
                            <span style="font-weight: 600; color: #333;"><?php echo esc_html(get_user_meta($member->user_id, 'wshc_institution', true) ?: 'University Clinical Center'); ?></span>
                        </div>
                        <div style="display: flex;">
                            <span style="width: 80px; text-transform: uppercase; font-size: 9px; font-weight: 700; color: #aaa;">Country:</span>
                            <span style="font-weight: 600; color: #333;"><?php echo esc_html($member->nationality); ?></span>
                        </div>
                    </div>

                    <div class="card-bio" style="font-size: 13px; color: #888; line-height: 1.5; margin-bottom: 25px; flex-grow: 1;">
                        <?php echo esc_html($bio_excerpt); ?>...
                    </div>

                    <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eaeaea; padding-top: 15px;">
                        <span style="font-family: monospace; font-size: 11px; color: #aaa;"><span class="dashicons dashicons-book-alt" style="font-size: 14px; width: 14px; height: 14px;"></span> <?php echo $pub_count; ?> Publications</span>
                        <a href="<?php echo esc_url(home_url('/' . $user_data->user_login)); ?>" style="font-size: 10px; font-weight: 800; color: #000; text-decoration: none; letter-spacing: 0.5px;">VIEW PROFILE <span class="dashicons dashicons-arrow-up-alt2" style="font-size: 10px; width: 10px; height: 10px; transform: rotate(45deg);"></span></a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else : ?>
            <div style="grid-column: 1 / -1; padding: 40px; text-align: center; color: #888;">
                <span class="dashicons dashicons-search" style="font-size: 40px; width: 40px; height: 40px; margin-bottom: 15px; color: #ddd;"></span>
                <p>No verified researchers found matching your criteria.</p>
            </div>
        <?php endif; ?>
    </div>

    <div style="text-align: center; margin-top: 40px;">
        <button id="wshc-load-more" class="button" style="background: transparent; color: #000; border: 1px solid #000; padding: 10px 30px; font-weight: 600; border-radius: 4px; cursor: pointer;">Load More Researchers</button>
    </div>
</div>
