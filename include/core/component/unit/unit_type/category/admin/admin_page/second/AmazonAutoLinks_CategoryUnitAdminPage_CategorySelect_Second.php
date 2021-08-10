<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */

/**
 * Adds the 'Select Categories' tab to the 'Add Unit by Category' page of the loader plugin.
 * 
 * @since       3
 * @extends     AmazonAutoLinks_AdminPage_Tab_Base
 */
class AmazonAutoLinks_CategoryUnitAdminPage_CategorySelect_Second extends AmazonAutoLinks_AdminPage_Tab_Base {

    /**
     * @remark  This also serves as the variable name on the JavaScript side.
     * @var string
     */
    private $___sAjaxScriptHandle1 = 'aalCategorySelection';

    protected function _construct( $oFactory ) {}

    /**
     * @return array
     * @since   4.2.0
     */
    protected function _getArguments() {
        return array(
            'tab_slug'      => 'second',
            'title'         => __( 'Add Unit by Category', 'amazon-auto-links' ),
            'description'   => __( 'Select categories.', 'amazon-auto-links' ),
            'style'         => array(
                AmazonAutoLinks_Registry::getPluginURL( 'template/_common/style.css' ),
                AmazonAutoLinks_Registry::getPluginURL( 'template/preview/style-preview.css' ), // the Preview template CSS file.
                AmazonAutoLinks_UnitTypeLoader_category::$sDirPath . '/asset/css/category_selection.css',  // 4.2.0
            ),
        );
    }

    /**
     * Triggered when the tab is loaded.
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
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

        // If doing Ajax do nothing
        if ( apply_filters( 'wp_doing_ajax', defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            return;
        }

        // For unknown reasons, `wp_enqueue_scripts` does not work.
        add_action( 'admin_head', array( $this, 'replyToEnqueueScripts' ) );

    }

        public function replyToEnqueueScripts() {

            // Get the user's set locale
            $_aUnitOptions      = $this->___getUnitOptions();
            $_sLocale           = $this->getElement( $_aUnitOptions, array( 'country' ), 'US' );
            $_oLocale           = new AmazonAutoLinks_Locale( $_sLocale );
            $_sRootCategoryURL  = $_oLocale->getBestSellersURL();

            // Ajax script
            wp_enqueue_script( 'jquery' );
            wp_enqueue_script(
                $this->___sAjaxScriptHandle1,    // handle
                $this->getSRCFromPath( AmazonAutoLinks_UnitTypeLoader_category::$sDirPath . '/asset/js/category-selection.js' ), // 4.2.0
                array( 'jquery' ),
                false,
                true
            );
            wp_localize_script(
                $this->___sAjaxScriptHandle1,
                $this->___sAjaxScriptHandle1,        // variable name on JavaScript side
                $this->___getDebugInformation( $_sLocale, $_aUnitOptions )
                + array(
                    'ajaxURL'                           => admin_url( 'admin-ajax.php' ),
                    'nonce'                             => wp_create_nonce( 'aalNonceCategorySelection' ),
                    'action_hook_suffix_category_list'  => 'aal_category_selection', // WordPress action hook name which follows after `wp_ajax_`
                    'action_hook_suffix_unit_preview'   => 'aal_unit_preview',
                    'spinnerURL'                        => admin_url( 'images/loading.gif' ),
                    'transientID'                       => $GLOBALS[ 'aal_transient_id' ],
                    'postID'                            => absint( $this->getElement( $_GET, array( 'post' ), 0 ) ), // for editing category selection, a post id is passed. // sanitization done
                    'maxNumberOfCategories'             => ( integer ) AmazonAutoLinks_Option::getInstance()->getMaximumNumberOfCategories(),
                    'rootURL'                           => $_sRootCategoryURL,
                    'translation'                       => array(
                        'category_not_selected' => __( 'Please select a category.', 'amazon-auto-links' ),
                        'too_many_categories'   => __( 'Please be aware that adding too many categories slows down the performance.', 'amazon-auto-links' ),
                        'already_added'         => __( 'The category is already added.', 'amazon-auto-links' ),
                    ),
                )
            );

        }

        /**
         * Returns an array holding debug information.
         * @param string $sLocale
         * @param array $aUnitOptions
         *
         * @return array
         * @since   4.2.2
         */
        private function ___getDebugInformation( $sLocale, array $aUnitOptions ) {
            $_bPluginDebugMode  = AmazonAutoLinks_Option::getInstance()->isDebug() || $this->isDebugMode();
            $_aDebugInformation = array(
                'debugMode' => $_bPluginDebugMode,
            );
            if ( ! $_bPluginDebugMode ) {
                return $_aDebugInformation;
            }
            $_mTransient = get_transient( $GLOBALS[ 'aal_transient_id' ] );
            return $_aDebugInformation + array(
                'debug' => array(
                    'locale'            => $sLocale,
                    'localeRaw'         => $this->getElement( $aUnitOptions, array( 'country' ), '' ), // raw - no default value passed
                    'countUnitOptions'  => count( $aUnitOptions ),
                    'callerURL'         => $this->getCurrentURL(),
                    'referrerURL'       => $this->getElement( $_SERVER, array( 'HTTP_REFERER' ) ),
                    'versionWP'         => $this->getElement( $GLOBALS, array( 'wp_version' ) ),
                    'versionAAL'        => AmazonAutoLinks_Registry::VERSION,
                    'activePlugins'     => $this->getAsArray( get_option( 'active_plugins' ) ),
                    'activeTheme'       => $this->___getThemeInfo(),
                    'hasTransient'      => ! empty( $_mTransient ),
                ),
            );
        }
            /**
             * @return string
             * @since 4.2.2
             */
            private function ___getThemeInfo() {

                $_oTheme = wp_get_theme( );
                if ( ! $_oTheme->exists() ) {
                    return 'The current theme could not be detected.';
                }
                return esc_html( $_oTheme->get( 'Name' ) . ' ' . $_oTheme->get( 'Version' ) );
                        
            }

        /**
         * There are two cases:
         *  a) Creating a new unit
         *  b) Editing the category selection of an already created unit
         * For the second case, GET[ 'post' ] is set.
         * @return array
         */
        private function ___getUnitOptions() {

            if ( ! isset( $_GET[ 'post' ] ) ) { // sanitization unnecessary as just checking
                $_aUnitOptions = $this->oFactory->getSavedOptions();
                if ( empty( $_aUnitOptions ) ) {
                    new AmazonAutoLinks_Error( 'CATEGORY_SELECTION_AJAX_CALL', 'The unit options are empty. Transient: ' . $GLOBALS[ 'aal_transient_id' ], $_aUnitOptions, true );
                }
                return $_aUnitOptions;
            }

            $_oUnitOption = new AmazonAutoLinks_UnitOption_category(
                ( integer ) $_GET[ 'post' ], // unit id // sanitization done
                array() // unit options
            );
            return $_oUnitOption->get();

        }

    /**
     *
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @callback        action      do_{$this->sPageSlug}_{$this->sTabSlug} 
     */
    public function replyToDoTab( $oFactory ) {

        $_aData = $oFactory->getSavedOptions();

        /**
         * Renders a custom form for category selection.
         * 
         * The object handles the form validations and redirection.
         * So this needs to be done in the load_{...} callback.
         * Then in the do_{...} callback, the form will be rendered.        
         */
        $_oCategorySelectForm = new AmazonAutoLinks_Form_CategorySelect(
            $_aData,     // unit options   // $oFactory->oProp->aOptions,     // unit options
            $_aData      // form options   // $oFactory->oProp->aOptions      // form options
        );            
        $_oCategorySelectForm->render();

        // Debug
        $this->___printDebugInformation( $oFactory );

    }
        /**
         * Debug information
         * @since       3
         * @since       3.5.0       Renamed from `_printDebugInfo()`.
         * @since       4.2.1  deprecated duplicated data.
         * @return      void
         * @param AmazonAutoLinks_AdminPageFramework $oFactory
         */
        private function ___printDebugInformation( $oFactory ) {

            $_oOption = AmazonAutoLinks_Option::getInstance();
            if ( ! $_oOption->isDebug() && ! $_oOption->isDebugMode() ) {
                return;
            }
            echo "<div class='aal-accordion'>";
            echo "<h4>" . 'Transients' . "</h4>";
            echo "<div>";
                $oFactory->oDebug->dump( array( 'key' => $GLOBALS[ 'aal_transient_id' ] ) );
                $oFactory->oDebug->dump( $this->getAsArray( get_transient( $GLOBALS[ 'aal_transient_id' ] ) ) );
            echo "</div>";
            echo "</div>";

        }

    /**
     *
     * @callback        filter      validation_{page slug}_{tab slug}
     *
     * @param array $aInputs
     * @param array $aOldInputs
     * @param AmazonAutoLinks_AdminPageFramework $oFactory
     * @param $aSubmitInfo
     *
     * @return array
     */
    public function validate( $aInputs, $aOldInputs, $oFactory, $aSubmitInfo ) {
        
        // Disable the setting notice in the next page load.
        $oFactory->setSettingNotice( '' );        

        if ( ! isset( $_POST[ 'amazon_auto_links_cat_select' ] ) ) {
            return $aOldInputs;    
        }
        
        return $this->___getCategorySelectFormInput(
            $this->getAsArray( $_POST ),
            $oFactory->getSavedOptions(),    // $aOldInput,
            $oFactory
        );
        
    }    
    
        /**
         * Called when the user submits the form of the category select page.
         * @return      array
         */
        private function ___getCategorySelectFormInput( array $aPost, array $aInputs, $oFactory ) {

            /**
             * Structure of the category array
             *
             * ```
             * md5( $aCurrentCategory[ 'page_url' ] ) => array(
             *         'breadcrumb' => 'US > Books',
             *         'page_url'   => 'http://...'        // the page url of the category
             * );
             * ```
             */
            $_aAdded    = $this->getElementAsArray( $aPost, array( 'added' ) );
            $_aExcluded = $this->getElementAsArray( $aPost, array( 'excluded' ) );
            $aInputs[ 'categories' ]         = $this->getCategoryArgumentsFormatted( $_aAdded );
            $aInputs[ 'categories_exclude' ] = $this->getCategoryArgumentsFormatted( $_aExcluded );

            $_oUnitOption = new AmazonAutoLinks_UnitOption_category(
                ( integer ) $this->getElement( $_GET, array( 'post' ), 0 ), // unit id // sanitization done
                $aInputs // unit options
            );
            $_iPostID = $this->___postUnitByCategory(
                $_oUnitOption->get(), // sanitized
                $aInputs // not sanitized, contains keys not needed for the unit options
            );
            // @deprecated 4.2.0 Even if the user returns to the category selection page right after creating a category unit, this message pops up and is not necessary.
//            if ( $_iPostID ) {
//                $oFactory->setSettingNotice(
//                    __( 'A unit has been created.', 'amazon-auto=links' ),
//                    'updated'
//                );
//            }

            // Clean temporary options.
            $this->deleteTransient( $GLOBALS[ 'aal_transient_id' ] );

            // Schedule pre-fetch.
            AmazonAutoLinks_Event_Scheduler::prefetch( $_iPostID );

            // Will exit the script.
            $this->goToPostDefinitionPage( $_iPostID, AmazonAutoLinks_Registry::$aPostTypes[ 'unit' ] );

            // Dummy return
            return array();
        }
            /**
             * @param array $aCategories
             *
             * @return array
             */
            private function getCategoryArgumentsFormatted( array $aCategories ) {
                $_oEncrypt              = new AmazonAutoLinks_Encrypt;
                $_aFormatted = array();
                foreach( $aCategories as $_sMD5 => $_aCategory ) {
                    $_aFormatted[ $_sMD5 ] = array(
                        'breadcrumb' => $_oEncrypt->decode( $_aCategory[ 'breadcrumb' ] ),
                        'page_url'   => $_oEncrypt->decode( $_aCategory[ 'page_url' ] ),                  );
                }
                return $_aFormatted;
            }
 
        /**
         * Creates a post of amazon_auto_links custom post type with unit option meta fields.
         * 
         * @return      integer     the post(unit) id.
         */
        private function ___postUnitByCategory( $aUnitOptions, $aOptions ) {
            
            $_iPostID = 0;
            
            // Create a custom post if it's a new unit.
            if ( ! isset( $_GET[ 'post' ] ) || ! $_GET[ 'post' ] ) {    // sanitization unnecessary as just checking
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
                : absint( $_GET[ 'post' ] );    // sanitization done
            
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
     * @deprecated  4.2.0
     */
    private function ___getUpdatedUnitOptions( $aPost, array $aInput, $oFactory ) {

        $_iNumberOfCategories = count( $aInput[ 'categories' ] )  
            + count( $aInput[ 'categories_exclude' ] );

        // @deprecated 4.2.0 No longer checked here.
        // Check the limit
        /*if (
            ( isset( $aPost[ 'add' ] ) || isset( $aPost[ 'exclude' ] ) )
            && $this->___isNumberOfCategoryReachedLimit( $_iNumberOfCategories )
        ) {
            $oFactory->setSettingNotice(
                $this->___getLimitNotice( true )
            );
            return $aInput;
        }*/

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
         * @deprecated  4.2.0
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
         * @deprecated 4.2.0
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