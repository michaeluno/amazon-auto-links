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
 * Utility tests regarding transients.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
 * @tags        transient
*/
class AmazonAutoLinks_UnitTest_FrameworkUtility_Transient extends AmazonAutoLinks_UnitTest_Base {

    private $___sTransientKey = 'aal_test_transient_key';

    private $___aTestData = array(
        'foo' => 'bar',
    );

    /**
     * Override this method.
     * @return mixed
     * @tags transient
     */
    public function test_setTransient() {
        return $this->setTransient( $this->___sTransientKey, $this->___aTestData, 10 );
    }

    public function test_getTransient() {
        return $this->getTransient( $this->___sTransientKey, 'DEFAULT_VALUE' );
    }

    public function test_deleteTransient() {
        return $this->deleteTransient( $this->___sTransientKey );
    }

    public function test_checkTransientDeleted() {
        return 'DEFAULT_VALUE' === $this->getTransient( $this->___sTransientKey, "DEFAULT_VALUE" );
    }

    public function test_setTransientWithShortLifespan() {
        return $this->setTransient( $this->___sTransientKey, $this->___aTestData, 1 );
    }

    public function test_getTransientOfShortLifespan() {
        sleep( 2 );
        return 'DEFAULT_VALUE' === $this->getTransient( $this->___sTransientKey, 'DEFAULT_VALUE' );
    }

    public function test_getTransientWithoutCache() {
        $this->setTransient( $this->___sTransientKey, $this->___aTestData, 1 );
        return $this->getTransientWithoutCache( $this->___sTransientKey );
    }

    public function test_getTransientWithoutCacheExpiry() {
        sleep( 2 );
        return false === $this->getTransientWithoutCache( $this->___sTransientKey, false );
    }

    public function test_setAndGetTransient() {

        $this->setTransient( $this->___sTransientKey, 'first', 100 );
        $_sFirstValue = $this->getTransient( $this->___sTransientKey );
        $this->setTransient( $this->___sTransientKey, 'second', 100 );
        return $_sFirstValue !== $this->getTransient( $this->___sTransientKey );

    }

    /**
     * @return bool
     * @throws Exception
     */
    public function test_cleanTransients() {
        $this->cleanTransients( $this->___sTransientKey );
        $_mValue = $this->getTransientWithoutCache( $this->___sTransientKey );
        if ( null !== $_mValue ) {
            throw new Exception( $this->_getDetails( $_mValue ) );
        }
        return true;
    }

}