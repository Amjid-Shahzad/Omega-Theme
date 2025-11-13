<?php
// early-output-check.php
add_action('plugins_loaded', function() {
    ob_start(function($output) {
        // Detect if anything is sent before the DOCTYPE
        if (stripos($output, '<!DOCTYPE html>') !== 0) {
            $log = ABSPATH . 'wp-content/early-output.log';
            $content = "----- EARLY OUTPUT DETECTED -----\n\n" . $output . "\n-----------------------------\n";
            file_put_contents($log, $content, FILE_APPEND);
        }
        return $output;
    });
});
