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
abstract class AmazonAutoLinks_AdminPage_Debug extends AmazonAutoLinks_AdminPage_Menu {

    /*
     * The Debug page
     * */
     public function do_aal_debug() {
            
        echo "<h3>Current URL</h3>";    
        echo $strCurrentURL = add_query_arg( $_GET, admin_url( $GLOBALS['pagenow'] ) );
        echo "<h3>Modified URL</h3>";    
        $arrQuery = array( 'post_type' => 'hello' ) + $_GET;
        unset( $arrQuery['post_type'] );
        echo add_query_arg( $arrQuery, admin_url( $GLOBALS['pagenow'] ) );        
            
        echo "<h3>V1 Options</h3>";    
        
        $arrV1Options = get_option( 'amazonautolinks' );
        unset( $arrV1Options['tab100'] );
        unset( $arrV1Options['tab101'] );
        unset( $arrV1Options['tab200'] );
        unset( $arrV1Options['tab201'] );
        unset( $arrV1Options['tab202'] );
        unset( $arrV1Options['tab203'] );
        unset( $arrV1Options['tab300'] );
        unset( $arrV1Options['editunit'] );
        unset( $arrV1Options[ 0 ] );
         
        $this->oDebug->dumpArray( $arrV1Options );
            
     }
}