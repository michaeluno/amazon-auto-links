<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Adds the 'Data' form section to the 'Reset' tab.
 *
 * Handles import and export options.
 * 
 * @since       3.6.6
 */
class AmazonAutoLinks_AdminPage_Setting_Reset_Data extends AmazonAutoLinks_AdminPage_Section_Base {
    
    /**
     * A user constructor.
     * 
     * @since       3.6.6
     * @return      void
     */
    protected function construct( $oFactory ) {
    }
    
    /**
     * Adds form fields.
     * @since       3.6.6
     * @return      void
     */
    public function addFields( $oFactory, $sSectionID ) {

       $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array( 
                'field_id'          => 'export',
                'title'             => __( 'Export Options', 'amazon-auto-links' ),
                'type'              => 'export',
            ),
            array(
                'field_id'          => 'import',
                'title'             => __( 'Import Options', 'amazon-auto-links' ),
                'type'              => 'import',
                'value'             => __( 'Import', 'amazon-auto-links' ),
            )           
        );          

    }

}