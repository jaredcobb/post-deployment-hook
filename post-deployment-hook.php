<?php
/**
 * @link              https://github.com/jaredcobb/post-deployment-hook
 * @since             1.0.0
 * @package           Post_Deployment_Hook
 *
 * @wordpress-plugin
 * Plugin Name:       Post Deployment Hook
 * Plugin URI:        http://jaredcobb.com/wordpress-post-deployment-hook/
 * Description:       Creates a webhook listener in WordPress, useful for integrating a post-deployment hook from your build / deployment process

 * Version:           1.0.1
 * Author:            Jared Cobb <wordpress@jaredcobb.com>
 * Author URI:        http://jaredcobb.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       post-deployment-hook
 * Domain Path:       /languages
 */

// if this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * main include file that defines hooks
 */
require_once plugin_dir_path( __FILE__ ) . 'includes/class-post-deployment-hook.php';

/**
 * Begin execution of the plugin.
 *
 * @since    1.0.0
 */
function run_post_deployment_hook() {

	// in order to optimize this plugin, quickly exit if we already know there's no token
	// url parameter AND it's called on a public page
	if (!is_admin() && !isset($_GET['tk'])) {
		return;
	}

	$plugin = new Post_Deployment_Hook();
	$plugin->run();

}
run_post_deployment_hook();
