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
abstract class AmazonAutoLinks_Run_Base extends AmazonAutoLinks_Run_Utility {

    /**
     * Stores user outputs thrown in the middle of a test method.
     * @var array
     */
    public $aOutputs = array();

    /**
     * Calls a given method by initializing class properties.
     * @return mixed
     * @since 4.3.4
     */
    public function call( /* $sMethodName, ...$aParameters */ ) {
        $_aParameters   = func_get_args();
        $_sMethodName   = array_shift( $_aParameters );
        $this->aOutputs = array();
        $this->_doBefore();
        $_mResult       = call_user_func_array( array( $this, $_sMethodName ), $_aParameters );
        $this->_doAfter();
        return $_mResult;
    }

    /**
     * Called right before a test method is invoked.
     * @remark Override this method to do some set-ups per method call basis.
     * @since 4.3.4
     */
    protected function _doBefore() {}

    /**
     * Called right after a test method is invoked.
     * @remark Override this method to do some set-ups per method call basis.
     * @since 4.3.4
     */
    protected function _doAfter() {}

    /**
     * @param $mValue
     * @return string
     * @since 4.3.0
     */
    protected function _getDetails( $mValue ) {
        return AmazonAutoLinks_Debug::getDetails( $mValue );
    }

    /**
     * @param string $sErrorMessage
     * @param integer $iCode
     * @throws Exception
     * @since 4.3.4
     */
    protected function _throw( $sErrorMessage, $iCode=0 ) {
        throw new AmazonAutoLinks_Test_Exception( $sErrorMessage, ( integer ) $iCode, array( 'file' => __FILE__ ) );
    }

    /**
     * @param string $sText
     * @since 4.3.4
     */
    protected function _output( $sText ) {
        $this->aOutputs[] = $sText;
    }

    /**
     * @param string sTitle
     * @param mixed ...$mValues
     * @since 4.3.4
     */
    protected function _outputDetails( /* $sTitle, ...$mValues */ ) {
        $_aParams = func_get_args();
        if ( 1 === func_num_args() ) {
            $this->aOutputs[] = $this->_getDetails( $_aParams[ 0 ] );
            return;
        }
        $_sTitle  = array_shift( $_aParams );
        $_aValues = $_aParams;
        $_sOutput = "<h5>{$_sTitle}</h5>";
        foreach( $_aValues as $_mValue ) {
            $_sOutput .= $this->_getDetails( $_mValue );
        }
        $this->aOutputs[] = $_sOutput;
    }

}