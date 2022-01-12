<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://randovelo.touteslatitudes.fr
 * @since      1.0.0
 *
 * @package    Amecycl
 * @subpackage Amecycl/admin
 */

define("ACY_PER_PAGE", 25 );

 // chargement de la classe Amecycl_Region_List_Table
if( ! class_exists( 'Amecycl_Region_List_Table' ) ) {
	require_once(sprintf("%s/class-amecycl-region-list-table.php", dirname(__FILE__)));
}
 // chargement de la classe Amecycl_Setting_List_Table
if( ! class_exists( 'Amecycl_Setting_List_Table' ) ) {
	require_once(sprintf("%s/class-amecycl-setting-list-table.php", dirname(__FILE__)));
}

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Amecycl
 * @subpackage Amecycl/admin
 * @author     toutesLatitudes <contact@randovelo.touteslatitudes.fr>
 */
class Amecycl_Admin {

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
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $plugin_main ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->main = $plugin_main;

	}

	/**
	 * Register the stylesheets for the admin area.
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

		wp_enqueue_style( 'jquery-ui-smoothness', plugin_dir_url( __FILE__ ) . 'jquery-ui/themes/smoothness/jquery-ui.min.css', array(), $this->version, 'all' );
				
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/amecycl-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
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
		 
		wp_enqueue_script('jquery-ui-dialog');
		
		wp_enqueue_script('jquery-ui-tabs');

		wp_enqueue_script('jquery-ui-tooltip');

		wp_enqueue_media();

		wp_register_script('amecycl-admin', plugin_dir_url( __FILE__ ) . 'js/amecycl-admin.min.js','','',true);

		wp_localize_script( 'amecycl-admin', 'acyvar', array('url' => site_url('/')));

		
		wp_enqueue_script('amecycl-admin');

	}

	/**
	 * Ajout des menus d'administration
	 */
	public function add_menu(){
		
		add_menu_page(
			__('Cycle routes','amecycl'),
			__('Cycle routes','amecycl'),
			ACY_ADMIN,
			'region-admin',
			array($this, 'region_admin_page'),
			'dashicons-chart-line'
		);

		add_submenu_page(
			'region-admin',
			__('Manage Regions','amecycl'),
			__('Manage Regions','amecycl'),
			ACY_ADMIN,
			'region-admin',
			array($this, 'region_admin_page')
		);

		add_submenu_page(
			'region-admin',
			__('Manage Settings','amecycl'),
			__('Manage Settings','amecycl'),
			ACY_ADMIN,
			'setting-admin',
			array($this, 'setting_admin_page')
		);

	}

// ====================================================== region admin page

	/**
	 * run region_admin_page
	 *
	 * @since    1.0.0
	 */
	public function region_admin_page() {

		$action = ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		$action = ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) ? $_REQUEST['action'] : $action;

		$rid = isset( $_REQUEST['region'] ) ? $_REQUEST['region'] : 0;

		switch($action) {
			// ajout d'une region
			case 'add':
				$html = $this->display_region(0);
				echo $html;
				break;

			// modification d'une region existante
			case 'edit':
				if ($rid){
					$html = $this->display_region($rid);
					echo $html;
				}
				break;


			// suppression d'une region existante
			case 'delete':
				if ($rid) {
					$region = $this->main->model->read_region($rid);
					echo '
						<div id="dialog-confirm" title="' . __('Delete Region Confirm','amecycl') . '">
						<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
						</span>' . __('Delete region','amecycl') .' ' . $region['region_name'] .' ?</p>
						</div>';
				}
				// reaffichage de la liste
				$this->display_list_regions();
				break;


			// Affichage de la liste des regions deja crees
			default:
				$this->display_list_regions();
		}
	}


	// Affichage de la liste des regions
	public function display_list_regions() {

		$region_list_table = new Amecycl_Region_List_Table( $this->main );

		echo '<style type="text/css">';
		echo '.wp-list-table .column-region_id { width: 5%; }';
		echo '.wp-list-table .column-region_name { width: 15%}';
		echo '.wp-list-table .column-region_year { width: 10%}';
		echo '.wp-list-table .column-region_datadate { width: 10%}';
		echo '.wp-list-table .column-region_slug { width: 15%; }';
		echo '.wp-list-table .column-region_setting { width: 15%;}';
		echo '.wp-list-table .column-region_filename { width: 15%;}';
		echo '.wp-list-table .column-region_nb { width: 5%;}';
		echo '.wp-list-table .column-region_km { width: 5%;}';
		echo '.wp-list-table .column-region_kmc { width: 5%;}';
		echo '</style>';

		$region_list_table->prepare_items();

		// lien Ajouter
		$addlnk = sprintf(' <a class="add-new-h2" href="?page=%s&action=%s">'.__('Add','amecycl').'</a>',$_REQUEST['page'],'add');

		echo '<div class="acy-table-list">
		<h2>' . __('Regions','amecycl'). '&nbsp;&nbsp;' . $addlnk . '</h2>
		<form method="post">
		<input type="hidden" name="page" value="region-admin">';

		$region_list_table->search_box( __('Search regions','amecycl'), 'acy' );
		$region_list_table->display();

		echo '</form></div>';
	}


	// Creation / modification d'une region
	public function display_region($rid=0) {


		$rid = (!$rid && isset($_POST['acy-id'])) ? $_POST['acy-id'] : $rid;

		// lecture / initialisation de la region
		$region = $this->main->model->readInit_region($rid, 'region');

		$msg = "";

		// liste des configurations existantes
		$settings = $this->main->model->read_settings();

		// liste des fichiers du repertoire uploads
		$uplfilenames = $this->main->model->read_upload_filenames();

		if (isset($_POST['acy-id'])) {

			// ============================== creation / modification region
			if (isset($_POST['update'])) {
				
				// Soumission du formulaire - Prise en compte des modifications

				$region['region_id'] = $rid;
				$region['region_name'] = isset($_POST['acy-name']) ? stripslashes($_POST['acy-name']) : $region['region_name'];
				$region['region_year'] = isset($_POST['acy-year']) ? stripslashes($_POST['acy-year']) : $region['region_year'];
				$region['region_datadate'] = isset($_POST['acy-datadate']) ? stripslashes($_POST['acy-datadate']) : $region['region_datadate'];

				$region['region_slug'] = isset($_POST['acy-slug']) ? sanitize_title(stripslashes($_POST['acy-slug'])) : $region['region_slug'];
				$region['region_slug'] = (empty($region['region_slug'])) ? sanitize_title($region['region_name'] . '-' . $region['region_year']) : $region['region_slug'];
				$region['region_slug'] = $this->main->model->get_unique_region_slug($region['region_slug']);

				$region['region_filename'] = isset($_POST['acy-filename']) ? stripslashes($_POST['acy-filename']) : $region['region_filename'];

				$region['region_setting'] = isset($_POST['acy-setting']) ? stripslashes($_POST['acy-setting']) : $region['region_setting'];

				$region['region_desc'] = isset($_POST['acy-desc']) ? stripslashes($_POST['acy-desc']) : $region['region_desc'];

				$region['region_modified'] = date( ACY_DATE_FORMAT );

				// creation ou mise a jour de la region
				$msgerr = '';
				if (empty($region['region_slug'])) {
					$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
							<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
							</span>' . __('Thanks to provide a region name','amecycl') . '</p></div>';
				}
				else {
					if ($rid) {
						// Update de la region existante (id et slug conserves)
						$nbu = $this->main->model->update_region( $rid, $region, $msgerr );
						if ($nbu) {
							// message ok
							$msg = '<div id="dialog-msg" title="' . __('Success','amecycl') . '"><p>
									<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Ok region updated','amecycl') . '</p></div>';
						}
						else {
							$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
									<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Region not updated','amecycl') . $msgerr . '</p></div>';
						}

					}
					else {
						// recherche d'une eventuelle region (non supprimee) de meme slug
						$reg = $this->main->model->search_region( array( 'region_slug' => $region['region_slug'] ) );
						if ($reg != null) {
								// existence d'une region portant le meme slug
								$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
								<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
								</span>' . __('This region already exists!','amecycl') . '</p></div>';
						}
						else {
							// insertion de la region en base
							$rid = $this->main->model->insert_region($region, $msgerr);
							if ($rid) {
								// message ok
								$msg = '<div id="dialog-msg" title="' . __('Success','amecycl') . '"><p>
									<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Ok region created','amecycl') . '</p></div>';
							}
							else {
								// erreur d'insertion
								$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
									<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Insertion error! ','amecycl')  . ' ' .$msgerr . '</p></div>';
							}
						}
					}
				}
			}
		}


		// titre si la region est deja creee
		$title = (!empty($region['region_name'])) ? '<h2 style="color:rgb(30,140,190);float:right;">' .
		$region['region_name'] . ' - ' . $region['region_year'] . '</h2>' : '';

		// bouton créer ou mettre à jour
		$button_label = (!empty($region['region_name'])) ?  __('Update','amecycl') :  __('Create','amecycl');

		$editor_settings =   array(
			'wpautop' => true,
			'media_buttons' => false, // show insert/upload button(s)
			'textarea_rows' => 3,
			'tinymce' => array(
					'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
						'bullist,blockquote,|,justifyleft,justifycenter' .
						',justifyright,justifyfull,|,link,unlink,|' .
						',spellchecker,wp_fullscreen,wp_adv'
			)
		);

		// editeur Desc
		ob_start();
		wp_editor($region['region_desc'],'acy-desc',$editor_settings);
		$desc = ob_get_contents();
		ob_end_clean();


		// ================================================== Affichage du formulaire

		$html = $msg . '
		<div class="acy-table">
		<form name="admin" method="post" action="' . $_SERVER["REQUEST_URI"] . '">' . $title . '
		<h2>' . __('Manage Regions','amecycl') . '</h2>' . wp_nonce_field('update-options') . '
		<p class="submit"><input type="submit" name="update" class="button button-primary" value="' . $button_label . '"  /></p>
		<div id="tabs">
		<table class="form-table" width="100%" cellpadding="10">
		<tbody>
		<tr valign="top">
		<th scope="row"><label for="acy-id">' . __('Region id','amecycl') . '</label></th>
		<td><input type="text" title="' . __('Help region id','amecycl') . '" size="4" name="acy-id" value="' . $rid . '" readonly /></td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-name">' . __('Region name','amecycl') . '</label></th>
		<td><input type="text" title="' . __('Help region name','amecycl') . '" id="acy-name" size="64" name="acy-name" value="' . $region['region_name'] . '" /> ' . '</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-year">' . __('Region year','amecycl') . '</label></th>
		<td><input type="text" title="' . __('Help region year','amecycl') . '" id="acy-year" size="12" name="acy-year" value="' . $region['region_year'] . '" /> ' . '</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-slug">' . __('Region slug','amecycl') . '</label></th>
		<td><input type="text" title="' . __('Help region slug','amecycl') . '" id="acy-slug" size="20" name="acy-slug" value="' . $region['region_slug'] . '" /> ' . '</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-filename">' . __('Region filename','amecycl') . '</label></th>
		<td>
			<select id="acy-filename" title="' . __('Help region filename','amecycl') . '" name="acy-filename">';
			foreach($uplfilenames as $uplfilename) {
				$selected = ($uplfilename == $region['region_filename']) ? ' selected="selected"' : '';
				$html .= '<option value="' . $uplfilename . '"' . $selected . '>' . $uplfilename . '</option>';
			}
			$html .= '</select>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-datadate">' . __('Region datadate','amecycl') . '</label></th>
		<td><input type="text" title="' . __('Help region datadate','amecycl') . '" id="acy-datadate" size="16" name="acy-datadate" value="' . $region['region_datadate'] . '" /> ' . '</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-setting">' . __('Setting','amecycl') . '</label></th>
		<td>
			<select id="acy-setting" title="' . __('Help setting','amecycl') . '" name="acy-setting">';
			foreach($settings as $setting) {
				$selected = ($setting['setting_id'] == $region['region_setting']) ? ' selected="selected"' : '';
				$html .= '<option value="' . $setting['setting_id'] . '"' . $selected . '>' . $setting['setting_name'] . '</option>';
			}
			$html .= '</select>
		</td>
		</tr>

		<tr valign="top">
		<th scope="row">
		<label for="acy-desc">' . __('Region desc','amecycl') . '</label></th>
		<td>' . $desc . '<textarea id="acy-desc" style="display:none"></textarea></td>
		</tr>

		</tbody>
		</table>
		</div>
		</form>
		<p>&nbsp;</p>

		<h3>Téléchargement des fichiers régions au format .geojson sur le serveur</h3>
		<form id="form-upload" method="post">
		<table class="form-table" width="100%" cellpadding="10">
		<tbody>
		<tr valign="top">
		<td>
		<input type="hidden" id="edited_region" name="edited_region" value="' . $region['region_filename'] . '"> 
		<input type="file" id="fileinput" accept=".geojson" style="display:none"/>
		<input type="button" name="selectfile" class="selectfile button button-secondary" value="Choisir" />&nbsp;
		<input type="text" required id="selected_filename" size="48"/>&nbsp;
		<input type="upload" name="upload" class="submit button button-primary" value="Envoyer">
		</td>
		<tr>
		</tbody>
		</table>
		</form>
		<p>En cas d\'erreur de téléchargement, augmenter le paramètre upload_max_filesize dans php.ini. Ou bien utilisez ftp pour transférer le fichier dans le répertoire uploads.</p>

		</div>';

		return $html;
	}


// ====================================================== setting admin page


	/**
	 * run setting_admin_page
	 *
	 * @since    1.0.0
	 */
	public function setting_admin_page() {
		
		$action = ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) ? $_REQUEST['action'] : '';
		$action = ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) ? $_REQUEST['action'] : $action;

		$sid = isset( $_REQUEST['setting'] ) ? $_REQUEST['setting'] : 0;

		switch($action) {
			// ajout d'une configuration
			case 'add':
				$html = $this->display_setting(0);
				echo $html;
				break;

			// modification d'une configuration existante
			case 'edit':
				if ($sid){
					$html = $this->display_setting($sid);
					echo $html;
				}
				break;


			// suppression d'une configuration existante
			case 'delete':
				if ($sid) {
					$setting = $this->main->model->delete_setting($sid);
					echo '
						<div id="dialog-confirm" title="' . __('Delete Setting Confirm','amecycl') . '">
						<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
						</span>' . __('Delete setting','amecycl') .' ' . $setting['setting_name'] .' ?</p>
						</div>';
				}
				// reaffichage de la liste
				$this->display_list_settings();
				break;


			// Affichage de la liste des configurations deja creees
			default:
				$this->display_list_settings();
		}
	}
	
	
	// Affichage de la liste des configurations
	public function display_list_settings() {

		$setting_list_table = new Amecycl_Setting_List_Table( $this->main );

		echo '<style type="text/css">';
		echo '.wp-list-table .column-setting_id { width: 5%; }';
		echo '.wp-list-table .column-setting_name { width: 15%}';
		echo '.wp-list-table .column-setting_slug { width: 15%; }';
		echo '.wp-list-table .column-setting_date { width: 10%}';
		echo '.wp-list-table .column-setting_desc { width: 55%;}';
		echo '</style>';

		$setting_list_table->prepare_items();

		// lien Ajouter
		$addlnk = sprintf(' <a class="add-new-h2" href="?page=%s&action=%s">'.__('Add','amecycl').'</a>',$_REQUEST['page'],'add');

		echo '<div class="acy-table-list">
		<h2>' . __('Settings','amecycl'). '&nbsp;&nbsp;' . $addlnk . '</h2>
		<form method="post">
		<input type="hidden" name="page" value="setting-admin">';

		$setting_list_table->search_box( __('Search settings','amecycl'), 'acy' );
		$setting_list_table->display();

		echo '</form></div>';
	}


	// Creation / modification d'une configuration
	public function display_setting($sid=0) {
		
		$msg = "";

		$sid = (!$sid && isset($_POST['acy-sid'])) ? $_POST['acy-sid'] : $sid;

		// lecture / initialisation de la configuration
		$setting = $this->main->model->readInit_setting($sid);

		$lasttab = isset($_POST['acy-lasttab']) ? $_POST['acy-lasttab'] : 0;


		if (isset($_POST['acy-sid'])) {

			// ============================== creation / modification configuration
			if (isset($_POST['update'])) {
				
				// Soumission du formulaire - Prise en compte des modifications

				$setting['setting_id'] = $sid;
				$setting['setting_name'] = isset($_POST['acy-sname']) ? stripslashes($_POST['acy-sname']) : $setting['setting_name'];
				$setting['setting_date'] = isset($_POST['acy-sdate']) ? stripslashes($_POST['acy-sdate']) : $setting['setting_date'];

				$setting['setting_slug'] = isset($_POST['acy-sslug']) ? sanitize_title(stripslashes($_POST['acy-sslug'])) : $setting['setting_slug'];
				$setting['setting_slug'] = (empty($setting['setting_slug'])) ? sanitize_title($setting['setting_name']) : $setting['setting_slug'];

				$setting['setting_desc'] = isset($_POST['acy-sdesc']) ? stripslashes($_POST['acy-sdesc']) : $setting['setting_desc'];

				$setting['setting_modified'] = date( ACY_DATE_FORMAT );
								
				// creation ou mise a jour de la configuration
				if (empty($setting['setting_slug'])) {
					$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
							<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
							</span>' . __('Thanks to provide a setting name','amecycl') . '</p></div>';
				}
				// configuration defaut exclue
				if (($setting['setting_slug'] === 'defaut')) {
					$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
							<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
							</span>' . __('Defaut as configuration name not allowed','amecycl') . '</p></div>';
				}				
				else {
					if ($sid) {
						// Update de la configuration existante (id et slug conserves)
						// et mise à jour des labels ($_POST['items'])
						$sid = $this->main->model->update_setting_labels( $sid, $setting, $_POST['items'] );

						if ($sid) {
							// message ok
							$msg = '<div id="dialog-msg" title="' . __('Success','amecycl') . '"><p>
								<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
								</span>' . __('Ok setting updated','amecycl') . '</p></div>';
						}
						else {
							// message ko
							$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
								<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
								</span>' . __('Setting not updated','amecycl') . '</p></div>';
						}
					}
					else {
						// recherche d'une eventuelle configuration (non supprimee) de meme slug
						$set = $this->main->model->search_setting( array( 'setting_slug' => $setting['setting_slug'] ) );
						if ($set != null) {
								// existence d'une configuration portant le meme slug
								$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
								<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
								</span>' . __('This setting already exists!','amecycl') . '</p></div>';
						}
						else {
							// insertion d'une configuration avec ces labels
							$sid = $this->main->model->update_setting_labels( $sid, $setting, $_POST['items'] );
							if ($sid) {
								// message ok
								$msg = '<div id="dialog-msg" title="' . __('Success','amecycl') . '"><p>
									<span class="ui-icon ui-icon-info" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Ok setting created','amecycl') . '</p></div>';
							}
							else {
								// erreur d'insertion
								$msg = '<div id="dialog-msg" title="' . __('Error','amecycl') . '"><p>
									<span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;">
									</span>' . __('Insertion error!','amecycl') . '</p></div>';								
							}
						}
					}
				}
			}
		}


		// titre si la configuration est deja creee
		$title = (!empty($setting['setting_name'])) ? '<h2 style="color:rgb(30,140,190);float:right;">' . $setting['setting_name'] . '</h2>' : '';

		// bouton créer ou mettre à jour
		$button_label = (!empty($setting['setting_name'])) ?  __('Update','amecycl') :  __('Create','amecycl');

		$editor_settings =   array(
			'wpautop' => true,
			'media_buttons' => true, // show insert/upload button(s)
			'textarea_rows' => 10,
			'tinymce' => array(
				'theme_advanced_buttons1' => 'formatselect,|,bold,italic,underline,|,' .
					'bullist,blockquote,|,justifyleft,justifycenter' .
					',justifyright,justifyfull,|,link,unlink,|' .
					',spellchecker,wp_fullscreen,wp_adv'
			)
		);

		// editeur Desc
		ob_start();
		wp_editor($setting['setting_desc'],'acy-sdesc',$editor_settings);
		$desc = ob_get_contents();
		ob_end_clean();


		// formulaire de saisie des labels et des couleurs		
		$html2 = '<table id="ame-table"><tbody>';
		$lbtemplate  = '<tr><td><input type="checkbox" name="items[%d][cb]" %s ></td>';
		$lbtemplate .= '<td><input type="text" size="60" name="items[%d][label]" value="%s"></td>';
		$lbtemplate .= '<td><input type="color" name="items[%d][color]" value="%s"></td></tr>';
		
		// lecture de la configuration par défaut
		$defaultAmeColors = $this->main->model->read_setting_labels(0);

		if ($sid) {
			// la configuration existe déjà - on la récupère depuis la base de données
			$ameColors = $this->main->model->read_setting_labels($sid);
		}
		else {
			// la configuration n'existe pas encore - on part de la configuration par défaut
			$ameColors = $defaultAmeColors;
		}
		
		$labels = array();
		foreach($ameColors as $label) {
			$slugs[] = $label['label_slug'];
			$labels[ $label['label_slug'] ]['cb'] = 1;
			$labels[ $label['label_slug'] ]['name'] = $label['label_name'];
			$labels[ $label['label_slug'] ]['slug'] = $label['label_slug'];
			$labels[ $label['label_slug'] ]['color'] = $label['label_color'];
		}

		foreach($defaultAmeColors as $label) {
			if ( array_search($label['label_slug'], $slugs) === false ) {
				$labels[ $label['label_slug'] ]['cb'] = 0;
				$labels[ $label['label_slug'] ]['name'] = $label['label_name'];
				$labels[ $label['label_slug'] ]['slug'] = $label['label_slug'];
				$labels[ $label['label_slug'] ]['color'] = $label['label_color'];
			}
		}

		// tri du tableau par ordre alphabétique
		ksort($labels);
		
		$nbi = 0;
		foreach($labels as $label) {
			$checked = ($label['cb']) ? 'checked' : '';
			$html2  .= sprintf($lbtemplate,$nbi,$checked,$nbi,$label['name'],$nbi,$label['color']);
			$nbi++;
		}
		$html2 .= '</tbody></table>';

		// boutons contextuels
		if ($setting['setting_slug'] == 'defaut') {
			// on ne doit pas pouvoir modifier la configuration par defaut
			$buttons = '';
		}
		else {
			// bouton créer ou mettre à jour
			$button_label = (empty($setting['setting_name']) ) ?  __('Create','amecycl') :  __('Update','amecycl');
		    $buttons = '<p class="submit"><input type="submit" name="update" class="button button-primary" value="' . $button_label . '"  /></p>';
		}


		// ================================================== Affichage du formulaire

		$html = $msg . '
		<div class="acy-table">
		<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">' . $title . '
		<h2>' . __('Manage Settings','amecycl') . '</h2>' . wp_nonce_field('update-options') . '
		<input type="hidden" id="acy-lasttab" name="acy-lasttab" value="' . $lasttab . '"/>
		<input type="hidden" name="acy-sslug" value="' . $setting['setting_slug'] . '">' . $buttons . '
		<div id="tabs">
			<ul>
				<li><a href="#tab-1"><span>' . __('Identification tab','amecycl') . '</span></a></li>
				<li><a href="#tab-2"><span>' . __('Labels tab','amecycl') . '</span></a></li>
			</ul>
			
			<div id="tab-1">
				<table class="form-table" width="100%" cellpadding="10">
				<tbody>
				<tr valign="top">
				<th scope="row"><label for="acy-sid">' . __('Setting id','amecycl') . '</label></th>
				<td><input type="text" title="' . __('Help setting id','amecycl') . '" size="4" id="acy-sid"  name="acy-sid" value="' . $sid . '" readonly /></td>
				</tr>

				<tr valign="top">
				<th scope="row"><label for="acy-sname">' . __('Setting name','amecycl') . '</label></th>
					<td><input type="text" size="40" title="' . __('Help setting name','amecycl') . '" id="acy-sname" name="acy-sname" value="' . $setting['setting_name'] . '" /></td>
				</tr>

				<tr valign="top">
				<th scope="row"><label for="acy-slug">' . __('Setting slug','amecycl') . '</label></th>
					<td><input type="text" size="40" title="' . __('Help setting slug','amecycl') . '" id="acy-sslug" name="acy-sslug" value="' . $setting['setting_slug'] . '" readonly /></td>
				</tr>

				<tr valign="top">
				<th scope="row"><label for="acy-date">' . __('Setting date','amecycl') . '</label></th>
					<td><input type="text" size="40" title="' . __('Help setting date','amecycl') . '" id="acy-sdate" name="acy-sdate" value="' . $setting['setting_date'] . '"/></td>
				</tr>

				<tr valign="top">
				<th scope="row">
				<label title="' . __('Help desc','amecycl') . '" for="acy-sdesc">' . __('Setting description','amecycl') . '</label></th>
				<td colspan="2">' . $desc . '<textarea id="acy-sdesc" style="display:none"></textarea></td>
				</tr>
				</tbody>
				</table>
			</div>
			
			
			<div id="tab-2">
				<table class="form-table" width="100%" cellpadding="10">
				<tbody>
				<tr valign="top">' . $html2 . '
				</tr>
				</tbody>
				</table>
			</div>
		</div>' . $buttons . '
		</form>
		</div>';

		return $html;
	}
	
	/**
	 * Register the Ajax request to delete a specific region
	 *
	 * Supprime une region
	 *
	 * @since    1.0.0
	 */
	public function delete_region() {
		
		if ( isset($_POST['rid']) && (!empty($_POST['rid'])) ) {

			$rid = $_POST['rid'];	// id de la région

			// suppression de la region et des données (stats & layers) associées
			$this->main->model->delete_region($rid);

			echo json_encode( array( "status" => 1 ) );
			die();
		}

		echo 0;
		die();
	}


	/**
	 * Register the Ajax request to delete a specific setting
	 *
	 * Supprime une configuration
	 *
	 * @since    1.0.0
	 */
	public function delete_setting() {
		
		if ( isset($_POST['sid']) && (!empty($_POST['sid'])) ) {

			$sid = $_POST['sid'];	// id de la configuration

			// suppression de la configuration et des labels associés
			$this->main->model->delete_setting($sid);

			echo json_encode( array( "status" => 1 ) );
			die();
		}

		echo 0;
		die();
	}


	/**
	 * Register the Ajax request to upload region files
	 *
	 * Traitement de l'upload d'un fichier region
	 *
	 * @since    1.0.0
	 */
	public function upload_region_file() {


		if (isset($_POST['edited_region'])) {
			$upload_dir = ACY_HOME_DIR . ACY_GEOJSON_UPLOAD_DIR . '/';

			$move = move_uploaded_file($_FILES['file']['tmp_name'], $upload_dir . $_FILES['file']['name']);

			// liste des fichiers du repertoire uploads
			$uplfilenames = $this->main->model->read_upload_filenames();
			
			$html = '';
			$edited_region = $_POST['edited_region'];
			foreach($uplfilenames as $uplfilename) {
				$selected = ($uplfilename == $edited_region) ? ' selected="selected"' : '';
				$html .= '<option value="' . $uplfilename . '"' . $selected . '>' . $uplfilename . '</option>';
			}

			echo json_encode(array( "status" => 1, "options" => $html )) ;
			die();
		}
		else {
			echo array( "status" => 0 ) ;
			die();
		}
	}

}