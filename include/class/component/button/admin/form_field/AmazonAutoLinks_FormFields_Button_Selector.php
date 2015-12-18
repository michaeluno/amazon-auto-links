<?php
/**
 * Provides the form fields definitions.
 * 
 * @since           3  
 */
class AmazonAutoLinks_FormFields_Button_Selector extends AmazonAutoLinks_FormFields_Base {

    /**
     * Returns field definition arrays.
     * 
     * Pass an empty string to the parameter for meta box options. 
     * 
     * @return      array
     */    
    public function get( $sFieldIDPrefix='', $sUnitType='category' ) {
        
        $_aFields       = array(
            array(
                'field_id'          => $sFieldIDPrefix . 'button_id',
                'type'              => 'select',            
                'title'             => __( 'Select Button', 'amazon-auto-links' ),
                'class'             => array(
                    'fieldrow'      => 'button-select-row',
                ),   
                'tip'               => sprintf(
                    __( 'Select the button for the <code>%1$s</code> parameter of the Item Format option.', 'amazon-auto-links' ),
                    '%button%'
                ),
                'description'       => array(
                    sprintf(
                        __( 'Buttons can be created from <a href="%1$s">this screen</a>.', 'amazon-auto-links' ),
                        add_query_arg(
                            array(
                                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'button' ],
                            ),
                            admin_url( 'edit.php' )
                        )
                    ),                    
                ),
                // The label argument will be set with the 'field_definition_{...}' filter as it performs a database query.
                // 'label'             => $this->_getActiveButtonLabels(),
                'after_field'       => "<div style='margin: 3em 3em 3em 0; width:100%;'>"
                    . "<div style='margin-left: auto; margin-right: auto; '>" // text-align:center;
                        . AmazonAutoLinks_PluginUtility::getButton( 
                            '__button_id__', 
                            '',     // label - use default by passing an empty string
                            false   // hidden - the script will make it visible
                        )
                    . "</div>"
                . "</div>"
            ),
            array(
                'field_id'          => $sFieldIDPrefix . 'button_type',
                'type'              => 'radio',            
                'title'             => __( 'Button Type', 'amazon-auto-links' ),            
                'label'             => array(
                    0   => __( 'Link to the product page.', 'amazon-auto-links' ),
                    1   => __( 'Add to cart.', 'amazon-auto-links' ),
                ),
                'default'           => 1,
            ), 
        );
       
        return $_aFields;
        
    }
        /**
         * @return      array
         */
        private function _getActiveButtonLabels() {
            
            $_aButtonIDs = AmazonAutoLinks_PluginUtility::getActiveButtonIDs();
            $_aLabels    = array();
            foreach( $_aButtonIDs as $_iButtonID ) {
                $_aLabels[ $_iButtonID ] = get_the_title( $_iButtonID )
                    . ' - ' . get_post_meta( $_iButtonID, 'button_label', true );
            }
            return $_aLabels;
            
        }
}