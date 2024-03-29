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
 * @since       3
 * @remark      The class name is important as it is used as the widget ID. 
 * And the name `AmazonAutoLinks_WidgetByID` is also used as the widget ID in v2.
 */
class AmazonAutoLinks_WidgetByID extends AmazonAutoLinks_AdminPageFramework_Widget {
    
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
                'description'   =>  __( 'Displays Amazon product unit outputs.', 'amazon-auto-links' ),
                'classname'     => 'widget-aal-unit-by-id amazon-auto-links-widget admin_page_framework_widget'
            ) 
        );
    
    }    

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load() {
        
        $this->addSettingFields(
            array(
                'field_id'      => 'title',
                'type'          => 'text',
                'title'         => __( 'Title', 'amazon-auto-links' ),
            ),
            array(
                'field_id'      => 'id',
                'type'          => 'select',
                'title'         => __( 'Units', 'amazon-auto-links' ),
                'is_multiple'   => true,
                'label'         => array(), // will be reassigned in a callback
                // 'label'         => $this->_getUnitLabels(),
                'description'   => __( 'Hold down the Ctrl (windows) / Command (Mac) key to select multiple items.', 'amazon-auto-links' ),
                'attributes'    => array(
                    'select'    => array(
                        'size'  => 10,
                    ),
                ),
            )
        );        

        // Additional fields 
        $this->_addFieldsByFieldClass(
            array(
                'AmazonAutoLinks_FormFields_Widget_Visibility',
            )
        );  

        add_filter( 'field_definition_' . $this->oProp->sClassName  . '_id', array( $this, 'replyToSetUnitLabels' ) );
        
    }
        /**
         * @return      array
         * @since       3.3.0
         */
        public function replyToSetUnitLabels( $aFieldset ) {
            $aFieldset[ 'label' ] = $this->_getUnitLabels();
            return $aFieldset;
        }
    
        /**
         * Adds form fields by the given class names.
         * @since 3.0.5
         * @param array $aClassNames
         */
        private function _addFieldsByFieldClass( $aClassNames ) {    
        
            foreach( $aClassNames as $_sClassName ) {
                $_oFields = new $_sClassName( $this );
                foreach( $_oFields->get() as $_aField ) {
                    
                    $_aField = $_aField + array(
                       'field_id' => null,
                    );
                    
                    // Modify a field 
                    if ( 'available_page_types' === $_aField[ 'field_id' ] ) {
                        $_aField[ 'default' ] = array(
                            'home'              => true,
                            'front'             => true,
                        ) + $_aField[ 'default' ];
                    }
                    
                    // Add the iterating field
                    $this->addSettingFields( $_aField );
                    
                }
            }            
        }
        
        /**
         * @return array
         */
        private function _getUnitLabels() {
            return AmazonAutoLinks_PluginUtility::getPostsLabelsByPostType(
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
            );            
        }
    
    /**
     * Validates the submitted form data.
     * 
     * Alternatively you may use validation_{instantiated class name} method.
     */
    public function validate( $aSubmit, $aStored, $oAdminWidget ) {
        return $aSubmit;
    }    
    
    /**
     * Print out the contents in the front-end.
     * 
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {

        $aFormData = $this->___getFormattedFormData( $aFormData );

        if ( ! $this->___shouldRender( $aFormData ) ) {
            $this->oProp->bShowWidgetTitle = false;
            return $sContent;
        }                

        // Store widget instance information so that the output function knows what to do with JavaScript loading.
        $aFormData[ '_widget_option_name' ] = $this->oProp->oWidget->option_name;
        $aFormData[ '_widget_number' ]      = $this->oProp->oWidget->number;

        // [4.6.8] Search units have the `Title` argument and this causes a conflict in WordPress 5.8
        unset( $aFormData[ 'title' ] );

        return $sContent . apply_filters( 'aal_filter_output', '', $aFormData );
    
    }
        /**
         * @param  array $aFormData
         * @return boolean
         * @since  4.6.8
         */
        private function ___shouldRender( $aFormData ) {
            if ( AmazonAutoLinks_Widget_Utility::isInWidgetPreview() ) {
                return true;
            }
            return in_array(
                AmazonAutoLinks_PluginUtility::getCurrentPageType(),
                $aFormData[ 'available_page_types' ]
            );
        }
        /**
         * 
         * @since       3.0.5
         * @return      array
         */
        private function ___getFormattedFormData( array $aFormData ) {
            $aFormData = $aFormData + array(
                'title'                     => null,
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

            return $aFormData;
            
        }        
    
}