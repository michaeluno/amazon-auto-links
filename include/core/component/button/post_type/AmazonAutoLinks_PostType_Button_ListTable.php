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
 * Defines button post type.
 * 
 * @package     Amazon Auto Links
 * @since       3
 */
abstract class AmazonAutoLinks_PostType_Button_ListTable extends AmazonAutoLinks_AdminPageFramework_PostType {
    
    /**
    * Defines the column header of the unit listing table.
    * 
    * @callback     filter      columns_{post type slug}
    * @return       array
    */
    public function replyToModifyColumnHeader( $aHeaderColumns ) {    
        return array(
            'cb'                    => '<input type="checkbox" />',   
            'title'                 => __( 'Label', 'amazon-auto-links' ),    
            'preview'               => __( 'Preview', 'amazon-auto-links' ),
            // 'template'              => __( 'Template', 'amazon-auto-links' ),
            // 'code'                  => __( 'Shortcode / PHP Code', 'amazon-auto-links' ),
        );                      
    }    
        
    /**
     * 
     * @callback        filter      cell_{post type slug}_{column slug}
     */
    public function cell_aal_button_preview( $sCell, $iPostID ) {
        
        return $sCell 
            . AmazonAutoLinks_PluginUtility::getButton(
                $iPostID
            );
        
    }
        
}