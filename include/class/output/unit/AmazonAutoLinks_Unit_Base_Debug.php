<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno
 * 
 */

/**
 * A one of the base classes for unit classes.
 * 
 * Provides shared methods and properties for debugging.
 * @since       3
 */
abstract class AmazonAutoLinks_Unit_Base_Debug extends AmazonAutoLinks_Unit_Base {
   
    /**
     * Stores debug info for each product.
     * @since      3
     */
    static public $aProductDebugInfo = array();
    
    /**
     * Sets a debug info for a product by column name.
     * @return      void
     * @since       3
     */
    protected function _setColumnItemDebugInfo( $sColumnName, $sASINLocale, array $aInfo ) {
        
        self::$aProductDebugInfo[ $sASINLocale ] = isset( self::$aProductDebugInfo[ $sASINLocale ] )
            ? self::$aProductDebugInfo[ $sASINLocale ]
            : array();
        self::$aProductDebugInfo[ $sASINLocale ][ $sColumnName ] = $aInfo;
        add_filter( 
            'aal_filter_unit_product_formatted_html',
            array( $this, '_replyToAddProductRowInfo' ),
            10,  // priority
            3    // 3 parameters
        );                
        
    }

    /**
     * 
     * @callback    filter      aal_filter_unit_product_formatted_html
     * @return      string
     */
    public function _replyToAddProductRowInfo( $sProductHTML, $sASIN, $sLocale ) {
        $_sASINLocale = $sASIN . '_' . $sLocale;
        return $sProductHTML
            . '<pre class="debug" style="max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;">'
                . '<h5>' 
                    . __( 'Debug Info', 'amazon-auto-links' ) 
                    . ' - ' . __( 'Row', 'amazon-auto-links' ) 
                    . ': ' . $sASIN . ' - ' . $sLocale
                . '</h5>'
                . AmazonAutoLinks_Debug::get( 
                    $this->_getProductRow( 
                        $sASIN, 
                        $sLocale
                    ) 
                )
                . '<h5>'
                    . __( 'Columns', 'amazon-auto-links' )
                . '</h5>'
                . $this->_getProductColumnDebugOutput( $_sASINLocale )
            . "</pre>"
        ;
    }
        /**
         * 
         * @return      string
         */
        private function _getProductColumnDebugOutput( $sASINLocale ) {
            
            return isset( self::$aProductDebugInfo[ $sASINLocale ] )
                ? '<pre class="debug" style="max-height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em; word-wrap: break-word; word-break: break-all; margin: 1em 0;">'
                    . AmazonAutoLinks_Debug::get( self::$aProductDebugInfo[ $sASINLocale ] )
                . '</pre>'
                : '';
                
        }
        private function _getProductRow( $sASIN, $sLocale ) {                    
            $_sASINLocale = $sASIN . '_' . strtoupper( $sLocale );
            return $this->oProductTable->getRow(
                "SELECT *
                FROM {$this->oProductTable->sTableName}
                WHERE asin_locale = '{$_sASINLocale}'"
            );                         
        }
    
    /**
     * Prints out debug information.
     * @since       3
     */
    protected function _printDebugInfo( $sTemplatePath, $aArguments, $aOptions, $aProducts ) {

        echo "<pre style='height: 300px; overflow-y: scroll; overflow-x: auto; padding: 0 1em'>";
            echo "<h3>Debug Info</h3>";
            echo "<h4>Template Path</h4>";
            AmazonAutoLinks_Debug::dump( $sTemplatePath );
            echo "<h4>Unit Arguments</h4>";
            AmazonAutoLinks_Debug::dump( $aArguments );
            echo "<h4>Plugin Options</h4>";
            AmazonAutoLinks_Debug::dump( $aOptions );
            echo "<h4>Fetched Data</h4>";
            AmazonAutoLinks_Debug::dump( $aProducts );
            echo "<span style='float:right;'>" 
                . AmazonAutoLinks_Registry::NAME 
                . ' ' . AmazonAutoLinks_Registry::VERSION 
                . "</span>";
        echo "</pre>";     
        
    }
    
}