<?php
namespace sv_tracking_manager_extended;

class freemius extends init {
	public function __construct() {

	}

	public function init() {
		if ( $this->is_parent_active_and_loaded() ) {
			// Init Freemius.
			$this->load_sdk();

			// Signal that the add-on's SDK was initiated.
			do_action( 'sv_tracking_manager_extended_freemius_loaded' );

			// Parent is active, add your init code here.
		} else if ( $this->is_parent_active() ) {
			// Init add-on only after the parent is loaded.
			add_action( 'sv_tracking_manager_extended_freemius_loaded', array($this, 'init') );
		} else {
			// Even though the parent is not activated, execute add-on for activation / uninstall hooks.
			$this->load_sdk();
		}
	}

	public function load_sdk() {
		global $sv_tracking_manager_extended_freemius;

		if ( ! isset( $sv_tracking_manager_extended_freemius ) ) {
			$sv_tracking_manager_extended_freemius = fs_dynamic_init( array(
				'id'                  => '7662',
				'slug'                => 'sv-tracking-manager-extended',
				'premium_slug'        => 'sv_tracking_manager_extended',
				'type'                => 'plugin',
				'public_key'          => 'pk_7665ba6ff220d3e12f05a332e8a4f',
				'is_premium'          => true,
				'has_addons'          => true,
				'has_paid_plans'      => true,
				'is_org_compliant'    => false,
				'parent'              => array(
					'id'         => '4993',
					'slug'       => 'sv-tracking-manager',
					'public_key' => 'pk_20c9b91b701dbbd82fc28dcb2c576',
					'name'       => 'SV Tracking Manager',
				),
				'menu'                => array(
					'slug'           => 'sv_tracking_manager_extended',
					'support'        => false,
					'parent'         => array(
						'slug' => 'straightvisions',
					),
				),
			) );
		}

		do_action( $this->get_root()->get_name().'_freemius_loaded' );

		return $sv_tracking_manager_extended_freemius;
	}
	function is_parent_active_and_loaded() {
		// Check if the parent's init SDK method exists.
		return $this->is_instance_active('sv_tracking_manager');
	}

	function is_parent_active() {
		$active_plugins = get_option( 'active_plugins', array() );

		if ( is_multisite() ) {
			$network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
			$active_plugins         = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
		}

		foreach ( $active_plugins as $basename ) {
			if ( 0 === strpos( $basename, 'sv-tracking-manager/' ) ||
				0 === strpos( $basename, 'sv-tracking-manager-premium/' )
			) {
				return true;
			}
		}

		return false;
	}
}