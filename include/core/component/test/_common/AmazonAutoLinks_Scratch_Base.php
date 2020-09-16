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
 * A scratch base class.
 *  
 * @package     Amazon Auto Links
 * @since       4.3.0
*/
class AmazonAutoLinks_Scratch_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * Override this method.
     * @return mixed
     */
    public function scratch() {
        return true;
    }

    protected function _getDetails( $mValue ) {
        return AmazonAutoLinks_Debug::getDetails( $mValue );
    }

}