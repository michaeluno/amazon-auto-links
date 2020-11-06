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
 * Handles counting PA-API requests.
 *
 * @since   4.4.0
 */
class AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter extends AmazonAutoLinks_VersatileFileManager {

    /**
     * Sets up properties.
     *
     * @param string  $sLocale  The locale code as an identifier.
     * @param integer $iTimeout 977616000 = 86400 * 365
     * @param string  $sFileNamePrefix
     * @since 4.4.0
     */
    public function __construct( $sLocale, $iTimeout=977616000, $sFileNamePrefix='' ) {
        $sFileNamePrefix = $sFileNamePrefix
            ? $sFileNamePrefix
            : AmazonAutoLinks_Registry::TRANSIENT_PREFIX . "_COUNT_PAAPI_REQUEST_{$sLocale}_";
        parent::__construct( $sLocale, $iTimeout, $sFileNamePrefix );
    }

    /**
     * @return string
     * @since  4.4.0
     */
    public function getDirectoryPath() {
        return AmazonAutoLinks_Registry::getPluginSiteTempDirPath() . '/paapi_request_count/' . $this->_sIdentifier; // storing the locale code
    }

    /**
     * Increments the counter.
     * @since  4.4.0
     */
    public function increment() {
        $this->_sFilePath = $this->getFilePath( time() );    // update the file path as it changes on the called time.
        $_bsCount         = $this->get();
        if ( false == $_bsCount ) { // if the file is locked by the system,
            // @todo Implement a fallback.
            return;
        }
        $this->set( ( ( integer ) $_bsCount ) + 1 );
        $this->___scheduleSaveIntoDatabase();
    }
        static private $___aLoaded = array();
        private function ___scheduleSaveIntoDatabase() {
            if ( isset( self::$___aLoaded[ $this->_sIdentifier ] ) ) {
                return;
            }
            self::$___aLoaded[ $this->_sIdentifier ] = true;
            AmazonAutoLinks_WPUtility::scheduleSingleWPCronTask(
                'aal_action_paapi_request_counter_save_log',
                array( $this->_sIdentifier ), // locale
                time() + 86400
            );
        }
    /**
     * @param  integer $iTime
     * @return string  The file path generated based on the given/current time.
     * @since  4.4.0
     */
    public function getFilePath( $iTime=0 ) {
        $iTime = $iTime ? $iTime : time();
        return $this->getDirectoryPath() . '/' . date( 'Y/m/d/H', $iTime ) . '.txt';
    }
}