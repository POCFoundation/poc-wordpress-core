<?php
/**
 * Plugin Name: POC
 * Text Domain: poc
 */

defined( 'ABSPATH' ) || exit;

if( ! defined( 'POC_PLUGIN_FILE' ) ) {
    define( 'POC_PLUGIN_FILE', __FILE__ );
}

if( ! class_exists( 'POC' ) ) {
    include_once dirname( POC_PLUGIN_FILE ) . '/includes/class-poc.php';
}

function POC() {
    return POC::instance();
}

POC();