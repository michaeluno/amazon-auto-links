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
        return $sCell
            // @deprecated 5.2.0 Uses iframes to display button previews
            // . AmazonAutoLinks_PluginUtility::getButton( $iPostID )
            . $this->___getButtonPreviewFrame( $iPostID );
    }
        /**
         * @param  integer $iButtonID
         * @return string
         * @since  5.2.0
         */
        private function ___getButtonPreviewFrame( $iButtonID ) {
            $_aAttributes = array(
                'title'          => 'Button Preview of ' . $iButtonID,
                'id'             => 'iframe-button-preview-' . $iButtonID,
                'data-button-id' => $iButtonID,
                'class'          => 'frame-button-preview',
                'frameborder'    => 0,
                'border'         => 0,
                'style'          => 'height:60px; border:none; overflow:hidden; margin: 0 auto; display: block;',
                'width'          => 200,
                'height'         => 60,
                'scrolling'      => 'no',
                'src'            => add_query_arg( array(
                    'aal-button-preview' => '_by_id',
                    'button-id' => $iButtonID,
                ), get_site_url() ),
            );
            return "<div class='iframe-button-preview-container'>"
                   . "<iframe " . $this->oUtil->getAttributes( $_aAttributes ) . "></iframe>"
                . "</div>";
        }
        
}