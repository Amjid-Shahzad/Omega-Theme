<?php
/**
 * Page Title Toggle (Classic Editor)
 * Adds a show/hide toggle button for the page title.
 * Visible only to admins and editors.
 */

if (!defined('ABSPATH')) exit;

function ptt_enqueue_page_title_toggle_script() {
    if (is_page() && current_user_can('edit_pages')) {
        ?>
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            // Detect the page title element (most themes use .entry-title)
            let $title = $('h1.entry-title, h1.page-title');

            if ($title.length) {
                // Insert button before title
                $title.before('<button id="toggle-page-title" class="page-title-toggle" style="margin-bottom:10px;display:block;">👁 Hide Title</button>');

                const bodyClasses = $('body').attr('class');
                const match = bodyClasses.match(/page-id-(\d+)/);
                if (!match) return;
                const pageId = match[1];
                const storageKey = 'pageTitleState_' + pageId;

                const $button = $('#toggle-page-title');

                // Apply saved state
                const savedState = localStorage.getItem(storageKey);
                if (savedState === 'hidden') {
                    $title.hide();
                    $button.text('👁 Show Title');
                } else {
                    $title.show(); // default visible
                    $button.text('👁 Hide Title');
                }

                // Toggle on button click
                $button.on('click', function() {
                    $title.toggle();
                    if ($title.is(':visible')) {
                        localStorage.setItem(storageKey, 'visible');
                        $button.text('👁 Hide Title');
                    } else {
                        localStorage.setItem(storageKey, 'hidden');
                        $button.text('👁 Show Title');
                    }
                });
            }
        });
        </script>
        <?php
    }
}
add_action('wp_footer', 'ptt_enqueue_page_title_toggle_script');
