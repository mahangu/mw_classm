<?php

if ( ! defined( 'ABSPATH' ) ) exit;

Class MWCM_Users
{

    public function __construct()
    {

        add_action( 'show_user_profile', array( $this, 'edit_user_class_section' ));
        add_action( 'edit_user_profile', array( $this, 'edit_user_class_section' ));

        add_action( 'personal_options_update', array( $this, 'save_user_class_terms' ));
        add_action( 'edit_user_profile_update', array( $this, 'save_user_class_terms' ));


    }


    function edit_user_class_section ($user) {


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


    function save_user_class_terms ($user_id) {

        $key = esc_attr( $_POST['class'] );

        update_user_meta( $user_id, '_class', $key);



    }

    function classm_update_class_count() {



    }




}

?>