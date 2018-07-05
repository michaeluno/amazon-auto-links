<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2018 Michael Uno
 */

/**
 * Creates Amazon product links by category.
 * 
 * @package     Amazon Auto Links
 * @filter      apply       aal_filter_description_node
 *  first parameter:    the description node
 *  second parameter:   the AmazonAutoLinks_Core object
 * 
 * @since       unknown
 * @since       3           Changed the name from `AmazonAutoLinks_UnitOutput_Category`.
 */
class AmazonAutoLinks_UnitOutput_category extends AmazonAutoLinks_UnitOutput_Base_ElementFormat {

    /**
     * Stores the unit type.
     * @remark      Note that the base constructor will create a unit option object based on this value.
     */
    public $sUnitType = 'category';

    public static $aStructure_Product = array(
        'thumbnail_url'         => null,
        'ASIN'                  => null,
        'product_url'           => null,
        'raw_title'             => null,
        'title'                 => null,
        'description'           => null,    // the formatted feed item description - some elements are removed 
        'text_description'      => null,    // the non-html description
            
        // 3+
        'price'                 => null,
        'review'                => null,
        'rating'                => null,
        'image_set'             => null,
        'button'                => null,
        
        // Unused items
        'updated_date'          => null,    // the date posted - usually it's the updated time of the feed at Amazon so it's useless.
        
        
        // 3.3.0
        'content'               => null,
        'meta'                  => null,
        'similar_products'      => null,
        
    );
    
    
    /**
     * Stores rss urls to fetch.
     */
    protected $_aRSSURLs = array();
    
    /**
     * Stores rss urls to fetch and exclude the items from the result.
     */
    protected $_aExcludingRSSURLs = array();

    public function get( $aURLs=array(), $sTemplatePath=null ) {
        $this->___setHooksForOutput();
        $_sOutput = parent::get( $aURLs, $sTemplatePath );
        $this->___removeHooksForOutput();
        return $_sOutput;
    }

    /**
     * Sets up properties.
     */
    public function __construct( $aoUnitOptions=array() ) {

        $this->___setProperties();
        parent::__construct( $aoUnitOptions );

    }

    /**
     * Called before any properties are set.
     */
    private function ___setProperties() {
        $_oOption = AmazonAutoLinks_Option::getInstance();
        if ( $_oOption->isAPIConnected() ) {
            $this->_aItemFormatDatabaseVariables[] = '%description%';
            $this->_aItemFormatDatabaseVariables[] = '%content%';
        }
    }

    /**
     * Sets up properties. Called at the end of the constructor.
     *
     * @remark      The 'tag' unit type will override this method.
     */
    protected function _setProperties() {

        $this->_aRSSURLs          = $this->_getRSSURLsFromArguments(
            $this->oUnitOption->get( array( 'categories' ), array() )
        );

        $this->_aExcludingRSSURLs = $this->_getRSSURLsFromArguments(
            $this->oUnitOption->get( array( 'categories_exclude' ), array() )
        );

    }
        /**
         * @since       3.3.0
         */
        private function ___setHooksForOutput() {
            add_filter(
                'aal_filter_unit_each_product_with_database_row', 
                array( $this, 'replyToFormatProductWithDBRow' ), 
                10, 
                3
            );
        }
        /**
         * @since       3.5.0
         */
        private function ___removeHooksForOutput() {
            remove_filter(
                'aal_filter_unit_each_product_with_database_row',
                array( $this, 'replyToFormatProductWithDBRow' ),
                10
            );
        }

        /**
         * @remark      The 'tag' unit type will extend this mehtod.
         * @return      array
         */
        protected function _getRSSURLsFromArguments( array $aCategories ) {
            $_aRSSURLs = array();
            foreach( $aCategories as $_aCategory ) {
                $_aRSSURLs[] = $_aCategory[ 'feed_url' ];
            }
            return $_aRSSURLs;                
        }
        

    /**
     * Called when the unit has access to the plugin custom database table.
     * 
     * Sets the 'content' and 'description' elements in the product (item) array which require plugin custom database table.
     * 
     * @return      3.3.0
     * @return      array
     * @callback    add_filter      aal_filter_unit_each_product_with_database_row
     */
    public function replyToFormatProductWithDBRow( $aProduct, $aDBRow, $aScheduleIdentifier=array() ) {
    
        $aProduct[ 'content' ]      = $this->_getContents( $aProduct, $aDBRow, $aScheduleIdentifier );
        $aProduct[ 'description' ]  = $aProduct[ 'description' ] . " "  // only the meta is added by default
            . "<div class='amazon-product-description'>"
                . $this->_getDescriptionSanitized(
                    $aProduct[ 'content' ], 
                    $this->oUnitOption->get( 'description_length' ), 
                    $this->_getReadMoreText( $aProduct[ 'product_url' ] )
                )
            . "</div>";
        return $aProduct;
    
    }
        
    /**
     * @return      string
     * @since       3.3.0
     * 
     */
    protected function _getContents( $aProduct /*, $aDBRow, $aScheduleIdentifier */ ) {
        
        $_aParams            = func_get_args();
        $aProduct            = $_aParams[ 0 ];
        $aDBRow              = $_aParams[ 1 ];
        $aScheduleIdentifier = $_aParams[ 2 ];

        $_oRow = new AmazonAutoLinks_UnitOutput___Database_Product(
            $aScheduleIdentifier[ 'asin' ],
            $aScheduleIdentifier[ 'locale' ],
            $aScheduleIdentifier[ 'associate_id' ],
            $aDBRow,
            $this->oUnitOption
        );
        $_aReviews = $_oRow->getCell( 'editorial_reviews', array() );

        $_oContentFormatter = new AmazonAutoLinks_UnitOutput__Format_content( 
            $_aReviews,
            $this->oDOM,
            $this->oUnitOption
        );
        $_sContents = $_oContentFormatter->get();
                
        return "<div class='amazon-product-content'>"
                . $_sContents
            . "</div>";

    }               

    /**
     * Fetches and returns the associative array containing the output of product links.
     * 
     * If the first parameter is not given, 
     * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
     * 
     * @return            array            The array contains product information. 
     */
    public function fetch( $aRSSURLs=array() ) {

        $aRSSURLs = $this->formatRSSURLs( 
            empty( $aRSSURLs )
                ? $this->_aRSSURLs
                : $aRSSURLs            
        );
        if ( empty ( $aRSSURLs ) ) { 
            return array(); 
        }

        $_aExcludingRSSURLs = $this->formatRSSURLs( 
            $this->_aExcludingRSSURLs
        );
        $_oRSS = new AmazonAutoLinks_RSSClient( 
            $aRSSURLs,
            ( int ) $this->oUnitOption->get( 'cache_duration' )
        );
        $this->_setBlackASINs( $_oRSS->get() );
        
        $_oRSS = new AmazonAutoLinks_RSSClient( 
            $aRSSURLs,
            ( int ) $this->oUnitOption->get( 'cache_duration' )
        );
        $_oRSS->sSortOrder = $this->_getSortOrder();        
        return $this->getProducts( $_oRSS->get() );

    }
        /**
         * Converts the sort order for the RSS client property.
         * @since       3
         * @return      string
         */
        private function _getSortOrder() {
            
            // random', // date, title, title_descending    
            $_sSortOrder = $this->oUnitOption->get( 'sort' );
            switch( $_sSortOrder ) {
                case 'date':
                    return 'date_descending';
                case 'title':
                    return 'title_ascending';
                case 'title_descending':
                case 'random':
                    return $_sSortOrder;
                default:
                    return 'random';
            }
        }
    /**
     * Formats the given RSS urls.
     * 
     * - Adds associate id.
     * - Adds the corresponding ad type's urls.
     * 
     *  [feed_type] => Array (
     *         [bestsellers]     => 1 // 'bestsellers'
     *         [hotnewreleases]  => 0 // 'new-releases'
     *         [moverandshakers] => 0 // 'movers-and-shakers'
     *         [toprated]        => 0 // 'top-rated'
     *         [mostwished_for]  => 0 // 'most-wished-for'
     *         [giftideas]       => 0 // 'most-gifted'
     *      )        
     * @remark            The passed urls are assumed for the bestsellers'.
     */
    protected function formatRSSURLs( $aRSSURLs ) {
    
        $_aFormatedURLs = array();
        foreach( $this->getAsArray( $aRSSURLs ) as $_sRSSURL ) {            
            foreach ( $this->oUnitOption->get( 'feed_type' ) as $_sSlug => $_bEnable ) {
                if ( ! $_bEnable ) {
                    continue; 
                }
                $_aFormatedURLs[] = $this->formatRSSURL( 
                    str_replace( 
                        "/rss/bestsellers/", 
                        "/rss/{$_sSlug}/", 
                        $_sRSSURL 
                    )
                );
            }    
        }
        return array_unique( $_aFormatedURLs );
        
    }
    protected function formatRSSURL( $sRSSURL ) {
        
        $_aURLElems = parse_url( trim( $sRSSURL ) );
        
        /**
         * If the scheme is https, Amazon returns the contents formed with SSL such as image src urls with https://.
         * However, fetching feeds via SSL makes it somewhat slow. To resolve the speed issue, 
         * it might be worth considering fetching feeds as non-SSL and convert images to SSL-compatible when rendering.
         */
        $_sScheme = $this->bIsSSL 
            ? "https" 
            : $_aURLElems[ 'scheme' ];
        return add_query_arg( 
            array( 
                // 'tag' => $this->oUnitOption->get( 'associate_id' ),
                'tag' => 'amazon-auto-links-20',
            ), 
            "{$_sScheme}://{$_aURLElems['host']}{$_aURLElems['path']}"
        );
        
        
    }
    
        
    /**
     * Adds the asin to black list from the given feed object.
     * @since       unknown
     * @since       3       Changed to accept rss items array from SimplePie feed object.
     * @return      void
     */
    protected function _setBlackASINs( array $aItems ) {
        foreach ( $aItems as $_aItem ) {
            $this->setParsedASIN( 
                $this->getASINFromURL(
                    $this->getElement( $aItems, 'link' ) 
                ) 
            );
        }
    }
    
    /**
     * Constructs and returns associative array from the fetched RSS data.
     * 
     * @return      array
     */
    protected function getProducts( array $aItems ) {

        // Disable DOM related errors to be displayed.
        $_bDOMError = libxml_use_internal_errors( true );

        $_aProducts = $this->___getProductsFromResponseItems(
            $aItems,          // items
            strtoupper( $this->oUnitOption->get( 'country' ) ), // locale
            $this->oUnitOption->get( 'associate_id' ),          // associate id
            $this->oUnitOption->get( 'count' )
        );
        // Revert the error message setting for DOM
        libxml_use_internal_errors( $_bDOMError );
        return $_aProducts;

    }
        /**
         * @param       array   $_aItems
         * @param       string  $_sLocale
         * @param       string  $_sAssociateID
         * @since       3.5.0
         * @return      array
         */
        private function ___getProductsFromResponseItems( array $aItems, $_sLocale, $_sAssociateID, $_iCount ) {

            // First Iteration - Extract displaying ASINs.
            $_aASINLocales = array();  // stores added product ASINs for performing a custom database query.
            $_aProducts    = array();
            foreach ( $aItems as $_iIndex => $_aItem ) {

                // This parsed item is no longer needed and must be removed once it is parsed
                // as this method is called recursively.
                unset( $aItems[ $_iIndex ] );

                try {
                    $_aProduct = $this->___getProduct(
                        $_aItem,
                        $_sLocale,
                        $_sAssociateID
                    );

                } catch ( Exception $_oException ) {
                    // AmazonAutoLinks_Debug::log( $_oException->getMessage() );
                    continue;   // skip
                }

                // Store the product output
                $_aASINLocales[] = $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale );
                $_aProducts[]    = $_aProduct;

                // Max Number of Items
                if ( count( $_aProducts ) >= $_iCount ) {
                    break;
                }

            }

            return $this->___getProductsFormattedFromResponseItems(
                $aItems,
                $_aProducts,
                $_aASINLocales,
                $_sLocale,
                $_sAssociateID,
                $_iCount
            );

        }
            /**
             *
             * @return     array
             * @since      3.5.0
             */
            private function ___getProductsFormattedFromResponseItems( $aItems, $_aProducts, $_aASINLocales, $_sLocale, $_sAssociateID, $_iCount ) {

                try {
                    $_iResultCount = count( $_aProducts );
                    // Second iteration.
                    $_aProducts = $this->_getProductsFormatted(
                        $_aProducts,
                        $_aASINLocales,
                        $_sLocale,
                        $_sAssociateID
                    );
                    $_iCountAfterFormatting = count( $_aProducts );
                    if ( $_iResultCount > $_iCountAfterFormatting ) {
                        throw new Exception( $_iCount - $_iCountAfterFormatting );
                    }

                } catch ( Exception $_oException ) {

                    // Do a recursive call
                    $_aAdditionalProducts = $this->___getProductsFromResponseItems(
                        $aItems,
                        $_sLocale,
                        $_sAssociateID,
                        ( integer ) $_oException->getMessage() // the number of items to retrieve
                    );
                    $_aProducts = array_merge( $_aProducts, $_aAdditionalProducts );

                }
                return $_aProducts;

            }
            /**
             *
             * @return     array
             * @since      3.5.0
             */
            private function ___getProduct( $_aItem, $_sLocale, $_sAssociateID ) {

                // Load a DOM Object for description.
                // passing an empty string to the second parameter disables mb_language() function to be executed.
                $_oDoc = $this->oDOM->loadDOMFromHTMLElement(
                    $this->getElement( $_aItem, 'description' ),
                    '' // mb_language
                );

                // The first depth div tag - If SimplePie is used outside of WordPress it should be the second depth which contains the description including images
                // Skip if the container node object could not be established. Sometimes this happens when unavailable feed is passed, such as Top Rated, which is not supported in some countries.
                $_oNodeDiv = $_oDoc->getElementsByTagName( 'div' )->item( 0 );
                if ( ! $_oNodeDiv ) {
                    throw new Exception( 'Failed to create a node.' );
                }

                $_aProduct = self::$aStructure_Product;

                // ASIN - required to detect duplicated items.
                $_sPermalink         = trim( $this->getElement( $_aItem, 'link' ) );
                $_aProduct[ 'ASIN' ] = $this->getASINFromURL( $_sPermalink );
                if ( $this->isASINBlocked( $_aProduct[ 'ASIN' ] ) ) {
                    throw new Exception( 'The ASIN is black-listed: ' . $_aProduct[ 'ASIN' ] );
                }

                // Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
                $_aProduct[ 'product_url' ] = $this->getProductLinkURLFormatted(
                    $_sPermalink,
                    $_aProduct[ 'ASIN' ]
                );

                // Title
                $_aProduct[ 'raw_title' ] = $this->getElement( $_aItem, 'title' );
                $_aProduct[ 'title' ]     = $this->_getTitleSanitized( $_aProduct[ 'raw_title' ] );
                if ( $this->isTitleBlocked( $_aProduct[ 'title' ] ) ) {
                    throw new Exception( 'The title is black-listed: ' . $_aProduct[ 'title' ] );
                }

                // Description ( creates $htmldescription and $textdescription )
                // remove the span tag containing the title
                $this->oDOM->removeNodeByTagAndClass( $_oNodeDiv, 'span', 'riRssTitle', 0 );
                $_aProduct[ 'text_description' ] = $this->getTextDescription( $_oNodeDiv );
                if ( $this->isDescriptionBlocked( $_aProduct[ 'text_description' ] ) ) {
                    throw new Exception( 'The description is black-listed: ' . $_aProduct[ 'text_description' ] );
                }

                // At this point, update the black&white lists as this item is parsed.
                $this->setParsedASIN( $_aProduct[ 'ASIN' ] );

                // Images - img tags
                $this->formatImages(
                    $_oDoc,
                    array(
                        'alt'   => $_aProduct[ 'title' ],
                        'title' => $_aProduct[ 'text_description' ]
                    )
                );

                // Thumbnail image
                $_aProduct[ 'thumbnail_url' ] = $this->getThumbnail(
                    $_oDoc,
                    $this->oUnitOption->get( 'image_size' )
                );

                // Check whether no-image shuld be skipped.
                if ( ! $this->isImageAllowed( $_aProduct[ 'thumbnail_url' ] ) ) {
                    throw new Exception( 'No image is allowed: ' );
                }

                // Links - a tags
                $this->formatLinks(
                    $_oNodeDiv,
                    array(
                        'rel'       => 'nofollow',
                        'target'    => '_blank',
                        'title'     => $_aProduct[ 'title' ] . " - " . $_aProduct[ 'text_description' ]
                    ),
                    $_aProduct[ 'ASIN' ]
                );
                $_aProduct[ 'meta' ]                = "<div class='amazon-product-meta'>"
                        . $this->getDescription( $_oNodeDiv, $_aItem )
                    . "</div>";
                $_aProduct[ 'description' ]         = $_aProduct[ 'meta' ];


                // no published date of the product is available in this feed
                $_aProduct[ 'updated_date' ]        = $this->getElement( $_aItem, 'pubDate' );  // 3.2.0+

                // Format the item
                // Thumbnail
                $_aProduct[ 'formatted_thumbnail' ] = $this->_getProductThumbnailFormatted( $_aProduct );
                $_aProduct[ 'formed_thumbnail' ]    = $_aProduct[ 'formatted_thumbnail' ];  // backward compatibility

                // Title
                $_aProduct[ 'formatted_title' ]     = $this->_getProductTitleFormatted( $_aProduct );
                $_aProduct[ 'formed_title' ]        = $_aProduct[ 'formatted_title' ];  // backward compatibility

                // Button - check if the %button% variable exists in the item format definition.
                // It accesses the database, so if not found, the method should not be called.
                if (
                    $this->hasCustomVariable(
                        $this->oUnitOption->get( 'item_format' ),
                        array( '%button%', )
                    )
                ) {
                    $_aProduct[ 'button' ] = $this->_getButton(
                        $this->oUnitOption->get( 'button_type' ),
                        $this->_getButtonID(),
                        $_aProduct[ 'product_url' ],
                        $_aProduct[ 'ASIN' ],
                        $_sLocale,
                        $_sAssociateID,
                        $this->_getButtonID(),
                        $this->oOption->get( 'authentication_keys', 'access_key' ) // public access key
                    );

                }

                /**
                 * Let third-parties filter products.
                 * @since 3.4.13
                 */
                $_aProduct = apply_filters(
                    'aal_filter_unit_each_product',
                    $_aProduct,
                    array(
                        'locale'        => $_sLocale,
                        'asin'          => $_aProduct[ 'ASIN' ],
                        'associate_id'  => $_sAssociateID,
                        'asin_locale'   => $_aProduct[ 'ASIN' ] . '_' . strtoupper( $_sLocale ),
                    ),
                    $this
                );
                if ( empty( $_aProduct ) ) {
                    throw new Exception( 'The product array is empty. Most likely it is filtered out.' );
                }
                return $_aProduct;

            }
        
    /**
     * Converts the url scheme to https:// from http:// and uses the amazon's secure image server.
     */
    protected function setSSLImagesByDOM( $oDoc ) {        
        foreach ( $oDoc->getElementsByTagName( 'img' ) as $_oNodeImg ) {
            $_oNodeImg->attributes->getNamedItem( "src" )->value = $this->getAmazonSSLImageURL(
                $_oNodeImg->attributes->getNamedItem( "src" )->value
            );    
        }
    }
        /**
         * @deprecated
         */
        protected function respectSSLImages( $oDoc ) {
            $this->setSSLImagesByDOM( $oDoc );
        }
        
    /**
     * Retrieves the description part from the given dom node.
     * 
     */
    protected function getDescription( $oNode )  {

        $oNode = apply_filters( 'aal_filter_description_node', $oNode, $this );
        
        // Add markings to the text node which later gets converted to a whitespace because by itself elements don't have white spaces between each other.
        foreach( $oNode->childNodes as $_oChildNode ) {
            if ( 3 == $_oChildNode->nodeType ) {        // nodeType:3 TEXT_NODE
                $_oChildNode->nodeValue = '[identical_replacement_string]' . $_oChildNode->nodeValue . '[identical_replacement_string]';
            }
        }
        
        // getInnerHTML extracts inner html code, meaning the outer div tag will be stripped.
        $_sDescription = str_replace( '[identical_replacement_string]', '<br />', $this->oDOM->getInnerHTML( $oNode ) );
        
        // Omit the text 'visit blah blah blah for more information'
        if ( preg_match( '/<span.+class=["\']price["\'].+span>/i', $_sDescription ) ) { // "
        
            // $_aDescription = preg_split('/<span.+class=["\']price["\'].+span>\K/i', $_sDescription);  // this works above PHP v5.2.4
            $_aDescription = preg_split( '/(<span.+class=["\']price["\'].+span>)\${0}/i', $_sDescription, null, PREG_SPLIT_DELIM_CAPTURE ); // "
            
        } else {
        
            // $_aDescription = preg_split('/<font.+color=["\']#990000["\'].+font>\K/i', $_sDescription);     // this works above PHP v5.2.4
            $_aDescription = preg_split( '/(<font.+color=["\']#990000["\'].+font>)\${0}/i', $_sDescription, null, PREG_SPLIT_DELIM_CAPTURE );    // " (syntax fixer )
        }    
        $_sDescription1 = isset( $_aDescription[0] ) ? $_aDescription[0] : '';
        $_sDescription2 = isset( $_aDescription[1] ) ? $_aDescription[1] : '';
        $_sDescription  = $_sDescription1 . $_sDescription2;
        $_aDescription  = preg_split('/<br.*?\/?>/i', $_sDescription);        // devide the string into arrays by <br> or <br />    
        $_sDescription  = trim( implode( " ", $_aDescription ) );    // return them back to html text
        $_sDescription  = force_balance_tags( $_sDescription );
        return $_sDescription;        
        
    }
    
    
    /**
     * Retrieves the description part from the given dom node and strips the html tags.
     * 
     */
    protected function getTextDescription( $oNode ) {
        
        // Divide the string into arrays by <br> or <br />
        $_aDescription = preg_split( 
            '/<br.*?\/?>/i', 
            $this->oDOM->getInnerHTML( $oNode ) 
        );
        array_splice( $_aDescription, -2 );        // remove the last two elements 
        $_sHTMLDescription = implode( "&nbsp;", $_aDescription );
        return esc_attr( 
            html_entity_decode( 
                trim( 
                    strip_tags( $_sHTMLDescription ) 
                ), 
                ENT_QUOTES, 
                $this->sCharEncoding 
            ) 
        );
        
    }

    
    /**
     * Formats the links in the given DOM node.
     * 
     */
    protected function formatLinks( $oNode, $aAttributes=array(), $sASIN ) {

        $aAttributes = ( ( array ) $aAttributes ) + array(
            'rel'   => 'nofollow',
            'title' => '',
        );
    
        foreach( $oNode->getElementsByTagName( 'a' ) as $_nodeA ) {
            
            $_sHref = $_nodeA->getAttribute( 'href' );
            if ( empty( $_sHref ) ) { continue; }
            $_sHref = $this->getProductLinkURLFormatted( $_sHref, $sASIN );

            // Reported Issue: Warning: DOMElement::setAttribute() [domelement.setattribute]: string is not in UTF-8
            $_bResult = @$_nodeA->setAttribute( 'href', $_sHref );
            
            // if ( empty( $_bResult ) ) echo "error setting the url: " . $_sHref;
            foreach( $aAttributes as $strAttr => $strProperty ) {
                @$_nodeA->setAttribute( $strAttr, $strProperty );
            }
        
        }
    
    }
    
    protected function formatImages( $oNode, $aAttributes=array() ) {
        
        // Take care of SSL image urls - in SSL enabled sites, if src-image urls use non-ssl protocol, some browsers show warnings.
        if ( $this->bIsSSL ) { 
            $this->setSSLImagesByDOM( $oNode ); 
        }
        
        // For the Brazil and Mexico locals, the element images in descriptions should be replaced as they don't load.
        if ( in_array( $this->oUnitOption->get( 'country' ), array( 'MX', 'BR' ) ) ) {
            $this->supportBrazilAndMexicoImages( $oNode );
        }
        
        // Modify attributes
        $this->oDOM->setAttributesByTagName( $oNode, 'img', $aAttributes );
        
    }
    
    protected function supportBrazilAndMexicoImages( $oNode ) {
    
        foreach( $oNode->getElementsByTagName( 'img' ) as $_oSelectedNode )  {
            
            $_sImageURL = @$_oSelectedNode->getAttribute( 'src' );
            $_sImageURL = str_replace(
                array( "/images/G/32/detail/", "/images/G/33/detail/" ),    // find    
                "/images/G/01/detail/",    // replace
                $_sImageURL    // source
            );    
            if ( $_sImageURL ) {
                @$_oSelectedNode->setAttribute( 'src', $_sImageURL );
            }
            
        }
        
    }
    
    protected function getThumbnail( $oDoc, $iImageSize ) {
            
        $_sImgURL =""; 
        if ( $iImageSize > 0 ) {            
        
            $nodeImg = $oDoc->getElementsByTagName( 'img' )->item( 0 );
            if ( $nodeImg ) {
                $_sImgURL = $nodeImg->attributes->getNamedItem( "src" )->value;
                $_sImgURL = $this->getImageURLBySize( $_sImgURL, $iImageSize );
            } 
            
        }
        // removes the div tag containing the image
        foreach ( $oDoc->getElementsByTagName( 'div' ) as $_nodeDivFloat ) {
            // if the string 'float' is found 
            if ( false !== stripos( $_nodeDivFloat->getAttribute( 'style' ), 'float' ) ) {        
                $_nodeDivFloat->parentNode->removeChild( $_nodeDivFloat );
                break;
            }
        }
        return $_sImgURL;            
        
    }
    
    
    /**
     * Sets up SimplePie object.
     * 
     * @param       integer     $iCacheDuration     // 60 seconds * 60 = 1 hour, 1800 = 30 minutes
     */
    protected function getFeedObj( $aUrls, $iItem=10, $iCacheDuration=36000 ) {    
    
        
        // Reuse the object that already exists. This conserves the memory usage.
        // $this->oFeed = isset( $this->oFeed ) ? $this->oFeed : new AmazonAutoLinks_SimplePie;
        // $oFeed = $this->oFeed; // 
        
        // For excluding sub categories, new instances need to be instantiated per set of uls as SimplePie somehow does not 
        // property output newly fetched items.
        $_oFeed = new AmazonAutoLinks_SimplePie();
        
        // Set sort type.        
        $_oFeed->set_sortorder( $this->oUnitOption->get( 'sort' ) );
        $_oFeed->set_charset_for_sort( $this->sCharEncoding );
        $_oFeed->set_keeprawtitle( $this->oUnitOption->get( 'keep_raw_title' ) );
        
        // Set urls
        $_oFeed->set_feed_url( $aUrls );    
        $_oFeed->set_item_limit( $iItem );    
        
        // This should be set after defining $urls
        $_oFeed->set_cache_duration( $iCacheDuration );    
        
        $_oFeed->set_stupidly_fast( true );
        
        // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
        if ( 0 == $iCacheDuration ) {
            // setting it true will be considered the background process; thus, it won't trigger the renewal event.
            $_oFeed->setBackground( true );    
        }
        
        // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
        $_oFeed->enable_order_by_date( true );    
        
        // $_oFeed->file_class = 'AmazonAutoLinks_SimplePie_File';    // this is assigned in the class definition already
        $_oFeed->init();     // will perform fetching the feeds.
        return $_oFeed;
        
    }        
    
}