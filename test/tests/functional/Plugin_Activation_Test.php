<?php
/**
 * Manually include the bootstrap script as Codeception bootstrap runs after loading this file.
 * @see https://github.com/Codeception/Codeception/issues/862
 */
// include_once( dirname( dirname( __FILE__ ) ) . '/_bootstrap.php' );

/**
 * @group   wp
 */
class Plugin_Activation_Test extends \WPPlugin_UnitTestCase {

    public function setUp() {
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    /**
     * Checks DB connection.
     */
    public function test_is_connected() {
        $this->assertTrue(
            $GLOBALS[ 'wpdb' ]->check_connection()
        );
    }

    public function test_get_option() {
        codecept_debug( 'site url: ' . get_bloginfo( 'url' ) );
        codecept_debug( 'site name: ' . get_option( 'blogname' ) );

        $this->assertTrue( get_option( 'testing_non_existing_option_key', true ) );
    }

    /**
     * Check if the plugin is activated.
     */
    public function test_is_active() {

        $this->assertTrue(
            is_plugin_active( '0-delay-late-caching-for-feeds/0-delay-late-caching-for-feeds.php' )
        );

    }

}
