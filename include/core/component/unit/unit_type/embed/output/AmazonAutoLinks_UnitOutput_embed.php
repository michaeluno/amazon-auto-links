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
 * Generates Amazon product links for oEmbed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_embed extends AmazonAutoLinks_UnitOutput_category3 {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'embed';

    /**
     * Lists the variables used in the Item Format unit option that require to access the custom database.
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%image_set%', '%similar%', '%feature%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%content%',
        '%description%',
    );

    /**
     * @var array
     */
    public static $aStructure_Product = array(
    );

    public function get( $aURLs=array(), $sTemplatePath='' ) {
        return parent::get( $aURLs=, $sTemplatePath );
    }

    /**
     * Fetches product data and returns the associative array containing the output of product links.
     *
     * @param array $aURLs
     * @return            array            An array contains products.
     */
    public function fetch( $aURLs=array() ) {

        $_aURLs     = array_merge( $aURLs, array( $this->oUnitOption->get( 'uri' ) ) );
        $_aProducts = array();
        $_iCount    = ( integer ) $this->oUnitOption->get( 'count' );
        $_sURL      = '';
        foreach( $_aURLs as $_iIndex => $_sURL ) {
            if ( $_iIndex > $_iCount ) {
                break;
            }
            $_aProducts[] = $this->___getProduct( $_sURL );
        }
        $_sASIN   = $this->getASINFromURL( $_sURL );
        $_sLocale = $this->___getLocaleFromURL( $_sURL );
        $_sAssociateID = $this->___getAssociateIDFromURL( $_sURL );
        return $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );

    }
        private function ___getProduct( $sURL ) {

            $_sAssociateID = $this->___getAssociateIDFromURL( $sURL );
            add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 4 );

            $_oScraper = new AmazonAutoLinks_ScraperDOM_Product( $sURL );
            $_sDomain  = parse_url( $sURL, PHP_URL_SCHEME ) . '://' . parse_url( $sURL, PHP_URL_HOST );
            $_aProduct = $_oScraper->get( $_sAssociateID, $_sDomain );

            remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );
            $_aProduct[ 'modified_date' ] = $this->getElement( $this->___aModifiedDates, $sURL );
            return $_aProduct;

        }
            private $___aModifiedDates = array();
            /**
             * @callback    filter      aal_filter_http_response_cache
             * @return      array
             */
            public function replyToCaptureUpdatedDate( $aCache, $iCacheDuration, $aArguments, $sRequestType ) {

                $this->___aModifiedDates[ $aCache[ 'request_uri' ]  ] = $aCache[ '_modified_timestamp' ];
                return $aCache;

            }

            /**
             * @param string $sURL
             *
             * @return  string
             */
            private function ___getAssociateIDFromURL( $sURL ) {
                $_sQuery = parse_url( $sURL, PHP_URL_QUERY );
                parse_str( $_sQuery, $_aQuery );
                return isset( $_aQuery[ 'tag' ] )
                    ? $_aQuery[ 'tag' ]
                    : ( string ) $this->oUnitOption->get( 'associate_id' );
            }
            /**
             * @param $sURL
             *
             * @return string
             */
            private function ___getLocaleFromURL( $sURL ) {
                $_sDomain        = parse_url( $sURL, PHP_URL_HOST );
                $_bisKey         = array_search( $_sDomain, AmazonAutoLinks_Property::$aStoreDomains );
                $_sDefaultLocale = ( string ) $this->oUnitOption->get( array( 'country' ), 'US' );
                return false === $_bisKey
                    ? $_sDefaultLocale
                    : $_bisKey;
            }

}