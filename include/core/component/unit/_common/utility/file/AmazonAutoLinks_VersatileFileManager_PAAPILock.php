<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Manages a lock file of PA-API requests.
 *
 * @since   4.3.5
 */
class AmazonAutoLinks_VersatileFileManager_PAAPILock extends AmazonAutoLinks_VersatileFileManager {

    /**
     * Sets up properties.
     *
     * @param string  $sLocale          The locale code as an identifier. Needed for a file prefix.
     * @param string  $sAccessKeyPublic
     * @param string  $sAccessKeySecret
     * @param integer $iTimeout
     * @param string  $sFileNamePrefix
     * @since 4.3.5
     * @since 4.5.9   Added the `$sAccessKeyPublic` and `$sAccessKeySecret` parameter.
     */
    public function __construct( $sLocale, $sAccessKeyPublic, $sAccessKeySecret, $iTimeout=1, $sFileNamePrefix='' ) {
        $sFileNamePrefix = $sFileNamePrefix
            ? $sFileNamePrefix
            : AmazonAutoLinks_Registry::TRANSIENT_PREFIX . "_LOCK_PAAPI_REQUEST_{$sLocale}_";
        parent::__construct( md5( $sLocale . $sAccessKeyPublic . $sAccessKeySecret ), $iTimeout, $sFileNamePrefix );
    }

}