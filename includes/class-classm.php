<?php

if ( ! defined( 'ABSPATH' ) ) exit;


class MW_Class_Management {

    private static $_instance = null;


    public function __construct ( $file, $version = '1.0.1' ){

        add_action( 'init', array( $this, 'classm_setup_class_post_type') );
        add_action( 'init', array( $this, 'classm_setup_user_taxonomies') );


        add_action("add_meta_boxes", array( $this, 'classm_admin_meta_boxes'));


        add_action( 'show_user_profile', array( $this, 'classm_edit_user_class_section' ));
        add_action( 'edit_user_profile', array( $this, 'classm_edit_user_class_section' ));

        add_action( 'personal_options_update', array( $this, 'classm_save_user_class_terms' ));
        add_action( 'edit_user_profile_update', array( $this, 'classm_save_user_class_terms' ));


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


        $this->utils = new MWCM_Utils();
        $this->classes = new MWCM_Classes();


    }



    public function classm_setup_class_post_type() {

        register_post_type( 'class',
            array(
                'labels' => array(
                    'name' => __( 'Classes' ),
                    'singular_name' => __( 'Class' )
                ),
                'public' => true,
                'has_archive' => true,
                'menu_icon'   => 'dashicons-groups',
                'supports' => array( 'title'),
            )
        );



    }


    function classm_setup_class_taxonomies()
    {


        register_taxonomy( 'level', 'class', $args );


    }



    function classm_edit_user_class_section ($user) {


        echo "<h3>Class Management</h3>";

        $tax = get_taxonomy('class');

        /* Make sure the user can assign terms of the class taxonomy before proceeding. */
//        if (!current_user_can($tax->cap->assign_terms)) {
//
//            return;
//
//        }

        $args = array(
            'orderby'          => 'date',
            'order'            => 'DESC',
            'post_type'        => 'class',
            'post_status'      => 'publish',
            'suppress_filters' => true
        );

        $posts = get_posts( $args );

        if (!empty($posts)) {

            echo '<select name="class">';

            foreach ($posts as $post) {

                echo '<option value="'.$post->post_name.'">'.$post->post_title.'</option><br />';
                var_dump($post);

            }

            echo "</select>";
        }

    }


    function classm_save_user_class_terms ($user_id) {

        $key = esc_attr( $_POST['class'] );

        update_user_meta( $user_id, '_class', $key);



    }

    function classm_update_class_count() {



    }


    function classm_admin_meta_boxes()
    {
        add_meta_box("classm_students", "Students", "classm_admin_students_meta_box_markup", "class", "normal", "high", null);
        add_meta_box("classm_teachers", "Teachers", "classm_admin_teachers_meta_box_markup", "class", "normal", "high", null);
    }

    function classm_admin_students_meta_box_markup($post)
    {

        $users = get_users();

        foreach ($users as $user) {

            $class = get_user_meta($user->ID, '_class', true);

            if ($class == $post->post_name && !in_array('teacher', $user->roles) ) {

                print $user->user_login.'<br />';

            }
        }

    }

    function classm_admin_teachers_meta_box_markup() {

        echo '<ol>';

        $users = get_users('role=teacher');
        foreach ($users as $user) {

            $class = get_user_meta($user->ID, '_class', true);

            if ($class == $post->post_name) {

                print $user->user_login.'<br />';

            }
        }

        echo '</ol>';

    }

}


















?>