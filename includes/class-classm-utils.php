<?php

if ( ! defined( 'ABSPATH' ) ) exit;

Class MWCM_Utils {

    function super_unique_array($array)
    {
        $result = array_map("unserialize", array_unique(array_map("serialize", $array)));

        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = super_unique($value);
            }
        }

        return $result;

    }


    // Helper function that generates a link to the user's profile.

    function generate_user_link($user, $list = false) {

        if ($list) {

            return '<li><a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name . "</a></li>";

        } else {

            return '<a href="' . get_edit_user_link($user->ID) . '">' . $user->display_name . "</a>, ";

        }

    }

}


?>