<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/jaredcobb/post-deployment-hook
 * @since      1.0.0
 *
 * @package    Post_Deployment_Hook
 * @subpackage Post_Deployment_Hook/includes
 */

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Post_Deployment_Hook
 * @subpackage Post_Deployment_Hook/admin
 * @author     Jared Cobb <wordpress@jaredcobb.com>
 */
class Post_Deployment_Hook_Public {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The name of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * The options for this plugin
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      array    $options    The options for thie plugin
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of the plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {
		// if we don't tell caching plugins to stop caching this request,
		// we cannot ever call the hook a second time!
		define( 'DONOTCACHEPAGE', true );

		$this->name = $name;
		$this->version = $version;
		$this->options = get_option($this->name . '-options');
	}

	/**
	 * Execute all of the actions for the webhook
	 *
	 * @since    1.0.0
	 */
	public function execute_webhook_actions() {

		if (isset($_GET['tk']) && $this->validate_token($_GET['tk'])) {

			// check option values and execute against those preferences
			if ( function_exists( 'prune_super_cache' ) && $this->options['purge_wp_supercache'] == true ) {
				global $cache_path;
				prune_super_cache( $cache_path, true );
			}

			if ( function_exists( 'w3tc_pgcache_flush' ) && $this->options['purge_w3_total_cache'] == true ) {
				w3tc_pgcache_flush();
			}

			if ( isset($this->options['user_defined_function']) && function_exists($this->options['user_defined_function']) ) {
				call_user_func($this->options['user_defined_function']);
			}

		}

	}

	/**
	 * Check to see if the token matches the hash of the salt/password
	 *
	 * @since    1.0.0
	 */
	protected function validate_token($token) {

		if (strlen($token) == 32) {

			if ($token == md5(AUTH_SALT . $this->options['password'])) {
				return true;
			}

		}

		return false;

	}

}
