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
     * @param boolean $bActual
     * @param string $sMessage
     * @param mixed $mData
     * @sicne 4.3.3
     */
    protected function _assertTrue( $bActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert true.";
        if ( true !== ( boolean ) $bActual ) {
            $this->_setError( $sMessage, $bActual, $mData );
            return;
        }
        $this->_setPass( $sMessage, $bActual );
    }
    /**
     * @param boolean $bActual
     * @param string $sMessage
     * @param mixed $mData
     * @sicne 4.3.3
     */
    protected function _assertFalse( $bActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert false.";
        $this->_assertTrue( ! ( boolean ) $bActual, $sMessage, $mData );
    }

    /**
     * @param mixed $mActual
     * @param string $sMessage
     * @param array $mData
     * @param false $bNegate
     * @since 4.3.3
     */
    protected function _assertNotEmpty( $mActual, $sMessage='', $mData=array(), $bNegate=false ) {
        $sMessage = $sMessage ? $sMessage : "Assert not empty.";
        $_bEmpty  = empty( $mActual );
        if ( $bNegate ? ! $_bEmpty : $_bEmpty ) {
            $this->_setError( $sMessage, $mActual, $mData );
            return;
        }
        $this->_setPass( $sMessage, $mActual );
    }
    /**
     * @param mixed $mActual
     * @param string $sMessage
     * @param array $mData
     * @since 4.3.3
     */
    protected function _assertEmpty( $mActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert empty.";
        $this->_assertNotEmpty( $mActual, $sMessage, $mData, true );
    }
    /**
     * @param mixed $mExpected
     * @param mixed $mActual
     * @param $sMessage
     * @param $mData
     * @sicne 4.3.3
     */
    protected function _assertEqual( $mExpected, $mActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert equal.";
        if ( $mExpected !== $mActual ) {
            $this->_setError( $sMessage, $mActual, $mData );
            return;
        }
        $this->_setPass( $sMessage, $mActual);
    }

    /**
     * @param string $sPrefix The expected prefix.
     * @param string $sHaystack The string to check.
     * @param $sMessage
     * @param $mData
     * @sicne 4.3.3
     */
    protected function _assertPrefix( $sPrefix, $sHaystack, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Check if the string has the specified prefix.";
        if ( ! $this->hasPrefix( $sPrefix, $sHaystack ) ) {
            $this->_setError( $sMessage, $sHaystack, $mData );
            return;
        }
        $this->_setPass( $sMessage, $sHaystack );
    }

    /**
     * Sets a success message that shows the test passed.
     * @param string $sMessage
     * @param mixed $mValue
     * @since 4.3.3
     */
    protected function _setPass( $sMessage, $mValue ) {
        $_oTestException  = new AmazonAutoLinks_Test_Exception( $sMessage, 0, array( 'file' => __FILE__ ) );
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
        $_oTestException  = new AmazonAutoLinks_Test_Exception( $sMessage, 0, array( 'file' => __FILE__ ) );
        $_oTestException->setData( 'data', $mData );
        $_iLine           = $_oTestException->get( 'line' );
        $this->aOutputs[] = "<p><span class='test-error bold'>Error</span> {$sMessage} Line: {$_iLine}.</p>"
            . $this->_getDetails( $mValue )
            . ( empty( $mData ) ? '' : $this->_getDetails( $mData ) );
        $this->aErrors[]  = $_oTestException;
    }

}