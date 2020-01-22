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
 * Formats amazon product link urls.
 * 
 * @since       3
 */
class AmazonAutoLinks_UnitOutput__Format_content extends AmazonAutoLinks_WPUtility {

    public $aReviews    = array();
    
    public $oDOM;
    
    public $oUnitOption;
    
    /**
     * Sets up properties.
     * @since       3
     * @since       3.9.0       Gave a type hint for the $aReviews parameter.
     */
    public function __construct( array $aReviews, $oDOM, $oUnitOption ) {
        
        $this->aReviews    = $aReviews;
        $this->oDOM        = $oDOM;
        $this->oUnitOption = $oUnitOption;
        
    }
    
    /**
     * Returns a formatted content.
     * @return      string
     */
    public function get() {

        $_sContents = $this->___getJoinedElements( $this->aReviews, 'Content' );
        $_sContents = $this->___getContentsSanitized( $_sContents );
        return $_sContents;
        
    }
    
        /**
         * @return      string
         * @since       3.3.0
         */
        private function ___getContentsSanitized( $sContents ) {
            
            // DOM
            $_oDoc = $this->oDOM->loadDOMFromHTMLElement( $sContents );
            
            // Remove <br> tags
            $this->oDOM->removeTags( $_oDoc, array( 'br', ) );
            
            // 
            $this->_setHighestHeadingTags( $_oDoc );
        
            return $_oDoc->saveXML( $_oDoc->documentElement, LIBXML_NOEMPTYTAG );
        
                        
        }        
            /**
             * @since       3.3.0
             */
            private function _setHighestHeadingTags( $_oDoc ) {
                    
                $_iDesiredHighestHTag = ( integer ) $this->oUnitOption->get( 'highest_content_heading_tag_level' );
                
                $_iActuralHighestHTag = $this->_getHighestHTag( $_oDoc );
                if ( 0 === $_iActuralHighestHTag ) {
                    return;
                }
                if ( $_iDesiredHighestHTag === $_iActuralHighestHTag ) {
                    return;
                }
                
                // Offset - for example, if the user wants h3 to be the highest, the offset will be 2
                $_iOffset   = $_iDesiredHighestHTag - $_iActuralHighestHTag; 
                if ( 0 === $_iOffset ) {
                    return;
                }
                
                // Convert the H{n} tags
                
                /// First prepare the search and replace arrays.
                $_aSearches = $_aReplaces = array();
                for( $_i = 5; $_i >= max( 1, $_iActuralHighestHTag ); $_i-- ) {
                    $_aSearches[] = "h{$_i}";
                    $_aReplaces[] = "h" . max( 1, min( $_i + $_iOffset, 6 ) ); // up to h6
                }                
                if ( $_iOffset < 0 ) {
                    $_aSearches = array_reverse( $_aSearches );
                    $_aReplaces = array_reverse( $_aReplaces );
                }

                /// Rename tags - modify the DOM document object
                $_oHeaderRenamer = new AmazonAutoLinks_RenameTags;
                $_oHeaderRenamer->rename(
                    $_aSearches,
                    $_aReplaces,
                    $_oDoc
                );    
                              
            }    
            
                /**
                 * @return      integer         The found highest heading tag number. 0 of not found.
                 */
                private function _getHighestHTag( $oDoc ) {
                    
                    // Check 1 to 6
                    for( $_i = 1; $_i <= 6; $_i++ ) {
                        
                        $_sSearchTag = "h{$_i}";
                        
                        // getElementsByTagName() misses some elements.
                        // $_oHNTags = $oDoc->getElementsByTagName( $_sSearchTag );

                        $_oXpath     = new DOMXPath( $oDoc );
                        $_oHNTags    = $_oXpath->query( 
                            ".//{$_sSearchTag}" // "//*/{$_sSearchTag}" 
                        );                           
                        
                        if ( ! empty( $_oHNTags->length ) ) {
                            return $_i;
                        }
                    }                
                    return 0;
                    
                }
            
        /**
         * Joins the given value if it is an array with the provided key.
         * 
         * For example, the subject array structure can be either
         * `
         * array(
         *      'Content' => 'some contents...'
         * )
         * `
         * or
         * `
         * array(
         *      array(
         *          'Content' => 'some contents...',
         *      ),
         *      array(
         *          'Content' => 'some contents...',
         *      ),
         *      ...
         * )
         * `
         * @param       array   $aParentArray
         * @param       string  $sKey
         * @return      string
         */
        private function ___getJoinedElements( array $aParentArray, $sKey ) {
            
            if ( isset( $aParentArray[ $sKey ] ) ) { 
                return ( string ) $aParentArray[ $sKey ]; 
            }
            
            $_aElems = array();
            foreach( $aParentArray as $_vElem ) {
                if ( ! isset( $_vElem[ $sKey ] ) ) {
                    continue;
                }
                $_aElems[] = $_vElem[ $sKey ];
            }
                    
            return implode( '', $_aElems );        
            
        }                
        
}