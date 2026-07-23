jQuery(document).ready(function($) {
    let offset = 10;
    let searchTimer;
    const loadMoreBtn = $('#wshc-load-more');
    const registryList = $('#wshc-member-registry');
    const searchInput = $('#wshc-directory-search');

    const specialtyFilter = $('#wshc-specialty-filter');
    const countryFilter = $('#wshc-country-filter');

    // Asynchronous Search Logic
    function triggerSearch() {
        clearTimeout(searchTimer);
        const query = searchInput.val();

        searchTimer = setTimeout(function() {
            offset = 0; // Reset offset for new search
            loadBatch(true, query);
        }, 500); // 500ms debounce
    }

    if (searchInput.length) {
        searchInput.on('input', triggerSearch);
    }

    if (specialtyFilter.length) {
        specialtyFilter.on('change', triggerSearch);
    }

    if (countryFilter.length) {
        countryFilter.on('change', triggerSearch);
    }

    // Load More Logic
    if (loadMoreBtn.length) {
        loadMoreBtn.on('click', function(e) {
            e.preventDefault();
            loadBatch(false, searchInput.val());
        });
    }

    /**
     * Fetch a batch of members.
     * @param {boolean} replace - Whether to replace or append results.
     * @param {string} search - The search query.
     */
    function loadBatch(replace = false, search = '') {
        const specialty = specialtyFilter.val();
        const country = countryFilter.val();
        const btn = $('#wshc-load-more');
        const originalBtnText = btn.html();

        if (replace) {
            registryList.css('opacity', '0.5');
        } else {
            btn.prop('disabled', true).html('<span class="dashicons dashicons-update spin"></span> Loading batches...');
        }

        $.ajax({
            url: wshc_directory_obj.ajaxurl,
            type: 'POST',
            data: {
                action: 'wshc_load_more_members',
                offset: offset,
                search: search,
                specialty: specialty,
                country: country
            },
            success: function(response) {
                registryList.css('opacity', '1');
                btn.prop('disabled', false).html(originalBtnText);

                if (response.success) {
                    const html = response.data.html;
                    const count = response.data.count;

                    if (replace) {
                        registryList.html(html || '<div class="no-members-notice"><span class="dashicons dashicons-search"></span><p>No results match your criteria.</p></div>');
                        offset = count;
                    } else {
                        const newRows = $(html).hide();
                        registryList.append(newRows);
                        newRows.fadeIn(400);
                        offset += count;
                    }

                    // Handle button visibility
                    if (count < 10) {
                        btn.hide();
                    } else {
                        btn.show();
                    }
                }
            },
            error: function() {
                registryList.css('opacity', '1');
                btn.prop('disabled', false).html(originalBtnText);
                alert('Connection error. Please refresh and try again.');
            }
        });
    }
    // Admin Add Member Modal
    $('#admin-add-member-btn').on('click', function(e) {
        e.preventDefault();
        $('#admin-add-member-modal').css('display', 'flex').hide().fadeIn(200);
    });

    $('#admin-add-member-modal .close-modal').on('click', function(e) {
        e.preventDefault();
        $('#admin-add-member-modal').fadeOut(200);
    });

    $('#admin-add-member-form').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        const btn = form.find('button[type="submit"]');
        const originalText = btn.text();

        btn.prop('disabled', true).text('CREATING...');

        $.ajax({
            url: wshc_directory_obj.ajaxurl,
            type: 'POST',
            data: form.serialize() + '&action=wshc_admin_add_member&nonce=' + wshc_directory_obj.nonce,
            success: function(response) {
                btn.prop('disabled', false).text(originalText);
                if (response.success) {
                    $('#admin-add-member-modal').fadeOut(200);
                    form[0].reset();
                    // Instantly append by reloading the first batch
                    offset = 0;
                    loadBatch(true, searchInput.val());
                } else {
                    alert(response.data.message);
                }
            }
        });
    });
});
