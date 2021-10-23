<?php

/**
 * Extracts ASINs from the web pages of the given URLs.
 * @since 5.0.0
 */
class AmazonAutoLinks_Unit_UnitType_URL_Output_ASINsFromHTMLs extends AmazonAutoLinks_UnitOutput_Utility {

    public $aURLs = array();

    public $iCacheDuration = 86400;

    public $sUnitType = '';

    /**
     * Sets up properties and hooks.
     * @since 5.0.0
     */
    public function __construct( array $aURLs, $iCacheDuration=86400, $sUnitType='url' ) {
        $this->aURLs          = $aURLs;
        $this->iCacheDuration = $iCacheDuration;
        $this->sUnitType      = $sUnitType;
    }

    /**
     * @return array
     * @since  5.0.0
     */
    public function get() {
        return $this->getASINsFromHTMLs( $this->___getHTMLBodies( $this->aURLs ) );
    }
        /**
         *
         * @param  array $aURLs
         * @since  ?
         * @since  3.8.1 Changed the visibility scope to protected from private as category unit accesses this method.
         * @since  5.0.0 Moved from `AmazonAutoLinks_UnitOutput_url`. Changed the visibility scope to private as no other classes use it.
         * @return array
         */
        private function ___getHTMLBodies( array $aURLs ) {

            $_aHTMLBodies    = array();
            $_iCacheDuration = $this->iCacheDuration;
            foreach( $aURLs as $_sURL ) {
                $_oHTTP    = new AmazonAutoLinks_HTTPClient(
                    $_sURL,
                    $_iCacheDuration,
                    array(  // http arguments
                        'timeout'     => 20,
                        'redirection' => 20,
                    ),
                    $this->sUnitType . '_unit_type' // request type
                );
                $_aHTMLBodies[ $_sURL ] = $_oHTTP->getBody();
            }
            return $_aHTMLBodies;

        }

}