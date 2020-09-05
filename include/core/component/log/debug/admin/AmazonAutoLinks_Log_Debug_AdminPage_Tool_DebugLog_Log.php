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
 * Adds the 'Log' form section to the 'Debug Log' tab.
 * 
 * @since       4.3.0
 */
class AmazonAutoLinks_Log_Debug_AdminPage_Tool_DebugLog_Log extends AmazonAutoLinks_Log_Error_AdminPage_Tool_ErrorLog_Log {

    protected function _getArguments() {
        return array(
            'section_id'    => 'log',
            'title'         => 'Debug Log',
        );
    }

    /**
     * A user constructor.
     * 
     * @since       4.3.0
     * @return      void
     */
    protected function _construct( $oFactory ) {
        $this->_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'debug_log' ];
    }

}