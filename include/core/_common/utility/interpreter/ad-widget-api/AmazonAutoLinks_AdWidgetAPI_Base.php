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
 * Performs Ad Widget API Search requests.
 *
 * @sicne       4.6.9
 */
class AmazonAutoLinks_AdWidgetAPI_Base extends AmazonAutoLinks_PluginUtility {

    /**
     * @var AmazonAutoLinks_Locale_Base
     */
    public $oLocale;

    /**
     * @var int
     */
    public $iCacheDuration = 86400;

    /**
     * Sets up properties and hooks.
     */
    public function __construct( $sLocale, $iCacheDuration=86400 ) {
        $this->oLocale = new AmazonAutoLinks_Locale( $sLocale );
        $this->iCacheDuration = $iCacheDuration;
    }

    /**
     * @remark This might need to be moved a utility class.
     * @param  string $sJSONP
     * @return array|null|false <b>NULL</b> is returned if the  * <i>json</i> cannot be decoded or if the encoded
     * data is deeper than the recursion limit.
     * @since  4.6.9
     */
    static public function getJSONFromJSONP( $sJSONP ) {

        // Strip the enclosing JS function
        // @see https://gist.github.com/umutakturk/3804958
        $_sJSONJS   = preg_replace("/[^(]*\((.*)\)/", "$1", $sJSONP );

        // The JSON syntax is still JS based. Enclose keys with double quotes
        // @see https://stackoverflow.com/a/40326949
        $_sJSONJS   = preg_replace('/("(.*?)"|(\w+))(\s*:\s*(".*?"|.))/s', '"$2$3"$4', $_sJSONJS );
        $_sJSON     = preg_replace('/("(.*?)"|(\w+))(\s*:\s*)\+?(0+(?=\d))?(".*?"|.)/s', '"$2$3"$4$6', $_sJSONJS );
        return json_decode( $_sJSON, true );

    }

}