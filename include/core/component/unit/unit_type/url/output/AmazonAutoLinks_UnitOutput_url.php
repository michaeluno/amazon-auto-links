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
 * Creates Amazon product links by urls.
 */
class AmazonAutoLinks_UnitOutput_url extends AmazonAutoLinks_UnitOutput_item_lookup {
    
    /**
     * Stores the unit type.
     * @remark Note that the base constructor will create a unit option object based on this value.
     * @var    string
     */    
    public $sUnitType = 'url';

    /**
     * @return string
     * @since  5.0.0
     */
    public function get() {

        $_aURLs  = $this->getAsArray( $this->oUnitOption->get( 'urls' ) );

        /**
         * Get ASINs by from the HTML body from the database table.
         *
         * This fetches the HTML source code externally if the data does not exist.
         */
        $_oASINGetter = new AmazonAutoLinks_Unit_UnitType_URL_Output_ASINsFromHTMLs( $_aURLs, $this->oUnitOption->get( 'cache_duration' ) );
        $_aFoundASINs = $_oASINGetter->get();

        $this->___setPostMeta( $this->oUnitOption->get( 'id' ), $_aFoundASINs );

        return $this->___getOutputByAdWidgetSearch( $_aFoundASINs );

    }
        /**
         * If the id is set, save the found items so that the user can view what's found in the unit editing page.
         * @param integer $iPostID
         * @param array   $aFoundASINs
         * @since 5.0.0
         */
        private function ___setPostMeta( $iPostID, $aFoundASINs ) {

            if ( ! $iPostID ) {
                return;
            }
            $_bNoProducts     = empty( $aFoundASINs );
            $_sNoItemsMessage = __( 'Product not found.', 'amazon-auto-links' );
            $_sFoundItems     = get_post_meta( $iPostID, '_found_items', true );

            if ( $_bNoProducts ) {
                if ( $_sFoundItems === $_sNoItemsMessage ) {
                    return; // already set
                }
                update_post_meta( $iPostID, '_found_items', __( 'Product not found.', 'amazon-auto-links' ) );
                return;
            }
            $_aStoredASINs = explode( PHP_EOL, $_sFoundItems );
            sort( $_aStoredASINs ); // sort to compare
            $_aCompare = $aFoundASINs;
            sort( $_aCompare );     // sort to compare
            if ( $_aStoredASINs === $_aCompare ) {
                return;
            }
            update_post_meta( $iPostID, '_found_items', implode( PHP_EOL, $aFoundASINs ) );

        }

        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByAdWidgetSearch( array $aASINs ) {
            $_aUnitOptions = $this->oUnitOption->get();
            $_aUnitOptions[ 'sort' ] = $this->oUnitOption->get( array( '_sort' ), 'raw' );  // 5.3.5 the `ad_widget_search` type stores the sort value in the `sort` argument.
            $_aUnitOptions[ 'asin' ] = implode( ',', $aASINs );
            $_oUnitOutput  = new AmazonAutoLinks_UnitOutput_ad_widget_search( $_aUnitOptions );
            return $_oUnitOutput->get();
        }

}