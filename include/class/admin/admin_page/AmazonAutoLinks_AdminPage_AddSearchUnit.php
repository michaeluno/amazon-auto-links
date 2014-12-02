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
abstract class AmazonAutoLinks_AdminPage_AddSearchUnit extends AmazonAutoLinks_AdminPage_DefineAutoInsert {

    /*
     * The Add Unit by Search Page
     * 
     */     
    public function load_aal_add_search_unit_search_products() {

        // Validation callbacks sets it in the $_POST array so check the $_REQUEST array.
        if ( ! isset( $_REQUEST['transient_id'] ) || false === AmazonAutoLinks_WPUtilities::getTransient( "AAL_CreateUnit_" . $_REQUEST['transient_id'] ) ) {
            
            $strMessage = __( 'A problem occurred while loading the page of adding a search unit. Please go back to the previous page.', 'amazon-auto-links' );
            // $this->setSettingNotice( $strMessage );
            die( "<div class='error'><p>{$strMessage}</p></div>" );
            
        }            
    
    }
    
    public function validation_aal_add_search_unit_initial_search_settings( $aInput, $aOldInput ) {    // validation_{page slug}_{tab slug}
        
        $fVerified = true;
        $arrErrors = array();
        $arrSearchOptions = $aInput['aal_add_search_unit']['search'];
    
        // Check the limitation.
        if ( $this->oOption->isUnitLimitReached() ) {

            $this->setFieldErrors( array( 'error' ) );        // must set an field error array which does not yield empty so that it won't be redirected.
            $this->setSettingNotice( 
                sprintf( 
                    __( 'Please upgrade to <A href="%1$s">Pro</a> to add more units! Make sure to empty the <a href="%2$s">trash box</a> to delete the units completely!', 'amazon-auto-links' ), 
                    'http://en.michaeluno.jp/amazon-auto-links-pro/',
                    admin_url( 'edit.php?post_status=trash&post_type=' . AmazonAutoLinks_Commons::PostTypeSlug )
                )
            );
            return $aOldInput;
            
        }         
        
        // If the Access Key fields are present, it means the user has not set them yet in the Settings page.
        // In this case, just check if they are valid and if so, save them in the settings' option array. Otherwise, return an error.
        if ( isset( $arrSearchOptions['search_access_key'], $arrSearchOptions['search_access_key_secret'] ) ) {

            $strPublicKey = $arrSearchOptions['search_access_key'];
            if ( strlen( $strPublicKey ) != 20 ) {
                $arrErrors['search']['search_access_key'] = __( 'The Access Key ID must consist of 20 characters.', 'amazon-auto-links' ) . ': ' . $strPublicKey . ' ';
                $fVerified = false;                
            }
            $strPrivateKey = $arrSearchOptions['search_access_key_secret'];
            if ( strlen( $strPrivateKey ) != 40 ) {
                $arrErrors['search']['search_access_key_secret'] = __( 'The Secret Access Key must consist of 40 characters.', 'amazon-auto-links' ) . ': ' . $strPrivateKey . ' ';
                $fVerified = false;
            }    
            
            // An invalid value is found.
            if ( ! $fVerified ) {
            
                // Set the error array for the input fields.
                $this->setFieldErrors( $arrErrors );
                $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
                return $aOldInput;
                
            }                
            
            // Test authentication - browse the Books node in amazon.com.
            $oAmazonAPI = new AmazonAutoLinks_ProductAdvertisingAPI( 'com', $strPublicKey, $strPrivateKey );
            if ( ! $oAmazonAPI->test() ) {
                
                $arrErrors['search']['search_access_key'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $strPublicKey;
                $arrErrors['search']['search_access_key_secret'] = __( 'Sent Value', 'amazon-auto-links' ) . ': ' . $strPrivateKey;            
                $this->setFieldErrors( $arrErrors );
                $this->setSettingNotice( __( 'Failed authentication.', 'amazon-auto-links' ) );
                $aOldInput;
                
            }        

            // It is authenticated, so set the keys in the Settings option array.
            // Since the validation_ callbacks internally merge with the framework's property option array,
            // modify the property array, NOT the option object that plugin creates.
            $this->oProps->arrOptions['aal_settings']['authentication_keys']['access_key'] = $strPublicKey;
            $this->oProps->arrOptions['aal_settings']['authentication_keys']['access_key_secret'] = $strPrivateKey;
            
        }
        
        if ( empty( $arrSearchOptions['search_associate_id'] ) ) {
            
            $arrErrors['search']['search_associate_id'] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $fVerified = false;                            
            
        }
    
        // An invalid value is found.
        if ( ! $fVerified ) {
        
            // Set the error array for the input fields.
            $this->setFieldErrors( $arrErrors );
            $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aOldInput;
            
        }                    
                
        // Drop the sections.
        $_aNewFields = array();
        foreach( $aInput['aal_add_search_unit'] as $strSection => $arrFields  ) 
            $_aNewFields = $_aNewFields + $arrFields;
        
        // Remove the search_ prefix in the keys.
        $_aSanitizedFields = array();
        foreach( $_aNewFields as $strKey => $vValue ) 
            $_aSanitizedFields[ preg_replace( '/^search_/', '', $strKey ) ] = $vValue;
    
        // Set the unit type based on the chosen one.
        // Redirect to the appropriate page by the search type.
        switch( $_aSanitizedFields['Operation'] ) {
            case 'ItemSearch':
                $_aSanitizedFields['unit_type'] = 'search';
                $sTabSlug = 'search_products';                
                break;
            case 'ItemLookup':
                $_aSanitizedFields['unit_type'] = 'item_lookup';
                $sTabSlug = 'item_lookup';
                break;
            case 'SimilarityLookup':
                $_aSanitizedFields['unit_type'] = 'similarity_lookup';
                $sTabSlug = 'similarity_lookup';
                break;
        }
            
        // Save the transient
        $arrTempUnitOptions = ( array ) AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $_aSanitizedFields['transient_id'] );
        $aSavingUnitOptions = AmazonAutoLinks_Utilities::uniteArrays( $_aSanitizedFields, $arrTempUnitOptions );
        AmazonAutoLinks_WPUtilities::setTransient( 'AAL_CreateUnit_' . $_aSanitizedFields['transient_id'], $aSavingUnitOptions, 60*10*6*24 );
                                    
        // Go to the next page.
        die( wp_redirect( add_query_arg( array( 'tab' => $sTabSlug, 'transient_id' => $_aSanitizedFields['transient_id'] ) + $_GET, $_aSanitizedFields['bounce_url'] ) ) );
                                            
    }
    
    public function validation_aal_add_search_unit_search_products( $aInput, $aOldInput ) {    // validation_ + page slug + tab slug
        $this->_createSearchUnit( $aInput );
    }

    public function validation_aal_add_search_unit_item_lookup( $aInput, $aOldInput ) {    // validation_ + page slug + tab slug        
        $this->_createSearchUnit( $aInput );
    }

    public function validation_aal_add_search_unit_similarity_lookup( $aInput, $aOldInput ) {    // validation_ + page slug + tab slug        
        $this->_createSearchUnit( $aInput );
    }
    
    /**
     * Creates a search unit type
     * 
     * @since            2.0.2
     */
    protected function _createSearchUnit( $aInput ) {

        // Drop the sections.
        $_aNewFields = array();
        foreach( $aInput['aal_add_search_unit'] as $_sSection => $_aFields ) {
            $_aNewFields = $_aNewFields + $_aFields;
        }
        
        // Remove the search_ prefix in the keys.
        $_aSanitizedFields = array();
        foreach( $_aNewFields as $_sKey => $_vValue ) {
            $_aSanitizedFields[ preg_replace( '/^search\d_/', '', $_sKey ) ] = $_vValue;
        }
        $_aSanitizedFields = $this->oOption->sanitizeUnitOpitons( $_aSanitizedFields );

        // Create a post.            
        $_fDoAutoInsert = $_aSanitizedFields['auto_insert'];
        unset( $_aSanitizedFields['auto_insert'] );
        $_iNewPostID = AmazonAutoLinks_Option::insertPost( $_aSanitizedFields );
        
        // Create an auto insert
        if ( $_fDoAutoInsert ) {
            $_aAutoInsertOptions = array( 
                    'unit_ids' => array( $_iNewPostID ) 
                ) + AmazonAutoLinks_Form_AutoInsert::$arrStructure_AutoInsertOptions;
            AmazonAutoLinks_Option::insertPost( $_aAutoInsertOptions, AmazonAutoLinks_Commons::PostTypeSlugAutoInsert );
        }        
        die( wp_redirect( 
            // e.g. http://.../wp-admin/post.php?post=196&action=edit&post_type=amazon_auto_links
            add_query_arg( 
                array( 
                    'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,
                    'action' => 'edit',
                    'post' => $_iNewPostID,
                ), 
                admin_url( 'post.php' ) 
            )
        ) );
                
    }
    
}