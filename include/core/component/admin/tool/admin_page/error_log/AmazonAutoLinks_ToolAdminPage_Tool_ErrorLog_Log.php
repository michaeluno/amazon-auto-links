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
                'show_title_column' => false,
                'value'           => $this->___getErrorLog(),
            ),
            array(
                'field_id'        => '_clear',
                'title'           => __( 'Clear', 'amazon-auto-links' ),
                'type'            => 'submit',
                'show_title_column' => false,
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
                $_sErrorLog .= $this->___getLogEntry( $_aLogItem );
            }
            return $_sErrorLog
                ? $_sErrorLog
                : __( 'No items found.', 'amazon-auto-links' );

        }
            /**
             * @since   4.0.0
             */
            private function ___getLogEntry( array $aLogItem ) {
                $_aRequired = array(
                    'time'          => 0,
                    'message'       => '',
                    'current_url'   => '',
                );
                $aLogItem = $aLogItem + $_aRequired;
                $_aExtra  = array_diff_key( $aLogItem, $_aRequired );
                $_sTime   = $this->getSiteReadableDate( $aLogItem[ 'time' ], 'Y-m-d H:i:s', true );
                return $_sTime . ' ' . implode( ' ', $_aExtra ) . "\r\n"
                    . $this->getElement( $aLogItem, array( 'current_url' ) ) . "\r\n"
                    . $aLogItem[ 'message' ] . "\r\n";
            }

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( __( 'Log items have been cleared.', 'amazon-auto-links' ), 'updated' );
        delete_option( AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ] );
        return $aInputs;
    }

}