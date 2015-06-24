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
abstract class AmazonAutoLinks_AdminPage_AddUnitByTag extends AmazonAutoLinks_AdminPage_AddSearchUnit {

    /*
     * The Add Unit by Tag Page
     * 
     */ 
    public function validation_aal_add_tag_unit( $arrInput, $arrOldInput ) {    // validation + _ + page slug + tab slug

        $fVerified = true;
        $arrErrors = array();
        
        // Check the limitation.
        if ( $this->oOption->isUnitLimitReached() ) {
            
            $this->setSettingNotice( 
                sprintf( 
                    __( 'Please upgrade to <A href="%1$s">Pro</a> to add more units! Make sure to empty the <a href="%2$s">trash box</a> to delete the units completely!', 'amazon-auto-links' ), 
                    'http://en.michaeluno.jp/amazon-auto-links-pro/',
                    admin_url( 'edit.php?post_status=trash&post_type=' . AmazonAutoLinks_Commons::PostTypeSlug )
                )
            );
            return $arrOldInput;
            
        }        
        
        // Customer ID must be 13 characters
        if ( $arrInput['aal_add_tag_unit']['tag']['tag_customer_id'] && strlen( $arrInput['aal_add_tag_unit']['tag']['tag_customer_id'] ) != 13 ) {
            
            $arrErrors['tag']['tag_customer_id'] = __( 'The customer ID must consist of 13 characters.', 'amazon-auto-links' ) . ' ';
            $arrInput['aal_add_tag_unit']['tag']['tag_customer_id'] = '';
            $fVerified = false;
            
        }
        
        if ( empty( $arrInput['aal_add_tag_unit']['tag']['tag_tags'] ) && empty( $arrInput['aal_add_tag_unit']['tag']['tag_customer_id'] ) ) {
            
            $arrErrors['tag']['tag_tags'] = __( 'Either tags or customer ID has to be entered.', 'amazon-auto-links' );
            
            $strMessage = __( 'Either tags or customer ID has to be entered.', 'amazon-auto-links' );
            $arrErrors['tag']['tag_customer_id'] = isset( $arrErrors['tag']['tag_customer_id'] )
                ? $arrErrors['tag']['tag_customer_id'] . $strMessage
                : $strMessage;
            $fVerified = false;
            
        }
        
        if ( empty( $arrInput['aal_add_tag_unit']['tag']['tag_associate_id'] ) ) {
            
            $arrErrors['tag']['tag_associate_id'] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $fVerified = false;
            
        }
        
        // An invalid value is found.
        if ( ! $fVerified ) {
        
            // Set the error array for the input fields.
            $this->setFieldErrors( $arrErrors );        
            $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $arrOldInput;
            
        }        
            
        // Drop the sections.
        $arrNewFields = array();
        foreach( $arrInput['aal_add_tag_unit'] as $strSection => $arrFields  ) 
            $arrNewFields = $arrNewFields + $arrFields;
        $arrSanitizedFields = array();
        
        // Remove the tag_ prefix in the keys.
        foreach( $arrNewFields as $strKey => $vValue ) 
            $arrSanitizedFields[ preg_replace( '/^tag_/', '', $strKey ) ] = $vValue;
        
        // Sanitize the tag input
        $arrSanitizedFields['tags'] = trim( AmazonAutoLinks_Utilities::trimDelimitedElements( $arrSanitizedFields['tags'], ',' ) ); 
        
        $arrSanitizedFields = $this->oOption->sanitizeUnitOpitons( $arrSanitizedFields );
        
        // If nothing is checked for the feed type, enable the bestseller item.
        if ( ! array_filter( $arrSanitizedFields['feed_type'] ) )             
            $arrSanitizedFields['feed_type']['new'] = true;        

// AmazonAutoLinks_Debug::logArray( '--Before Escaping KSES Filter--' );            
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['item_format'] );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['image_format'] );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['title_format'] );

        // Apply allowed HTML tags for the KSES filter.
        add_filter( 'safe_style_css', array( $this, 'allowInlineStyleMaxWidth' ) );
        $arrAllowedHTMLTags = AmazonAutoLinks_Utilities::convertStringToArray( $this->oOption->arrOptions['aal_settings']['form_options']['allowed_html_tags'], ',' );
        $arrSanitizedFields['item_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrSanitizedFields['item_format'], $arrAllowedHTMLTags );
        $arrSanitizedFields['image_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrSanitizedFields['image_format'], $arrAllowedHTMLTags );
        $arrSanitizedFields['title_format'] = AmazonAutoLinks_WPUtilities::escapeKSESFilter( $arrSanitizedFields['title_format'], $arrAllowedHTMLTags );
        remove_filter( 'safe_style_css', array( $this, 'allowInlineStyleMaxWidth' ) );
        
// AmazonAutoLinks_Debug::logArray( '--After Escaping KSES Filter--' );
// AmazonAutoLinks_Debug::logArray( $arrAllowedHTMLTags );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['item_format'] );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['image_format'] );
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields['title_format'] );

        // Create a post.            
        $fDoAutoInsert = $arrSanitizedFields['auto_insert'];
        unset( $arrSanitizedFields['auto_insert'] );
        
        $intNewPostID = AmazonAutoLinks_Option::insertPost( $arrSanitizedFields );
        
        // Create an auto insert
        if ( $fDoAutoInsert ) {
            
            $arrAutoInsertOptions = array( 
                    'unit_ids' => array( $intNewPostID ) 
                ) + AmazonAutoLinks_Form_AutoInsert::$arrStructure_AutoInsertOptions;
            
            AmazonAutoLinks_Option::insertPost( $arrAutoInsertOptions, AmazonAutoLinks_Commons::PostTypeSlugAutoInsert );
            
        }
        
        die( wp_redirect( 
            // e.g. http://.../wp-admin/post.php?post=196&action=edit&post_type=amazon_auto_links
            add_query_arg( 
                array( 
                    'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,
                    'action' => 'edit',
                    'post' => $intNewPostID,
                ), 
                admin_url( 'post.php' ) 
            )
        ) );        
        
    }
        public function allowInlineStyleMaxWidth( $arrProperties ) {
            $arrProperties[] = 'max-width';
            return $arrProperties;
        }
            
}