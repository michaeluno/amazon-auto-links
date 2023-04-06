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
 * Creates a widget by unit.
 * 
 * @since 3
 */
class AmazonAutoLinks_ContextualProductWidget extends AmazonAutoLinks_AdminPageFramework_Widget {
    
    /**
     * The user constructor.
     * 
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {}
    
    /**
     * Sets up arguments.
     * 
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments( 
            array(
                'description'   =>  __( 'Displays Amazon product links contextually related to the current page contents.', 'amazon-auto-links' ),
                'classname'     => 'widget-aal-contextual amazon-auto-links-widget admin_page_framework_widget',
            ) 
        );
        
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
        add_filter( 'style_' . $this->oProp->sClassName, array( $this, 'replyToModifyCSSRules' ) );
        add_filter( 'options_' . $this->oProp->sClassName, array( $this, 'replyToSetDefaultOptions' ) );        
        
        
    }    
        /**
         * @param  string $sCSSRules
         * @return string
         */
        public function replyToModifyCSSRules( $sCSSRules ) {
            
            return $sCSSRules
                . '
.links-style-label code,    
.links-style-label > div {
    font-size: 0.8em;
}                
.widget .amazon-auto-links-input-label-container {
    width: auto;
}
.widget form-table .amazon-auto-links-section-table,
.widget .amazon-auto-links-sectionset, 
.widget .amazon-auto-links-section {
    margin-bottom: 0;
}
.widget .amazon-auto-links-section-title > h3 {
    margin: 0.2em 0;
}
.widget .amazon-auto-links-field-text .amazon-auto-links-input-label-container {
    width: 98%;
}
                ';
            
        }
        
        /**
         * Sets the default options.
         * @param  array $aOptions
         * @return array
         * @since  3.3.0
         */
        public function replyToSetDefaultOptions( $aOptions ) {
            $_aDefaults = apply_filters( 'aal_filter_default_unit_options_search', array() );
            return $aOptions + $_aDefaults;
        }


    /**
     * @since 4.5.0
     */
    public function getValue() {
        $_aParameters = func_get_args();
        if ( empty( $_aParameters ) ) {
            return $this->___getWidgetOptions();
        }
        if ( is_array( $_aParameters[ 0 ] ) ) {
            return $this->oUtil->getElement( $this->___getWidgetOptions(), $_aParameters[ 0 ], $this->oUtil->getElement( $_aParameters, 1 ) );
        }
        return $this->oUtil->getElement( $this->___getWidgetOptions(), $_aParameters );
    }

    /**
     * @return array
     * @since  4.5.0
     */
    private function ___getWidgetOptions() {
        $_aCache = AmazonAutoLinks_PluginUtility::getObjectCache( __METHOD__ . $this->oProp->oWidget->id );
        if ( null !== $_aCache ) {
            return $_aCache;
        }
        $_aOptions = $this->oUtil->getElementAsArray(
            $this->oProp->oWidget->get_settings(),
            $this->oProp->oWidget->number
        );
        AmazonAutoLinks_PluginUtility::setObjectCache( __METHOD__ . $this->oProp->oWidget->id, $_aOptions );
        return $_aOptions;
    }

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load() {

        if ( $this->___shouldShowPAAPIRequirement() ) {
            $this->addSettingField(
                array(
                    'field_id'    => '_message_dummy_id',
                    'type'        => '_message',
                    'description' => array(
                        sprintf(
                            __( 'Please set up API keys first from <a href="%1$s">this page</a>.', 'amazon-auto-links' ),
                            AmazonAutoLinks_PluginUtility::getAPIAuthenticationPageURL( array( 'locale' => $this->getValue( 'country' ) ) )
                        ),
                    ),
                    'save'        => false,
                    'attributes'  => array(
                        'name' => '',
                    ),
                )
            );
            return;
        }

        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Widget_ContextualProduct',
            'AmazonAutoLinks_FormFields_Unit_Common',
            // 'AmazonAutoLinks_FormFields_Unit_Locale', // cannot support this yet as it needs to know the selected locale in advance
            'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
            // 'AmazonAutoLinks_FormFields_Button_Selector',    // @deprecated 5.2.0 Changed to `AmazonAutoLinks_FormFields_Unit_Button`.
            'AmazonAutoLinks_FormFields_Unit_Button',
            'AmazonAutoLinks_FormFields_Unit_Cache',
            // 'AmazonAutoLinks_FormFields_Unit_Template', @deprecated 4.0.0
            'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport',   // 4.0.0+
            'AmazonAutoLinks_FormFields_Widget_Visibility',
        );
        $this->___addFieldsByFieldClass( $_aClasses );

        // Register custom filed type.
        new AmazonAutoLinks_RevealerCustomFieldType( $oFactory->oProp->sClassName );

        // Product filters
        $this->addSettingSections(
            array(
                'title'         => __( 'Product Filters', 'amazon-auto-links' ),
                'section_id'    => 'product_filters',
                'collapsible'   => array(
                    'collapsed' => true,
                    'type'      => 'button',
                    'container' => 'section',
                ),
            )
        );

        // Add fields
        $this->addSettingFields( 'product_filters' );   // Set the target section.
        $this->___addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_ProductFilter',
                'AmazonAutoLinks_FormFields_ProductFilter_Image',
            )
        );

        // Add fields
        $this->addSettingFields( '_default' );  // Set the target section.
        $this->___addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_Unit_ProductFilterAdvanced',
            )
        );

        add_filter( 'field_definition_' . $this->oProp->sClassName . '_button_id', array( $this, 'replyToSetActiveButtonLabels' ) );

    }

        /**
         * @return boolean
         * @since  5.0.0
         */
        private function ___shouldShowPAAPIRequirement() {
            $_sLocale          = $this->getValue( 'country' );
            if ( ! $_sLocale ) {
                return false;
            }
            $_aAdWidgetLocales = AmazonAutoLinks_Locales::getLocalesWithAdWidgetAPISupport();
            if ( in_array( $_sLocale, $_aAdWidgetLocales, true ) ) {
                return false;
            }
            $_oOption          = AmazonAutoLinks_Option::getInstance();
            return ! $_oOption->isPAAPIKeySet( $_sLocale );
        }
        /**
         * Modifies the 'button_id' field to add lables for selection.
         * @param  array $aFieldset
         * @return array
         * @since  3.3.0
         */
        public function replyToSetActiveButtonLabels( $aFieldset ) {
            $aFieldset[ 'label' ] = AmazonAutoLinks_PluginUtility::getActiveButtonLabelsForFields();
            return $aFieldset;
        }

        /**
         * Adds form fields by the given class names.
         * @since 3.0.3
         * @param array $aClassNames
         */
        private function ___addFieldsByFieldClass( $aClassNames ) {     
            foreach( $aClassNames as $_sClassName ) {            
                $_oFields = new $_sClassName( $this );
                $_aFields = 'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport' === $_sClassName
                    ? $_oFields->get( '', 'contextual_widget' )
                    : $_oFields->get();
                foreach( $_aFields as $_aField ) {
                    // [5.0.7] Drop the required attribute as browsers do not show the pop-up notice for widget forms.
                    // And without the required field to be filled, the Save action does not start and the user just will think it is broken.
                    if ( isset( $_aField[ 'attributes' ][ 'required' ] ) ) {
                        unset( $_aField[ 'attributes' ][ 'required' ] );
                    }
                    $this->_addField( $_aField, $_sClassName );
                }
            }            
        }

            /**
             * @param array  $aField     A fieldset definition array.
             * @param string $sClassName The name of the class that defines the fieldset.
             * @since 3.1.2
             * @since 4.3.0  Added the `$sClassName` parameter.
             */
            private function _addField( $aField, $sClassName ) {

                // 4.3.0
                if (
                    'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport' === $sClassName
                    && 'custom_text' === $this->oUtil->getElement( $aField, array( 'field_id' ) )
                ) {
                    $aField[ 'rich' ] = false;
                }

                $_sFieldID = $this->oUtil->getElement( $aField, 'field_id' );
                if ( $this->oUtil->hasSuffix( 'available_page_types', $_sFieldID ) ) {
                    $this->oUtil->setMultiDimensionalArray( $aField, array( 'default', 'search' ) , true );
                }
                
                $this->addSettingFields( $aField );
            }

    /**
     * Validates the submitted form data.
     *
     * @callback add_filter() validation_{instantiated class name}
     * @param    array        $aSubmit
     * @param    array        $aStored
     * @param    AmazonAutoLinks_AdminPageFramework_Widget $oAdminWidget
     * @return   array
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // When the user does not set the API keys, an empty widget form will be rendered and thus inputs will be empty.
        if ( empty( $aSubmit ) ) {
            return $aSubmit;
        }

        $_aDefaults = apply_filters( 'aal_filter_default_unit_options_contextual', array() );
        $aSubmit    = array( 'unit_type' => 'contextual' )  // 3.6.0+ for Ajax unit loading
            + $aSubmit
            + $_aDefaults;

        // Sanitize the form inputs.
        $_oItemFormatValidator = new AmazonAutoLinks_FormValidator_ItemFormat( $aSubmit, $aStored );
        $aSubmit    = $_oItemFormatValidator->get();

        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_contextual( null, $aSubmit );
        return $_oUnitOption->get();

    }        
    
    /**
     * Print out the contents in the front-end.
     * 
     * @callback add_filter() content_{instantiated class name}
     */
    public function content( $sContent, $aArguments, $aFormData ) {

        $aFormData = $this->_getFormattedFormData( $aFormData );        
        if (
            ! AmazonAutoLinks_Widget_Utility::isInWidgetPreview()
            &&
            ! in_array( 
                AmazonAutoLinks_PluginUtility::getCurrentPageType(), 
                $aFormData[ 'available_page_types' ] 
            )
        ) {
            $this->oProp->bShowWidgetTitle = false;
            return $sContent;
        }        

        // @todo 3.6.0+ Echo JavaScript loading specific outputs.
        // $_bLoadAsJS = $this->getElement( $aFormData, array( 'load_with_javascript' ) );

        // Store widget instance information so that the output function knows what to do with JavaScript loading.
        $aFormData[ '_widget_option_name' ] = $this->oProp->oWidget->option_name;
        $aFormData[ '_widget_number' ] = $this->oProp->oWidget->number;

        $_sOutput = $this->_getOutput( $aFormData );
        if ( ! $_sOutput && ! $aFormData[ 'show_title_on_no_result' ] ) {
            $this->oProp->bShowWidgetTitle = false;
        }
        return $sContent . $_sOutput;
    
    }
        /**
         * @param   array $aFormData
         * @return  array
         */
        private function _getFormattedFormData( array $aFormData ) {
            $aFormData = $aFormData + array(
                'title'                     => null,
                'show_title_on_no_result'   => false,
                'criteria'                  => array(
                    'post_title'            => null,
                    'breadcrumb'            => null,
                    'taxonomy_terms'        => null,
                    'site_title'            => null,
                    'url_query'             => null,
                    'post_meta'             => null,
                ),
                'http_query_parameters'     => array(),
                'additional_keywords'       => '',
                'excluding_keywords'        => '',  // 3.12.0
                'width'                     => 100,
                'width_unit'                => '%',
                'height'                    => 400,
                'height_unit'               => 'px',
                'available_page_types'      => array(
                    'home'              => true,
                    'front'             => true,                
                    'singular'          => true,
                    'post_type_archive' => false,
                    'taxonomy'          => false,
                    'date'              => false,
                    'author'            => false,
                    'search'            => false,
                    '404'               => false,
                ),
            );
            $aFormData[ 'available_page_types' ] = array_keys( 
                array_filter( $aFormData[ 'available_page_types' ] ) 
            );

            // 4.2.7 - the widget form does not support currency and language.
            // For cases that the user have saved the form before v4.2.4 may have incorrect values of them.
            // Let the unit option class to format those arguments.
            unset(
                $aFormData[ 'preferred_currency' ],
                $aFormData[ 'language' ]
            );

            return $aFormData;

        }

        /**
         * Returns the output of the widget.
         *
         * @param  array  $aFormData
         * @return string
         */
        private function _getOutput( $aFormData ) {
            $aFormData[ 'unit_type' ] = 'contextual';
            // [4.6.8] Search units have the `Title` argument and this causes a conflict in WordPress 5.8
            unset( $aFormData[ 'title' ] );
            return apply_filters( 'aal_filter_output', '', $aFormData );
        }
       
}