<?php
/**
 * Amazon Auto Links
 *
 * http://en.michaeluno.jp/amazn-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 *
 */

/**
 * Provides locale information.
 *
 * @since       4.3.4
 */
class AmazonAutoLinks_Locale {

    /**
     * @var AmazonAutoLinks_Locale_Base
     */
    public $oLocale;

    /**
     * Sets up properties and hooks.
     * @param string $sLocale
     * @since 4.3.4
     */
    public function __construct( $sLocale ) {
        $_sSlug      = strtoupper( $sLocale );
        $_sClassName = "AmazonAutoLinks_Locale_{$_sSlug}";
        $_sClassName = class_exists( $_sClassName )
            ? $_sClassName
            : "AmazonAutoLinks_Locale_US";  // default
        $this->oLocale = new $_sClassName;
    }

    /**
     * @param  string $sMethodName
     * @param  array $aArguments
     * @return mixed|void
     */
    public function __call( $sMethodName, array $aArguments ) {
        return call_user_func_array( array( $this->oLocale, $sMethodName ), $aArguments );
    }

}