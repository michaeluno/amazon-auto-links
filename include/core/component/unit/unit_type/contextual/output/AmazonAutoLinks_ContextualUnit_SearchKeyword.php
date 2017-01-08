<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2017 Michael Uno
 */


/**
 * Provides methods to find search keywords from the current page.
 * 
 * @since           3
 * @since           3.5.0       Renamed from `AmazonAutoLinks_ContextualProductWidget_SearchKeyword`.
 */
class AmazonAutoLinks_ContextualUnit_SearchKeyword extends AmazonAutoLinks_PluginUtility {

    public $aCriteria           = array();
    public $sAdditionalKeywords = '';

    /**
     * Sets up properties.
     */
    public function __construct( array $aCriteria, $sAdditionalKeywords='' ) {
        
        $this->aCriteria           = $aCriteria;
        $this->sAdditionalKeywords = $sAdditionalKeywords;
        
    }
    
    /**
     * 
     * @return      string       The search keywords.
     */
    public function get( $bRetunType=true ) {
        
        $_aKeywords    = $this->_getSearchKeywordsByCriteria( $this->aCriteria );
        $_aKeywords    = array_merge(
            $_aKeywords,
            $this->_getSiteSearchKeywords()
        );
        $_aKeywords    = $this->_getFormattedSearchKeywordsArray( $_aKeywords );
        $_sAdditionals = $this->trimDelimitedElements( 
            trim( $this->sAdditionalKeywords ), // subject string
            ',',  // delimiter
            false // add a space with the delimiter
        );
        $_sAdditionals = $_sAdditionals
            ? ',' . $_sAdditionals
            : '';
        $_sKeywords    =   implode( ',', $_aKeywords )
            . $_sAdditionals;
            
        return $bRetunType
            ? explode( ',', $_sKeywords )
            : $_sKeywords;
            
    }
        
        /**
         * @return      array
         */
        private function _getSiteSearchKeywords() {
            
            $_sQuery = $this->getElement( $_GET, 's' );
            $_sQuery = str_replace(
                array( '+' ),
                ',',
                $_sQuery
            );
            return explode( ',', $_sQuery );
            
        }

        /**
         * 
         * @return      array
         */
        private function _getFormattedSearchKeywordsArray( array $aKeywords ) {
            $aKeywords = array_unique( array_filter( $aKeywords ) );
            return $aKeywords;
        }

        /**
         * 
         * @since       3
         * @param       array       $aCriteria      
         * The structure:
         *  array(
         *      'post_title'        => true,
         *      'breadcrumb'        => true,
         *      'taxonomy_terms'    => true,
         *  )
         * @return      string
         */
        private function _getSearchKeywordsByCriteria( array $aCriteria ) {

            $_aKeywords  = array();
            foreach( $this->_getFormattedCriteriaArray( $aCriteria ) as $_sCriteriaKey ) {                
                $_aKeywords = array_merge(
                    call_user_func(
                        array( $this, '_getSearchKeywordsByType_' . $_sCriteriaKey )
                    ),
                    $_aKeywords
                );
            }
            return $_aKeywords;
            
        }
            /**
             * 
             * @since       3
             * @return      array
             */
            private function _getFormattedCriteriaArray( array $aCriteria ) {                
                return array_keys(
                    array_filter( $aCriteria )
                );                    
            }
            /**
             * 
             * @since       3
             * @return      array
             */
            private function _getSearchKeywordsByType_post_title() {
                return isset( $GLOBALS[ 'post' ]->post_title )
                    ? array( $GLOBALS[ 'post' ]->post_title )
                    : array();
            }
            
            /**
             * 
             * @since       3
             * @return      array
             */
            private function _getSearchKeywordsByType_breadcrumb() {
                $_oBreadcrumb  = new AmazonAutoLinks_ContextualUnit_Breadcrumb;
                return $_oBreadcrumb->get();
            }  
 
            /**
             * 
             * @since       3
             * @return      array
             */
            private function _getSearchKeywordsByType_taxonomy_terms() {
                
                if ( ! isset( $GLOBALS[ 'post' ]->post_type, $GLOBALS[ 'post' ]->ID ) ) {
                    return array();
                }
                
                // Retrieve associated taxonomies.
                $_aTaxonomyObjects = get_object_taxonomies( 
                    $GLOBALS[ 'post' ]->post_type, 
                    'objects' 
                );
                
                // Generate current taxonomy terms.
                $_aTaxoomyNames = array();
                foreach( $_aTaxonomyObjects as $_sKey => $_oTaxonomy ) {
                    if ( ! $_oTaxonomy->public ) {
                        continue;
                    }                    
                    $_aTaxoomyNames[] = $_oTaxonomy->name;
                }
                $_aTermsObjects = wp_get_post_terms( 
                    $GLOBALS[ 'post' ]->ID, 
                    $_aTaxoomyNames
                );
                        
                        
                $_aTerms = array();
                foreach( $_aTermsObjects as $_oTerm ) {
                    if ( in_array( 'uncategorized', array( $_oTerm->slug ) ) ) {
                        continue;
                    }                    
                    $_aTerms[] = $_oTerm->name;
                }
                return $_aTerms;
                
            }             
    
}