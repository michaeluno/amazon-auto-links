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
 * Adds the 'Log' form section to the 'Error Log' tab.
 * 
 * @since       3.9.0
 */
class AmazonAutoLinks_Log_Error_AdminPage_Section_ErrorLog_Log extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @var       string
     * @since     4.3.0
     * @deprecatd 4.4.0  Uses a file.
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
     * @deprecated  4.4.0
     */
/*    protected function _construct( $oFactory ) {

        $this->_sOptionKey = AmazonAutoLinks_Registry::$aOptionKeys[ 'error_log' ];

    }*/

    /**
     * Adds form fields.
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @since       3.9.0
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
        foreach( $this->_getFields() as $_aFieldset ) {
            $oFactory->addSettingFields( $sSectionID, $_aFieldset );
        }
    }
        /**
         * @return array[]
         * @sinec  4.4.0
         */
        protected function _getFields() {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return array(
                array(
                    'field_id'          => '_filters',
                    'title'             => __( 'Filters', 'amazon-auto-links' ),
                    'content'           => array(
                        array(
                            'field_id'          => 'include',
                            'title'             => __( 'Include', 'amazon-auto-links' ),
                            'type'              => 'text',
                            'save'              => false,
                            'class'             => array(
                                'input' => 'width-full filter-include',
                                'field' => 'width-full',
                            ),
                        ),
                        array(
                            'field_id'          => 'exclude',
                            'title'             => __( 'Exclude', 'amazon-auto-links' ),
                            'type'              => 'text',
                            'save'              => false,
                            'class'             => array(
                                'input' => 'width-full filter-exclude',
                                'field' => 'width-full',
                            ),
                        ),
                    ),
                    'description'       => __( 'Type characters that match log entries to include/exclude separated commas.', 'amazon-auto-links' ),
                ),
                array(
                    'field_id'          => '_log',
                    'save'              => false,
                    'show_title_column' => false,
                    'class'             => array(
                        'field' => 'width-full',
                        'input' => 'width-full log',
                    ),
                    'content'           => $this->___getCopyToClipboardButton()
                        . $this->___getLogHTMLPart(),
                ),
                array(
                    'field_id'          => '_clear',
                    'title'             => __( 'Clear', 'amazon-auto-links' ),
                    'type'              => 'submit',
                    'save'              => false,
                    'show_title_column' => false,
                    'value'             => __( 'Clear', 'amazon-auto-links' ),
                ),
                array(
                    'field_id'          => '_debug_title',
                    'title'             => 'Debug',
                    'if'                => $_oOption->isDebug() || $_oOption->isDebugMode(),
                    'save'              => false,
                ),
                array(
                    'field_id'          => '_debug_output',
                    'show_title_column' => false,
                    'if'                => $_oOption->isDebug() || $_oOption->isDebugMode(),
                    'save'              => false,
                    'content'           => $this->___getDebugOutput(),
                )
            );
        }
            private function ___getDebugOutput() {
                $_oFile        = $this->_getLogFileObject();
                $_sFilePath    = $_oFile->getFilePath();
                $_iSizeInBytes = file_exists( $_sFilePath )
                    ? filesize( $_sFilePath )
                    : 0;
                return "<div class='debug-output'>"
                        . AmazonAutoLinks_Debug::get( array(
                            'file path' => $_sFilePath,
                            'exists'    => file_exists( $_sFilePath ) ? 'Yes' : 'No',
                            'size'      => $this->getReadableBytes( $_iSizeInBytes ),
                        ) )
                    .  "</div>";
            }
        private function ___getCopyToClipboardButton() {
            return "<div class='button-container-copy-to-clipboard'>"
                    . "<a class='button-secondary copy-to-clipboard'>" . __( 'Copy to Clipboard', 'amazon-auto-links' ) . "</a>"
                . "</div>";
        }
        private function ___getLogHTMLPart() {
            // @deprecated 4.4.0
            // $_aLog          = $this->getAsArray( get_option( $this->_sOptionKey, array() ) );
            $_oLogFile      = $this->_getLogFileObject();
            $_aLog          = $_oLogFile->get();
            $_sLogHTMLPart  = '';
            foreach( array_reverse( $_aLog ) as $_aLogItem ) {
                $_sLogHTMLPart .= $this->___getLogEntryHTMLPart( $this->getAsArray( $_aLogItem ) );
            }
            return "<div class='log'>" . $_sLogHTMLPart . "</div>";

        }

            /**
             * @return AmazonAutoLinks_Log_VersatileFileManager_ErrorLog
             */
            protected function _getLogFileObject() {
                return new AmazonAutoLinks_Log_VersatileFileManager_ErrorLog;
            }

            private function ___getLogEntryHTMLPart( array $aLogItem ) {

                $_aRequired = array(
                    'time'          => 0,   'message'       => '', 'current_hook'  => '',
                    'current_url'   => '',  'page_load_id'  => '', 'stack_trace'   => '',
                );
                $_sPad         = '    ';
                $aLogItem      = $aLogItem + $_aRequired;
                $_aExtra       = array_diff_key( $aLogItem, $_aRequired );
                $_iTime        = floor( $aLogItem[ 'time' ] );
                $_sTime        = $this->getSiteReadableDate( $_iTime, 'Y-m-d H:i:s', true );
                $_sFraction    = $this->getPrefixRemoved( ( string ) round( $aLogItem[ 'time' ] - $_iTime, 4 ), '0' );
                $_sTime        = $_sTime . $_sFraction . ' ';
                $_sPageLoadID  = $this->getElement( $aLogItem, array( 'page_load_id' ), '' );
                $_sPageLoadID  = $_sPageLoadID ? $_sPageLoadID . ' ' : '';
                $_sCurrentHook = $this->getElement( $aLogItem, array( 'current_hook' ), '' );
                $_sCurrentHook = $_sCurrentHook ? $_sCurrentHook . ' ' : '';
                $_sStackTrace  = $this->getElement( $aLogItem, array( 'stack_trace' ), '' );
                $_sDetails     = $this->___getArrayRepresentation( $_aExtra, $_sPad );
                $_sDetails     = $_sDetails
                    ? "<div class='extra'>" . strip_tags( $_sDetails ) . "</div>"
                    : '';
                $_sDetails    .= ( $_sStackTrace
                    ? "<textarea class='stack-trace' wrap='off' readonly='readonly'>" . $_sStackTrace . "</textarea>"
                    : '' );
                $_sURL         = urldecode( $this->getElement( $aLogItem, array( 'current_url' ) ) );
                return "<div class='log-item'>"
                        . "<div class='log-item-head'>"
                                . "<h5 class='log-item-title'>" . $_sTime . $_sPageLoadID . $_sCurrentHook . $_sURL . "</h5>"
                                . (
                                    $aLogItem[ 'message' ]
                                        ? "<p class='log-item-message'>" . $_sPad . strip_tags( $aLogItem[ 'message' ] ) . "</p>"
                                        : ''
                                )
                            . "</div>"
                        . (
                            $_sDetails
                                ? "<div class='log-item-body'>"
                                    . $_sDetails
                                . "</div>"
                                : ''
                        )
                    . "</div>";
            }
                private function ___getArrayRepresentation( array $aArray, $sPad='' ) {

                    $_sOutput  = '';

                    if ( empty( $aArray ) ) {
                        return $_sOutput;
                    }

                    foreach( $aArray as $_sKey => $mValue ) {
                        if ( is_scalar( $mValue ) ) {
                            $_sValue   = '(' . gettype( $mValue ) . ') ' . ( string ) $mValue;
                        } else {
                            $_sClass   = is_object( $mValue ) ? ': ' . get_class( $mValue ) : '';
                            $_sValue   = '(' . gettype( $mValue ) . $_sClass . ')' . PHP_EOL;
                            $_sValue  .= $this->___getArrayRepresentation( ( array ) $mValue, $sPad . '    ' );
                            $_sValue   = trim( $_sValue );
                        }
                        $_sOutput .= $sPad . $_sKey . ': ' . $_sValue . PHP_EOL;
                    }
                    return rtrim( $_sOutput ) . PHP_EOL;

                }

    /**
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oAdminPage
     * @param  array $aSubmitInfo
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
        $oAdminPage->setSettingNotice( '' ); // disable the notice
        // @deprecated 4.4.0
        // delete_option( $this->_sOptionKey );
        $_oFile = $this->_getLogFileObject();
        $_oFile->delete();
        return $aInputs;
    }

}