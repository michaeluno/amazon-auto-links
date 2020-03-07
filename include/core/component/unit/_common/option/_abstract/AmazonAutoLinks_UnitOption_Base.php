<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
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
        $this->aUnitOptions = $this->_getUnitOptionsFormatted( $this->aUnitOptions, $this->aDefault );

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
     * @param array $aUnitOptions
     * @param array $aDefaults
     *
     * @return array|mixed
     * @since       3
     * @since       4.0.0   Changed the name from `format()` as it was too general.
     * @since       4.0.0   Added the $aDefaults parameter.
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults ) {

        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $aUnitOptions = $aUnitOptions + $aDefaults;

        $aUnitOptions = $this->_getShortcodeArgumentKeysSanitized( $aUnitOptions, self::$aShortcodeArgumentKeys );

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
            if ( array_key_exists( $_sKey, $aDefaults ) ) {
                continue;
            }
            unset( $aUnitOptions[ $_sKey ] );
        }

        $_oTemplateDeterminer = new AmazonAutoLinks_UnitOutput__Template( $this );
        $_sTemplateID         = $_oTemplateDeterminer->get();
        $aUnitOptions[ 'template_id' ] = $_sTemplateID;

        // @deprecated 4.0.2    This misses a case that the `template` argument is given
//        $_sTemplateID         = $this->getElement( $aUnitOptions, array( 'template_id' ), '' );
//        $_oTemplateOption    = AmazonAutoLinks_TemplateOption::getInstance();
//        $_sTemplateID        = $_oTemplateOption->isActive( $_sTemplateID )
//            ? $_sTemplateID
//            : $_oTemplateOption->getDefaultTemplateIDByUnitType( $this->getElement( $aUnitOptions, 'unit_type' ) );

        $_aOutputFormats                = $this->___getOutputFormats( $aUnitOptions, $_sTemplateID );
        $aUnitOptions[ 'item_format' ]  = $this->___getItemFormat( $aUnitOptions, $_aOutputFormats, $_sTemplateID );
        $aUnitOptions[ 'image_format' ] = $this->___getImageFormat( $aUnitOptions, $_aOutputFormats, $_sTemplateID );
        $aUnitOptions[ 'title_format' ] = $this->___getTitleFormat( $aUnitOptions, $_aOutputFormats, $_sTemplateID );
        return $aUnitOptions;
        
    }

        /**
         * Extracts the Output Formats unit option from the unit options array.
         * @param   array $aUnitOptions
         * @param   string $sTemplateID
         *
         * @return  array
         * @since   4.0.4
         */
        private function ___getOutputFormats( array $aUnitOptions, $sTemplateID ) {
            $_sTemplateFieldKey  = str_replace( array( '.', '/', '\\', '-' ), '_', $sTemplateID ); // the field id (name) gets automatically converted by Admin Page Framework.
            $_aOutputFormats     = $this->getElementAsArray( $aUnitOptions, array( 'output_formats', $_sTemplateFieldKey ) );
            if ( ! empty( $_aOutputFormats )  ) {
                return $_aOutputFormats;
            }
            // For backward compatibility for a case the ID has a trailing slash
            return $this->getElementAsArray( $aUnitOptions, array( 'output_formats', $_sTemplateFieldKey . '_' ) );
        }

        /**
         * Returns the `item_format` unit option. As of v4, it is not stored anywhere but `output_formats` option holds the value for each template.
         *
         * @param array  $aUnitOptions       The unit options to parse.
         * @param array  $aOutputFormats     The `output_formats` element of unit options.
         * @param string $sTemplateID        The set template ID.
         * @return      string
         * @since       4.0.0
         */
        private function ___getItemFormat( array $aUnitOptions, array $aOutputFormats, $sTemplateID ) {

            /**
             * Get the template specific item format option.
             *
             * Applying filters here is important for 3rd party template conversions such as AALB.
             * For example, there is a case:
             *  1. the user saves the default unit options (Amazon Auto Links -> Settings -> Default)
             *  2. activates the desired template such as Text. (Amazon Auto Links -> Templates)
             *  3. set the Template Conversion option. (Settings -> 3rd Party -> AALB)
             * In this case, even though the `template_id` argument is properly converted to Text, the default item format option is not set, which results in setting the global default item format and showing unnecessary unit output elements such as sub-images.
             */
            $_sItemFormat        = $this->getElement( $aOutputFormats, array( 'item_format' ) );
            $_sItemFormat        = isset( $_sItemFormat )
                ? $_sItemFormat
                : apply_filters(
                    'aal_filter_template_default_item_format_' . $sTemplateID,
                    $this->getElement( $aUnitOptions, array( 'item_format' ), '' )   // backward-compatibility for v3 or below
                );

            // Append internal tags.
            // For hidden Item Format elements, '%_discount_rate%', '%_review_rate%'
            if ( $this->getElement( $aUnitOptions, array( '_filter_by_rating', 'enabled' ) ) ) {
                $_sItemFormat .= '<!-- %_review_rate% -->';
            }
            if ( $this->getElement( $aUnitOptions, array( '_filter_by_discount_rate', 'enabled' ) ) ) {
                $_sItemFormat .= '<!-- %_discount_rate% -->';
            }
            return $_sItemFormat;

        }

        /**
         * Returns the `image_format` unit option. As of v4, it is not stored anywhere but `output_formats` option holds the value for each template.
         *
         * @param array  $aUnitOptions       The unit options to parse.
         * @param array  $aOutputFormats     The `output_formats` element of unit options.
         * @param string $sTemplateID        The set template ID.
         *
         * @return      string
         * @since       4.0.0
         */
        private function ___getImageFormat( array $aUnitOptions, array $aOutputFormats, $sTemplateID ) {
            $_sImageFormat = $this->getElement( $aOutputFormats, array( 'image_format' ) );
            return isset( $_sImageFormat )
                ? $_sImageFormat
                : apply_filters(
                    'aal_filter_template_default_image_format_' . $sTemplateID,
                    $this->getElement( $aUnitOptions, array( 'image_format' ), '' )   // backward-compatibility for v3 or below
                );
        }

        /**
         * Returns the `title_format` unit option. As of v4, it is not stored anywhere but `output_formats` option holds the value for each template.
         *
         * @param array  $aUnitOptions       The unit options to parse.
         * @param array  $aOutputFormats     The `output_formats` element of unit options.
         * @param string $sTemplateID        The set template ID.
         *
         * @return      string
         * @since       4.0.0
         */
        private function ___getTitleFormat( array $aUnitOptions, array $aOutputFormats, $sTemplateID ) {
            $_sTitleFormat = $this->getElement( $aOutputFormats, array( 'title_format' ) );
            return isset( $_sTitleFormat )
                ? $_sTitleFormat
                : apply_filters(
                    'aal_filter_template_default_title_format_' . $sTemplateID,
                    $this->getElement( $aUnitOptions, array( 'title_format' ), '' )   // backward-compatibility for v3 or below
                );
        }

        /**
         * @remark      shortcode arguments are all converted to lower-cases but Amazon API keys are camel-calsed.
         * @since       3.4.6
         * @return      array
         */
        protected function _getShortcodeArgumentKeysSanitized( array $aUnitOptions, array $aShortcodeArgumentKeys ) {
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
     * @return mixed
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