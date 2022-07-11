<?php
	namespace sv_tracking_manager_extended;
	
	class screentime extends modules {
		public function __construct() {
		
		}
        public function init() {
			// Section Info
			$this->set_section_title( __('Screentime', 'sv_tracking_manager_extended' ) )
				 ->set_section_desc( __('Setup screentime tracking', 'sv_tracking_manager_extended' ))
				 ->set_section_type( 'settings' )
				 ->load_settings()
				 ->get_root()->add_section( $this );

			add_action('init', array($this, 'load'));
		}
        public function register_scripts(): screentime {
            $this->get_script('screentime')
                ->set_path('lib/frontend/js/screentime.js')
                ->set_type('js');

			return $this;
		}
        public function load_settings(): screentime {
			$this->get_setting('activate')
				 ->set_title( __( 'Activate', 'sv_tracking_manager_extended' ) )
				 ->set_description('Enable ScreenTime')
				 ->load_type( 'checkbox' );
            return $this;
        }
        public function is_active(): bool {
            // activate not set
			if(!$this->get_setting('activate')->get_data()){
				return false;
			}
            // activate not true
			if($this->get_setting('activate')->get_data() !== '1'){
				return false;
			}
            return true;
        }

        public function load(): screentime {
			if($this->is_active()){
				$this->register_scripts()->add_service();

				$this->get_script('screentime')->set_is_enqueued();
			}
			
			return $this;
		}
    }