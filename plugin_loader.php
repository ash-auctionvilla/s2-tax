<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

if (file_exists( __DIR__.'/vendor/autoload.php' ))
    require __DIR__.'/vendor/autoload.php';

require_once( plugin_dir_path( __FILE__ ) . '/inc/class.s2billing.php' );



add_action( 'plugins_loaded', function () {
    s2Billing_hacks::get_instance();

} );

?>
