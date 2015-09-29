<?php

if ( ! defined( 'ABSPATH' ) ) exit;

Class MWCM_Classes {

    public function __construct() {

        // Setup Class CPT
        add_action( 'init', array( $this, 'setup_class_post_type') );

        //Create and manage columns of Class CPT
        add_filter( 'manage_edit-class_columns', array( $this, 'edit_class_columns' )) ;
        add_action( 'manage_class_posts_custom_column', array( $this, 'manage_class_columns'), 10, 2 );

        //Add Metaboxes to Class CPT post screen
        add_action("add_meta_boxes", array( $this, 'add_admin_meta_boxes'));

    }


    public function setup_class_post_type() {

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


    public function setup_class_taxonomies()
    {


        register_taxonomy( 'level', 'class', $args );


    }

    public function manage_class_columns ( $column, $post_id ) {

        global $post;

        switch ($column) {

            case 'teachers' :

                $post = get_post($post_id);

                $users = get_users(array(

                    'meta_key' => '_class'));

                foreach ($users as $user) {

                    $class = get_user_meta($user->ID, '_class', true);

                    if ($post->post_name == $class && in_array('teacher',$user->roles)) {

                        echo '<a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name . "</a>, ";

                    }
                }

                break;




            case 'students' :

                $post = get_post($post_id);

                $users = get_users(array(

                    'meta_key' => '_class'));

                foreach ($users as $user) {

                    $class = get_user_meta($user->ID, '_class', true);

                    if ($post->post_name == $class && !in_array('teacher',$user->roles)) {

                        echo '<a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name. "</a>, ";

                    }
                }

                break;


            case 'courses':

                $users = get_users(array('

            meta_key' => '_class'));


                $course_ids = array();

                foreach ($users as $user) {

                    $course_statuses = WooThemes_Sensei_Utils::sensei_check_for_activity(array('user_id' => $user->ID, 'type' => 'sensei_course_status'), true);

                    $class = get_user_meta($user->ID, '_class', true);

                    if ($class == $post->post_name) {

                        // User may only be on 1 Course
                        if (!is_array($course_statuses)) {
                            $course_statuses = array($course_statuses);
                        }

                        $completed_ids = $active_ids = array();


                        foreach ($course_statuses as $course_status) {

                            if (WooThemes_Sensei_Utils::user_completed_course($course_status, $user->ID)) {
                                $completed_ids[] = $course_status->comment_post_ID;

                                foreach ($completed_ids as $compid) {

                                    //array_push($course_ids, $compid);

                                }

                            } else {

                                $active_ids[] = $course_status->comment_post_ID;

                                foreach ($active_ids as $actid) {

                                    $course_ids[] = intval($actid);


                                }

                            }

                        }


                    }


                }

                $cid =  MW_Class_Management()->utils->super_unique_array($course_ids);

                $all_ids = [];

                foreach ($cid as $id) {

                    $post = get_post($id);

                    echo '<a href="' . get_edit_post_link($post->ID) . '">' . $post->post_title . "</a>, ";

                }

                break;

        }

    }


    public function edit_class_columns( $columns ) {

        $columns = array(
            'cb' => '<input type="checkbox" />',
            'title' => __( 'Class' ),
            'teachers' => __( 'Teacher' ),
            'students' => __( 'Students' ),
            'courses' => __( 'Courses' ),
            'avgrade' => __( 'Average Grade' )
        );

        return $columns;
    }




    public function add_admin_meta_boxes()
    {
        add_meta_box("classm_students", "Students", array ($this, "admin_students_meta_box_markup"), "class", "normal", "high", null);
        add_meta_box("classm_teachers", "Teachers", array ($this, "admin_teachers_meta_box_markup"), "class", "normal", "high", null);
    }


    public function admin_students_meta_box_markup($post)
    {

        $users = get_users();

        echo "<ol>";

        foreach ($users as $user) {

            $class = get_user_meta($user->ID, '_class', true);

            if ($class == $post->post_name && !in_array('teacher', $user->roles) ) {

                echo '<li><a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name. "</a></li> ";

            }
        }

        echo "</ol>";

    }

    public function admin_teachers_meta_box_markup($post) {

        echo '<ol>';

        $users = get_users(array(

            'meta_key' => '_class'));

        foreach ($users as $user) {

            $class = get_user_meta($user->ID, '_class', true);

            if ($post->post_name == $class && in_array('teacher',$user->roles)) {

                echo '<li><a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name. "</a></li> ";

            }
        }

        echo '</ol>';

    }



}


?>