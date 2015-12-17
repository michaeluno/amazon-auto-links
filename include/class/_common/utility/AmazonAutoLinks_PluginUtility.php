<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Provides plugin specific utility methods that uses WordPerss built-in functions.
 *
 * @package     Amazon Auto Links
 * @since       3       
 */
class AmazonAutoLinks_PluginUtility extends AmazonAutoLinks_WPUtility {
       
    /**
     * Returns the auto-insert ids.
     * 
     * 
     * @since       3.3.0
     * @return      array
     */
    static public function getActiveAutoInsertIDs() {
        $_oQuery = new WP_Query(
            array(
                'post_status'    => 'publish',     // optional
                'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ], 
                'posts_per_page' => -1, // ALL posts
                'fields'         => 'ids',  // return an array of post IDs
                'meta_query'        => array(
                    array(    // do not select tasks of empty values of the _next_run_time key.
                        'key'       => 'status',
                        'value'     => true,
                    ),                            
                ),                    
            )
        );       
        return $_oQuery->posts;
    }    
       
    /**
     * @since       3.2.4
     * @return      string
     */
    static public function getUpgradePromptMessage( $bHasLink=true ) {        
        return $bHasLink
            ? sprintf(
                __( 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to enable this feature.', 'amazon-auto-links' ),
                'http://en.michaeluno.jp/amazon-auto-links-pro/'
            )
            : __( 'Please consider upgrading to Pro to enable this feature.', 'amazon-auto-links' );
            
    }
    
    /**
     * Returns the HTML credit comment.
     * @since       3.1.0
     * @return      string
     */
    static public function getCommentCredit() {
        return '<!-- Rendered with Amazon Auto Links by miunosoft -->';
    }
    
    /**
     * Returns a list of labels (unit taxonomy) associated with the given unit id.
     * @since       3.1.0
     * @return      string
     */
    static public function getReadableLabelsByUnitID( $iUnitID ) {
        $_aboTerms = get_the_terms( 
            $iUnitID, 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ]
        );
        if ( ! is_array( $_aboTerms ) ) {
            return '';
        }
        $_aTermLabels = array();
        foreach( $_aboTerms as $_oTerm ) {
            $_aTermLabels[] = $_oTerm->name;
        }
        return implode( ', ', $_aTermLabels );
    }
    
    /**
     * @since       3.1.0
     * @return      string      comma-delimited readable unit labels.
     */
    static public function getReadableLabelsByLabelID( $asTermIDs ) {

        $_aTermIDs = is_array( $asTermIDs )
            ? $asTermIDs
            : self::convertStringToArray( $asTermIDs, ',' );
    
        $_aTermLabels = array();
        foreach( $_aTermIDs as $_iTermID ) {
            $_oTerm = get_term_by( 
                'id', 
                absint( $_iTermID ), 
                AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ]
            );
            $_aTermLabels[] = $_oTerm->name;
        }
    
        return implode( ', ', $_aTermLabels );
        
    }
    
    
    /**
     * 
     * @since       3.1.0
     * @return      string       comma-delimited unit label ids.
     */
    static public function getLabelIDsByUnitID( $iUnitID ) {
        
        // Get the genres for the post.
        $_abTerms = get_the_terms( 
            $iUnitID, 
            AmazonAutoLinks_Registry::$aTaxonomies[ 'tag' ] 
        );
        
        $_aTerms  = self::getAsArray( $_abTerms );

        // Iterate each term, linking to the 'edit posts' page for the specific term. 
        $_aOutput = array();
        foreach ( $_aTerms as $_oTerm ) {
            $_aOutput[] = $_oTerm->term_id;
        }

        // Join the terms, separating them with a comma.
        return implode( ',', $_aOutput );
        
    }

    /**
     * Returns an array holding unit type labels.
     * @since       3
     * @return      array
     */
    static public function getUnitTypeLabels() {
        return array(
            'category'          => __( 'Category', 'amazon-auto-links' ),
            'tag'               => __( 'Tag', 'amazon-auto-links' ),
            'search'            => __( 'Product Search', 'amazon-auto-links' ),
            'item_lookup'       => __( 'Item Look-up', 'amazon-auto-links' ),
            'similarity_lookup' => __( 'Similarity Look-up', 'amazon-auto-links' ),
            'url'               => __( 'URL', 'amazon-auto-links' ),
        );
    }

    /**
     * @return  string
     * @since   3
     */
    static public function getDegree( $sDegreeType='width', $aArguments ) {
        $_iDegree = self::getElement(
            $aArguments,
            $sDegreeType,
            null
        );
        if ( in_array( $_iDegree, array( null, '', false ), true ) ) {
            return '';
        }
        $_sUnit = self::getElement(
            $aArguments,
            $sDegreeType . '_unit',            
            'width' === $sDegreeType
                ? '%'
                : 'px'
        );
        return $_iDegree . $_sUnit;
    }

    /**
     * 
     * @since       3
     */
    static private $_aFunctionNamesWithSlugs = array(
        // function name => page type slug
        'is_home'               => 'home',
        'is_front_page'         => 'front',    
        'is_singular'           => 'singular',
        'is_post_type_archive'  => 'post_type_archive',
        'is_tax'                => 'taxonomy',
        'is_tag'                => 'taxonomy',
        'is_category'           => 'taxonomy',
        'is_date'               => 'date',
        'is_author'             => 'author',
        'is_search'             => 'search',
        'is_404'                => '404',
    );
    /**
     * 
     * @since       3
     * @return      string      The type slug
     */
    static public function getCurrentPageType() {
        foreach( self::$_aFunctionNamesWithSlugs as $_sFuncName => $_sTypeSlug ) {
            if ( call_user_func( $_sFuncName ) ) {
                return $_sTypeSlug;
            }
        }
        return '';
    }    

    /**
     * Returns all CSS rules of active buttons.
     * 
     * @return      string
     * @since       3
     */
    static public function getCSSRulesOfActiveButtons() {
        
        $_aCSSRules = array();
        foreach( self::getActiveButtonIDs() as $_iID ) {
            $_aCSSRules[]  = str_replace(
                '___button_id___',
                $_iID,
                trim( get_post_meta( $_iID, 'button_css', true ) )
            );
            $_aCSSRules[] = trim( get_post_meta( $_iID, 'custom_css', true ) );
        }
        return trim( implode( PHP_EOL, array_filter( $_aCSSRules ) ) );
        
    }    
    
    /**
     * 
     * @return      array       An array holding button IDs.
     */
    static public function getActiveButtonIDs() {
        
        $_oQuery = new WP_Query(
            array(
                'post_status'    => 'publish',     // optional
                'post_type'      => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ], 
                'posts_per_page' => -1, // ALL posts
                'fields'         => 'ids',  // return an array of post IDs
            )
        );       
        return $_oQuery->posts;                
        
    }        

    /**
     * Returns a button output by a given button (custom post) ID.
     * @return      string
     * @since       3
     */
    static public function getButton( $isButtonID, $sLabel='', $bVisible=true ) {
        
        $_sButtonLabel      = $sLabel
            ? $sLabel
            : ( 
                is_numeric( $isButtonID ) && $isButtonID
                    ? get_post_meta( $isButtonID, 'button_label', true )
                    : ''
            );

        $_sButtonLabel      = $_sButtonLabel
            ? $_sButtonLabel
            : __( 'Buy Now', 'amazon-auto-links' );
            
        $_sButtonIDSelector = $isButtonID
            ? "amazon-auto-links-button-$isButtonID"
            : "amazon-auto-links-button-___button_id___";
        $_sNone   = 'none';
        $bVisible = $bVisible
            ? ''
            : "display:{$_sNone};";
        return "<div class='amazon-auto-links-button-container' style='{$bVisible}'>"
                . "<div class='amazon-auto-links-button {$_sButtonIDSelector}'>"
                    . $_sButtonLabel
                . "</div>"
            . "</div>";
            
    } 
    
    /**
     * Returns the url using the Amazon SSL image server.
     * @since       3
     * @return      string      The converted url.
     */
    static public function getAmazonSSLImageURL( $sImgURL ) {
        return preg_replace(
            "/^http:\/\/.+?\//", 
            "https://images-na.ssl-images-amazon.com/",
            $sImgURL
        );
    }

    /**
     * Creates an auto-insert.
     * @since       3
     */
    static public function createAutoInsert( $iNewPostID ) {
                
        // Construct a meta array.
        $_aAutoInsertOptions = array( 
            'unit_ids' => array( $iNewPostID ),
        ) + AmazonAutoLinks_AutoInsertAdminPage::$aStructure_AutoInsertDefaultOptions;
        
        // Insert a post.
        AmazonAutoLinks_WPUtility::insertPost( 
            $_aAutoInsertOptions, 
            AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
        );            
        
    }    
    
    /**
     * Go to the Authentication tab of the Settings page.
     * @since       3
     * @return      void
     */
    static public function goToAPIAuthenticationPage()  {
        exit(
            wp_safe_redirect( self::getAPIAuthenticationPageURL() )
        );        
    }
    
    /**
     * @return      string
     */
    static public function getAPIAuthenticationPageURL() {
        return add_query_arg(
            array( 
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'main' ],
                'tab'       => 'authentication',
            ), 
            admin_url( 'edit.php' )
        );
    }
 
}