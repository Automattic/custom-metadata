<?php

/**
 * Base unit test class for Custom Metadata
 */
class CustomMetadata_TestCase extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		global $custom_metadata;
		$this->_toc = $custom_metadata;
	}
}
