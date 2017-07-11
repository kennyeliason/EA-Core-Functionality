<?php
/**
 * General
 *
 * @package      CoreFunctionality
 * @author       Bill Erickson
 * @since        1.0.0
 * @license      GPL-2.0+
**/

// Don't let WPGA create yet another top level menu
add_filter( 'wpga_menu_on_top', '__return_false' );

// Don't let WPSEO metabox be high priority
add_filter( 'wpseo_metabox_prio', function(){ return 'low'; } );

/**
 * Remove WPSEO Notifications
 *
 */
function ea_remove_wpseo_notifications() {

	if( ! class_exists( 'Yoast_Notification_Center' ) )
		return;

	remove_action( 'admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
	remove_action( 'all_admin_notices', array( Yoast_Notification_Center::get(), 'display_notifications' ) );
}
add_action( 'init', 'ea_remove_wpseo_notifications' );

/**
 * Gravity Forms Domain
 *
 * Adds a notice at the end of admin email notifications
 * specifying the domain from which the email was sent.
 *
 * @param array $notification
 * @param object $form
 * @param object $entry
 * @return array $notification
 */
function ea_gravityforms_domain( $notification, $form, $entry ) {

	if( $notification['name'] == 'Admin Notification' ) {
		$notification['message'] .= 'Sent from ' . home_url();
	}

	return $notification;
}
add_filter( 'gform_notification', 'ea_gravityforms_domain', 10, 3 );

/**
 * Carbon Fields Compatibility
 * If plugin is deactivated for some reason, this prevents errors on frontend
 *
 */
function ea_carbon_fields_compat() {


  if ( ! function_exists( 'carbon_get_post_meta' ) ) {
      function carbon_get_post_meta( $id, $name, $type = null ) {
          return false;
      }
  }

  if ( ! function_exists( 'carbon_get_the_post_meta' ) ) {
      function carbon_get_the_post_meta( $name, $type = null ) {
          return false;
      }
  }

  if ( ! function_exists( 'carbon_get_theme_option' ) ) {
      function carbon_get_theme_option( $name, $type = null ) {
          return false;
      }
  }

  if ( ! function_exists( 'carbon_get_term_meta' ) ) {
      function carbon_get_term_meta( $id, $name, $type = null ) {
          return false;
      }
  }

  if ( ! function_exists( 'carbon_get_user_meta' ) ) {
      function carbon_get_user_meta( $id, $name, $type = null ) {
          return false;
      }
  }

  if ( ! function_exists( 'carbon_get_comment_meta' ) ) {
      function carbon_get_comment_meta( $id, $name, $type = null ) {
          return false;
      }
  }
}
add_action( 'plugins_loaded', 'ea_carbon_fields_compat' );

/**
 * Dont Update the Plugin
 * If there is a plugin in the repo with the same name, this prevents WP from prompting an update.
 *
 * @since  1.0.0
 * @author Jon Brown
 * @param  array $r Existing request arguments
 * @param  string $url Request URL
 * @return array Amended request arguments
 */
function ea_dont_update_core_func_plugin( $r, $url ) {
  if ( 0 !== strpos( $url, 'https://api.wordpress.org/plugins/update-check/1.1/' ) )
    return $r; // Not a plugin update request. Bail immediately.
    $plugins = json_decode( $r['body']['plugins'], true );
    unset( $plugins['plugins'][plugin_basename( __FILE__ )] );
    $r['body']['plugins'] = json_encode( $plugins );
    return $r;
 }
add_filter( 'http_request_args', 'ea_dont_update_core_func_plugin', 5, 2 );

/**
 * Author Links on CF Plugin
 *
 */
function ea_author_links_on_cf_plugin( $links, $file ) {

	if ( strpos( $file, 'core-functionality.php' ) !== false ) {
		$links[1] = 'By <a href="http://www.billerickson.net">Bill Erickson</a> & <a href="http://www.jaredatchison.com">Jared Atchison</a>';
    }

    return $links;
}
add_filter( 'plugin_row_meta', 'ea_author_links_on_cf_plugin', 10, 2 );
