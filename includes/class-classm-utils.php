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

}


?>