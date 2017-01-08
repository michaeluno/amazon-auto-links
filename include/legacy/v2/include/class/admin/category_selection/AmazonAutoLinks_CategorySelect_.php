<?php
/**
 * Provides methods to render the HTML elements of the category selection page in the plugin admin area.
 * 
 * This class should be instantiated before the header gets sent since it checks the form and if the Save/Create button is pressed,
 * it needs to redirect the page to another page. In that case, if the header is already sent, an error occurs.
 * 
 */
abstract class AmazonAutoLinks_CategorySelect_ {

    function __construct( $arrUnitOptions ) {
        
        $this->strCharEncoding = get_bloginfo( 'charset' ); 
        $this->oEncrypt = new AmazonAutoLinks_Encrypt();
        $this->oDOM = new AmazonAutoLinks_DOM();
        
        $this->arrUnitOptions = ( array ) $arrUnitOptions;
        $this->arrUnitOptions = $this->handleSumittedData( $this->arrUnitOptions );
        $this->arrUnitOptions = $this->setUnitOptionsForEdit( $this->arrUnitOptions );
// AmazonAutoLinks_Debug::logArray( $this->arrUnitOptions );
    }
    
    protected function setUnitOptionsForEdit( $arrUnitOptions ) {
        
        // If the user comes from the eidt page, the post query key is set.
        if ( ! isset( $_GET['post'] ) ) return $arrUnitOptions;
        
        $arrPostData = array();
        
        // Stored Data
        // $arrMeta = get_post_meta( $_GET['post'], '', true ); // this creates 0 indexes and unserialized values
        if ( $_GET['post'] ) {    // there is a case that the post id is 0 or not set like ...&post&post_type=
            foreach( get_post_custom_keys( $_GET['post'] ) as $strKey ) 
                $arrPostData[ $strKey ] = get_post_meta( $_GET['post'], $strKey, true );        // this way, array will be unserialized
        }
        $arrPostData['mode'] = 0;
        $arrPostData['bounce_url'] = add_query_arg( 
            array( 
                'post_type' => AmazonAutoLinks_Commons::PostTypeSlug,
                'post' => $_GET['post'],
                'action' => 'edit',
            ) 
            ,admin_url( 'post.php' ) 
        );

// AmazonAutoLinks_Debug::logArray( $arrPostData, dirname( __FILE__ ) . '/meta.txt' );        
        return $arrUnitOptions + $arrPostData + AmazonAutoLinks_Unit_Category::$arrStructure_Args;
        
    }
    
    protected function handleSumittedData( $arrUnitOptions ) {

        // Arrived from the Proceed or Select Category(from the edit page) button
        if ( ! isset( $_POST['amazon_auto_links_cat_select'] ) )
            return $arrUnitOptions;
        
        // Check nonce.
        if ( ! wp_verify_nonce( $_POST['nonce'],  AmazonAutoLinks_Commons::AdminOptionKey ) ) 
            die( "<div class='error'><p>" . __( 'A problem occurred.', 'amazon-auto-links' ) . '</p></div>' );
        
        $arrPost = $_POST['amazon_auto_links_cat_select'];
        
        // If the Save or Create button is pressed, save the data to the post.
        if ( isset( $arrPost['save'] ) ) {
            
            $this->postUnitByCategory( $arrUnitOptions );        
            
            wp_redirect( 
                $arrUnitOptions['mode'] == 0 && isset( $_GET['post'] ) && $_GET['post']
                ? $arrUnitOptions['bounce_url']
                : admin_url( "edit.php?post_type=" . AmazonAutoLinks_Commons::PostTypeSlug ) 
            );
            exit;
            
        }    

        // Check the limit
        if ( ( isset( $arrPost['add'] ) || isset( $arrPost['exclude'] ) ) && $this->isReachedLimit( $arrUnitOptions ) ) {
            if ( headers_sent() )
                $this->showLimitNotice();
            else
                add_action( 'admin_notices', array( $this, 'showLimitNotice' ) );
            return $arrUnitOptions;
        }

        
        $arrCategories = $arrUnitOptions['categories'];
        /*
         * structure of the category array
         * 
         * md5( $arrCurrentCategory['feed_url'] ) => array(
         *         'breadcrumb' => 'US > Books',
         *         'feed_url' => 'http://amazon....',    // the feed url of the category
         *         'page_url' => 'http://...'        // the page url of the category
         * 
         * );
         */
        $arrExcludingCategories = $arrUnitOptions['categories_exclude'];
        $arrCurrentCategory = $_POST['amazon_auto_links_cat_select']['category'];
        $arrCurrentCategory['breadcrumb'] = $this->oEncrypt->decode( $arrCurrentCategory['breadcrumb'] );
                        
        // Check if the "Add Category" button is pressed
        if ( isset( $arrPost['add'] ) ) 
            $arrCategories[ md5( $arrCurrentCategory['feed_url'] ) ] =  $arrCurrentCategory;
        
        if ( isset( $arrPost['exclude'] ) )
            $arrExcludingCategories[ md5( $arrCurrentCategory['feed_url'] ) ] = $arrCurrentCategory;
        
        // Check if the "Remove Checked" button is pressed
        if ( isset( $arrPost['remove'] ) ) 
            foreach( $arrPost['checkboxes'] as $strKey => $strName ) {
                unset( $arrCategories[ $strName ] );
                unset( $arrExcludingCategories[ $strName ] );
            }
                    
        $arrUnitOptions['categories'] = $arrCategories;
        $arrUnitOptions['categories_exclude'] = $arrExcludingCategories;
        return $arrUnitOptions;
        
    }

    
    /**
     * Renders the form fields for the category selection preview page.
     * 
     */
    public function renderForm() {
        
        // Set up variables.
        $strPageURL = $this->getPageURL( isset( $_GET['href'] ) ? $_GET['href'] : '', $this->arrUnitOptions['country'] );
        $arrSidebar = $this->getSidebar( $strPageURL, $this->arrUnitOptions['country'] );
                            
        $this->printPreviewTable(
            array(
                'fNew' => $this->arrUnitOptions['mode'],        // 0 : edit, 1 : new
                'strPageURL' => $strPageURL,
                'strRSSURL' => $arrSidebar['strRSSURL'],
                'arrSelectedRSSURLs' => $this->getSelectedRSSURLs( $this->arrUnitOptions['categories'] ),
                'strBounceURL' => isset( $this->arrUnitOptions['bounce_url'] ) ? $this->arrUnitOptions['bounce_url'] : "",
                'arrWorkingURLs' => array(),
                'strBreadcrumb' => $arrSidebar['strRSSURL'] ? $arrSidebar['strBreadcrumb'] : __( 'None', 'amazon-auto-links' ),
                'strSidebarHTML' => $arrSidebar['strCategoryList'] ? $arrSidebar['strCategoryList'] : $arrSidebar['error'],
                'strSelectedCategories' => $this->composeSelectedCategoriesList( $this->arrUnitOptions['categories'] ),
                'strSelectedExcludingCategories' => $this->composeSelectedCategoriesList( $this->arrUnitOptions['categories_exclude'] ),
                'strSelectedPreview' => '<p>Selected preview goes here</p>',
                'strStoredPreview' => '<p>Stored preview goes here</p>',
            ),
            $this->arrUnitOptions
        );
        return $this->arrUnitOptions;
        
    }

    /**
     * A helper function for the above renderForm() method that retrieves the feed urls of added categories.
     * 
     */
    protected function getSelectedRSSURLs( $arrCategories ) {
            
        $arrURLs = array();
        foreach( $arrCategories as $arrCategory ) 
            $arrURLs[] = $arrCategory['feed_url'];
        return $arrURLs;
        
    }
    
    /**
     * 
     * $arrCategory = array(
     *    'breadcrumb' => ... 
     *  'feed_url' => ..
     *  'page_url' => ...
     * );
     */
    protected function composeSelectedCategoriesList( $arrCategories ) {
        
        if ( empty( $arrCategories ) )
            return "<p>" . __( 'No categories added.', ' amazon-auto-links' ) . "</p>"; 
            
        $arrOutput = array();
        foreach( $arrCategories as $strKey => $arrCategory ) {
            
            $strName = md5( $arrCategory['feed_url'] );
            $strPageURL = $this->formatLinkURL( $arrCategory['page_url'] );
            $arrOutput[] = "<div class='category-select-selected-category'>" 
                    . "<label for='cb-{$strName}'>"
                        . "<input type='checkbox' name='amazon_auto_links_cat_select[checkboxes][{$strKey}]' value='{$strName}' id='cb-{$strName}' />"
                        . "<a href='{$strPageURL}'>{$arrCategory['breadcrumb']}</a>"
                    . "</label>"
                . "</div>";
            
        }
        
        return implode( PHP_EOL, $arrOutput );
    }
        
    /**
     * Creates a post of amazon_auto_links custom post type with unit option meta fields.
     * 
     */
    protected function postUnitByCategory( $arrUnitOptions ) {
        
        // Create a custom post if it's a new unit.
        if ( ! isset( $_GET['post'] ) || ! $_GET['post'] )
            $intPostID = wp_insert_post(
                array(
                    'comment_status'    =>    'closed',
                    'ping_status'        =>    'closed',
                    'post_author'        =>    $GLOBALS['user_ID'],
                    // 'post_name'            =>    $slug,
                    'post_title'        =>    $arrUnitOptions['unit_title'],
                    'post_status'        =>    'publish',
                    'post_type'            =>    AmazonAutoLinks_Commons::PostTypeSlug,
                    // 'post_content'         => null,
                    // 'post_date' => date('Y-m-d H:i:s'),
                    // 'post_author' => $user_ID,
                )
            );        
        
        // Add meta fields.
        $intPostID = $arrUnitOptions['mode'] == 1 ? $intPostID : $_GET['post'];
        if ( $arrUnitOptions['mode'] != 1 )    // if not New
            unset( $arrUnitOptions['auto_insert'] );
        unset( $arrUnitOptions['unit_title'] );
        
        foreach( $arrUnitOptions as $strFieldID => $vValue ) 
            update_post_meta( $intPostID, $strFieldID, $vValue );
            
        // Create an auto insert - the 'auto_insert' key will be removed when creating a post.s
        if ( isset( $arrUnitOptions['auto_insert'] ) && $arrUnitOptions['auto_insert'] && $arrUnitOptions['mode'] == 1 ) {
            
            $arrAutoInsertOptions = array( 
                    'unit_ids' => array( $intPostID ) 
                ) + AmazonAutoLinks_Form_AutoInsert::$arrStructure_AutoInsertOptions;
            
            AmazonAutoLinks_Option::insertPost( $arrAutoInsertOptions, AmazonAutoLinks_Commons::PostTypeSlugAutoInsert );
            
        }    

    }    
    
    protected function printPreviewTable( $arrComponents, $arrArgs ) {
            
        // Instantiate the core object - the fetching process should be done while rendering the HTML output
        // because it takes some time so the flush() function is used in the middle.
        $arrArgs['template_path'] = AmazonAutoLinks_Commons::$strPluginDirPath . '/template/preview/template.php';
        $arrArgs['is_preview'] = true;    // this disables the global ASIN blacklist.
        $oAALCatPreview = new AmazonAutoLinks_Unit_Category( $arrArgs );
        $oAALUnitPreview = new AmazonAutoLinks_Unit_Category( $arrArgs );
        echo "<!-- page url:{$arrComponents['strPageURL']}-->";
        echo "<!-- preview url:{$arrComponents['strRSSURL']}-->";

        // Buttons 
        $fReachedLimit = $this->isReachedLimit( $arrArgs );
        $fIsAlreadyAdded = $this->isAddedCategory( $arrComponents['strBreadcrumb'], $arrArgs['categories'] );
        $fIsAlreadyAddedExcludingCategory = $this->isAddedCategory( $arrComponents['strBreadcrumb'], $arrArgs['categories_exclude'] );
        $fIsSubCategoryOfAddedItems = $this->isSubCategoryOfAddedItems( $arrComponents['strBreadcrumb'], $arrArgs['categories'] );
        $strAddDisabled = empty( $arrComponents['strRSSURL'] ) || $fIsAlreadyAdded || $fIsAlreadyAddedExcludingCategory ? "disabled='Disabled'" : "";
        $strExcludeDisabled = empty( $arrArgs['categories'] ) || $fIsAlreadyAdded || $fIsAlreadyAddedExcludingCategory || ! $fIsSubCategoryOfAddedItems ? "disabled='Disabled'" : "";
        $strRemoveDisabled = empty( $arrArgs['categories'] ) ? "disabled='Disabled'" : "";
        $strCreateDisabled = empty( $arrArgs['categories'] ) ? "disabled='Disabled'" : "";
        $strCreateOrSave = $arrComponents['fNew'] ? __( 'Create', 'amazon-auto-links' ) : __( 'Save', 'amazon-auto-links' );
        
        // Arrows
        $strAddArrow = $arrComponents['fNew'] && ! empty( $arrComponents['strRSSURL'] ) && empty( $arrArgs['categories'] ) ? "<img class='category-select-right-arrow' title='" . __( 'Add the current selection!', 'amazon-auto-links' ) . "' src='" . AmazonAutoLinks_Commons::getPluginURL( 'asset/image/arrow_right.png' ) . "'/>" : "";
        $strCreateArrow = $arrComponents['fNew'] && ! empty( $arrComponents['strRSSURL'] ) && ! empty( $arrArgs['categories'] ) ? "<img class='category-select-right-arrow' title='" . __( 'Create the unit!', 'amazon-auto-links' ) . "' src='" . AmazonAutoLinks_Commons::getPluginURL( 'asset/image/arrow_right.png' ) . "'/>" : "";
        $strSelectArrow = $arrComponents['fNew'] && empty( $arrComponents['strRSSURL'] ) && empty( $arrArgs['categories'] ) ? "<img class='category-select-left-bottom-arrow' title='" . __( 'Select a category from the links!', 'amazon-auto-links' ) . "' src='" . AmazonAutoLinks_Commons::getPluginURL( 'asset/image/arrow_left_bottom.png' ) . "'/>" : "";
                
        ?>
        <form action="" method="post">        
            <?php if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( AmazonAutoLinks_Commons::AdminOptionKey, 'nonce' ); }  ?>
            <input type="hidden" name="amazon_auto_links_cat_select[category][breadcrumb]" value="<?php echo $this->oEncrypt->encode( $arrComponents['strBreadcrumb'] ) ;?>" />
            <input type="hidden" name="amazon_auto_links_cat_select[category][feed_url]" value="<?php echo $arrComponents['strRSSURL'] ;?>" />
            <input type="hidden" name="amazon_auto_links_cat_select[category][page_url]" value="<?php echo $arrComponents['strPageURL'] ;?>" />            
            <table class="category-select-table">
                <tbody>
                    <tr>
                        <td class="category-select-first-column">                
                            <h3><?php _e( 'Current Selection', 'amazon-auto-links' ); ?></h3>
                            <p class="category-select-breadcrumb"><?php echo $arrComponents['strBreadcrumb']; ?></p>
                        </td>
                        <td class="category-select-second-column" colspan="2">        
                            <div class="category-select-submit-buttons">
                                <span><a class="button button-primary" href="<?php echo $arrComponents['strBounceURL']; ?>"><?php _e( 'Go Back', 'amazon-auto-links' ); ?></a></span>
                                <span><?php echo $strCreateArrow; ?><input type="submit" name="amazon_auto_links_cat_select[save]" class="button button-primary" value="<?php echo $strCreateOrSave; ?>" <?php echo $strCreateDisabled; ?> /></span>
                                <span><?php echo $strAddArrow; ?><input type="submit" name="amazon_auto_links_cat_select[add]" class="button button-secondary" value="<?php _e( 'Add Category', 'amazon-auto-links' ); ?>" <?php echo $strAddDisabled; ?> /></span>
                                <span><input type="submit" name="amazon_auto_links_cat_select[exclude]" class="button button-secondary" value="<?php _e( 'Add Excluding Category', 'amazon-auto-links' ); ?>" <?php echo $strExcludeDisabled; ?> /></span>                                
                                <span><input type="submit" name="amazon_auto_links_cat_select[remove]" class="button button-secondary" value="<?php _e( 'Remove Checked', 'amazon-auto-links' ); ?>" <?php echo $strRemoveDisabled; ?> /></span>
                            </div>
                            <div>
                                <h3><?php _e( 'Added Categories', 'amazon-auto-links' ); ?></h3>
                                <?php echo $arrComponents['strSelectedCategories']; ?>
                                <h3><?php _e( 'Added Excluding Sub-categories', 'amazon-auto-links' ); ?></h3>
                                <?php echo $arrComponents['strSelectedExcludingCategories']; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td class="category-select-first-column">
                            <h3><?php _e( 'Select Category', 'amazon-auto-links' ); ?></h3>
                            <?php echo $strSelectArrow; ?>
                            <?php echo $arrComponents['strSidebarHTML']; ?>
                        </td>
                        <td class="category-select-second-column category-select-preview-left">
                            <h3>
                                <?php echo $arrComponents['strRSSURL'] ? __( 'Preview of This Category', 'amazon-auto-links' ) : __( 'No Preview', 'amazon-auto-links' ); ?>
                            </h3>                        
                            <div class="widthfixer" style="width:<?php echo $arrArgs['image_size']; ?>px;"></div>
                            <?php 
                            if ( $arrComponents['strRSSURL'] ) {
                                $oAALCatPreview->render( array( $arrComponents['strRSSURL'] ) );
                                // flush();
                            } else 
                                _e( 'Please select a category from the list on the left.', 'amazon-auto-links' );  
                            ?>
                        </td>
                        <td class="category-select-third-column category-select-preview-right">
                            <h3><?php _e( 'Unit Preview', 'amazon-auto-links' ); ?></h3>                            
                            <div class="widthfixer" style="width:<?php echo $arrArgs['image_size']; ?>px;"></div>
                            <?php                         
                            if ( ! empty( $arrComponents['arrSelectedRSSURLs'] ) ) { 
                                $oAALUnitPreview->render( $arrComponents['arrSelectedRSSURLs'] );
                                flush(); 
                            } else 
                                _e( 'Please add a category from the list after selecting it.', 'amazon-auto-links' );
                            ?>
                        </td>
                    </tr>
                </tbody>
            </table>    
        </form>
        <?php
    }
        /**
         * Determines whether the current browsing category is already added or not.
         */
        protected function isAddedCategory( $strBreadCrumb, $arrCategories ) {
            
            foreach( $arrCategories as $arrCategory ) 
                if ( trim( $arrCategory['breadcrumb'] ) == trim( $strBreadCrumb ) )
                    return true;
            
        }
        /**
         * Determines whether the current browsing category is a sub-category of added ones.
         */
        protected function isSubCategoryOfAddedItems( $strBreadCrumb, $arrCategories ) {
            
            foreach( $arrCategories as $arrCategory ) 
                if ( 
                    ( strpos( trim( $strBreadCrumb ), trim( $arrCategory['breadcrumb'] ) ) !== false )
                    && ( trim( $arrCategory['breadcrumb'] ) != trim( $strBreadCrumb ) )
                )
                    return true;
            
        }
            
        /**
         * Checks whether the category item limit is reached.
         * 
         */
        protected function isReachedLimit( $arrArgs ) {
            
            $intNumberOfCategories = count( $arrArgs['categories'] ) + count( $arrArgs['categories_exclude'] );
            return $GLOBALS['oAmazonAutoLinks_Option']->isReachedCategoryLimit( $intNumberOfCategories )
                ? true
                : false;
                
            // if ( count( $arrArgs['categories'] ) + count( $arrArgs['categories_exclude'] ) >= 3 )
                // return true;
            // return false;
            
        }
        
        /**
         * Shows the admin message.
         * 
         */
        protected function getLimitNotice( $fIsReachedLimit, $fEnableHTMLTag=true ) {
            
            if ( ! $fIsReachedLimit ) return '';
            
            return $fEnableHTMLTag 
                ? sprintf( __( 'Please upgrade to <a href="%1$s" target="_black">Pro</a> to add more categories.', 'amazon-auto-links' ), 'http://en.michaeluno.jp/amazon-auto-links-pro/' )
                : __( 'Please upgrade to Pro to add more categories!', 'amazon-auto-links' );
            
        }
        public function showLimitNotice() {
                        
            echo "<div class='error'><p>" . $this->getLimitNotice( true ) . "</p></div>";
            
        }        
        
    /**
     * Returns the decrypted URL.
     * 
     * @since            2.0.0
     */
    protected function getPageURL( $strEncryptedURL, $strLocale='US' ) {
                
        $strURL = $strEncryptedURL 
            ? $this->oEncrypt->decode( $strEncryptedURL  )
            : ( isset( AmazonAutoLinks_Properties::$arrCategoryRootURLs[ $strLocale ] ) 
                ? AmazonAutoLinks_Properties::$arrCategoryRootURLs[ $strLocale ] 
                : AmazonAutoLinks_Properties::$arrCategoryRootURLs[ 'US' ] 
            );
            
        // Add a trailing slash; this is tricky, the uk and ca sites have an issue that they display a not-found(404) page when the trailing slash is missing.
        // e.g. http://www.amazon.ca/Bestsellers-generic/zgbs won't open but http://www.amazon.ca/Bestsellers-generic/zgbs/ does.
        // Note that this problem has started occurring after using wp_remote_get(). So it has something to do with the function.             
        $strURL    = trailingslashit( $strURL ); 
        
        return $strURL;
        
    }
    
    /**
     * Represents the sidebar array.
     * 
     */
    protected static $arrStructure_Sidebar = array(
        'strRSSURL' => null,
        'strCategoryList' => null,
        'strBreadcrumb' => null,
        'error'    => null,
    );    
    /**
     * Retrieves the sidebar category list.
     * 
     * @since            2.0.0
     * @remark            Due to missing elements with the DOMDocument class in some Japanese pages, this method uses the simple_html_dom library.
     */
    protected function getSidebar( $strPageURL, $strLocale='US', $intAttempt=0 ) {
        
        // Include the library.
        if ( ! class_exists( 'simple_html_dom_node' ) )
            include_once( AmazonAutoLinks_Commons::$strPluginDirPath . '/include/library/simple_html_dom.php' );
        
        // This has a caching functionality.
        $strHTML = $this->oDOM->getHTML( $strPageURL );
        if ( ! $strHTML ) {
            // AmazonAutoLinks_Debug::logArray( $strHTML );
            return array( 'error' => __( "Could not load the page: {$strPageURL}. Please consult the plugin developer.", 'amazon-auto-links' ) ) + self::$arrStructure_Sidebar;
        }
        $strHTML = $this->convertCharacters( $strHTML, $this->getMBLanguage( $strLocale ) );
                
        // Instantiate the class.
        $oSimpleDOM = str_get_html( $strHTML );
        if ( ! $oSimpleDOM->getElementById( 'zg_browseRoot' ) ) {
            
            // Delete the cache and try it again.
            $this->oDOM->deleteCache( $strPageURL );
    
            // Try with a R18 confirmation redirect - must use file_get_contents(), not wp_remote()
            if ( $intAttempt >= 2 ) {
                
                $strRedirectURL = AmazonAutoLinks_Properties::$arrCategoryBlackCurtainURLs[ $strLocale ] . '?redirect=true&redirectUrl=' . urlencode( $strPageURL );
                $strHTML = $this->oDOM->getHTML( $strRedirectURL, true );    // the second parameter force it to use file_get_contents().
                if ( ! $strHTML ) {
                    // AmazonAutoLinks_Debug::logArray( $strHTML );
                    return array( 'error' => __( "Could not load the page: {$strRedirectURL}. Please consult the plugin developer.", 'amazon-auto-links' ) ) + self::$arrStructure_Sidebar;
                }
                $strHTML = $this->convertCharacters( $strHTML, $this->getMBLanguage( $strLocale ) );
                
                $oSimpleDOM = str_get_html( $strHTML );
                if ( ! $oSimpleDOM->getElementById( 'zg_browseRoot' ) ) {
                    $strHTML = $oSimpleDOM->outertext;
                    // $strEncoded = htmlspecialchars( $strHTML, ENT_COMPAT, get_bloginfo( 'charset' ) );
                    // AmazonAutoLinks_Debug::logArray( $strHTML, dirname( __FILE__ ) . '/unable_to_fetch_category_' . md5( $strPageURL ) . '.txt' );
                    $this->oDOM->deleteCache( $strRedirectURL );
                    return array( 
                        'error' => sprintf( 
                            __( 'Could not find the category in this page: %1$s Please consult the plugin developer.', 'amazon-auto-links' ),
                            $strRedirectURL 
                        )                         
                    ) + self::$arrStructure_Sidebar;
                }
                return array(
                    'strRSSURL' => $this->getCategoryFeedURL( $oSimpleDOM ),    
                    'strCategoryList' => $this->getCategoryList( $oSimpleDOM ), // must be done after the getCategoryFeedURL() method as this method modifies the links.
                    'strBreadcrumb' => $this->getBreadcrumb( $oSimpleDOM, $strLocale ),
                );        
                
            }
            return $this->getSidebar( $strPageURL, $strLocale, ++$intAttempt );
    
        }
        
        return array(
            'strRSSURL' => $this->getCategoryFeedURL( $oSimpleDOM ),    
            'strCategoryList' => $this->getCategoryList( $oSimpleDOM ), 
            'strBreadcrumb' => $this->getBreadcrumb( $oSimpleDOM, $strLocale ),
        );    
        
    }
    
    protected function _getSidebar( $strPageURL, $strLocale='US', $intAttempt=0 ) {

        $arrStructure_Sidebar = self::$arrStructure_Sidebar;
    
        // Create a DOM document object        
        $oDOM = $this->oDOM->loadDOMFromURL( $strPageURL, $this->getMBLanguage( $strLocale ) );
        if ( ! $oDOM ) {
            $strHTML = $oDOM->saveXML( $oDOM->getElementsByTagName('body')->item(0) );
            // $strEncoded = htmlspecialchars( $strHTML, ENT_COMPAT, get_bloginfo( 'charset' ) );
            // AmazonAutoLinks_Debug::logArray( $strHTML );
            return array( 'error' => __( 'Could not load categories. Please consult the plugin developer.', 'amazon-auto-links' ) ) + $arrStructure_Sidebar;
        }
            
        // Check if the category element exists.
        $oXpath = new DOMXPath( $oDOM );     // since getElementByID constantly returned false for unknown reasons, use DOMXPath
        $nodeBrowseRoot = $oXpath->query( "//*[@id='zg_browseRoot']" );
        if ( ! $nodeBrowseRoot->length ) {
            
            // Delete the cache and try it again.
            $this->oDOM->deleteCache( $strPageURL );
            
            // Try sanitizing the document.
            if ( $intAttempt == 2 ) {
                    
                // Try with file_get_contents()
                $strHTML = file_get_contents( $strPageURL );
                $strHTML = trim( preg_replace( '/^<!DOCTYPE.+?>/', '', trim( $strHTML ) ) );
                // $strHTML = preg_replace( "/&(?!amp;)/", "&amp;", $strHTML );
                
                $strHTML= preg_replace_callback('/&(\w+);/', array( $this, 'only_html_entity_decode' ), $strHTML );
                
                $strHTML = balanceTags( $strHTML );
                // $strHTML = force_balance_tags( $strHTML );
                // $strHTML = htmlentities( $strHTML );
                $oDOM = $this->oDOM->loadDOMFromHTML( $strHTML, $this->getMBLanguage( $strLocale ), true );    
// AmazonAutoLinks_Debug::logArray( $strHTML, dirname( __FILE__ ) . '/amazon_page.txt' );                
                $oXpath = new DOMXPath( $oDOM );
                $nodeBrowseRoot = $oXpath->query( "//*[@id='zg_browseRoot']" );
                if ( ! $nodeBrowseRoot->length ) 
                    return $this->getSidebar( $strPageURL, $strLocale, ++$intAttempt );
                return array(
                    'strRSSURL' => $this->getCategoryFeedURL( $oDOM ),    
                    'strCategoryList' => $this->getCategoryList( $oDOM, $nodeBrowseRoot ), // must be done after the getCategoryFeedURL() method as this method modifies the links.
                    'strBreadcrumb' => $this->getBreadcrumb( $oDOM ),
                );    
                
            }
            // Try with a R18 confirmation redirect - must use file_get_contents(), not wp_remote()
            else if ( $intAttempt >= 3 ) {
                
                $strRedirectURL = AmazonAutoLinks_Properties::$arrCategoryBlackCurtainURLs[ $strLocale ] . '?redirect=true&redirectUrl=' . urlencode( $strPageURL );
                $oDOM = $this->oDOM->loadDOMFromURL( $strRedirectURL, $this->getMBLanguage( $strLocale ), true );    // the third parameter tell it to use file_get_contents()
                $oXpath = new DOMXPath( $oDOM );
                $nodeBrowseRoot = $oXpath->query( "//*[@id='zg_browseRoot']" );
                if ( ! $nodeBrowseRoot->length ) {
                    $strHTML = $oDOM->saveXML( $oDOM->getElementsByTagName('body')->item(0) );
                    // $strEncoded = htmlspecialchars( $strHTML, ENT_COMPAT, get_bloginfo( 'charset' ) );
                    // AmazonAutoLinks_Debug::logArray( $strHTML );
                    return array( 
                        'error' => sprintf( 
                            __( 'Could not load the page: %1$s Please consult the plugin developer.', 'amazon-auto-links' ),
                            $strPageURL 
                        )                         
                    ) + $arrStructure_Sidebar;
                }
                return array(
                    'strRSSURL' => $this->getCategoryFeedURL( $oDOM ),    
                    'strCategoryList' => $this->getCategoryList( $oDOM, $nodeBrowseRoot ), // must be done after the getCategoryFeedURL() method as this method modifies the links.
                    'strBreadcrumb' => $this->getBreadcrumb( $oDOM ),
                );        
                
            }
            return $this->getSidebar( $strPageURL, $strLocale, ++$intAttempt );    
        }    

// AmazonAutoLinks_Debug::logArray( $oDOM->saveXML() );
        return array(
            'strRSSURL' => $this->getCategoryFeedURL( $oDOM ),    
            'strCategoryList' => $this->getCategoryList( $oDOM, $nodeBrowseRoot ), // must be done after the getCategoryFeedURL() method as this method modifies the links.
            'strBreadcrumb' => $this->getBreadcrumb( $oDOM ),
        );        
                
    }
    protected function only_html_entity_decode($match) {
        if ( in_array( $match[1], array( 'amp', 'lt', 'gt', 'quot', 'apos') ) )
            return $match[0];
        else
            return html_entity_decode( $match[0], ENT_COMPAT, $this->strCharEncoding );
    }
    /**
     * Returns the language code of the specified Amazon store locale.
     * 
     * Either ja, en, or uni is returned.
     * 
     */
    protected function getMBLanguage( $strLocale='US' ) {
        
        return isset( AmazonAutoLinks_Properties::$arrCategoryPageMBLanguages[ $strLocale ] ) 
                ? AmazonAutoLinks_Properties::$arrCategoryPageMBLanguages[ $strLocale ] 
                : 'uni';        
        
    }
    
    /**
     * Generates the HTML output of the node tree list.
     * 
     * @since            2.0.0
     */
    protected function getCategoryList( $oSimpleDOM ) {
        
        $nodeBrowseRoot = $oSimpleDOM->getElementById( 'zg_browseRoot' );
            
        $this->modifyHref( $nodeBrowseRoot );
        
        return $nodeBrowseRoot->outertext;    // the sidebar html code
        
    }    
    protected function _getCategoryList( $oDoc, $nodeBrowseRoot ) {
        
        $domleftCol = $nodeBrowseRoot->item( 0 );
    
        $this->modifyHref( $oDoc );
        
        // get the sidebar html code
        return $this->removeLineFeeds( $oDoc->saveXML( $domleftCol ) );        
        
    }
        
    private function removeLineFeeds( $strOutput ) {
                
        $strOutput = str_replace( array( "\r\n", "\r" ), "\n", $strOutput );
        
        $arrLines = explode( "\n", $strOutput );
        $arrNewLines = array();
        foreach( $arrLines as $i => $strLine ) 
            if( ! empty( $strLine ) )
                $arrNewLines[] = trim( $strLine, '\t\n\r\0\x0B' );
        
        return implode( $arrNewLines );
    
    }
    /**
     * Converts href urls into a url with query which contains the original url.
     * 
     * e.g. <a href="http://amazon.com/something"> -> <a href="localhost/me.php?href=http://amazon.com/something"
     * and the href value beceomes base64 encoded.
     */
    protected function modifyHref( $oSimpleDOMNode, $arrQueries=array() ) {
        
        foreach( $oSimpleDOMNode->getElementsByTagName( 'a' ) as $nodeA ) {
            
            $strHref = $nodeA->getAttribute( 'href' );
            
            // strip the string after 'ref=' in the url
            // e.g. http://amazon.com/ref=zg_bs_123/324-5242552 -> http://amazon.com
            $arrURL = explode( "ref=", $strHref, 2 );
            $strHref = $arrURL[0];            
            
            $nodeA->setAttribute( 'href', $this->formatLinkURL( $strHref, $arrQueries ) );
            
        }
        
    }
    protected function _modifyHref( $oDOM, $arrQueries=array() ) {    
            
        $arrQueries = ( array ) $arrQueries;
                
        $oXpath = new DOMXPath( $oDOM );     // since getElementByID constantly returned false for unknown reason, use xpath
        $domleftCol = $oXpath->query( "//*[@id='zg_browseRoot']" )->item( 0 );        // $domleftCol = $oDOM->getElementById('zg_browseRoot');
        if ( !$domleftCol ) {
            echo '<!-- ' . __( 'Categories not found. Please consult the plugin developer.', 'amazon-auto-links' ) . ' -->' . PHP_EOL;
            return false;
        }
        foreach( $oDOM->getElementsByTagName( 'a' ) as $nodeA ) {
            
            $strHref = $nodeA->getAttribute( 'href' );
            $nodeA->removeAttribute( 'href' );
            
            // strip the string after 'ref=' in the url
            // e.g. http://amazon.com/ref=zg_bs_123/324-5242552 -> http://amazon.com
            $arrURL = explode( "ref=", $strHref, 2 );
            $strHref = $arrURL[0];
            
            @$nodeA->setAttribute( 'href', $this->formatLinkURL( $strHref, $arrQueries ) );
            
        }    
        return true;
        
    }    
    
    /**
     * Gets the current self-url. needs to exclude the query part 
     * e.g. http://localhost/me.php?href=http://....  -> http://localhost/me.php
     */
    protected function formatLinkURL( $strURL, $arrQueries=array() ) {
        return add_query_arg( 
                array( 
                    'href' => $this->oEncrypt->encode( $strURL ),
                ) + $arrQueries + $_GET                
                , admin_url( $GLOBALS['pagenow'] ) 
            );
    }
    
    /**
     * Creates a breadcrumb of the Amazon page sidebar.
     * 
     * This is specific to Amazon's store page so if the site page structure changes, it won't work.
     * Especially it uses the unique id and class names including zg_browseRoot, zg_selected, the sidebar element IDs. 
     * 
     * @since            2.0.0
     */
    protected function getBreadcrumb( $oSimpleDOM, $strLocale='US' ) {
        
        $arrBreadcrumb = array();
        
        $nodeBrowseRoot = $oSimpleDOM->getElementById( 'zg_browseRoot' );
        $nodeSelected = $nodeBrowseRoot->find( '.zg_selected', 0 );
        // $nodeSelected = $oSimpleDOM->find( "//*[@id='zg_browseRoot']//*[@class='zg_selected']" );        
        if ( ! $nodeSelected ) {
            return __( 'Failed to generate the breadcrumb.', 'amazon-auto-links' );
        }
            
        // Current category
        $arrBreadcrumb[] = trim( $nodeSelected->plaintext );        
        
        // Climb up the node
        $nodeClimb = $nodeSelected->parentNode();
        Do {
            if ( $nodeClimb->nodeName() == 'ul' ) {
                $nodeUpperUl = $nodeClimb->parentNode();
                $nodeLi = $nodeUpperUl->getElementByTagName( 'li' );
                $nodeA = $nodeLi->getElementByTagName( 'a' );
                $arrBreadcrumb[] = trim( $nodeA->innertext );
            }
            $nodeClimb = $nodeClimb->parentNode();    
            
        } While ( $nodeClimb && $nodeClimb->getAttribute( 'id' ) != 'zg_browseRoot' );        
        
        array_pop( $arrBreadcrumb );    // remove the last element
        $arrBreadcrumb[] = strtoupper( $strLocale );    // set the last element to the country code
        $arrBreadcrumb = array_reverse( $arrBreadcrumb );
        return implode( " > ", $arrBreadcrumb );
        
    }
    
    protected function convertCharacters( $strHTML, $strMBLang='', $strEncoding='' ) {
    
        // without this, the characters get broken    
        if ( ! empty( $strMBLang ) ) { 
            mb_language( $strMBLang ); 
        }
                    
        $strEncoding = empty( $strEncoding ) ? @mb_detect_encoding( $strHTML, 'AUTO' ) : $strEncoding;
        $strHTML = @mb_convert_encoding( $strHTML, $this->strCharEncoding , $strEncoding );    
        return @mb_convert_encoding( $strHTML, 'HTML-ENTITIES', $this->strCharEncoding );         
        
    }
    
    protected function _getBreadcrumb( $oDOM, $strLocale='US' ) {
        
        $arrBreadcrumb = array();
        
        // Extract the current selecting category with xpath
        $oXpath = new DomXpath( $oDOM );
        $nodeZg_Selected = $oXpath->query( "//*[@id='zg_browseRoot']//*[@class='zg_selected']" ); 
        
        if ( ! $nodeZg_Selected->length ) {
            return __( 'Failed to generate the breadcrumb.', 'amazon-auto-links' );
        }
        
        $strCurrentCategory = trim( $nodeZg_Selected->item( 0 )->nodeValue );
        $arrBreadcrumb[] = $strCurrentCategory;
        
        // Climb up the node
        $nodeClimb = $nodeZg_Selected->item( 0 )->parentNode;        // this is the weird part that item() method is required. once the parent node is retrieved, it's no more needed.        
        Do {    
            if ( $nodeClimb->nodeName == 'ul' ) {
                $nodeUpperUl = $nodeClimb->parentNode;
                $strUpperCategory = $nodeUpperUl->getElementsByTagName( 'li' )->item( 0 )->nodeValue;
                array_push( $arrBreadcrumb, trim( preg_replace( '/^.+\s?/', '', $strUpperCategory ) ) );
            }
            $nodeClimb = $nodeClimb->parentNode;    
        } While ( $nodeClimb && $nodeClimb->getAttribute( 'id' ) != 'zg_browseRoot' );
        
        array_pop( $arrBreadcrumb );    // remove the last element
        $arrBreadcrumb[] = strtoupper( $strLocale );    // set the last element to the country code
        $arrBreadcrumb = array_reverse( $arrBreadcrumb );
        return implode( " > ", $arrBreadcrumb );
        
    }
    
    /**
     * Extracts the category feed url from the given DOM object.
     * 
     * @since            2.0.0
     */
    protected function getCategoryFeedURL( $oSimpleDOM ) {
        
        $domRSSLinks = $oSimpleDOM->getElementById( 'zg_rssLinks' );
        if ( ! $domRSSLinks ) {
            
            // the root category does not provide a rss link, so return silently
            echo '<!-- ' . __METHOD__ . ': The zg_rssLinks ID element could not be found. -->';
            return;
            
        }
        
        $nodeA2 = $domRSSLinks->getElementsByTagName( 'a', 1 );    // the second link.
        $strRSSLink = $nodeA2->getAttribute( 'href' );
        $arrURL = explode( "ref=", $strRSSLink, 2 );
        return $arrURL[0];
    
    }
    protected function _getCategoryFeedURL( $oDOM ) {
        
        $strRSSLink = '';
        $strIDRSS = 'zg_rssLinks';
        $domRSSLinks = $oDOM->getElementById( $strIDRSS );
        if ( ! $domRSSLinks ) {
            
            // the root category does not provide a rss link, so return silently
            echo '<!-- ' . __METHOD__ . ': ' . $strIDRSS . ' ID could not be found. -->';
            return;
            
        }

        // remove the first h3 tag
        $domRSSLinks = $domRSSLinks->cloneNode( true );
        $nodeH3 = $domRSSLinks->getElementsByTagName( 'h3' )->item( 0 );
        $domRSSLinks->removeChild( $nodeH3 );
        $nodeA1 = $domRSSLinks->getElementsByTagName( 'a' )->item( 0 );
        $strRSSLink = $nodeA1->getAttribute( 'href' );
        $arrURL = explode( "ref=", $strRSSLink, 2 );
        $strRSSLink = $arrURL[0];
        return $strRSSLink;        
        
    }
    
}
        