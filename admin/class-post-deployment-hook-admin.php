<?php
/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       https://github.com/jaredcobb/post-deployment-hook
 * @since      1.0.0
 *
 * @package    Post_Deployment_Hook
 * @subpackage Post_Deployment_Hook/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and settings menu for
 * the configuration of the post deployment hook
 *
 * @package    Post_Deployment_Hook
 * @subpackage Post_Deployment_Hook/admin
 * @author     Jared Cobb <wordpress@jaredcobb.com>
 */
class Post_Deployment_Hook_Admin {

	/**
	 * The name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The name of this plugin.
	 */
	private $name;

	/**
	 * The display name of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $display    The display name of this plugin.
	 */
	private $display_name;

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
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version ) {

		$this->name = $name;
		$this->version = $version;
		$this->options = get_option($this->name . '-options');
		$this->display_name = 'Post Deployment Hook';

	}

	/**
	 * Adds the settings page
	 *
	 * @since    1.0.0
	 */
	public function add_settings_page() {

		add_options_page(
			$this->display_name,
			$this->display_name,
			'manage_options',
			$this->name . '-settings-page',
			array( $this, 'create_settings_page' )
		);

	}

	/**
	 * Creates the markup for the settings page and also populates the fields
	 *
	 * @since    1.0.0
	 */
	public function create_settings_page() {

		?>

			<div class="wrap">
				<h2><?php echo $this->display_name; ?> Settings</h2>
				<form method="post" action="options.php">
					<?php
						settings_fields($this->name . '-group');
						do_settings_sections( $this->name . '-settings-page' );
						submit_button();
					?>
				</form>
			</div>

		<?php
	}

	/**
	 * Initializes & registers the settings page functionality including fields to define
	 *
	 * @since    1.0.0
	 */
	public function settings_page_init() {

		register_setting(
			$this->name . '-group', // option group
			$this->name . '-options', // option name
			array( $this, 'sanitize' ) // sanitize
		);

		add_settings_section(
			'general_config_section', // id
			'General Settings', // title
			array( $this, 'output_general_config_callback' ), // callback
			$this->name . '-settings-page' // page
		);

		add_settings_field(
			'password', // id
			'Password', // title
			array( $this, 'password_callback' ), // callback
			$this->name . '-settings-page', // page
			'general_config_section' // section
		);

		add_settings_field(
			'token_url', // id
			'Token URL', // title
			array( $this, 'token_url_callback' ), // callback
			$this->name . '-settings-page', // page
			'general_config_section' // section
		);

		if ( function_exists( 'prune_super_cache' ) ) {
			add_settings_field(
				'purge_wp_supercache',
				'Automatically Purge WP Super Cache',
				array( $this, 'purge_wp_supercache_callback' ),
				$this->name . '-settings-page',
				'general_config_section'
			);
		}

		if ( function_exists( 'w3tc_pgcache_flush' ) ) {
			add_settings_field(
				'purge_w3_total_cache',
				'Automatically Purge W3 Total Cache',
				array( $this, 'purge_w3_total_cache_callback' ),
				$this->name . '-settings-page',
				'general_config_section'
			);
		}

		add_settings_field(
			'user_defined_function', // id
			'User Defined Function', // title
			array( $this, 'user_defined_function_callback' ), // callback
			$this->name . '-settings-page', // page
			'general_config_section' // section
		);

	}

	/**
	 * Validate the fields that were submitted
	 *
	 * @since    1.0.0
	 */
	public function sanitize( $input ) {

		$sanitized_input = array();

		if (isset($input['password'])) {
			$sanitized_input['password'] = sanitize_text_field($input['password']);
		}

		if (isset($input['purge_wp_supercache'])) {
			$sanitized_input['purge_wp_supercache'] = 1;
		}

		if (isset($input['user_defined_function'])) {
			// regex of a valid php method/function name according to http://php.net/manual/en/language.oop5.basic.php
			if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $input['user_defined_function'])) {
				$sanitized_input['user_defined_function'] = sanitize_text_field($input['user_defined_function']);
			}
		}

		return $sanitized_input;
	}

	/**
	 * Print the Section text
	 *
	 * @since    1.0.0
	 */
	public function output_general_config_callback() {
		echo <<<HTML
			<span>Your general settings control the overall configuration of the plugin</span>
HTML;
	}

	/**
	 * Get the settings option array and print the password
	 *
	 * @since    1.0.0
	 */
	public function password_callback() {

		$password = isset( $this->options['password'] ) ? esc_attr( $this->options['password']) : '';

		echo <<<HTML
			<input type="text" id="password" name="{$this->name}-options[password]" value="{$password}" />
			<p class="description">You won&lsquo;t need to remember this. It&lsquo;s used to securely generate your URL token (below). If you change it, however, your token will change (and any existing integrations will need to be updated).</p>
HTML;
	}

	/**
	 * Get the settings option array and print the token url
	 */
	public function token_url_callback() {

		$password = isset( $this->options['password'] ) ? esc_attr( $this->options['password']) : '';

		if (strlen($password) > 0) {
			$token_url = get_home_url() . '/?tk=' . md5(AUTH_SALT . $password);
		}
		else {
			$token_url = 'You must set a password in order to generate a token url (webhook)';
		}

		echo <<<HTML
			<strong>$token_url</strong>
			<p class="description">This is the webhook you will call from your deployment/build process. Calling this URL will trigger the actions below.</p>
HTML;
	}

	/**
	 * Get the settings option array and print the wp supercache checkbox
	 */
	public function purge_wp_supercache_callback() {

		$purge_wp_supercache = isset( $this->options['purge_wp_supercache'] ) ? 'checked="checked"' : '';

		echo <<<HTML
			<input type="checkbox" id="purge-wp-supercache" name="{$this->name}-options[purge_wp_supercache]" value="1" {$purge_wp_supercache} />
			<label for="purge-wp-supercache" class="description">Should we purge the cache from WP Supercache when the webhook is called?</label>
HTML;
	}

	/**
	 * Get the settings option array and print the w3 total cache
	 */
	public function purge_w3_total_cache_callback() {

		$purge_w3_total_cache = isset( $this->options['purge_w3_total_cache'] ) ? 'checked="checked"' : '';

		echo <<<HTML
			<input type="checkbox" id="purge-w3_total_cache" name="{$this->name}-options[purge_w3_total_cache]" value="1" {$purge_w3_total_cache} />
			<label for="purge-wp-supercache" class="description">Should we purge the cache from W3 Total Cache when the webhook is called?</label>
HTML;
	}

	/**
	 * Get the settings option array and print the user defined function textbox
	 */
	public function user_defined_function_callback() {

		$user_defined_function = isset( $this->options['user_defined_function'] ) ? esc_attr( $this->options['user_defined_function']) : '';

		echo <<<HTML
			<input type="text" id="user-defined-function" name="{$this->name}-options[user_defined_function]" value="{$user_defined_function}" />
			<p class="description">What is the function name that should be called when the webhook is fired? (This should provide you with the most flexible solution as you can defined a single function in <code>functions.php</code> and execute anything you wish).</p>
			<strong><p class="description">Note: Enter your function without parenthesis like so: <code>my_function_name</code></p></strong>
HTML;
	}

}
