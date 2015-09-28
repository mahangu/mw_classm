<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*
Plugin Name: Class Management
Plugin URI:
Description: Trying to write a Sensei class management extension.
Version:     0.1
Author:      Mahangu Weerasinghe
Author URI:  http://mahangu.wordpress.com
License: GPL v2

 */

if ( ! function_exists( 'woothemes_queue_update' ) ) {
    require_once( 'includes/woo-includes/woo-functions.php' );
}

/**
 * Functions used by plugins
 */
if ( ! class_exists( 'WooThemes_Sensei_Dependencies' ) ) {
    require_once 'includes/woo-includes/class-woothemes-sensei-dependencies.php';
}

/**
 * Sensei Detection
 */
if ( ! function_exists( 'is_sensei_active' ) ) {
    function is_sensei_active() {
        return WooThemes_Sensei_Dependencies::sensei_active_check();
    }
}


require_once('includes/class-classm.php');


function MW_Class_Management() {

    return MW_Class_Management::instance(__FILE__, '1.0.3');
}

    // load this plugin only after sensei becomes available globaly

add_action('plugins_loaded', 'MW_Class_Management');




?>