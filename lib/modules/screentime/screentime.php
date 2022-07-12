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
                 ->load_js_settings()
				 ->get_root()->add_section( $this );
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

            $this->get_setting('google_analytics')
                ->set_title( __( 'Google Analytics', 'sv_tracking_manager_extended' ) )
                ->set_description('Enable Google Analytics')
                ->load_type( 'checkbox' );

            $this->get_setting('report_interval')
                 ->set_title( __( 'Report Interval in seconds, e.g. 5', 'sv_tracking_manager_extended' ) )
                 ->set_description('Report Interval in seconds, e.g. 5')
                 ->load_type( 'number' );

            $this->get_setting('percent_on_screen')
                 ->set_title( __( 'Percent on Screen, e.g. 50% or 100%', 'sv_tracking_manager_extended' ) )
                 ->set_description('Percent of screen that must be on screen to be counted as active, e.g. 10%')
                 ->load_type( 'text' );

            $this->get_setting('tracked_elements')
                ->set_title(__('Tracked Elements', 'sv_tracking_manager'))
                ->load_type('group');

            $this->get_setting('tracked_elements')->run_type()->add_child()
				->set_ID('entry_label')
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
        /**
         * Loads percentage setting, default is 50%
         *
         * @return string
         */
        public function load_percentage_on_screen(): string {
            $percent_on_screen = $this->get_setting('percent_on_screen')->get_data();
            if(!preg_match('/^[0-9]{1,3}%$/', $percent_on_screen)){
                return '50%';
            }
            return $percent_on_screen;
        }
        public function google_analytics_enabled(): bool {
            return $this->get_setting('google_analytics')->get_data() === '1';
        }
        public function get_report_interval(): int {
            return (int) $this->get_setting('report_interval')->get_data();
        }
        public function load_js_settings(): screentime {
            if($this->is_active()){
                $js_settings = array();

                // load tracked elements
                $tracked_elements = $this->get_setting('tracked_elements')->get_data();
                $tracked_elements_js = array();
    
                if($tracked_elements && is_array($tracked_elements) && count($tracked_elements) > 0){
                    $tracked_elements_js = array_merge($tracked_elements_js, $tracked_elements);
                    $tracked_elements_js = $this->clean_tracked_elements($tracked_elements_js);
                    $js_settings['tracked_elements'] = $tracked_elements_js;
                }

                // load percentage on screen
                $js_settings['percent_on_screen'] = $this->load_percentage_on_screen();

                // load google analytics enabled
                $js_settings['google_analytics_enabled'] = $this->google_analytics_enabled();

                // load report interval
                $js_settings['report_interval'] = $this->get_report_interval();

                $this->get_script( 'screentime' )
                     ->set_is_enqueued()
                     ->set_localized( $js_settings );
            }
            return $this;
        }
        public function clean_tracked_elements(array $tracked_elements_js): array {
            $tracked_elements_js = array_map(function($element){
                if( !isset($element['entry_label']) || !isset($element['selector'])
                    || empty($element['entry_label']) || empty($element['selector']) ) {
                    return null;
                }

				$element['name']	= $element['entry_label'];
				unset($element['entry_label']);

                return $element;
            }, $tracked_elements_js);
            $tracked_elements_js = array_filter($tracked_elements_js);
            return $tracked_elements_js;
        }
    }