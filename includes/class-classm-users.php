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

        if (!current_user_can('manage_options')) {

            return;

        }

        $class = get_user_meta($user->ID, '_class', true);

        echo "<h3>Class Management</h3>";

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

                if ($class == $post->post_name) {

                    echo '<option selected="selected" value="'.$post->post_name.'">'.$post->post_title.'</option><br />';

                } else {

                    echo '<option value="' . $post->post_name . '">' . $post->post_title . '</option><br />';

                }

            }

            echo "</select>";
        }

    }


    function save_user_class_terms ($user_id) {

        $key = esc_attr( $_POST['class'] );

        update_user_meta( $user_id, '_class', $key);



    }

    function update_class_count() {



    }




}

?>