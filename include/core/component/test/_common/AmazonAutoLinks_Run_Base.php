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
class AmazonAutoLinks_Run_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Stores user outputs thrown in the middle of a test method.
     * @var array
     */
    public $aOutputs = array();

    /**
     * @param $mValue
     * @return string
     */
    protected function _getDetails( $mValue ) {
        return AmazonAutoLinks_Debug::getDetails( $mValue );
    }

    /**
     * @param string $sErrorMessage
     * @param integer $iCode
     * @throws Exception
     * @deprecated 4.3.1    The line and file given by the stacktrace are not accurate.
     */
/*    protected function _throwError( $sErrorMessage, $iCode=0 ) {
        throw new Exception( $sErrorMessage, ( integer ) $iCode );
    }*/

    /**
     * @param string $sText
     * @since 4.3.3
     */
    protected function _output( $sText ) {
        $this->aOutputs[] = $sText;
    }

}