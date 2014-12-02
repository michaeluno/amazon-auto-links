<?php
class AmazonAutoLinks_Unit_Tag_ extends AmazonAutoLinks_Unit_Category_ {
    
    /**
     * Fixes the 'newly tagged...' and 'tagged "..." x times' insertion. 
     * 
     * @since            2.0.3.5b
     */
    protected function sanitizeTitle( $sTitle ) {
        
        $sTitle = parent::sanitizeTitle( $sTitle );
        $aPatterns = array(
            '/\s(newly|recently)\stagged.+$/',
            '/\stagged\s.+times$/',
        );
        $sTitle = preg_replace( $aPatterns, '', $sTitle );
        return $sTitle;
        
    }
    
    /**
     * @param            array            The argument array/
     * @param            null            This is only to be compatible with the parent class method.
     */
    protected function getRSSURLsFromArguments( $arrArgs, $_deprecated=null ) {
        
        $arrRSSURLs = array();
        $strScheme = $this->fIsSSL ? 'https' : 'http';
        $arrTags = AmazonAutoLinks_Utilities::convertStringToArray( $arrArgs['tags'], "," );
        
        // If the customer ID is provided, compose the URL for it first.
        if ( $arrArgs['customer_id'] ) {
            
            if ( ! $arrArgs['tags'] || empty( $arrTags ) ) {
                $arrRSSURLs[] = "{$strScheme}://www.amazon.com/rss/people/{$arrArgs['customer_id']}/products?tag={$arrArgs['associate_id']}";
                return $arrRSSURLs;
            }
            
            foreach( $arrTags as $strTag )     {    
            
                $strTag = strtolower( $strTag );
                $arrRSSURLs[] = "{$strScheme}://www.amazon.com/rss/people/{$arrArgs['customer_id']}/products/{$strTag}?tag={$arrArgs['associate_id']}&threshold={$arrArgs['threshold']}";
                
            }
            return $arrRSSURLs;
        }
        
        // So there is a tag set by the user.
        foreach( $arrTags as $strTag ) {
            
            $strTag = strtolower( $strTag );
            
            foreach( $arrArgs['feed_type'] as $strType => $fEnable ) {
                
                if ( ! $fEnable ) continue;
                
                // $strType : new, popular, or recent
                $arrRSSURLs[] = "{$strScheme}://www.amazon.com/rss/tag/{$strTag}/{$strType}?tag={$arrArgs['associate_id']}&threshold={$arrArgs['threshold']}";
                
            }
                    
        }
// AmazonAutoLinks_Debug::logArray( $arrRSSURLs );
        return $arrRSSURLs;
        
    }
    
    protected function formatRSSURLs( $arrRSSURLs ) { return $arrRSSURLs; }
    
    protected function getDescription( $oNode )  {

        $oNode = apply_filters( 'aal_filter_description_node', $oNode, $this );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssAllTags' );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssProductTag' );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssReviews' );
        return parent::getDescription( $oNode );
    
        // class="tgRssProductTag"
        // class="tgRssAllTags"
        // $nodeSpanTags = $oNode->getElementsByTagName( 'span' );
        // Iterate dom from backwards since dom is a live object
        // for ( $i = $nodeSpanTags->length - 1; $i >= 0; $i-- ) {
            // $nodeSpan = $nodeSpanTags->item( $i );
            // $strClassAttr = $nodeSpan->getAttribute( 'class' );
            // if ( stripos( $strClassAttr, 'tgRssAllTags' ) !== false || stripos( $strClassAttr, 'tgRssProductTag' ) !== false ) { 
                // $nodeSpan->parentNode->removeChild( $nodeSpan );
            // }

        // }
        
    }    
    
}