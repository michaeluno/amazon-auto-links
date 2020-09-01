<?php
/**
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 * 
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013-2020, Michael Uno
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

        parent::setUp();    // needs to run to set the _sCustomNonce property first.

        if (  $this->_isInThePage() ) {
            new AmazonAutoLinks_PostType__AutoInsert___ActionLink_Status(
                $this,
                $this->_sNonceKey,
                $this->_sCustomNonce
            );
            add_filter(
                'action_links_' . $this->oProp->sPostType,
                array( $this, 'replyToModifyActionLinks' ),
                10,
                2
            );
            add_filter(
                'bulk_actions-edit-' . $this->oProp->sPostType,
                array( $this, 'replyToModifyBulkActionsDropDownList' )
             );
        }        
        

    }

    /**
     * 
     * @callback        filter      action_links_{post type slug}
     */
    public function replyToModifyActionLinks( $aActions, WP_Post $oPost ) {

        unset( $aActions['inline'] );
        unset( $aActions['inline hide-if-no-js'] );

        $_sSettingPageURL = add_query_arg( 
            array( 
                // the main post type slug, not auto-insert's.  \'
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],    
                'page'      => AmazonAutoLinks_Registry::$aAdminPages[ 'auto_insert' ],
                'tab'       => 'edit',
                'post'      => $oPost->ID,
            ), 
            admin_url( 'edit.php' ) 
        );            

        $aActions[ 'edit' ] = "<a href='{$_sSettingPageURL}'>" 
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


}