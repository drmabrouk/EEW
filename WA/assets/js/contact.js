jQuery(document).ready(function($) {
    $('#wshc-contact-form').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        var responseDiv = $('#wshc-contact-response');
        var data = form.serialize() + '&action=wshc_submit_contact&nonce=' + wshc_contact_obj.nonce;

        form.find('button').prop('disabled', true).text('Sending...');

        $.post(wshc_contact_obj.ajaxurl, data, function(response) {
            form.find('button').prop('disabled', false).text('Send Message');
            if (response.success) {
                responseDiv.html('<p style="color:green;">' + response.data.message + '</p>');
                form[0].reset();
            } else {
                responseDiv.html('<p style="color:red;">' + response.data.message + '</p>');
            }
        });
    });
});
