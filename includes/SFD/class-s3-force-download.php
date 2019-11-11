<?php
/**
 * Class to make a connection to an S3 bucket and force files to download.
 *
 * @package s3-force-download
 * @link https://docs.aws.amazon.com/aws-sdk-php/v2/guide/installation.html
 */

namespace SFD;

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

use Aws\S3\S3Client;
use Aws\S3\Exception\S3Exception;

class S3_Force_Download {

    const FILE_ID        = 'file_id';
    const BUCKET_NAME    = 'bucket_name';
    const BUCKET_REGION  = 'bucket_region';
    const ACCESS_KEY     = 'access_key';
    const SECRET_KEY     = 'secret_key';

    /**
	 * Class instance
	 *
	 * @var S3_Force_Download
	 * @access private
	 */
    private static $instance = null;

    /**
	 * Get class instance
	 *
	 * @return S3_Force_Download
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
        $this->sfd_start_download_request();
    }

    /**
	 * Redirect to referer page or homepage if download page was accessed directly
	 *
     * @return void
	 * @access public
	 */
    public function sfd_redirect_to_referer() {

        global $_SERVER;

        $location = $_SERVER['HTTP_REFERER'];
        header( 'Content-type: application/html; charset=utf-8' );

        if ( $location ) {
            wp_safe_redirect( $location );
        } else {
            wp_safe_redirect( esc_url( home_url() ) );
        }
        exit;
    } 

    /**
     * Find the correct file's content type
     * 
     * @param string $ext: the file's extension 
     * @return string the content type
     * @access public
     */
    public function sfd_get_content_type( $ext ) {
        $content_type = '';

        // check filetype
        switch ( $ext ) {
            case 'png': 
                $content_type='image/png'; 
                break;
            case 'gif': 
                $content_type='image/gif'; 
                break;
            case 'tiff': 
                $content_type='image/tiff'; 
                break;
            case 'jpeg': 
            case 'jpg':
                $content_type='image/jpg'; 
                break;
            case 'pdf': 
                $content_type='application/pdf'; 
                break;
            case 'mp3': 
                $content_type='audio/mpeg'; 
                break;
            case 'mp4': 
                $content_type='video/mp4'; 
                break;
            case 'webm': 
                $content_type='video/webm'; 
                break;
            case 'ogg': 
                $content_type='video/ogg'; 
                break;
            default: 
                $content_type='application/force-download';
        }

        return $content_type;
    }

    /**
     * Force download of given file
     * 
     * @param string[] $file_info: contains the file's url, name & extension 
     * @return void
     * @access public
     */
    public function sfd_force_s3_file_download( $file_info ) {

        global $sfd_options;

        $content_type = $this->sfd_get_content_type( $file_info['ext'] ); // get content type
        $s3_path      = $sfd_options['file_path'] ? trim( $sfd_options['file_path'] ): 'wp-content';
        // get keyname from the path for attachments to be offloaded to in the bucket
        preg_match( '/' . $s3_path . '(.+)/', $file_info['url'], $keyname_matches );
        $keyname      =  (string) $keyname_matches[0] ;
        
        // connect to S3 bucket
        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $sfd_options[ self::BUCKET_REGION ] ? trim( $sfd_options[ self::BUCKET_REGION ] ) : $_ENV['S3_BUCKET_REGION'],
            'credentials' => [
                'key'     => $sfd_options[ self::ACCESS_KEY ] ? trim( $sfd_options[ self::ACCESS_KEY ] ) : $_ENV['S3_ACCESS_KEY'],
                'secret'  => $sfd_options[ self::SECRET_KEY ] ? trim( $sfd_options[ self::SECRET_KEY ] ) : $_ENV['S3_SECRET_KEY'],
            ],
        ]);
        
        // get requested file and force download
        try {
            $cmd = $s3->getCommand( 'GetObject', [
                'Bucket'                      => $sfd_options[ self::BUCKET_NAME ] ? trim( $sfd_options[ self::BUCKET_NAME ] ) : $_ENV['S3_BUCKET_NAME'],
                'Key'                         => $keyname,
                'ResponseContentDisposition'  => "attachment; filename=\"{$file_info['name']}\"",
                'ResponseContentType'         => "{$content_type}",
            ]);
    
            $signed_url = $s3->createPresignedRequest( $cmd, '+15 minutes' )
                        ->getUri()
                        ->__toString();
            
            header( "Location: {$signed_url}" );  
            header( 'Content-Description: File Transfer' );
            exit;
            
        } catch ( S3Exception $e ) {
            echo 'There was an error downloading the file. ' . $e;
        }
    }

    /**
     * Sanitize vars, check all required info exists and verify user
     * 
     * @return boolean
     * @access public
     */
    public function sfd_vars_and_security_check() {

        global $sfd_options;

        $file_id_exists       = filter_input( INPUT_GET, self::FILE_ID, FILTER_SANITIZE_STRING );
        $action_exists        = filter_input( INPUT_GET, 'action', FILTER_SANITIZE_STRING ) && $_GET['action'] === 'sfd_download_file';
        $nonce_exists         = filter_input( INPUT_GET, 'nonce', FILTER_SANITIZE_STRING );
        $nonce_is_verified    = wp_verify_nonce( $nonce_exists, 'sfd_download_file_' );
        $bucket_name_exists   = $sfd_options[ self::BUCKET_NAME ] || $_ENV['S3_BUCKET_NAME'];
        $bucket_region_exists = $sfd_options[ self::BUCKET_REGION ] || $_ENV['S3_BUCKET_REGION'];
        $access_key_exists    = $sfd_options[ self::ACCESS_KEY ] || $_ENV['S3_ACCESS_KEY'];
        $secret_key_exists    = $sfd_options[ self::SECRET_KEY ] || $_ENV['S3_SECRET_KEY'];
        // check that all vars are set
        $info_list            = array( $file_id_exists, $action_exists, $nonce_exists, $nonce_is_verified, $bucket_name_exists, $bucket_region_exists, $access_key_exists, $secret_key_exists );
        $all_info_exists      = array_reduce( $info_list, function( $x, $y ) { return $x && $y; }, true );

        if ( $all_info_exists ) {
            return true;
        }

        return false;
    }       
           
    /**
     * Gather and check file information before forcing download
     * 
     * @return void
     * @access public
     */
    public function sfd_start_download_request() {

        global $sfd_options;

        $required_info_exists = $this->sfd_vars_and_security_check();

        if ( $required_info_exists ) {
            $file_id    = (int) filter_input( INPUT_GET, self::FILE_ID, FILTER_SANITIZE_STRING );
            $file_url   = (string) wp_get_attachment_url( $file_id ); // get file url
        
            if ( !$file_url ) {
                $this->sfd_redirect_to_referer();
            }

            $file_clean_url      = stripslashes( trim( $file_url ) );
            $file_name           = basename( $file_url );
            $file_name_lowercase = strtolower( $file_clean_url );
            $file_extension      = pathinfo( $file_name ); 
            $whitelist           = $sfd_options['file_ext'] ?  array_map( 'trim', explode( ',', $sfd_options['file_ext'] ) ) : array( 'pdf', 'mp4', 'mp3', 'jpg', 'png' ); // allowed file extensions

            if ( !in_array( end( explode( '.', $file_name_lowercase ) ), $whitelist ) || strpos( $file_clean_url, '.php' ) === true ) {
                $this->sfd_redirect_to_referer();
            }

            $file_info = array(
                'url'  => $file_url,
                'name' => $file_name,
                'ext'  => $file_extension['extension']
            );

            $this->sfd_force_s3_file_download( $file_info );
            exit;

        } 
        
        if ( !is_admin() ) {
            $this->sfd_redirect_to_referer();
        }
    }
}