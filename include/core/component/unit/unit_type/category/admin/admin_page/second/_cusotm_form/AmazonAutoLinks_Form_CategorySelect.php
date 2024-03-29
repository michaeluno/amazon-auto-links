<?php
/**
 * Auto Amazon Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2022 Michael Uno
 */

/**
 * Provides methods to render the HTML elements of the category selection page in the plugin admin area.
 * 
 * This class should be instantiated before the header gets sent since it checks the form and if the Save/Create button is pressed,
 * it needs to redirect the page to another page. In that case, if the header is already sent, an error occurs.
 * 
 */
class AmazonAutoLinks_Form_CategorySelect extends AmazonAutoLinks_Form_CategorySelect__Base {

    /**
     * Stores the form options.
     * 
     * Identical to the options given by the below $oUnitOption object 
     * except this contains keys not defined in the unit default key structure.
     */
    public $aOptions;
    
    /**
     * Stores category unit options. 
     * 
     * Keys that are not defined in the default key structure will be stripped out.
     */
    public $oUnitOption;

    /**
     * User constructor.
     */
    public function construct( /* array $aUnitOptions=array(), array $aFormOptions=array() */ ) {
        
        $_aParams           = func_get_args() + array( 0 => array(), 1 => array() );
        // $_aUnitOptions      = $_aParams[ 0 ]; // @deprecated unused
        $_aFormOptions      = $_aParams[ 1 ];
        $this->aOptions     = array(
            'template_path' => AmazonAutoLinks_Registry::$sDirPath . '/template/preview/template.php',
            'is_preview'    => true, // this disables the global ASIN blacklist.
        ) + $_aFormOptions;
        $this->oUnitOption  = new AmazonAutoLinks_UnitOption_category(
            null,   // post id
            $this->aOptions
        );
    }
    
    /**
     * Renders the form fields for the category selection preview page.
     * 
     */
    public function render() {
        
        $sPageURL = $this->___getPageURL(
            isset( $_GET[ 'href' ] )    // sanitization unnecessary as just checking
                ? $_GET[ 'href' ]                   // sanitization done by passing ___getPageURL()
            : '', 
            $this->oUnitOption->get( 'country' )
        );

        $this->_printPreviewTable(
            array(
                'bNew'                          => isset( $this->aOptions[ 'mode' ] ) 
                    ? $this->aOptions[ 'mode' ]
                    : 0, // 0 : edit, 1 : new
                'sPageURL'                      => '', // $_oSidebar->get( 'PageURL' ),
                'sRSSURL'                       => '', // $_oSidebar->get( 'RSSURL' ),
                'aSelectedRSSURLs'              => '', //$this->___getSelectedRSSURLs( $this->oUnitOption->get( 'categories' ) ), // @deprecated 3.8.1
                'aSelectedPageURLs'             => wp_list_pluck( $this->oUnitOption->get( 'categories' ), 'page_url' ),
                'sBounceURL'                    => $this->aOptions[ 'bounce_url' ],
                'aWorkingURLs'                  => array(),
                'sBreadcrumb'                   => '<span class="now-loading-breadcrumb">'
                    . $this->___getNowLoading()
                    . '</span>',
                'sSidebarHTML'                  => '<span class="now-loading-category-list">'
                    . $this->___getNowLoading()
                    . '</span>',
                'sSelectedCategories'           => $this->___getSelectedCategoryList(
                    $this->oUnitOption->get( 'categories' ),
                    'added'
                ),
                'sSelectedExcludingCategories'  => $this->___getSelectedCategoryList(
                    $this->oUnitOption->get( 'categories_exclude' ),
                    'excluded'
                ),
                'sSelectedPreview'              => '<p>' 
                        . __( 'Selected preview comes here.', 'amzon-auto-links' )
                    . '</p>',
                'sStoredPreview'                => '<p>'
                        . __( 'Stored preview comes here.', 'amazon-auto-links' )
                    . '</p>',
            )
        );
        
    }

        /**
         * A helper function for the above renderForm() method that retrieves the feed urls of added categories.
         *
         * @since       unknown
         * @since       3.5.7       Changed the scope to private as this is only used in this class.
         * @deprecated  As bestseller feeds are deprecated this is no longer needed.
         */
        // private function ___getSelectedRSSURLs( $aCategories ) {
        //
        //     $aURLs = array();
        //     foreach( $aCategories as $aCategory ) {
        //         $aURLs[] = $aCategory[ 'feed_url' ];
        //     }
        //     return $aURLs;
        //
        // }
        /**
         * 
         * $aCategory = array(
         *    'breadcrumb' => ... 
         *  'feed_url' => ..
         *  'page_url' => ...
         * );
         * @since       unknown
         * @since       3.5.7       Changed the scope to private as this is only used in this class.
         * @since       4.2.0       Changed the output HTML structure for the new design.
         * @param       array   $aCategories
         * @param       string  $sContext       `added` or `excluded`
         * @return      string
         */
        private function ___getSelectedCategoryList( $aCategories, $sContext ) {

            $_sNoCategories = __( 'No categories added.', ' amazon-auto-links' );

            if ( $this->isEmpty( $aCategories ) ) {
                return "<p class='no-categories-added'>" . $_sNoCategories . "</p>";
            }

            // If the user returns from the unit editing page, the values are set.
            $_aOutput = array(
                "<p class='no-categories-added hidden'>" . $_sNoCategories . "</p>"

            );
            foreach( $aCategories as $sKey => $aCategory ) {
                $_sClassName = 'added' === $sContext
                    ? 'added-category'
                    : 'excluding-category';
                $_aOutput[] = "<p class='{$_sClassName}'>"
                        . AmazonAutoLinks_Unit_Utility_category::getCategoryCheckbox( $aCategory[ 'page_url' ], $aCategory[ 'breadcrumb' ], $sContext )
                    . "</p>";
                
            }
            return implode( PHP_EOL, $_aOutput );

        }
        
   
    /**
     * 
     * @uses        flush()
     */
    protected function _printPreviewTable( $aPageElements ) {

        // Instantiate the core object - the fetching process should be done while rendering the HTML output
        // because it takes some time so the flush() function is used in the middle.
        /// Edit the excluding category unit option for preview
        $_aPreviewUnitOptions = $this->oUnitOption->get();
        $_aPreviewUnitOptions[ 'categories_exclude' ] = array();

        // Buttons 
        // $bReachedLimit      = $this->_isNumberOfCategoryReachedLimit(
        //     count( $this->oUnitOption->get( 'categories' ) )
        //     + count( $this->oUnitOption->get( 'categories_exclude' ) )
        // );
        $bIsAlreadyAdded    = $this->___isAddedCategory(
            $aPageElements[ 'sBreadcrumb' ],
            $this->oUnitOption->get( 'categories' )
        );
        $bIsAlreadyAddedExcludingCategory = $this->___isAddedCategory(
            $aPageElements[ 'sBreadcrumb' ],
            $this->oUnitOption->get( 'categories_exclude' )
        );
        $bIsSubCategoryOfAddedItems = $this->___isSubCategoryOfAddedItems(
            $aPageElements[ 'sBreadcrumb' ],
            $this->oUnitOption->get( 'categories' )
        );

        $sAddDisabled = "disabled='disabled'";
        $sExcludeDisabled   = $this->isEmpty( $this->oUnitOption->get( 'categories' ) ) || $bIsAlreadyAdded || $bIsAlreadyAddedExcludingCategory || ! $bIsSubCategoryOfAddedItems
            ? "disabled='disabled'"
            : "";
        $sRemoveDisabled    = $this->isEmpty( $this->oUnitOption->get( 'categories' ) )
            ? "disabled='disabled'"
            : "";
        $sCreateDisabled    = $this->isEmpty( $this->oUnitOption->get( 'categories' ) )
            ? "disabled='disabled'"
            : "";
        $sCreateOrSave      = $aPageElements[ 'bNew' ]
            ? __( 'Create', 'amazon-auto-links' )
            : __( 'Save', 'amazon-auto-links' );

        // Arrows
        /// @deprecated 4.2.0
        $_sAddArrowURL      = $this->getSRCFromPath( AmazonAutoLinks_Unit_UnitType_Loader_category::$sDirPath . '/asset/image/arrow_right.png' );
        $sAddArrow          = "<img id='arrow-add' class='hidden category-select-right-arrow' title='" . __( 'Add the current selection!', 'amazon-auto-links' ) . "' src='" . esc_url( $_sAddArrowURL ) . "'/>";
        $_sCreateArrowURL   = $this->getSRCFromPath( AmazonAutoLinks_Unit_UnitType_Loader_category::$sDirPath . '/asset/image/arrow_right.png' );
        $sCreateArrow       = "<img id='arrow-create' class='hidden category-select-right-arrow' title='" . esc_attr( __( 'Create the unit!', 'amazon-auto-links' ) ) . "' src='" . esc_url( $_sCreateArrowURL ) . "'/>";
        $_sSelectArrowURL   = $this->getSRCFromPath( AmazonAutoLinks_Unit_UnitType_Loader_category::$sDirPath . '/asset/image/arrow_left_bottom.png' );
        $sSelectArrow       = "<img id='arrow-select' class='hidden category-select-left-bottom-arrow' title='" . esc_attr( __( 'Select a category from the links!', 'amazon-auto-links' ) ) . "' src='" . esc_url( $_sSelectArrowURL ) . "'/>";

        $_oEncrypt          = new AmazonAutoLinks_Encrypt;
        $_oOption           = AmazonAutoLinks_Option::getInstance();
        $_aUsingHTMLTags    = array( 'input' => array(
            'type'  => true,
            'name'  => true,
            'id'    => true,
            'style' => true,
            'value' => true,
        ) ) + $_oOption->getAllowedHTMLTags();
        ?>

<input type="hidden" name="amazon_auto_links_cat_select[category][breadcrumb]" value="<?php echo $_oEncrypt->encode( $aPageElements[ 'sBreadcrumb' ] ); ?>" />
<input type="hidden" name="amazon_auto_links_cat_select[category][feed_url]" value="<?php echo esc_url( $aPageElements[ 'sRSSURL' ] ); ?>" />
<input type="hidden" name="amazon_auto_links_cat_select[category][page_url]" value="<?php echo esc_url( $aPageElements[ 'sPageURL' ] ); ?>" />
<table class="category-select-table">
    <tbody>
        <tr>
            <td class="category-select-first-column">
                <h3><?php esc_html_e( 'Current Selection', 'amazon-auto-links' ); ?></h3>
                <p id="category-select-breadcrumb"><?php echo wp_kses( $aPageElements[ 'sBreadcrumb' ], $_aUsingHTMLTags ); ?></p>
            </td>
            <td class="category-select-second-column" colspan="2">
                <div class="category-select-submit-buttons">
                    <span class="primary"><a class="button button-primary" href="<?php echo esc_url( $aPageElements[ 'sBounceURL' ] ); ?>"><?php esc_html_e( 'Go Back', 'amazon-auto-links' ); ?></a></span>
                    <span class="primary"><?php echo wp_kses( $sCreateArrow, $_aUsingHTMLTags ); ?><input id="button-save-unit" type="submit" name="amazon_auto_links_cat_select[save]" class="button button-primary" value="<?php echo esc_attr( $sCreateOrSave ); ?>" <?php echo wp_kses( $sCreateDisabled, $_aUsingHTMLTags ); ?> /></span>
                    <span><?php echo wp_kses( $sAddArrow, $_aUsingHTMLTags ); ?><input id="button-add-category" type="submit" name="amazon_auto_links_cat_select[add]" class="button button-secondary" value="<?php esc_html_e( 'Add Category', 'amazon-auto-links' ); ?>" <?php echo wp_kses( $sAddDisabled, $_aUsingHTMLTags ); ?> /></span>
                    <span><input id="button-add-excluding-category" type="submit" name="amazon_auto_links_cat_select[exclude]" class="button button-secondary" value="<?php esc_html_e( 'Add Excluding Category', 'amazon-auto-links' ); ?>" <?php echo wp_kses( $sExcludeDisabled, $_aUsingHTMLTags ); ?> /></span>
                    <span><input id="button-remove-checked" type="submit" name="amazon_auto_links_cat_select[remove]" class="button button-secondary" value="<?php esc_html_e( 'Remove Checked', 'amazon-auto-links' ); ?>" <?php echo wp_kses( $sRemoveDisabled, $_aUsingHTMLTags ); ?> /></span>
                </div>
                <div id="selected-categories">
                    <h3><?php esc_html_e( 'Added Categories', 'amazon-auto-links' ); ?></h3>
                    <div id="added-categories">
                        <?php echo wp_kses( $aPageElements[ 'sSelectedCategories' ], $_aUsingHTMLTags ); ?>
                    </div>
                    <h3><?php esc_html_e( 'Added Excluding Sub-categories', 'amazon-auto-links' ); ?></h3>
                    <div id="excluding-categories">
                        <?php echo wp_kses( $aPageElements[ 'sSelectedExcludingCategories' ], $_aUsingHTMLTags ); ?>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td class="category-select-first-column">
                <h3 id="category-select-title"><?php esc_html_e( 'Select Category', 'amazon-auto-links' ); ?></h3>
                <div id="category-list">
                <?php echo wp_kses( $sSelectArrow, $_aUsingHTMLTags ); ?>
                <?php echo ( ( boolean ) $aPageElements[ 'sSidebarHTML' ] )
                    ? wp_kses( $aPageElements[ 'sSidebarHTML' ], $_aUsingHTMLTags )
                    : "<p>" . esc_html__( 'Failed to generate the category list.', 'amazon-auto-links' ) . "</p>";
                ?>
                </div>
            </td>
            <td class="category-select-second-column category-select-preview-left">
                <h3>
                    <?php echo esc_html__( 'Preview of This Category', 'amazon-auto-links' ); ?>
                </h3>
                <div class="widthfixer" style="width:<?php echo esc_attr( $this->oUnitOption->get( 'image_size' ) ); ?>px;"></div>

                <?php
                /**
                 * @since   3.8.1   Changed the value to give to the below `render()` method from a RSS URL to a page URL as feeds are deprecated.
                 */
                echo '<div id="category-preview">'
                        . '<p class="now-loading-category-preview">'
                            . wp_kses( $this->___getNowLoading(), $_aUsingHTMLTags )
                        . '</p>'
                     . '</div>';
                ?>
            </td>
            <td class="category-select-third-column category-select-preview-right">
                <h3 id="unit-preview-title"><?php esc_html_e( 'Unit Preview', 'amazon-auto-links' ); ?></h3>
                <div class="widthfixer" style="width:<?php echo esc_attr( $this->oUnitOption->get( 'image_size' ) ); ?>px;"></div>
                <?php
                echo '<div id="unit-preview">'
                        . '<p class="now-loading-unit-preview">'
                            . wp_kses( $this->___getNowLoading(), $_aUsingHTMLTags )
                        . '</p>'
                    . '</div>';
                ?>
            </td>
        </tr>
    </tbody>
</table>    
            <?php
            
        }

            /**
             * @return string
             * @since   4.2.0
             */
            private function ___getNowLoading() {
                $_sMessage = __( 'Now loading...', 'amazon-auto-links' );
                return '<span class="now-loading" >'
                        . '<img src="' . esc_url( admin_url( 'images/loading.gif' ) ) . '" alt="' . esc_attr( $_sMessage ) . '" />'
                        . $_sMessage
                    . '</span>';
            }
    
        /**
         * Determines whether the current browsing category is already added or not.
         * @since       unknown
         * @since       3.5.7       Changed the scope to private as this is only used in this class.
         */
        private function ___isAddedCategory( $sBreadCrumb, $aCategories ) {
            
            foreach( $aCategories as $aCategory ) {
                if ( trim( $aCategory['breadcrumb'] ) == trim( $sBreadCrumb ) ) {
                    return true;
                }
            }
            return false;
            
        }
        /**
         * Determines whether the current browsing category is a sub-category of added ones.
         * @since       unknown
         * @since       3.5.7       Changed the scope to private as this is only used in this class.
         * @return      boolean
         */
        private function ___isSubCategoryOfAddedItems( $sBreadCrumb, $aCategories ) {
            $_sBreadCrumb = trim( $sBreadCrumb );
            foreach( $aCategories as $_aCategory ) {
                $_sCategoryBreadCrumb = trim( $_aCategory[ 'breadcrumb' ] );
                if ( $_sCategoryBreadCrumb === $_sBreadCrumb ) {
                    continue;
                }
                if ( false !== strpos( $_sBreadCrumb, $_sCategoryBreadCrumb ) ) {
                    return true;
                }
            }
            return false;
        }
   
        /**
         * Returns the decrypted URL.
         *
         * @since           2.0.0
         * @since           3.5.7       Changed the scope to private as it is only used in this class.
         */
        private function ___getPageURL( $sEncryptedURL, $sLocale='US' ) {

            $_oLocale  = new AmazonAutoLinks_Locale( $sLocale );
            $_oEncrypt = new AmazonAutoLinks_Encrypt;
            $_sURL     = $sEncryptedURL
                ? $_oEncrypt->decode( $sEncryptedURL  )
                : $_oLocale->getBestSellersURL();

            // @since 3.8.1 Sometimes part of url gets double slashed like https://www.amazon.xxx//gp/top-sellers/office-products/
            $_sURL = str_replace("//gp/","/gp/", $_sURL );

            // Add a trailing slash; this is tricky, the uk and ca sites have an issue that they display a not-found(404) page when the trailing slash is missing.
            // e.g. http://www.amazon.ca/Bestsellers-generic/zgbs won't open but http://www.amazon.ca/Bestsellers-generic/zgbs/ does.
            // Note that this problem has started occurring after using wp_remote_get(). So it has something to do with the function.
            return trailingslashit( $_sURL );

        }

}
        