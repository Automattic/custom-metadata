<?php
class Test_Custom_Metadata_Manager extends WP_UnitTestCase {

	function setUp() {
		parent::setUp();
		$this->load_plugin();
	}

	function load_plugin() {
		if ( ! class_exists( 'custom_metadata_manager' ) ) {
			require_once( dirname( dirname( __FILE__ ) ) . '/custom_metadata.php' );
		}

		custom_metadata_manager::instance();
		add_action( 'custom_metadata_manager_init', array( $this, '_add_a_group' ) );
		do_action( 'admin_init' );
		$this->post_metadata = &custom_metadata_manager::instance()->metadata['post'];
	}

}