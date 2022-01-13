<?php
	namespace sv_tracking_manager_extended;
	
	class modules extends init {
		public function __construct() {
		
		}
		
		public function init() {
			$this->load_module('usercentrics');
			$this->load_module('plausible');
			$this->load_module('freemius');
			$this->load_module('google_optimize');
			$this->load_module('hubspot');
			$this->load_module('product_recommendation_quiz');
		}

		public function add_service(): modules{
			if($this->is_active()){
				// filter name: sv_tracking_manager_extended_active_services
				add_filter($this->get_root()->get_prefix('active_services'), function(array $services){
					return array_merge($services,array($this->get_module_name() => $this->get_section_title()));
				});

				add_action('wp_head', array($this, 'consent_management'), 1);
				add_action('wp_footer', array($this, 'consent_management'), 1);
			}

			return $this;
		}
		public function consent_management(): modules{
			$activated = apply_filters('sv_tracking_manager_consent_management', false);

			// @todo: currently no effect, since uc scripts are directly loaded
			// filter name: sv_tracking_manager_extended_no_consent_required
			$no_consent_required	= apply_filters($this->get_root()->get_prefix('no_consent_required'), array(
				'usercentrics',
				'usercentrics_block',
				'usercentrics_block_ui'
			));

			if($activated){
				foreach($this->get_scripts() as $script){
					if(!in_array($script->get_handle(),$no_consent_required)) {
						$script
							->set_consent_required()
							// filter name: sv_tracking_manager_data_attributes
							->set_custom_attributes(apply_filters('sv_tracking_manager_data_attributes', $script->get_custom_attributes(), $script));
					}
				}
			}

			return $this;
		}
	}