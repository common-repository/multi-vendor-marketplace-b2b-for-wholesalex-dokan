<?php

/**
 * Multi Vendor Marketplace B2B for WholesaleX Dokan
 *
 *
 * @link    https://www.wpxpo.com/
 * @since   1.0.0
 * @package           Wholesalex_Dokan
 *
 * Plugin Name:       Multi Vendor Marketplace B2B for WholesaleX Dokan
 * Plugin URI:        https://wordpress.org/plugins/multi-vendor-marketplace-b2b-for-wholesalex-dokan
 * Description:       WholesaleX for Dokan migration plugin allows you to transform marketplaces into wholesale multivendor marketplaces and lets vendors add wholesale prices, communicate with customers and control product visibility.
 * Version:           1.0.1
 * Author:            Wholesale Team
 * Author URI:        https://getwholesalex.com/
 * License:           GPLv3
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       multi-vendor-marketplace-b2b-for-wholesalex-dokan
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Defince Plugin Version
 */
define( 'WHOLESALEX_DOKAN_VERSION', '1.0.1' ); // This should change each new release
define( 'WHOLESALEX_DOKAN_URL', plugin_dir_url(__FILE__) ); 

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-activator.php
 *
 * @since 1.0.0
 */
function wholesalex_dokan_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-activator.php';
	Wholesalex_Dokan_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-deactivator.php
 *
 * @since 1.0.0
 */
function wholesalex_dokan_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan-deactivator.php';
	Wholesalex_Dokan_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'wholesalex_dokan_activate' );
register_deactivation_hook( __FILE__, 'wholesalex_dokan_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-multi-vendor-marketplace-b2b-for-wholesalex-dokan.php';

function wholesalex_dokan_init() {
	$required_plugins = array(
        'WooCommerce' => array('path'=> 'woocommerce/woocommerce.php', 'version'=>''),
        'WholesaleX' => array('path'=>'wholesalex/wholesalex.php','version'=>'1.2.4'),
		'Dokan' => array('path'=>'dokan-lite/dokan.php','version'=>'') 
    );
	$plugin = new Wholesalex_Dokan($required_plugins);
	$plugin->run();
}

/**
 * Begins execution of the plugin.
 *
 *
 * @since    1.0.0
 */
function wholesalex_dokan_run() {

	add_action('init','wholesalex_dokan_init');

}
wholesalex_dokan_run();
