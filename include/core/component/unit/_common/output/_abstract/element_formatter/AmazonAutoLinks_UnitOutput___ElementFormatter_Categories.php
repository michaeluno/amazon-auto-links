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
 * A class that provides methods to format category outputs.
 *
 * @since       3.8.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Categories extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.8.0
     */
    public function get() {

        // For search-type and feed units, this value is already set.
        if ( isset( $this->_aProduct[ 'category' ] ) ) {
            return $this->_aProduct[ 'category' ];
        }

        $_snEncodedHTML = $this->_getCell( 'categories' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving the categories.', 'amazon-auto-links' ),
                $this->_sLocale
            );
        }
        return $this->___getFormattedOutput( $_snEncodedHTML );

    }
        /**
         * @since   3.8.0
         * @return  string
         */
        private function ___getFormattedOutput( $_snEncodedHTML ) {
            if ( '' === $_snEncodedHTML ) {
                return '';
            }
            $_asCategories = $this->getElement( $this->_aRow, array( 'categories' ), array() );
            // If the data is already set in the plugin custom database table, it is stored as a string
            if ( is_string( $_asCategories ) ) {
                return $_asCategories;
            }
            // For backward compatibility for v3.8.14 or below
            return AmazonAutoLinks_Unit_Utility::getCategories( $_asCategories );
        }


}