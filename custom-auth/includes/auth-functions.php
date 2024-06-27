<?php

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Register custom REST API routes.
add_action( 'rest_api_init', function () {
    register_rest_route( 'custom-auth/v1', '/signup', array(
        'methods' => 'POST',
        'callback' => 'custom_auth_signup',
    ));

    register_rest_route( 'custom-auth/v1', '/login', array(
        'methods' => 'POST',
        'callback' => 'custom_auth_login',
    ));

    register_rest_route( 'custom-auth/v1', '/user', array(
        'methods' => 'GET',
        'callback' => 'custom_auth_get_user_info',
        'permission_callback' => 'custom_auth_check_token',
    ));
});

// Signup function.
function custom_auth_signup( $request ) {
    global $wpdb;

    $params = json_decode($request->get_body(), true); // Decode the JSON payload manually

    $email = sanitize_email( $params['email'] );
    $username = sanitize_text_field( $params['username'] );
    $password = $params['password'];

    // Check if the username or email already exists.
    if ( username_exists( $username ) || email_exists( $email ) ) {
        return new WP_Error( 'user_exists', 'User with this email or username already exists', array( 'status' => 400 ) );
    }

    // Hash the password.
    $hashed_password = wp_hash_password( $password );

    // Insert user into the database.
    $result = $wpdb->insert(
        $wpdb->prefix . 'users',
        array(
            'user_login' => $username,
            'user_pass' => $hashed_password,
            'user_email' => $email,
            'user_registered' => current_time( 'mysql' ),
            'user_status' => 0,
        )
    );

    if ( $result === false ) {
        return new WP_Error( 'registration_failed', 'User registration failed', array( 'status' => 500 ) );
    }

    return array( 'message' => 'User registered successfully' );
}

// Login function.
function custom_auth_login( $request ) {
    global $wpdb;

    $params = json_decode($request->get_body(), true); // Decode the JSON payload manually

    $username = sanitize_text_field( $params['username'] );
    $password = $params['password'];

    // Check if the user exists.
    $user = get_user_by( 'login', $username );
    if ( ! $user || ! wp_check_password( $password, $user->user_pass, $user->ID ) ) {
        return new WP_Error( 'invalid_credentials', 'Invalid username or password', array( 'status' => 401 ) );
    }

    // Generate a token.
    $token = bin2hex( random_bytes( 16 ) );

    // Store the token in the database.
    $wpdb->insert(
        $wpdb->prefix . 'user_tokens',
        array(
            'user_id' => $user->ID,
            'token' => $token,
            'created_at' => current_time( 'mysql' ),
        )
    );

    return array( 'token' => $token );
}

// Get user info function.
function custom_auth_get_user_info( $request ) {
    $user = wp_get_current_user();
    if ( ! $user->ID ) {
        return new WP_Error( 'invalid_token', 'Invalid token', array( 'status' => 401 ) );
    }

    return array(
        'username' => $user->user_login,
        'email' => $user->user_email,
    );
}

// Token validation function.
function custom_auth_check_token( $request ) {
   ;
    $token = $request->get_param('token');
    if ( ! $token ) {
        return false;
    }

    global $wpdb;
    $user_id = $wpdb->get_var( $wpdb->prepare(
        "SELECT user_id FROM {$wpdb->prefix}user_tokens WHERE token = %s", $token
    ));

    if ( $user_id ) {
        wp_set_current_user( $user_id );
        return true;
    }

    return false;
}
?>
