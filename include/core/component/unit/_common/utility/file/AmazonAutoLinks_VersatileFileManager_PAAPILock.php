<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
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
     * @param string  $sLocale  The locale code as an identifier.
     * @param integer $iTimeout
     * @param string  $sFileNamePrefix
     */
    public function __construct( $sLocale, $iTimeout=1, $sFileNamePrefix='' ) {
        $sFileNamePrefix = $sFileNamePrefix
            ? $sFileNamePrefix
            : AmazonAutoLinks_Registry::TRANSIENT_PREFIX . "_LOCK_PAAPI_REQUEST_{$sLocale}_";
        parent::__construct( $sLocale, $iTimeout, $sFileNamePrefix );
    }

}