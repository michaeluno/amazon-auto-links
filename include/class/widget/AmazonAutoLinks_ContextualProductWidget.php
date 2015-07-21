<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
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
                    'attributes'  => array(
                        'name' => '',
                    ),
                )
            );
            return;
        }
        
        $_aClasses = array(
            'AmazonAutoLinks_FormFields_Widget_ContxtualProduct',
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
                'section_id' => 'product_filters',
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
        
    }
        /**
         * Adds form fields by the given class names.
         * @since       3.0.3
         * @return      void
         */
        private function _addFieldsByFieldClass( $aClassNames ) {     
            foreach( $aClassNames as $_sClsssName ) {            
                $_oFields = new $_sClsssName;
                foreach( $_oFields->get() as $_aField ) {
                    $this->addSettingFields( $_aField );
                }
            }            
        }
    
    /**
     * Validates the submitted form data.
     * 
     * @callback        filter      validation_{instantiated class name}
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        
        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );
        
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
            $_sSearchKeywords   = $_oContextualSearch->get();           
            if ( ! $_sSearchKeywords ) {
                return '';
            }

            return AmazonAutoLinks( 
                array( 
                    'Keywords'         => $_sSearchKeywords,
                    'Operation'     => 'ItemSearch',
                )
                + $aFormData, 
                false // echo or output
            );

        }
       
}