<div class="wshc-public-directory-portal" style="max-width: 1200px; margin: 40px auto; padding: 0 20px; font-family: 'Inter', sans-serif;">

    <!-- Directory Header -->
    <div class="directory-header" style="margin-bottom: 40px; display: flex; justify-content: space-between; align-items: flex-start;">
        <div>
            <h1 style="font-family: 'Space Grotesk', sans-serif; font-size: 32px; font-weight: 700; color: #000; margin-bottom: 10px;">GLOBAL ENCYCLOPEDIA OF ACADEMIES</h1>
            <p style="color: #888; font-size: 16px;">An authoritative indexed archive of certified medical schools, sports science institutes, and research clinics.</p>
        </div>
        <button style="background: #000; color: #fff; border: none; padding: 12px 24px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; letter-spacing: 1px; display: flex; align-items: center; gap: 8px;">
            <span class="dashicons dashicons-plus" style="font-size: 16px; width: 16px; height: 16px;"></span> SUGGEST ACADEMY
        </button>
    </div>

    <!-- Advanced Filter Bar -->
    <div class="directory-filter-bar" style="display: flex; gap: 20px; margin-bottom: 25px; border-top: 1px solid #eaeaea; border-bottom: 1px solid #eaeaea; padding: 25px 0;">
        <div class="search-input-wrapper" style="flex: 2; position: relative; border: none; padding: 0; background: transparent; border-radius: 0;">
            <span class="dashicons dashicons-search" style="position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #aaa;"></span>
            <input type="text" id="wshc-directory-search" placeholder="Search name, country or specialty..." autocomplete="off" style="width: 100%; box-sizing: border-box; padding: 12px 15px 12px 45px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; outline: none;">
        </div>
        <select id="wshc-specialty-filter" style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #555; background: #fff; outline: none; appearance: none;">
            <option value="">All Institution Types</option>
            <option value="Medical School">Medical School</option>
            <option value="Research Institute">Research Institute</option>
            <option value="Sports Science Center">Sports Science Center</option>
            <option value="Rehabilitation Clinic">Rehabilitation Clinic</option>
        </select>
        <select id="wshc-country-filter" style="flex: 1; padding: 12px 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 14px; color: #555; background: #fff; outline: none; appearance: none;">
            <option value="">All Countries</option>
            <?php echo \WSHC\Utils\CountryPicker::render_options(); ?>
        </select>
    </div>


    <!-- Grid Container -->
    <div id="wshc-institutions-registry" class="members-grid-container" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 25px;">
        <?php
        $institutions = [
            [
                'name' => 'Harvard Medical School',
                'type' => 'MEDICAL SCHOOL',
                'location' => 'Boston, United States',
                'description' => 'Harvard Medical School is the graduate medical school of Harvard University, renowned worldwide for premium education, scientific breakthroughs, and affiliated clinical...',
                'papers' => 420
            ],
            [
                'name' => 'Sorbonne University Faculty of Medicine',
                'type' => 'MEDICAL SCHOOL',
                'location' => 'Paris, France',
                'description' => 'One of the premier medical faculties in Europe, integrating fundamental bio-medical research with clinical practice alongside teaching hospitals like Pitié-Salpêtrière.',
                'papers' => 280
            ],
            [
                'name' => 'Kyoto University School of Medicine',
                'type' => 'RESEARCH INSTITUTE',
                'location' => 'Kyoto, Japan',
                'description' => 'A world-class research hub leading stem cell discoveries, cellular physiology, and surgical rehabilitation. Pioneer of induced pluripotent stem (iPS) cells.',
                'papers' => 310
            ],
            [
                'name' => 'Cairo University Faculty of Medicine',
                'type' => 'MEDICAL SCHOOL',
                'location' => 'Cairo, Egypt',
                'description' => 'The historical cornerstone of medical practice and clinical training in the Nile valley, recognized as the oldest and largest medical school in North Africa.',
                'papers' => 150
            ],
        ];

        foreach ($institutions as $inst) :
        ?>
            <div class="institution-card" style="border: 1px solid #eaeaea; border-radius: 12px; padding: 25px; background: #fff; display: flex; flex-direction: column; transition: box-shadow 0.2s;">

                <div class="card-meta-top" style="display: flex; justify-content: space-between; margin-bottom: 20px; font-size: 10px; font-family: monospace; color: #888; text-transform: uppercase;">
                    <span style="background: #f5f5f5; padding: 4px 8px; border-radius: 4px; color: #555; font-weight: 600;"><?php echo esc_html($inst['type']); ?></span>
                    <span style="display: flex; align-items: center; gap: 4px;"><span class="dashicons dashicons-location-alt" style="font-size: 12px; width: 12px; height: 12px;"></span> <?php echo esc_html($inst['location']); ?></span>
                </div>

                <div class="card-identity" style="margin-bottom: 15px;">
                    <h2 style="margin: 0; font-size: 18px; font-weight: 700; color: #000;"><?php echo esc_html($inst['name']); ?></h2>
                </div>

                <div class="card-bio" style="font-size: 13px; color: #777; line-height: 1.6; margin-bottom: 25px; flex-grow: 1;">
                    <?php echo esc_html($inst['description']); ?>
                </div>

                <div class="card-footer" style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid #eaeaea; padding-top: 15px;">
                    <span style="font-family: monospace; font-size: 11px; color: #aaa;"><span class="dashicons dashicons-book-alt" style="font-size: 14px; width: 14px; height: 14px;"></span> <?php echo $inst['papers']; ?> papers</span>
                    <a href="#" style="font-size: 10px; font-weight: 800; color: #000; text-decoration: none; letter-spacing: 0.5px;">READ ENTRY <span class="dashicons dashicons-arrow-up-alt2" style="font-size: 10px; width: 10px; height: 10px; transform: rotate(45deg);"></span></a>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
