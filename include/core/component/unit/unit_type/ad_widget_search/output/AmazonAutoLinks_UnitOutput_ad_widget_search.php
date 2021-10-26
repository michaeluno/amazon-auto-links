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
 * Handles outputs of ad-widget search units.
 *
 * @since 5.0.0
 */
class AmazonAutoLinks_UnitOutput_ad_widget_search extends AmazonAutoLinks_UnitOutput_category {

    /**
     * Stores the unit type.
     * @remark The base constructor creates a unit option object based on this value.
     * @since  5.0.0
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * @return string
     */
    public function get() {
        if ( $this->_shouldUsePAAPI() ) {
            return $this->___getOutputByPAAPI();
        }
        return parent::get();
    }
        /**
         * @return string
         * @since  5.0.0
         */
        private function ___getOutputByPAAPI() {
            $_sUnitType = $this->___getPAAPIUnitTypeFromArguments( $this->oUnitOption->aRawOptions );
            $_sClass      = "AmazonAutoLinks_UnitOutput_" . $_sUnitType;
            $_oUnitOutput = new $_sClass( $this->oUnitOption->aRawOptions );
            return $_oUnitOutput->get();
        }
            private function ___getPAAPIUnitTypeFromArguments( array $aRawArguments ) {
                if ( isset( $aRawArguments[ 'asin' ] ) ) {
                    return 'item_lookup';
                }
                return 'search';
            }

}