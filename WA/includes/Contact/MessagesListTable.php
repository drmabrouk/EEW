<?php

namespace WSHC\Contact;

if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}

class MessagesListTable extends \WP_List_Table {
    public function __construct() {
        parent::__construct([
            'singular' => 'message',
            'plural'   => 'messages',
            'ajax'     => false
        ]);
    }

    public function get_columns() {
        return [
            'cb'         => '<input type="checkbox" />',
            'full_name'  => 'Name',
            'email'      => 'Email',
            'subject'    => 'Subject',
            'status'     => 'Status',
            'created_at' => 'Date'
        ];
    }

    public function get_sortable_columns() {
        return [
            'full_name'  => ['full_name', false],
            'email'      => ['email', false],
            'created_at' => ['created_at', true]
        ];
    }

    protected function column_default($item, $column_name) {
        switch ($column_name) {
            case 'email':
            case 'subject':
            case 'status':
            case 'created_at':
                return esc_html($item->$column_name);
            default:
                return print_r($item, true);
        }
    }

    protected function column_cb($item) {
        return sprintf('<input type="checkbox" name="message[]" value="%s" />', $item->id);
    }

    protected function column_full_name($item) {
        $view_nonce = wp_create_nonce('view_message_' . $item->id);
        $delete_nonce = wp_create_nonce('delete_message_' . $item->id);

        $actions = [
            'view'   => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s">View</a>', $_REQUEST['page'], 'view', $item->id, $view_nonce),
            'delete' => sprintf('<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" style="color:red;">Delete</a>', $_REQUEST['page'], 'delete', $item->id, $delete_nonce),
        ];

        return sprintf('%1$s %2$s', esc_html($item->full_name), $this->row_actions($actions));
    }

    public function prepare_items() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'wshc_contact_messages';

        $per_page = 20;
        $columns = $this->get_columns();
        $hidden = [];
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = [$columns, $hidden, $sortable];

        $where = "WHERE 1=1";

        // Handle Search
        if (!empty($_REQUEST['s'])) {
            $search = '%' . $wpdb->esc_like($_REQUEST['s']) . '%';
            $where .= $wpdb->prepare(" AND (full_name LIKE %s OR email LIKE %s OR subject LIKE %s OR message LIKE %s)", $search, $search, $search, $search);
        }

        // Handle Filter by Status
        if (!empty($_REQUEST['filter_status'])) {
            $where .= $wpdb->prepare(" AND status = %s", sanitize_text_field($_REQUEST['filter_status']));
        }

        // Handle Ordering
        $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field($_REQUEST['orderby']) : 'created_at';
        $order = (!empty($_REQUEST['order'])) ? sanitize_text_field($_REQUEST['order']) : 'DESC';

        // Allowed orderby keys
        $valid_orderbys = ['full_name', 'email', 'created_at'];
        if (!in_array($orderby, $valid_orderbys)) {
            $orderby = 'created_at';
        }
        $order = ($order === 'ASC') ? 'ASC' : 'DESC';

        $current_page = $this->get_pagenum();
        $offset = ($current_page - 1) * $per_page;

        $total_items = $wpdb->get_var("SELECT COUNT(*) FROM $table_name $where");
        $this->items = $wpdb->get_results("SELECT * FROM $table_name $where ORDER BY $orderby $order LIMIT $per_page OFFSET $offset");

        $this->set_pagination_args([
            'total_items' => $total_items,
            'per_page'    => $per_page,
            'total_pages' => ceil($total_items / $per_page)
        ]);
    }
}
