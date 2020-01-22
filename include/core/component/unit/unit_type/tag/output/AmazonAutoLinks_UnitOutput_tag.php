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
 * Defines how the 'tag' unit type should be displayed.
 */
class AmazonAutoLinks_UnitOutput_tag extends AmazonAutoLinks_UnitOutput_category {
            
    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'tag';

    /**
     * Sets up properties.
     * @remark      The 'tag' unit type will override this method.
     */
    protected function _setProperties() {
 
        $this->_aRSSURLs          = $this->_getRSSURLsFromArguments(
            $this->oUnitOption->get() 
        );
    
    }
    
    /**
     * Sanitizes the raw title. 
     * 
     * This does not create a final result of the title as this method is called from sorting items as well.
     * 
     * @remark      Used for sorting as well.
     * @since       2.0.3.5b
     * @since       3
     * @return      string
     */
    public function replyToModifyRawTitle( $sTitle ) {
        
        $sTitle = parent::replyToModifyRawTitle( $sTitle );
        
        // Fixes the 'newly tagged...' and 'tagged "..." x times' insertion. 
        $_aPatterns  = array(
            '/\s(newly|recently)\stagged.+$/',
            '/\stagged\s.+times$/',
        );
        return preg_replace( 
            $_aPatterns, // needle
            '', // remove
            $sTitle  // subject haystack
        );     
            
    }    
    
    /**
     * @param            array           The argument array.
     */
    protected function _getRSSURLsFromArguments( array $aArguments ) {
        
        $aRSSURLs           = array();
        $sScheme            = $this->bIsSSL ? 'https' : 'http';
        $aTags              = $this->getStringIntoArray( $aArguments[ 'tags' ], "," );
        $_sDummyAssociateID = 'amazon-auto-links-20';
        
        // If the customer ID is provided, compose the URL for it first.
        if ( $aArguments[ 'customer_id' ] ) {
            
            if ( ! $aArguments[ 'tags' ] || empty( $aTags ) ) {
                // $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/people/{$aArguments[ 'customer_id' ]}/products?tag={$aArguments[ 'associate_id' ]}";
                $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/people/{$aArguments[ 'customer_id' ]}/products?tag={$_sDummyAssociateID}";
                return $aRSSURLs;
            }
            
            foreach( $aTags as $sTag ) {                
                $sTag = strtolower( $sTag );
                // $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/people/{$aArguments[ 'customer_id' ]}/products/{$sTag}?tag={$aArguments[ 'associate_id' ]}&threshold={$aArguments[ 'threshold' ]}";                
                $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/people/{$aArguments[ 'customer_id' ]}/products/{$sTag}?tag={$_sDummyAssociateID}&threshold={$aArguments[ 'threshold' ]}";                
            }
            return $aRSSURLs;
            
        }
        
        // So there is a tag set by the user.
        foreach( $aTags as $sTag ) {
            
            $sTag = strtolower( $sTag );
            foreach( $aArguments[ 'feed_type' ] as $sType => $fEnable ) {
                
                if ( ! $fEnable ) {
                    continue;
                }
                
                // $sType : new, popular, or recent
                // $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/tag/{$sTag}/{$sType}?tag={$aArguments[ 'associate_id' ]}&threshold={$aArguments[ 'threshold' ]}";
                $aRSSURLs[] = "{$sScheme}://www.amazon.com/rss/tag/{$sTag}/{$sType}?tag={$_sDummyAssociateID}&threshold={$aArguments[ 'threshold' ]}";
                
            }
                    
        }
        return $aRSSURLs;
        
    }
    
    protected function formatRSSURLs( $aRSSURLs ) { 
        return $this->getAsArray( $aRSSURLs ); 
    }
    
    protected function getDescription( $oNode )  {

        $oNode = apply_filters( 'aal_filter_description_node', $oNode, $this );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssAllTags' );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssProductTag' );
        $this->oDOM->removeNodeByTagAndClass( $oNode, 'span', 'tgRssReviews' );
        return parent::getDescription( $oNode );
        
    }    
    
}