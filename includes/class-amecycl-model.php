<?php

/**
 * Amecycl_model
 *
 * Classe description du modele d'un amenagement cyclable
 * S'appuie sur le schema de données des aménagements cyclables
 * Voir https://github.com/etalab/amenagements-cyclables
 *
 * @since      1.0.0
 * @package    Amecycl/
 * @subpackage Amecycl/includes
 * @author     toutesLatitudes <contact@randovelo.touteslatitudes.fr>
 */

global $wpdb;

// Noms des tables en base de données
define("ACY_REGIONS_TABLE", $wpdb->prefix . "acy_regions");		// table des agglomérations étudiés
define("ACY_STATS_TABLE", $wpdb->prefix . "acy_stats");			// table des amenagements cyclables - chiffres
define("ACY_LAYERS_TABLE", $wpdb->prefix . "acy_layers");		// table des amenagements cyclables - cartes
define("ACY_SCHEMA_TABLE", $wpdb->prefix . "acy_schema");		// table des amenagements cyclables - schema

define("ACY_SETTINGS_TABLE", $wpdb->prefix . "acy_settings");				// table des configurations
define("ACY_SETTING_LABELS_TABLE", $wpdb->prefix . "acy_setting_labels");	// table des labels de configuration


class Amecycl_Model {
	
	public function __construct() {

		// include geoPhp.php library - cf https://github.com/phayes/geoPHP
		require_once(sprintf("%s/geoPHP/geoPHP.inc", dirname(__FILE__)));

	}

	// initialisation des tables regions si elles n'existent pas
	public function init_amecycl_region_tables() {

		global $wpdb;

		// Type d'aménagement cyclables - Régions
		$show = "SHOW TABLES LIKE '" . ACY_REGIONS_TABLE . "'";
		if( $wpdb->get_var( $show ) != ACY_REGIONS_TABLE ) {
			// Creation de la table REGIONS si elle n'existe pas
			$sql = "CREATE TABLE IF NOT EXISTS `" . ACY_REGIONS_TABLE . "` (
				`region_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`region_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				`region_name` varchar(200) NOT NULL DEFAULT '',
				`region_year` varchar(12) NOT NULL DEFAULT '',
				`region_datadate` varchar(12) NOT NULL DEFAULT '',
				`region_slug` varchar(200) NOT NULL DEFAULT '',
				`region_filename` varchar(256) NOT NULL DEFAULT '',
				`region_setting` bigint(20) unsigned NOT NULL,
				`region_desc` longtext NOT NULL,
				`region_bound1` varchar(36) NOT NULL DEFAULT '',
				`region_bound2` varchar(36) NOT NULL DEFAULT '',
				`region_nb` smallint unsigned NOT NULL DEFAULT 0,
				`region_km` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`region_kmc` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`region_version` varchar(10) NOT NULL DEFAULT '" . ACY_VERSION ."',
				PRIMARY KEY  (`region_id`),
				KEY `region_name` (`region_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}


		// Type d'aménagement cyclables - Chiffres
		$show = "SHOW TABLES LIKE '" . ACY_STATS_TABLE . "'";
		if( $wpdb->get_var( $show ) != ACY_STATS_TABLE ) {
			// Creation de la table STATS si elle n'existe pas
			$sql = "CREATE TABLE IF NOT EXISTS `" . ACY_STATS_TABLE . "` (
				`stat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`stat_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				`stat_region` bigint(20) unsigned NOT NULL,
				`stat_name` varchar(64) NOT NULL DEFAULT '',
				`stat_slug` varchar(64) NOT NULL DEFAULT '',
				`stat_nb_unid` smallint unsigned NOT NULL DEFAULT 0,
				`stat_m_unid` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_nb_unig` smallint unsigned NOT NULL DEFAULT 0,
				`stat_m_unig` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_nb_bid` smallint unsigned NOT NULL DEFAULT 0,
				`stat_m_bid` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_nb_big` smallint unsigned NOT NULL DEFAULT 0,
				`stat_m_big` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_nb` smallint unsigned NOT NULL DEFAULT 0,
				`stat_km` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_kmc` decimal(10,2) unsigned NOT NULL DEFAULT 0.00,
				`stat_version` varchar(10) NOT NULL DEFAULT '" . ACY_VERSION ."',
				PRIMARY KEY  (`stat_id`),
				KEY `stat_name` (`stat_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}


		// Type d'aménagement cyclables - Cartes
		$show = "SHOW TABLES LIKE '" . ACY_LAYERS_TABLE . "'";
		if( $wpdb->get_var( $show ) != ACY_LAYERS_TABLE ) {
			// Creation de la table LAYERS si elle n'existe pas
			$sql = "CREATE TABLE IF NOT EXISTS `" . ACY_LAYERS_TABLE . "` (
				`layer_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`layer_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				`layer_region` bigint(20) unsigned NOT NULL,
				`layer_name` varchar(200) NOT NULL DEFAULT '',
				`layer_slug` varchar(200) NOT NULL DEFAULT '',
				`layer_nb_features` mediumint NOT NULL DEFAULT 0,
				`layer_geojson` longtext NOT NULL DEFAULT '',
				`layer_version` varchar(10) NOT NULL DEFAULT '" . ACY_VERSION ."',
				PRIMARY KEY  (`layer_id`),
				KEY `layer_name` (`layer_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
	}

		
	// initialisation des tables settings si elles n'existent pas
	public function init_amecycl_setting_tables() {

		global $wpdb;
		global $acy_default_ameColors;	// liste des labels de configuration par defaut

		// Configurations 
		$show = "SHOW TABLES LIKE '" . ACY_SETTINGS_TABLE . "'";
		if( $wpdb->get_var( $show ) != ACY_SETTINGS_TABLE ) {
			// Creation de la table SETTINGS si elle n'existe pas
			$sql = "CREATE TABLE IF NOT EXISTS `" . ACY_SETTINGS_TABLE . "` (
				`setting_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`setting_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				`setting_name` varchar(200) NOT NULL DEFAULT '',
				`setting_slug` varchar(200) NOT NULL DEFAULT '',
				`setting_date` varchar(32) NOT NULL DEFAULT '',
				`setting_desc` longtext NOT NULL DEFAULT '',
				`setting_version` varchar(10) NOT NULL DEFAULT '" . ACY_VERSION ."',
				PRIMARY KEY  (`setting_id`),
				KEY `setting_name` (`setting_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}

		$show = "SHOW TABLES LIKE '" . ACY_SETTING_LABELS_TABLE . "'";
		if( $wpdb->get_var( $show ) != ACY_SETTING_LABELS_TABLE ) {
			// Creation de la table SETTING_LABELS si elle n'existe pas
			$sql = "CREATE TABLE IF NOT EXISTS `" . ACY_SETTING_LABELS_TABLE . "` (
				`label_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`label_setting` bigint(20) unsigned NOT NULL,
				`label_modified` datetime NOT NULL DEFAULT '1000-01-01 00:00:00',
				`label_name` varchar(200) NOT NULL DEFAULT '',
				`label_slug` varchar(200) NOT NULL DEFAULT '',
				`label_color` varchar(30) NOT NULL DEFAULT '',
				`label_version` varchar(10) NOT NULL DEFAULT '" . ACY_VERSION ."',
				PRIMARY KEY  (`label_id`),
				KEY `label_name` (`label_name`)
			) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
		
		// initialisation avec la configuration par defaut
		$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE . "` WHERE `setting_name`='defaut'";
		$setting = $wpdb->get_row($select, ARRAY_A);
		
		if (!$setting) {
			// la configuration par defaut n'existe pas. Il faut la créer.
			$setting = $this->init_setting();
			$setting['setting_name'] = 'Par défaut';
			$setting['setting_slug'] = 'defaut';
			$setting['setting_desc'] = __('Default settings description','amecycl');
			$setting['setting_date'] = date("d/m/y");

			$sid = $this->insert_setting($setting);
			if ($sid) {
				$setting_label = $this->init_setting_label();
				foreach($acy_default_ameColors as $label => $color) {
					$setting_label['label_name'] = $label;
					$setting_label['label_slug'] = sanitize_title($label);
					$setting_label['label_setting'] = $sid;
					$setting_label['label_color'] = $color;
					// insertion du label de configuration
					$this->insert_setting_label($setting_label);
				}
			}
		}

		// generation du fichier css correspondant à la configuration
		$this->generate_setting_css_file($sid);
	}

	// initialisation des repertoires upload/downloads si ils n'existent pas déja
	public function init_amecycl_directories() {

		$dir_created = true;

		// creation du repertoire pour l'upload des fichiers geoJson
		$upload_dir = ACY_HOME_DIR . ACY_GEOJSON_UPLOAD_DIR;
		if (!is_dir($upload_dir)) {
			$dir_created = mkdir($upload_dir, 0777, true);
		}

		if (!$dir_created) return $dir_created;

		// creation du repertoire pour le download des fichiers geoJson
		$download_dir = ACY_HOME_DIR . ACY_GEOJSON_DOWNLOAD_DIR;
		if (!is_dir($download_dir)) {
			$dir_created = mkdir($download_dir, 0777, true);
		}

		return $dir_created;
	}


	// creation de la table temporaire schema
	public function init_schema_table() {

		global $wpdb;

		// paramètres de la table temporaire ACY_SCHEMA_TABLE
		$columns = array(
			"id_osm",
			"ame_d","regime_d","sens_d",
			"ame_g","regime_g","sens_g",
			"longueur"
		);

		// Creation de la table temporaire SCHEMA
		// La table n'existe que le temps de la session php

		$sql = "CREATE TEMPORARY TABLE `" . ACY_SCHEMA_TABLE . "` (
			`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			`id_osm` varchar(64) DEFAULT '',
			`ame_d` varchar(64) NOT NULL DEFAULT '',
			`regime_d` varchar(64) DEFAULT '',
			`sens_d` varchar(64) DEFAULT '',
			`ame_g` varchar(64) NOT NULL DEFAULT '',
			`regime_g` varchar(64) DEFAULT '',
			`sens_g` varchar(64) DEFAULT '',
			`longueur` decimal(10,2) unsigned DEFAULT 0.00,
			`feature` blob DEFAULT '',
			PRIMARY KEY  (`id`)
		) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";

		$res = $wpdb->query($sql);

		if ($res) return $columns;
		else return null;
	}


	// lecture / initialisation d'une region
	public function readInit_region( $rid ) {
		if ($rid) {
			// lecture d'une region existante
			$region =  $this->read_region( $rid );
		}
		else {
			// Initialisation d'une nouvelle region
			$region =  $this->init_region();
		}
		return $region;
	}

	// initialisation d'une structure region
	public function init_region() {
		
		// lecture de la configuration par defaut 
		$default_setting = $this->search_setting( array( 'setting_slug' => 'defaut' ) );

		// creation d'une nouvelle region
		$region = array(
			'region_modified' => date( ACY_DATE_FORMAT ),
			'region_name' => '',
			'region_year' => '',
			'region_datadate' => '',
			'region_slug' => '',
			'region_filename' => '',
			'region_setting' => $default_setting['setting_id'], // initialisé à défaut
			'region_desc' => '',
			'region_bound1' => '53.540307,-11.030273',	// carte de France par défaut
			'region_bound2' => '39.095963,17.094727',	// carte de France par défaut
			'region_nb' => 0,
			'region_km' => 0.00,
			'region_kmc' => 0.00,
			'region_version' => ACY_VERSION
		);
		return $region;
	}

	// lecture des infos d'une region
	public function read_region( $rid ) {
		global $wpdb;

		$region = null;
		if ($rid) {
			$select = "SELECT * FROM `" . ACY_REGIONS_TABLE . "` WHERE `region_id`='$rid'";
			$region = $wpdb->get_row($select, ARRAY_A);
		}

		return $region;
	}


	private function geojson_filter($var)
	{
		// retourne vrai si le nom de fichier comporte .geojson
		return ( (strpos($var,'.geojson') === false) ? 0 : 1 );
	}

	// lecture des fichiers du repertoire amecycl/uploads
	public function read_upload_filenames() {

		$upload_dir = ACY_HOME_DIR . ACY_GEOJSON_UPLOAD_DIR;
		$files = scandir( $upload_dir );
		
		$geojson_files = array_filter($files, array($this, 'geojson_filter'));

		return $geojson_files;
	}


	// fourniture d'un slug region unique
	public function get_unique_region_slug( $slug ) {

		global $wpdb;

		$slug = sanitize_title($slug);
		$select = "SELECT * FROM `" . ACY_REGIONS_TABLE . "` WHERE `region_slug`='$slug'";
		$region = $wpdb->get_row($select, ARRAY_A);
		if (!empty($region)) {
			$slug = $slug . '-1';
		}
		return $slug;
	}


	// lecture des amenagements d'une region
	public function read_layers( $rid ) {
		global $wpdb;

		$layers = null;
		if ($rid) {
			$select = "SELECT * FROM `" . ACY_LAYERS_TABLE . "` WHERE `layer_region`='$rid'";
			$layers = $wpdb->get_results($select, ARRAY_A);
		}

		return $layers;
	}
	
	
	// lecture selective des amenagements d'une region
	// seuls les amenagements correspondant aux labels d'une configuration sont retournés
	// les données retournées correspondent à une jointure entre la table layers et la table setting_labels
	public function read_selected_layers( $rid, $sid ) {
		global $wpdb;

		$layers = null;
		if ( $rid && $sid ) {
			$select  = "SELECT * FROM `" . ACY_LAYERS_TABLE ."`" ;
			$select .= " LEFT JOIN `" . ACY_SETTING_LABELS_TABLE . "` ON `" . ACY_LAYERS_TABLE . "`.`layer_slug` = `" . ACY_SETTING_LABELS_TABLE . "`.`label_slug`";
			$select .= " WHERE `" . ACY_LAYERS_TABLE . "`.`layer_region`='$rid' AND `" . ACY_SETTING_LABELS_TABLE . "`.`label_setting`='$sid'";
			$select .= " ORDER BY `" . ACY_LAYERS_TABLE . "`.`layer_name` ASC";
			$layers = $wpdb->get_results($select, ARRAY_A);
		}

		return $layers;
	}


	// lecture des statistiques d'une region
	public function read_stats( $rid ) {
		global $wpdb;

		$stats = null;
		if ($rid) {
			$select = "SELECT * FROM `" . ACY_STATS_TABLE . "` WHERE `stat_region`='$rid'";
			$stats = $wpdb->get_results($select, ARRAY_A);
		}

		return $stats;
	}


	// lecture selective des statistiques d'une region
	// seules les statistiques correspondant aux labels d'une configuration sont retournées
	// les données retournées correspondent à une jointure entre la table stats et la table setting_labels
	public function read_selected_stats( $rid, $sid ) {
		global $wpdb;

		$stats = null;
		if ( $rid && $sid ) {
			$select  = "SELECT * FROM `" . ACY_STATS_TABLE ."`" ;
			$select .= " LEFT JOIN `" . ACY_SETTING_LABELS_TABLE . "` ON `" . ACY_STATS_TABLE . "`.`stat_slug` = `" . ACY_SETTING_LABELS_TABLE . "`.`label_slug`";
			$select .= " WHERE `" . ACY_STATS_TABLE . "`.`stat_region`='$rid' AND `" . ACY_SETTING_LABELS_TABLE . "`.`label_setting`='$sid'";
			$select .= " ORDER BY `" . ACY_STATS_TABLE . "`.`stat_name` ASC";
			$stats = $wpdb->get_results($select, ARRAY_A);
		}

		return $stats;
	}

	// lecture des types d'aménagements pour des régions
	public function get_stat_types( $rids ) {
		global $wpdb;

		$types = array();
		if (count($rids)) {
			$lstids = implode(',',$rids); // liste des ids des regions
			$select = "SELECT DISTINCT stat_name FROM `" . ACY_STATS_TABLE . "` WHERE `stat_region` IN(" . $lstids . ") ORDER BY stat_name";
			$results = $wpdb->get_results($select, ARRAY_A);
			if (count($results)) {
				foreach($results as $result) {
					$types[] = $result['stat_name'];
				}
			}
		}

		return $types;
	}


	// mise a jour d'une region en base
	// retourne l'id de la region modifiee 0 sinon
	// msgerr est renseigné en cas d'erreur
	public function update_region( $rid, $region, &$msgerr ) {
		global $wpdb;

		// lecture de la region prealablement enregistrée
		$pregion = $this->read_region($rid);

		if ( $pregion['region_filename'] != $region['region_filename'] ) {
			// mise à jour des enregistrements Layers et Stats de la région
			// suppression des anciens enregistrements
			$this->delete_stats_layers($region);

			// creation des nouveaux enregistrements
			$this->create_stats_layers($region, $msgerr);
		}

		// mise à jour de la table région avec les statistiques de la region
		$nbu = $wpdb->update( ACY_REGIONS_TABLE , $region, array( 'region_id' => $rid ) );
		$rid = $nbu ? $rid : 0;

		return $rid;
	}

	// insertion d'une region en base
	// retourne l'id de la region inseree 0 sinon
	public function insert_region( $region, &$msgerr ) {
		global $wpdb;

		$nbr = $wpdb->insert( ACY_REGIONS_TABLE , $region );

		$rid = 0;
		if ($nbr) {
			$rid = $wpdb->insert_id;
			$region['region_id'] = $rid ;

			// creation des enregistrements Layers et Stats de la région
			$nbf = $this->create_stats_layers($region, $msgerr);
			if (!$nbf) {
				// suppression suite à une erreur
				$wpdb->delete( ACY_REGIONS_TABLE , array('region_id' => $rid ) );
				$rid = 0;
			}
			else {
				// mise à jour de la table région avec les statistiques de la region et la bbox
				$nbu = $wpdb->update( ACY_REGIONS_TABLE , $region, array( 'region_id' => $rid ) );
				$rid = $nbu ? $rid : 0;
			}
		}

		return $rid;
	}


	// recherche d'une region
	public function search_region( $where ) {
		global $wpdb;

		$whereArray = array();
		foreach($where as $key => $value) {
				$whereArray[] = "`" . $key . "`" . "='" . $value . "'";
		}
		$whereClause = implode (' AND ', $whereArray);
		$whereClause = !empty($whereClause) ? $whereClause : '';

		$select = "SELECT * FROM `" . ACY_REGIONS_TABLE . "` WHERE " . $whereClause;
		
		$region = $wpdb->get_row($select, ARRAY_A);

		return $region;
	}


	// recherche d'une region avec like
	public function search_like_region( $like, $pid, $orderby='region_id', $order='asc' ) {
		global $wpdb;

		$search = '';
		if (!empty($like)) {
			$likeArray = array();
			foreach($like as $key => $value) {
				$likeArray[] = "`" . $key ."` LIKE '%" . $value .  "%'";
			}
			$search = '(' . implode(" OR ", $likeArray ) . ') ';
		}

		$where = (!empty($search)) ? "WHERE $search " : '';

		// lecture des regions
		$select = "SELECT * FROM `" . ACY_REGIONS_TABLE ."` $where ORDER BY `$orderby` $order";

		return $select;
	}

	// recherche d'une region et sa configuration avec like
	public function search_like_region_setting( $like, $pid, $orderby='region_id', $order='asc' ) {
		global $wpdb;

		$search = '';
		if (!empty($like)) {
			$likeArray = array();
			foreach($like as $key => $value) {
				$likeArray[] = "`" . $key ."` LIKE '%" . $value .  "%'";
			}
			$search = '(' . implode(" OR ", $likeArray ) . ') ';
		}

		$where = (!empty($search)) ? "WHERE $search " : '';

		// lecture des regions avec lecture de la configuration
		$select = "SELECT * FROM `" . ACY_REGIONS_TABLE ."` r  
				   LEFT JOIN `" . ACY_SETTINGS_TABLE ."` s 
				   ON r.region_setting = s.setting_id 
				   $where ORDER BY `$orderby` $order";
				  
		return $select;
	}


	// execution d'une requete
	public function exec( $select ){

		global $wpdb;

		$results = $wpdb->get_results($select, ARRAY_A);

		return $results;
	}


	// Création des cartes et des stats associée à la région
	private function create_stats_layers( &$region, &$msgerr ) {

		global $wpdb;
		
		$nbf = 0; // nombre de features

		// analyse du fichier geoJSON associé à la région
		if (!empty($region['region_filename'])) {
			// test de l'existence du fichier geojson dans le repertoire
			$upload_dir = ACY_HOME_DIR . ACY_GEOJSON_UPLOAD_DIR;
			$files = scandir( $upload_dir );
			
			if ( in_array($region['region_filename'], $files )) {

				$geofile = realpath( $upload_dir .'/' . $region['region_filename'] );

				$type = mime_content_type($geofile);
				$allowedFileType = [
					'application/json',
					'application/geo+json',
					'text/plain'
				];

				if (in_array($type, $allowedFileType)) {

					// creation de la table temporaire schema à partir du fichier
					$res = $this->get_schema($geofile);
					$nbf = $res['nbf'];
					if ($nbf) {

						// Mise à jour de la boite englobante
						$region['region_bound1'] = $res['bb1'];
						$region['region_bound2'] = $res['bb2'];

						// Calcul des statistiques
						$this->get_stats($region, $msgerr);

						if (!$msgerr) {
							// Création des calques de la carte
							$this->get_layers($region);
						}
						else {
							$nbf = 0;
						}
					}
				}
			}
			else {
				$msgerr = __('no existing file', 'amecycl');	
			}
		}
		else {
			$msgerr = __('blank file name', 'amecycl');
		}

		return $nbf;
	}


	// Création des calques et des stats associées à la région
	private function delete_stats_layers( $region ) {

		global $wpdb;
		
		$rid = $region['region_id'];
		if ($rid) {
			// suppression des calques
			$wpdb->delete( ACY_LAYERS_TABLE , array('layer_region' => $rid ));
			// suppression des statistiques
			$wpdb->delete( ACY_STATS_TABLE , array('stat_region' => $rid ));
			
			// suppression du repertoire download contenant les calques
			$this->delete_download_dir( $region['region_slug'] );
		}

		return;

	}


	// Construction de la table schema
	// retourne le nombre d'enregistrements créé en base
	private function get_schema( $geofile ) {

		global $wpdb;

		$nbf = 0;
		$bb1 = '';
		$bb2 = '';

		// creation de la table temporaire schema
		$columns = $this->init_schema_table();
		if (!isset($columns)) return $nbf;

		// lecture du fichier geoJSON 
		$data = file_get_contents($geofile);
		
		if ($data) {
			// Calcul de la boite englobante
			ini_set('memory_limit', '-1');	// nécessaire pour charger un gros fichier
			$geom = geoPHP::load($data,'geojson'); // load the geometry from the geojson content
			$bbox = $geom->getBBox();	
			
			$bb1 = $bbox['miny'] . ',' . $bbox['minx'];
			$bb2 = $bbox['maxy'] . ',' . $bbox['maxx'];

			// insertion des features dans la table temporaire
			$schema = json_decode($data);

			// traitement de chaque feature
			foreach($schema->features as $feature) {

				// calcul de la longueur de la voie
				$voie = geoPHP::load($feature,'geojson');
				$km = $voie->greatCircleLength();
				$feature->properties->longueur = $km;
				// ordonnancement et filtrage des proprietes de chaque feature
				$feature = $this->beautify_feature($feature);

				// insertion dans la table temporaire
				$row = (array)$feature->properties;
				$schema_row = array_intersect_key( $row, array_flip($columns) );

				// sauvegarde de la feature sous forme de blob
				$schema_row['feature'] = json_encode($feature);
				$nbi = $wpdb->insert( ACY_SCHEMA_TABLE, $schema_row );
				if ($nbi) $nbf++;
			}
		}

		return array('nbf' => $nbf, 'bb1' => $bb1, 'bb2' => $bb2 );
	}

	// ordonnancement et filtrage des proprietes de chaque feature
	public function beautify_feature( $feature ) {
		
		// suppression de la propriete geometrie
		unset($feature->properties->geom);
		
		// 2 chiffres après la virgule pour la longueur
		//$longueur = number_format($feature->properties->longueur, 2);
		//$feature->properties->longueur = $longueur;

		// filtrage et rie des clés
		$properties = (array)$feature->properties;
		$properties = array_filter($properties, array($this, "isnot_underscore_key"), ARRAY_FILTER_USE_KEY);
		ksort($properties);
		$feature->properties = (object) $properties;

		return $feature;
		
	}

	// filtre les clés qui commence par un underscore
	private function isnot_underscore_key( $key ) {
		$isnot_underscore_key = (substr($key, 0, 1) !== '_' ) ? true : false;
		return $isnot_underscore_key;
	}
		


	// Calcul du kilométrage des différents types d'aménagements cyclables
	public function get_stats( &$region, &$msgerr ) {

		global $wpdb;
		
		// coefficient de calcul du linéaire selon le type d'aménagement
		global $acy_ameLength_coeff;

		// selection des aménagements à droite

		// droite unidirectionnelle
		$select_unid  = "SELECT ame_d, COUNT(ame_d) AS nb, SUM(longueur) AS km FROM `" . ACY_SCHEMA_TABLE ."`";
		$select_unid .= " WHERE `sens_d` = 'UNIDIRECTIONNEL'";
		$select_unid .= " GROUP BY `ame_d`";

		$res_uni_droite = $wpdb->get_results($select_unid, ARRAY_A);


		// droite bidirectionnelle
		$select_bid  = "SELECT ame_d, COUNT(ame_d) AS nb, SUM(longueur) AS km FROM `" . ACY_SCHEMA_TABLE ."`";
		$select_bid .= " WHERE `sens_d` = 'BIDIRECTIONNEL'";
		$select_bid .= " GROUP BY `ame_d`";

		$res_bi_droite = $wpdb->get_results($select_bid, ARRAY_A);

		// selection des aménagements à gauche

		// gauche unidirectionnelle
		$select_unig  = "SELECT ame_g, COUNT(ame_g) AS nb, SUM(longueur) AS km FROM `" . ACY_SCHEMA_TABLE ."`";
		$select_unig .= " WHERE `sens_g` = 'UNIDIRECTIONNEL'";
		$select_unig .= " GROUP BY `ame_g`";
	
		$res_uni_gauche = $wpdb->get_results($select_unig, ARRAY_A);

		// gauche bidirectionnelle
		$select_big  = "SELECT ame_g, COUNT(ame_g) AS nb, SUM(longueur) AS km FROM `" . ACY_SCHEMA_TABLE ."`";
		$select_big .= " WHERE `sens_g` = 'BIDIRECTIONNEL'";
		$select_big .= " GROUP BY `ame_g`";
	
		$res_bi_gauche = $wpdb->get_results($select_big, ARRAY_A);


		// Insertion dans la table ACY_STATS_TABLE des kilométrages

		$keys = array();	// type d'amenagement

		// droite unidirectionnelle
		$unid = array();
		if (isset($res_uni_droite)) {
			foreach($res_uni_droite as $res) {
				$keys[] = $res['ame_d'];					// type d'amenagement
				$unid[$res['ame_d']]['nb'] = $res['nb'];    // nbre
				$unid[$res['ame_d']]['km'] = $res['km'];	// kilometrage
			}
		}

		// droite bidirectionnelle
		$bid = array();
		if (isset($res_bi_droite)) {
			foreach($res_bi_droite as $res) {
				$keys[] = $res['ame_d'];
				$bid[$res['ame_d']]['nb'] = $res['nb'];
				$bid[$res['ame_d']]['km'] = $res['km'];
			}
		}

		// gauche unidirectionnelle
		$unig = array();
		if (isset($res_uni_gauche)) {
			foreach($res_uni_gauche as $res) {
				$keys[] = $res['ame_g'];
				$unig[$res['ame_g']]['nb'] = $res['nb'];
				$unig[$res['ame_g']]['km'] = $res['km'];
			}
		}

		// gauche bidirectionnelle
		$big = array();
		if (isset($res_bi_gauche)) {
			foreach($res_bi_gauche as $res) {
				$keys[] = $res['ame_g'];
				$big[$res['ame_g']]['nb'] = $res['nb'];
				$big[$res['ame_g']]['km'] = $res['km'];
			}
		}

		$types_ame = array_unique($keys); // type d'aménagement distincts
		sort($types_ame);	// tri alphabetique
		
		// update / insertion des statistiques
		if (count($types_ame)) {
			
			// controle de la conformité des labels / schema de donnees
			$errors = array();
			foreach($types_ame as $type) {
				if (!array_key_exists($type, $acy_ameLength_coeff)) {
					$errors[] = $type;
				}
			}
		
			// labels conformes
			if (!count($errors)) {

				// suppression prealable des types d'amenagements deja enregistres en base pour la meme region et la même année
				$nbd = $wpdb->delete( ACY_STATS_TABLE , array('stat_region' => $region['region_id'] ));

				// statistiques pour la région tous types d'amenagements confondus
				$nbt = 0;
				$kmt = 0;
				$kmct = 0;

				// analyse par type d'amenagement
				foreach($types_ame as $type) {

					$nb_unid = (isset($unid[$type])) ? $unid[$type]['nb'] : 0;
					$m_unid = (isset($unid[$type])) ? $unid[$type]['km'] : 0;

					$nb_bid = (isset($bid[$type])) ? $bid[$type]['nb'] : 0;
					$m_bid = (isset($bid[$type])) ? $bid[$type]['km'] : 0;

					$nb_unig = (isset($unig[$type])) ? $unig[$type]['nb'] : 0;
					$m_unig = (isset($unig[$type])) ? $unig[$type]['km'] : 0;

					$nb_big = (isset($big[$type])) ? $big[$type]['nb'] : 0;
					$m_big = (isset($big[$type])) ? $big[$type]['km'] : 0;

					$nb = $nb_unid + $nb_bid + $nb_unig + $nb_big;			// nbre d'entites

					if ( isset( $acy_ameLength_coeff[$type] ) && ( $acy_ameLength_coeff[$type] < 1 ) ) {
						// cas d'une voie verte
						$kmc = ($m_unid + $m_bid + $m_unig + $m_big)/1000;	// lineaire cyclable en km
						$km = $kmc / 2;		// longueur de voie en km
					}
					else {
						$km = ($m_unid + $m_bid + $m_unig + $m_big)/1000;	// longueur de voie en km
						$kmc = ($m_unid + (2 * $m_bid) + $m_unig + (2 * $m_big))/1000;  // limeaire cyclable en km
					}

					$row = array (
						'stat_region' => $region['region_id'],
						'stat_modified' => date( ACY_DATE_FORMAT ),
						'stat_name' => $type,
						'stat_slug' => sanitize_title($type),
						'stat_nb_unid' => $nb_unid,
						'stat_m_unid' => $m_unid,
						'stat_nb_bid' => $nb_bid,
						'stat_m_bid' => $m_bid,
						'stat_nb_unig' => $nb_unig,
						'stat_m_unig' => $m_unig,
						'stat_nb_big' => $nb_big,
						'stat_m_big' => $m_big,
						'stat_nb' => $nb,
						'stat_km' => $km,
						'stat_kmc' => $kmc,
						'stat_version' => ACY_VERSION
					);

					// insertion en base
					$nbi = $wpdb->insert( ACY_STATS_TABLE, $row);

					if ($nbi) {
						$nbt = $nbt + $nb;		// nbre d'entites
						$kmt = $kmt + $km;		// longueur de voie en km
						$kmct = $kmct + $kmc;	// limeaire cyclable en km
					}

				}

				$region['region_nb'] = $nbt;
				$region['region_km'] = $kmt;
				$region['region_kmc'] = $kmct;
			}
			else {
				$msg = __('wrong labels: %s', 'amecycl');
				$lsterr = implode(', ',$errors);
				$msgerr = sprintf($msg, $lsterr);	
			}
		}

		return;
	}


	// Sauvegarde des cartes des différents types d'aménagements cyclables
	public function get_layers($region) {

		global $wpdb;

		$modified = date( ACY_DATE_FORMAT );

		// selection des aménagements non vides à droite - peu importe ce qu'il y a à gauche

		$selectd = "SELECT ame_d AS type, feature FROM `" . ACY_SCHEMA_TABLE .
			"` WHERE `ame_d`<>'AUCUN' ORDER BY `ame_d` ASC";

		$resd = $wpdb->get_results($selectd, ARRAY_A);

		// selection des aménagements non vides à gauche et vide à droite

		$selectg = "SELECT ame_g AS type, feature FROM `" . ACY_SCHEMA_TABLE .
			"` WHERE `ame_g`<>'AUCUN' AND `ame_d`='AUCUN' ORDER BY `ame_g` ASC";

		$resg = $wpdb->get_results($selectg, ARRAY_A);

		// selection des aménagements vide à droite et vide à gauche

		$selectdg = "SELECT ame_d AS type, feature FROM `" . ACY_SCHEMA_TABLE .
			"` WHERE (`ame_d`='AUCUN' AND `ame_g`='AUCUN') ORDER BY `ame_d` ASC";

		$resdg = $wpdb->get_results($selectdg, ARRAY_A);


		$layers = array();

		foreach($resd as $res) {
			if (!array_key_exists( $res['type'], $layers )) {
				$layers[$res['type']] = array();
			}
			// enregistrement de la feature
			$layers[$res['type']][] = $res['feature'];
		}

		foreach($resg as $res) {
			if (!array_key_exists( $res['type'], $layers )) {
				$layers[$res['type']] = array();
			}
			// enregistrement de la feature
			$layers[$res['type']][] = $res['feature'];
		}

		foreach($resdg as $res) {
			if (!array_key_exists( $res['type'], $layers )) {
				$layers[$res['type']] = array();
			}
			// enregistrement de la feature
			$layers[$res['type']][] = $res['feature'];
		}

		// insertions des calques en base de données
		if (count($layers)) {

			// creation du repertoire de depot des fichiers geoJson
			$dir_created = true;
			$download_dir = ACY_HOME_DIR . ACY_GEOJSON_DOWNLOAD_DIR . '/' . $region['region_slug'];
			if (!is_dir($download_dir)) {
				$dir_created = mkdir($download_dir, 0777, true);
			}

			$header = '{"type":"FeatureCollection","name":"%s","features":[';

			$footer = ']}';

			// tri des types d'aménagement
			ksort($layers);

			foreach($layers as $type => $features) {

				// construction de la couche
				$nbf = 0;
				$headertype = sprintf($header, $type);
				$linefeatures = array();
				for($i=0; $i<count($features); $i++) {
					$linefeatures[] = $features[$i];
					$nbf++;
				}
				$content = $headertype . implode(",\n", $linefeatures) . $footer;

				// insertion en base
				$row = array(
					'layer_modified' => $modified,
					'layer_region' => $region['region_id'],
					'layer_name' => $type,
					'layer_slug' => sanitize_title($type),
					'layer_nb_features' => $nbf,
					'layer_geojson' => $content,
					'layer_version' => ACY_VERSION
				);

				$nbi = $wpdb->insert( ACY_LAYERS_TABLE, $row);

				// creation d'un fichier geojson par type d'amenagement
				if ($dir_created) {
					$filename = sanitize_title($type);
					$handle = fopen($download_dir . '/' . $filename . ".geojson", "w");
					fwrite($handle, $content);
					fclose($handle);
				}
			}
		}
	}
	
	// suppression d'une region
	public function delete_region( $rid ) {
		global $wpdb;

		if ($rid) {
			$region = $this->read_region($rid);
			
			// suppression des calques
			$wpdb->delete( ACY_LAYERS_TABLE , array('layer_region' => $rid ));
			// suppression des statistiques
			$wpdb->delete( ACY_STATS_TABLE , array('stat_region' => $rid ));
			// suppression de la region
			$wpdb->delete( ACY_REGIONS_TABLE , array('region_id' => $rid ));
			
			// suppression du repertoire download contenant les calques
			$this->delete_download_dir( $region['region_slug'] );
			
		}

		return;
	}

	// suppression du repertoire download contenant les calques d'une région
	public function delete_download_dir( $slug ) {

		// suppression du reprtoire download qui contient les calques
		$download_dir = ACY_HOME_DIR . ACY_GEOJSON_DOWNLOAD_DIR . '/' . $slug;
		if (is_dir($download_dir)) {
			$this->rrmdir($download_dir);
		}		
	}

	// suppression recursif d'un repertoire
	private function rrmdir($dir) { 
	  foreach(glob($dir . '/*') as $file) { 
		if(is_dir($file)) rrmdir($file); else unlink($file); 
	  } rmdir($dir); 
	}

// ============================================================ Configurations

	// lecture / initialisation d'une configuration
	public function readInit_setting( $sid ) {
		if ($sid) {
			// lecture d'une configuration existante
			$setting =  $this->read_setting( $sid );
		}
		else {
			// Initialisation d'une nouvelle configuration
			$setting =  $this->init_setting();
		}
		return $setting;
	}

	// initialisation d'une structure configuration
	public function init_setting() {
		// creation d'une nouvelle configuration
		$setting = array(
			'setting_modified' => date( ACY_DATE_FORMAT ),
			'setting_name' => '',
			'setting_date' => '',
			'setting_slug' => '',
			'setting_desc' => '',
			'setting_version' => ACY_VERSION
		);
		return $setting;
	}

	// initialisation d'une structure label de configuration
	public function init_setting_label() {
		// creation d'un nouveau label de configuration
		$label = array(
			'label_modified' => date( ACY_DATE_FORMAT ),
			'label_name' => '',
			'label_slug' => '',
			'label_setting' => 0,
			'label_color' => '',
			'label_version' => ACY_VERSION
		);
		return $label;
	}


	// lecture de toutes les configurations
	public function read_settings( ) {
		global $wpdb;

		$settings = null;
		$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE . "`";
		$settings = $wpdb->get_results($select, ARRAY_A);

		return $settings;
	}


	// lecture des infos d'une configuration
	public function read_setting( $sid ) {
		global $wpdb;

		$setting = null;
		if ($sid) {
			$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE . "` WHERE `setting_id`='$sid'";
			$setting = $wpdb->get_row($select, ARRAY_A);
		}

		return $setting;
	}

	// lecture des labels d'une configuration
	public function read_setting_labels( $sid ) {
		global $wpdb;

		$labels = null;
		if ($sid) {
			$select = "SELECT * FROM `" . ACY_SETTING_LABELS_TABLE . "` WHERE `label_setting`='$sid' ORDER BY `label_name` ASC";
			$labels = $wpdb->get_results($select, ARRAY_A);
		}
		else {
			// lecture de la configuration par defaut
			$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE . "` WHERE `setting_slug`='defaut'";
			$setting = $wpdb->get_row($select, ARRAY_A);
			if ($setting) {
				$sid = $setting['setting_id'];
				$select = "SELECT * FROM `" . ACY_SETTING_LABELS_TABLE . "` WHERE `label_setting`='$sid' ORDER BY `label_name` ASC";
				$labels = $wpdb->get_results($select, ARRAY_A);			
			}
		}

		return $labels;
	}

	// insertion d'une configuration en base
	// retourne l'id de la configuration inseree 0 sinon
	public function insert_setting( $setting ) {
		global $wpdb;

		$nbr = $wpdb->insert( ACY_SETTINGS_TABLE , $setting );

		$sid = 0;
		if ($nbr) {
			$sid = $wpdb->insert_id;
			$setting['setting_id'] = $sid ;
		}

		return $sid;
	}
	
	// mise a jour d'une configuration en base
	// retourne l'id de la configuration modifiee 0 sinon
	public function update_setting( $sid, $setting ) {
		global $wpdb;

		// mise à jour des labels
		$this->update_setting_labels($setting);

		// mise à jour de la table configuration avec les labels de la configuration
		$nbu = $wpdb->update( ACY_SETTINGS_TABLE , $setting, array( 'setting_id' => $sid ) );
		$sid = $nbu ? $sid : 0;

		return $sid;
	}


	// insertion d'un label de configuration en base
	// retourne l'id du label insere 0 sinon
	public function insert_setting_label( $label ) {
		global $wpdb;

		$nbr = $wpdb->insert( ACY_SETTING_LABELS_TABLE , $label );

		$lid = 0;
		if ($nbr) {
			$lid = $wpdb->insert_id;
			$label['label_id'] = $lid ;
		}
		return $lid;
	}


	// update des labels d'une configuration
	// modification par suppression de tous les labels puis insertion des nouveaux labels
	// generation du fichier .css correspondant
	// retourne le nombre de labels inserés
	public function update_setting_labels( $sid=0, $setting, $items ) {
		global $wpdb;

		if ($sid) {
			// suppression des labels si ils existent
			$select = "SELECT * FROM `" . ACY_SETTING_LABELS_TABLE . "` WHERE `label_setting`='$sid'";
			$labels = $wpdb->get_results($select, ARRAY_A);

			if (count($labels)) {
				// suppression des labels préexistants
				$nbl = $wpdb->delete( ACY_SETTING_LABELS_TABLE , array('label_setting' => $sid ));
			}
		}
		else {
			// creation de la configuration
			$sid = $this->insert_setting($setting);
		}

		$setting_label = $this->init_setting_label();
		$nbl = 0;
		
		if ($sid) {
			// ajout des labels
			foreach($items as $item) {
				if ( count($item) == 3 ) {
					$setting_label['label_name'] = $item['label'];
					$setting_label['label_slug'] = sanitize_title($setting_label['label_name']);
					$setting_label['label_setting'] = $sid;
					$setting_label['label_color'] = $item['color'];
					// insertion du label de configuration
					$this->insert_setting_label($setting_label);
					$nbl++;
				}
			}

			// generation du fichier css correspondant à la configuration
			$this->generate_setting_css_file($sid);

		}

		return $sid;
	}	


	// recherche d'une configuration
	public function search_setting( $where ) {
		global $wpdb;

		$whereArray = array();
		foreach($where as $key => $value) {
				$whereArray[] = "`" . $key . "`" . "='" . $value . "'";
		}
		$whereClause = implode (' AND ', $whereArray);
		$whereClause = !empty($whereClause) ? $whereClause : '';

		$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE . "` WHERE " . $whereClause;
		
		$setting = $wpdb->get_row($select, ARRAY_A);

		return $setting;
	}


	// recherche d'une configuration avec like
	public function search_like_setting( $like, $pid, $orderby='setting_id', $order='asc' ) {
		global $wpdb;

		$search = '';
		if (!empty($like)) {
			$likeArray = array();
			foreach($like as $key => $value) {
				$likeArray[] = "`" . $key ."` LIKE '%" . $value .  "%'";
			}
			$search = '(' . implode(" OR ", $likeArray ) . ') ';
		}

		$where = (!empty($search)) ? "WHERE $search " : '';

		// lecture des configurations
		$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE ."` $where ORDER BY `$orderby` $order";

		return $select;
	}

	// recherche d'une configuration avec like - sauf defaut - utiliser pour masquer defaut de la liste
	public function search_like_setting_except_default( $like, $pid, $orderby='setting_id', $order='asc' ) {
		global $wpdb;

		$search = '';
		if (!empty($like)) {
			$likeArray = array();
			foreach($like as $key => $value) {
				$likeArray[] = "`" . $key ."` LIKE '%" . $value .  "%'";
			}
			$search = '(' . implode(" OR ", $likeArray ) . ') ';
		}

		$where = (!empty($search)) ? "WHERE `setting_id`<>'defaut' AND $search " : "WHERE `setting_slug`<>'defaut' ";

		// lecture des configurations
		$select = "SELECT * FROM `" . ACY_SETTINGS_TABLE ."` $where ORDER BY `$orderby` $order";

		return $select;
	}


	// Generation dans le repertoire configurations du fichier .css correspondant à la configuration
	// Seul le fichier defaut va dans le repertoire public/css
	// Retourne le nombre de labels ecrits dans le fichier css
	public function generate_setting_css_file( $sid ) {
		
		global $wpdb;

		$setting = $this->read_setting($sid);
		$labels = $this->read_setting_labels($sid);
		
		if ( $setting['setting_slug'] === 'defaut' ) {
			$dirname = ACY_PLUGIN_DIR . 'public/css/';
		}
		else {
			// creation du repertoire des fichiers css de configuration
			$dirname = ACY_PLUGIN_DIR . 'public/css/' . ACY_SETTINGS_CSS . '/';
			if (!is_dir($dirname)) {
				mkdir($dirname, 0777, true);
			}
		}

		$filename = 'amecycl-' . $setting['setting_slug'] . '.css'; 
	
		$tpl_title = '/* Styles pour la configuration "%s" */' . PHP_EOL . PHP_EOL;
		$tpl_label = '/* %s */' . PHP_EOL;
		$tpl_style = 'input[type="checkbox"].typame + label.%s::before { color: %s; }' . PHP_EOL . PHP_EOL;
				
		$nbl = 0;
		if ($f = fopen ( $dirname . $filename , 'w')) {
			fwrite($f, sprintf($tpl_title, $setting['setting_name']));
			foreach($labels as $label) {
				// ecriture du label en commentaire
				fwrite($f, sprintf($tpl_label, $label['label_name']));
				// ecriture du style
				fwrite($f, sprintf($tpl_style, $label['label_slug'], $label['label_color']));
				$nbl++;
			}			
		}
		fclose($f);
		
		return $nbl;
	}


	// suppression d'une configuration
	// la suppression d'une configuration utilisée par une region bascule automatiquement la region sur la configuration par defaut
	// le fichier css est supprime du repertoire public/css/configurations
	public function delete_setting( $sid ) {
		global $wpdb;

		$setting = '';

		if ($sid) {
			
			// lecture de la configuration par defaut 
			$default_setting = $this->search_setting( array( 'setting_slug' => 'defaut' ) );
			$dsid = $default_setting['setting_id'];

			// lecture de la configuration à supprimer
			$setting = $this->search_setting( array( 'setting_id' => $sid ) );

			// suppression des labels 
			$wpdb->delete( ACY_SETTING_LABELS_TABLE , array('label_setting' => $sid ));
			// suppression de la configuration
			$wpdb->delete( ACY_SETTINGS_TABLE , array('setting_id' => $sid ));

			if (!empty($setting)) {
				// suppression du fichier css correspondant
				$dirname = ACY_PLUGIN_DIR . 'public/css/' . ACY_SETTINGS_CSS . '/';
				$filename = 'amecycl-' . $setting['setting_slug'] . '.css'; 
				unlink( $dirname . $filename );
				
				// mise à jour de la configuration des regions qui utilisait cette configuration
				$select = "SELECT * FROM `" . ACY_REGIONS_TABLE . "` WHERE `region_setting` = " . $sid;	
				$regions = $wpdb->get_results($select, ARRAY_A);
				if (count($regions)) {
					foreach($regions as $region) {
						$region['region_setting'] = $dsid;
						$nbu = $wpdb->update( ACY_REGIONS_TABLE , $region, array( 'region_id' => $region['region_id'] ));
					}
				}
			}
		}

		return $setting;
	}

	// Désinstallation du plugin Amecycl
	// suppression de toutes les tables regions, amenagements cyclables (cartes et stats) et configurations
	// suppression des repertoires amecycl/uploads et amecyc/downloads
	public static function uninstall_amecycl() {
	
		global $wpdb;

		// ============== suppression des tables
		$show = "SHOW TABLES LIKE '" . ACY_REGIONS_TABLE . "'";
		if( $wpdb->get_var($show) == ACY_REGIONS_TABLE ) {
			//suppression de la table REGIONS
			$wpdb->query("DROP TABLE `" . ACY_REGIONS_TABLE . "`");
		}

		$show = "SHOW TABLES LIKE '" . ACY_STATS_TABLE . "'";
		if( $wpdb->get_var($show) == ACY_STATS_TABLE ) {
			//suppression de la table STATS
			$wpdb->query("DROP TABLE `" . ACY_STATS_TABLE . "`");
		}

		$show = "SHOW TABLES LIKE '" . ACY_LAYERS_TABLE . "'";
		if( $wpdb->get_var($show) == ACY_LAYERS_TABLE ) {
			//suppression de la table LAYERS
			$wpdb->query("DROP TABLE `" . ACY_LAYERS_TABLE . "`");
		}

		$show = "SHOW TABLES LIKE '" . ACY_SETTINGS_TABLE . "'";
		if( $wpdb->get_var($show) == ACY_SETTINGS_TABLE ) {
			//suppression de la table SETTINGS
			$wpdb->query("DROP TABLE `" . ACY_SETTINGS_TABLE . "`");
		}

		$show = "SHOW TABLES LIKE '" . ACY_SETTING_LABELS_TABLE . "'";
		if( $wpdb->get_var($show) == ACY_SETTING_LABELS_TABLE ) {
			//suppression de la table SETTING_LABELS
			$wpdb->query("DROP TABLE `" . ACY_SETTING_LABELS_TABLE . "`");
		}

		// ============== suppression du repertoire ACY_GEOJSON_DIR
		if (!defined('ACY_HOME_DIR')) {
			require_once( ABSPATH . 'wp-admin/includes/file.php' );
			define("ACY_HOME_DIR", get_home_path());	// home path 
			define("ACY_GEOJSON_DIR", 'amecycl');		// répertoire des fichiers geojson
		}
		
		$acydir = ACY_HOME_DIR . ACY_GEOJSON_DIR;
		if (is_dir($acydir)) {
			self::deletedir($acydir);
		}
		// this is the end ...
	}
	
	// suppression recursif d'un repertoire - methode static
	public static function deletedir($dir) { 
	  foreach(glob($dir . '/*') as $file) { 
		if(is_dir($file)) self::deletedir($file); else unlink($file); 
	  } rmdir($dir); 
	}


}