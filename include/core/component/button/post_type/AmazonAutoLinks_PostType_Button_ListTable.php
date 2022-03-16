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
 * A base class for the button post type, overriding list table methods.
 * 
 * @since 3
 */
abstract class AmazonAutoLinks_PostType_Button_ListTable extends AmazonAutoLinks_AdminPageFramework_PostType {
    
    /**
    * Defines the column header of the unit listing table.
    * 
    * @callback add_filter() columns_{post type slug}
    * @return   array
    */
    public function replyToModifyColumnHeader( $aHeaderColumns ) {    
        return array(
            'cb'                    => '<input type="checkbox" />',   
            'title'                 => __( 'Label', 'amazon-auto-links' ),
            'status'                => __( 'Status', 'amazon-auto-links' ),
            'shortcode'             => __( 'Shortcode', 'amazon-auto-links' ),
            'preview'               => __( 'Preview', 'amazon-auto-links' ),
        );                      
    }

    /**
     * @callback add_filter() cell_{post type slug}_{column slug}
     * @return   string
     */
    public function cell_aal_button_shortcode( $sCell, $iPostID ) {
        $_sShortcode = AmazonAutoLinks_Registry::$aShortcodes[ 'button' ];
        $_sLabel     = __( 'Buy Now', 'amazon-auto-links' );
        return $sCell . "<p class='shortcode'>"
                . "<span>"
                . "[{$_sShortcode} id='{$iPostID}' asin='1118987241' label='{$_sLabel}']"
               . "</span>"
            . "</p>";
    }

    /**
     * @callback add_filter() cell_{post type slug}_{column slug}
     * @return   string
     */
    public function cell_aal_button_preview( $sCell, $iPostID ) {
        // Only load the first iframe, and let other frames being loaded by the script to reduce the load
        $_aFrameAttributes = $this->oUtil->hasBeenCalled( __METHOD__ )
            ? array( 'src' => null )
            : array();
        return $sCell
            . AmazonAutoLinks_Button_Utility::getIframeButtonPreview(
                $iPostID,
                '_by_id',
                null,
                $_aFrameAttributes + array(
                    'id'  => 'iframe-button-preview-' . $iPostID,
                )
            );
    }

}