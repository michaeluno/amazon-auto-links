<?php
/**
 * Deals with the plugin admin pages.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.5
 * 
 */
abstract class AmazonAutoLinks_AdminPage_Start extends AmazonAutoLinks_AdminPageFramework {

    public function start_AmazonAutoLinks_AdminPage() {
        
        // Set objects
        $this->oOption = & $GLOBALS['oAmazonAutoLinks_Option'];
        $this->oEncode = new AmazonAutoLinks_Encrypt;

        // Disable object caching in the plugin pages and the options.php (the page that stores the settings)
        if ( 
            'options.php' === $GLOBALS['pagenow']
            || isset( $_GET['post_type'] ) && ( $_GET['post_type'] == AmazonAutoLinks_Commons::PostTypeSlug || $_GET['post_type'] == AmazonAutoLinks_Commons::PostTypeSlugAutoInsert ) ) 
        {
            // wp_suspend_cache_addition( true );    
            $GLOBALS['_wp_using_ext_object_cache'] = false;
        }
    
        // For the create new unit page. Disable the default one.
        if ( $GLOBALS['pagenow'] == 'post-new.php' && isset( $_GET['post_type'] ) && count( $_GET ) == 1 ) {
                
            if ( $_GET['post_type'] == AmazonAutoLinks_Commons::PostTypeSlug )
                die( wp_redirect( add_query_arg( array( 'post_type' => AmazonAutoLinks_Commons::PostTypeSlug, 'page' => 'aal_add_category_unit' ), admin_url( 'edit.php' ) ) ) );
            if ( $_GET['post_type'] == AmazonAutoLinks_Commons::PostTypeSlugAutoInsert )
                die( wp_redirect( add_query_arg( array( 'post_type' => AmazonAutoLinks_Commons::PostTypeSlug, 'page' => 'aal_define_auto_insert' ), admin_url( 'edit.php' ) ) ) );

        }
        
        $this->oUserAds = isset( $GLOBALS['oAmazonAutoLinksUserAds'] ) ? $GLOBALS['oAmazonAutoLinksUserAds'] : new AmazonAutoLinks_UserAds;
                    
    }
        
}