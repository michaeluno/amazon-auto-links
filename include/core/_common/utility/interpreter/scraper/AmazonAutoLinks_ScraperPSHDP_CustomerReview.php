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
 * @since        3
 */
class AmazonAutoLinks_ScraperPSHDP_CustomerReview extends AmazonAutoLinks_ScraperPSHDP_Base {
        
    /**
     * @return      integer|null     10 multiplied rating. The maximum is 50. e.g. 45. This is optimized for database by removing the decimal point.
     */
    public function getRating() {
        
        $_oNode_img      = $this->oSimpleDOM->find( 'span[class=crAvgStars] img', 0 );
        if ( ! $_oNode_img ) {
            return null;
        }
        $_sAlt           = $_oNode_img->alt;
        $_sRatingMessage = $_sAlt
            ? $_sAlt
            : $_oNode_img->title;
        preg_match(
            '/\d\.\d/', // needle
            $_sRatingMessage,   // subject 
            $_aMatches
        );
        return is_numeric( $_aMatches[ 0 ] )
            ? ( integer ) ( $_aMatches[ 0 ] * 10 )
            : null;
        
    }
    /**
     * 
     * @return      string
     */
    public function getRatingImageURL() {
        $_oNode_img = $this->oSimpleDOM->find( 'span[class=crAvgStars] img', 0 );        
        return $_oNode_img
            ? $_oNode_img->src
            : '';
    }
    /**
     * 
     * @return      string
     */
    public function getRatingHTML() {
        
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
     * Returns a number of customer reviews.
     * @return      integer
     */
    public function getNumberOfReviews() {
        $_oNode_asinReviewsSummary = $this->oSimpleDOM->find( 'span[class=crAvgStars]', 0 );
        return $_oNode_asinReviewsSummary
            ? ( integer ) preg_replace(
                '/[^\d]/', // needle
                '', // replacement
                $_oNode_asinReviewsSummary->plaintext   // subject
            )
            : null;
    }
    
    /**
     * 
     * @return      string
     */
    public function getCustomerReviews() {
        $_oReviewTable = $this->oSimpleDOM->find( 'table[class=crIFrameReviewList]', 0 );
        return $_oReviewTable   
            ? $_oReviewTable->outertext
            : '';
    }    
} 