<?php
/**
 * Amazon Auto Links
 * 
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 * 
 */

/**
 * Handles ad_widget_search unit options.
 * 
 * @since 5.0.0

 */
class AmazonAutoLinks_UnitOption_ad_widget_search extends AmazonAutoLinks_UnitOption_Base {

    /**
     * @var string
     */
    public $sUnitType = 'ad_widget_search';

    /**
     * Stores the default structure and key-values of the unit.
     * @remark Accessed from the base class constructor to construct a default option array.
     */
    static public $aStructure_Default = array(
        'Keywords'              => '',
        'sort'                  => 'raw', // title, title_descending
    );

    /**
     * @return array
     * @since  5.0.0
     */
    protected function getDefaultOptionStructure() {
        return parent::getDefaultOptionStructure()
            + AmazonAutoLinks_UnitOption_search::$aStructure_Default
            + AmazonAutoLinks_UnitOption_item_lookup::$aStructure_Default;
    }

    /**
     * @param  array $aUnitOptions
     * @param  array $aDefaults
     * @param  array $aRawOptions
     * @return array
     * @since  5.0.0
     */
    protected function _getUnitOptionsFormatted( array $aUnitOptions, array $aDefaults, array $aRawOptions ) {

        // Shortcode arguments
        if ( isset( $aUnitOptions[ 'asin' ] ) ) {
            $_sKeywords = $this->getStringIntoArray( $aUnitOptions[ 'asin' ], ',' );;
            $aUnitOptions[ 'Keywords' ] = isset( $aUnitOptions[ 'Keywords' ] ) && $aUnitOptions[ 'Keywords' ]
                ? $aUnitOptions[ 'Keywords' ] . ', ' . $_sKeywords
                : $_sKeywords;
        }
        if ( isset( $aUnitOptions[ 'search' ] ) ) {
            $_sKeywords = $this->getStringIntoArray( $aUnitOptions[ 'search' ], ',' );
            $aUnitOptions[ 'Keywords' ] = isset( $aUnitOptions[ 'Keywords' ] ) && $aUnitOptions[ 'Keywords' ]
                ? $aUnitOptions[ 'Keywords' ] . ', ' . $_sKeywords
                : $_sKeywords;
        }

        $_aUnitOptions = $this->_getShortcodeArgumentKeysSanitized(
            $aUnitOptions,
            AmazonAutoLinks_UnitOption_search::$aShortcodeArgumentKeys + AmazonAutoLinks_UnitOption_item_lookup::$aShortcodeArgumentKeys
        );

        return parent::_getUnitOptionsFormatted( $_aUnitOptions, $aDefaults, $aRawOptions );

    }
}