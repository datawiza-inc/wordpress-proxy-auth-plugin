<?php

/**
 * Plugin Name: Reverse Proxy Auth Widget
 * Description: The plugin authenticates the user in Wordpress and set him/her role via HTTP header fields.
 * Version: 1.1.0
 * Author: Datawiza
 * Author URI: https://www.datawiza.com/
 * License: GPL-2.0
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0-standalone.html
 * Text Domain: datawiza
 * Domain Path: /languages
 */


/* logout uri is: /ab-logout. 
 * This fuction is called when the logout button is clicked in WordPress. 
 */
function logUserOutOfProxy()
{
    wp_clear_auth_cookie();
    wp_redirect('/ab-logout');
    exit;
}

add_action('init', function () {
    // If the user has logged in
    $current_user_id = wp_get_current_user()->ID;
    if ($current_user_id) {
        return;
    }

    // If we cannot extract the user's email from header
    $email = $_SERVER['HTTP_X_USER'];
    if (!$email) {
        return;
    }

    $user = get_user_by('email', $email);
    if (!$user) {
        $random_password = wp_generate_password($length = 64, $include_standard_special_chars = false);
        $user_id = wp_create_user($email, $random_password, $email);
        $user = get_user_by('id', $user_id);
        $role = $_SERVER['HTTP_ROLE'];
        // If we can extract the user's role from header, then set the role
        // Otherwise set it to default role: subscriber
        if ($role) {
            $user->set_role(strtolower($role));
        }
    }

    wp_clear_auth_cookie();
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user->ID);
    do_action('wp_login', $user->login, $user);
    wp_safe_redirect(isset($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url());
    exit;

});

add_action('login_init', function () {
    if ($_SERVER['HTTP_X_USER']) {
        if (isset($_GET['action']) && $_GET['action'] === 'logout') {
            logUserOutOfProxy();
        }
    }
});
