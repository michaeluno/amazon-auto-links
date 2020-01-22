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
 * A class that inserts the impression counter script.
 *
 * @since       3.5.0
 */
class AmazonAutoLinks_UnitOutput__ImpressionCounter extends AmazonAutoLinks_UnitOutput__DelegationBase {

    /**
     * Stores the locales of the impression counter scripts to insert.
     * @since       3.1.0
     */
    static private $___aImpressionCounterScriptLocales = array();

    public function add( $sLocale, $sAssociateID ) {
        if ( ! $this->_oUnitOutput->oOption->get( 'external_scripts', 'impression_counter_script' ) ) {
            return;
        }
        self::$___aImpressionCounterScriptLocales[ $sLocale ] = isset( self::$___aImpressionCounterScriptLocales[ $sLocale ] )
            ? self::$___aImpressionCounterScriptLocales[ $sLocale ]
            : array();
        self::$___aImpressionCounterScriptLocales[ $sLocale ][ $sAssociateID ] = $sAssociateID;
        $this->___setCallback();
    }
        private function ___setCallback() {
            if ( $this->hasBeenCalled( __CLASS__ ) ) {
                return;
            }
            add_action( 'wp_footer', array( __CLASS__, 'replyToInsertImpressionCounter' ), 999 );
        }

    /**
     * Inserts impression counter scripts.
     * @since       3.1.0
     * @callback    add_action      wp_footer
     */
    static public function replyToInsertImpressionCounter() {
        foreach( self::$___aImpressionCounterScriptLocales as $_sLocale => $_aAssociateTags ) {
            foreach( $_aAssociateTags as $_sAssociateTag ) {
                echo str_replace(
                    '%ASSOCIATE_TAG%',  // needle
                    $_sAssociateTag,    // replacement
                    AmazonAutoLinks_Property::getImpressionCounterScript( $_sLocale ) // haystack
                );
            }
        }
    }

}