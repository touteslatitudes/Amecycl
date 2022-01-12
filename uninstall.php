<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

require plugin_dir_path( __FILE__ ) . 'includes/class-amecycl-model.php';

// les tables ACY_REGIONS_TABLE, ACY_STATS_TABLE, ACY_LAYERS_TABLE, ACY_SETTINGS_TABLE et ACY_SETTING_LABELS_TABLE
// sont supprimées à la desinstallation
// le répertoire ACY_GEOJSON_DIR et tous ces sous-répertoires et fichiers sont supprimés

$acy_model = new Amecycl_Model();
$acy_model->uninstall_amecycl();

