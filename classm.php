<?php

if ( ! defined( 'ABSPATH' ) ) exit;

/*
Plugin Name: MW Class Management
Plugin URI:
Description: Trying to write a Sensei class management extension.
Version:     0.1
Author:      Mahangu Weerasinghe
Author URI:  http://mahangu.wordpress.com
License: GPL v2

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

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