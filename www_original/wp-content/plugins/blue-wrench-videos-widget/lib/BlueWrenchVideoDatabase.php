<?php
if (!class_exists('WP_List_Table')) {
    require_once(ABSPATH . 'wp-admin/includes/class-wp-list-table.php');
}
if(!class_exists('BlueWrenchVideoDatabase')){
	/**
	 * Singleton implementation of BlueWrenchVideoDatabase
	 *
	 * @author Sunil Nanda
	 *
	 */
	class BlueWrenchVideoDatabase extends WP_List_Table{
		private static $instance ;
		public function __construct(){
			global $status, $page;
			parent::__construct(array(
				'singular' => 'BW Video',
				'plural' => 'BW Videos',
			));
			$this->bwVideoController = new BlueWrenchVideoController();
		}
		public static function getInstance(){
			if( !isset(self::$instance)){
				self::$instance = new BlueWrenchVideoDatabase();
			}
			return self::$instance;
		}

		public function column_default($item, $column_name){
	        return $item[$column_name];
		
		}

	    public function column_url($item){
			if ($item['post_status']=="publish"){
		        $visibility = "hide";
		        $visibilityText = "Hide in Widget";
			}else if ($item['post_status']=="draft"){
		        $visibility = "show";
		        $visibilityText = "Show in Widget";
			}

			$url = sprintf('<a href="javascript: void(0);" onclick="javascript: bw_slideToggle(%s);">%s</a>', $item['id'], $item['value']);

			$video_preview = "<div style='height:".$this->bwVideoController->videoheight."px; width:".$this->bwVideoController->videowidth."px'><img style='margin-top:".intval($this->bwVideoController->videoheight/2-11)."px; margin-left:".intval($this->bwVideoController->videowidth/2-11)."px;' src='".plugins_url( '/bluewrench-video-widget/images/ajax-loader.gif' )."'></div>";


			$actions = array(
				'edit' => sprintf('<a href="?page=%s&id=%s">%s</a>', BlueWrenchVideoConstants::BW_MANAGE_VIDEOS_PAGE, $item['id'], __('Edit', 'bluewrench-video-widget')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'bluewrench-video-widget')),
				'preview_video' => sprintf('<a href="javascript: void(0);" onclick="javascript: bw_slideToggle(%s);">%s</a>', $item['id'], __('Preview', 'bluewrench-video-widget')),
				'visibility' => sprintf('<a href="?page='.$_REQUEST['page'].'&action='.$visibility.'&id='.$item['id'].'">'.__($visibilityText, 'bluewrench-video-widget').'</a>')
			);

			$value =  $url.'<div class="bw_preview_container" id="prev_'.$item['id'].'" style="display:none;">'.$video_preview.'</div>';
			return $value;

			/*return sprintf('%s %s',
				$value,
				$this->row_actions($actions)
			);*/

	        //return '<em>' . $item['value'] . '</em>';
		}

	    public function column_sortorder($item){
			if ($item['sortorder']==9999)
		        return '<em>--</em>';

	        return '<em>' . $item['sortorder'] . '</em>';
		}

	    public function column_visibility($item){
			if ($item['post_status']=="publish"){
		        return 'Shown';
			}else if ($item['post_status']=="draft"){
		        return '<b>Hidden</b>';
			}
	        return '<em>Unknown</em>';
		}

		public function column_title($item){
			// links going to /admin.php?page=[your_plugin_page][&other_params]
			// notice how we used $_REQUEST['page'], so action will be done on curren page
			// also notice how we use $this->_args['singular'] so in this example it will
			// be something like &person=2
			/*
			$actions = array(
				'edit' => sprintf('<a href="?page=%s&id=%s">%s</a>', BlueWrenchVideoConstants::BW_MANAGE_VIDEOS_PAGE, $item['id'], __('Edit', 'bluewrench-video-widget')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'bluewrench-video-widget')),
			);
			*/
			//return $item['title'];
			/*
			return sprintf('%s %s',
				$item['title'],
				$this->row_actions($actions)
			);
			*/

			if ($item['post_status']=="publish"){
		        $visibility = "hide";
		        $visibilityText = "Hide in Widget";
				$title = $item['title'];
			}else if ($item['post_status']=="draft"){
				$title = '<em>'.$item['title'].'</em>';
		        $visibility = "show";
		        $visibilityText = "Show in Widget";
			}


			$video_preview = "<div style='height:".$this->bwVideoController->videoheight."px; width:".$this->bwVideoController->videowidth."px'><img style='margin-top:".intval($this->bwVideoController->videoheight/2-11)."px; margin-left:".intval($this->bwVideoController->videowidth/2-11)."px;' src='".plugins_url( '/bluewrench-video-widget/images/ajax-loader.gif' )."'></div>";

			$actions = array(
				'edit' => sprintf('<a href="?page=%s&id=%s">%s</a>', BlueWrenchVideoConstants::BW_MANAGE_VIDEOS_PAGE, $item['id'], __('Edit', 'bluewrench-video-widget')),
				'delete' => sprintf('<a href="?page=%s&action=delete&id=%s">%s</a>', $_REQUEST['page'], $item['id'], __('Delete', 'bluewrench-video-widget')),
				'preview_video' => sprintf('<a href="javascript: void(0);" onclick="javascript: bw_slideToggle(%s);">%s</a>', $item['id'], __('Preview', 'bluewrench-video-widget')),
				'visibility' => sprintf('<a href="?page='.$_REQUEST['page'].'&action='.$visibility.'&id='.$item['id'].'">'.__($visibilityText, 'bluewrench-video-widget').'</a>'),
				//'move_up' => sprintf('<a href="?page='.$_REQUEST['page'].'&action=move_up&id='.$item['id'].'">'.__('Move Up', 'bluewrench-video-widget').'</a>'),
				//'move_down' => sprintf('<a href="?page='.$_REQUEST['page'].'&action=move_down&id='.$item['id'].'">'.__('Move Down', 'bluewrench-video-widget').'</a>'),
			);

			$value =  $item['title'].'<div class="bw_preview_container" id="prev_'.$item['id'].'" style="display:none;">'.$video_preview.'</div>';

			return sprintf('%s %s',
				$title,
				$this->row_actions($actions)
			);

		}
	    public function column_cb($item){
			return sprintf(
				'<input type="checkbox" name="id[]" value="%s" />',
				$item['id']
			);
		}
		public function get_columns(){
			$columns = array(
				'cb' => '<input type="checkbox" />', //Render a checkbox instead of text
				//'sortorder' => __('Sort Order', 'bluewrench-video-widget'),
				'title' => __('Title', 'bluewrench-video-widget'),
				'url' => __('Video (URL)', 'bluewrench-video-widget'),
				'visibility' => __('Visibility', 'bluewrench-video-widget'),
			);
			return $columns;
		}

		public function get_sortable_columns(){
			$sortable_columns = array(
				'title' => array('title', true),
				'url' => array('value', false),
				'visibility' => array('post_status', false),
			);
			return $sortable_columns;
		}
		public function get_bulk_actions(){
			$actions = array(
				'delete' => 'Delete',
				'show' => 'Show in Widget',
				'hide' => 'Hide in Widget'
			);
			return $actions;
		}
		public function process_bulk_action(){
			global $wpdb;
			$table_name = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";

			if ('delete' === $this->current_action()) {
				$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
				if (is_array($ids)) $ids = implode(',', $ids);

				if (!empty($ids)) {
					$wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
				}
			}else if ('show' === $this->current_action()) {
				$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
				if (is_array($ids)) $ids = implode(',', $ids);

				if (!empty($ids)) {
					$wpdb->query("UPDATE $table_name set post_status = 'publish' WHERE id IN($ids)");
				}
			}else if ('hide' === $this->current_action()) {
				$ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
				if (is_array($ids)) $ids = implode(',', $ids);

				if (!empty($ids)) {
					$wpdb->query("UPDATE $table_name set post_status = 'draft' WHERE id IN($ids)");
				}
			}

		}

		/**
		 * [REQUIRED] This is the most important method
		 *
		 * It will get rows from database and prepare them to be showed in table
		 */
		public function prepare_items(){
			global $wpdb;
			$table_name = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";

			$per_page = 5; // constant, how much records will be shown per page

			$columns = $this->get_columns();


			$hidden = array();
			$sortable = $this->get_sortable_columns();



			// here we configure table headers, defined in our methods
			$this->_column_headers = array($columns, $hidden, $sortable);

			// [OPTIONAL] process bulk action if any
			$this->process_bulk_action();

			// will be used in pagination settings
			$total_items = $wpdb->get_var("SELECT COUNT(id) FROM $table_name");

			// prepare query params, as usual current page, order by and order direction
			$paged = isset($_REQUEST['paged']) ? max(0, intval($_REQUEST['paged']) - 1) : 0;
			$orderby = (isset($_REQUEST['orderby']) && in_array($_REQUEST['orderby'], array_keys($this->get_sortable_columns()))) ? $_REQUEST['orderby'] : 'title';
			$order = (isset($_REQUEST['order']) && in_array($_REQUEST['order'], array('asc', 'desc'))) ? $_REQUEST['order'] : 'asc';

			// [REQUIRED] define $items array
			// notice that last argument is ARRAY_A, so we will retrieve array
			$this->items = $wpdb->get_results($wpdb->prepare("SELECT * FROM $table_name ORDER BY $orderby $order LIMIT %d OFFSET %d", $per_page, $paged), ARRAY_A);

			// [REQUIRED] configure pagination
			$this->set_pagination_args(array(
				'total_items' => $total_items, // total items defined above
				'per_page' => $per_page, // per page constant defined at top of method
				'total_pages' => ceil($total_items / $per_page) // calculate pages count
			));
		}



	}
}//end if(!class_exists('BlueWrenchVideoDatabase'))
?>