<?php
/**
 * Plugin Name: Clients WP - Vimeo
 * Plugin URI:  https://www.gravity2pdf.com
 * Description: Connect Vimeo with Clients WP
 * Version:     1.0
 * Author:      gravity2pdf
 * Author URI:  https://github.com/raphcadiz
 * Text Domain: cl-wp-vimeo
 */

if (!class_exists('Clients_WP_Vimeo')):

    define( 'CWPV_PATH', dirname( __FILE__ ) );
    define( 'CWPV_PATH_INCLUDES', dirname( __FILE__ ) . '/includes' );
    define( 'CWPV_PATH_CLASS', dirname( __FILE__ ) . '/class' );
    define( 'CWPV_FOLDER', basename( CWPV_PATH ) );
    define( 'CWPV_URL', plugins_url() . '/' . CWPV_FOLDER );
    define( 'CWPV_URL_INCLUDES', CWPV_URL . '/includes' );
    define( 'CWPV_URL_CLASS', CWPV_URL . '/class' );
    define( 'CWPV_VERSION', 1.0 );

    register_activation_hook( __FILE__, 'clients_wp_vimeo_activation' );
    function clients_wp_vimeo_activation(){
        if ( ! class_exists('Clients_WP') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
            wp_die('Sorry, but this plugin requires the Restrict Content Pro and Clients WP to be installed and active.');
        }

    }

    add_action( 'admin_init', 'clients_wp_vimeo_activate' );
    function clients_wp_vimeo_activate(){
        if ( ! class_exists('Clients_WP') ) {
            deactivate_plugins( plugin_basename( __FILE__ ) );
        }
    }

    /*
     * include necessary files
     */
    require_once(CWPV_PATH.'/vendor/autoload.php');
    require_once(CWPV_PATH_CLASS . '/cwp-vimeo-main.class.php');
    require_once(CWPV_PATH_CLASS . '/cwp-vimeo-pages.class.php');

    /* Intitialize licensing
     * for this plugin.
     */
    if( class_exists( 'Clients_WP_License_Handler' ) ) {
        $cwp_vimeo = new Clients_WP_License_Handler( __FILE__, 'Clients WP - Vimeo', CWPV_VERSION, 'gravity2pdf', null, null, 7561);
    }

    add_action( 'plugins_loaded', array( 'Clients_WP_Vimeo', 'get_instance' ) );
endif;