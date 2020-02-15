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
 * Creates a widget by unit.
 * 
 * @since   3
 */
class AmazonAutoLinks_ContextualProductWidget extends AmazonAutoLinks_AdminPageFramework_Widget {
    
    /**
     * The user constructor.
     * 
     * Alternatively you may use start_{instantiated class name} method.
     */
    public function start() {
    }
    
    /**
     * Sets up arguments.
     * 
     * Alternatively you may use set_up_{instantiated class name} method.
     */
    public function setUp() {

        $this->setArguments( 
            array(
                'description'   =>  __( 'Displays Amazon product links contextually related to the current page contents.', 'amazon-auto-links' ),
            ) 
        );
        
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
        add_filter( 'style_' . $this->oProp->sClassName, array( $this, 'replyToModifyCSSRules' ) );
        
        add_filter( 'options_' . $this->oProp->sClassName, array( $this, 'replyToSetDefaultOptions' ) );        
        
        
    }    
        /**
         * @return      string
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
                ';
            
        }
        
        /**
         * Sets the default options.
         * @return      array
         * @since       3.3.0
         */
        public function replyToSetDefaultOptions( $aOptions ) {          
            $_aDefaults = apply_filters( 'aal_filter_default_unit_options_search', array() );
            return $aOptions + $_aDefaults;
        }
        

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load() {
                
        $_oOption       = AmazonAutoLinks_Option::getInstance();
        $_bAPIConnected = $_oOption->isAPIConnected();
        if ( ! $_bAPIConnected ) {
            $this->addSettingField(
                array(
                    'field_id'    => '_message_dummy_id',
                    'type'        => '_message',
                    'description' => array(
                        sprintf( 
                            __( 'Please set up API keys first from <a href="%1$s">this page</a>.', 'amazon-auto-links' ),
                            AmazonAutoLinks_PluginUtility::getAPIAuthenticationPageURL()
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
//            'AmazonAutoLinks_FormFields_Unit_Locale', // cannot support this yet as it needs to know the selected locale in advance
            'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
            'AmazonAutoLinks_FormFields_Button_Selector',
            'AmazonAutoLinks_FormFields_Unit_Cache',
            // 'AmazonAutoLinks_FormFields_Unit_Template', @deprecated 4.0.0
            'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport',   // 4.0.0+
            'AmazonAutoLinks_FormFields_Widget_Visibility',
        );
        $this->_addFieldsByFieldClass( $_aClasses );
        
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
        $this->_addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_ProductFilter',
                'AmazonAutoLinks_FormFields_ProductFilter_Image',
            )
        );

        // Add fields
        $this->addSettingFields( '_default' );  // Set the target section.
        $this->_addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_ProductFilterAdvanced',
            )
        );

        add_filter( 'field_definition_' . $this->oProp->sClassName . '_button_id', array( $this, 'replyToSetActiveButtonLabels' ) );
        
    }
        /**
         * Modifies the 'button_id' field to add lables for selection.
         * @return      array
         * @since       3.3.0
         */
        public function replyToSetActiveButtonLabels( $aFieldset ) {
            
            $aFieldset[ 'label' ] = $this->_getActiveButtonLabelsForFields();
            return $aFieldset;
            
        }
            /**
             * @return      array
             * @since       3.3.0
             */
            private function _getActiveButtonLabelsForFields() {
                
                static $_aCache;
                
                if ( isset( $_aCache ) ) {
                    return $_aCache;
                }
                
                $_aButtonIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDs();
                $_aLabels    = array();
                foreach( $_aButtonIDs as $_iButtonID ) {
                    $_aLabels[ $_iButtonID ] = get_the_title( $_iButtonID )
                        . ' - ' . get_post_meta( $_iButtonID, 'button_label', true );
                }
                $_aCache = $_aLabels;           
                return $_aCache;
                
            }
            
        /**
         * Adds form fields by the given class names.
         * @since       3.0.3
         * @return      void
         */
        private function _addFieldsByFieldClass( $aClassNames ) {     
            foreach( $aClassNames as $_sClassName ) {            
                $_oFields = new $_sClassName;
                $_aFields = 'AmazonAutoLinks_FormFields_Unit_Template_EachItemOptionSupport' === $_sClassName
                    ? $_oFields->get( '', 'contextual_widget' )
                    : $_oFields->get();
                foreach( $_aFields as $_aField ) {
                    $this->_addField( $_aField );
                }
            }            
        }
            /**
             * @since       3.1.2
             */
            private function _addField( $aField ) {
                
                $_sFieldID = $this->oUtil->getElement( $aField, 'field_id' );
                if ( $this->oUtil->hasSuffix( 'available_page_types', $_sFieldID ) ) {
                    $this->oUtil->setMultiDimensionalArray( $aField, array( 'default', 'search' ) , true );
                }
                
                $this->addSettingFields( $aField );
            }
    
    /**
     * Validates the submitted form data.
     * 
     * @callback        filter      validation_{instantiated class name}
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // When the user does not set the API keys, an empty widget form will be rendered and thus inputs will be empty.
        if ( empty( $aSubmit ) ) {
            return $aSubmit;
        }

        // @fixed 3.6.0 Not sure why {...}_search was used here. It should be {...}_contextual
//         $_aDefaults = apply_filters( 'aal_filter_default_unit_options_search', array() );

        $_aDefaults = apply_filters( 'aal_filter_default_unit_options_contextual', array() );
        $aSubmit    = array( 'unit_type' => 'contextual' )  // 3.6.0+ for Ajax unit loading
            + $aSubmit
            + $_aDefaults;

        // Sanitize the form inputs.
        $_oItemFormatValidator = new AmazonAutoLinks_FormValidator_ItemFormat( $aSubmit, $aStored );
        $aSubmit    = $_oItemFormatValidator->get();      
        return $aSubmit;
        
    }        
    
    /**
     * Print out the contents in the front-end.
     * 
     * @callback        filter      content_{instantiated class name}
     */
    public function content( $sContent, $aArguments, $aFormData ) {

//$sContent = $sContent
//    . '<h3>Testing</h3>'
//    . "<div>" . AmazonAutoLinks_Debug::get( $aFormData ) . "</div>";

        $aFormData = $this->_getFormattedFormData( $aFormData );        
        if ( 
            ! in_array( 
                AmazonAutoLinks_PluginUtility::getCurrentPageType(), 
                $aFormData[ 'available_page_types' ] 
            )
        ) {
            $this->oProp->bShowWidgetTitle = false;
            return $sContent;
        }        

        // @todo 3.6.0+ Echo JavaScript loading specific outputs.
//        $_bLoadAsJS = $this->getElement( $aFormData, array( 'load_with_javascript' ) );

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
         * 
         * @return      array
         */
        private function _getFormattedFormData( array $aFormData ) {
            $aFormData = $aFormData + array(
                'title'                     => null,
                'show_title_on_no_result'   => false,
                'criteria'                  => array(
                    'post_title'            => null,
                    'breadcrumb'            => null,
                    'taxonomy_terms'        => null,
                ),
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
            
            // $_oOption = AmazonAutoLinks_Option::getInstance();
            $aFormData[ 'show_errors' ] = false; // $_oOption->isDebug();
            
            return $aFormData;
            
        }        
        
        /**
         * Returns the output of the widget.
         * @return      string
         */
        private function _getOutput( $aFormData ) {
            $aFormData[ 'unit_type' ] = 'contextual';
            return AmazonAutoLinks( $aFormData, false );
        }
       
}