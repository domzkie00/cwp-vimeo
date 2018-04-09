<?php if ( ! defined( 'ABSPATH' ) ) exit;
session_start();

use Vimeo\Vimeo;

class Clients_WP_Vimeo{
    
    private static $instance;

    public static function get_instance()
    {
        if( null == self::$instance ) {
            self::$instance = new Clients_WP_Vimeo();
        }

        return self::$instance;
    }

    function __construct(){
        add_action('admin_init', array($this, 'register_integration'));
        add_action('admin_init', array($this, 'get_access_token'));
        add_action('admin_init', array($this, 'get_vimeo_user_credentials'));
        add_action('admin_enqueue_scripts', array( $this, 'cwp_vimeo_add_admin_scripts' ));
        add_action('wp_enqueue_scripts', array($this, 'cwp_vimeo_add_wp_scripts'), 20, 1);
        add_filter('the_content', array($this, 'folder_content_table'), 6);
        add_action('wp_ajax_vimeo_list_albums', array($this, 'vimeo_list_albums'));
        if(isset($_SESSION['vimeo_error_msg'])) {
            $this->vimeo_error($_SESSION['vimeo_error_msg']);
            unset($_SESSION['vimeo_error_msg']);
        }
    }

    public function cwp_vimeo_add_admin_scripts() {
        wp_register_script('cwp_vimeo_admin_scripts', CWPV_URL . '/assets/js/cwp-vimeo-admin-scripts.js', '1.0', true);
        $cwpv_admin_script = array(
            'ajaxurl' => admin_url( 'admin-ajax.php' )
        );
        wp_localize_script('cwp_vimeo_admin_scripts', 'cwpv_admin_script', $cwpv_admin_script );
        wp_enqueue_script('cwp_vimeo_admin_scripts');
    }

    public function cwp_vimeo_add_wp_scripts() {
        wp_register_script('cwp_vimeo_wp_scripts', CWPV_URL . '/assets/js/cwp-vimeo-scripts.js', '1.0', true);
        wp_enqueue_script('cwp_vimeo_wp_scripts');
    }

    public function register_integration($array) {
        $vimeo = array(
            'vimeo' => array(
                'key'       => 'vimeo',
                'label'     => 'Vimeo'
            )
        );

        $clients_wp_integrations = get_option('clients_wp_integrations');

        if(is_array($clients_wp_integrations)) {
            $merge_integrations = array_merge($clients_wp_integrations, $vimeo);
            update_option('clients_wp_integrations', $merge_integrations);
        } else {
            update_option('clients_wp_integrations', $vimeo);
        }
        
    }

    public function get_access_token(){
        if (isset($_REQUEST['cwpintegration']) && $_REQUEST['cwpintegration'] == 'vimeo' ):
            if (!isset($_REQUEST['vimeouser'])):
                $cwpvimeo_settings_options = get_option('cwpvimeo_settings_options');
                $app_key    = isset($cwpvimeo_settings_options['app_key']) ? $cwpvimeo_settings_options['app_key'] : '';
                $app_secret = isset($cwpvimeo_settings_options['app_secret']) ? $cwpvimeo_settings_options['app_secret'] : '';

                if(!empty($app_key) && !empty($app_secret)) {
                    $vimeo = new Vimeo($app_key, $app_secret);
                    $result = $vimeo->clientCredentials();

                    if(isset($result['body']['error'])) {
                        $_SESSION['vimeo_error_msg'] = $result['body']['error'];
                        $cwpvimeo_settings_options['user_credentials'] = '';
                        $cwpvimeo_settings_options['app_token'] = '';
                        $cwpvimeo_settings_options['user_id'] = '';
                    } else {
                        $cwpvimeo_settings_options['app_token'] = $result['body']['access_token'];
                    }

                    update_option( 'cwpvimeo_settings_options', $cwpvimeo_settings_options );
                    header('Location: ' . admin_url( 'edit.php?post_type=bt_client&page=cwp-vimeo' ));
                }
            endif;
        endif;
    }

    public function get_vimeo_user_credentials(){
        if (isset($_REQUEST['cwpintegration']) && $_REQUEST['cwpintegration'] == 'vimeo' ):
            if (isset($_REQUEST['vimeouser'])):
                $cwpvimeo_settings_options = get_option('cwpvimeo_settings_options');
                $app_key    = isset($cwpvimeo_settings_options['app_key']) ? $cwpvimeo_settings_options['app_key'] : '';
                $app_secret = isset($cwpvimeo_settings_options['app_secret']) ? $cwpvimeo_settings_options['app_secret'] : '';
                $app_token  = isset($cwpvimeo_settings_options['app_token']) ? $cwpvimeo_settings_options['app_token'] : '';
                $user_id  = isset($cwpvimeo_settings_options['user_id']) ? $cwpvimeo_settings_options['user_id'] : '';

                if(!empty($app_key) && !empty($app_secret) && !empty($app_token) && !empty($user_id)) {
                    $vimeo = new Vimeo($app_key, $app_secret);
                    $result = $vimeo->request('/users/'.$user_id);

                    if(isset($result['body']['error'])) {
                        $_SESSION['vimeo_error_msg'] = $result['body']['error'];
                        $cwpvimeo_settings_options['user_credentials'] = '';
                    } else {
                        unset($_SESSION['vimeo_error_msg']);
                        $cwpvimeo_settings_options['user_credentials'] = serialize($result['body']);
                    }

                    update_option( 'cwpvimeo_settings_options', $cwpvimeo_settings_options );
                    header('Location: ' . admin_url( 'edit.php?post_type=bt_client&page=cwp-vimeo' ));
                }
            endif;
        endif;
    }

    public function vimeo_error($msg) {
        ?>
        <div class="notice error is-dismissible" >
            <p><b>Vimeo Error:</b> <?php _e( $msg, 'my-text-domain' ); ?></p>
        </div>
        <?php
    }

    public function vimeo_list_albums() {
        $cwpvimeo_settings_options = get_option('cwpvimeo_settings_options');
        $app_key    = isset($cwpvimeo_settings_options['app_key']) ? $cwpvimeo_settings_options['app_key'] : '';
        $app_secret = isset($cwpvimeo_settings_options['app_secret']) ? $cwpvimeo_settings_options['app_secret'] : '';
        $app_token  = isset($cwpvimeo_settings_options['app_token']) ? $cwpvimeo_settings_options['app_token'] : '';
        $user_id  = isset($cwpvimeo_settings_options['user_id']) ? $cwpvimeo_settings_options['user_id'] : '';
        $user_credentials  = isset($cwpvimeo_settings_options['user_credentials']) ? $cwpvimeo_settings_options['user_credentials'] : '';

        if(!empty($app_key) && !empty($app_secret) && !empty($app_token) && !empty($user_id) && !empty($user_credentials)) {
            $vimeo = new Vimeo($app_key, $app_secret);
            $result = $vimeo->request('/users/'.$user_id.'/albums');
            echo json_encode($result);
        } else {
            echo null;
        }

        die();
    }

    public function folder_content_table() {
        global $pages;

        foreach($pages as $page) {
            $page_content = nl2br($page);
            if (strpos($page, '[cwp_') !== FALSE) {
                $args = array(
                    'meta_key' => '_clients_page_shortcode',
                    'meta_value' => $page,
                    'post_type' => 'bt_client_page',
                    'post_status' => 'any',
                    'posts_per_page' => -1
                );
                $posts = get_posts($args);

                foreach($posts as $post) {
                    echo $post->post_content;

                    $integration = get_post_meta($post->ID, '_clients_page_integration', true);
                    $root_folder = get_post_meta($post->ID, '_clients_page_integration_folder', true);

                    if (isset($integration) && isset($root_folder)) {
                        if((!empty($integration) && $integration == 'vimeo') && !empty($root_folder)) {
                            $cwpvimeo_settings_options = get_option('cwpvimeo_settings_options');
                            $app_key    = isset($cwpvimeo_settings_options['app_key']) ? $cwpvimeo_settings_options['app_key'] : '';
                            $app_secret = isset($cwpvimeo_settings_options['app_secret']) ? $cwpvimeo_settings_options['app_secret'] : '';
                            $user_id  = isset($cwpvimeo_settings_options['user_id']) ? $cwpvimeo_settings_options['user_id'] : '';

                            $linked_client_id = get_post_meta($post->ID, '_clients_page_client', true);
                            $client_email = get_post_meta($linked_client_id, '_bt_client_group_owner', true);

                            if(is_user_logged_in()) {
                                $current_user = wp_get_current_user();
                                if(!current_user_can('administrator')) {
                                    if($current_user->user_email != $client_email) {
                                        echo 'You are not allowed to see this contents.';
                                        return;
                                    }
                                } else {
                                    if($current_user->user_email != $client_email) {
                                        echo 'You are not allowed to see this contents.';
                                        return;
                                    }
                                }
                            } else {
                                echo 'You are not allowed to see this contents.';
                                return;
                            }

                            if(!empty($app_key) && !empty($app_secret)) {
                                $vimeo = new Vimeo($app_key, $app_secret);
                                $result = $vimeo->request('/users/'.$user_id.'/albums/'.$root_folder.'/videos');

                                ob_start();
                                include_once(CWPV_PATH_INCLUDES . '/cwp-vimeo-table.php');
                                $page_content .= ob_get_clean();
                            }
                        }
                    }
                }
            }

            return $page_content;
        }
    } 
}