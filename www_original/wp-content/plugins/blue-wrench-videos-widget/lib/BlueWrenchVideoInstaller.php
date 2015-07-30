<?php
if(!class_exists('BlueWrenchVideoInstaller')){
	/**
	 * Singleton implementation of BlueWrenchVideoInstaller
	 *
	 * @author Sunil Nanda
	 *
	 */
	class BlueWrenchVideoInstaller{
		private static $instance ;
		private $bwvideoAdmin ;

		private function __construct(){
			$this->bwvideoAdmin=BlueWrenchVideoAdmin::getInstance();
		}

		public static function getInstance(){
			if( !isset(self::$instance)){
				self::$instance = new BlueWrenchVideoInstaller();
			}
			return self::$instance;
		}

		/**
		 * Function installs the Blue Wrench Video plugin
		 * and initializes rewrite rules.
		 */
		public function install() {
			global $wpdb;
			$table = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";
			$structure = "CREATE TABLE $table (
				id int(9) NOT NULL AUTO_INCREMENT,
				title varchar(150) NOT NULL,
				value varchar(255) NOT NULL,
				sortorder int(3) NOT NULL DEFAULT  '9999',
				dateadded datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				post_status varchar(20) NOT NULL DEFAULT 'publish',
				UNIQUE KEY id (id)
			);";
			$wpdb->query($structure);
			add_option("Blue Wrench Video Widget Version", BlueWrenchVideoConstants::BW_VERSION_NUM);
		}

		/**
		 * Function removes Blue Wrench Video plugin related information.
		 */
		public function remove() {
			global $wpdb;
			$table = $wpdb->prefix.BlueWrenchVideoConstants::BW_TABLE_PREFIX."videos";
			$structure = "drop table if exists $table";
			$wpdb->query($structure);  
			delete_option("Blue Wrench Video Widget Version", BW_VERSION_NUM);
		}

		public function upgrade(){
			update_option("Blue Wrench Video Widget Version", BlueWrenchVideoConstants::BW_VERSION_NUM);
		}
	}
}//end if(!class_exists('BlueWrenchVideoInstaller'))
?>