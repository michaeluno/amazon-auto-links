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
    protected function _getDirectoryPath() {
        return AmazonAutoLinks_Registry::getPluginSiteTempDirPath() . '/paapi_request_count/' . $this->_sIdentifier; // storing the locale code
    }

    /**
     * Increments the counter.
     * @since  4.4.0
     */
    public function increment() {
        $this->_sFilePath = $this->___getFilePath();    // update the file path as it changes on the called time.
        $this->set( ( integer ) $this->get() + 1 );
    }
        /**
         * @since 4.4.0
         */
        private function ___getFilePath() {
            return $this->_getDirectoryPath() . '/' . date( 'Y/m/d/H' ) . '.txt';
        }
}