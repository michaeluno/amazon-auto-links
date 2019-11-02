<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 * 
 */

/**
 * Provides methods to extracts customer reviews.
 *
 * @since      3.9.0
 * @deprecated       3.9.1      Use `AmazonAutoLinks_ScraperDOM_CustomerReview2` instead.
 */
class AmazonAutoLinks_ScraperPSHDP_CustomerReview2 extends AmazonAutoLinks_ScraperPSHDP_CustomerReview {
        
    /**
     * @return      integer     10 multiplied rating. The maximum is 50. e.g. 45. This is optimized for database by removing the decimal point.
     */
    public function getRating() {

        $_oNode_i      = $this->oSimpleDOM->find( 'div[class=AverageCustomerReviews] i[class=averageStarRating]', 0 );
        if ( ! $_oNode_i ) {
            return 0;
        }
        return AmazonAutoLinks_Unit_Utility::getRatingExtracted( $_oNode_i->innertext );
//        $_sRatingMessage = $_oNode_i->innertext;
//        preg_match(
//            '/\d\.\d/', // needle
//            $_sRatingMessage,   // subject
//            $_aMatches
//        );
//        return is_numeric( $_aMatches[ 0 ] )
//            ? ( integer ) ( $_aMatches[ 0 ] * 10 )
//            : null;
//
    }
    /**
     * 
     * @return      string
     */
    public function getRatingImageURL( /* $dRating */ ) {
        $_aParams = func_get_args();
        if ( ! isset( $_aParams[ 0 ] ) ) {
            return '';
        }
        return AmazonAutoLinks_Unit_Utility::getRatingStarImageURL( $_aParams[ 0 ] );

    }

    /**
     * Returns a number of customer reviews.
     * @return      integer|null
     */
    public function getNumberOfReviews() {
        $_oNode_NumberOfReviews = $this->oSimpleDOM->find( 'div[class=averageStarRatingNumerical]', 0 );
        return $_oNode_NumberOfReviews
            ? ( integer ) preg_replace(
                '/[^\d]/', // needle
                '', // replacement
                $_oNode_NumberOfReviews->plaintext   // subject
            )
            : null;
    }

    /**
     * 
     * @return      string
     * @deprecated  3.9.0
     */
    public function getRatingHTML( /* $iReviewCount */ ) {
        return '';
        // Remove characters. `***** (1818 customer reviews)` -> `****** (1818)`
        $_oNode = $this->oSimpleDOM->find( 'span[class=crAvgStars] a', 1 );
        if ( ! $_oNode ) {
            return '';
        }
        $_oNode->innertext = preg_replace(
            '/[^\d\(\)]/', // needle
            '', // replacement
            $_oNode->innertext   // subject
        );        
        
        $_oNode = $this->oSimpleDOM->find( 'div[class=crIFrameNumCustReviews]', 0 );
        return $_oNode
            ? $_oNode->outertext
            : '';
            
    }

    /**
     * 
     * @return      string
     */
    public function getCustomerReviews() {
        $_oReviewTable = $this->oSimpleDOM->find( 'div[class=review-views]', 0 );
        return $_oReviewTable   
            ? $_oReviewTable->outertext
            : '';
    }    
} 