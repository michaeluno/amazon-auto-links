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
 * Provides methods to output of contextual units.
 *
 * @since 3.5.0
 * @since 5.0.0 Extends a unit output class like other unit type output classes.
 */
class AmazonAutoLinks_UnitOutput_contextual extends AmazonAutoLinks_UnitOutput_ad_widget_search {

    /**
     * Stores the unit type.
     * @since  5.0.0
     */
    public $sUnitType = 'contextual';

    /**
     * @return string
     */
    public function get() {

        // this is a widget title field value and causes a conflict with the PA-API payload argument.
        $this->oUnitOption->delete( 'title' );
        $this->oUnitOption->set( 'Keywords', implode( ',', $this->___getKeywords() ) );

        // 3.1.4+   By default the given comma-delimited multiple keywords such as `PHP,WordPress` are searched in one query.
        // The Amazon API does not provide an OR operator for the Keywords parameter. The `Power` argument cannot be used for categories other than Books.
        // So here we set a plugin specific option to perform search by each keyword.
        $this->oUnitOption->set( 'search_per_keyword', true );

        return $this->___getOutputByAdWidgetSearch();

    }
        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByAdWidgetSearch() {
            $_aUnitOptions = $this->oUnitOption->get();
            $_oUnitOutput  = new AmazonAutoLinks_UnitOutput_ad_widget_search( $_aUnitOptions );
            return $_oUnitOutput->get();
        }

        /**
         * @return array
         * @since  5.0.0
         */
        private function ___getKeywords() {

            $_oContextualSearch = new AmazonAutoLinks_ContextualUnit_SearchKeyword( $this->oUnitOption );
            $_aSearchKeywords   = $_oContextualSearch->get(); // get as an array

            if ( $this->oUnitOption->get( 'concatenate_keywords' ) ) {
                $_aSearchKeywords = array( implode( ' ', $_aSearchKeywords ) );
            }

            // @todo make these optional
            shuffle( $_aSearchKeywords );
            array_splice( $_aSearchKeywords, 5 );   // up to 5 keywords.

            // 3.6.0+ This allows third parties to modify search keywords for cases that the keyword gets too long for long product title.
            // @see https://wordpress.org/support/topic/problem-with-title-width/
            $_aSearchKeywords = apply_filters( 'aal_filter_contextual_keywords', $_aSearchKeywords );
            return $this->getAsArray( $_aSearchKeywords );

        }

}