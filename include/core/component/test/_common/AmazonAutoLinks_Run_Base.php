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
     * @param $mValue
     * @return string
     */
    protected function _getDetails( $mValue ) {
        return AmazonAutoLinks_Debug::getDetails( $mValue );
    }

    /**
     * @param string $sErrorMessage
     * @param integer|string $isCode
     * @throws Exception
     */
    protected function _throwError( $sErrorMessage, $isCode=0 ) {
        throw new Exception( $sErrorMessage, $isCode );
    }

}