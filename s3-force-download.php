<?php
/**
 * Plugin Name: S3 Force Download
 * Description: Force File Download From Amazon S3 Bucket.
 * Plugin URI: https://github.com/paomancia/s3-force-download
 * Author: Paola Mancía, Applaudo Studios
 * Version: 1.0.0
 * Text Domain: sfd_domain
 * License: GPLv3
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

if ( !defined('ABSPATH') ) {
    exit; // exit if file is accessed directly
}

if ( class_exists( 'S3_Force_Download' ) ) {
    die('ERROR: It looks like you have more than one instance of S3 Force Download installed. Please remove additional instances for this plugin to work again.');
}

/** 
 * ---------------------
 *   TABLE OF CONTENTS
 * ---------------------
 * 1. GLOBAL OPTIONS VARIABLE
 * 2. LOAD AWS SDK
 * 3. CLASS AUTOLOADER
 * 4. CLASS INITIALIZATION
 */

/*
 * 1. GLOBAL OPTIONS VARIABLE
 */
$sfd_options = get_option( 'sfd_settings' );

/*
 * 2. LOAD AWS SDK
 */
require_once plugin_dir_path(__FILE__) . '/includes/vendor/aws/aws-autoloader.php';

/*
 * 3. CLASS AUTOLOADER
 */
require_once plugin_dir_path(__FILE__) . '/includes/autoloader.php';

/*
 * 4. CLASS INITIALIZATION
 */
use SFD\S3_Force_Download_Settings;
use SFD\S3_Force_Download_Shortcodes;

$initClasses = array(
	S3_Force_Download_Settings::class,
	S3_Force_Download_Shortcodes::class,
); 

foreach ( $initClasses as $clazz ) {
	$clazz::get_instance();
} 

