<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Tests add_action() function.
 *  
 * @package Amazon Auto Links
 * @since   4.3.4
 * @tags    add_action
*/
class AmazonAutoLinks_UnitTest_WordPress_add_action extends AmazonAutoLinks_UnitTest_Base {

    /**
     * @tags
     */
    public function test_add_action() {
        add_action( 'aal_test_add_action', array( $this, 'recordCalls01' ), 1 );

        do_action( 'aal_test_add_action' );
        $this->_assertEqual( 2, count( $this->___aCalls ) );
        $this->_assertNotEmpty( $this->___aCalls );
    }

        private $___aCalls = array();
        public function recordCalls01() {
            add_action( 'aal_test_add_action', array( $this, 'recordCalls02' ), 10 );
            $this->___aCalls[ __METHOD__ ] = isset( $this->___aCalls[ __METHOD__ ] )
                ? $this->___aCalls[ __METHOD__ ]++
                : 1;
        }
        public function recordCalls02() {
            $this->___aCalls[ __METHOD__ ] = isset( $this->___aCalls[ __METHOD__ ] )
                ? $this->___aCalls[ __METHOD__ ]++
                : 1;
        }


}