<?php
	namespace sv_tracking_manager_extended;
	
	class hubspot extends modules {
		public function __construct() {
		
		}
		
		public function init() {
			$this->set_section_title( __( 'Hubspot', 'sv_tracking_manager_extended' ) )
				->set_section_desc( __( 'Extended', 'sv_tracking_manager_extended' ) )
				->load_settings()
				->get_root()->add_section( $this );

			add_action('wp_print_scripts', array($this, 'disable_plugin_loader'));
		}
		
		protected function load_settings(): hubspot{
			$this->get_setting( 'disable_plugin_loader' )
				->set_title( __( 'Disable Plugin Loader', 'sv_tracking_manager_extended' ) )
				->set_description( __( 'Disable Loading Hubspot Pixel via official Hubspot Plugin. This prevents doubled Script loading and respects Usercentrics CMP. You should disable Hubspot Cookie Consent Feature if loading is controlled by Usercentrics CMP.', 'sv_tracking_manager_extended' ) )
				->load_type( 'checkbox' );
				
			return $this;
		}
		public function disable_plugin_loader(): hubspot{
			if(!$this->get_setting( 'disable_plugin_loader' )->get_data()){
				return $this;
			}

			wp_dequeue_script('leadin-script-loader-js');

			return $this;
		}
	}