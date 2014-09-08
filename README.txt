=== Post Deployment Hook ===
Contributors: jaredcobb
Tags: hook, webhook, deployment, build
Requires at least: 3.0.1
Tested up to: 4.0
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Creates a webhook listener in WordPress, useful for integrating a post-deployment hook from your build / deployment process

== Description ==

If you have a build / deployment process for your site, it's often useful to have a way to
execute code in your theme or plugins after you commit or deploy your code.

Common use cases are

*   Purge the cache after you deploy new theme code (WP Super Cache or W3 Total Cache)
*   Trigger a WordPress hook
*   Run any other plugin or theme code you wish!

== Installation ==

1. Upload the plugin folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Enter a passphrase to secure your webhook
4. Use the generated URL (including it's token) as your post-deployment / post-commit hook url

== Frequently Asked Questions ==

= Will this slow down my site? =

Nope. While the plugin does execute on each page request, it simply checks if the request
is a webhook call first. If not, it just exits.

== Screenshots ==

== Changelog ==

= 1.0 =
* Initial commit
