<?php
	namespace sv_plugin_boilerplate;
	
	class common extends modules {
		public function __construct() {
		
		}
		
		public function init() {
			$this->set_section_title( __( 'Common', 'sv_plugin_boilerplate' ) )
				->set_section_type( 'settings' )
				->load_settings()
				->get_root()->add_section( $this );

			add_shortcode( $this->get_name(), array( $this, 'shortcode' ) );

		}
		
		protected function load_settings(): common{
			$this->get_setting( 'my_setting' )
				->set_title( __( 'My Setting', 'sv100' ) )
				->set_description( __( 'Some text', 'sv100' ) )
				->load_type( 'text' );
				
			return $this;
		}
		
		public function shortcode( $settings = array() ): string {
			$output = '';
			
			$settings								= shortcode_atts(
				array(
					'inline'						=> true,
				),
				$settings,
				$this->get_module_name()
			);
			
			ob_start();
			require( $this->get_path( 'lib/frontend/tpl/common.php' ) );
			$output = ob_get_clean();
			
			return $output;
			
		}


	}