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
 * A unit test base class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_UnitTest_Base extends AmazonAutoLinks_Run_Base {

    /**
     * Stores exception objects.
     * This property must be reset everytime a test method runs.
     * @var array
     */
    public $aErrors = array();

    /**
     * Override this method.
     */
    public function test() {
        return true;
    }

    /**
     * Calls a given method by initializing class properties.
     * @param $sMethodName
     * @param mixed ...$aParameters
     * @since 4.3.3
     * @return mixed
     */
    public function call( $sMethodName, ...$aParameters ) {
        $this->aErrors = array();
        return parent::call( $sMethodName, ...$aParameters );
    }

    /**
     * @param $bBoolean
     * @param $sMessage
     * @param $mData
     * @sicne 4.3.3
     */
    protected function _assertTrue( $bBoolean, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert true.";
        if ( true !== ( boolean ) $bBoolean ) {
            $this->_setError( $sMessage, $bBoolean, $mData );
            return;
        }
        $this->_setPass( $sMessage, $bBoolean );
    }

    /**
     * Sets a success message that shows the test passed.
     * @param string $sMessage
     * @param mixed $mValue
     * @since 4.3.3
     */
    protected function _setPass( $sMessage, $mValue ) {
        $_oTestException  = new AmazonAutoLinks_Test_Exception( $sMessage, 0, 2 );
        $_iLine           = $_oTestException->get( 'line' );
        $this->aOutputs[] = "<p><span class='test-success bold'>OK</span> {$sMessage} Line: {$_iLine}.</p>"
            . $this->_getDetails( $mValue );
    }

    /**
     * Sets an error message.
     * @param string $sMessage
     * @param mixed $mValue
     * @param mixed $mData
     * @since 4.3.3
     */
    protected function _setError( $sMessage, $mValue, $mData ) {
        $_oTestException  = new AmazonAutoLinks_Test_Exception( $sMessage, 0, 2 );
        $_oTestException->setData( 'data', $mData );
        $_iLine           = $_oTestException->get( 'line' );
        $this->aOutputs[] = "<p><span class='test-error bold'>Error</span> {$sMessage} Line: {$_iLine}.</p>"
            . $this->_getDetails( $mValue )
            . ( empty( $mData ) ? '' : $this->_getDetails( $mData ) );
        $this->aErrors[]  = $_oTestException;
    }

}