<?php
namespace um;


if ( ! defined( 'ABSPATH' ) ) exit;


/**
 * Ultimate Member Dependency Checker
 *
 * Checks if Ultimate Member plugin is enabled
 */
if ( ! class_exists( 'um\Dependencies' ) ) {


	/**
	 * Class Dependencies
	 *
	 * @package um
	 */
	class Dependencies {


		/**
		 * @var
		 */
		private static $active_plugins;


		/**
		 * For backward compatibility checking
		 *
		 * @var array
		 */
		public $ext_required_version = array(
			'bbpress'               => '2.0.7-rc.1',
			'followers'             => '2.1.6-rc.1',
			'forumwp'               => '2.0.1',
			'friends'               => '2.1.4-rc.1',
			'groups'                => '2.1.7-rc.1',
			'instagram'             => '2.0.5-rc.1',
			'mailchimp'             => '2.2.0-rc.1',
			'messaging'             => '2.2.5-rc.1',
			'mycred'                => '2.1.6-rc.1',
			'notices'               => '2.0.5-rc.1',
			'notifications'         => '2.1.3-rc.1',
			'online'                => '2.1.1-rc.1',
			'private-content'       => '2.0.5',
			'profile-completeness'  => '2.1.2-rc.1',
			'recaptcha'             => '2.1.2-rc.1',
			'reviews'               => '2.1.5-rc.1',
			'social-activity'       => '2.2.0-rc.1',
			'social-login'          => '2.2.0-rc.1',
			'terms-conditions'      => '2.1.1',
			'unsplash'              => '2.0.2-rc.1',
			'user-bookmarks'        => '2.0.2-rc.1',
			'user-photos'           => '2.0.4-rc.1',
			'user-tags'             => '2.1.0-rc.1',
			'verified-users'        => '2.0.5-rc.1',
			'woocommerce'           => '2.1.9-rc.1',

			/*????*/
			'restrict-content'      => '2.0',

			/*rc*/
			'user-notes'            => '2.0',
			'profile-tabs'          => '1.6',

			/*beta*/
			'frontend-posting'      => '1.0',
			/*alpha*/
			'user-exporter'         => '1.0.0',

			/*in development*/
			'user-location'         => '1.0',
			'filesharing'           => '1.0.0',
			'beaver-builder'        => '2.0',
			'user-events'           => '1.0.0',
		);


		/**
		 * Get all active plugins
		 */
		public static function init() {

			self::$active_plugins = (array) get_option( 'active_plugins', array() );

			if ( is_multisite() )
				self::$active_plugins = array_merge( self::$active_plugins, get_site_option( 'active_sitewide_plugins', array() ) );
		}


		/**
		 * @return mixed
		 */
		public function get_active_plugins() {
			if ( ! self::$active_plugins ) self::init();

			return self::$active_plugins;
		}


		/**
		 * Check if UltimateMember core plugin is active
		 *
		 * @return bool
		 */
		public static function ultimatemember_active_check() {

			if ( ! self::$active_plugins ) self::init();

			return in_array( 'ultimate-member/ultimate-member.php', self::$active_plugins ) || array_key_exists( 'ultimate-member/ultimate-member.php', self::$active_plugins );

		}


		/**
		 * Check if bbPress plugin is active
		 *
		 * @return bool
		 */
		public static function bbpress_active_check() {

			if ( ! self::$active_plugins ) self::init();

			return in_array( 'bbpress/bbpress.php', self::$active_plugins ) || array_key_exists( 'bbpress/bbpress.php', self::$active_plugins );

		}


		/**
		 * Check if ForumWP plugin is active
		 *
		 * @return bool
		 */
		public static function forumwp_active_check() {

			if ( ! self::$active_plugins ) self::init();

			return in_array( 'forumwp/forumwp.php', self::$active_plugins ) || array_key_exists( 'forumwp/forumwp.php', self::$active_plugins );

		}


		/**
		 * Check if myCRED plugin is active
		 *
		 * @return bool
		 */
		public static function mycred_active_check() {

			if ( ! self::$active_plugins ) self::init();

			return in_array( 'mycred/mycred.php', self::$active_plugins ) || array_key_exists( 'mycred/mycred.php', self::$active_plugins );

		}


		/**
		 * Check if Woocommerce plugin is active
		 *
		 * @return bool
		 */
		public static function woocommerce_active_check() {

			if ( ! self::$active_plugins ) self::init();

			return in_array( 'woocommerce/woocommerce.php', self::$active_plugins ) || array_key_exists( 'woocommerce/woocommerce.php', self::$active_plugins );

		}


		/**
		 * Compare UM core and extension versions
		 *
		 * @param string $um_required_ver
		 * @param string $ext_ver
		 * @param string $ext_key
		 * @param string $ext_title
		 * @return bool
		 */
		public function compare_versions( $um_required_ver, $ext_ver, $ext_key, $ext_title ) {

			if ( version_compare( ultimatemember_version, $um_required_ver, '<' )
			     || empty( $this->ext_required_version[$ext_key] )
			     || version_compare( $this->ext_required_version[$ext_key], $ext_ver, '>' ) ) {

				$message = '';
				if ( version_compare( ultimatemember_version, $um_required_ver, '<' ) ) {
					$message = sprintf( __( 'This version of <strong>"%s"</strong> requires the core <strong>%s</strong> plugin to be <strong>%s</strong> or higher.', 'ultimate-member' ), $ext_title, ultimatemember_plugin_name, $um_required_ver ) .
					           '<br />' .
					           sprintf( __( 'Please update <strong>%s</strong> to the latest version.', 'ultimate-member' ), ultimatemember_plugin_name );
				} elseif ( empty( $this->ext_required_version[$ext_key] ) || version_compare( $this->ext_required_version[$ext_key], $ext_ver, '>' ) ) {
					$message = sprintf( __( 'Sorry, but this version of <strong>%s</strong> does not work with extension <strong>"%s" %s</strong> version.', 'ultimate-member' ), ultimatemember_plugin_name, $ext_title, $ext_ver ) .
					           '<br />' .
					           sprintf( __( 'Please update extension <strong>"%s"</strong> to the latest version.', 'ultimate-member' ), $ext_title );
				}

				return $message;
			} else {
				//check correct folder name for extensions
				if ( ! self::$active_plugins ) self::init();

				if ( ! in_array( "um-{$ext_key}/um-{$ext_key}.php", self::$active_plugins ) && ! array_key_exists( "um-{$ext_key}/um-{$ext_key}.php", self::$active_plugins ) ) {
					$message = sprintf( __( 'Please check <strong>"%s" %s</strong> extension\'s folder name.', 'ultimate-member' ), $ext_title, $ext_ver ) .
					           '<br />' .
					           sprintf( __( 'Correct folder name is <strong>"%s"</strong>', 'ultimate-member' ), "um-{$ext_key}" );

					return $message;
				}
			}

			return true;
		}


		/**
		 * @param string $extension_version Extension version
		 * @return mixed
		 */
		public static function php_version_check( $extension_version ) {

			return version_compare( phpversion(), $extension_version, '>=' );

		}

	}
}


if ( ! function_exists( 'is_um_active' ) ) {
	/**
	 * Check UltimateMember core is active
	 *
	 * @return bool active - true | inactive - false
	 */
	function is_um_active() {
		return Dependencies::ultimatemember_active_check();
	}
}