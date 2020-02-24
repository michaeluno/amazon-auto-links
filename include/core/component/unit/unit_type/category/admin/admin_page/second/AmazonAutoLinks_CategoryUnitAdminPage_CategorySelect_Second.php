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
 * Adds the 'Select Categories' tab to the 'Add Unit by Category' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_Second extends AmazonAutoLinks_AdminPage_Tab_Base {
    
    /**
     * Triggered when the tab is loaded.
     * @callback        action      load_{$sPageSlug}_{$this->sTabSlug}
     */
    public function replyToLoadTab( $oFactory ) {
                
        // Create a dummy form to trigger a validation callback.
        $oFactory->addSettingFields(
            '_default', // the target section id    
            array(
                'field_id'      => '_dummy_field_for_validation',
                'hidden'        => true,
                'type'          => 'hidden',
                'value'         => $GLOBALS[ 'aal_transient_id' ],
                'attributes'    => array(
                    'name'  => 'transient_id',
                ),
            )
        );
        
        // Load the Preview template CSS file.
        $oFactory->enqueueStyle( AmazonAutoLinks_Registry::getPluginURL( 'template/preview/style-preview.css' ) );
        
    }
    
    /**
     * 
     * @callback        action      do_{$this->sPageSlug}_{$this->sTabSlug} 
     */
    public function replyToDoTab( $oFactory ) {

        /**
         * Renders a custom form for category selection.
         * 
         * The object handles the form validations and redirection.
         * So this needs to be done in the load_{...} callback.
         * Then in the do_{...} callback, the form will be rendered.        
         */
        $_oCategorySelectForm = new AmazonAutoLinks_Form_CategorySelect(
            $oFactory->getSavedOptions(),     // unit options   // $oFactory->oProp->aOptions,     // unit options            
            $oFactory->getSavedOptions()      // form options   // $oFactory->oProp->aOptions      // form options
        );            
        $_oCategorySelectForm->render();

        // Debug
        $this->___printDebugInformation( $oFactory );
        
    }
        /**
         * Debug information
         * @since       3
         * @since       3.5.0       Renamed from `_printDebugInfo()`.
         * @return      void
         */
        private function ___printDebugInformation( $oFactory ) {
                    
            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isDebug() ) {
                return;
            }                
            echo "<h3>"     
                    . __( 'Form Options', 'amazon-auto-links' ) 
                . "</h3>";
            $oFactory->oDebug->dump(
                $oFactory->getValue()
            );
            
        }
    
    /**
     * 
     * @callback        filter      validation_{page slug}_{tab slug}
     */
    public function validate( $aInput, $aOldInput, $oFactory, $aSubmitInfo ) {

        $_bVerified = ! $oFactory->hasFieldError();
        
        // Disable the setting notice in the next page load.
        $oFactory->setSettingNotice( '' );        
               
        // If the user presses one of the custom form submit buttons, 
        if ( isset( $_POST[ 'amazon_auto_links_cat_select' ] ) ) {
            return $this->___getCategorySelectFormInput(
                $_POST[ 'amazon_auto_links_cat_select' ],
                $oFactory->getSavedOptions(),    // $aOldInput,
                $oFactory
            );
        }   
        
        // $aInput just contains dummy items so do not use it.
        return $aOldInput;
        
    }    
    
        /**
         * Called when the user submits the form of the category select page.
         * @return      array
         */
        private function ___getCategorySelectFormInput( array $aPost, $aInput, $oFactory ) {

            // If the 'Save' or 'Create' button is pressed, save the data to the post.
            // The key is set in `AmazonAutoLinks_Form_CategorySelect` when rendering the form.
            if ( isset( $aPost[ 'save' ] ) ) {
                $_oUnitOption = new AmazonAutoLinks_UnitOption_category(
                    isset( $_GET[ 'post' ] )
                        ? $_GET[ 'post' ]
                        : null,
                    $aInput // unit options
                );
                $_iPostID = $this->___postUnitByCategory(
                    $_oUnitOption->get(), // sanitized
                    $aInput // not sanitized, contains keys not needed for the unit options
                );
                if ( $_iPostID ) {
                    $oFactory->setSettingNotice( 
                        __( 'A unit has been created.', 'amazon-auto=links' ),
                        'updated'
                    );
                }
                
                $_oUtil = new AmazonAutoLinks_PluginUtility;
                
                // Clean temporary options.
                $_oUtil->deleteTransient(
                    $GLOBALS[ 'aal_transient_id' ]
                );
                
                // Schedule pre-fetch.
                AmazonAutoLinks_Event_Scheduler::prefetch( $_iPostID );
                
                // Will exit the script.
                $_oUtil->goToPostDefinitionPage(
                    $_iPostID,
                    AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ]
                );
            }  
                                
            // Otherwise, update the form data.
            return $this->___getUpdatedUnitOptions(
                $aPost,
                $aInput,
                $oFactory
            );
            
        }
 
        /**
         * Creates a post of amazon_auto_links custom post type with unit option meta fields.
         * 
         * @return      integer     the post(unit) id.
         */
        private function ___postUnitByCategory( $aUnitOptions, $aOptions ) {
            
            $_iPostID = 0;
            
            // Create a custom post if it's a new unit.
            if ( ! isset( $_GET['post'] ) || ! $_GET['post'] ) {
                $_iPostID = wp_insert_post(
                    array(
                        'comment_status'    => 'closed',
                        'ping_status'       => 'closed',
                        'post_author'       => $GLOBALS[ 'user_ID' ],
                        'post_title'        => $aOptions[ 'unit_title' ],
                        'post_status'       => 'publish',
                        'post_type'         => AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ],
                    )
                );        
            }
            
            // Add meta fields.
            $_iPostID = 1 == $aOptions[ 'mode' ]
                ? $_iPostID 
                : $_GET[ 'post' ];
            
            // Remove unnecessary items.
            // The unit title was converted to post_title above.
            unset( 
                $aUnitOptions[ 'unit_title' ],
                $aUnitOptions[ 'is_preview' ] 
            );

            $_oOption = AmazonAutoLinks_Option::getInstance();
            $_oTemplateOption   = AmazonAutoLinks_TemplateOption::getInstance();
            $aUnitOptions[ 'template_id' ] = $_oTemplateOption->getDefaultTemplateIDByUnitType( 
                'category'
            );
            $aUnitOptions[ '_error' ] = 'normal';   // 3.7.9
            AmazonAutoLinks_WPUtility::updatePostMeta( $_iPostID, $aUnitOptions );

            // Create an auto insert - the 'auto_insert' key will be removed when creating a post.s
            if ( 
                isset( $aOptions[ 'auto_insert' ] ) 
                && $aOptions[ 'auto_insert' ] 
                && 1 == $aOptions[ 'mode' ]  // new
            ) {
                AmazonAutoLinks_PluginUtility::createAutoInsert( 
                    $_iPostID 
                );
            }    
            
            return $_iPostID;
            
        }            
        
    
   
    /**
     * Processes the user submitted form data in the Category Select page.
     * 
     * @return      array      The (un)updated data.
     */
    private function ___getUpdatedUnitOptions( $aPost, array $aInput, $oFactory ) {

        $_iNumberOfCategories = count( $aInput[ 'categories' ] )  
            + count( $aInput[ 'categories_exclude' ] );
        
        // Check the limit
        if ( 
            ( isset( $aPost[ 'add' ] ) || isset( $aPost[ 'exclude' ] ) )
            && $this->___isNumberOfCategoryReachedLimit( $_iNumberOfCategories )
        ) {
            $oFactory->setSettingNotice(
                $this->___getLimitNotice( true )
            );
            return $aInput;
        }

        /**
         * Structure of the category array
         * 
         * md5( $aCurrentCategory[ 'page_url' ] ) => array(
         *         'breadcrumb' => 'US > Books',
         *         'feed_url'   => 'http://amazon....',    // the feed url of the category
         *         'page_url'   => 'http://...'        // the page url of the category
         * 
         * );
         *
         * @since   unknown
         * @since   3.9.4       The `feed_url` element is deprecated as of 3.9.0. So use the `page_url` element instead.
         */
        $_oEncrypt              = new AmazonAutoLinks_Encrypt;        
        $aCategories            = $aInput[ 'categories' ];
        $aExcludingCategories   = $aInput[ 'categories_exclude' ];
        $aCurrentCategory       = $aPost[ 'category' ];
        $aCurrentCategory[ 'breadcrumb' ] = $_oEncrypt->decode( $aCurrentCategory[ 'breadcrumb' ] );
                        
        // Check if the "Add Category" button is pressed
        if ( isset( $aPost[ 'add' ] ) ) {
            $aCategories[ md5( $aCurrentCategory[ 'page_url' ] ) ] =  $aCurrentCategory;
        }
        
        if ( isset( $aPost[ 'exclude' ] ) ) {
            $aExcludingCategories[ md5( $aCurrentCategory[ 'page_url' ] ) ] = $aCurrentCategory;
        }
        
        // Check if the "Remove Checked" button is pressed
        if ( isset( $aPost[ 'remove' ], $aPost[ 'checkboxes' ] ) ) {
            foreach( $aPost[ 'checkboxes' ] as $_sKey => $_sName ) {
                unset( $aCategories[ $_sName ] );
                unset( $aExcludingCategories[ $_sName ] );
            }
        }
                    
        $aInput[ 'categories' ]         = $aCategories;
        $aInput[ 'categories_exclude' ] = $aExcludingCategories;
        return $aInput;
        
    }
        /**
         * Checks whether the category item limit is reached.
         * @return      boolean
         */
        private function ___isNumberOfCategoryReachedLimit( $iNumberOfCategories ) {
            $_oOption = AmazonAutoLinks_Option::getInstance();
            return ( boolean ) $_oOption->isReachedCategoryLimit( 
                $iNumberOfCategories
            );            
        }           
    
        /**
         * Returns the admin message.
         * @return      string
         */
        private function ___getLimitNotice( $bIsReachedLimit, $bEnableHTMLTag=true ) {
            if ( ! $bIsReachedLimit ) {
                return '';
            }
            return $bEnableHTMLTag 
                ? sprintf( __( 'Please upgrade to <a href="%1$s" target="_black">Pro</a> to add more categories.', 'amazon-auto-links' ), AmazonAutoLinks_Registry::STORE_URI_PRO )
                : __( 'Please upgrade to Pro to add more categories!', 'amazon-auto-links' );
        }         

    
}