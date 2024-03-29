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
 * Adds the 'miunosoft Affiliate' form section.
 * 
 * @since 3.2.0
 * @since 4.7.0 Moved from the main component and renamed from `AmazonAutoLinks_AdminPage_Setting_General_MiunosoftAffiliate`.
 */
class AmazonAutoLinks_Opt_In_Setting_Section_Affiliate extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since  3.11.1
     */
    protected function _getArguments() {
        return array(
            'title'         => __( 'Affiliate', 'amazon-auto-links' ),
            'section_id'    => 'miunosoft_affiliate',
            'tab_slug'      => $this->sTabSlug,
        );
    }

    /**
     * A user constructor.
     * 
     * @since 3.2.0
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3.2.0
     * @param       AmazonAutoLinks_AdminPageFramework $oFactory
     * @param       string $sSectionID
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {

        $oFactory->addSettingFields(
            $sSectionID, // the target section id,
            array(
                'field_id'      => 'affiliate_id',
                'type'          => 'text',
                'title'         => __( 'Auto Amazon Links Pro Affiliate ID', 'amazon-auto-links' ),
                'tip'           => array(
                    "<img style='float: left; margin: 1em 1.6em 1em 0.5em; text-align:center;' src='"
                        . esc_url( AmazonAutoLinks_Registry::getPluginURL( AmazonAutoLinks_Main_Loader::$sDirPath . '/asset/image/tip/credit_link.jpg', true ) )
                        . "' alt='" . esc_attr( __( 'Credit Link', 'amazon-auto-links' ) ) . "'/>",
                    __( 'If you set your affiliate ID of Auto Amazon Links Pro here, the credit text at the bottom of unit outputs will be linked to the Auto Amazon Links Pro product page.', 'amazon-auto-links' ),
                ),
                'description'   => array(
                    __( 'Earn commissions by putting a link to the product page of Auto Amazon Links Pro in the credit link of unit outputs.', 'amazon-auto-links' ),
                    sprintf(
                        __( 'You need to <a href="%1$s" target="_blank">sign up</a> first for Auto Amazon Links Pro affiliate program to get commisions.', 'amazon-auto-links' ),
                        esc_url( 'https://store.michaeluno.jp/amazon-auto-links-pro/affiliate-area/' )
                    ),
                    'e.g. <code>456</code>',
                ),
            ),
            // [5.1.6]
            array(
                'field_id'      => 'user_comment',
                'type'          => 'textarea',
                'title'         => __( 'Your Comment', 'amazon-auto-links' ) . '&nbsp;<span class="warning">* (' . __( 'required', 'amazon-auto-links' ) . ')</span>',
                'description'   => array(
                    __( 'This text will be placed in the <code>alt</code> and <code>title</code> attributes of the credit link.', 'amazon-auto-links' ),
                    __( 'What do you think about this plugin?', 'amazon-auto-links' ),
                    __( 'What you can do with this plugin?', 'amazon-auto-links' ),
                    __( 'What is special about this plugin?', 'amazon-auto-links' ),
                ),
                'attributes'    => array(
                    'placeholder' => __( 'You can earn commission fees with this WordPress plugin! etc.', 'amazon-auto-links' ),
                ),
            )
        );

    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since 3.2.0
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        // $_bVerified = true;
        $_aErrors   = array();

        $aInputs[ 'affiliate_id' ] = trim( $aInputs[ 'affiliate_id' ] );
        $aInputs[ 'user_comment' ] = trim( $aInputs[ 'user_comment' ] );
        if ( $aInputs[ 'affiliate_id' ] && empty( $aInputs[ 'user_comment' ] ) ) {
            $_aErrors[ $this->sSectionID ][ 'user_comment' ] = __( 'Enter your comment about the plugin.', 'amazon-auto-links' );
            $oAdminPage->setFieldErrors( $_aErrors );
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
        
        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        // if ( ! $_bVerified ) {
        //     $oAdminPage->setFieldErrors( $_aErrors );     
        //     $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
        //     return $aOldInputs;
        // }
                
        return $aInputs;
        
    }
   
}