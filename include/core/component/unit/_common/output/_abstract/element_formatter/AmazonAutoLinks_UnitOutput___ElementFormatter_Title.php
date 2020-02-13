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
 * A class that provides methods to format title outputs.
 *
 * @since       3.10.0
 */
class AmazonAutoLinks_UnitOutput___ElementFormatter_Title extends AmazonAutoLinks_UnitOutput___ElementFormatter_Base {

    /**
     * @return      string
     * @throws      Exception
     * @since       3.10.0
     */
    public function get() {

        // For feed units, the database should not be accessed.
        if ( isset( $this->_aProduct[ 'formatted_title' ] ) ) {
            return $this->getTitleSanitized( $this->_aProduct[ 'title' ], $this->_oUnitOption->get( 'title_length' ) );
        }

        // Check if the preferred language is the default language for the locale.
        // If not, try to use the title value set in the database as it can written in the preferred language.
        $_sPreferredLanguage = $this->_oUnitOption->get( 'language' );
        $_sDefaultLanguage   = AmazonAutoLinks_PAAPI50___Locales::getDefaultLanguageByLocale( $this->_sLocale );
        if ( $_sPreferredLanguage === $_sDefaultLanguage ) {
            return $this->_aProduct[ 'title' ];
        }

        $_sStoredLanguage = $this->_getCell( 'language' );
        if ( null === $_sStoredLanguage ) {
            return $this->_aProduct[ 'title' ];
        }

        return $this->getTitleSanitized( $this->_getCell( 'title' ), $this->_oUnitOption->get( 'title_length' ) );

    }

}