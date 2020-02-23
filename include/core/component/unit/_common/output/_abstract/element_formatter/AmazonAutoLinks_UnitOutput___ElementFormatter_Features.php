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
 * A class that provides methods to format features.
 *
 * @since       3.8.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Features extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.8.0
     */
    public function get() {

        // For search-type and feed units, this value is already set.
        if ( isset ( $this->_aProduct[ 'feature' ] ) ) {
            return $this->_aProduct[ 'feature' ];
        }

        $_snEncodedHTML = $this->_getCell( 'features' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving the features.', 'amazon-auto-links' ),
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

            $_asFeatures = $this->getElement( $this->_aRow, 'features', array() );
            // If the data is already set in the plugin custom database table, it is stored as a string
            if ( is_string( $_asFeatures ) ) {
                return $_asFeatures;
            }
            // For backward compatibility for v3.8.14 or below
            return AmazonAutoLinks_Unit_Utility::getFeatures( $_asFeatures );
        }


}