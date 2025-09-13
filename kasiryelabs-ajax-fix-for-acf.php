<?php
/**
 * Plugin Name:       KasiryeLabs AJAX Fix for ACF
 * Plugin URI:        https://kasiryelabs.com/plugins/kasirye-ajax-fix-for-acf
 * Description:       Fixes and enhances AJAX handling issues for Advanced Custom Fields (ACF).
 * Version:           1.0.0
 * Author:            Arthur Kasirye
 * Author URI:        https://kasiryelabs.com
 * License:           GPLv2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       kasiryelabs-ajax-fix-for-acf
 * Domain Path:       /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class KasiryeLabs_Ajax_Fix_For_ACF {

    public function __construct() {
        // Hook ACF AJAX initialization later to avoid "called too early" notices
        add_action( 'init', [ $this, 'init_fix' ], 20 );
    }

    /**
     * Ensure ACF AJAX actions are loaded at the correct time
     */
    public function init_fix() {
        if ( function_exists( 'acf' ) ) {
            // Re-register ACF AJAX hooks on init (instead of too early)
            add_action( 'wp_ajax_acf/ajax_query', [ $this, 'handle_acf_ajax_query' ] );
            add_action( 'wp_ajax_nopriv_acf/ajax_query', [ $this, 'handle_acf_ajax_query' ] );
        }
    }

    /**
     * Handle ACF AJAX requests safely
     */
    public function handle_acf_ajax_query() {
        // Only run if ACF is active
        if ( function_exists( 'acf' ) && class_exists( 'ACF_Ajax_Query' ) ) {
            // Use the ACF class handler if available
            $handler = new ACF_Ajax_Query();
            if ( method_exists( $handler, 'response' ) ) {
                $handler->response();
                return;
            }
        }

        // Fallback response
        wp_send_json_error( array( 'message' => 'ACF not active or handler not available' ) );
    }
}

new KasiryeLabs_Ajax_Fix_For_ACF();
