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
 * A scratch class for the PA-API request counter.
 *  
 * @package     Amazon Auto Links
 * @since       4.4.0
*/
class AmazonAutoLinks_Scratch_PAAPIRequestCounter extends AmazonAutoLinks_Scratch_Base {

    /**
     * @purpose Saves file log into the options table.
     * @tags   save
     * @throws Exception
     */
    public function scratch_saveFileLogToDatabase() {
        $_aLocales = func_get_args();
        $_iCount   = 0;
        foreach( $_aLocales as $_sLocale ) {
            if ( ! AmazonAutoLinks_Locales::isValidLocale( $_sLocale ) ) {
                $this->_output( "The specified locale '{$_sLocale}' is not valid." );
                continue;
            }
            $this->_output( 'Saving PA-API Request counter log. Locale: ' . $_sLocale );
            do_action( 'aal_action_paapi_request_counter_save_log', $_sLocale );
            $_iCount++;
        }
        if ( ! $_iCount ) {
            throw new Exception( 'No locale is processed. Set a locale code in the argument field.' );
        }
    }

    /**
     * @tags    counter
     * @purpose Generates PA-API request count log for testing.
     * @throws  ReflectionException
     * @throws Exception
     */
    public function scratch_GenerateLog() {
        $_iCount   = 0;
        foreach( func_get_args() as $_sLocale ) {
            if ( ! AmazonAutoLinks_Locales::isValidLocale( $_sLocale ) ) {
                $this->_output( "The specified locale '{$_sLocale}' is not valid." );
                continue;
            }
            $_oMock    = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter', array( $_sLocale ) );
            $_sDirPath = $_oMock->call( 'getDirectoryPath' );
            if ( ! is_dir( $_sDirPath ) ) {
                mkdir( $_sDirPath, 0777, true );
            }

            // $_oCounter = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( 'IT' );
            $_iNow   = time();
            $_iDay = 1;
            while ( $_iDay <= 60 ) {

                $_iTimeDay  = $_iNow - ( 86400 * $_iDay );
                $_iHour = 1;
                while( $_iHour <= 24 ) {
                    $_iTime = $_iTimeDay + ( $_iHour * 3600 );
                    $_sThisFilePath = $_sDirPath . '/' . date( 'Y/m/d/H', $_iTime ) . '.txt';
                    $this->_outputDetails( 'file path', $_sThisFilePath );
                    $_sThisDirPath  = dirname( $_sThisFilePath );
                    if ( ! is_dir( $_sThisDirPath ) ) {
                        mkdir( $_sThisDirPath, 0777, true );
                    }
                    file_put_contents( $_sThisFilePath, rand( 1, 300 ) );
                    $_iHour++;
                }
                $_iDay++;

            }
            $_iCount++;
        }
        if ( ! $_iCount ) {
            throw new Exception( 'No locale is processed. Set a locale code in the argument field.' );
        }

    }

}