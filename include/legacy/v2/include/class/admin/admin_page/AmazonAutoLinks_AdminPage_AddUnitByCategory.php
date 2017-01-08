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
abstract class AmazonAutoLinks_AdminPage_AddUnitByCategory extends AmazonAutoLinks_AdminPage_AddUnitByTag {

    /*
     * The Add Unit by Category Page 
     * 
     */
    public function do_aal_add_category_unit() {
        // echo AmazonAutoLinks_Debug::getArray( $this->oProps );
    }
    public function load_aal_add_category_unit_select_categories() {    // load_ + page slug + _  + tab slug

        // Retrieve the submitted options. 
        $strTransientID = isset( $_GET['transient_id'] ) ? $_GET['transient_id'] : '';
        $arrOptions = AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $strTransientID );
        
        // Note that this method is called in the validation callback as well whose page is options.php and does not have $_GET parameters.
        if ( ! isset( $_GET['post'] ) && $arrOptions === false )
            die ( "<div class='error'><p>" . __( 'An error occurred. Please go back to the previous page and do it again.', 'amazon-auto-links' ) . "</p></div>" );
            
        $this->oCategorySelect = new AmazonAutoLinks_CategorySelect( $arrOptions );    
    
    }
    public function do_aal_add_category_unit_select_categories() {    // do_ + page slug + _  + tab slug
        
        $strTransientID = isset( $_GET['transient_id'] ) ? $_GET['transient_id'] : '';
        $arrOptions = $this->oCategorySelect->renderForm();
        AmazonAutoLinks_WPUtilities::setTransient( 'AAL_CreateUnit_' . $strTransientID, $arrOptions, 60*10*6*24 );    // this transient should be deleted when creating a new unit.
        
    }
    
    public function validation_aal_add_category_unit_set_category_unit_options( $aInput, $aOldInput ) {    // validation + _ + page slug + tab slug
        
        $_fVerified = true;
        $_aErrors = array();
        
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
        
        if ( empty( $aInput['aal_add_category_unit']['category']['category_associate_id'] ) ) {
            
            $_aErrors['category']['category_associate_id'] = __( 'The associate ID cannot be empty.', 'amazon-auto-links' );
            $_fVerified = false;
            
        }
                            
        // An invalid value is found.
        if ( ! $_fVerified ) {
        
            // Set the error array for the input fields.
            $this->setFieldErrors( $_aErrors );        
            $this->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aOldInput;
            
        }        
            
        // Drop the sections.
        $arrNewFields = array();
        foreach( $aInput['aal_add_category_unit'] as $strSection => $arrFields  ) 
            $arrNewFields = $arrNewFields + $arrFields;
        $arrSanitizedFields = array();
        
        // Remove the category_ prefix in the keys.
        foreach( $arrNewFields as $strKey => $vValue ) 
            $arrSanitizedFields[ preg_replace( '/^category_/', '', $strKey ) ] = $vValue;
        $arrSanitizedFields['categories'] = array();
        $arrSanitizedFields['categories_exclude'] = array();
        
        $arrSanitizedFields = $this->oOption->sanitizeUnitOpitons( $arrSanitizedFields );
            
        // If nothing is checked for the feed type, enable the bestseller item.
        if ( ! array_filter( $arrSanitizedFields['feed_type'] ) ) {
            $arrSanitizedFields['feed_type']['bestsellers'] = true;
        }    
        
        $arrTempUnitOptions = ( array ) AmazonAutoLinks_WPUtilities::getTransient( 'AAL_CreateUnit_' . $arrSanitizedFields['transient_id'] );
        AmazonAutoLinks_WPUtilities::setTransient( 'AAL_CreateUnit_' . $arrSanitizedFields['transient_id'], AmazonAutoLinks_Utilities::uniteArrays( $arrSanitizedFields, $arrTempUnitOptions ), 60*10*6*24 );
            
// AmazonAutoLinks_Debug::logArray( $arrSanitizedFields );
        
        return $aInput;
        
    }
    
}