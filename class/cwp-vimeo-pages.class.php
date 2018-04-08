<?php
class Clients_WP_Vimeo_Pages {

    public function __construct() {
        add_action('admin_init', array( $this, 'settings_options_init' ));
        add_action('admin_menu', array( $this, 'admin_menus'), 12 );
    }

    public function settings_options_init() {
        register_setting( 'cwpvimeo_settings_options', 'cwpvimeo_settings_options', '' );
    }

    public function admin_menus() {
        add_submenu_page ( 'edit.php?post_type=bt_client' , 'Vimeo' , 'Vimeo' , 'manage_options' , 'cwp-vimeo' , array( $this , 'cwp_vimeo' ));
    }

    public function cwp_vimeo() {
        include_once(CWPV_PATH_INCLUDES.'/cwp_vimeo.php');
    }
}

new Clients_WP_Vimeo_Pages();