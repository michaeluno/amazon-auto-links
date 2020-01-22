<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2020 Michael Uno
 */

/**
 * Adds the 'Add New Auto-insert' tab to the 'Add/Edit Auto-insert' page.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_Edit
 */
class AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_New extends AmazonAutoLinks_AutoInsertAdminPage_AutoInsert_Edit {
    
    /**
     * Returns a list of classes that define form fields.
     * @access      protected       The extended ..._New  class will override this mthoed.
     * @return      array
     */
    protected function getFormFieldClasses() {         

        return array(
            // 'AmazonAutoLinks_FormFields_AutoInsert_GoBack',
            'AmazonAutoLinks_FormFields_AutoInsert_Status',
            // 'AmazonAutoLinks_FormFields_AutoInsert_PostID',
            'AmazonAutoLinks_FormFields_AutoInsert_Area',
            'AmazonAutoLinks_FormFields_AutoInsert_Static',
            'AmazonAutoLinks_FormFields_AutoInsert_WhereToEnable',
            'AmazonAutoLinks_FormFields_AutoInsert_WhereToDisable',
            'AmazonAutoLinks_FormFields_AutoInsert_Save',
        );      
     
    }    
        
    /**
     * Form field validation.
     * 
     * @callback        validation_{page slug}_{tab_slug}
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {
        
        $_aInput = parent::validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo );
        $_sURL   = add_query_arg(
            array(
                'post_type' => AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ],
            ),
            admin_url( 'edit.php' )
        );
        exit(
            wp_redirect( $_sURL )
        );
        
    }       
        
        /**
         * 
         * 
         * @access      protected as the ..._New class extends this class and acess this method.
         */
        protected function _updatePostMeta( $iPostID, $aMeta ) {
            AmazonAutoLinks_WPUtility::insertPost( 
                $aMeta, 
                AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
            );
        }
    
}
