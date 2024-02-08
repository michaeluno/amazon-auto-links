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
 * Handles outputs of ad-widget search units.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_UnitOutput_ad_widget_search extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     * @since  5.0.0
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * @var   integer Stores the last modified time of the fetched API response.
     * @since 5.1.4
     */
    public $iLastModified;

    /**
     * @return string
     * @since  5.0.0
     */
    public function get() {
        if ( $this->_shouldUsePAAPI() ) {
            return $this->___getOutputByPAAPI();
        }
        return parent::get();
    }
        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByPAAPI() {
            $_sUnitType   = $this->___getPAAPIUnitTypeFromArguments( $this->oUnitOption->aRawOptions, $this->oUnitOption->aUnitOptions );
            $_sClass      = "AmazonAutoLinks_UnitOutput_" . $_sUnitType;
            $_oUnitOutput = new $_sClass( $this->oUnitOption->aRawOptions );
            return $_oUnitOutput->get();
        }
            /**
             * @param  array  $aRawArguments
             * @return string
             * @sicne  5.0.0
             */
            private function ___getPAAPIUnitTypeFromArguments( array $aRawArguments, array $aUnitOptions ) {
                if ( isset( $aRawArguments[ 'asin' ] ) ) { // from shortcode, this argument is set
                    return 'item_lookup';
                }
                if ( $this->___isKeywordAllASINs( $aUnitOptions[ 'Keywords' ] ) ) {
                    return 'item_lookup';
                }
                return 'search';
            }
                /**
                 * @param  string|array $asKeywords
                 * @return boolean
                 * @sicne  5.3.8
                 */
                private function ___isKeywordAllASINs( $asKeywords ) {
                    foreach( $this->getStringIntoArray( $asKeywords, ',' ) as $_sKeyword ) {
                        if ( ! $this->isASINOrISBN( $_sKeyword ) ) {
                            return false;
                        }
                    }
                    return true;
                }

}