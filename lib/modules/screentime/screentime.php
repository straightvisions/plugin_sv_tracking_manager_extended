<?php
	namespace sv_tracking_manager_extended;
	
	class screentime extends modules {
        public function init() {
			// Section Info
			$this->set_section_title( __('Screentime', 'sv_tracking_manager_extended' ) )
				 ->set_section_desc( __('Setup screentime tracking', 'sv_tracking_manager_extended' ))
				 ->set_section_type( 'settings' )
                 ->register_scripts()
				 ->load_settings()
                 ->load_tracked_elements()
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

            $this->get_setting('tracked_elements')
                ->set_title(__('Tracked Elements', 'sv_tracking_manager'))
                ->load_type('group');

            $this->get_setting('tracked_elements')->run_type()->add_child()
				->set_ID('label')
                ->set_description(__('This label will be used as Text Label in Google Analytics', 'sv_tracking_manager'))
				->load_type('text')
				->set_placeholder('Label ...');

            $this->get_setting('tracked_elements')->run_type()->add_child()
				->set_ID('selector')
                ->set_description(__('This is the css selector of the element. For example h2#landing-page', 'sv_tracking_manager'))
				->load_type('text')
				->set_placeholder('CSS Selector ...');

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
        public function load_tracked_elements(): screentime {
            if($this->is_active()){
                $tracked_elements = $this->get_setting('tracked_elements')->get_data();
                $tracked_elements_js = array();
    
                if($tracked_elements && is_array($tracked_elements) && count($tracked_elements) > 0){
                    $tracked_elements_js = array_merge($tracked_elements_js, $tracked_elements);
                    
                        $this->get_script( 'screentime' )
                            ->set_is_enqueued()
                            ->set_localized( $tracked_elements_js );
                    // foreach($tracked_elements as $element){
                    //     $tracked_elements_js[] = $element;
                    // }
                }
            }
            return $this;
        }
    }