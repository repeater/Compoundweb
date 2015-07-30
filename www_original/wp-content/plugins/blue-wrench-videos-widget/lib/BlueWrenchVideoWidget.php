<?php
/*
*  Blue Wrench Video Widget
*/ 
if( !class_exists('BlueWrenchVideoWidget')) {
	class BlueWrenchVideoWidget extends WP_Widget {
		private static $instance ;
		public function __construct(){
			$widget_ops = array( 'classname' => 'bw_video_widget', 'description' => __('Blue Wrench Video Widget to display videos from various video sharing networks', 'bw_video_widget') );
			$control_ops = array( 'width' => 200, 'height' => 350, 'id_base' => 'bw_video_widget' );
			$this->WP_Widget( 'bw_video_widget', __('Blue Wrench Video Widget', 'bw_video_widget'), $widget_ops, $control_ops );
			add_action('wp_enqueue_scripts', array(&$this, 'js'));
		}
		public static function getInstance(){
			if( !isset(self::$instance)){
				self::$instance = new BlueWrenchVideoAdmin();
			}
			return self::$instance;
		}
		function js(){
			if ( is_active_widget(false, false, $this->id_base, true) ) {
				wp_register_script( 'bw_js', plugins_url( '/bluewrench-video-widget/bw_js.js'), array('jquery'));
				wp_enqueue_script( 'bw_js' );
			}           
		}
		function widget( $args, $instance ) {		//function to echo out widget on sidebar
			extract( $args );
			$showcaption	= $instance['bwv_showcaption'];
			echo $before_widget;
			$videowidth	= $instance['bwv_width'];
			$videoheight	= $instance['bwv_height'];
			$layout		= $instance['bwv_layout'];
			global $wpdb;
			$table_name = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";
			$sql = "SELECT * FROM ".$table_name." where post_status = 'publish' order by sortorder desc";
			$results = $wpdb->get_results($sql);
			if(count($results) > 0){
				$rowCount		= 0;
				$bwVideoController = new BlueWrenchVideoController($videowidth, $videoheight);
				foreach($results as $video){
					//print_r($video);
					$videoID		= $video->id;
					$videoCaption	= $video->title;
					$videoURL		= $video->value;
					//p($videoURL);
					$video_html		= $bwVideoController->generateEmbeddHTML($videoURL, false);
					echo "<div class=\"bwv_video_container bwv_{$layout}_layout\">";
					echo $video_html;
					if($showcaption){
						echo "<div class=\"bwv_videocaption\"><p>$videoCaption</p></div>";
					}
					echo "</div>";
				}
			}
			echo $after_widget;
		}//end of function widget

		//function to update widget setting
		function update( $new_instance, $old_instance ) {
			$instance = $old_instance;
			$instance['bwv_width'] = strip_tags( $new_instance['bwv_width'] );
			$instance['bwv_height'] = strip_tags( $new_instance['bwv_height'] );
			$instance['bwv_showcaption'] = $new_instance['bwv_showcaption'];
			$instance['bwv_layout'] = $new_instance['bwv_layout'];
			return $instance;
		}//end of function update

		//function to create Widget Admin form
		function form($instance) {
			$instance = wp_parse_args( (array) $instance, array( 'bwv_width' => '285', 'bwv_height' => '200', 
			'bwv_showcaption' => 'Yes', 'bwv_layout' => 'grid') );
			$instance['bwv_width'] = strip_tags( $instance['bwv_width'] );
			$instance['bwv_height'] = strip_tags( $instance['bwv_height'] );
			$br_nonce = wp_create_nonce( 'br_nonce' );
		?>
			<p>
				<label for="<?php echo $this->get_field_id('bwv_width'); ?>">Video Width: </label>
				<input class="widefat" id="<?php echo $this->get_field_id('bwv_width'); ?>" 
				name="<?php echo $this->get_field_name('bwv_width'); ?>" type="text" value="<?php echo $instance['bwv_width']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('bwv_height'); ?>">Video Height: </label>
				<input class="widefat" id="<?php echo $this->get_field_id('bwv_height'); ?>" 
				name="<?php echo $this->get_field_name('bwv_height'); ?>" type="text" value="<?php echo $instance['bwv_height']; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('bwv_showcaption'); ?>">Display Video Caption: </label>
				<select id="<?php echo $this->get_field_id( 'bwv_showcaption' );?>" 
				name="<?php echo $this->get_field_name( 'bwv_showcaption' );?>" class="widefat" style="width:100%;">';
					<option value='1' <?php  if($instance['bwv_showcaption'] == '1'){echo 'selected="selected"';}?>>Yes</option>
					<option value='0' <?php  if($instance['bwv_showcaption'] == '0'){echo 'selected="selected"';}?>>No</option>
				</select>
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('bwv_layout'); ?>">Layout: </label>
				<table width="100%" id="bw_admin_layout_settings">
					<tr>
						<td align="left">
							<input type="radio" name="<?php echo $this->get_field_name( 'bwv_layout' );?>" value="list" <?php  if($instance['bwv_layout'] == 'list'){echo 'checked="checked"';}?>> <img src= "<?php echo plugins_url('/bluewrench-video-widget/images/list_view.png');?>">List View
						</td>
					</tr>
					<tr>
						<td align="left">
							<input type="radio" name="<?php echo $this->get_field_name( 'bwv_layout' );?>" value="grid" <?php  if($instance['bwv_layout'] == 'grid'){echo 'checked="checked"';}?>> <img src= "<?php echo plugins_url('/bluewrench-video-widget/images/grid_view.png');?>">Grid View
						</td>
					</tr>
				</table>
			</p>
			<?php
		}//end of function form($instance)
	}//end of Blue Wrench Video Widget
}//end if class_exists('BlueWrenchVideoWidget')
?>