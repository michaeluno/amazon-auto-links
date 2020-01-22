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
 * A class that injects debug information in each product output.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__DebugInformation_Product extends AmazonAutoLinks_UnitOutput__DebugInformation_Base {

    /**
     * @return array
     */
    protected function _getFilterArguments() {
        return array(
            array(
                'aal_filter_unit_product_formatted_html',
                array( $this, '_replyToInsertDebugOutput' ),
                10,  // priority
                3    // 3 parameters
            ),
        );
    }

    /**
     * Stores debug info for each product.
     * @since      3
     */
    static private $___aProductDebugInfo = array();

    /**
     * Adds an item that shows debug information.
     * @param   string  $sColumnName
     * @param   string  $sASINLocale
     * @param   array   $aInfo
     * @return  void
     */
    static public function add( $sColumnName, $sASINLocale, array $aInfo ) {
        self::$___aProductDebugInfo[ $sASINLocale ] = isset( self::$___aProductDebugInfo[ $sASINLocale ] )
            ? self::$___aProductDebugInfo[ $sASINLocale ]
            : array();
        self::$___aProductDebugInfo[ $sASINLocale ][ $sColumnName ] = $aInfo;
    }

    /**
     * @param           string      $sProductHTML
     * @param           string      $sASIN
     * @param           string      $sLocale
     * @return          string
     * @callback        add_filter      aal_filter_unit_product_formatted_html
     */
    public function _replyToInsertDebugOutput( $sProductHTML, $sASIN, $sLocale ) {

        if ( ! isset( self::$___aProductDebugInfo[ $sASIN . '_' . $sLocale ] ) ) {
            return $sProductHTML;
        }

        $_sASINLocale = $sASIN . '_' . $sLocale;
        return $sProductHTML
            . '<pre class="debug" style="max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;">'
                . '<h5>'
                    . __( 'Debug Info', 'amazon-auto-links' )
                    . ' - ' . __( 'Row', 'amazon-auto-links' )
                    . ': ' . $sASIN . ' - ' . $sLocale
                . '</h5>'
                . AmazonAutoLinks_Debug::get(
                    $this->___getProductRow( $sASIN, $sLocale )
                )
                . '<h5>'
                    . __( 'Columns', 'amazon-auto-links' )
                . '</h5>'
                . $this->___getProductColumnsDebugOutput( $_sASINLocale )
            . "</pre>"
        ;

    }
        /**
         * @param $sASIN
         * @param $sLocale
         *
         * @return array
         */
        private function ___getProductRow( $sASIN, $sLocale ) {
            $_sASINLocale = $sASIN . '_' . strtoupper( $sLocale );
            $_sTableName  = $this->_oUnitOutput->oProductTable->getTableName();
            return $this->_oUnitOutput->oProductTable->getRow(
                "SELECT *
                FROM {$_sTableName}
                WHERE asin_locale = '{$_sASINLocale}'"
            );
        }

        /**
         *
         * @return      string
         */
        private function ___getProductColumnsDebugOutput( $sASINLocale ) {
            $_sColumnInformation = isset( self::$___aProductDebugInfo[ $sASINLocale ] )
                ? '<pre class="debug" style="max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;">'
                    . AmazonAutoLinks_Debug::get( self::$___aProductDebugInfo[ $sASINLocale ] )
                . '</pre>'
                : '';
            unset( self::$___aProductDebugInfo[ $sASINLocale ] );
            return $_sColumnInformation;
        }


}