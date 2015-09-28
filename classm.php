<?php
/*
Plugin Name: Class Management
Plugin URI:
Description: Trying to write a Sensei class management extension.
Version:     0.1
Author:      Mahangu Weerasinghe
Author URI:  http://mahangu.wordpress.com
License: GPL v2

 */

add_action( 'init', 'classm_setup_class_post_type' );
add_action( 'init', 'classm_setup_user_taxonomies' );


add_action("add_meta_boxes", "classm_admin_meta_boxes");


add_action( 'show_user_profile', 'classm_edit_user_class_section' );
add_action( 'edit_user_profile', 'classm_edit_user_class_section' );

add_action( 'personal_options_update', 'classm_save_user_class_terms' );
add_action( 'edit_user_profile_update', 'classm_save_user_class_terms' );


add_filter( 'manage_edit-class_columns', 'classm_edit_class_columns' ) ;

add_action( 'manage_class_posts_custom_column', 'classm_manage_class_columns', 10, 2 );


function classm_manage_class_columns ( $column, $post_id )
{
    global $post;

    switch ($column) {

        case 'teachers' :

            $post = get_post($post_id);

            $users = get_users(array(

            'meta_key'  => '_class',
            'role'      => 'teacher'));


            foreach ($users as $user) {

                $class = get_user_meta($user->ID, '_class', true);

//                var_dump($class);

                if ($post->post_name == $class) {

                    echo '<a href="' . get_edit_user_link($user->ID) . '">' . $user->user_login . "</a>, ";

                }
            }

            break;




        case 'students' :

            $post = get_post($post_id);

            $users = get_users(array('

            meta_key' => '_class'));


            foreach ($users as $user) {

                $class = get_user_meta($user->ID, '_class', true);

//                var_dump($class);

                if ($post->post_name == $class && !in_array('teacher',$user->roles)) {

                    echo '<a href="' . get_edit_user_link($user->ID) . '">' . $user->user_login . "</a>, ";

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

            $cid = super_unique_array($course_ids);

            $all_ids = [];

            foreach ($cid as $id) {

                $post = get_post($id);

                echo '<a href="' . get_edit_post_link($post->ID) . '">' . $post->post_title . "</a>, ";

            }

        break;

    }

}

function super_unique_array($array)
{
    $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

    foreach ($result as $key => $value)
    {
        if ( is_array($value) )
        {
            $result[$key] = super_unique($value);
        }
    }

    return $result;

}


function classm_edit_class_columns( $columns ) {

    $columns = array(
        'cb' => '<input type="checkbox" />',
        'title' => __( 'Class' ),
        'teacher' => __( 'Teacher' ),
        'students' => __( 'Students' ),
        'courses' => __( 'Courses' ),
        'avgrade' => __( 'Average Grade' )
    );

    return $columns;
}

function classm_setup_class_post_type() {

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


function classm_setup_user_taxonomies()
{
    $labels = array(
        'name'                           => 'Classes',
        'singular_name'                  => 'Class',
        'search_items'                   => 'Search Classes',
        'all_items'                      => 'All Classs',
        'edit_item'                      => 'Edit Class',
        'update_item'                    => 'Update Class',
        'add_new_item'                   => 'Add New Class',
        'new_item_name'                  => 'New Class Name',
        'menu_name'                      => 'Class',
        'view_item'                      => 'View Class',
        'popular_items'                  => 'Popular Class',
        'separate_items_with_commas'     => 'Separate Classes with commas',
        'add_or_remove_items'            => 'Add or remove Classes',
        'choose_from_most_used'          => 'Choose from the most used Classes',
        'not_found'                      => 'No Classes found'
    );

    $capabilities = array(
    'manage_terms' => 'edit_users', // Using 'edit_users' cap to keep this simple.
    'edit_terms'   => 'edit_users',
    'delete_terms' => 'edit_users',
    'assign_terms' => 'read',
    );

    register_taxonomy(
        'class',
        'user',
        array(
            'label' => __( 'Class' ),
            'hierarchical' => false,
            'labels' => $labels,
            'capabilities' => $capabilities,
            'update_count_callback' => 'classm_update_class_count' // Use a custom function to update the count.
        )
    );


    $args = array(
        'orderby'          => 'date',
        'order'            => 'DESC',
        'post_type'        => 'class',
        'post_status'      => 'publish',
        'suppress_filters' => true
    );

    $posts = get_posts( $args );



}


function classm_edit_user_class_section ($user) {


    echo "<h3>Class Management</h3>";

    $tax = get_taxonomy('class');

    /* Make sure the user can assign terms of the class taxonomy before proceeding. */
    if (!current_user_can($tax->cap->assign_terms)) {
        return;

    }

    /* Get the terms of the 'class' taxonomy. */
    $terms = get_terms('class', array('hide_empty' => false));

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
            //echo $term->name;
            echo '<option value="'.$post->post_name.'">'.$post->post_title.'</option><br />';
;
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
    echo '<ol>';
/*
    $blogusers = get_users();
    foreach ($blogusers as $user) {
        echo '<li>' . esc_html($user->user_nicename) . '</li>';
    }

    echo '</ol>';*/

    $users = get_users();

    foreach ($users as $user) {

        $class = get_user_meta($user->ID, '_class', true);

        if ($class == $post->post_name && !in_array('teacher', $user->roles) ) {

            print $user->user_login.'<br />';

        }
    }

}

function classm_admin_teachers_meta_box_markup()
{

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

?>