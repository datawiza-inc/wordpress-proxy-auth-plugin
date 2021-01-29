<?php
namespace Datawiza;

class DatawizaAdmin
{
  public function __construct(){
    // https://codex.wordpress.org/Creating_Options_Pages
    add_action('admin_init', array($this, 'registerSettingsAction'));

    // https://codex.wordpress.org/Adding_Administration_Menus
    add_action('admin_menu', array($this, 'optionsMenuAction'));
  }

  public function optionsMenuAction() {
    add_options_page(
        'Datawiza Proxy Auth Options',
        'Datawiza Proxy Auth',
        'manage_options',
        'datawiza-sign-in-widget',
        array($this, 'optionsPageAction')
    );
  }

  public function optionsPageAction() {
    if (current_user_can('manage_options'))  {
        include(plugin_dir_path(__FILE__)."../templates/options-form.php");
    } else {
        wp_die( 'You do not have sufficient permissions to access this page.' );
    }
  }

  public function optionsPageTextInputAction($option_name, $type, $placeholder=false, $description=false) {
    $option_value = get_option($option_name, '');
    printf(
        '<input type="%s" id="%s" name="%s" value="%s" style="width: 100%%" autocomplete="off" placeholder="%s" />',
        esc_attr($type),
        esc_attr($option_name),
        esc_attr($option_name),
        esc_attr($option_value),
        esc_attr($placeholder)
    );
    if($description)
        echo '<p class="description">'.$description.'</p>';
  }

  public function registerSettingsAction() {
    add_settings_section(
      'datawiza-sign-in-widget-options-section',
      '',
      null,
      'datawiza-sign-in-widget'
    );
    
    register_setting('datawiza-sign-in-widget', 'datawiza-private-secret', array(
      'type' => 'string',
      'show_in_rest' => false,
    ));

    add_settings_field(
        'datawiza-private-secret',
        'DW Token Private Secret',
        function() { $this->optionsPageTextInputAction('datawiza-private-secret', 'text', 'Copy paste your JWT\'s private secret here. ', 'It is used for verifying the token. If you are using Datawiza Access Broker, the secret is Provisioning Secret which is the same with the one when you setting up Access Broker'); },
        'datawiza-sign-in-widget',
        'datawiza-sign-in-widget-options-section'
    );
  }
}
