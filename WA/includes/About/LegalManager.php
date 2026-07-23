<?php

namespace WSHC\About;

class LegalManager {
    public function init() {
        add_shortcode('wshc_legal_policies', [$this, 'render_legal_page']);
    }

    public function render_legal_page() {
        ob_start();
        ?>
        <div class="wshc-about-container" style="max-width: 1000px; margin: 40px auto; font-family: sans-serif; line-height: 1.6;">
            <div class="about-header" style="text-align: center; padding: 40px 0; border-bottom: 2px solid #eaeaea; margin-bottom: 30px;">
                <h1 style="font-size: 36px; margin-bottom: 10px;">Legal & Policies</h1>
                <p style="font-size: 18px; color: #555;">Terms, Conditions, and Privacy Guidelines of the WSHC.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Privacy Policy</h2>
                <p>We are committed to protecting your personal information. Our privacy practices comply with global data protection regulations, ensuring that member data, research submissions, and institutional records are securely stored and strictly utilized for professional council operations.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Terms & Conditions</h2>
                <p>By accessing the Healthedia global archive or utilizing WSHC services, you agree to abide by our professional code of conduct. Unauthorized distribution of restricted research, falsification of credentials, or misuse of the directory is strictly prohibited.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Publication Policies</h2>
                <p>All research submitted to the archive undergoes rigorous peer review. Authors retain copyright but grant the WSHC a non-exclusive license to publish and archive the material globally. Plagiarism or data manipulation will result in immediate expulsion from the council.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
