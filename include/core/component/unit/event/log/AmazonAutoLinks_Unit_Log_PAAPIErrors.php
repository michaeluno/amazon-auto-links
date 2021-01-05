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
 * Logs errors of Product Advertising API responses.
 *
 * @since        3.9.0
 */
class AmazonAutoLinks_Unit_Log_PAAPIErrors extends AmazonAutoLinks_PluginUtility {

    /**
     * Sets up properties and hooks.
     */
    public function __construct() {
        add_action( 'aal_action_detected_paapi_errors', array( $this, 'replyToLogPAAPIErrors' ), 10, 6 );
    }
    
    public function replyToLogPAAPIErrors( $aErrors, $sURL, $sCacheName, $sCharSet, $iCacheDuration, $aArguments ) {
        foreach( $aErrors as $_sError ) {
            new AmazonAutoLinks_Error(
                'PAAPI_ERROR',
                $_sError,
                array(
                    'url'            => $sURL,
                    'cache_name'     => $sCacheName,
                    'character_set'  => $sCharSet,
                    'cache_duration' => $iCacheDuration,
                    'arguments'      => $aArguments,
                ),
                true
            );
        }
    }
}