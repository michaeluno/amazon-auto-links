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
            ) 
        );
    
    }    

    /**
     * Sets up the form.
     * 
     * Alternatively you may use load_{instantiated class name} method.
     */
    public function load( $oAdminWidget ) {
        
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
                'label'         => $this->_getUnitLabels(),
                'description'   => __( 'Hold down the Ctrl (windows) / Command (Mac) key to select multiple items.', 'amazon-auto-links' )
            )
           
        );        

        
    }
        /**
         * @return  array
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
        
        // Uncomment the following line to check the submitted value.
        // AdminPageFramework_Debug::log( $aSubmit );
        
        return $aSubmit;
        
    }    
    
    /**
     * Print out the contents in the front-end.
     * 
     * Alternatively you may use the content_{instantiated class name} method.
     */
    public function content( $sContent, $aArguments, $aFormData ) {
        
        return $sContent
            . AmazonAutoLinks( 
                $aFormData, 
                false // echo or output
            )
            ;
    
    }
        
}