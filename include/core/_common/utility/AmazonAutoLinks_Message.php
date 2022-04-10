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
 * Provides plugin messages.
 *
 * This provides the ability to retrieve a translation item on demand by avoiding to load unnecessary translation items all at once.
 * @since 4.7.0
 */
class AmazonAutoLinks_Message {

    /**
     * @var string
     * @since 4.7.0
     */
    public $sTextDomain  = 'amazon-auto-links';

    /**
     * @var array
     * @since 4.7.0 Stores messages.
     */
    public $aMessages = array();

    /**
     * @var array
     * @since 4.7.0 Stores default messages.
     */
    public $aDefaults = array(
        'activate_license'            => 'Please activate the license.',
        'message_not_sent'            => 'For some reasons, the message could not be sent. Please contact %1$s directly.',
        'consider_pro'                => 'Please consider upgrading to Pro to enable this feature.',
        'consider_pro_link'           => 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to enable this feature.',
        'available_in_pro'            => 'Available in Pro.',
        'upgrade_to_pro'              => 'Please upgrade to <a href="%1$s">Pro</a> to add more units!',
        'agree_to_send_info'          => 'I understand that the system information including a PHP version and WordPress version etc. and plugin settings will be sent along with the messages to help troubleshooting.',
        'locale_field_tip_associates' => 'If the country is not listed, set an Associate ID in the <a href="%1$s">Associates</a> section.', // 5.0.0
        'locale_field_tip_paapi'      => 'For countries which require PA-API, set PA-API keys as well.', // 5.0.0
    );

    /**
     * @var   AmazonAutoLinks_Message   A cached self class instance.
     * @since 4.7.0
     */
    static public $oSelf;

    /**
     * @since 4.7.0
     */
    public function __construct() {
        $this->aMessages = array_fill_keys( array_keys( $this->aDefaults ), null );
    }

    /**
     * @since  4.7.0
     * @return AmazonAutoLinks_Message
     */
    static public function getInstance() {
        if ( isset( self::$oSelf ) ) {
            return self::$oSelf;
        }
        $_sThisClass = __CLASS__;
        self::$oSelf = new $_sThisClass;
        return self::$oSelf;
    }

    /**
     * @param  string $sItemKey
     * @return string
     * @since  4.7.0
     */
    static public function get( $sItemKey ) {
        $_oMessage = self::getInstance();
        return apply_filters( 'aal_filter_message_' . $sItemKey, $_oMessage->getItem( $sItemKey ) );
    }

    /**
     * @param  string $sItemKey
     * @return string
     * @since  4.7.0
     */
    public function getItem( $sItemKey ) {
        return isset( $this->aMessages[ $sItemKey ] )
            ? __( $this->aMessages[ $sItemKey ], $this->sTextDomain )
            : __( $this->{$sItemKey}, $this->sTextDomain );
    }

    /**
     * Called when an unknown property is accessed.
     * @param  string $sPropertyName
     * @return string
     * @since  4.7.0
     */
    public function __get( $sPropertyName ) {
        return isset( $this->aDefaults[ $sPropertyName ] )
            ? $this->aDefaults[ $sPropertyName ]
            : $sPropertyName;
    }

    /**
     * @return  string
     * @since   4.0.0
     * @since   4.7.0   Moved from `AmazonAutoLinks_PluginUtility`
     */
    static public function getUpgradePromptMessageToAddMoreUnits() {
        return sprintf(
            self::get( 'upgrade_to_pro' ),
            esc_url( AmazonAutoLinks_Registry::STORE_URI_PRO ),
            admin_url( 'edit.php?post_status=trash&post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] )
        );
    }

    /**
     * @since   3.2.4
     * @since   4.7.0   Moved from `AmazonAutoLinks_PluginUtility`
     * @return  string
     * @param   boolean $bHasLink
     */
    static public function getUpgradePromptMessage( $bHasLink=true ) {
        return $bHasLink
            ? sprintf( self::get( 'consider_pro_link' ), esc_url( 'https://store.michaeluno.jp/amazon-auto-links-pro/downloads/amazon-auto-links-pro/' ) )
            : self::get( 'consider_pro' );
    }

    /**
     * @param  boolean $bHasLink
     * @return string
     * @since  5.0.0
     */
    static public function getLocaleFieldGuide( $bHasLink=true ) {
        $_sMessage = self::get( 'locale_field_tip_associates' );
        return $bHasLink
            ? sprintf( $_sMessage, AmazonAutoLinks_PluginUtility::getAPIAuthenticationPageURL() )
            : strip_tags( $_sMessage );
    }

    /**
     * @param  string $sEmailAddress
     * @return string
     * @since  4.7.0
     */
    static public function getMessageNotSent( $sEmailAddress ) {
        return sprintf( self::get( 'message_not_sent' ), $sEmailAddress );
    }

    /**
     * @remark This does nothing but for translation tools such as POEdit to parse the source code to find translation items.
     * @since  4.7.0
     */
    private function ___translate() {
        __( 'Please activate the license.', 'amazon-auto-links' );
        __( 'For some reasons, the message could not be sent. Please contact %1$s directly.', 'amazon-auto-links' );
        __( 'Please consider upgrading to Pro to enable this feature.', 'amazon-auto-links' );
        __( 'Please consider upgrading to <a href="%1$s" target="_blank">Pro</a> to enable this feature.', 'amazon-auto-links' );
        __( 'Available in Pro.', 'amazon-auto-links' );
        __( 'Please upgrade to <a href="%1$s">Pro</a> to add more units!', 'amazon-auto-links' ) . ' ' . __( 'Make sure to empty the <a href="%2$s">trash box</a> to delete the units completely!', 'amazon-auto-links' );
        __( 'I understand that the system information including a PHP version and WordPress version etc. and plugin settings will be sent along with the messages to help troubleshooting.', 'amazon-auto-links' );
        __( 'If the country is not listed, set an Associate ID in the <a href="%1$s">Associates</a> section.', 'amazon-auto-links' ); // 5.0.0
        __( 'For countries which require PA-API, set PA-API keys as well.', 'amazon-auto-links' );  // 5.0.0
    }

}