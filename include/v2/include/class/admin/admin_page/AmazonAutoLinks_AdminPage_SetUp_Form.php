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
abstract class AmazonAutoLinks_AdminPage_SetUp_Form extends AmazonAutoLinks_AdminPage_SetUp_Page {

    protected function _setUpForms() {

        /*
         * Form elements - Sections
         */

        // Form Elements - Add Unit by Category 
        $oCategoryFormElements = new AmazonAutoLinks_Form_Category( 'aal_add_category_unit' );
        call_user_func_array( array( $this, "addSettingSections" ), $oCategoryFormElements->getSections() );
        call_user_func_array( array( $this, "addSettingFields" ), $oCategoryFormElements->getFields( 'category' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oCategoryFormElements->getFields( 'category_auto_insert' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oCategoryFormElements->getFields( 'category_template' ) );
                                    
        // Form Elements - Add Unit by Tag and Customer ID 
        $oTagFormElements = new AmazonAutoLinks_Form_Tag( 'aal_add_tag_unit' );
        call_user_func_array( array( $this, "addSettingSections" ), $oTagFormElements->getSections() );
        call_user_func_array( array( $this, "addSettingFields" ), $oTagFormElements->getFields( 'tag' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oTagFormElements->getFields( 'tag_auto_insert' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oTagFormElements->getFields( 'tag_template' ) );

        // Form Elements - Add Unit by Search
        $oSearchFormElements = new AmazonAutoLinks_Form_Search( 'aal_add_search_unit' );
        call_user_func_array( array( $this, "addSettingSections" ), $oSearchFormElements->getSections() );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search' ) );
        
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_second', 'search2_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_advanced', 'search2_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_auto_insert', 'search2_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_template', 'search2_' ) );
        
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_item_lookup', 'search3_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_item_lookup_advanced', 'search3_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_auto_insert2', 'search3_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_template2', 'search3_' ) );

        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'similarity_lookup', 'search4_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'similarity_lookup_advanced', 'search4_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_auto_insert3', 'search4_' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oSearchFormElements->getFields( 'search_template3', 'search4_' ) );
        
        // Form elements - Add / Edit Auto Insert
        $oAutoInsertFormElements = new AmazonAutoLinks_Form_AutoInsert( 'aal_define_auto_insert' );
        call_user_func_array( array( $this, "addSettingSections" ), $oAutoInsertFormElements->getSections() );
        call_user_func_array( array( $this, "addSettingFields" ), $oAutoInsertFormElements->getFields( 'autoinsert_status' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oAutoInsertFormElements->getFields( 'autoinsert_area' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oAutoInsertFormElements->getFields( 'autoinsert_static_insertion' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oAutoInsertFormElements->getFields( 'autoinsert_enable' ) );
        call_user_func_array( array( $this, "addSettingFields" ), $oAutoInsertFormElements->getFields( 'autoinsert_disable' ) );
        
        // Form elements - Settings 
        $oSettingsFormElements = new AmazonAutoLinks_Form_Settings( 'aal_settings' );
        call_user_func_array( array( $this, "addSettingSections" ), $oSettingsFormElements->getSections() );
        call_user_func_array( array( $this, "addSettingFields" ), $oSettingsFormElements->getFields() );    
    
    }
        
}