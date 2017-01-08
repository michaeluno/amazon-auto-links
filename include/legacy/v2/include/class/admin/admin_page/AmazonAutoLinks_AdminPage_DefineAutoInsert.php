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
abstract class AmazonAutoLinks_AdminPage_DefineAutoInsert extends AmazonAutoLinks_AdminPage_Setting {

    /**
     * The define Auto Insert page.
     */
    public function validation_aal_define_auto_insert( $arrInput, $arrOldInput ) {
        
        // Drop the sections.
        $arrNewFields = array();
        foreach( $arrInput['aal_define_auto_insert'] as $strSection => $arrFields  ) 
            $arrNewFields = $arrNewFields + $arrFields;

        // Remove the search_ prefix in the keys.
        $arrSanitizedFields = array();
        foreach( $arrNewFields as $strKey => $vValue ) 
            $arrSanitizedFields[ preg_replace( '/^autoinsert_/', '', $strKey ) ] = $vValue;
        $fVerified = true;
        $arrErrors = array();    
        
        // Check necessary settings.
        if ( ! array_filter( $arrSanitizedFields['built_in_areas'] +  $arrSanitizedFields['static_areas'] ) 
            && ! $arrSanitizedFields['filter_hooks']
            && ! $arrSanitizedFields['action_hooks']
            ) {
                
            $arrErrors['autoinsert_area']['autoinsert_built_in_areas'] = __( 'At least one area must be set.', 'amazon-auto-links' );
            $arrErrors['autoinsert_static_insertion']['autoinsert_static_areas'] = __( 'At least one area must be set.', 'amazon-auto-links' );
            $arrErrors['autoinsert_area']['autoinsert_filter_hooks'] = __( 'At least one area must be set.', 'amazon-auto-links' );
            $arrErrors['autoinsert_area']['autoinsert_action_hooks'] = __( 'At least one area must be set.', 'amazon-auto-links' );
            $fVerified = false;
            
        }
        if ( ! isset( $arrSanitizedFields['unit_ids'] ) ) {    // if no item is selected, the select input with the multiple attribute does not send the key.
            
            $arrErrors['autoinsert_area']['autoinsert_unit_ids'] = __( 'A unit must be selected.', 'amazon-auto-links' );
            $fVerified = false;
            
        }
        
        // An invalid value is found.
        if ( ! $fVerified ) {
        
            // Set the error array for the input fields.
            $this->setFieldErrors( $arrErrors );
            $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $arrOldInput;
            
        }                
        
        $arrSanitizedFields['filter_hooks'] = AmazonAutoLinks_Utilities::trimDelimitedElements( $arrSanitizedFields['filter_hooks'], ',' );
        $arrSanitizedFields['action_hooks'] = AmazonAutoLinks_Utilities::trimDelimitedElements( $arrSanitizedFields['action_hooks'], ',' );
        $arrSanitizedFields['enable_post_ids'] = AmazonAutoLinks_Utilities::trimDelimitedElements( $arrSanitizedFields['enable_post_ids'], ',' );
        $arrSanitizedFields['diable_post_ids'] = AmazonAutoLinks_Utilities::trimDelimitedElements( $arrSanitizedFields['diable_post_ids'], ',' );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields );
        
        
        // Edit - Update the post.
        $fIsEdit = ( isset( $_POST['mode'], $_POST['post'] ) && $_POST['post'] && $_POST['mode'] == 'edit' );
        if ( $fIsEdit )
            AmazonAutoLinks_Option::updatePostMeta( $_POST['post'], $arrSanitizedFields );
        else    // New - Create a post.    
            $intNewPostID = AmazonAutoLinks_Option::insertPost( $arrSanitizedFields, AmazonAutoLinks_Commons::PostTypeSlugAutoInsert );
        
        // e.g. http://.../wp-admin/edit.php?post_type=aal_auto_insert
        die( 
            wp_redirect( 
                $fIsEdit    // edit.php?post_type=amazon_auto_links&page=aal_define_auto_insert&mode=edit&post=265 
                    ? admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlug . '&page=aal_define_auto_insert&mode=edit&post=' . $_POST['post'] ) // stay on the same page.
                    : admin_url( 'edit.php?post_type=' . AmazonAutoLinks_Commons::PostTypeSlugAutoInsert )     // the listing table page
            ) 
        );
                
    }
    
}