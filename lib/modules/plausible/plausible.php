<?php
	namespace sv_tracking_manager_extended;
	
	class plausible extends modules {
		public function __construct() {
		
		}
		
		public function init() {
			$this->set_section_title( __( 'Plausible', 'sv_tracking_manager_extended' ) )
				->set_section_desc( __( 'Extended', 'sv_tracking_manager_extended' ) )
				->load_settings()
				->get_root()->add_section( $this );

			add_action('init', array($this, 'local_cache'), 999);
		}
		
		protected function load_settings(): plausible{
			$this->get_setting( 'local_cache' )
				->set_title( __( 'Activate Local Proxy', 'sv_tracking_manager_extended' ) )
				->set_description( __( 'Communication with Plausible is tunneled by server proxy. External Files will be cached and updated every 24 hours where possible.', 'sv_tracking_manager_extended' ) )
				->load_type( 'checkbox' );
				
			return $this;
		}

		public function local_cache(): plausible {
			if(!$this->get_setting( 'local_cache' )->get_data()){
				return $this;
			}

			if(!$this->is_instance_active('sv_tracking_manager')){
				return $this;
			}

			$script = $this->get_instance('sv_tracking_manager')->get_module('plausible')->get_script('default');

			$script->set_path($this->cache_file($script->get_url()));

			// catch API Events
			$this->api_events();

			return $this;
		}
		private function api_events() {
			if($_SERVER['REQUEST_URI'] === '/api/event'){
				$body		= file_get_contents('php://input');
				if(json_decode($body)){
					http_response_code ( 202 );

					$remote_get		= static::$remote_get
						->create( $this )
						->set_request_url( 'https://plausible.io/api/event' )
						->set_args(array(
							'headers'		=> array(
								'Content-Type' => 'application/json; charset=utf-8',
								'User-Agent'	=> $_SERVER['HTTP_USER_AGENT']
							),
							'data_format'	=> 'body',
							'method'		=> 'POST',
							'body'			=> $body
						));

					echo wp_remote_retrieve_body($remote_get->get_response(true));
					die();
				}
			}
		}
		public function cache_file(string $url): string{
			$hash = md5($url);

			if(strlen(get_transient( $this->get_prefix($hash))) === 0) {
				$remote_get		= static::$remote_get->create( $this )
					->set_request_url( $url );

				$file_content	= $remote_get->get_response_body();
				$file_version	= '?ver='.md5($file_content);
				$file_name		= explode('.',basename($url));

				if($file_name){
					$file_ext = '.'.end($file_name);
				}else{
					$file_ext = '';
				}

				$new_file_name	= $hash.$file_ext;

				file_put_contents($this->get_path_cached($new_file_name), $file_content);

				set_transient($this->get_prefix($hash), $this->get_url_cached($new_file_name), 24 * HOUR_IN_SECONDS);
			}

			add_filter( 'rocket_exclude_defer_js', function($excluded_files = array()) use($hash) : array{
				$excluded_files[] = get_transient( $this->get_prefix($hash));

				return $excluded_files;
			} );

			return get_transient( $this->get_prefix($hash) );
		}
		public function get_path_cached(string $file): string{
			return static::$scripts->create( $this )->get_path_cached($file);
		}
		public function get_url_cached(string $file): string{
			return static::$scripts->create( $this )->get_url_cached($file);
		}
	}