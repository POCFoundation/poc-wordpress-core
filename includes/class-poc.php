<?php

class POC
{
    protected static $instance = null;

    public static function instance() {
        if ( is_null( self::$instance ) ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->define_constants();
        $this->includes();
        $this->init_hooks();
    }

    private function define_constants()
    {
        $this->define( 'POC_ABSPATH', dirname( POC_PLUGIN_FILE ) . '/' );
    }

    private function includes()
    {
        include_once POC_ABSPATH . 'includes/class-poc-api.php';
    }

    private function init_hooks()
    {
        add_action( 'plugins_loaded', array( $this, 'on_plugins_loaded' ) );
        add_action( 'admin_init', array( $this, 'on_admin_init' ) );
    }

    public function on_plugins_loaded()
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        if ( current_user_can( 'manage_network ' ) && ! is_plugin_active( 'poc-pro/poc-pro.php' ) ) {
            add_action('admin_notices', function() {
                ob_start(); ?>
                <style>
                    .poc-notice {
                        border: none;
                        border-radius: 4px;
                        padding: 12px;
                        font-size: 15px;
                    }

                    .poc-notice h2 {
                        margin-top: 0;
                        margin-bottom: .5em;
                    }

                    .poc-notice p {
                        margin-top: 0;
                        font-size: 15px;
                    }

                    .poc-notice p:last-child {
                        margin-bottom: 0;
                    }
                </style>
                <div class="notice poc-notice">
                    <h2>You’re almost there</h2>
                    <p>Install and active POC Pro plugin to manage sites, create landing page and a lot more!</p>
                    <form action="" method="POST">
                        <?php wp_nonce_field( 'poc_install_pro_version', 'poc_install_pro_version' ); ?>
                        <p><input class="button button-primary" type="submit" value="Install & Activate"> <a href="" class="button">Learn more</a></p>
                    </form>
                </div>
                <?php echo ob_get_clean();
            } );
        }
    }

    public function on_admin_init()
    {
        if ( isset( $_POST['poc_install_pro_version'] ) && wp_verify_nonce( $_POST['poc_install_pro_version'], 'poc_install_pro_version' ) ) {
            $api = new POC_API();
            $plugin = $api->get_pro_version();

            if( ! is_null( $plugin ) ) {
                include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                include_once ABSPATH . 'wp-admin/includes/class-wp-ajax-upgrader-skin.php';
                include_once ABSPATH . 'wp-admin/includes/class-plugin-upgrader.php';

                $skin = new WP_Ajax_Upgrader_Skin();

                $upgrader = new Plugin_Upgrader( $skin );

                $upgrader->install( $plugin['download_link'] );

                activate_plugin( 'poc-pro/poc-pro.php', '', true );

                header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
                exit;
            }
        }
    }

    private function define($name, $value) {
        if ( ! defined( $name ) ) {
            define( $name, $value );
        }
    }
}