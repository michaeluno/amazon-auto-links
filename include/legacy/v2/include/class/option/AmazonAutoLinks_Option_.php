<?php
/**
 * Handles the options of Amazon Auto Links.
 * 
 * @package      Amazon Auto Links
 * @copyright    Copyright (c) 2013-2015, Michael Uno
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0
 * @filter       aal_filter_max_column_number
 * 
 */
abstract class AmazonAutoLinks_Option_ {
    
    /**
     * 
     * This is public as accessed when defining the form fields.
     */
    public static $arrStructure_Options = array(
        'aal_settings' => array(
            'capabilities' => array(
                'setting_page_capability' => 'manage_options',
            ),        
            'debug' => array(
                'debug_mode' => 0,
            ),
            'form_options' => array(
                'allowed_html_tags' => 'style, noscript',
            ),
            'product_filters' => array(
                'black_list' => array(
                    'asin' => '',
                    'title' => '',
                    'description' => '',
                ),
                'white_list' => array(
                    'asin' => '',
                    'title' => '',
                    'description' => '',
                ),
                'case_sensitive' => 0,
                'no_duplicate' => 0,    // in 2.0.5.1 changed to 0 from 1.
            ),
            'support' => array(
                'rate' => 0,            // asked for the first load of the plugin admin page
                'ads' => false,            // asked for the first load of the plugin admin page
                'review' => 0,            // not implemented yet
                'agreed' => false,        // hidden
            ),
            'cache'    =>    array(
                'chaching_mode' => 'normal',
            ),
            'query' => array(
                'cloak' => 'productlink'
            ),
            'authentication_keys' => array(
                'access_key' => '',
                'access_key_secret' => '',
            ),            
            // Hidden options
            'template' => array(
                'max_column' => 1,
            ),
            'import_v1_options' => array(
                'dismiss' => false,
            ),
            // 2.2.0+
            'unit_preview' => array(
                'preview_post_type_slug' => '',
                'visible_to_guests'      => true,
                'searchable'             => false,
            ),
        ),
        'arrTemplates' => array(),    // stores information of active templates.
    );
     
    public $arrOptions = array();    // stores the option values.
         
    protected $strOptionKey = '';    // stores the option key for this plugin. 
         
    public function __construct( $sOptionKey ) {

        $this->strOptionKey     = $sOptionKey;
        $this->arrOptions       = $this->set( $sOptionKey );
        $this->strCharEncoding  = get_bloginfo( 'charset' ); 
        
        // Black ASINs 
        $GLOBALS['arrBlackASINs'] = AmazonAutoLinks_Utilities::convertStringToArray( 
            $this->arrOptions['aal_settings']['product_filters']['black_list']['asin'],
            ',' 
        );
        
    }    
    
    /*
     * 
     * Back end methods
     * */
    protected function set( $sOptionKey ) {
        
        $vOption = get_option( $sOptionKey, array() );
        
        // Avoid casting array because it causes a zero key when the subject is null.
        $vOption = empty( $vOption ) 
            ? array() 
            : $vOption;        
        
        // Now $vOption is an array so merge with the default option to avoid undefined index warnings.
        $arrOptions = AmazonAutoLinks_Utilities::uniteArrays( $vOption, self::$arrStructure_Options );
            
        return $arrOptions;
                
    }

    /**
     * Resets options.
     * 
     */
    protected function reset() {
        delete_option( $this->strOptionKey );
    }
    
    
    public function save() {
        update_option( $this->strOptionKey, $this->arrOptions );
    }
    
    public function sanitizeUnitOpitons( $arrUnitOptions ) {
        
        if( isset( $arrUnitOptions['count'] ) )    // the item lookup search unit type does not have a count field
            $arrUnitOptions['count'] = AmazonAutoLinks_Utilities::fixNumber( 
                $arrUnitOptions['count'],     // number to sanitize
                10,     // default
                1,         // minimum
                $this->getMaximumProductLinkCount()     // max
            );            
        $arrUnitOptions['image_size'] = AmazonAutoLinks_Utilities::fixNumber( 
            $arrUnitOptions['image_size'],     // number to sanitize
            160,     // default
            0,         // minimum
            500     // max
        );        
        if ( isset( $arrUnitOptions['column'] ) ) 
            $arrUnitOptions['column'] = AmazonAutoLinks_Utilities::fixNumber( 
                $arrUnitOptions['column'],     // number to sanitize
                4,     // default
                1,         // minimum
                $this->getMaxSupportedColumnNumber()
                // $this->arrOptions['aal_settings']['template']['max_column']     // max
            );            

        // For the 'item_lookup' unit type
        if ( isset( $arrUnitOptions['unit_type'] ) && 'item_lookup' === $arrUnitOptions['unit_type'] ) {
            $this->sanitizeUnitOptions_ItemLookUp( $arrUnitOptions );        
        }
        
        return $arrUnitOptions;
        
    }
    /**
     * Sanitizes the unit options of the item_lookup unit type.
     * 
     * @since            2.0.2
     */
    protected function sanitizeUnitOptions_ItemLookUp( array &$aUnitOptions ) {
        
        // if the ISDN is spceified, the search index must be set to Books.
        if ( isset( $aUnitOptions['IdType'], $aUnitOptions['SearchIndex'] ) && $aUnitOptions['IdType'] == 'ISBN' ) {
            $aUnitOptions['SearchIndex'] = 'Books';
        }
        
        $aUnitOptions['ItemId'] =  trim( AmazonAutoLinks_Utilities::trimDelimitedElements( $aUnitOptions['ItemId'], ',' ) );
        
        
    }
    
    public static function getUnitOptionsByPostID( $intPostID ) {
        
        if ( 0 == $intPostID  ) { 
            return $arrPostData;
        }
        
        // this way, array will be unserialized
        // casting array in case the post does not exist
        $arrPostData = array();
        foreach( ( array ) get_post_custom_keys( $intPostID ) as $strKey ) {
            $arrPostData[ $strKey ] = get_post_meta( $intPostID, $strKey, true );        
        }
        return $arrPostData;
        
    }
    
    public static function getUnitType( $intPostID=null ) {

        if ( $intPostID ) {
            return get_post_meta( $intPostID, 'unit_type', true );
        }
            
        if ( ! isset( $_GET['post'] ) || ! $_GET['post'] ) { 
            return ''; 
        }
    
        // If the 'action' query value is edit, search for the meta field value which previously set when it is saved.
        if ( isset( $_GET['action'] ) && $_GET['action'] == 'edit' ) {
            return get_post_meta( $_GET['post'], 'unit_type', true );
        }
        return '';
        
    }        

    public function getAccessPublicKey() {
        return $this->arrOptions['aal_settings']['authentication_keys']['access_key'];
    }
    public function getAccessPrivateKey() {
        return $this->arrOptions['aal_settings']['authentication_keys']['access_key_secret'];
    }
    
    /**
     * Creates a post of a specified custom post type with unit option meta fields.
     * 
     */
    public static function insertPost( $arrUnitOptions, $strPostTypeSlug='', $arrTaxonomy=array(), $arrIgnoreFields=array( 'unit_title' ) ) {
        
        // Create a custom post if it's a new unit.        
        $intPostID = wp_insert_post(
            array(
                'comment_status'    =>    'closed',
                'ping_status'        =>    'closed',
                'post_author'        =>    $GLOBALS['user_ID'],
                // 'post_name'            =>    $slug,
                'post_title'        =>    isset( $arrUnitOptions['unit_title'] ) ? $arrUnitOptions['unit_title'] : '',
                'post_status'        =>    'publish',
                'post_type'            =>    $strPostTypeSlug ? $strPostTypeSlug : AmazonAutoLinks_Commons::PostTypeSlug,
                // 'post_content'         => null,
                // 'post_date' => date('Y-m-d H:i:s'),
                'tax_input'            => $arrTaxonomy // support for custom taxonomies. This does not work if the user capability is not sufficient.        
            )
        );        
        
        // Remove the ignoring keys.
        foreach( $arrIgnoreFields as $strFieldKey ) {
            unset( $arrUnitOptions[ $strFieldKey ] );
        }
        
        // Add meta fields.
        self::updatePostMeta( $intPostID, $arrUnitOptions );
                
        return $intPostID;
        
    }    
    public static function updatePostMeta( $intPostID, $arrPostData ) {
        
        foreach( $arrPostData as $strFieldID => $vValue ) {
            update_post_meta( $intPostID, $strFieldID, $vValue );
        }
        
    }
    
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isCustomPreviewPostTypeSet()  {
        
        if ( ! $this->arrOptions['aal_settings']['unit_preview']['preview_post_type_slug'] ) {
            return false;
        }
        return AmazonAutoLinks_Commons::PostTypeSlug !== $this->arrOptions['aal_settings']['unit_preview']['preview_post_type_slug'];
        
    }
    /**
     * 
     * @since       2.2.0
     * @return      boolean
     */
    public function isPreviewVisible() {
        
        if ( $this->arrOptions['aal_settings']['unit_preview']['visible_to_guests'] ) {
            return true;
        }
        return ( boolean ) is_user_logged_in();
        
    }
    
    
    public function isDebugMode() {
        
        // if ( ! defined( 'WP_DEBUG' ) || ! WP_DEBUG  ) { 
            // return false; 
        // }
        if ( ! $this->arrOptions['aal_settings']['debug']['debug_mode'] ) { 
            return false; 
        }
        return true;
        
    }
    
    public function isSupported() {
        
        $numPercentage = $this->arrOptions['aal_settings']['support']['rate'];
        return ( mt_rand( 1, 100 ) <= $numPercentage )
            ? true
            : false;
        
    }
    public function isUnitLimitReached( $intNumberOfUnits=null ) {
        
        if ( ! isset( $intNumberOfUnits ) ) {
            $oNumberOfUnits = AmazonAutoLinks_WPUtilities::countPosts( AmazonAutoLinks_Commons::PostTypeSlug );
            $intNumberOfUnits = $oNumberOfUnits->publish + $oNumberOfUnits->private + $oNumberOfUnits->trash;
        } 
        return ( $intNumberOfUnits >= 3 )    
            ? true
            : false;
        
    }    
    public function getRemainedAllowedUnits( $intNumberOfUnits=null ) {
        
        if ( ! isset( $intNumberOfUnits ) ) {
            $oNumberOfUnits     = AmazonAutoLinks_WPUtilities::countPosts( AmazonAutoLinks_Commons::PostTypeSlug );
            $intNumberOfUnits   = $oNumberOfUnits->publish + $oNumberOfUnits->private + $oNumberOfUnits->trash;
        } 
        
        return 3 - $intNumberOfUnits;
        
    }
    public function isReachedCategoryLimit( $intNumberOfCategories ) {        
        return ( $intNumberOfCategories >= 3 ) 
            ? true 
            : false;
    }    
    public function getMaximumProductLinkCount() {
        return 10;
    }
    public function canExport() {
        return false;
    }
    public function isSupportMissing() {
        return true;
    }
    public function isAdvancedAllowed() {
        return false;
    }
    
    public function getMaxSupportedColumnNumber() {
        return apply_filters( 
            'aal_filter_max_column_number', 
            $this->arrOptions['aal_settings']['template']['max_column'] 
        );            
    }
    
}