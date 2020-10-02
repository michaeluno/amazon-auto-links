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
 * Provides shared methods for the category select form classes.
 *
 */
class AmazonAutoLinks_Form_CategorySelect__Utility extends AmazonAutoLinks_Unit_Utility {

    /**
     * Gets the current self-url. needs to exclude the query part
     * e.g. http://localhost/me.php?href=http://....  -> http://localhost/me.php
     * @return      string
     * @since       unknown
     * @since       3.5.7       Moved from `AmazonAutoLinks_Form_CategorySelect`.
     * @since       3.5.7       Renamed from from `_formatLinkURL()`.
     * @param       string      $sURL
     * @param       array       $aQueries
     */
    protected function _getLinkURLFormatted( $sURL, $aQueries=array() ) {
        $_oEncrypt = new AmazonAutoLinks_Encrypt;
        return add_query_arg(
            array(
                'href' => $_oEncrypt->encode( $sURL ),
            ) + $aQueries + $_GET
            , admin_url( $GLOBALS[ 'pagenow' ] )
        );
    }

}