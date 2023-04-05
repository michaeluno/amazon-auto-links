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
 * Provides methods to find search keywords from the current page.
 * 
 * @since 3
 * @since 3.5.0 Renamed from `AmazonAutoLinks_ContextualProductWidget_SearchKeyword`.
 */
class AmazonAutoLinks_ContextualUnit_SearchKeyword extends AmazonAutoLinks_PluginUtility {

    public $aCriteria           = array(
        // 1 or 0 as string (saved form value)
        'post_title'     => '0',
        'site_title'     => '0',
        'taxonomy_terms' => '0',
        'breadcrumb'     => '0',
    );
    public $sAdditionalKeywords = '';
    public $sExcludingKeywords  = '';        // 3.12.0

    private $___oPost;
    private $___aGET = array();

    /**
     * Sets up properties.
     */
    public function __construct( array $aCriteria, $sAdditionalKeywords='', $sExcludingKeywords='' ) {

        $this->aCriteria           = $aCriteria + $this->aCriteria;
        $this->sAdditionalKeywords = $sAdditionalKeywords;
        $this->sExcludingKeywords  = $sExcludingKeywords;

        // Allow ajax unit loading to set referrer's request
        $this->___oPost            = apply_filters(
            'aal_filter_post_object', 
            $this->getElement( $GLOBALS, 'post' )
        );
        // Currently, only the `s` element is used
        $_aGET                     = array(
            's' => _sanitize_text_fields( $this->getElement( $_GET, array( 's' ) ) ),   // sanitization done
        );
        $this->___aGET             = apply_filters( 'aal_filter_http_get', $_aGET );
    }
    
    /**
     * @return array The search keywords.
     */
    public function get() {
        
        $_aKeywords        = $this->___getSearchKeywordsByCriteria( $this->aCriteria );
        $_aKeywords        = array_merge(
            $_aKeywords,
            $this->___getSiteSearchKeywords(),
            $this->getStringIntoArray( $this->sAdditionalKeywords, ',' )
        );
        $_aExcludeWords    = $this->getStringIntoArray( $this->sExcludingKeywords, ',' );
        $_aKeywords        = array_udiff( $_aKeywords, $_aExcludeWords, 'strcasecmp' ); // case-insensitive
        return array_unique( $_aKeywords );

    }
        
        /**
         * @return array
         */
        private function ___getSiteSearchKeywords() {
            $_sQuery = str_replace(
                array( '+' ),
                ',',
                $this->getElement( $this->___aGET, 's' )
            );
            return array_filter( explode( ',', $_sQuery ) );
        }

        /**
         * @return     array
         * @deprecated 5.4.0
         */
        // private function ___getFormattedSearchKeywordsArray( array $aKeywords ) {
        //     $aKeywords = array_unique( array_filter( $aKeywords ) );
        //     if ( empty( $aKeywords ) ) {
        //         $aKeywords[] = get_bloginfo( 'name' );
        //     }
        //     return $aKeywords;
        // }

        /**
         *
         * @since  3
         * @param  array $aCriteria
         * The structure:
         * ```
         *  array(
         *      'post_title'        => true,
         *      'breadcrumb'        => true,
         *      'taxonomy_terms'    => true,
         *      'site_title'        => true,
         *  )
         * ```
         * @return array
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
             * @since  3
             * @return array
             */
            private function ___getFormattedCriteriaArray( array $aCriteria ) {
                return array_keys( array_filter( $aCriteria ) );
            }
            /**
             *
             * @since  5.4.0
             * @return array
             */
            private function ___getSearchKeywordsByType_site_title() {
                return array( get_bloginfo( 'name' ) );
            }
            /**
             * @since  3
             * @return array
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