<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
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
     * Stores the default option structure.
     * 
     * This one will be merged with several other key structure and $aDefault will be constructed.
     */
    static public $aStructure_Default = array();
    
    /**
     * @remark      Shortcode argument keys are all converted to lower-cases but Amazon API keys are camel-cased.
     * @since       3.4.6
     */
    static public $aShortcodeArgumentKeys = array();
    
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
        
        $_oOption = AmazonAutoLinks_Option::getInstance();
        
        $this->iUnitID      = $iUnitID;
        $this->aDefault     = array(
                'unit_type' => $this->sUnitType,
                'id'        => null,    // required when parsed in the Output class
            )
            + $this->getDefaultOptionStructure()
            + $_oOption->get( 'unit_default' )      // 3.4.0+
            ;
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

        $aUnitOptions = $this->getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );
        
        // the item lookup search unit type does not have a count field
        if( isset( $aUnitOptions[ 'count' ] ) ) {
            $aUnitOptions[ 'count' ] = $this->fixNumber(
                $aUnitOptions[ 'count' ],     // number to sanitize
                10,     // default
                1,         // minimum
                $_oOption->getMaximumProductLinkCount() // max
            );            
        }
        $aUnitOptions[ 'image_size' ] = $this->fixNumber( 
            $aUnitOptions[ 'image_size' ],     // number to sanitize
            160,     // default
            0,         // minimum
            500     // max
        );        
        if ( isset( $aUnitOptions[ 'column' ] ) ) {
            $aUnitOptions[ 'column' ] = AmazonAutoLinks_Utility::fixNumber( 
                $aUnitOptions[ 'column' ],     // number to sanitize
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
         * @remark      shortcode arguments are all converted to lower-cases but Amazon API keys are camel-calsed.
         * @since       3.4.6
         * @return      array
         */
        protected function getShortcodeArgumentKeysSanitized( array $aUnitOptions, array $aShortcodeArgumentKeys ) {
            // Shortcode parameter keys are converted to lower cases.
            foreach( $aUnitOptions as $_sKey => $_mValue ) {
                if ( isset( $aShortcodeArgumentKeys[ $_sKey ] ) ) {
                    $_sCorrectKey = $aShortcodeArgumentKeys[ $_sKey ];
                    $aUnitOptions[ $_sCorrectKey ] = $_mValue;
                    unset( $aUnitOptions[ $_sKey ] );
                }
            }
            return $aUnitOptions;
        }

    /**
     * Returns the all associated options if no key is set; otherwise, the value of the specified key.
     * 
     * @since       3
     * @return      
     */
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