<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class MW_Class_Management {

    private static $_instance = null;


    public function __construct ( $file, $version = '1.0.1' ){








        // Initalise the other classes we'll be using.

        $this->initalise_classes();
    }

    /**
     * Main MW_Class_Management Instance
     *
     * Ensures only one instance of MW_Class_Management is loaded or can be loaded.
     *
     * @since 1.0.0
     * @static
     * @see Sensei_Content_Drip()
     * @return Main MW_Class_Management
     */
    public static function instance ( $file, $version = '1.0.1' )
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self($file, $version);
        }
        return self::$_instance;

    }


    public function initalise_classes() {

        require_once('class-classm-utils.php');
        require_once('class-classm-classes.php');
        require_once('class-classm-users.php');


        $this->utils = new MWCM_Utils();
        $this->classes = new MWCM_Classes();
        $this->users = new MWCM_Users();


    }









}


















?>