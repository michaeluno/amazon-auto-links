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
 * Adds the 'Capability' form section to the 'Misc' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_Reset_RestSettings extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'reset_settings',
            'title'         => __( 'Reset Settings', 'amazon-auto-links' ),
            'description'   => array(
                __( 'If you get broken options, initialize them by performing reset.', 'amazon-auto-links' ),
            ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {
             
        // reset_{instantiated class name}_{section id}_{field id}
        add_action( 
            "reset_{$oFactory->oProp->sClassName}_{$this->sSectionID}_all",
            array( $this, 'replyToResetOptions' ), 
            10, // priority
            4 // number of parameters
        );
        
    }
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

       $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array( 
                'field_id'          => 'all',
                'title'             => __( 'Restore Default', 'amazon-auto-links' ),
                'type'              => 'submit',
                'reset'             => true,
                'value'             => __( 'Restore', 'amazon-auto-links' ),
                'tip'               => __( 'Restore the default options.', 'amazon-auto-links' ),
                'attributes'        => array(
                    'size'          => 30,
                ),
            ),
            array(
                'field_id'          => 'reset_on_uninstall',
                'title'             => __( 'Delete Options upon Uninstall', 'amazon-auto-links' ),
                'type'              => 'checkbox',
                'label'             => __( 'Delete options and caches when the plugin is uninstalled.', 'amazon-auto-links' ),
            )           
        );          
            
    
    
    }
        
    public function replyToResetOptions( $asKeyToReset, $aInput, $oFactory, $aSubmitInfo ) {
        
        // Delete the template options as well.
        delete_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'template' ]
        );
        
        // Button options
        delete_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'button_css' ]
        );
        
        // Last inputs
        delete_option(
            AmazonAutoLinks_Registry::$aOptionKeys[ 'last_input' ]
        );
    
    }
    
   
}