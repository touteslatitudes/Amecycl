<?php

/**
 * Fired during plugin activation
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Amecycl
 * @subpackage Amecycl/includes
 * @author     toutesLatitudes <contact@randovelo.touteslatitudes.fr>
 */
class Amecycl_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		
		// Les elements suivants sont créés a l'activation du plugin :
		// les tables ACY_REGIONS_TABLE, ACY_STATS_TABLE, ACY_LAYERS_TABLE, ACY_SETTINGS_TABLE et ACY_SETTING_LABELS_TABLE
		// les repertoires amecycl/upload et amecycl/download
		// le fichier amecycl/public/css/amecycl-defaut.css

		$acy_model = new Amecycl_Model();
		$acy_model->init_amecycl_region_tables();	// initialisation des tables region du plugin
		$acy_model->init_amecycl_setting_tables();	// initialisation des tables setting du plugin
		$acy_model->init_amecycl_directories();		// initialisation des repertoires upload/downloads
	}
}