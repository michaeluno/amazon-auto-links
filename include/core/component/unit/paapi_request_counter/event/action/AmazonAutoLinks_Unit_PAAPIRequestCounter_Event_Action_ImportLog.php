<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Handles importing PA-API request count logs.
 *
 * @since 4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_ImportLog extends AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_ExportLog {

    /**
     * Sets up hooks.
     * @param    AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback add_action() load_{page slug}_{tab slug}
     */
    public function replyToLoadTab( $oFactory ) {
        $_sPageSlug = $oFactory->oProp->getCurrentPageSlug();
        $_sTabSlug  = $oFactory->oProp->getCurrentTabSlug();
        add_filter( "import_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToGetImportDataFormatted' ), 10, 7 );
        add_filter( "import_format_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToGetFormat' ), 10, 0 );
        add_filter( "import_option_key_{$_sPageSlug}_{$_sTabSlug}", '__return_empty_string', 10, 0 );   // cancel saving in the options table with the main plugin option key
        add_filter( "import_mime_types_{$_sPageSlug}_{$_sTabSlug}", array( $this, 'replyToGetMIMEType' ), 10, 1 );
    }
    public function replyToGetMIMEType( $aMIMETypes ) {
        $aMIMETypes[] = 'text/csv';
        return $aMIMETypes;
    }
    public function replyToGetImportDataFormatted( $sCSV, $aOptions, $sFieldID, $sInputID, $sFormatType, $sOptionKey, $oFactory ) {

        try {

            $this->___saveCountLog( $sCSV );

        } catch ( Exception $_oException ) {

            $oFactory->setSettingNotice( $_oException->getMessage() );

        }
        return $sCSV;

    }

        /**
         * @param  string $sCSV
         * @throws Exception
         */
        private function ___saveCountLog( $sCSV ) {

            $_aCSVLog = $this->___getLogFromCSV( $sCSV, $_sLocale );
            if ( ! $_sLocale ) {
                throw new Exception( __( 'The locale could not be detected. The data might be corrupt.', 'amazon-auto-links' ) );
            }
            $_oLog = new AmazonAutoLinks_Unit_PAAPIRequestCounter_LogRetriever_Database( $_sLocale );
            $_oLog->set( $this->uniteArrays( $_oLog->getAll(), $_aCSVLog ) );
            $_oLog->save();

        }
            private function ___getLogFromCSV( $sCSV, &$sLocale ) {
                $sCSV    = preg_replace( '/[\r\n]+/', PHP_EOL, $sCSV );
                $_aLines = explode(PHP_EOL, $sCSV );
                unset( $_aLines[ 0 ] ); // the first element is column names (labels) so drop it.

                $_aLog       = array();
                foreach ( $_aLines as $_sLine ) {
                    $_aFields = str_getcsv( $_sLine );
                    if ( ! isset( $_aFields[ 0 ], $_aFields[ 1 ], $_aFields[ 2 ] ) ) {
                        continue;
                    }
                    $sLocale      = $_aFields[ 2 ]; // update the parameter variable
                    $_iCount      = ( integer ) $_aFields[ 1 ];
                    $_sTimeByHour = $_aFields[ 0 ]; // e.g. 2020-10-30 07:00
                    $_aTimeParts  = explode( ' ', $_sTimeByHour );
                    $_aDateParts  = explode( '-', $_aTimeParts[ 0 ] );
                    $_aHourParts  = explode( ':', $_aTimeParts[ 1 ] );
                    $_aKeys       = array( $_aDateParts[ 0 ], $_aDateParts[ 1 ], $_aDateParts[ 2 ], $_aHourParts[ 0 ] ); // should be array( '2020', '10', '30', '07' )
                    $this->setMultiDimensionalArray( $_aLog, $_aKeys, $_iCount );
                }
                return $_aLog;
            }

}