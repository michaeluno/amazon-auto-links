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
 * Provides methods to find search keywords from the current page.
 * 
 * @since           3
 * @since           3.5.0       Renamed from `AmazonAutoLinks_ContextualProductWidget_SearchKeyword`.
 * @filter          aal_filter_post_object
 */
class AmazonAutoLinks_ContextualUnit_SearchKeyword extends AmazonAutoLinks_PluginUtility {

    public $aCriteria           = array();
    public $sAdditionalKeywords = '';

    private $___oPost;
    private $___aGET = array();

    /**
     * Sets up properties.
     */
    public function __construct( array $aCriteria, $sAdditionalKeywords='' ) {

        $this->aCriteria           = $aCriteria;
        $this->sAdditionalKeywords = $sAdditionalKeywords;

        // Allow ajax unit loading to set referrer's request
        $this->___oPost            = apply_filters(
            'aal_filter_post_object', 
            $this->getElement( $GLOBALS, 'post' )
        );
        $this->___aGET             = apply_filters( 'aal_filter_http_get', $_GET );
    }
    
    /**
     * 
     * @return      string       The search keywords.
     * @todo        If nothing uses the `$bReturnType` parameter, deprecate it.
     */
    public function get( $bReturnType=true ) {
        
        $_aKeywords    = $this->___getSearchKeywordsByCriteria( $this->aCriteria );
        $_aKeywords    = array_merge(
            $_aKeywords,
            $this->___getSiteSearchKeywords()
        );
        $_aKeywords    = $this->___getFormattedSearchKeywordsArray( $_aKeywords );
        $_sAdditionals = $this->trimDelimitedElements( 
            trim( $this->sAdditionalKeywords ), // subject string
            ',',  // delimiter
            false // add a space with the delimiter
        );
        $_sAdditionals = $_sAdditionals
            ? ',' . $_sAdditionals
            : '';
        $_sKeywords    = implode( ',', $_aKeywords )
            . $_sAdditionals;
            
        return $bReturnType
            ? explode( ',', $_sKeywords )
            : $_sKeywords;
            
    }
        
        /**
         * @return      array
         */
        private function ___getSiteSearchKeywords() {

            $_sQuery = $this->getElement( $this->___aGET, 's' );
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
        private function ___getFormattedSearchKeywordsArray( array $aKeywords ) {
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
        private function ___getSearchKeywordsByCriteria( array $aCriteria ) {

            $_aKeywords  = array();
            foreach( $this->___getFormattedCriteriaArray( $aCriteria ) as $_sCriteriaKey ) {
                $_aKeywords = array_merge(
                    call_user_func(
                        array( $this, '___getSearchKeywordsByType_' . $_sCriteriaKey )
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
            private function ___getFormattedCriteriaArray( array $aCriteria ) {
                return array_keys(
                    array_filter( $aCriteria )
                );                    
            }
            /**
             * 
             * @since       3
             * @return      array
             */
            private function ___getSearchKeywordsByType_post_title() {
                return isset( $this->___oPost->post_title )
                    ? array( $this->___oPost->post_title )
                    : array();
            }
            
            /**
             * 
             * @since       3
             * @return      array
             */
            private function ___getSearchKeywordsByType_breadcrumb() {
                $_oBreadcrumb  = new AmazonAutoLinks_ContextualUnit_Breadcrumb(
                    $this->___aGET,
                    $this->___oPost
                );
                return $_oBreadcrumb->get();
            }  
 
            /**
             * 
             * @since       3
             * @return      array
             */
            private function ___getSearchKeywordsByType_taxonomy_terms() {
                
                if ( ! isset( $this->___oPost->post_type, $this->___oPost->ID ) ) {
                    return array();
                }
                
                // Retrieve associated taxonomies.
                $_aTaxonomyObjects = get_object_taxonomies( 
                    $this->___oPost->post_type, 
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
                    $this->___oPost->ID, 
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