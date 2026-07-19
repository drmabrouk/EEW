<?php
$file = 'WA/assets/js/dashboard.js';
$content = file_get_contents($file);
if (strpos($content, '$(\'.nav-btn\').on(\'click\'') !== false) {
    echo "Click handler found\n";
} else {
    echo "Click handler NOT found\n";
}
