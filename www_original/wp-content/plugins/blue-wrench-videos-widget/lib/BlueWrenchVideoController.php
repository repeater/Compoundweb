<?php
if(!class_exists('BlueWrenchVideoController')){
	/**
	 * Singleton implementation of BlueWrenchVideoController
	 *
	 * @author Sunil Nanda
	 *
	 */
	class BlueWrenchVideoController{
		private static $instance ;
		private $bwvideoAdmin ;
		public $dev_mode;
		private $supported_networks = array(
			'youtu.be',
			'www.youtube.com',
			'player.vimeo.com',
			'vimeo.com',
			'dai.ly',
			'www.dailymotion.com',
			'blip.tv',
			'www.mefeedia.com',
			'www.metacafe.com',
			'www.veoh.com',
			'www.break.com',
			'screen.yahoo.com',
			'news.yahoo.com',
			'www.liveleak.com',
			'revision3.com',
			'www.videojug.com',
			'www.collegehumor.com',
			'www.funnyordie.com',
			'www.ted.com',
			'www.ustream.tv',
			'www.ustream.com'
		);
		function __construct($videowidth=300, $videoheight=198){
			//$this->bwvideoAdmin=BlueWrenchVideoAdmin::getInstance();
			$this->dev_mode = BlueWrenchVideoConstants::DEBUG;
			$this->videowidth = $videowidth;
			$this->videoheight = $videoheight;
			$this->videoid = 0;
		}
		public static function getInstance(){
			if( !isset(self::$instance)){
				self::$instance = new BlueWrenchVideoController();
			}
			return self::$instance;
		}
		public function get_supported_networks(){
			return $this->supported_networks;
		}
		public function is_supported_network($url=''){
			if ($this->dev_mode) return true;
			$url_chunks = parse_url($url);
			if (is_array($url_chunks) && array_key_exists("host", $url_chunks)){
				$host = strtolower($url_chunks['host']);
				if (in_array($host, $this->get_supported_networks())) return true;
			}
			return false;
		}
		public function wp_get_http_headers( $url, $deprecated = false ) {
			if ( !empty( $deprecated ) )
				_deprecated_argument( __FUNCTION__, '2.7' );

			$response = wp_safe_remote_head( $url );

			if ( is_wp_error( $response ) )
				return false;

			return wp_remote_retrieve_headers( $response );
		}
		public function fetch_vimeo_id($url) {
			$headers = $this->wp_get_http_headers($url, array('timeout' => 10, 'sslverify' => false) );
			if (is_array($headers) && count($headers) > 0){
				if (array_key_exists("location",$headers)){
					$location = $headers["location"];
					return str_replace("/","",$location);
				}
			}
			# Could not find id
			return null;
		}
		public function fetch_revision3_video_info($url='') {
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://revision3.com/api/oembed/?url=".urlencode($url)."&format=xml";
				$result = @file_get_contents($url);
				if ($result){
					$matches = array();
					preg_match('#\<html\>(.+?)\<\/html\>#s', $result, $matches);
					if (is_array($matches) && count($matches)>0){
						$result = html_entity_decode(array_pop($matches));

						$pattern = '#(\s+)(width)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{width}',$result);

						$pattern = '#(\s+)(height)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{height}',$result);

						$pattern = '#(\&?)(width)(=[0-9]*)+#s';
						$result = preg_replace($pattern, '{width1}',$result);

						$pattern = '#(\&?)(height)(=[0-9]*)+#s';
						$result = preg_replace($pattern, '{height1}',$result);

						$result = str_replace("{width}",' width="'.$this->videowidth.'"',$result);
						$result = str_replace("{height}",' height="'.$this->videoheight.'"',$result);

						$result = str_replace("{width1}",'&width='.$this->videowidth.'',$result);
						$result = str_replace("{height1}",'&height='.$this->videoheight.'',$result);

						return $result;
					}
				}
			}
			return false;
		}
		public function fetch_videojug_video_info($url=''){
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://www.videojug.com/oembed.xml?url=".urlencode($url)."&format=xml";
				$result = @file_get_contents($url);
				if ($result){
					$matches = array();
					preg_match('#\<id\>(.+?)\<\/id\>#s', $result, $matches);
					if (is_array($matches) && count($matches)>0){
						$result = html_entity_decode(array_pop($matches));
						return $result;
					}
				}
			}
			return false;
		}
		public function fetch_collegehumor_video_info($url=''){
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://www.collegehumor.com/oembed.xml?url=".urlencode($url)."&format=xml";
				$result = @file_get_contents($url);
				if ($result){
					$matches = array();
					preg_match('#\<html\>(.+?)\<\/html\>#s', $result, $matches);
					if (is_array($matches) && count($matches)>0){
						//$result = html_entity_decode(array_pop($matches));
						$result = html_entity_decode(array_pop($matches));
						$matches = array();
						preg_match('#\<object(.*?)\>(.+?)\<\/object\>#s', $result, $matches);
						if (is_array($matches) && count($matches)>0){
							$result = html_entity_decode(array_shift($matches));

							$pattern = '#(\s+)(width)(=(\"?)[0-9]*(\"?))+#s';
							$result = preg_replace($pattern, '{width}',$result);

							$pattern = '#(\s+)(height)(=(\"?)[0-9]*(\"?))+#s';
							$result = preg_replace($pattern, '{height}',$result);

							$result = str_replace("{width}",' width="'.$this->videowidth.'"',$result);
							$result = str_replace("{height}",' height="'.$this->videoheight.'"',$result);
		
							return $result;
						}
						return $result;
					}
				}
			}
			return false;
		}
		public function fetch_funnyordie_video_info($url=''){
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://www.funnyordie.com/oembed.xml?url=".urlencode($url);
				$result = @file_get_contents($url);
				if ($result){
					$matches = array();
					preg_match('#\<html\>(.+?)\<\/html\>#s', $result, $matches);
					if (is_array($matches) && count($matches)>0){
						$result = html_entity_decode(array_pop($matches));
						$matches = array();
						preg_match('#\<iframe(.*?)\>(.+?)\<\/iframe\>#s', $result, $matches);
						if (is_array($matches) && count($matches)>0){
							$result = html_entity_decode(array_shift($matches));
							preg_match('#\<iframe.+?src=[\'|\"?](.*?)[(\'|\"?)](.*?)\>.+?\<\/iframe\>#s', $result, $matches);
							if (is_array($matches) && count($matches)>1){
								$result = html_entity_decode($matches[1]);
								return $result;
							}
						}
					}
				}
			}
			return false;
		}
		public function fetch_ted_video_info($url=''){
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://www.ted.com/talks/oembed.xml?url=".urlencode($url);
				$result = @file_get_contents($url);
				if ($result){
					$matches = array();
					preg_match('#\<html\>(.+?)\<\/html\>#s', $result, $matches);
					if (is_array($matches) && count($matches)>0){
						$result = html_entity_decode(array_pop($matches));
						$matches = array();

						$pattern = '#(\s+)(width)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{width}',$result);

						$pattern = '#(\s+)(height)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{height}',$result);

						$result = str_replace("{width}",' width="'.$this->videowidth.'"',$result);
						$result = str_replace("{height}",' height="'.$this->videoheight.'"',$result);
						return $result;
					}
				}
			}
			return false;
		}
		public function fetch_ustream_video_info($url=''){
			/*Uses oEmbed*/
			if(filter_var($url, FILTER_VALIDATE_URL)){
				$url = "http://www.ustream.tv/oembed?url=".urlencode($url);
				$result = @file_get_contents($url);
				if ($result){
					$result = str_replace("{","",$result);
					$result = str_replace("}","",$result);
					$result = explode(",",$result);

					$matches = array();
					if (is_array($result) && count($result)>0){
						foreach ($result as $res){
							$res = stripslashes($res);
							$pattern = '/^\"([^\"]+)[\"?][:?][\"?](.*)[$\"]/';
							preg_match($pattern, $res, $mm);
							if (is_array($mm) && count($mm)==3){
								$matches[$mm[1]] = $mm[2];
							}
						}
					}
					if (array_key_exists("html", $matches)){
						$result = $matches['html'];
						$pattern = '#(\s+)(width)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{width}',$result);
						$pattern = '#(\s+)(height)(=(\"?)[0-9]*(\"?))+#s';
						$result = preg_replace($pattern, '{height}',$result);
						$result = str_replace("{width}",' width="'.$this->videowidth.'"',$result);
						$result = str_replace("{height}",' height="'.$this->videoheight.'"',$result);
						return $result;
					}else{
						return false;
					}
				}
			}
			return false;
		}
		public function generateEmbeddHTML($video_url='', $display_errors=true){
			$network = 'unknown';
			if ($video_url!="" && function_exists("parse_url")){
				if ($video_url != esc_url($video_url)){
					return '<div class="error">Invalid video url</div>';
				}
				$url_chunks = parse_url($video_url);
				if (is_array($url_chunks) && array_key_exists("host", $url_chunks)){
					$host = strtolower($url_chunks['host']);
					//p($host);
					if (!$this->is_supported_network($video_url)){
						if ($display_errors){
							return '<div class="error">This video network is not yet supported. Please contact the author of this plugin <a href="http://www.sunilnanda.com/contact/" target="_blank">here</a> for more help.</div>';
						}else{
							return '';
						}
					}
					switch($host){
						case 'youtu.be':
						case 'www.youtube.com':
							$video_url = $url_chunks['scheme'].'://www.youtube.com/embed'.$url_chunks['path'];
							$network = 'youtube';
							break;
						case 'player.vimeo.com':
						case 'vimeo.com':
							if ($url_chunks['host']=="player.vimeo.com"){
								$video_url = $url_chunks['scheme'].'://player.vimeo.com'.$url_chunks['path'];
							}else if ($url_chunks['host']=="vimeo.com"){
								$video_url = $url_chunks['scheme']."://".$url_chunks['host'].$url_chunks['path'];
								$video_id = $this->fetch_vimeo_id($video_url);
								if ($video_id){
									$video_url = $url_chunks['scheme'].'://player.vimeo.com/video/'.$video_id;
								}else{
									$location = $url_chunks['path'];
									$video_id = str_replace("/","",$location);
									if ($video_id){
										$video_url = $url_chunks['scheme'].'://player.vimeo.com/video/'.$video_id;
									}
								}
							}else{
								$video_url = $url_chunks['scheme'].'://player.vimeo.com/video'.$url_chunks['path'];
							}
							$network = 'vimeo';
							break;
						case 'dai.ly':
						case 'www.dailymotion.com':
							if ($url_chunks['host']=="www.dailymotion.com"){
								$video_url = $url_chunks['scheme'].'://www.dailymotion.com/embed'.$url_chunks['path'];
							}else{
								$video_url = $url_chunks['scheme'].'://www.dailymotion.com/embed/video'.$url_chunks['path'];
							}
							$network = 'dailymotion';
							break;
						case 'blip.tv':
							$path = $url_chunks['path'];
							$pos = strpos($path, "/play/");
							if ($pos === false){
								/*Retrieve video information from RSS */
								$v_arr = preg_split("/[-]+/",$path);
								if (is_array($v_arr) && count($v_arr)>0){
									$v_id = array_pop($v_arr);
									$feed_url = "http://blip.tv/rss/view/".$v_id;
									$content = @file_get_contents($feed_url);
									$p = xml_parser_create();
									xml_parse_into_struct($p, $content, $vals, $index);
									xml_parser_free($p);
									if (is_array($index) && array_key_exists("BLIP:EMBEDURL",$index)){
										if (is_array($index["BLIP:EMBEDURL"])){
											$url_index = array_pop($index["BLIP:EMBEDURL"]);
										}
										if (array_key_exists($url_index,$vals)){
											if (is_array($vals[$url_index]) && array_key_exists("value", $vals[$url_index])) {
												$video_url = $vals[$url_index]["value"];
											}
										}
									}
								}
							}
							$network = 'blip.tv';
							break;
						case 'www.mefeedia.com':
							$network = 'www.mefeedia.com';
							break;
						case 'www.metacafe.com':
							$network = 'www.metacafe.com';
							$path = $url_chunks['path'];
							$pattern = '/[0-9]+/';
							preg_match($pattern, $path, $matches);
							if (is_array($matches) && count($matches)==1){
								$video_id = array_pop($matches);
								$video_url = "http://www.metacafe.com/embed/".$video_id;
							}
							$network = 'metacafe';
							break;
						case 'www.veoh.com':
							$path = $url_chunks['path'];
							$v_arr = preg_split("/[\/]+/",$path);
							if (is_array($v_arr) && count($v_arr)>0){
								$v_id = array_pop($v_arr);
								$video_url = $v_id;
							}
							$network = 'veoh';
							break;
						case 'www.break.com':
							$path = $url_chunks['path'];
							$pos = strpos($path, "/video/");
							if ($pos !== false){
								$v_arr = preg_split("/[\/]+/",$path);
								if (is_array($v_arr) && count($v_arr)>0){
									$v_id = array_pop($v_arr);
									if ($v_id!=""){
										$v_arr = preg_split("/[\-]+/",$path);
										if (is_array($v_arr) && count($v_arr)>0){
											$video_id = array_pop($v_arr);
											$video_url = "http://www.break.com/embed/".$video_id;
										}
									}
								}
							}
							$network = 'break';
							break;
						case 'screen.yahoo.com':
						case 'news.yahoo.com':
							$path = $url_chunks['path'];
							$v_arr = preg_split("/[\/]+/",$path);
							if (is_array($v_arr) && count($v_arr)>0){
								$v_arr[0] = "embed";
								$path = "/".implode($v_arr,"/");
							}
							$video_url = "http://screen.yahoo.com".$path;
							$network = 'yahoo';
							break;
						case 'www.liveleak.com':
							$path = $url_chunks['path'];
							$video_url = $url_chunks['scheme']."://".$url_chunks['host']."/ll_embed?".$url_chunks['query'];
							$network = 'liveleak.com';
							break;
						case 'revision3.com':
							$path = $url_chunks['path'];
							$video_url = html_entity_decode($this->fetch_revision3_video_info($video_url));
							$network = 'revision3.com';
							break;
						case 'www.videojug.com':
							$video_id = html_entity_decode($this->fetch_videojug_video_info($video_url));
							if ($video_id){
								$video_url = $url_chunks['scheme']."://".$url_chunks['host']."/embed/".$video_id;
							}
							$network = 'videojug';
							break;
						case 'www.collegehumor.com':
							$video_url = html_entity_decode($this->fetch_collegehumor_video_info($video_url));
							$network = 'collegehumor';
							break;
						case 'www.funnyordie.com':
							$video_url = $this->fetch_funnyordie_video_info($video_url);
							$network = 'funnyordie';
							break;
						case 'www.ted.com':
							$video_url = $this->fetch_ted_video_info($video_url);
							$network = 'ted';
							break;
						case 'www.ustream.tv':
						case 'www.ustream.com':
							$video_url = $this->fetch_ustream_video_info($video_url);
							$network = 'ustream';
							break;
						default:
							echo "<pre>";
							print_r($url_chunks);
							echo $video_url;
							echo "</pre>";
							break;
					}
				}
			}else{
				die("parse_url error");
			}
			$videowidth = (($this->videowidth>0) ? $this->videowidth : 248);
			$videoheight = (($this->videoheight>0) ? $this->videoheight : 150);
			switch ($network){
				case 'vimeo':
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
					break;
				case 'metacafe':
				case 'break':
				case 'liveleak.com':
				case 'videojug':
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0" webkitAllowFullScreen allowFullScreen></iframe>';
					break;
				case 'blip.tv':
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0" allowfullscreen></iframe><embed type="application/x-shockwave-flash" src="'.$video_url.'" style="display:none"></embed>';
					break;
				case 'www.mefeedia.com':
					$video_preview = '<iframe src="'.$video_url.'?iframe=1&w='.$videowidth.'&h='.$videoheight.'&autoplay=0" width="'.($videowidth+16).'" height="'.($videoheight+16).'" frameborder="0" scrolling="no" webkitAllowFullScreen allowFullScreen></iframe>';
					break;
				case 'veoh':
					$video_preview = '<object width="'.$videowidth.'" height="'.($videoheight+52).'" id="veohFlashPlayer" name="veohFlashPlayer"><param name="movie" value="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1419&permalinkId='.$video_url.'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.veoh.com/swf/webplayer/WebPlayer.swf?version=AFrontend.5.7.0.1419&permalinkId='.$video_url.'&player=videodetailsembedded&videoAutoPlay=0&id=anonymous" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="'.$videowidth.'" height="'.($videoheight+52).'" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed"></embed></object>';
					break;
				case 'yahoo':
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0" scrolling="no"></iframe>';
					break;
				case 'revision3.com':
				case 'collegehumor':
				case 'ted':
				case 'ustream':
					$video_preview = $video_url;
					break;
				case 'funnyordie':
					$videowidth += 20;
					$videoheight += 20;
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen scrolling="no"></iframe>';
					break;
				default:
					$video_preview = '<iframe src="'.$video_url.'" width="'.$videowidth.'" height="'.$videoheight.'" frameborder="0"></iframe>';
					break;
			}
			return $video_preview;
		}


	}
}//end if(!class_exists('BlueWrenchVideoController'))
?>