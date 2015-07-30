<?php
if( !class_exists('BlueWrenchVideoAdmin')) {
	class BlueWrenchVideoAdmin {
		private static $instance ;

		private function __construct(){
			//$this->bwDB = BlueWrenchVideoDatabase::getInstance() ;
		}

		public static function getInstance(){
			if( !isset(self::$instance)){
				self::$instance = new BlueWrenchVideoAdmin();
			}
			return self::$instance;
		}
		
		public function createAdminMenu(){
			add_menu_page(
				'BW Videos',
				'BW Videos',
				'manage_options',
				BlueWrenchVideoConstants::BW_VIDEO_LISTING_PAGE,
				array( $this, 'adminController' ),
				plugins_url( '/bluewrench-video-widget/images/video_icon.png' ),25
			);
			add_submenu_page('bw_videos', 'BlueWrench Videos', 'BlueWrench Videos', 'manage_options', BlueWrenchVideoConstants::BW_VIDEO_LISTING_PAGE, array( $this, 'adminController'));
			add_submenu_page('bw_videos','Add new video', 'Add new video', 'manage_options', BlueWrenchVideoConstants::BW_MANAGE_VIDEOS_PAGE, array( $this, 'adminManageVideos'));
		}

		public function addAdminScripts(){
			$nonce = wp_create_nonce( 'bw-nonce' );
			wp_register_script( 'bw_ajax_script', plugins_url( '/bluewrench-video-widget/bw_script.js'), array('jquery'));
			wp_localize_script( 'bw_ajax_script', 'bwAjax', array( 'ajaxurl' => admin_url( 'admin-ajax.php'), 'sID' => $nonce));
			wp_enqueue_script( 'bw_ajax_script' );

			wp_register_style ( 'bw-plugin-jquery-ui', plugins_url( '/bluewrench-video-widget/jquery-ui.css' ));
			wp_enqueue_style( 'bw-plugin-jquery-ui' );

			$page = ( isset($_GET['page'] ) ) ? esc_attr( $_GET['page'] ) : false;
			if( 'bw_videos' == $page ){
				wp_register_style ( 'bw-plugin_style', plugins_url( '/bluewrench-video-widget/style.css' ));
				wp_enqueue_style( 'bw-plugin_style' );
			}
		}

		public function adminController(){
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
		    global $wpdb;
			$this->bwDB = BlueWrenchVideoDatabase::getInstance() ;
		    $table = $this->bwDB;
		    $table->prepare_items();
		    $message = '';
			if ('delete' === $table->current_action()) {
		        $message = '<div class="updated below-h2" id="message"><p>' . sprintf(__('Items deleted: %d', 'bluewrench-video-widget'), count($_REQUEST['id'])) . '</p></div>';
		    }?>
			<div class="wrap">
			    <div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
			    <h2><?php _e('Blue Wrench Video Widget', 'bluewrench-video-widget')?> <a class="add-new-h2" href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page='.BlueWrenchVideoConstants::BW_MANAGE_VIDEOS_PAGE);?>"><?php _e('Add New Video', 'bluewrench-video-widget')?></a></h2>
				<?php echo $message; ?>
				<form id="persons-table" method="GET">
					<input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>"/>
					<?php $table->display() ?>
				</form>
			</div>
			<?php
		}

		public function adminManageVideos(){
			if (!current_user_can('manage_options'))  {
				wp_die( __('You do not have sufficient permissions to access this page.') );
			}
			global $wpdb;
			$table_name = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";

			$message = '';
			$notice = '';

			// this is default $item which will be used for new records
			$default = array(
				'id' => 0,
				'title' => '',
				'value' => '',
				'sortorder' => 9999,
				'dateadded' => current_time( 'mysql' ),
				'post_status' => 'publish',
			);

//			if ( (isset($_POST['action']) && trim($_POST['action']) == "save") && (isset($_POST['submit']) && ( trim($_POST['submit']) == "Add Video" || trim($_POST['submit']) == "Modify Video") )) {

				// here we are verifying does this request is post back and have correct nonce
				if (isset($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], basename(__FILE__))) {
					// combine our default item with request params
					$item = shortcode_atts($default, $_REQUEST);
					// validate data, and if all ok save item to database
					// if id is zero insert otherwise update
					$item_valid = $this->validateVideoEntry($item);
					if ($item_valid === true) {
						if ($item['id'] == 0) {
							$sql = "SELECT * FROM ".$table_name." where  value='".$item['value']."' limit 1";
							$results = $wpdb->get_results($sql);
							if(count($results) > 0){
								$notice = __('Video URL already found in the database', 'bluewrench-video-widget');
							}else{
								$result = $wpdb->insert($table_name, $item);
								$item['id'] = $wpdb->insert_id;
								if ($result) {
									$message = __('Item was successfully saved', 'bluewrench-video-widget');
								} else {
									$notice = __('There was an error while saving item', 'bluewrench-video-widget');
								}
							}
						} else {
							$sql = "SELECT * FROM ".$table_name." where id != '".$item['id']."' AND value='".$item['value']."' limit 1";
							$results = $wpdb->get_results($sql);
							if(count($results) > 0){
								$notice = __('Video URL already found in the database', 'bluewrench-video-widget');
							}else{
								$result = $wpdb->update($table_name, $item, array('id' => $item['id']));
								if ($result) {
									$message = __('Item was successfully updated', 'bluewrench-video-widget');
								} else {
									$notice = __('There was an error while updating item', 'bluewrench-video-widget');
								}
							}
						}
					} else {
						// if $item_valid not true it contains error message(s)
						$notice = $item_valid;
					}
				}
				else {
					// if this is not post back we load item to edit or give new one to create
					$item = $default;
					if (isset($_REQUEST['id'])) {
						$item = $wpdb->get_row($wpdb->prepare("SELECT * FROM $table_name WHERE id = %d", $_REQUEST['id']), ARRAY_A);
						if (!$item) {
							$item = $default;
							$notice = __('Item not found', 'bluewrench-video-widget');
						}
					}
				}
//			}
			// here we adding our custom meta box
			add_meta_box('videos_meta_box', 'Video Detail', array( $this, 'adminManageVideosHtml'), 'bw_add_modify_videos', 'normal', 'default');

			?>
			<div class="wrap">
				<div class="icon32 icon32-posts-post" id="icon-edit"><br></div>
				<h2><?php _e('Blue Wrench Video Widget', 'bluewrench-video-widget'); ?> <a class="add-new-h2"
											href="<?php echo get_admin_url(get_current_blog_id(), 'admin.php?page='.BlueWrenchVideoConstants::BW_VIDEO_LISTING_PAGE);?>"><?php _e('back to list', 'bluewrench-video-widget')?></a>
				</h2>

				<?php if (!empty($notice)): ?>
				<div id="notice" class="error"><p><?php echo $notice ?></p></div>
				<?php endif;?>
				<?php if (!empty($message)): ?>
				<div id="message" class="updated"><p><?php echo $message ?></p></div>
				<?php endif;?>

				<form id="form" method="POST">
					<input type="hidden" name="nonce" value="<?php echo wp_create_nonce(basename(__FILE__))?>"/>
					<?php /* NOTICE: here we storing id to determine will be item added or updated */ ?>
					<input type="hidden" name="id" value="<?php echo $item['id'] ?>"/>

					<div class="metabox-holder" id="poststuff">
						<div id="post-body">
							<div id="post-body-content">
								<?php /* And here we call our custom meta box */ ?>
								<?php do_meta_boxes('bw_add_modify_videos', 'normal', $item); ?>
								<input type="submit" value="<?php _e('Save', 'bluewrench-video-widget')?>" id="submit" class="button-primary" name="submit">
							</div>
						</div>
					</div>
				</form>
			</div>
			<?php


		}


		/**
		 * This function renders our custom meta box
		 * $item is row
		 *
		 * @param $item
		 */
		public function adminManageVideosHtml($item){
			?>
		<table cellspacing="2" cellpadding="5" style="width: 100%;" class="form-table">
			<tbody>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="bw_title"><?php _e('Video Title', 'bluewrench-video-widget')?></label>
				</th>
				<td>
					<input id="bw_title" name="title" maxlength="75" type="text" style="width: 95%" value="<?php echo esc_attr($item['title'])?>" size="70" class="code" placeholder="<?php _e('Video Title is optional', 'bluewrench-video-widget')?>">
				</td>
			</tr>
			<tr class="form-field">
				<th valign="top" scope="row">
					<label for="bw_url"><?php _e('Video URL', 'bluewrench-video-widget')?></label>
				</th>
				<td>
					<input id="bw_url" name="value" maxlength="255" size="70" type="text" style="width: 95%" value="<?php echo esc_url($item['value'])?>" size="70" class="code" placeholder="<?php _e('Please provide Video URL', 'bluewrench-video-widget')?>" required>
					<p>Hint: Simply copy the video's URL from your web browser's address bar while viewing the video</p>

					<a id="bw_infobar_link" href="javascript: void(0);" onclick="javascript: bw_infobarToggle();">More Help</a>

					<div id="bw_infobar" style="display:none;">
					<p>Examples:<br />http://www.youtube.com/embed/XXXXXXXXXXX<br />http://player.vimeo.com/video/XXXXXXXX<br />http://www.dailymotion.com/video/XXXXXXX</p>

					<p><strong>Note for Yahoo! Video: </strong>Please use Embedd button on video player to get the video link. See screenshot below:</p>
					<img src='<?php echo plugins_url( '/bluewrench-video-widget/images/yahoo.png'); ?>'>
					
					</div>

				</td>
			</tr>
			</tbody>
		</table>
		<?php
		}

		private function validateVideoEntry($item){
			$messages = array();
			if (empty($item['value'])) $messages[] = __('URL is required', 'bluewrench-video-widget');
			if (empty($messages)) return true;
			return implode('<br />', $messages);
		}

	}
}//end if class_exists('BlueWrenchVideoAdmin')
?>