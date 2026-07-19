<?php

namespace WSHC\Contact;

class ContactManager {
    public function init() {
        add_action('init', [$this, 'register_cpts']);
        add_shortcode('wshc_contact_us', [$this, 'render_contact_page']);
        add_shortcode('wshc_faq', [$this, 'render_faq_section']);

        // Handle AJAX form submission
        add_action('wp_ajax_nopriv_wshc_submit_contact', [$this, 'handle_submission']);
        add_action('wp_ajax_wshc_submit_contact', [$this, 'handle_submission']);
    }

    public function admin_init() {

    }

    public function register_cpts() {
        register_post_type('wshc_faq', [
            'labels' => [
                'name' => 'FAQs',
                'singular_name' => 'FAQ'
            ],
            'public' => false,
            'show_ui' => true,
            'menu_icon' => 'dashicons-editor-help',
            'supports' => ['title', 'editor']
        ]);
    }

    public function register_admin_menu() {
        add_menu_page(
            'Messages',
            'Messages',
            'manage_options',
            'wshc_contact_messages',
            [$this, 'render_messages_page'],
            'dashicons-email',
            25
        );
    }

    public function render_messages_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        global $wpdb;
        $table_name = $wpdb->prefix . 'wshc_contact_messages';

        $action = $_GET['action'] ?? '';
        $id = intval($_GET['id'] ?? 0);

        if ($id) {
            if ($action === 'delete') {
                check_admin_referer('delete_message_' . $id);
                $wpdb->delete($table_name, ['id' => $id]);
                echo '<div class="notice notice-success"><p>Message deleted.</p></div>';
            } elseif ($action === 'view') {
                check_admin_referer('view_message_' . $id);
                $wpdb->update($table_name, ['status' => 'read'], ['id' => $id]);
                $msg = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $id));
                if ($msg) {
                    echo '<div class="wrap"><h1>View Message</h1>';
                    echo '<p><strong>From:</strong> ' . esc_html($msg->full_name) . ' (' . esc_html($msg->email) . ')</p>';
                    echo '<p><strong>Subject:</strong> ' . esc_html($msg->subject) . '</p>';
                    echo '<p><strong>Date:</strong> ' . esc_html($msg->created_at) . '</p>';
                    echo '<hr><p>' . nl2br(esc_html($msg->message)) . '</p>';
                    echo '<a href="?page=wshc_contact_messages" class="button">Back to Messages</a></div>';
                    return;
                }
            }
        }

        require_once WSHC_PLUGIN_DIR . 'includes/Contact/MessagesListTable.php';
        $list_table = new MessagesListTable();
        $list_table->prepare_items();

        echo '<div class="wrap"><h1>Contact Messages</h1>';
        echo '<form method="get">';
        echo '<input type="hidden" name="page" value="' . esc_attr($_REQUEST['page']) . '" />';
        echo '<div class="tablenav top">';
        echo '<div class="alignleft actions">';
        echo '<select name="filter_status">';
        echo '<option value="">All Statuses</option>';
        $selected_unread = (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'unread') ? 'selected' : '';
        $selected_read = (isset($_REQUEST['filter_status']) && $_REQUEST['filter_status'] === 'read') ? 'selected' : '';
        echo '<option value="unread" ' . $selected_unread . '>Unread</option>';
        echo '<option value="read" ' . $selected_read . '>Read</option>';
        echo '</select>';
        echo '<input type="submit" class="button" value="Filter">';
        echo '</div>';
        echo '</div>';
        $list_table->search_box('Search Messages', 's');
        $list_table->display();
        echo '</form></div>';
    }

    public function render_contact_page() {
        wp_enqueue_script('wshc-contact-js', WSHC_PLUGIN_URL . 'assets/js/contact.js', ['jquery'], '1.0.0', true);
        wp_localize_script('wshc-contact-js', 'wshc_contact_obj', [
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce('wshc_contact_nonce')
        ]);

        ob_start();
        ?>
        <div class="wshc-contact-container">
            <div class="contact-info">
                <h2>Get in Touch</h2>
                <p>Have questions about the World Sports Health Council? Contact us directly.</p>
            </div>
            <form id="wshc-contact-form">
                <input type="text" name="full_name" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email Address" required>
                <input type="text" name="subject" placeholder="Subject" required>
                <textarea name="message" placeholder="Your Message" required></textarea>
                <button type="submit">Send Message</button>
                <div id="wshc-contact-response"></div>
            </form>
            <div class="faq-section">
                <?php echo do_shortcode('[wshc_faq]'); ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }

    public function render_faq_section() {
        $faqs = get_posts([
            'post_type' => 'wshc_faq',
            'posts_per_page' => -1,
            'post_status' => 'publish'
        ]);

        if (empty($faqs)) return '';

        ob_start();
        echo '<div class="wshc-faqs"><h2>Frequently Asked Questions</h2>';
        foreach ($faqs as $faq) {
            echo '<div class="faq-item">';
            echo '<h3>' . esc_html($faq->post_title) . '</h3>';
            echo '<div class="faq-content">' . apply_filters('the_content', $faq->post_content) . '</div>';
            echo '</div>';
        }
        echo '</div>';
        return ob_get_clean();
    }

    public function handle_submission() {
        check_ajax_referer('wshc_contact_nonce', 'nonce');

        global $wpdb;
        $table_name = $wpdb->prefix . 'wshc_contact_messages';

        $data = [
            'full_name' => sanitize_text_field($_POST['full_name']),
            'email'     => sanitize_email($_POST['email']),
            'subject'   => sanitize_text_field($_POST['subject']),
            'message'   => sanitize_textarea_field($_POST['message']),
            'status'    => 'unread'
        ];

        if (empty($data['full_name']) || empty($data['email']) || empty($data['message'])) {
            wp_send_json_error(['message' => 'Please fill all required fields.']);
        }

        $inserted = $wpdb->insert($table_name, $data);

        if ($inserted) {
            // Optional email notification
            $admin_email = get_option('admin_email');
            wp_mail($admin_email, 'New Contact Message: ' . $data['subject'], "From: {$data['full_name']} <{$data['email']}>\n\n{$data['message']}");
            wp_send_json_success(['message' => 'Your message has been sent successfully.']);
        } else {
            wp_send_json_error(['message' => 'There was an error sending your message. Please try again.']);
        }
    }
}
