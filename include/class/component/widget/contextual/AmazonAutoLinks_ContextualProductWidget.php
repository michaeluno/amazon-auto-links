<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
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
    public function load( $oAdminWidget ) {
                
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
            'AmazonAutoLinks_FormFields_Widget_ContxtualProduct',
            'AmazonAutoLinks_FormFields_Unit_Common',
            'AmazonAutoLinks_FormFields_Unit_CommonAdvanced',
            'AmazonAutoLinks_FormFields_Button_Selector',
            'AmazonAutoLinks_FormFields_Unit_Cache',
            'AmazonAutoLinks_FormFields_Unit_Template',
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
      
        // Set the target section.
        $this->addSettingFields( 'product_filters' );
        
        // Add fields 
        $this->_addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_ProductFilter',
                'AmazonAutoLinks_FormFields_ProductFilter_Image',
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
            foreach( $aClassNames as $_sClsssName ) {            
                $_oFields = new $_sClsssName;
                $_aFields = 'AmazonAutoLinks_FormFields_Unit_Template' === $_sClsssName
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

        $_aDefaults = apply_filters( 'aal_filter_default_unit_options_search', array() );
        $aSubmit    = $aSubmit + $_aDefaults;
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

        $_sOutput = $this->_getOutput( $aFormData, $aArguments );
        if ( ! $_sOutput && ! $aFormData[ 'show_title_on_no_result' ] ) {
            $this->oProp->bShowWidgetTitle = false;
        }
        
        return $sContent
            . $_sOutput;
    
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
        private function _getOutput( $aFormData, $aArguments ) {

            $_oContextualSearch = new AmazonAutoLinks_ContextualProductWidget_SearchKeyword(
                $aFormData[ 'criteria' ], 
                $aFormData[ 'additional_keywords' ]
            );
            $_aSearchKeywords   = $_oContextualSearch->get(); // get as an array
            if ( empty( $_aSearchKeywords ) ) {
                return '';
            }
            
            shuffle ( $_aSearchKeywords );
            array_splice( $_aSearchKeywords, 5 );   // up to 5 keywords.

            return AmazonAutoLinks( 
                array( 
                    'Keywords'         => implode( ',', $_aSearchKeywords ),
                    'Operation'        => 'ItemSearch',
                    
                    /**
                     * Fixed a bug that contextual widgets did not return outputs
                     * due to the form data was having the value of `category` for the `unit_type` argument.
                     * This was because the unit option formatter class did not set the correct `unit_type` in the class,
                     * which has been fixed in 3.4.7.
                     * 
                     * So setting the value here is a workaround to keep backward compatibility.
                     * @since       3.4.7
                     */
                    'unit_type'        => 'search',
                    
                    // The `Power` parameter will not be used as it only works with the Books category.
                    
                    // 3.1.4+   By default the given comma-delimited multiple keywords such as `PHP,WordPress` are searched in one query.
                    // The Amazon API does not provide an OR operator for the Keywords parameter. Power cannot be used for categories other than Books.
                    // So here we set a plugin specific option here to perform search by each keyword.
                    'search_per_keyword'    => true,
                )
                + $aFormData, 
                false // echo or output
            );

        }
       
}