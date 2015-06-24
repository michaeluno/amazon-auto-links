<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2015, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 */

/**
 * Provides methods for defining columns of listing table of the auto-insert post type.
 * @since       3
 */
class AmazonAutoLinks_PostType_AutoInsert_Action extends AmazonAutoLinks_PostType_AutoInsert_Column {

    /**
     * Sets up hooks.
     */
    public function setUp() {
        
        if (  $this->_isInThePage() ) {

            $this->handleCustomActions();   
        
            add_filter( 
                'post_row_actions', 
                array( $this, 'replyToModifyRowActions' ), 
                10, 
                2 
            );
            add_filter( 
                'bulk_actions-edit-' . $this->oProp->sPostType, 
                array( $this, 'replyToModifyBulkActionsDropDownList' ) 
             );
        }        
        
        parent::setUp();
    }

    /**
     * 
     * @callback        filter      post_row_actions
     */
    public function replyToModifyRowActions( $aActions, WP_Post $oPost ) {
// @todo add Disable, Enable actions.        
return $aActions;        
        if ( $oPost->post_type != $this->oProp->sPostType ) {
            return $aActions;
        }
        
        unset( $aActions['inline'] );
        unset( $aActions['inline hide-if-no-js'] );
        // http://.../wp-admin/edit.php?post_type=amazon_auto_links&page=aal_define_auto_insert
        $_sSettingPageURL = add_query_arg( 
            array( 
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    // the main post type slug, not auto-insert's.
                'page'      => 'aal_define_auto_insert',
                'mode'      => 'edit',
                'post'      => $oPost->ID,
            ), 
            admin_url( 'edit.php' ) 
        );    
        $aActions['edit'] = "<a href='{$_sSettingPageURL}'>" 
                . __( 'Edit', 'amazon-auto-links' ) 
            . "</a>";
        
        return $aActions;
    }
    
    /**
     * 
     * @callback    filter       bulk_action-edit-{post type slug}
     * @return      array
     */
    public function replyToModifyBulkActionsDropDownList( $aBulkActions ) {
        unset( $aBulkActions[ 'edit' ] );
        return $aBulkActions;
    }    
    
    protected function handleCustomActions() {
        
        if ( ! isset( $_GET['custom_action'], $_GET['nonce'], $_GET['post'] ) ) { 
            return; 
        }
        
        $_sNonce = AmazonAutoLinks_WPUtility::getTransient( 'AAL_Nonce_' . $_GET['nonce'] );
        if ( false === $_sNonce ) { 
            add_action( 'admin_notices', array( $this, 'replyToNotifyNonceFailed' ) );
            return;
        }
        AmazonAutoLinks_WPUtility::deleteTransient( 'AAL_Nonce_' . $_GET['nonce'] );
        
        // Currently only the status toggle is supported.
        If ( 'toggle_status' === $_GET['custom_action'] && $_GET['post'] ) {
            
            $_aUnitIDs = get_post_meta( $_GET['post'], 'unit_ids', true );    
            // if this field is empty, the post must be the wrong post type.
            if ( empty( $_aUnitIDs ) ) { 
                return; 
            }  
            
            $_bIsEnabled = get_post_meta( $_GET['post'], 'status', true );
            update_post_meta( $_GET['post'], 'status', ! $_bIsEnabled );
            
        }
    
    }
    
    /**
     * 
     * @callback        action      admin_notice
     */
    public function replyToNotifyNonceFailed() {
        echo '<div class="error">'
                . '<p>' 
                    . __( 'The action could not be processed due to the inactivity.', 'amazon-auto-links' )
                . '</p>'
            . '</div>';
    }
       

}