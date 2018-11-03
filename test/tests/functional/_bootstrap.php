<?php
$_sPluginRootDirPath = dirname( codecept_root_dir() );
$_sWPTestLibDirPath  = getenv( 'WP_TESTS_DIR' );
// $_sSystemTempDirPath         = getenv( 'TEMP' ) ? getenv( 'TEMP' ) : '/tmp';
$GLOBALS[ '_sProjectDirPath' ]  = $_sPluginRootDirPath; // dirname( dirname( dirname( dirname( __FILE__ ) ) ) );
$_sTestSiteDirPath              = dirname( dirname( dirname( $GLOBALS['_sProjectDirPath'] ) ) );
if ( ! $_sWPTestLibDirPath ) {
    $_sWPTestLibDirPath = $_sTestSiteDirPath . '/wordpress-tests-lib';
}

define( 'WP_USE_THEMES', false );

// Referenced from bootstrap.php
$GLOBALS[ '_sTestsDirPath' ] = $_sWPTestLibDirPath;
require_once $GLOBALS[ '_sTestsDirPath' ] . '/includes/functions.php';


// Store the value of the $file variable as it will be changed by WordPress.
// $_file = isset( $file ) ? $file : null;
require_once( $GLOBALS[ '_sTestsDirPath' ] . '/includes/bootstrap.php' );
// $file = $_file;

$_noActivated = activate_plugin( '0-delay-late-caching-for-feeds/0-delay-late-caching-for-feeds.php' );

// Console messages
codecept_debug( 'Activated Plugin: ' . ( null === $_noActivated ? 'Yes' : 'No' ) );
codecept_debug( 'Codeception Directory: ' . codecept_root_dir() );

class WPPlugin_UnitTestCase extends \WP_UnitTestCase {

    /**
     * @var bool
     * @see     https://core.trac.wordpress.org/ticket/39327
     */
    protected $backupGlobals = true;

    public function setUp() {

        /**
         * @see     https://core.trac.wordpress.org/ticket/39327#comment:8
         */
        $GLOBALS[ 'wpdb' ]->db_connect(); // this must be done before the parent `setUp()` method.
        parent::setUp();
    }

    public function tearDown() {
        parent::tearDown();
    }

    /**
     * @remark      Fixes the error: [PHPUnit_Framework_Exception] mysqli_query(): Couldn't fetch mysqli.
     * @see         https://wordpress.org/support/topic/wp_unittestcaseteardown-causes-mysqli_query-couldnt-fetch-mysqli/
     */
    public static function tearDownAfterClass() {

        // \PHPUnit\Framework\TestCase::tearDownAfterClass();
        PHPUnit_Framework_TestCase::tearDownAfterClass();

        // This causes an error: [PHPUnit_Framework_Exception] mysqli_query(): Couldn't fetch mysqli
        // _delete_all_data();
        // self::flush_cache();

        $c = self::get_called_class();
        if ( ! method_exists( $c, 'wpTearDownAfterClass' ) ) {
            return;
        }

        call_user_func( array( $c, 'wpTearDownAfterClass' ) );
        self::commit_transaction();

    }
}


codecept_debug( 'Functional: _bootstrap.php loaded' );
var_dump( 'functional bootstrap' );