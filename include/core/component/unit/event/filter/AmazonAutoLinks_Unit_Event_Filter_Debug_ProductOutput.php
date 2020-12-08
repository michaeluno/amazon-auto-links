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
 * Inserts a debug output below each product output.
 * @since   4.3.5
 */
class AmazonAutoLinks_Unit_Event_Filter_Debug_ProductOutput extends AmazonAutoLinks_PluginUtility {

    /**
     * @since  4.3.5
     * @remark Already checked if the plugin debug mode is turned on.
     */
    public function __construct() {
        add_filter( 'aal_filter_unit_product_formatted_html', array( $this, 'replyToInsertDebugOutput' ), 10, 5 );
    }

    /**
     * @param  string $sOutputHTML
     * @param  string $sProductID
     * @param  AmazonAutoLinks_UnitOutput_Base $oUnitOutput
     * @param  array $aProduct
     * @param  array $aProductDBRow
     * @return string
     * @since  4.3.5
     */
    public function replyToInsertDebugOutput( $sOutputHTML, $sProductID, $oUnitOutput, array $aProduct, array $aProductDBRow ) {
        if ( $oUnitOutput->oUnitOption->get( 'is_preview' ) ) {
            return $sOutputHTML;
        }
        return $sOutputHTML . $this->___getProductDebugInformation( $sProductID, $oUnitOutput, $aProduct, $aProductDBRow );
    }
        /**
         * @param  string $sProductID
         * @param  AmazonAutoLinks_UnitOutput_Base $oUnitOutput
         * @param  array $aProduct
         * @param  array $aProductDBRow
         * @return string
         * @sine   4.3.5
         */
        private function ___getProductDebugInformation( $sProductID, $oUnitOutput, array $aProduct, array $aProductDBRow ) {
            $_aAttributes = array(
                'class' => 'debug',
                'style' => 'max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;',
            );
            return '<pre ' . $this->getAttributes( $_aAttributes ) . '>'
                . '<h4>'
                    . __( 'Debug Info', 'amazon-auto-links' ) . ' - ' . __( 'Row', 'amazon-auto-links' ) . ': ' . $sProductID
                . '</h4>'
                . AmazonAutoLinks_Debug::getDetails( $aProductDBRow )
            . "</pre>"
            . '<pre ' . $this->getAttributes( $_aAttributes ) . '>'
                . '<h4>'
                    . __( 'Debug Info', 'amazon-auto-links' ) . ' - ' . __( 'Product', 'amazon-auto-links' )
                . '</h4>'
                . AmazonAutoLinks_Debug::getDetails( $aProduct )
            . "</pre>";

        }

}