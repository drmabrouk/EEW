<?php

namespace WSHC\Dashboard\Modules;

class GenericCPTModule {
    public function render($post_type, $title, $search = '', $status = '') {
        $args = [
            'post_type' => $post_type,
            'post_status' => ['publish', 'draft'],
            'posts_per_page' => -1,
            'orderby' => 'date',
            'order' => 'DESC'
        ];

        if (!empty($search)) {
            $args['s'] = sanitize_text_field($search);
        }
        if (!empty($status)) {
            $args['post_status'] = sanitize_text_field($status);
        }

        $posts = get_posts($args);

        ob_start();
        ?>
        <div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; font-size:20px; font-weight:800;"><?php echo esc_html($title); ?> Registry</h2>
            <button class="button" style="background:#000; color:#fff; border:none; padding:10px 20px; border-radius:4px; font-weight:bold; cursor:pointer;"><span class="dashicons dashicons-plus"></span> Add New Record</button>
        </div>

        <div class="wshc-module-toolbar" style="display:flex; gap:15px; margin-bottom:20px; background:#fff; padding:15px; border-radius:8px; border:1px solid #eaeaea;">
            <input type="text" class="wshc-search-bar" id="module-search-input" placeholder="Search <?php echo esc_attr(strtolower($title)); ?>..." value="<?php echo esc_attr($search); ?>" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:4px;">
            <select class="wshc-filter-group module-filter-select" id="module-status-filter" style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                <option value="">All Statuses</option>
                <option value="publish" <?php selected($status, 'publish'); ?>>Active / Published</option>
                <option value="draft" <?php selected($status, 'draft'); ?>>Archived / Draft</option>
            </select>
            <button class="button trigger-module-search-btn" data-module="<?php echo esc_attr(str_replace('wshc_', '', $post_type)); ?>" style="background:#0073aa; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Filter</button>
        </div>

        <div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">
            <table class="wp-list-table widefat striped" style="border:none; margin:0;">
                <thead>
                    <tr>
                        <th style="padding:15px;">Title</th>
                        <th style="padding:15px;">Visibility</th>
                        <th style="padding:15px; text-align:right;">Registry Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($posts)) : ?>
                        <tr><td colspan="3" style="padding:30px; text-align:center; color:#888;">Registry is empty or no items match your search.</td></tr>
                    <?php else : ?>
                        <?php foreach ($posts as $p) :
                            $status_badge = ($p->post_status === 'publish') ? '<span style="background:#e8f5e9; color:#2e7d32; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Active</span>' : '<span style="background:#fff3e0; color:#f57c00; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Archived</span>';
                        ?>
                            <tr>
                                <td style="padding:15px; font-weight:bold;"><?php echo esc_html($p->post_title); ?></td>
                                <td style="padding:15px;"><?php echo $status_badge; ?></td>
                                <td style="padding:15px; text-align:right;">
                                    <?php if ($p->post_status !== 'publish') : ?>
                                        <button class="button item-action-btn" data-module="<?php echo esc_attr(str_replace('wshc_', '', $post_type)); ?>" data-id="<?php echo $p->ID; ?>" data-action="publish" title="Publish"><span class="dashicons dashicons-yes-alt"></span></button>
                                    <?php else : ?>
                                        <button class="button item-action-btn" data-module="<?php echo esc_attr(str_replace('wshc_', '', $post_type)); ?>" data-id="<?php echo $p->ID; ?>" data-action="unpublish" title="Archive"><span class="dashicons dashicons-archive"></span></button>
                                    <?php endif; ?>
                                    <button class="button item-action-btn" data-module="<?php echo esc_attr(str_replace('wshc_', '', $post_type)); ?>" data-id="<?php echo $p->ID; ?>" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>
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
