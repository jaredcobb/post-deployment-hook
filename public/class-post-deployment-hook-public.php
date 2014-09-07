<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://jaredcobb.com
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
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
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
		define( 'DONOTCACHEPAGE', true );
		$this->name = $name;
		$this->version = $version;
		$this->options = get_option($this->name . '-options');
	}

	public function execute_webhook_actions() {

		if (isset($_GET['tk']) && $this->validate_token($_GET['tk'])) {

			// check option values and execute against those preferences
			if ( function_exists( 'prune_super_cache' ) && $this->options['purge_wp_supercache'] == true ) {
				global $cache_path;
				prune_super_cache( $cache_path, true );
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
