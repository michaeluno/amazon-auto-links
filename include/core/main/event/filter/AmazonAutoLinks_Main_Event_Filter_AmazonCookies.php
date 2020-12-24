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
 * Checks amazon cookies and if it is expired, schedule a renewal event.
 *
 * @since        4.5.0
 */
class AmazonAutoLinks_Main_Event_Filter_AmazonCookies extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_filter( 'aal_filter_amazon_cookies_cache', array( $this, 'replyToGetAmazonCookies' ), 10, 5 );
    }

    /**
     * @param  array  $aCookies
     * @param  integer $iLifespan
     * @param  AmazonAutoLinks_Locale_Base $oLocale
     * @param  string $sLanguage
     * @param  array $aOldCookies
     * @return array
     * @since  4.0.0
     */
    public function replyToGetAmazonCookies( $aCookies, $iLifespan, $oLocale, $sLanguage, $aOldCookies ) {

        if ( ! $this->isExpired( $iLifespan ) ) {
            return $aCookies;
        }
        $this->scheduleTask( 'aal_action_renew_amazon_cookies', array( $oLocale->sSlug, $sLanguage ) );
        return $aOldCookies;

    }

}