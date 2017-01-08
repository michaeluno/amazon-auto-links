<?php
abstract class AmazonAutoLinks_MetaBox_Misc_ {

    public function __construct() {
        
        if ( isset( $_GET['post_type'] ) && $_GET['post_type'] != AmazonAutoLinks_Commons::PostTypeSlug ) return;
        add_action( 'add_meta_boxes', array( $this, 'addCustomMetaBoxes' ) );    
        
    }
    
    public function addCustomMetaBoxes() {

        // Sponsors' box.
        add_meta_box( 
            'miunosoft-sponsors',         // id
            __( 'Information', 'amazon-auto-links' ),     // title
            array( $this, 'callSponsors' ),     // callback
            AmazonAutoLinks_Commons::PostTypeSlug,        // post type
            'side',     // context ('normal', 'advanced', or 'side'). 
            'low',    // priority ('high', 'core', 'default' or 'low') 
            null // callback argument
        );    
        
    }
    
    public function callSponsors() {
    
    // $current_user = wp_get_current_user();
    // echo "<pre>" . print_r( $current_user, true ) . "</pre>";

    
        $oUserAds = isset( $GLOBALS['oAmazonAutoLinksUserAds'] ) ? $GLOBALS['oAmazonAutoLinksUserAds'] : new AmazonAutoLinks_UserAds;
        echo rand ( 0 , 1 )
            ? $oUserAds->get250xNTopRight() 
            : $oUserAds->get250xN( 2 );
            
    }    
    
}