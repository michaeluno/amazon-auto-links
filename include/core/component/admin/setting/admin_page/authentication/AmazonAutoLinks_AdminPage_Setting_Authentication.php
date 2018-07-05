<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Adds the 'Generator' tab to the 'Tools' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_AdminPage_Setting_Authentication extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     */
    public function replyToLoadTab( $oAdminPage ) {
        
        new AmazonAutoLinks_AdminPage_Setting_Authentication_AuthenticationKeys( 
            $oAdminPage,
            $this->sPageSlug, 
            array(
                'section_id'    => 'authentication_keys', 
                'tab_slug'      => $this->sTabSlug,
                'title'         => __( 'AWS Access Key Identifiers', 'amazon-auto-links' ),
                'description'   => sprintf( 
                        __( 'For the Search Unit type, credentials are required to perform search requests with Amazon <a href="%1$s" target="_blank">Product Advertising API</a>.', 'amazon-auto-links' ), 
                        'https://affiliate-program.amazon.com/gp/advertising/api/detail/main.html' 
                    )
                    . ' ' . sprintf( 
                        __( 'The keys can be obtained by logging in to the <a href="%1$s" target="_blank">Amazon Web Services web site</a>.', 'amazon-auto-links' ), 
                        'http://aws.amazon.com/' 
                    )
                    . ' ' . sprintf( 
                        __( 'The instruction is documented <a href="%1$s" target="_blank">here</a>.', 'amazon-auto-links' ), 
                        '?post_type=' . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] . '&page=aal_help&tab=notes#How_to_Obtain_Access_Key_and_Secret_Key' 
                    ),
            )
        );
        
    }
            
}
