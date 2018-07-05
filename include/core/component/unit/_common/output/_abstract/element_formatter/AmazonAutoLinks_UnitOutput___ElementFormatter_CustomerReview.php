<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * A class that provides methods to format customer review outputs.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_CustomerReview extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {
        
        $_snEncodedHTML = $this->_getCell( 'customer_reviews' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving customer reviews.', 'amazon-auto-links' )
            );
        }
        return $this->___getFormattedOutput( $_snEncodedHTML );

    }

        /**
         * @since   3.5.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML ) {
            if ( ! $_snEncodedHTML ) {
                return '';
            }            
            $_oScraper  = new AmazonAutoLinks_ScraperDOM_CustomerReview_Each(
                $_snEncodedHTML,
                true,
                $this->_oUnitOption->get( 'customer_review_max_count' ),
                $this->_oUnitOption->get( 'customer_review_include_extra' )
            );
            return "<div class='amazon-customer-reviews'>" 
                    . $_oScraper->get()
                . "</div>";       
        }

}