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
 * Defines the meta box added to the 'feed' unit definition page.
 * @since       4.0.0
 */
class AmazonAutoLinks_UnitPostMetaBox_Main_feed extends AmazonAutoLinks_UnitPostMetaBox_Base {
    
    /**
     * Stores the unit type slug(s). 
     */    
    protected $aUnitTypes = array( 'feed' );
    
    /**
     * Sets up form fields.
     */ 
    public function setUp() {
        
        $_oFields = new AmazonAutoLinks_FormFields_FeedUnit_Main;
        foreach( $_oFields->get() as $_aField ) {
            if ( in_array( $_aField[ 'field_id' ], array( 'unit_title', 'country' ) ) ) {
                continue;
            }
            $this->addSettingFields( $_aField );
        }

    }

    public function load() {
        add_action( 'do_meta_boxes', array( $this, 'replyToRemoveMetaBoxes' ) );
    }
        public function replyToRemoveMetaBoxes() {
            remove_meta_box(
                'amazon_auto_links_locale',
                AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ], // screen: post type slug
                'side'
            );
        }


    /**
     * Validates submitted form data.
     */
    public function validate( $aInputs, $aOriginal, $oFactory ) {    
        
        $_aErrors   = array();
        $_bVerified = true;
        
        // Formats the options
        $_oUnitOption = new AmazonAutoLinks_UnitOption_feed(null, $aInputs );
        $_aFormatted  = $_oUnitOption->get();
        
        // Check if a feed url is set.
        $aInputs[ 'feed_urls' ] = $this->oUtil->getAsArray( $aInputs[ 'feed_urls' ] );
        if ( empty( $aInputs[ 'feed_urls' ] ) ) {
            $_aErrors[ 'feed_urls' ] = __( 'Please set a url.', 'amazon-auto-links' );
            $_bVerified = false;
        }        
        
        // An invalid value is found.
        if ( ! $_bVerified ) {
        
            // Set the error array for the input fields.
            $oFactory->setFieldErrors( $_aErrors );        
            $oFactory->setSettingNotice( __( 'There was an error in your input.', 'amazon-auto-links' ) );
            return $aInputs;
            
        }       

        // Sanitize
        foreach ( $aInputs[ 'feed_urls' ] as $_iIndex => $_sURL ) {
            $aInputs[ 'feed_urls' ][ $_iIndex ] = trim( $_sURL );
        }
        
        
        // Drop unsent keys.
        foreach( $_aFormatted as $_sKey => $_mValue ) {
            if ( ! array_key_exists( $_sKey, $aInputs ) ) {
                unset( $_aFormatted[ $_sKey ] );
            }
        }
        
        // Schedule pre-fetch for the unit if the options have been changed.
        if ( $aInputs !== $aOriginal ) {
            AmazonAutoLinks_Event_Scheduler::prefetch(
                AmazonAutoLinks_PluginUtility::getCurrentPostID()
            );
        }
        
        return $_aFormatted + $aInputs;
        
    }
    
}