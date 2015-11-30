<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * Handles unit options.
 * 
 * @since       3
 * @remark      Do not make it abstract as form fields classes need to access the default struture of the item format array.
 */
class AmazonAutoLinks_UnitOption_Base extends AmazonAutoLinks_WPUtility {

    /**
     * Stores the unit type.
     * @remark      Should be overridden in an extended class.
     */
    public $sUnitType = 'category';

    /**
     * Stores the unit ID.
     */
    public $iUnitID;

    /**
     * Stores the required common unit option keys.
     * 
     */
    public $aCommonKeys = array(
        'unit_type'                     => null,
        'unit_title'                    => null,
        'cache_duration'                => 86400,  // 60*60*24
        
        'count'                         => 10,
        'column'                        => 4,
        'country'                       => 'US',
        'associate_id'                  => null,
        'image_size'                    => 160,      
        'ref_nosim'                     => false,
        'title_length'                  => -1,
        'link_style'                    => 1,
        'credit_link'                   => 1, // 1 or 0

// @todo not sure about this         
'title'                 => '',      // won't be used to fetch links. Used to create a unit.
        
        'template'              => '',      // the template name - if multiple templates with a same name are registered, the first found item will be used.
        'template_id'           => null,    // the template ID: md5( dir path )
        'template_path'         => '',      // the template can be specified by the template path. If this is set, the 'template' key won't take effect.
        
        'is_preview'            => false,   // for the search unit, true won't be used but just for the code consistency. 
        
        
        // stores labels associated with the units (the plugin custom taxonomy).
        '_labels'               => array(),    
        
// this is for fetching by label. AND, IN, NOT IN can be used
'operator'              => 'AND',   

        
        // 3+
        'subimage_size'                 => 100,
        'subimage_max_count'            => 5,
        'customer_review_max_count'     => 2,
        'customer_review_include_extra' => false,
        
        'button_id'                     => null, // a button (post) id will be assigned
        // 3.1.0+
        'button_type'                   => 1,   // 0: normal link, 1: add to cart
        
        'product_filters'               => array(
            'white_list'    => array(
                'asin'          => '',
                'title'         => '',
                'description'   => '',
            ),
            'black_list'    => array(
                'asin'          => '',
                'title'         => '',
                'description'   => '',
            ),
            'case_sensitive'    => 0,   // or 1
            'no_duplicate'      => 0,   // or 1
        ),
        // 3.1.0+
        'skip_no_image'               => false,
       
       
        'width'         => null,
        'width_unit'    => '%',
        'height'        => null,
        'height_unit'   => 'px',
        
        'show_errors'   => true,    // whether to show an error message.
        
        // 3.2.0+
        'show_now_retrieving_message'   => true,
 
    );
    
    /**
     * Stores the default option structure.
     * 
     * This one will be merged with several other key structure and $aDefault will be constructed.
     */
    static public $aStructure_Default = array();    
    
    /**
     * Stores the default unit option values and represents the array structure.
     * 
     * @remark      Should be defined in an extended class.
     */
    public $aDefault = array();
    
    /**
     * Stores the associated options to the unit.
     */
    public $aUnitOptions = array();
        
    /**
     * Sets up properties.
     * 
     * @param       integer     $iUnitID        The unit ID as a post ID.
     * @param       array       $aUnitOptions   (optional) The unit option to set. Used to sanitize unit options.
     */
    public function __construct( $iUnitID, array $aUnitOptions=array() ) {
        
        $this->iUnitID      = $iUnitID;
        $this->aDefault     = array(
                'unit_type' => $this->sUnitType,
                'id'        => null,    // required when parsed in the Output class
            )
            + $this->getDefaultOptionStructure()
            + $this->getDefaultItemFormat()
            + $this->aCommonKeys;
        $this->aUnitOptions = $iUnitID
            ? $aUnitOptions 
                + array( 'id' => $iUnitID ) 
                + $this->getPostMeta( $iUnitID )
            : $aUnitOptions;
        $this->aUnitOptions = $this->format( $this->aUnitOptions );

    }
        /**
         * @return      array
         */
        protected function getDefaultOptionStructure() {

            // This lets PHP 5.2 access static properties of an extended class.
            $_aProperties = get_class_vars( get_class( $this ) );
            return $_aProperties[ 'aStructure_Default' ];
            
        }
    /**
     * 
     * @since       3 
     */
    protected function format( array $aUnitOptions ) {

        $_oOption     = AmazonAutoLinks_Option::getInstance();        
        $aUnitOptions = $aUnitOptions + $this->aDefault;

        // the item lookup search unit type does not have a count field
        if( isset( $aUnitOptions['count'] ) ) {
            $aUnitOptions['count'] = $this->fixNumber( 
                $aUnitOptions['count'],     // number to sanitize
                10,     // default
                1,         // minimum
                $_oOption->getMaximumProductLinkCount() // max
            );            
        }
        $aUnitOptions[ 'image_size' ] = $this->fixNumber( 
            $aUnitOptions['image_size'],     // number to sanitize
            160,     // default
            0,         // minimum
            500     // max
        );        
        if ( isset( $aUnitOptions[ 'column' ] ) ) {
            $aUnitOptions[ 'column' ] = AmazonAutoLinks_Utility::fixNumber( 
                $aUnitOptions['column'],     // number to sanitize
                4,     // default
                1,         // minimum
                $_oOption->getMaxSupportedColumnNumber()
            );            
        }

        // Drop undefined keys.
        foreach( $aUnitOptions as $_sKey => $_mValue ) {
            if ( array_key_exists( $_sKey, $this->aDefault ) ) {
                continue;
            }
            unset( $aUnitOptions[ $_sKey ] );
        }
        
        return $aUnitOptions;
        
    }    
        
    /**
     * @scope       static  public        This is because form field classes need to retrieve the structure.
     * @remark      The array contains the concatenation character(.) 
     * so it cannot be done in the declaration.
     * @return      array
     */
    static public function getDefaultItemFormat() {
        
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_bAPIConnected = $_oOption->isAPIConnected();
        return array(
            'item_format' => $_bAPIConnected
                ? '%image%' . PHP_EOL    // since the 
                    . '%image_set%' . PHP_EOL
                    . '%rating%' . PHP_EOL
                    . '%title%' . PHP_EOL
                    . '%description%'
                : '%image%' . PHP_EOL    // since the 
                    . '%title%' . PHP_EOL
                    . '%description%',
                    
            'image_format' => '<div class="amazon-product-thumbnail" style="max-width:%max_width%px; min-height:%max_width%;">' . PHP_EOL
                . '    <a href="%href%" title="%title_text%: %description_text%" rel="nofollow" target="_blank">' . PHP_EOL 
                . '        <img src="%src%" alt="%description_text%" style="max-height:%max_width%;" />' . PHP_EOL
                . '    </a>' . PHP_EOL
                . '</div>',
                
            'title_format' => '<h5 class="amazon-product-title">' . PHP_EOL
                . '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow" target="_blank">%title_text%</a>' . PHP_EOL 
                . '</h5>',    
                
        );
        
    }        
    
    /**
     * Returns the all associated options if no key is set; otherwise, the value of the specified key.
     * 
     * @since       3
     * @return      
     */
    public function get( /* $sKey1, $sKey2, $sKey3, ... OR $aKeys, $vDefault */ ) {
    
        $_mDefault  = null;
        $_aKeys     = func_get_args() + array( null );

        // If no key is specified, return the entire option array.
        if ( ! isset( $_aKeys[ 0 ] ) ) {
            return $this->aUnitOptions;
        }
        
        // If the first key is an array, te second parameter is the default value.
        if ( is_array( $_aKeys[ 0 ] ) ) {
            $_mDefault = isset( $_aKeys[ 1 ] )
                ? $_aKeys[ 1 ]
                : null;
            $_aKeys    = $_aKeys[ 0 ];
        }    
    
        // Now either the section ID or field ID is given. 
        return $this->getArrayValueByArrayKeys( 
            $this->aUnitOptions, 
            $_aKeys,
            $_mDefault
        );    
  
    }    
    
    /**
     * Sets a value to the specified keys.
     * 
     * @param       array|string        $asOptionKey        The key path. e.g. 'search_per_keyword'
     * @return      void
     * @since       3.1.4
     */
    public function set( $asOptionKey, $mValue ) {
        $this->setMultiDimensionalArray( 
            $this->aUnitOptions, 
            $this->getAsArray( $asOptionKey ),
            $mValue
        );
    }
    
}