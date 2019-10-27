<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Adds the 'Log' form section to the 'Error Log' tab.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_ToolAdminPage_Tool_ErrorLog_Log extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3.9.0
     * @return      void
     */
    protected function _construct( $oFactory ) {}

    /**
     * Adds form fields.
     * @since       3.9.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id   
            array( 
                'field_id'        => '_log',
                'title'           => __( 'Log', 'amazon-auto-links' ),
                'type'            => 'system',
                'value'           => $this->___getErrorLog(),
            ),
            array(
                'field_id'        => '_clear',
                'title'           => __( 'Clear', 'amazon-auto-links' ),
                'type'            => 'submit',
                'value'           => __( 'Clear', 'amazon-auto-links' ),
            ),
            array()
        );

    }

        private function ___getErrorLog() {

            $_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
            $_aErrorLog  = $this->getAsArray( get_option( $_sOptionKey, array() ) );
            $_sErrorLog  = '';
            foreach( $_aErrorLog as $_dMicrosecond => $_aLogItem ) {
                if ( ! isset( $_aLogItem[ 'time' ] ) ) {
                    continue;
                }
                $_sErrorLog .= $this->getSiteReadableDate( $_aLogItem[ 'time' ], 'Y-m-d H:i:s', true ) . ' ' . $_aLogItem[ 'cache_name' ] . ' ' . $_aLogItem[ 'url' ] . "\r\n"
                    . $_aLogItem[ 'message' ] . "\r\n";
            }
            return $_sErrorLog
                ? $_sErrorLog
                : __( 'No items found.', 'amazon-auto-links' );
        }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( __( 'Log items have been cleared.', 'amazon-auto-links' ), 'updated' );
        delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ] );
        return $aInputs;
    }

}