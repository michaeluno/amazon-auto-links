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
 * Provides the ability to parse a shortcode
 *
 * @since       4.7.0
 */
class AmazonAutoLinks_Disclosure_Shortcode extends AmazonAutoLinks_WPUtility {

    /**
     * Registers the shortcode(s).
     */
    public function __construct() {
        add_shortcode( AmazonAutoLinks_Registry::$aShortcodes[ 'disclosure' ], array( $this, 'replyToGetOutput' ) );
    }
    
    /**
     * Returns the output based on the shortcode arguments.
     * @since    4.7.0
     * @param    array  $aArguments
     * @return   string
     * @callback add_shortcode()
     */
    public function replyToGetOutput( $aArguments ) {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        return wpautop( str_replace(
            array(
                '%site_host%',
                '%associates_list%'
            ),
            array(
                $this->getSubDomain( site_url() ),
                $this->___getAssociatesList(),
            ),
            $_oOption->get( 'disclosure', 'disclosure_text' )
        ) )
        . wpautop( $_oOption->get( 'disclosure', 'disclaimer_text' ) );
    }
        private function ___getAssociatesList() {
            $_sList   = '';
            $_oOption = AmazonAutoLinks_Option::getInstance();
            foreach( $this->getAsArray( $_oOption->get( 'associates' ) ) as $_sLocale => $_aAssociate ) {
                // the 'locale' element is a string
                if ( ! is_array( $_aAssociate ) ) {
                    continue;
                }
                if ( ! $this->getElement( $_aAssociate, 'associate_id' ) ) {
                    continue;
                }
                $_oLocale = new AmazonAutoLinks_Locale( $_sLocale );
                $_sList .= "<li>" . $_oLocale->getDomain() . "</li>";
            }
            return "<ul class='associates-list'>"
                    . $_sList
                . "</ul>";
        }

}