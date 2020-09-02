<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Performs scratches regarding database.
 * @since   4.3.0
 *
 */
class AmazonAutoLinks_Test_Event_Ajax_Delete extends AmazonAutoLinks_Test_Event_Ajax_Tests {

    protected $_sActionHookSuffix = 'aal_action_admin_do_delete';
    protected $_bLoggedIn = true;
    protected $_bGuest    = false;

    /**
     * @since   4.3.0
     * @return  void
     */
    protected function _construct() {
        // load_{page slug}_{tab slug}
        add_action( 'load_aal_tests_delete', array( $this, 'replyToEnqueueResources' ) );
    }
        /**
         * @since       4.3.0
         * @return      void
         */
        public function replyToEnqueueResources() {
            $this->_enqueueResources( AmazonAutoLinks_Test_Loader::$sDirPath . '/run/delete', array( 'AmazonAutoLinks_Scratch_Base' ), 'database' );
        }

    /**
     * @param string $sClassName The class name to test.
     * @param string $sFilePath The file path of the class.
     * @param string $sMethodPrefix The prefix of methods to test.
     * @return array
     * @throws ReflectionException
     * @since   4.3.0
     */
    protected function _getResults( $sClassName, $sFilePath, $sMethodPrefix='scratch' ) {
        return parent::_getResults( $sClassName, $sFilePath, 'scratch' );
    }

}