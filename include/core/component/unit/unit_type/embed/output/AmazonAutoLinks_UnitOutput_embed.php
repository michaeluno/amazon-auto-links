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
        '%review%', '%similar%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
    );

    /**
     * Represents the structure of each product array.
     * @var array
     */
    public static $aStructure_Product = array();

    /**
     * Fetches product data and returns the associative array containing the output of product links.
     *
     * @param array $aURLs
     * @return            array            An array contains products.
     */
    public function fetch( $aURLs=array() ) {

        $_aURLs     = preg_split( "/\s+/", trim( ( string ) $this->oUnitOption->get( 'uri' ) ), 0, PREG_SPLIT_NO_EMPTY );
        $_aURLs     = array_merge( $aURLs, $_aURLs );
        $_aProducts = array();
        $_iCount    = ( integer ) $this->oUnitOption->get( 'count' );
        $_sURL      = '';
        foreach( $_aURLs as $_iIndex => $_sURL ) {
            if ( $_iIndex > $_iCount ) {
                break;
            }
            // multiple ASINs can be embedded in a single URL like https://amazon.com/dp/ABCDEFGHIJ?tag=my-associate-21,ABCDEFGHI2,ABCDEFGHI3
            $_aASINs       = $this->getASINs( $_sURL );
            $_sAssociateID = $this->___getAssociateIDFromURL( $_sURL );
            foreach( $_aASINs as $_sASIN ) {
                $_aProducts[] = $this->___getProduct( $_sURL, $_sASIN, $_sAssociateID );
            }

        }
        $_sLocale      = $this->___getLocaleFromURL( $_sURL );
        $_sAssociateID = $this->___getAssociateIDFromURL( $_sURL );
        $this->oUnitOption->set( 'associate_id', $_sAssociateID ); // some elements are formatted based on this value
        $this->oUnitOption->set( 'country', $_sLocale ); // when no image is found, the alternative thumbnail is based on the default locale
        return $this->_getProducts( $_aProducts, $_sLocale, $_sAssociateID, $_iCount );

    }
        private function ___getProduct( $sURL, $sASIN, $sAssociateID ) {

            $_sDomain      = parse_url( $sURL, PHP_URL_SCHEME ) . '://' . parse_url( $sURL, PHP_URL_HOST );
            $_sURL         = add_query_arg(
                array(
                    'tag' => $sAssociateID,
                ),
                $_sDomain . '/dp/' . $sASIN . '/'
            );

            add_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10, 4 );

            $_oScraper = new AmazonAutoLinks_ScraperDOM_Product( $_sURL );
            $_aProduct = $_oScraper->get( $sAssociateID, $_sDomain );
            remove_filter( 'aal_filter_http_response_cache', array( $this, 'replyToCaptureUpdatedDate' ), 10 );

            if ( empty( $_aProduct ) ) {
                return $_aProduct;
            }

            $_aProduct[ 'updated_date' ] = $this->getElement( $this->_aModifiedDates, $_sURL );
            $_sDescriptionExtracted      = $this->_getDescriptionSanitized(
                isset( $_aProduct[ 'description' ] ) ? $_aProduct[ 'description' ] : implode( ' ', $_aProduct[ '_features' ] ),
                $this->oUnitOption->get( 'description_length' ),
                $this->_getReadMoreText( $_aProduct[ 'product_url' ] )
            );
            $_aProduct[ 'description' ]  = $_sDescriptionExtracted
                ? "<div class='amazon-product-description'>"
                    . $_sDescriptionExtracted
                . "</div>"
                : '';
            $_aProduct[ 'content' ]      = ! empty( $_aProduct[ 'feature' ] )
                ? "<div class='amazon-product-content'>"
                    . $_aProduct[ 'feature' ]
                . "</div>"
                : '';

            unset( $_aProduct[ '_features' ] );
            return $_aProduct;

        }
            /**
             * @param string $sURL
             *
             * @return  string
             */
            private function ___getAssociateIDFromURL( $sURL ) {

                $_bOverrideAssociatesIDOfURL = ( boolean ) $this->oOption->get( 'custom_oembed', 'override_associates_id_of_url' );
                if ( ! $_bOverrideAssociatesIDOfURL ) {
                    $_sQuery = parse_url( $sURL, PHP_URL_QUERY );
                    parse_str( $_sQuery, $_aQuery );
                    if ( isset( $_aQuery[ 'tag' ] ) ) {
                        return $_aQuery[ 'tag' ];
                    }
                }

                $_sHost   = parse_url( $sURL, PHP_URL_HOST ); // without https://
                $_sLocale = AmazonAutoLinks_Property::getLocaleByDomain( $_sHost );
                $_sSetAssociatesID = $this->oOption->get( 'custom_oembed', 'associates_ids', $_sLocale );
                return $_sSetAssociatesID
                    ? $_sSetAssociatesID
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