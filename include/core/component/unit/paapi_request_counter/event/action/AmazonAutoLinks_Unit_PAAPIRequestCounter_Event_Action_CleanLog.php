<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Cleans PA-API Request count log stored as files and the database options table.
 * @since        4.4.0
 */
class AmazonAutoLinks_Unit_PAAPIRequestCounter_Event_Action_CleanLog extends AmazonAutoLinks_Event___Action_Base {

    protected $_sActionHookName     = 'aal_action_paapi_request_counter_clean_log';
    protected $_iCallbackParameters = 1;

    /**
     * @return boolean
     * @since  4.4.0
     */
    protected function _shouldProceed( /* $aArguments */ ) {
        if ( $this->hasBeenCalled( get_class( $this ) . '::' . __METHOD__ . '_' . serialize( func_get_args() ) ) ) {
            return false;
        }
        return true;
    }

    /**
     * Scan files and populate data and remove files. Then, store the data in the options table.
     */
    protected function _doAction( /* $sLocale, */ ) {

        $_aParams       = func_get_args() + array( null );
        $_sLocale       = $_aParams[ 0 ];
        $_sOptionKey    = isset( AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ $_sLocale ] )
            ? AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ $_sLocale ]
            : AmazonAutoLinks_Registry::$aOptionKeys[ 'paapi_request_counter' ][ 'US' ];
        delete_option( $_sOptionKey );
        $_oCounter = new AmazonAutoLinks_VersatileFileManager_PAAPI_RequestCounter( $_sLocale );
        $this->removeDirectoryRecursive( $_oCounter->getDirectoryPath() );

    }

}