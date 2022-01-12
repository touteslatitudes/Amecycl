<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Amecycl
 * @subpackage Amecycl/public
 * @author     toutesLatitudes <contact@randovelo.touteslatitudes.fr>
 */
class Amecycl_Public {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	//Store plugin main class to allow public access.
	public $main;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_main ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->main = $plugin_main;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Amecycl_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Amecycl_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		 
		global $wp_query;

		$post = (object) $wp_query->get_queried_object();
		
		$shortcode = 'amecycl';

		if ( isset($post) && isset($post->post_content) ) {

			$pattern = '\[' . $shortcode;

			if (preg_match( '/'. $pattern .'/s', $post->post_content )) {
				
				// chargement des styles uniquement si le shortcode est présent dans la page

				wp_enqueue_style( 'leaflet', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.css', array(), '1.7.1', 'all' );

				wp_enqueue_style( 'leaflet-sidebar2', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-sidebar-v2/css/leaflet-sidebar.min.css', array(), null, 'all' );
				wp_enqueue_style( 'leaflet-sidebar1', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-sidebar-v1/L.Control.Sidebar1.css', array(), null, 'all' );
				wp_enqueue_style( 'leaflet-fullscreen', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-fullscreen/leaflet.fullscreen.css', array(), null, 'all' );
				wp_enqueue_style( 'leaflet-geocoder', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-control-osm-geocoder/Control.OSMGeocoder.css', array(), null, 'all' );

				wp_enqueue_style( 'amecycl-public', plugin_dir_url( __FILE__ ) . 'css/amecycl-public.css', array(), ACY_VERSION, 'all' );
			}		
		}

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Amecycl_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Amecycl_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $wp_query;

		$post = (object) $wp_query->get_queried_object();
		
		$shortcode = 'amecycl';

		if ( isset($post) && isset($post->post_content) ) {

			$pattern = '\[' . $shortcode;

			if (preg_match( '/'. $pattern .'/s', $post->post_content )) {
				
				// chargement des scripts uniquement si le shortcode est présent dans la page
				wp_enqueue_script( 'leaflet', plugin_dir_url( __FILE__ ) . 'leaflet/leaflet.js', array(), '1.7.1', true );

				wp_enqueue_script( 'leaflet-sidebar2', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-sidebar-v2/js/leaflet-sidebar.min.js', array('leaflet'), null, true );
				wp_enqueue_script( 'leaflet-sidebar1', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-sidebar-v1/L.Control.Sidebar1.min.js', array('leaflet'), null, true );
				wp_enqueue_script( 'leaflet-fullscreen', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-fullscreen/Leaflet.fullscreen.min.js', array('leaflet'), null, true );
				wp_enqueue_script( 'leaflet-geocoder', plugin_dir_url( __FILE__ ) . 'leaflet-plugins/leaflet-control-osm-geocoder/Control.OSMGeocoder.min.js', array('leaflet'), null, true );

				wp_enqueue_script( 'amecycl-public', plugin_dir_url( __FILE__ ) . 'js/amecycl-public.min.js', array( 'jquery','leaflet' ), ACY_VERSION, true );
			}		
		}
	}


	/**
	 * amecycl shortcode
	 *
	 * shortcode amecycl : affichage de la carte d'une région
	 *
	 * paramètres :
	 *	id        : id de la région
	 *  slug      : identifiant textuel de la region
	 *  setting   : identifiant textuel (slug) de la configuration - surcharge la configuration attachée à la région
	 *  width     : largeur du div englobant - en % ou en px
	 *  height    : hauteur du div englobant - en px
	 *  padding   : valeur du padding - positif sans unité (3 par defaut)
	 *  deltazoom : valeur du delta zoom - entier positif ou négatif - permet de modifier le zoom obtenu après le mapBound (0 par defaut)
	 *
	 * Affichage d'une carte : [amecycl id='3']
	 * Affichage d'une carte avec une configuration: [amecycl id='3' setting='cfg2021']
	 * Affichage d'une carte : [amecycl id='3' width='100%']
	 * Affichage d'une carte par son slug : [amecycl slug='ame-orleans-2020' width='700px' height='700px']
	 * Affichage d'une carte par son slug : [amecycl slug='ame-orleans-2020' height='700px']
	 *
	 * Le shortcode peut être utilisé via un i-frame. Les parametres sont pris en compte via $_GET
	 *
	 * @since    1.0.0
	 */
	function amecycl_shortcode( $atts ) {

		$html = '';

		$default_width = '100%';
		$default_height = '700px';
		$default_padding = 3;
		$default_deltazoom = 0;

		$args = shortcode_atts(
			array(
				'id'    => '0',						// obligatoire sauf si slug est défini
				'slug'  => '',						// slug
				'setting' => '',					// configuration. Surcharge la configuration de la région
				'width' => $default_width,			// largeur en % ou en px - de 300px à 1200px - 100% par defaut
				'height' => $default_height,		// hauteur en px de 100px à 1000px. 500px par défaut
				'padding' => $default_padding,		// padding en valeur absolue sans unité
				'deltazoom' => $default_deltazoom 	// delta zoom
				
			),
			$atts
		);

		// rid argument du shortcode ou du Get
		$rid = ( strtolower( $args['id']) != "" ) ? (int) $args['id'] : 0;
		$grid = (isset($_GET['id']) && !empty($_GET['id'])) ? $_GET['id'] : $rid;
		$rid = ($grid) ? $grid : $rid;
		$rid = trim($rid);

		// slug argument du shortcode ou du Get
		$slug = (!empty($args['slug'])) ? $args['slug'] : '';
		$gslug = (isset($_GET['slug']) && !empty($_GET['slug'])) ? $_GET['slug'] : $slug;
		$slug = ($gslug) ? $gslug : $slug;
		$slug = trim($slug);

		// setting argument du shortcode ou du Get
		$setting = (!empty($args['setting'])) ? $args['setting'] : '';
		$gsetting = (isset($_GET['setting']) && !empty($_GET['setting'])) ? $_GET['setting'] : $setting;
		$setting = ($gsetting) ? $gsetting : $setting;
		$setting = trim($setting);

		// width argument du shortcode ou du Get - en % ou en px
		$width = ( $args['width'] != "" ) ? $args['width'] : $default_width;
		$gwidth = (isset($_GET['width']) && !empty($_GET['width'])) ? $_GET['width'] : $width;
		$width = ($gwidth) ? $gwidth : $width;
		$width = trim($width);

		// controle width
		$pos = strpos($width, 'px');
		if (($pos >= 3) && ($pos <= 4)) {
			$nb = (int) substr($width, 0, $pos);
			if ($nb < 300 || $nb > 1200) {
				$width = $default_width;
			}
			else {
				$width = $nb . 'px';
			}
		}
		else {
			// controle %
			$pos = strpos($width, '%');
			if (($pos >= 2) && ($pos <= 3)) {
				$nb = (int) substr($width, 0, $pos);
				if ($nb < 10 || $nb > 100) {
					$width = $default_width;
				}
				else {
					$width = $nb . '%';
				}
			}
			else {
				$width = $default_width;
			}
		}

		// height argument du shortcode ou du get - en px
		$height = ( $args['height'] != "" ) ? $args['height'] : $default_height;
		$gheight = (isset($_GET['height']) && !empty($_GET['height'])) ? $_GET['height'] : $height;
		$height = ($gheight) ? $gheight : $height;
		$height = trim($height);
		
		// padding
		$padding = ( $args['padding'] != "" ) ? $args['padding'] : $default_padding;
		$gpadding = (isset($_GET['padding']) && !empty($_GET['padding'])) ? $_GET['padding'] : $padding;
		$padding = ($gpadding) ? $gpadding : $padding;
		
		$padding = (int) trim($padding);
		if (!is_int($padding)) $padding = $default_padding;

		// deltazoom
		$deltazoom = ( $args['deltazoom'] != "" ) ? $args['deltazoom'] : $default_deltazoom;
		$gdeltazoom = (isset($_GET['deltazoom']) && !empty($_GET['deltazoom'])) ? $_GET['deltazoom'] : $deltazoom;
		$deltazoom = ($gdeltazoom) ? $gdeltazoom : $deltazoom;
		$deltazoom = (int) $deltazoom;
		
		// controle px
		$pos = strpos($height, 'px');
		if (($pos >= 3) && ($pos <= 4)) {
			$nb = (int) substr($height, 0, $pos);
			if ($nb < 100 || $nb > 1200) {
				$height = $default_height;
			}
			else {
				$height = $nb . 'px';
			}
		}
		else {
			$height = $default_height;
		}

		if ( (!empty($rid)) || (!empty($slug))) {
			if (empty($rid)) {
				// recherche de la region par slug
				$res = $this->main->model->search_region(array('region_slug' => $slug));
				if (!empty($res)) $rid = $res['region_id'];
			}
			if (!empty($rid)) {
				// generation de la vue
				$msgerr = '';
				$map = $this->get_amecycl_view( $rid, $setting, $width, $height, $deltazoom, $padding, $msgerr );
				if (!$msgerr) {
					$html = '<div class="map-container">' . $map . '</div>';
				}
				else {
					$html = '<p class="acy-error">' .  $msgerr  . '</p>';
				}
			}
			else {
				$msg = sprintf( __('Map - %s : Unknown region slug - Check the shortcode call !','amecycl'), $slug );
				$html = '<p class="acy-error">' . $msg . '</p>';
			}
		}
		else {
			$html = '<p class="acy-error">' . __('Map - You should provide a region slug. Check the shortcode call !','amecycl') . '</p>';
		}

		return $html;
	}


	/**
	 * Get amecycl view
	 *
	 * Generation de la vue du plugin à partir du template
	 *
	 * @since    1.0.0
	 */
	 private function get_amecycl_view( $rid, $sc_setting, $width, $height, $deltazoom, $padding, &$msgerr ) {
		 
		$html = '';

		// infos sur la region
		$region = $this->main->model->read_region($rid);

		// Surcharge eventuelle de la configuration
		if ($sc_setting) {
			// Surcharge de la configuration - recherche de l'id de la configuration choisie
			$setting = $this->main->model->search_setting( array('setting_slug' => $sc_setting) );
			if (!empty($setting)) {
				// surcharge de l'id de configuration
				$region['region_setting'] = $setting['setting_id'];
			}
			else {
				$msgerr = sprintf(__('Map - %s : Unknown setting. Check the shortcode call !', 'amecycl'), $sc_setting);
				return 0;
			}
		}
		
		// Prise en compte de la configuration
		$sid = $region['region_setting'];
		$setting = $this->main->model->read_setting( $sid );
		$labels = $this->main->model->read_setting_labels( $sid );
		
		// Chargement du fichier css correspondant à la configuration. Nécessaire pour la couleur des cases à cocher
		if ($setting['setting_slug'] === 'defaut') {
			// repertoire public/css
			wp_enqueue_style( 'amecycl-config',  plugin_dir_url( __FILE__ ) . 'css/amecycl-defaut.css', array(), '1.0.0', 'all' );
		}
		else {
			// repertoire configurations 
			wp_enqueue_style( 'amecycl-config', plugin_dir_url( __FILE__ ) . 'css/' . ACY_SETTINGS_CSS . '/amecycl-' . $setting['setting_slug'] . '.css', array(), '1.0.0', 'all' );
		}

		// infos sur les amenagements. Seules les couches de la configuration sont prises en compte
		$layers = $this->main->model->read_selected_layers( $rid, $sid );

		// Liste des aménagements et couleurs
		$ameColors = array();
		foreach($labels as $label) {
			$ameColors[ $label['label_name'] ] = $label['label_color'];
		}
		
		if (count($layers)) {
			// chargement de la classe template
			if( ! class_exists( 'ACY_Template' ) ) {
				require_once(sprintf("%s/class-amecycl-template.php", dirname(__FILE__)));
			}
			$tplfile = dirname(__FILE__) . '/partials/amecycl-public-display.php';
			$tpl = new ACY_Template( $tplfile );	// instanciation du moteur

			// instanciation de l'id associé à l'instance amecycl
			$tpl->assign('ame_rid', $rid);

			// Présentation des statistiques en introduction. Seules les stats de la configuration sont prises en compte
			$stats = $this->main->model->read_selected_stats($rid, $sid);			
			
		    $ame_presentation = $this->get_ame_stats_html($region, $stats);	// introduction et stats au format html
			$tpl->assign('ame_presentation', $ame_presentation);

			$ame_menu = $this->get_ame_menu_html($rid, $layers);	// menu de selection des aménagements au format html
			$tpl->assign('ame_menu', $ame_menu);

			$ame_download_list = $this->get_ame_download_list_html($region['region_slug'], $layers);
			$tpl->assign('ame_download_list', $ame_download_list);

			$html = $tpl->parse();

			// Chargement de la clé d'API IGN si définie
			$iak_arg = defined('ACY_IGN_API_KEY') ? ',iak:"' . ACY_IGN_API_KEY . '"' : '';
			
			// OpenCycleMap key si definie
			$oak_arg = defined ('ACY_OCM_API_KEY') ? ',oak:"' . ACY_OCM_API_KEY . '"' : '';


			wp_localize_script( 'amecycl-public', 'acyvar', array('url' => site_url('/'), 'ameColors' => $ameColors ));

			$html .= '<script type="text/javascript">
jQuery(document).ready(function($){
	var map' . $rid. '= $("#map-' . $rid. '");
	map' . $rid. '.acyLeaflet({
		rid:' .$rid.',
		bnd1:[' . $region['region_bound1'] . '],bnd2:[' . $region['region_bound2'] . '],
        width:"' . $width . '",height:"' . $height . '",deltazoom:"' . $deltazoom . '",padding:"' . $padding . '",maps:[' . ACY_MAPS . ']' . $iak_arg . $oak_arg . '
	});
	map' . $rid. '.acyLeaflet().addAmes({rid:' . $rid. ', sid: ' .$sid. '});
});
</script>';
		}

		return $html;
	}


	/**
	 * Get ame download list
	 *
	 * Retourne la liste des fichiers à telecharger
	 *
	 * @since    1.0.0
	 */
	public function get_ame_download_list_html($region_slug, $layers) {

		$list = '';
		$download_dir = ACY_SITE_URL . '/' . ACY_GEOJSON_DOWNLOAD_DIR;

		if (count($layers)) {
			$list = '<ul>';
			$item = '<li><a href="%s" target="_blank" rel="nofollow noopener">%s</a></li>';

			foreach($layers as $layer) {
				$label = $this->mb_ucfirst(mb_strtolower($layer['layer_name'], 'UTF-8'), 'UTF-8');
				$url = $download_dir .'/'. $region_slug . '/' . $layer['layer_slug'] . '.geojson';
				$list .= sprintf($item, $url, $label);
			}
			$list .= '</ul>';
		}

		return $list;
	}


	/**
	 * Get ame menu
	 *
	 * Retourne le menu de selection des amenagements
	 *
	 * @since    1.0.0
	 */
	public function get_ame_menu_html($rid, $layers) {

		$menu = '';
		if (count($layers)) {
			$menu = '<ul>';
			$item = '<li><div style="display:flex;align-items:top;">';
			$item .= '<input id="%s-' . $rid . '" type="checkbox" class="typame typame-' . $rid . '" name="%s"><label class="%s" for="%s-' . $rid . '">%s</label></div></li>';

			foreach($layers as $layer) {
				$name = $layer['layer_name'];
				$slug = $layer['layer_slug'];
				$label = $this->mb_ucfirst(mb_strtolower($layer['layer_name'], 'UTF-8'), 'UTF-8');
				$menu .= sprintf($item, $slug, $name, $slug, $slug, $label);
			}
			$menu .= '</ul>';
		}
		return $menu;
	}
	
	private function mb_ucfirst($string, $encoding) {
	   $firstChar = mb_substr($string, 0, 1, $encoding);
	   $then = mb_substr($string, 1, null, $encoding);
	   return mb_strtoupper($firstChar, $encoding).$then;
	}

	/**
	 * Get ame desc + stats
	 *
	 * Retourne la page d'introduction du side-bar avec les statistiques globales à la region
	 *
	 * @since    1.0.0
	 */
	 private function get_ame_stats_html( $region, $stats ) {

		// presentation
		$html  = '<div class="ame-pres">';
		$html .= '<div class="ame-desc">' . $region['region_desc'] . '</div>';

		// cumul du linéaire filtre par la configuration
		$km = 0;
		$kmc = 0;
		foreach($stats as $stat) {
			$km += $stat['stat_km'];
			$kmc += $stat['stat_kmc'];
		}

		$html .= '<div class="ame-stat">';
		$html .= __('region_km:','amecycl') . ' <strong>' . round($km) . ' km</strong><br/>';
		$html .= __('region_kmc:','amecycl') . ' <strong>' . round($kmc) . ' km</strong>';
		$html .= '</div>';
		
		// copyright
		$datadate = (!empty($region['region_datadate'])) ? ' du <strong>' . $region['region_datadate'] . '</strong>' : '';
		$osm = sprintf( __('osm date','amecycl'), $datadate);
		$copyright = sprintf( __('copyright','amecycl'), ACY_VERSION);
		$html .= '<div class="ame-copyright">' . $osm . '<br/>' . $copyright .'</div>';

		$html .= '</div>';

		return $html;
	 }



	/**
	 * amecycl-stats shortcode
	 *
	 * shortcode-stats amecycl : affichage des statistiques d'une ou plusieurs régions
	 *
	 * paramètres :
	 *	ids       : id des régions
	 *  slugs     : identifiants textuels des regions
	 *  setting   : identifiant textuel (slug) de la configuration à utiliser pour l'affichage
	 *  width     : largeur alloué au tableau en px ou en %
	 *  align     : left|center|right
	 *  font-size : taille de la police de caractères
	 *
	 * Affichage des statistiques : [amecycl-stats id='3,4,5']
	 * Affichage des statistiques en utilisant les slugs : [amecycl-stats slugs='ame-orleans-2020,tours-2020,bourges-2020']
	 * Affichage des statistiques en utilisant une configuration : [amecycl-stats slugs='ame-orleans-2020,tours-2020,bourges-2020' setting='config-2020']
	 *
	 * Le choix d'une configuration s'applique à toutes les régions de la liste ids ou slugs
	 *
	 * @since    1.0.0
	 */
	function amecycl_stats_shortcode( $atts ) {

		$html = '';
		
		$default_width = '100%';
		$default_font_size = '12px';

		$args = shortcode_atts(
			array(
				'ids'    => '0',						// obligatoire sauf si slugs est défini
				'slugs'  => '',							// slug
				'setting' => '',						// configuration. Surcharge la configuration des régions
				'width' => $default_width,				// largeur en % ou en px - de 300px à 1200px - 100% par defaut
				'align' => 'left',						// alignement horizontal
				'font-size' => $default_font_size		// font-size de 8px à 24px - defaut : 12px
			),
			$atts
		);

		// rids argument du shortcode ou du Get
		$ids = ( strtolower( $args['ids']) != "" ) ? $args['ids'] : 0;
		$gids = (isset($_GET['ids']) && !empty($_GET['ids'])) ? $_GET['ids'] : $ids;
		$ids = ($ids) ? $ids : $gids;
		$ids = implode(',', array_map("trim",explode(',',$ids)));
		
		// slugs argument du shortcode ou du Get
		$slugs = (!empty($args['slugs'])) ? $args['slugs'] : '';
		$gslugs = (isset($_GET['slugs']) && !empty($_GET['slugs'])) ? $_GET['slugs'] : $slugs;
		$slugs = ($slugs) ? $slugs : $gslugs;
		$slugs = implode(',', array_map("trim",explode(',',$slugs)));

		// setting argument du shortcode ou du Get
		$setting = (!empty($args['setting'])) ? $args['setting'] : '';
		$gsetting = (isset($_GET['setting']) && !empty($_GET['setting'])) ? $_GET['setting'] : $setting;
		$setting = ($gsetting) ? $gsetting : $setting;
		$setting = trim($setting);

		// width argument du shortcode ou du Get - en % ou en px
		$width = ( $args['width'] != "" ) ? $args['width'] : $default_width;
		$gwidth = (isset($_GET['width']) && !empty($_GET['width'])) ? $_GET['width'] : $width;
		$width = ($width) ? $width : $gwidth;
		$width = trim($width);

		// controle width
		$pos = strpos($width, 'px');
		if (($pos >= 3) && ($pos <= 4)) {
			$nb = (int) substr($width, 0, $pos);
			if ($nb < 300 || $nb > 1200) {
				$width = $default_width;
			}
			else {
				$width = $nb . 'px';
			}
		}
		else {
			// controle %
			$pos = strpos($width, '%');
			if (($pos >= 2) && ($pos <= 3)) {
				$nb = (int) substr($width, 0, $pos);
				if ($nb < 10 || $nb > 100) {
					$width = $default_width;
				}
				else {
					$width = $nb . '%';
				}
			}
			else {
				$width = $default_width;
			}
		}

		// align argument du shortcode ou du Get
		$align = (!empty($args['align'])) ? $args['align'] : 'left';
		$galign = (isset($_GET['align']) && !empty($_GET['align'])) ? $_GET['align'] : $align;
		$align = ($align) ? $align : $galign;
		$align = trim($align);

		// font_size argument du shortcode ou du Get - en % ou en px
		$font_size = ( $args['font-size'] != "" ) ? $args['font-size'] : $default_font_size;
		$gfont_size = (isset($_GET['font-size']) && !empty($_GET['font-size'])) ? $_GET['font-size'] : $font_size;
		$font_size = ($font_size) ? $font_size : $gfont_size;
		$font_size = trim($font_size);

		// controle font_size
		$pos = strpos($font_size, 'px');
		if ($pos === 2) {
			$nb = (int) substr($font_size, 0, $pos);
			if ($nb < 8 || $nb > 20) {
				$font_size = $default_font_size;
			}
			else {
				$font_size = $nb . 'px';
			}
		}

		if ( (!empty($ids)) || (!empty($slugs))) {
			// recherche des regions par slug
			if (empty($ids)) {
				$slugs_array = explode(',',$slugs);
				$regions = array();
				foreach($slugs_array as $slug) {
					$res = $this->main->model->search_region(array('region_slug' => $slug));
					if (!empty($res)) {
						$regions[] = $res;
					}
					else {
						$msg = sprintf( __('Stats - %s : Unknown region slug - Check the shortcode call !','amecycl'), $slug );
						$html =  '<p class="acy-error">' . $msg . '</p>';
						break;
					}
				}
			}
			else {
				$ids_array = explode(',',$ids);
				$regions = array();
				foreach($ids_array as $id) {
					$res = $this->main->model->search_region(array('region_id' => $id));
					if (!empty($res)) {
						$regions[] = $res;
					}
					else {
						$msg = sprintf( __('Stats - %s : Unknown region slug - Check the shortcode call !','amecycl'), $id );
						$html =  '<p class="acy-error">' . $msg . '</p>';
						break;
					}
				}
			}
			if (count($regions)) {
				// generation de la vue
				$html = '<div class="table-responsive">';
				$html .= $this->get_stats_view($regions, $setting, $width, $align, $font_size);
				$html .= '</div>';
			}
		}
		else {
			$html = '<p class="acy-error">' . __('Stats - You should provide at least one region slug. Check the shortcode call !','amecycl') . '</p>';
		}

		return $html;
	}


	/**
	 * Generate the statistics view
	 *
	 * Generation de la vue statistiques
	 *
	 * @since    1.0.0
	 */
	 private function get_stats_view( $regions, $sc_setting, $width, $align, $font_size ) {

		$html = '';
		
		// Surcharge eventuelle de la configuration
		$sid = 0;
		if ($sc_setting) {
			// Surcharge de la configuration - recherche de l'id de la configuration choisie
			$setting = $this->main->model->search_setting( array('setting_slug' => $sc_setting) );
			// surcharge de l'id de configuration
			$sid = $setting['setting_id'];
		}
		
		if (count($regions)) {
			$stats = array();
			$rids = array();
			// statistiques des regions
			foreach($regions as $region) {
				$rids[] = $region['region_id'];
				if (!$sid) {
					// on conserve la configuration de la region
					$results = $this->main->model->read_selected_stats( $region['region_id'], $region['region_setting'] );
				}
				else {
					// on utilise la configuration passée via le shortcode ou get
					$results = $this->main->model->read_selected_stats( $region['region_id'], $sid );
				}
				if (count($results)) {
					foreach($results as $result) {
						$stats[$region['region_name']][$result['stat_name']] = $result;
					}
				}
			}

			// alignement
			if ($align === 'center') {
				$margin = '0 auto'; // center							
			}
			elseif ($align === 'right') {
				$margin = '0 0 0 auto'; // right								
			}
			else {
				$margin = '0 auto 0 0'; // left				
			}

			// liste triée des differents types existants dans les regions sélectionnées
			$types = array();
			foreach($stats as $stat_region => $stat) {
				foreach($stat as $type => $values) {
					if (!in_array($type, $types)) $types[] = $type;
				}
			}
			sort($types);

			// construction du tableau
			$tab = '<table class="ame-stats table table-bordered table-hover compare table-responsive-sm" ';
			$tab .= 'style="width:' . $width . ';margin:' . $margin . ';font-size:' . $font_size . '">';

			// header
			$tab .= '<thead><tr><th style="vertical-align:middle" rowspan="2">' . __("type amenagement",'amecycl') . '</th>';
			foreach($regions as $region) {
				$tab .= '<th colspan="2">' . $region['region_name'] . '</th>';
			}
			$tab .= '</tr><tr>';
			foreach($regions as $region) {
				$tab .= '<th>' . __('longueur voie','amecycl') . '</th><th>' . __('lineaire cyclable','amecycl') . '</th>';
			}
			$tab .= '</tr></thead>';


			// body
			$tab .= '<tbody>';
			foreach($types as $type) {
				$tab .= '<tr><td>' . $type . '</td>';
				foreach($regions as $region) {
					if (isset($stats[$region['region_name']][$type]) && !empty($stats[$region['region_name']][$type])) {
						$s = $stats[$region['region_name']][$type];
						$km = round($s['stat_km']) ? round($s['stat_km']) : $s['stat_km'];
						$kmc = round($s['stat_kmc']) ? round($s['stat_kmc']) : $s['stat_kmc'];
						$tab .= '<td>' . $km . '</td><td>' . $kmc . '</td>';
					}
					else {
						$tab .= '<td></td><td></td>';
					}
				}
				$tab .= '</tr>';
			}

			// totaux - Totaux de la selection induite par la configuration
			$tab .= '<tr style="font-weight:bold;"><td style="text-align:right">' . __('total','amecycl') .'</td>';
			foreach($regions as $region) {
				$km = 0;
				$kmc = 0;
				foreach($stats[$region['region_name']] as $label) {
					$km += $label['stat_km'];
					$kmc += $label['stat_kmc'];
				}
				$tab .= '<td>' . round($km) . '</td><td>' . round($kmc) . '</td>';
			}
			$tab .= '</tr>';

			// fin du tableau
			$tab .= '</tbody'>
			$tab .= '</table>';

			$html = $tab;
		}

		return $html;
	 }



	/**
	 * Register the Ajax request to get amecycl data for a specific region
	 *
	 * Retourne une structure json comportant pour une region donnée les amenagements cyclables sous la forme de couche geojson
	 *
	 * @since    1.0.0
	 */
	public function get_ames() {

		if ( isset($_POST['rid']) && (!empty($_POST['rid'])) && isset($_POST['sid']) && (!empty($_POST['sid'])) ) {

			$rid = $_POST['rid'];	// id de la région
			$sid = $_POST['sid'];	// id de la configuration

			// lecture des types d'amenagements de la region
			// a chaque type correspond une couche geojson
			$layers = $this->main->model->read_selected_layers($rid, $sid);

			// liste des amenagements
			$ames =array();
			foreach($layers as $layer) {
				$ames[] = $layer['layer_geojson'];
			}

			// retour ajax (type json) des amenagements
			//$data = (object) array( 'ames' => $ames );
			$response = json_encode($ames);
			echo $response;
			die();
		}

		echo 0;
		die();
	}

}
