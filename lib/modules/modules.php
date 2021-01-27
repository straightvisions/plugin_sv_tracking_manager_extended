<?php
	namespace sv_tracking_manager_extended;
	
	class modules extends init {
		public function __construct() {
		
		}
		
		public function init() {
			$this->usercentrics->init();

			$this->freemius->init();
		}
	}