<?php
/**
 * @package Koopid
 * @version 1.1
 */
/*
Plugin Name: Koopid Chat
Plugin URI: https://app.koopid.io/provider/
Description: Conversational engagement, simplified. Koopid delivers digital self-service and agent engagement across channels. Learn more at https://koopid.ai
Author: Koopid
Version: 2.2
Author URI: https://koopid.ai
*/
defined( 'ABSPATH' ) or die( 'No script kiddies please!' );
include 'variables.php';
class Koopid_Plugin {
    var $version = 5;
    public static function activate()   {
        Koopid_Plugin::clear_cache();
    }
    public static function deactivate() {
        Koopid_Plugin::clear_cache();
    }

    function __construct() {
    //  add_option('koopid_widget_code', '', '', 'yes');
    //  add_option('koopid_widget_name', '', '', 'yes');
        add_action('wp_footer', array( &$this, 'load_snippet' ) );
        add_action('admin_menu', array( &$this, 'create_menu' ) );
        add_action('admin_init', array( &$this, 'register_my_setting' ) );
        
        $this->update_plugin();
    }
    function register_my_setting() {
        register_setting( 'koopid-settings', 'koopid_options', 'options_sanitize' ); 
    }
    
    function options_sanitize($input) {
        $input['koopid_server'] = sanitize_text_field($input['koopid_server']);
        $input['entry_tag'] = sanitize_text_field($input['entry_tag']);
        $input['provider_email'] = sanitize_email($input['provider_email']);
        $input['require_login'] = isset($input['require_login']) ? true : false;
        $input['open_popup'] = isset($input['open_popup']) ? true : false;
        $input['button_css'] = sanitize_text_field($input['button_css']);
        $input['url_params'] = sanitize_text_field($input['url_params']);
        $input['proactive_chat'] = isset($input['proactive_chat']) ? true : false;
        $input['proactive_chat_timer'] = sanitize_text_field($input['proactive_chat_timer']);
        return $input;
    }
    function update_plugin() {
        update_option('koopid_plugin_ver', $this->version);
    }
    function create_menu() {
        add_menu_page('Koopid', 'Koopid', 'manage_options', 'koopid-menu', array( &$this, 'koopid_settings' ), plugins_url('menu-logo.png', __FILE__));
    }
    function load_snippet() {
        global $current_user;
        global $koopidHome;
        
        //$koopidHome = "http://127.0.0.1:8080";
        $options = get_option('koopid_options');
        $proactive_chat = $options['proactive_chat']; 
        if (wp_is_mobile()) {
            $proactive_chat = false;
        }
        if ($options['koopid_server']) {
            $koopidHome = $options['koopid_server'];
        }
        if ($options && $options['provider_email'] && $options['entry_tag']) {
            echo("<script type='text/javascript' data-cfasync='false'>window.koopidApi = { l: [], t: [], on: function () { this.l.push(arguments); } }; (function () { var done = false; var style = document.createElement('link'); style.setAttribute('rel', 'stylesheet'); style.setAttribute('href', '" . $koopidHome . "/kpd-static/common/css/koopid.css'); document.getElementsByTagName('HEAD').item(0).appendChild(style); style = document.createElement('style'); style.setAttribute('type', 'text/css'); style.innerText = '#kpd_chat { z-index: 99999; }'; document.getElementsByTagName('HEAD').item(0).appendChild(style); var script = document.createElement('script'); script.async = true; script.type = 'text/javascript'; script.src = '" . $koopidHome . "/kpd-static/common/js/koopid-embed.js'; document.getElementsByTagName('HEAD').item(0).appendChild(script); script.onreadystatechange = script.onload = function (e) { if (!done && (!this.readyState || this.readyState == 'loaded' || this.readyState == 'complete')) { kpde.server = '" . $koopidHome . "'; document.body.setAttribute('provider-email', '" . $options['provider_email'] . "'); var div = document.createElement('div'); div.setAttribute('data-kpdembedded', '" . ($options['open_popup'] == "true" ? "false" : "true") . "'); div.setAttribute('data-kpdguest', '" . ($options['require_login'] == "true" ? "false" : "true") . "'); div.setAttribute('data-kpdtag', '" . $options['entry_tag'] . "'); div.setAttribute('data-kpdproactive', '" . $proactive_chat . "'); div.setAttribute('data-kpdproactivetimer', '" . $options['proactive_chat_timer'] . "'); div.setAttribute('data-kpdparams', '" . $options['url_params'] . "'); div.className = 'kpd_koopidtag'; div.setAttribute('style', 'position: fixed; bottom: 10px; right: 10px; z-index: 99999; width: 48px; height: 48px; background-image: url(\"" . $koopidHome . "/kpd-static/providers/0/images/logo-96.png\"); background-size: cover; background-repeat: no-repeat; border-radius: 12px; box-shadow: 0 0 10px 1px #808080;" . $options['button_css'] . "'); document.body.appendChild(div); done = true; } }; })();</script>");
        }
    }
    private static function clear_cache() {
        if (function_exists('wp_cache_clear_cache')) {
            wp_cache_clear_cache();
        }
    }
    function koopid_settings() {
        ?>
        <head>
            <style type="text/css">
                div.title {
                    vertical-align: top;
                }
                div.title > * {
                    display: inline-block;
                }
                div.title > span {
                    color: #0056eb; font-size: 24px; line-height: 32px;
                    vertical-align: top; padding: 0;
                    border-top: solid 1px #0056eb;
                    border-bottom: solid 1px #0056eb;
                }
                div.title > img {
                    float: right; margin-right: 1ch;
                }
                form {
                    margin-left: 8ch;
                }
                form > * {
                    margin-top: 1ch;
                }
            </style>
        </head>
        <p>
        <div class="content">
            <div class="title">
                <img class="logo" src="<?php echo plugins_url('wp-logo.png', __FILE__); ?>" alt="Koopid logo"></img>
                <span>Koopid Chat Plugin</span>
            </div>
            <div class="desc">
                <p>Koopid enables interaction with your website visitors using chat bots, business workflows, data collection widgets, or human agents.</p>
                <ol>
                <li>Signup for a business account on our <a href="https://app.koopid.io/provider/" target="_blank">provider portal</a>.</li>
                <li>Create a new entry tag to reach an agent or to trigger a workflow.</li>
                <li>Edit the information below to match your data and preference.</li>
                </ol>
            </div>
            <form action="options.php" method="post">
            <?php
                settings_fields('koopid-settings');
                do_settings_sections('koopid-settings');
                $options = get_option('koopid_options');
            ?>
                <div>
                    <input type="text" placeholder="Koopid Server"
                           name="koopid_options[koopid_server]"
                           value="<?php echo esc_attr($options['koopid_server']); ?>" />
                </div>
                <div>
                    <input type="text" placeholder="Registered Email"
                           name="koopid_options[provider_email]"
                           value="<?php echo esc_attr($options['provider_email']); ?>" />
                </div>
                <div>
                    <input type="text" placeholder="Entry Tag"
                           name="koopid_options[entry_tag]"
                           value="<?php echo esc_attr($options['entry_tag']); ?>" />
                </div>
                <div>
                    <input type="checkbox" name="koopid_options[require_login]" value="true"
                            <?php if ($options['require_login'] == 'true') { echo 'checked'; } ?>/>
                    Require customer login?
                </div>
                <div>
                    <input type="checkbox" name="koopid_options[open_popup]" value="true"
                            <?php if ($options['open_popup'] == 'true') { echo 'checked'; } ?>/>
                    Open popup window?
                </div>
                 <div>
                    <input type="checkbox" name="koopid_options[proactive_chat]" value="true"
                            <?php if ($options['proactive_chat'] == 'true') { echo 'checked'; } ?>/>
                    Proactive Chat Popup?
                </div>
                <div>
                    <input type="text" placeholder="Proactive timer (secs)"
                           name="koopid_options[proactive_chat_timer]"
                           value="<?php echo esc_attr($options['proactive_chat_timer']); ?>" />
                </div>
                <div>
                    <input type="text" placeholder="Button CSS"
                           name="koopid_options[button_css]"
                           value="<?php echo esc_attr($options['button_css']); ?>" />
                </div>
                <div>
                    <input type="text" placeholder="URL Params"
                           name="koopid_options[url_params]"
                           value="<?php echo esc_attr($options['url_params']); ?>" />
                </div>
                <?php submit_button(); ?>
            </form>
        </div>
        <?php
    }
}
register_activation_hook(__FILE__, array('Koopid_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('Koopid_Plugin', 'deactivate'));
new Koopid_Plugin();
?>