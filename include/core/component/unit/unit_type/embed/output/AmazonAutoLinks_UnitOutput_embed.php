<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Generates Amazon product links for oEmbed outputs.
 *
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitOutput_embed extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'embed';

    /**
     * Lists the tags (variables) used in the Item Format unit option that require to access the custom database.
     * @var array
     */
    protected $_aItemFormatDatabaseVariables = array(
        '%review%', '%similar%', '%category%', '%rank%', '%prime%',
        '%_discount_rate%', '%_review_rate%', // 3.9.2  - used for advanced filters
        '%discount%'                          // 4.7.8
    );

    /**
     * Indicates whether the given URLs contain a non-product URL.
     * @var   boolean
     * @since 4.4.0
     * @since 5.0.0   Changed the visibility scope to public from private as accessed from the product fetcher class.
     */
    public $bNonProductURL = false;

    /**
     * @return string
     * @since  5.0.0
     */
    public function get() {

        // As of v5.0.0, multiple URLs are not supported. Only accepts a single URL.
        $_sURL    = trim( ( string ) $this->oUnitOption->get( 'uri' ) );
        $this->oUnitOption->set( 'country', AmazonAutoLinks_Locales::getLocaleFromURL( $_sURL, ( string ) $this->oUnitOption->get( array( 'country' ), 'US' ) ) );
        $_aASINs  = $this->getASINs( $_sURL );
        if ( empty( $_aASINs ) ) {
            $this->oUnitOption->set( 'urls', array( $_sURL ) );
            return $this->___getOutputByURLUnitType( $this->oUnitOption->get() );
        }
        $this->oUnitOption->set( 'asin', $_aASINs );
        return $this->___getOutputByAdWidgetSearchUnitType( $this->oUnitOption->get() );

    }
        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByURLUnitType( $aUnitOptions ) {
            $_oUnitOutput = new AmazonAutoLinks_UnitOutput_url( $aUnitOptions );
            return $_oUnitOutput->get();
        }
        private function ___getOutputByAdWidgetSearchUnitType( $aUnitOptions ) {
            $_oUnitOutput = new AmazonAutoLinks_UnitOutput_ad_widget_search( $aUnitOptions );
            return $_oUnitOutput->get();
        }

    /**
     * Overrides parent method to return errors specific to this embed unit type.
     * @param  array  $aProducts
     * @return string
     * @since  4.2.2
     * @deprecated 5.0.0
     */
    protected function _getError( array $aProducts ) {

        if ( $this->bNonProductURL && ! empty( $this->aErrors ) ) {
            $_sErrors = implode( ' ', $this->aErrors );
            return $_sErrors . ' ' . $this->getEnableHTTPProxyOptionMessage();
        }

        /**
         * For users not using PA-API keys, errors should be displayed.
         * Otherwise, even failing to access to the store page, API requests can be preformed with ASIN.
         */
        $_sLocale       = $this->oUnitOption->get( 'country' );
        if ( ! $this->oOption->isPAAPIKeySet( $_sLocale ) ) {
            if ( ! empty( $this->aErrors ) ) {
                $this->aErrors = array_map( array( $this, '___replyToSetASINToErrorMessage' ), $this->aErrors, array_keys( $this->aErrors ), array_fill(0, count( $this->aErrors ), $_sLocale ) );
                $_sErrors = implode( ' ', $this->aErrors );
                return $_sErrors  . ' ' . $this->getEnableHTTPProxyOptionMessage();
            }
            $aProducts = $this->___getProductsFilteredForWithoutPAAPI( $aProducts );
        }

        $_sErrorMessage = parent::_getError( $aProducts );
        return $_sErrorMessage
            ? $_sErrorMessage . ' ' . $this->getEnableHTTPProxyOptionMessage()
            : $_sErrorMessage;

    }
        /**
         * @param    string $sErrorMessage
         * @param    string $sURL
         * @param    string $sLocale
         * @return   string
         * @callback array_map()
         * @since    4.4.6
         */
        private function ___replyToSetASINToErrorMessage( $sErrorMessage, $sURL, $sLocale ) {
            $_aASINs = $this->getASINs( $sURL );
            if ( empty( $_aASINs ) ) {
                return $sErrorMessage;
            }
            $_sASINs  = implode( ', ', $_aASINs );
            return $sErrorMessage . " ({$sLocale}: {$_sASINs})";
        }
        /**
         * Filters out unfinished product data.
         *
         * For some reasons, like cloudflare cache errors, the Amazon product page responds with the 200 status but shows an error.
         * In that case, products data become unfinished. So remove those.
         * @param  array $aProducts
         * @return array
         * @since  4.4.5
         */
        private function ___getProductsFilteredForWithoutPAAPI( $aProducts ) {
            foreach( $aProducts as $_iIndex => $_aProduct ) {
                if ( ! $this->getElement( $_aProduct, 'thumbnail_url' ) && ! isset( $aProducts[ 'title' ] ) ) {
                    unset( $aProducts[ $_iIndex ] );
                }
            }
            return $aProducts;
        }

}