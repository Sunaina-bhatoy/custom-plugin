<?php
/**
 * Plugin Name: Custom Authentication
 * Description: A custom authentication plugin for handling user signup and login via custom REST API endpoints.
 * Version: 1.0
 * Author: Your Name
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Include custom authentication functions.
require_once plugin_dir_path( __FILE__ ) . 'includes/auth-functions.php';

// Create tokens table on plugin activation.
register_activation_hook( __FILE__, 'custom_auth_create_tokens_table' );

function custom_auth_create_tokens_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'user_tokens';

    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        user_id bigint(20) NOT NULL,
        token varchar(255) NOT NULL,
        created_at datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY token (token)
    ) $charset_collate;";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta( $sql );
}

// Add CORS headers
add_action( 'rest_api_init', 'custom_auth_add_cors_headers' );
function custom_auth_add_cors_headers() {
    add_filter( 'rest_pre_serve_request', function( $value ) {
        header( 'Access-Control-Allow-Origin: *' );
        header( 'Access-Control-Allow-Methods: POST, GET, OPTIONS, PUT, DELETE' );
        header( 'Access-Control-Allow-Credentials: true' );
        header( 'Access-Control-Allow-Headers: Content-Type, Authorization' );
        return $value;
    });
}
?>
