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
 * Adds the 'Unit Preview' form section to the 'General' tab.
 * 
 * @since       3
 */
class AmazonAutoLinks_AdminPage_Setting_General_UnitPreview extends AmazonAutoLinks_AdminPage_Section_Base {

    /**
     * @return array
     * @since   3.11.1
     */
    protected function _getArguments() {
        return array(
            'section_id'    => 'unit_preview',
            'title'         => __( 'Unit Preview', 'amazon-auto-links' ),
        );
    }

    /**
     * A user constructor.
     * 
     * @since       3
     * @return      void
     */
    protected function _construct( $oFactory ) {}
    
    /**
     * Adds form fields.
     * @since       3
     * @return      void
     */
    protected function _addFields( $oFactory, $sSectionID ) {
    
        $oFactory->addSettingFields(
            $sSectionID, // the target section id
            array(
                'field_id'       => 'preview_post_type_label',
                'type'           => 'text',
                'title'          => __( 'Post Type Label', 'amazon-auto-links' ),
                'tip'            => __( 'Set the name which appear in breadcrumbs.', 'amazon-auto-links' ),
                'description'    => __( 'Default', 'amazon-auto-links' )
                    . ': <code>'
                        . AmazonAutoLinks_Registry::NAME
                    . '</code>',
                'attributes'     => array(
                    'style' => 'width: 400px;',
                ),
                'default'        => AmazonAutoLinks_Registry::NAME,
            ),            
            array(
                'field_id'       => 'preview_post_type_slug',
                'title'          => __( 'Post Type Slug', 'amazon-auto-links' ),
                'tip'            => __( 'Up to 20 characters with small-case alpha numeric characters.', 'amazon-auto-links' ),
                'description'    => __( 'Default', 'amazon-auto-links' )
                    . ': <code>'
                        . AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
                    . '</code>',
                'type'           => 'text',
                'default'        => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
            ),
            array(
                'type'           => 'checkbox',
                'field_id'       => 'visible_to_guests',
                'title'          => __( 'Visibility', 'amazon-auto-links' ),                
                'label'          => __( 'Visible to non-logged-in users.', 'amazon-auto-links' ),
                'default'        => true,
            ),
            array(
                'type'           => 'checkbox',
                'field_id'       => 'searchable',
                'title'          => __( 'Searchable', 'amazon-auto-links' ),                
                'label'          => __( 'Possible for the WordPress search form to find the plugin preview pages.', 'amazon-auto-links' ),
                'default'        => false,
            )  
        );
    
    }
        
    
    /**
     * Validates the submitted form data.
     * 
     * @since       3
     */
    public function validate( $aInputs, $aOldInputs, $oAdminPage, $aSubmitInfo ) {
    
        $_bVerified = true;
        $_aErrors   = array();
        
        // Sanitize the custom preview slug.
        $aInputs[ 'preview_post_type_slug' ] = AmazonAutoLinks_Utility::sanitizeCharsForURLQueryKey(
            AmazonAutoLinks_Utility::getTrancatedString(
                $aInputs[ 'preview_post_type_slug' ],
                20, // character length
                ''  // suffix
            )   
        );
        
        // If a custom post type slug is set, the rewrite rules need to be refreshed.
        $this->_flushRewriteRulesIfNecessary( $aInputs, $aOldInputs );

        // An invalid value is found. Set a field error array and an admin notice and return the old values.
        if ( ! $_bVerified ) {
            $oAdminPage->setFieldErrors( $_aErrors );     
            $oAdminPage->setSettingNotice( __( 'There was something wrong with your input.', 'amazon-auto-links' ) );
            return $aOldInputs;
        }
                
        return $aInputs;     
        
    }
        
        /**
         * Refreshes the site rewrite rules if a custom post type slug is set.
         * @since       3.2.4
         * @return      void
         */
        private function _flushRewriteRulesIfNecessary( $aInputs, $aOldInputs ) {

            $_oUtil = new AmazonAutoLinks_PluginUtility;
            
            $_sNew = trim( $_oUtil->getElement( $aInputs, 'preview_post_type_slug' ) );
            $_sOld = trim( $_oUtil->getElement( $aOldInputs, 'preview_post_type_slug' ) );
            
            // If no change, do nothing
            if ( $_sNew === $_sOld ) {
                return;
            }

            // If the user set an empty value, the default will be used so do nothing
            if ( ! $_sNew ) {
                return;
            }
                        
            // Set an option and instantiate the custom post type to force register the custom preview post type
            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_oOption->set( 
                array( 'unit_preview', 'preview_post_type_slug' ),
                $_sNew
            );
            
            // Force post type registration.
            $_oPostType = new AmazonAutoLinks_PostType_UnitPreview;
            $_oPostType->_replyToRegisterCustomPreviewPostType( $_sNew );
            
            // Flush rewrite rules.
            flush_rewrite_rules( true );
            
        }
   
}