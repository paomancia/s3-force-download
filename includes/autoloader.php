<?php

if ( !defined( 'WPINC' ) || !defined('ABSPATH' ) ) {
    exit;
}

function sfd_autoloader( $class_name ) {
    if ( false !== strpos( $class_name, 'SFD' ) ) {
        $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR;
        $data = explode( "\\", $class_name );
        $class_file = '';
        $maxIt = count( $data );
        for( $i = 0; $i < $maxIt; $i++ ) {
            $slice = $data[$i];
            if( $i + 1 == $maxIt ) {
                $lSlice = str_replace( "_", "-", strtolower( $slice ) );
                $class_file .= "class-$lSlice.php";
            } else {
                $class_file .= $slice . DIRECTORY_SEPARATOR;
            }
        }
        require_once $classes_dir . $class_file;
    }
}

spl_autoload_register( 'sfd_autoloader' ); // Register autoloader