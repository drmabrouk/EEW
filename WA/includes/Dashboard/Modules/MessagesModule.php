<?php

namespace WSHC\Dashboard\Modules;

class MessagesModule {
    public function render($search = '', $status = '') {
        global $wpdb;
        $table = $wpdb->prefix . 'wshc_contact_messages';

        $where = "WHERE 1=1";
        $params = [];

        if (!empty($search)) {
            $like = '%' . $wpdb->esc_like(sanitize_text_field($search)) . '%';
            $where .= " AND (full_name LIKE %s OR email LIKE %s OR subject LIKE %s)";
            $params[] = $like; $params[] = $like; $params[] = $like;
        }
        if (!empty($status)) {
            $where .= " AND status = %s";
            $params[] = sanitize_text_field($status);
        }

        $query = "SELECT * FROM $table $where ORDER BY created_at DESC";
        if (!empty($params)) {
            $query = $wpdb->prepare($query, $params);
        }
        $messages = $wpdb->get_results($query);

        ob_start();
        ?>
        <div class="dashboard-module-header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
            <h2 style="margin:0; font-size:20px; font-weight:800;">Support Communications Inbox</h2>
        </div>

        <div class="wshc-module-toolbar" style="display:flex; gap:15px; margin-bottom:20px; background:#fff; padding:15px; border-radius:8px; border:1px solid #eaeaea;">
            <input type="text" class="wshc-search-bar" id="module-search-input" placeholder="Search messages..." value="<?php echo esc_attr($search); ?>" style="flex:1; padding:10px; border:1px solid #ddd; border-radius:4px;">
            <select class="wshc-filter-group module-filter-select" id="module-status-filter" style="padding:10px; border:1px solid #ddd; border-radius:4px;">
                <option value="">All Statuses</option>
                <option value="unread" <?php selected($status, 'unread'); ?>>Unread</option>
                <option value="read" <?php selected($status, 'read'); ?>>Read</option>
            </select>
            <button class="button trigger-module-search-btn" data-module="messages" style="background:#0073aa; color:#fff; border:none; padding:10px 20px; border-radius:4px; cursor:pointer;">Filter</button>
        </div>

        <div class="wshc-table-wrapper" style="background:#fff; border-radius:8px; border:1px solid #eaeaea; overflow:hidden;">
            <table class="wp-list-table widefat striped" style="border:none; margin:0;">
                <thead>
                    <tr>
                        <th style="padding:15px;">Sender</th>
                        <th style="padding:15px;">Subject</th>
                        <th style="padding:15px;">Status</th>
                        <th style="padding:15px;">Timestamp</th>
                        <th style="padding:15px; text-align:right;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($messages)) : ?>
                        <tr><td colspan="5" style="padding:30px; text-align:center; color:#888;">Inbox is clear or no messages match your criteria.</td></tr>
                    <?php else : ?>
                        <?php foreach ($messages as $msg) :
                            $status_badge = ($msg->status === 'unread') ? '<span style="background:#e3f2fd; color:#1565c0; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Unread</span>' : '<span style="background:#f5f5f5; color:#757575; padding:3px 8px; border-radius:12px; font-size:12px; font-weight:bold;">Read</span>';
                        ?>
                            <tr style="<?php echo ($msg->status === 'unread') ? 'background:#fbfdff;' : ''; ?>">
                                <td style="padding:15px;"><strong><?php echo esc_html($msg->full_name); ?></strong><br><small style="color:#666;"><?php echo esc_html($msg->email); ?></small></td>
                                <td style="padding:15px;"><?php echo esc_html($msg->subject); ?></td>
                                <td style="padding:15px;"><?php echo $status_badge; ?></td>
                                <td style="padding:15px;"><?php echo esc_html($msg->created_at); ?></td>
                                <td style="padding:15px; text-align:right;">
                                    <?php if ($msg->status === 'unread') : ?>
                                        <button class="button item-action-btn" data-module="messages" data-id="<?php echo $msg->id; ?>" data-action="read" title="Mark Read"><span class="dashicons dashicons-yes"></span></button>
                                    <?php endif; ?>
                                    <button class="button item-action-btn" data-module="messages" data-id="<?php echo $msg->id; ?>" data-action="delete" style="color:#d32f2f;" title="Delete"><span class="dashicons dashicons-trash"></span></button>
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
