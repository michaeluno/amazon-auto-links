<?php
/**
 * Amazon Auto Links
 * 
 * 
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2015 Michael Uno; Licensed GPLv2
 * 
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
            'AmazonAutoLinks_FormFields_AutoInsert_GoBack',
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
         * 
         * 
         * @access      protected as the ..._New class extends this class and acess this method.
         */
        protected function updatePostMeta( $iPostID, $aMeta ) {
            AmazonAutoLinks_WPUtility::insertPost( 
                $aMeta, 
                AmazonAutoLinks_Registry::$aPostTypes[ 'auto_insert' ]
            );
        }
    
}
