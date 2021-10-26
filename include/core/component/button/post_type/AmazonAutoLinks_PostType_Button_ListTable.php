<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Defines button post type.
 * 
 * @package     Auto Amazon Links
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
            'status'                => __( 'Status', 'amazon-auto-links' ),
            'shortcode'             => __( 'Shortcode', 'amazon-auto-links' ),
            'preview'               => __( 'Preview', 'amazon-auto-links' ),
            // 'template'              => __( 'Template', 'amazon-auto-links' ),
            // 'code'                  => __( 'Shortcode / PHP Code', 'amazon-auto-links' ),
        );                      
    }

    /**
     * @callback        filter      cell_{post type slug}_{column slug}
     * @return string
     */
    public function cell_aal_button_shortcode( $sCell, $iPostID ) {
        $_sShortcode = AmazonAutoLinks_Registry::$aShortcodes[ 'button' ];
        $_sLabel     = __( 'Buy Now', 'amazon-auto-links' );
        return $sCell . "<p class='shortcode'>"
                . "<span>"
                . "[{$_sShortcode} id='{$iPostID}' asin='1118987241' label='{$_sLabel}']"
               . "</span>"
//               . "<span class='description'> <i>* " . __( 'Change the <code>asin</code> parameter value to your desired product ASIN.', 'amazon-auto-links' ) . "</i></p>"
            . "</p>";
    }

    /**
     * @return string
     * @callback        filter      cell_{post type slug}_{column slug}
     */
    public function cell_aal_button_preview( $sCell, $iPostID ) {
        return $sCell 
            . AmazonAutoLinks_PluginUtility::getButton(
                $iPostID
            );
    }
        
}