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
     * @tags    counter
     * @purpose Generates PA-API request count log for testing.
     * @throws  ReflectionException
     */
    public function scratch_GenerateLog() {
        $_oMock    = new AmazonAutoLinks_MockClass( 'AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter', array( 'US' ) );
        $_sDirPath = $_oMock->call( '_getDirectoryPath' );
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

    }

}