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
     * @var string
     * @since   4.3.0
     */
    protected $_sOptionKey = '';

    protected function _getArguments() {
        return array(
            'section_id'    => 'log',
            'title'         => __( 'Errors', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3.9.0
     * @return      void
     */
    protected function _construct( $oFactory ) {
        $this->_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];
    }

    /**
     * Adds form fields.
     * @since       3.9.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id   
            array( 
                'field_id'          => '_log',
                'title'             => __( 'Log', 'amazon-auto-links' ),
                'type'              => 'textarea',
                'save'              => false,
                'show_title_column' => false,
                'attributes'        => array(
                    'readonly'  => 'readonly',
                    'style'     => 'min-height: 720px;',
                    'wrap'      => 'off',
                ),
                'class'             => array(
                    'field' => 'width-full',
                    'input' => 'width-full',
                ),
                'value'             => $this->___getErrorLog(),
            ),
            array(
                'field_id'          => '_clear',
                'title'             => __( 'Clear', 'amazon-auto-links' ),
                'type'              => 'submit',
                'save'              => false,
                'show_title_column' => false,
                'value'             => __( 'Clear', 'amazon-auto-links' ),
            ),
            array()
        );

    }
        private function ___getErrorLog() {

            $_aErrorLog  = $this->getAsArray( get_option( $this->_sOptionKey, array() ) );
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
                    'page_load_id'  => '',
                );
                $aLogItem     = $aLogItem + $_aRequired;
                $_aExtra      = array_diff_key( $aLogItem, $_aRequired );
                $_sTime       = $this->getSiteReadableDate( $aLogItem[ 'time' ], 'Y-m-d H:i:s', true ) . ' ';
                $_sPageLoadID = $this->getElement( $aLogItem, array( 'page_load_id' ), '' );
                $_sPageLoadID = $_sPageLoadID ? $_sPageLoadID . ' ' : '';
                return $_sTime . $_sPageLoadID . $this->getElement( $aLogItem, array( 'current_url' ) ) . PHP_EOL
                    . '    ' . $aLogItem[ 'message' ] . PHP_EOL
                    . $this->___getArrayRepresentation( $_aExtra, '    ' )
                    ;
            }
                private function ___getArrayRepresentation( array $aArray, $sPad='' ) {

                    $_sOutput = '';

                    if ( empty( $aArray ) ) {
                        return $_sOutput;
                    }

                    foreach( $aArray as $_sKey => $mValue ) {
                        if ( is_scalar( $mValue ) ) {
                            $_sValue   = '(' . gettype( $mValue ) . ') ' . ( string ) $mValue;
                        } else {
                            $_sValue   = '(' . gettype( $mValue ) . ')' . PHP_EOL;
                            $_sValue  .= $this->___getArrayRepresentation( ( array ) $mValue, $sPad . '    ' );
                            $_sValue   = trim( $_sValue );
                        }
                        $_sOutput .= $sPad . $_sKey . ': ' . $_sValue . PHP_EOL;
                    }
                    return rtrim( $_sOutput ) . PHP_EOL;

                }

            /**
             * @since   4.0.0
             * @deprecated  4.3.0
             */
            /*private function ___getLogEntry( array $aLogItem ) {
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
            }*/

    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( '' ); // disable the notice
        delete_option( $this->_sOptionKey );
        return $aInputs;
    }

}