<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Space+Grotesk:wght@700&display=swap" rel="stylesheet">
    <?php wp_head(); ?>
</head>
<body <?php body_class('healthedia-app-body'); ?>>

<div class="healthedia-layout-wrapper">
    <?php include WSHC_PLUGIN_DIR . 'templates/global/header.php'; ?>

    <main class="healthedia-main-content">
        <?php
        if (is_front_page() || is_home()) {
            echo do_shortcode('[wshc_scientific_engine_home]');
        } else {
            // Render normal page content (like dashboard, auth, etc.)
            if (have_posts()) {
                while (have_posts()) {
                    the_post();
                    the_content();
                }
            }
        }
        ?>
    </main>

    <?php include WSHC_PLUGIN_DIR . 'templates/global/footer.php'; ?>
</div>

<?php wp_footer(); ?>
</body>
</html>
