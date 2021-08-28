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
 * Loads the admin pages of the Disclosure component.
 *
 * @package      Amazon Auto Links/Disclosure
 * @since        4.7.0
 */
class AmazonAutoLinks_Disclosure_Setting {

    /**
     * Sets up hooks.
     */
    public function __construct() {
        add_action( 'load_' . 'AmazonAutoLinks_AdminPage', array( $this, 'replyToLoadAdminPages' ) );
    }

    public function replyToLoadAdminPages( $oAdmin ) {
        add_action( 'load_' . AmazonAutoLinks_Registry::$aAdminPages[ 'main' ], array( $this, 'replyToLoadPage' ) );
        add_filter( 'validation_' . $oAdmin->oProp->sClassName, array( $this, 'validateAll' ), 10, 4 );
    }

    /**
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @return      void
     * @callback    action      load_{page slug}
     */
    public function replyToLoadPage( $oFactory ) {
        new AmazonAutoLinks_Disclosure_Setting_Tab_Disclosure( $oFactory, AmazonAutoLinks_Registry::$aAdminPages[ 'main' ] );
    }

    /**
     * Sets the 'page' field value if the user hasn't saved the 'Disclosure' section options
     * when one of the setting tab's form is submitted.
     *
     * @param  array $aInputs
     * @param  array $aOldInputs
     * @param  AmazonAutoLinks_AdminPageFramework $oFactory
     * @param  array $aSubmit
     * @return array
     * @since  4.7.3
     */
    public function validateAll( $aInputs, $aOldInputs, $oFactory, $aSubmit ) {

        $_oOption     = AmazonAutoLinks_Option::getInstance();
        $_aRawOptions = $_oOption->getRawOptions();
        if ( isset( $_aRawOptions[ 'disclosure' ] ) ) {
            return $aInputs;
        }
        // This might be sent from the 'Disclosure' tab.
        if ( isset( $aInputs[ 'disclosure' ] ) ) {
            return $aInputs;
        }

        // If the Affiliate Disclosure page exists and the user still hasn't saved the options, set it
        $_aDisclosurePage = AmazonAutoLinks_Disclosure_Utility::getPostByGUID( AmazonAutoLinks_Disclosure_Loader::$sDisclosureGUID, 'ID,post_title' );
        $_iPostID         = $oFactory->oUtil->getElement( $_aDisclosurePage, 'ID' );
        if ( ! $_iPostID ) {
            $_iPostID = AmazonAutoLinks_Disclosure_Utility::getDisclosurePageCreated();
        }
        $_oPost = get_post( $_iPostID );
        if ( is_a( $_oPost, 'WP_Post' ) ) {
            $aInputs[ 'disclosure' ] = is_array( $aInputs[ 'disclosure' ] ) ? $aInputs[ 'disclosure' ] : array();
            $aInputs[ 'disclosure' ][ 'page' ] = array(
                'value'   => $_iPostID,
                'encoded' => json_encode( array( array( 'id' => $_iPostID, 'text' => $_oPost->post_title ) ) ),
            );
        }
        return $aInputs;
    }

}