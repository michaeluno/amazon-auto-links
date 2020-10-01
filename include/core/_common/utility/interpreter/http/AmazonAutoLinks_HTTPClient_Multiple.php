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
 * Serves as an HTTP client.
 *
 * Accepts multiple URLs to be passed.
 * It saves database queries by retrieving caches at once.
 *
 * @sicne 4.3.3
 */
class AmazonAutoLinks_HTTPClient_Multiple extends AmazonAutoLinks_HTTPClient {

    public $aURLs;

    /**
     * @var AmazonAutoLinks_HTTPClient[]
     */
    public $aHTTPs = array();


    public $aCacheNames = array();
    /**
     * @var array
     */
    public $aCaches = array();

    /**
     * Sets up properties.
     *
     * @param array $aURLs Each element must be unique.
     * @param int $iCacheDuration
     * @param array $aArguments
     * @param string $sRequestType
     */
    public function __construct( array $aURLs, $iCacheDuration=86400, array $aArguments=array(), $sRequestType='wp_remote_get' ) {

        $this->aArguments     = $this->_getArgumentsFormatted( $aArguments, $aURLs );
        $this->sRequestType   = $sRequestType;

        foreach( array_unique( $aURLs ) as $_sURL ) {
            $_sCacheName = $this->_getCacheName( $_sURL, $this->aArguments, $this->sRequestType );
            $this->aURLs[ $_sCacheName ] = $_sURL;
        }
        $this->iCacheDuration = $iCacheDuration;

    }

    /**
     * Deletes caches.
     */
    public function deleteCaches() {
        $_oCacheTable = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
        $_oCacheTable->deleteCache( array_keys( $this->aURLs ) );
    }
    /**
     * An alias of `deleteCaches()`.
     */
    public function deleteCache() {
        $this->deleteCaches();
    }

    /**
     * @return array
     */
    public function get() {
        $this->___setRequestProperties();
        $_aResponses = array();
        foreach( $this->aHTTPs as $_sURL => $_oHTTP ) {
            $_aResponses[ $_sURL ] = $_oHTTP->get();
        }
        return $_aResponses;
    }

    /**
     * @return array
     */
    public function getRaw() {
        $this->___setRequestProperties();
        $_aResponses = array();
        foreach( $this->aHTTPs as $_sURL => $_oHTTP ) {
            $_aResponses[ $_sURL ] = $_oHTTP->getRaw();
        }
        return $_aResponses;
    }

        private function ___setRequestProperties() {
            static $_bCalled = false;
            if ( $_bCalled ) {
                return;
            }
            $_bCalled = true;
            $this->___setCaches();
            $this->___setHTTPs();
        }
            /**
             * Retrieve caches all at once and set them in a property.
             * @retmark must be called before ___setHTTPs().
             */
            private function ___setCaches() {
                $_oCacheTable  = new AmazonAutoLinks_DatabaseTable_aal_request_cache;
                $this->aCaches = $_oCacheTable->getCache( array_keys( $this->aURLs ), $this->iCacheDuration );
            }

            /**
             * @remark  must be called after ___setCaches()
             */
            private function ___setHTTPs() {
                foreach( $this->aURLs as $_sCacheName => $_sURL ) {
                    $_sURL          = trim( $_sURL );
                    $_aCache        = $this->getElementAsArray( $this->aCaches, array( $_sCacheName ) );
                    $_aCache        = empty( $_aCache[ 'data' ] ) ? array() : $_aCache;
                    $this->aHTTPs[ $_sURL ] = new AmazonAutoLinks_HTTPClient( $_sURL, $this->iCacheDuration, $this->aArguments, $this->sRequestType, $_aCache );
                }
            }

    /**
     * @param array $aArguments
     * @param array|string $asURLs
     * @return array|bool[]
     */
    protected function _getArgumentsFormatted( array $aArguments, $asURLs ) {
        return parent::_getArgumentsFormatted( $aArguments, $asURLs ) + array(
            'skip_argument_format' => true,
        );
    }

}