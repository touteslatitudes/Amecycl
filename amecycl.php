<?php

/**
 *
 * @link              https://randovelo.touteslatitudes.fr
 * @since             1.0.0
 * @package           Amecycl
 *
 * @wordpress-plugin
 * Plugin Name:       Aménagements Cyclables
 * Plugin URI:        https://randovelo.touteslatitudes.fr/amecycl
 * Description:       Affichage sur une carte et statistiques du linéaire des aménagements cyclables d'un territoire. 
 *                    Nécessite des données conforme à https://github.com/etalab/amenagements-cyclables
 * Version:           1.1.1
 * Author:            toutesLatitudes
 * Author URI:        https://randovelo.touteslatitudes.fr
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       amecycl
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// version du plugin Amecycl
define( 'ACY_VERSION', '1.1.1' );

// version du schema de donnees - Voir https://github.com/etalab/amenagements-cyclables/blob/master/schema_amenagements_cyclables.json
define( 'ACY_SCHEMA_VERSION', '0.3.3' ); 

// chemins et urls
define("ACY_HOME_URL", get_home_url());							// home url ex: www.monsite.fr
// On s'assure que get_home_path() est bien déclaré
require_once( ABSPATH . 'wp-admin/includes/file.php' );
define("ACY_HOME_DIR", get_home_path());						// home path 

define("ACY_SITE_URL", get_site_url());							// site url ex: www.monsite.fr/int1

define("ACY_PLUGIN_URL", plugin_dir_url(__FILE__));
define("ACY_PLUGIN_DIR", plugin_dir_path(__FILE__));			// repertoire du plugin

define("ACY_DATE_FORMAT", "Y-m-d H:i:s" );
define("ACY_DAY_FORMAT", "d/m/Y" );
define("ACY_HOUR_FORMAT", "H:i:s" );

define('ACY_ADMIN', 'manage_options');

define("ACY_GEOJSON_DIR", 'amecycl');							// répertoire des fichiers geojson
define("ACY_GEOJSON_UPLOAD_DIR", 'amecycl/uploads');			// répertoire uploads des fichiers geojson
define("ACY_GEOJSON_DOWNLOAD_DIR", 'amecycl/downloads');		// répertoire downloads des fichiers geojson
define("ACY_SETTINGS_CSS", 'configurations');					// nom du repertoire contenant les fichiers .css des configurations

// liste des types d'amenagement, couleur et coefficient de longueur
global $acy_default_ameColors;		// couleurs des différents types d'aménagement
global $acy_ameLength_coeff;		// coefficient de calcul de la longueur d'un aménagement

require_once plugin_dir_path( __FILE__ ) . 'amecycl_ame.php';


/* Couches possibles pour la carte
 * OSM    : Openstreetmap - par défaut
 * COSM   : CyclOSM
 * OTM    : OpenTopoMap
 * --- couches IGN : 
 * MAPS   : IGN - cartes standard - nécessite une clé lié au site
 * SCAN25 : IGN - cartes topo touristiques - nécessite une clé lié au site
 * PLANS  : IGN - Plans - clé 'essentiels'
 * ORTHO  : IGN - Photos aeriennes - clé 'essentiels'
 * --- couche OpenCycleMap : nécessite une clé
 * OCM    : OpenCycleMap
 * --- couches GoogleMaps : nécessite une clé 
 * GMRMP  : Google Maps RoadMaP
 * GMSAT  : Google Maps SATellite
 * GMTER  : Google Maps TERrain
 * --- couches ESRI
 * ETOPO  : ESRI Topo Map
 * EIMAG  : ESRI Imagery
 */

// clé IGN nécessaires pour les fonds SCAN25 et IGN cartes standard (ajouter "SCAN25" et "MAPS" dans la liste) 
//define('ACY_IGN_API_KEY', "mettre votre clé ign ici"); 

// clé OpenCycleMap (ajouter "OCM" dans la liste)
//define('ACY_OCM_API_KEY', "mettre votre clé OpenCycleMap ici");

// Utilisation des fonds de carte Google. Nécessite une clé. (ajouter "GMRMP","GMSTAT","GMTER" dans la liste)
//define('ACY_GOOGLE_API_KEY', "mettre votre clé google map ici");

// Liste des fonds de cartes à afficher choix parmi "COSM","OSM","OTM","MAPS","SCAN25","PLANS","ORTHO","OCM","ETOPO","EIMAG","GMRMP","GMSTAT","GMTER"
define('ACY_MAPS', '"OSM", "COSM","OTM","ETOPO","EIMAG","PLANS","ORTHO"');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-amecycl-activator.php
 */
function activate_amecycl() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amecycl-activator.php';
	Amecycl_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-amecycl-deactivator.php
 */
function deactivate_amecycl() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-amecycl-deactivator.php';
	Amecycl_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_amecycl' );
register_deactivation_hook( __FILE__, 'deactivate_amecycl' );


/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-amecycl.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_amecycl() {

	$plugin = new Amecycl();
	$plugin->run();

}
run_amecycl();
