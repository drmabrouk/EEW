<?php

namespace WSHC\Dashboard\Modules;

class PagesModule {
    public function render($search = '', $status = '') {
        $args = [
            'post_type' => 'page',
            'post_status' => ['publish', 'draft', 'private'],
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC'
        ];

        if (!empty($search)) {
            $args['s'] = sanitize_text_field($search);
        }
        if (!empty($status)) {
            $args['post_status'] = sanitize_text_field($status);
        }

        $pages = get_posts($args);

        ob_start();
        ?>
        <div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; font-size:20px; font-weight:800;">Site Pages Ledger</h2>
            <button class="button restore-pages-btn" style="background:#000; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;"><span class="dashicons dashicons-update"></span> Auto-Restore System Pages</button>
        </div>

        <div class="wshc-module-toolbar" style="display:flex; gap:15px; margin-bottom:20px; background:#fff; padding:15px; border-radius:8px; border:1px solid #eaeaea;">
            <input type="text" class="wshc-search-bar" id="module-search-input" placeholder="Search pages..." value="<?php echo esc_attr($search); ?>" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:4px;">
            <select class="wshc-filter-group module-filter-select" id="module-status-filter" style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                <option value="">All Statuses</option>
                <option value="publish" <?php selected($status, 'publish'); ?>>Published</option>
                <option value="draft" <?php selected($status, 'draft'); ?>>Draft</option>
                <option value="private" <?php selected($status, 'private'); ?>>Hidden</option>
            </select>
            <button class="button trigger-module-search-btn" data-module="pages" style="background:#0073aa; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Filter</button>
        </div>

        <div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">
            <table class="wp-list-table widefat striped" style="border:none; margin:0;">
                <thead>
                    <tr>
                        <th style="padding:15px;">Page Title</th>
                        <th style="padding:15px;">Status</th>
                        <th style="padding:15px; text-align:right;">Action Controls</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($pages)) : ?>
                        <tr><td colspan="3" style="padding:30px; text-align:center; color:#888;">No pages match your criteria.</td></tr>
                    <?php else : ?>
                        <?php foreach ($pages as $p) :
                            $status_badge = '';
                            if ($p->post_status === 'publish') $status_badge = '<span style="background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Published</span>';
                            elseif ($p->post_status === 'draft') $status_badge = '<span style="background:#fff3e0; color:#f57c00; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Draft</span>';
                            elseif ($p->post_status === 'private') $status_badge = '<span style="background:#ffebee; color:#c62828; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Hidden</span>';
                        ?>
                            <tr>
                                <td style="padding:15px; font-weight:bold;"><?php echo esc_html($p->post_title); ?></td>
                                <td style="padding:15px;"><?php echo $status_badge; ?></td>
                                <td style="padding:15px; text-align:right;">
                                    <?php if ($p->post_status !== 'publish') : ?>
                                        <button class="button page-action-btn" data-id="<?php echo $p->ID; ?>" data-action="publish" title="Publish"><span class="dashicons dashicons-yes"></span></button>
                                    <?php else : ?>
                                        <button class="button page-action-btn" data-id="<?php echo $p->ID; ?>" data-action="unpublish" title="Draft"><span class="dashicons dashicons-hidden"></span></button>
                                    <?php endif; ?>
                                    <button class="button page-action-btn" data-id="<?php echo $p->ID; ?>" data-action="duplicate" title="Duplicate"><span class="dashicons dashicons-admin-page"></span></button>
                                    <button class="button page-action-btn" data-id="<?php echo $p->ID; ?>" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        return ob_get_clean();
    }
}
