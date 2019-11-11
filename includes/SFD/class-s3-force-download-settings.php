<?php
/**
 * Class to add option settings in the admin dashboard for s3-force-download.
 *
 * @package s3-force-download
 */

namespace SFD;

if( !defined( 'ABSPATH' ) ) {
	exit;
}

class S3_Force_Download_Settings {

    const SFD_DOMAIN = 'sfd_domain';

    /**
	 * Class instance
	 *
	 * @var S3_Force_Download_Settings
	 * @access private
	 */
    private static $instance = null;

    /**
	 * Get class instance
	 *
	 * @return S3_Force_Download_Settings
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
     * @return void
	 */
    private function __construct() {
        // if user is logged into the dashboard, load option settings
        if ( is_admin() ) {
            add_action( 'admin_menu', array( $this, 'sfd_options_menu_link' ) );
            add_action( 'admin_init' , array( $this, 'sfd_register_setings' ) );
        }
    }

    /**
	 * Create Menu Link
	 *
	 * @return void
     * @access public
	 */
    public function sfd_options_menu_link() {
        add_options_page(
            'S3 Force Download Options',
            'S3 Force Download',
            'manage_options',
            'sfd-options',
            array( $this, 'sfd_create_options_content' ) 
        );
    }

    /**
	 * Register Settings
	 *
	 * @return void
     * @access public
	 */
    public function sfd_register_setings() {
        register_setting( 'sfd_settings_group', 'sfd_settings' );
    }

    /**
	 * Create Options Content
	 *
	 * @return string html to generate settings form 
     * @access public
	 */
    public function sfd_create_options_content() {

        global $sfd_options;  // Init options global

        ob_start(); ?> 
        <div class="wrap">
            <header>
                <h1>
                    <?php _e( 'S3 Force Download Settings', self::SFD_DOMAIN ); ?>
                </h1>
                <h3>
                    <?php _e( 'Settings for the S3 Force Download plugin', self::SFD_DOMAIN ); ?>
                </h3>
            </header>
            <main>
                <form method="post" action="options.php">
                    <?php settings_fields( 'sfd_settings_group' ); ?>
                    <table class="form-table">
                        <tbody>
                            <!-- Field: Bucket Name -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[bucket_name]">
                                        <?php _e( 'Bucket Name', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[bucket_name]" type="text" id="sfd_settings[bucket_name]" value="<?php echo $sfd_options['bucket_name']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter your Bucket Name.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <strong><?php _e( 'Example: bkt-name001', self::SFD_DOMAIN ); ?></strong>
                                        <br />
                                        <?php _e( 'Leave empty if you prefer to set the environment variable "S3_BUCKET_NAME" in your server.', self::SFD_DOMAIN ); ?>
                                    </p>
                                </td>
                            </tr>
                            <!-- Field: Bucket Region -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[bucket_region]">
                                        <?php _e( 'Bucket Region', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[bucket_region]" type="text" id="sfd_settings[bucket_region]" value="<?php echo $sfd_options['bucket_region']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter your Bucket Region.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <strong><?php _e( 'Example: us-east-1', self::SFD_DOMAIN ); ?></strong>
                                        <br />
                                        <?php _e( 'Leave empty if you prefer to set the environment variable "S3_BUCKET_REGION" in your server.', self::SFD_DOMAIN ); ?>
                                    </p>
                                </td>
                            </tr>
                            <!-- Field: S3 Access Key -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[access_key]">
                                        <?php _e( 'S3 Access Key', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[access_key]" type="text" id="sfd_settings[access_key]" value="<?php echo $sfd_options['access_key']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter your S3 Access Key.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <?php _e( 'Leave empty if you prefer to set the environment variable "S3_ACCESS_KEY" in your server.', self::SFD_DOMAIN ); ?>
                                    </p>
                                </td>
                            </tr>
                            <!-- Field: S3 Secret Key -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[secret_key]">
                                        <?php _e( 'S3 Secret Key', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[secret_key]" type="text" id="sfd_settings[secret_key]" value="<?php echo $sfd_options['secret_key']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter your S3 Secret Key.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <?php _e( 'Leave empty if you prefer to set the environment variable "S3_SECRET_KEY" in your server.', self::SFD_DOMAIN ); ?>
                                    </p>
                                </td>
                            </tr>
                            <!-- Field: S3 File Path -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[file_path]">
                                        <?php _e( 'S3 File Path', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[file_path]" type="text" id="sfd_settings[file_path]" value="<?php echo $sfd_options['file_path']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter your S3 File Path.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <strong><?php _e( 'Example: /wp-content/uploads', self::SFD_DOMAIN ); ?></strong>
                                    </p>
                                </td>
                            </tr>
                            <!-- Field: Allowed File Extensions -->
                            <tr>
                                <th scope="row">
                                    <label for="sfd_settings[file_ext]">
                                        <?php _e( 'Allowed File Extensions', self::SFD_DOMAIN ); ?>
                                    </label>
                                </th>
                                <td>
                                    <input name="sfd_settings[file_ext]" type="text" id="sfd_settings[file_ext]" value="<?php echo $sfd_options['file_ext']; ?>" class="regular-text" />

                                    <p class="description">
                                        <?php _e( 'Enter all the allowed file extensions to be downloaded.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <?php _e( 'Multiple extensions may be listed using commas.', self::SFD_DOMAIN ); ?>
                                        <br />
                                        <strong><?php _e( 'Example: jpg, pdf, mp4', self::SFD_DOMAIN ); ?></strong>
                                    </p>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e( 'Save Changes', self::SFD_DOMAIN ); ?>">
                    </p>
                </form>
            </main>
        </div>
        <?php echo ob_get_clean();
    }    
}








