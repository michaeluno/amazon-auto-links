<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * A unit test base class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
abstract class AmazonAutoLinks_UnitTest_Base extends AmazonAutoLinks_Run_Base {

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
     * @since 4.3.4
     * @return mixed
     */
    public function call( /* $sMethodName, ...$aParameters */ ) {
        $_aParameters = func_get_args();
        $_sMethodName = array_shift( $_aParameters );
        $this->aErrors = array();
        return call_user_func_array( array( 'parent', $_sMethodName ), $_aParameters );
        // return parent::call( $_sMethodName, ...$aParameters );
    }

    /**
     * @param  boolean $bActual
     * @param  string  $sMessage
     * @param  mixed   $mData
     * @return boolean
     * @sicne  4.3.4
     */
    protected function _assertTrue( $bActual, $sMessage='', $mData=array()) {
        $sMessage = $sMessage ? $sMessage : "Assert true.";
        if ( true !== ( boolean ) $bActual ) {
            $this->_setError( $sMessage, $bActual, $mData );
            return false;
        }
        $this->_setPass( $sMessage );
        return true;
    }
    /**
     * @param  boolean $bActual
     * @param  string $sMessage
     * @param  mixed $mData
     * @sicne  4.3.4
     * @return boolean
     */
    protected function _assertFalse( $bActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert false.";
        if ( false !== ( boolean ) $bActual ) {
            $this->_setError( $sMessage, $bActual, $mData );
            return false;
        }
        $this->_setPass( $sMessage );
        return true;
    }

    /**
     * @param  mixed   $mThing
     * @param  string  $sMessage
     * @return boolean
     * @since  4.3.4
     */
    protected function _assertNotWPError( $mThing, $sMessage='' ) {
        $sMessage = $sMessage ? $sMessage : "Assert not WP_Error.";
        if ( $mThing instanceof WP_Error ) {
            $this->_setError( $sMessage, $mThing->get_error_code() . ' : ' . $mThing->get_error_message(), $mThing->get_error_data() );
            return false;
        }
        $this->_setPass( $sMessage );
        return true;
    }

    /**
     * @param  mixed $mActual
     * @param  string $sMessage
     * @param  array $mData
     * @param  false $bNegate
     * @return boolean
     * @since  4.3.4
     */
    protected function _assertNotEmpty( $mActual, $sMessage='', $mData=array(), $bNegate=false ) {
        $sMessage = $sMessage ? $sMessage : "Assert not empty.";
        $_bEmpty  = empty( $mActual );
        if ( $bNegate ? ! $_bEmpty : $_bEmpty ) {
            $this->_setError( $sMessage, $mActual, $mData );
            return false;
        }
        $this->_setPass( $sMessage, $mActual );
        return true;
    }
    /**
     * @param  mixed $mActual
     * @param  string $sMessage
     * @param  array $mData
     * @return boolean
     * @since  4.3.4
     */
    protected function _assertEmpty( $mActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert empty.";
        return $this->_assertNotEmpty( $mActual, $sMessage, $mData, true );
    }
    /**
     * @sicne  4.3.4
     * @param  mixed  $mExpected
     * @param  mixed  $mActual
     * @param  string $sMessage
     * @param  mixed $mData
     * @return boolean
     */
    protected function _assertEqual( $mExpected, $mActual, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Assert equal.";
        if ( $mExpected !== $mActual ) {
            $_aDisplay = array(
                'expected' => $mExpected,
                'actual'   => $mActual,
            );
            $this->_setError( $sMessage, $_aDisplay, $mData );
            return false;
        }
        $this->_setPass( $sMessage, $mActual );
        return true;
    }

    /**
     * @param  string  $sPrefix     The expected prefix.
     * @param  string  $sHaystack   The string to check.
     * @param  string  $sMessage
     * @param  mixed   $mData
     * @return boolean
     * @sicne  4.3.4
     */
    protected function _assertPrefix( $sPrefix, $sHaystack, $sMessage='', $mData=array() ) {
        $sMessage = $sMessage ? $sMessage : "Check if the string has the specified prefix.";
        if ( ! $this->hasPrefix( $sPrefix, $sHaystack ) ) {
            $this->_setError( $sMessage, $sHaystack, $mData );
            return false;
        }
        $this->_setPass( $sMessage, $sHaystack );
        return true;
    }

    /**
     * @param  string $sSubString
     * @param  string $sHaystack
     * @param  string $sMessage
     * @param  array  $mData
     * @return bool
     */
    protected function _assertSubString( $sSubString, $sHaystack, $sMessage='', $mData=array() ) {
        $sMessage   = $sMessage ? $sMessage : "Check if the string contains the given sub-string ";
        $_biFound   = strpos( $sHaystack, $sSubString );
        if ( false === $_biFound ) {
            $this->_setError( $sMessage, $sHaystack, $mData );
            return false;
        }
        $_sHaystack = ( 0 !== $_biFound ? '...' : '' )
            . substr( $sHaystack, max( $_biFound - 20, 0 ),120 )
            . '...';
        $this->_setPass( $sMessage, $_sHaystack );
        return true;
    }

    /**
     * Sets a success message that shows the test passed.
     * @param string $sMessage
     * @param mixed ...$aValues
     * @since 4.3.4
     */
    protected function _setPass( $sMessage, ...$aValues ) {
        $_oTestException  = new AmazonAutoLinks_Test_Exception( $sMessage, 0, array( 'file' => __FILE__ ) );
        $_iLine           = $_oTestException->get( 'line' );
        $_sDetails        = '';
        foreach( $aValues as $_mValue ) {
            $_sDetails .= $this->_getDetails( $_mValue );
        }
        $this->aOutputs[] = "<p><span class='test-success bold'>OK</span> {$sMessage} Line: {$_iLine}.</p>"
            . $_sDetails;
    }

    /**
     * Sets an error message.
     * @param string $sMessage
     * @param mixed $mValue
     * @param mixed $mData
     * @since 4.3.4
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