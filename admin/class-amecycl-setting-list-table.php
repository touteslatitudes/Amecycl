<?php/** * Classe de gestion de l'affichage des configurations (Admin) * * @link       https://randovelo.touteslatitudes.fr * @since      1.0.0 * * @package    Amecycl * @subpackage Amecycl/admin */

 // chargement de la classe List_Tableif( ! class_exists( 'Amecycl_List_Table' ) ) {	require_once(sprintf("%s/class-amecycl-list-table.php", dirname(__FILE__)));}

if (!class_exists("Amecycl_Setting_List_Table")) {

	class Amecycl_Setting_List_Table extends Amecycl_List_Table {

		//Store plugin main class to allow public access.
		public $main;

		var $items = array();

		public function __construct( $plugin_main ) {

			global $status, $page;
			$this->main = $plugin_main;
 			parent::__construct( array(
				'singular'  => __( 'setting', 'amecycl' ),   //singular name of the listed records
				'plural'    => __( 'settings', 'amecycl' ),  //plural name of the listed records
				'ajax'      => false        				//does this table support ajax?
			) );
		}

		public function no_items() {
			_e( 'No settings found','amecycl' );
		}

		public function column_default( $item, $column_name ) {
			switch( $column_name ) {
				case 'setting_id':
				case 'setting_name':
				case 'setting_slug':				case 'setting_date':
				case 'setting_desc':
				return $item[ $column_name ];
				default:
					return print_r( $item, true ) ; //Show the whole array for troubleshooting purposes			}
		}

		public function get_sortable_columns() {
			$sortable_columns = array(
				'setting_id'  => array('setting_id',false),
				'setting_name' => array('setting_name',true),
				'setting_slug' => array('setting_slug',false),
				'setting_date' => array('setting_date',true),				'setting_desc' => array('setting_desc',false),
			);
			return $sortable_columns;
		}

		public function get_columns(){
			$columns = array(
				'cb'        		=> '<input type="checkbox" />',
				'setting_id'  		=> __( 'Id', 'amecycl' ),
				'setting_name'    	=> __( 'Name', 'amecycl' ),
				'setting_date'   	=> __( 'Date', 'amecycl' ),
				'setting_slug'		=> __( 'Slug', 'amecycl' ),
				'setting_desc'		=> __( 'Description', 'amecycl' )
			);
			return $columns;
		}

		public function column_setting_name($item){
			$srch = (isset($_REQUEST['s']) && (! empty($_REQUEST['s']))) ? "&s=" . $_REQUEST['s'] : "";
			$paged = (isset($_REQUEST['paged'])) ? "&paged=" . $_REQUEST['paged'] : "";
			$actions['edit'] = sprintf('<a href="?page=%s&action=%s&setting=%s">'.__('Edit','amecycl').'</a>',$_REQUEST['page'],'edit',$item['setting_id']);
			$actions['delete'] = sprintf('<a href="?page=%s&action=%s&setting=%s%s%s">'.__('Delete','amecycl').'</a>',$_REQUEST['page'],
				'delete',$item['setting_id'], $srch, $paged);

			return sprintf('%1$s %2$s', $item['setting_name'], $this->row_actions($actions) );
		}

		public function get_bulk_actions() {
			$actions = array(				'delete'  => __('Delete','amecycl')			);			return $actions;		}

		public function column_cb($item) {
			return sprintf(				'<input type="checkbox" name="setting[]" value="%s" />', $item['setting_id']			);		}

		public function prepare_items() {
			$orderby = ( ! empty( $_GET['orderby'] ) ) ? $_GET['orderby'] : 'setting_id';
			$order = ( ! empty($_GET['order'] ) ) ? $_GET['order'] : 'asc';
			// recherche
			$like = array();
			if ( ! empty($_REQUEST['s'] ) ) {
				$like = array(
					'setting_name' => $_REQUEST['s']
				);
			}
			// construction de la requete
			$select = $this->main->model->search_like_setting_except_default( $like, 0, $orderby, $order );
			// recherche
			$data = $this->main->model->exec( $select );
			/* -- Pagination parameters -- */
			$total_items = count($data); //return the total number of affected rows
			//How many to display per page?
			$ACY_PER_PAGE = ACY_PER_PAGE;
			//Which page is this?
			$paged = !empty($_GET["paged"]) ? sanitize_text_field($_GET["paged"]) : '';
			//Page Number
			if(empty($paged) || !is_numeric($paged) || $paged<=0 ){ $paged=1; }
			//How many pages do we have in total?
			$total_pages = ceil($total_items/$ACY_PER_PAGE);
			$paged = ($paged <= $total_pages) ? $paged : 1;
			//adjust the query to take pagination into account
			if(!empty($paged) && !empty($ACY_PER_PAGE)){
				$offset=($paged-1) * $ACY_PER_PAGE;
				$select.=' LIMIT '.(int)$offset.','.(int)$ACY_PER_PAGE;
			}

			// Register the pagination
			$this->set_pagination_args( array(
				"total_items" => $total_items,
				"total_pages" => $total_pages,
				"ACY_PER_PAGE" => $ACY_PER_PAGE,
			) );
			// The pagination links are automatically built according to those parameters

			// Register the Columns
			$columns = $this->get_columns();
			$hidden = array();
			$sortable = $this->get_sortable_columns();
			$this->_column_headers = array($columns, $hidden, $sortable);

			// recherche paginee
			$this->items = $this->main->model->exec( $select );
		}
	}
}