<?php
namespace Datawiza;

/**
 * Plugin Name: Datawiza Proxy Auth Plugin - SSO
 * Description: The plugin authenticates the user in Wordpress and set him/her role via HTTP header fields.
 * Version: 1.1.1
 * Author: Datawiza
 * Author URI: https://www.datawiza.com/
 * License: MPL-2.0 License
 * License URI: https://www.mozilla.org/en-US/MPL/2.0/
 * Text Domain: datawiza
 * Domain Path: /languages
 */

require 'vendor/autoload.php';
require 'includes/datawiza-admin.php';

use Exception;
use Firebase\JWT\JWT;
use Firebase\JWT\SignatureInvalidException;
use Monolog\Handler\FirePHPHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class DatawizaSignIn
{
    private $logger;
    private $DatawizaAdmin;
    private $validToken;

    public function __construct()
    {
        $this->logger = new Logger('dw_widget_logger');
        $this->logger->pushHandler(new StreamHandler(__DIR__ . '/log/debug.log', Logger::DEBUG));
        $this->logger->pushHandler(new FirePHPHandler());

        $this->DatawizaAdmin = new DatawizaAdmin();

        add_action('init', array($this, 'logUserInWordpress'));
        add_action('login_init', array($this, 'logUserOutOfAccessBroker'));
    }

    public function logUserOutOfAccessBroker()
    {
        if (!isset($_SERVER['HTTP_DW_TOKEN']) || !$this->verifyToken($_SERVER['HTTP_DW_TOKEN'])) {
            return;
        }
        if (!isset($_GET['action']) || $_GET['action'] !== 'logout') {
            return;
        }
        wp_clear_auth_cookie();
        wp_redirect('/ab-logout');
        exit;
    }

    public function logUserInWordpress()
    {
        // If the user has logged in
        $current_user_id = wp_get_current_user()->ID;
        if ($current_user_id) {
            return;
        }

        // If we cannot extract the dw-token from header
        if (!isset($_SERVER['HTTP_DW_TOKEN'])) {
            return;
        }
        $dw_token = $_SERVER['HTTP_DW_TOKEN'];
        $key = get_option('datawiza-private-secret');
        try {
            $payload = JWT::decode($dw_token, $key, array('HS256'));
        } catch (SignatureInvalidException $e) {
            return;
        } catch (Exception $e) {
            return;
        }

        // If we cannot extract the user's email from header
        if (!isset($payload->email)) {
            return;
        }
        $email = $payload->email;

        $user = get_user_by('email', $email);

        if (!$user) {
            $random_password = wp_generate_password($length = 64, $include_standard_special_chars = false);
            $user_id = wp_create_user($email, $random_password, $email);
            $user = get_user_by('id', $user_id);

            // If we can extract the user's role from header, then set the role
            // Otherwise set it to default role: subscriber
            if (isset($payload->role)) {
                $user->set_role(strtolower($payload->role));
            }
        }

        wp_clear_auth_cookie();
        wp_set_current_user($user->ID);
        wp_set_auth_cookie($user->ID);
        do_action('wp_login', $user->login, $user);
        wp_safe_redirect(isset($_GET['redirect_to']) ? $_GET['redirect_to'] : home_url());
        exit;
    }

    private function verifyToken($jwt)
    {
        $key = get_option('datawiza-private-secret');
        try {
            $payload = JWT::decode($jwt, $key, array('HS256'));
        } catch (SignatureInvalidException $e) {
            return false;
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

}

$datawiza = new DatawizaSignIn();
