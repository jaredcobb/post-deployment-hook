=== Post Deployment Hook ===
Contributors: jaredcobb
Tags: hook, webhook, deployment, build
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: 1.0.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a webhook listener in WordPress, useful for integrating a post-deployment hook from your build / deployment process

== Description ==

If you have a build / deployment process for your site, it's often useful to have a way to execute code in your theme or plugins after you commit or deploy your code changes.

GitHub, Bitbucket, and Beanstalk are common tools that allow you to configure a webhook URL that gets called as soon as you make a check-in or deployment.

Common uses are

* Purge the cache after you deploy new theme code (WP Super Cache or W3 Total Cache)
* After you build your JavaScript/CSS, trigger a backup of the live site
* Trigger any WordPress hook
* Run any other plugin or theme code you wish!

**More information on how to use this plugin (including how to setup GitHub, Bitbucket, and Beanstalk) can be found at [http://jaredcobb.com/post-deployment-hook](http://jaredcobb.com/post-deployment-hook)**

The plugin is also [hosted on GitHub](https://github.com/jaredcobb/post-deployment-hook). Pull requests are welcome!

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter a passphrase in the Settings > Post Deployment Hook options to secure your webhook
4. Use the generated URL (including it's token) as your post-deployment / post-commit hook url

== Frequently Asked Questions ==

= Will this slow down my site? =

Nope. While the plugin does execute on each page request, it simply checks if the request is a webhook call first. If not, it just exits.

= Who would use this plugin? =

Probably developers. But anyone with some knowledge of WordPress could use this plugin to run jobs in WordPress by hitting the URL this plugin provides.

== Screenshots ==

1. The settings page will allow you to set a password and create a webhook url. You may also automatically purge the cache (WP Super Cache or W3 Total Cache).
2. You can define your own function (I recommend placing it into `functions.php`) and run any code you wish once the token url is hit.

== Changelog ==

= 1.0.1 =
* Updated README.txt and added screenshots

= 1.0.0 =
* Initial commit
