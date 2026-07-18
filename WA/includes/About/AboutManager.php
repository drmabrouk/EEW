<?php

namespace WSHC\About;

class AboutManager {
    public function init() {
        add_shortcode('wshc_about_us', [$this, 'render_about_page']);
    }

    public function render_about_page() {
        ob_start();
        ?>
        <div class="wshc-about-container" style="max-width: 1000px; margin: 0 auto; font-family: sans-serif; line-height: 1.6;">
            <div class="about-header" style="text-align: center; padding: 40px 0; border-bottom: 2px solid #eaeaea; margin-bottom: 30px;">
                <h1 style="font-size: 36px; margin-bottom: 10px;">About the World Sports Health Council</h1>
                <p style="font-size: 18px; color: #555;">Upholding the highest professional and ethical standards in sport health globally.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Our Mission & Vision</h2>
                <p>The World Sports Health Council (WSHC) is dedicated to advancing the field of sport health through rigorous scientific research, global collaboration, and standardizing medical practices for athletes worldwide. We envision a future where every sports professional has access to optimized, scientifically backed health care and injury prevention strategies.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Core Values</h2>
                <ul>
                    <li><strong>Integrity:</strong> Committing to ethical research and practice.</li>
                    <li><strong>Excellence:</strong> Striving for the highest quality in training and credentials.</li>
                    <li><strong>Collaboration:</strong> Fostering a global network of sport health practitioners.</li>
                    <li><strong>Innovation:</strong> Supporting cutting-edge technological and medical advancements.</li>
                </ul>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>Governance & Structure</h2>
                <p>The WSHC is governed by an international board of distinguished medical professionals, researchers, and sports executives. Our organizational structure includes specialized committees for Scientific Review, Fellowship Programs, and Regional Coordination, ensuring comprehensive oversight and global reach.</p>
            </div>

            <div class="about-section" style="margin-bottom: 30px;">
                <h2>History</h2>
                <p>Founded to bridge the gap between sports science and clinical medical practice, the WSHC has grown into a premier regulatory and educational body. We continue to publish leading research and provide gold-standard certifications to practitioners across the globe.</p>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}
