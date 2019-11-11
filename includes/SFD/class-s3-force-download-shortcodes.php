<?php
/**
 * Class to add shortcodes for s3-force-download
 *
 * @package s3-force-download
 */

namespace SFD;

use SFD\S3_Force_Download;

if( !defined( 'ABSPATH' ) ) {
	exit;
}

class S3_Force_Download_Shortcodes {

    /**
	 * Class instance
	 *
	 * @var S3_Force_Download_Shortcodes
	 * @access private
	 */
    private static $instance = null;

    /**
	 * Get class instance
	 *
	 * @return S3_Force_Download_Shortcodes
	 * @static
	 */
	public static function get_instance() {
		if ( !isset( self::$instance ) ) {
			self::$instance = new self;
        }
        
		return self::$instance;
    }

    /**
	 * Constructor
	 *
	 * @access private
	 */
    private function __construct() {
        add_shortcode( 'sfd_page', array( $this, 'add_sfd_page_shortcode') );
        add_shortcode( 'sfd_link', array( $this, 'add_sfd_link_shortcode') );
    }

    /**
	 * Shortcode to convert a page into the download page
     * 
	 * @return void
     * @access public
	 */
    public function add_sfd_page_shortcode() {

        S3_Force_Download::get_instance(); 
    } 

    /**
	 * Shortcode to dynamically generate a link with the necessary info. Required attributes: page_slug and file_id
	 *
	 * @param array $atts: attributes this shortcode supports. List of params: page_slug, file_id, classes, data_attr, data_val
     * @param string $content: text or html
	 * @return string
     * @access public
	 */
    public function add_sfd_link_shortcode( $atts = array(), $content = null ) {
        extract(
            shortcode_atts( 
                array(
                    'page_slug'  => 'download',
                    'file_id'    => '',
                    'classes'    => '',
                    'data_attr'  => '',
                    'data_val'   => ''
                ), 
                $atts
            )
        );

        $link = add_query_arg( 
            array(
                'file_id' => $file_id,
                'action'  => 'sfd_download_file',
                'nonce'    => wp_create_nonce( 'sfd_download_file_' ) 
            ), 
            home_url() . '/' . $page_slug 
        );

        return '<a href="' . esc_url( $link ) . '" class="' . esc_attr( $classes ) . '" data-' . $data_attr . '="' . esc_attr( $data_val ) . '" download>' . $content . '</a>';
    }
}