<?php
class Test_Custom_Metadata_Manager extends WP_UnitTestCase {

	public $group_slug = 'test_group';
	public $group_label = 'Test Group';
	public $non_existant_group_slug = 'this_group_does_not_exist';
	public $post_metadata = array();

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

	function _add_a_group(){
		x_add_metadata_group( $this->group_slug, array( 'post' ), array(
			'label' => $this->group_label,
		) );
	}

	function test_post_has_added_group() {
		$this->assertArrayHasKey( $this->group_slug, custom_metadata_manager::instance()->metadata['post'] );
	}

	function test_post_has_not_added_nonexistant_group() {
		$this->assertArrayNotHasKey( $this->non_existant_group_slug, custom_metadata_manager::instance()->metadata['post'] );
	}

	function test_group_label_is_correct() {
		$this->assertEquals( $this->group_label, $this->post_metadata[$this->group_slug]->label );
	}

}