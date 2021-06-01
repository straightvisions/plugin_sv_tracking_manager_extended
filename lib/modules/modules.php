<?php
	namespace sv_tracking_manager_extended;
	
	class modules extends init {
		public function __construct() {
		
		}
		
		public function init() {
			$this->load_module('usercentrics');
			$this->load_module('plausible');
			$this->load_module('freemius');
		}
	}