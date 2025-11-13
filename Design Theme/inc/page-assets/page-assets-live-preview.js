function($) {

    // Live preview for CSS
    $('#page_css').on('input', function() {
        let css = $(this).val();

        // If style tag doesn't exist, create it
        if ($('#live-preview-css').length === 0) {
            $('head').append('<style id="live-preview-css"></style>');
        }

        // Update CSS live
        $('#live-preview-css').text(css);
    });

    // Live preview for JS
    $('#page_js').on('input', function() {
        let js = $(this).val();

        // Remove any old preview script
        $('#live-preview-js').remove();

        // Create fresh script
        let script = document.createElement('script');
        script.id = 'live-preview-js';

        try {
            script.appendChild(document.createTextNode(js));
        } catch (e) {
            script.text = js;
        }

        document.body.appendChild(script);
    });

}(jQuery);
