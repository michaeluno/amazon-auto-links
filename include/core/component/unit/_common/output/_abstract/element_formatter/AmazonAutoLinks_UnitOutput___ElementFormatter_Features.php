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
 * A class that provides methods to format features.
 *
 * @since       3.8.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Features extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    protected $_aProduct = array();

    /**
     * Sets up properties.
     */
    public function __construct( $sASIN, $sLocale, $sAssociateID, array $aRow, $oUnitOption, $aProduct ) {
        parent::__construct( $sASIN, $sLocale, $sAssociateID, $aRow, $oUnitOption );
        $this->_aProduct = $aProduct;
    }

    /**
     * @return      string
     * @throws      Exception
     * @since       3.5.0
     */
    public function get() {

        // For search-type units, this value is already set with API response.
        if ( $this->_aProduct[ 'feature' ] ) {
            return "<p>Features Already Exists</p>"
                . $this->_aProduct[ 'feature' ];
        }

        $_snEncodedHTML = $this->_getCell( 'features' );
        if ( null === $_snEncodedHTML ) {
            return $this->_getPendingMessage(
                __( 'Now retrieving the features.', 'amazon-auto-links' )
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
            return AmazonAutoLinks_UnitOutput_Utility::getFeatures(
                $this->getElementAsArray( $this->_aRow, 'features' )
            );
        }


}