<?php
/**
 * Creates Amazon product links by category.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @filter            aal_filter_set_url            
 *         first parameter:    feed url
 *         second parameter:    ad type slug
 * @filter            aal_filter_description_node
 *         first parameter:    the description node
 *         second parameter:    the AmazonAutoLinks_Core object
 * @action            aal_action_output_rss
 *         first parameter:     the AmazonAutoLinks_Core object
 *         second paramter:    the unit option array 
 */

abstract class AmazonAutoLinks_Unit_Category_ extends AmazonAutoLinks_Unit {

    public static $arrStructure_Args = array(
        'count'                 => 10,
        'column'                => 4,
        'country'               => 'US',
        'associate_id'          => null,
        'image_size'            => 160,
        'sort'                  => 'random', // date, title, title_descending    
        'keep_raw_title'        => false,    // this is for special sorting method.
        'feed_type'             => array(
            'bestsellers'           => true, 
            'new-releases'          => false,
            'movers-and-shakers'    => false,
            'top-rated'             => false,
            'most-wished-for'       => false,
            'most-gifted'           => false,    
        ),
        'ref_nosim'             => false,
        'title_length'          => -1,
        'link_style'            => 1,
        'credit_link'           => 1,
        'categories'            => array(),    
        'categories_exclude'    => array(),
        'title'                 => '',          // won't be used to fetch links. Used to create a unit.
        'template'              => '',          // the template name - if multiple templates with a same name are registered, the first found item will be used.
        'template_id'           => null,        // the template ID: md5( relative dir path )
        'template_path'         => '',          // the template can be specified by the template path. If this is set, the 'template' key won't take effect.

        'is_preview'            => false,       // used to decide whether the global ASIN black/white list should be used.
        
        // The below are retrieved separately
        // 'item_format' => '',
        // 'image_format' => '',
        // 'title_format' => '',
        
        'id'                    => null,    // the unit id
        '_labels'               => array(),    // stores labels (plugin custom taxonomy)
    );

    public static $arrStructure_Product = array(
        'thumbnail_url'         => null,
        'ASIN'                  => null,
        'product_url'           => null,
        'raw_title'             => null,
        'title'                 => null,
        'description'           => null,    // the formatted feed item description - some elements are removed 
        'text_description'      => null,    // the non-html description
        
        // Useless items
        'content'               => null,    // the raw feed item content 
        'author'                => null,    // the author of the product - most likely no value
        'date'                  => null,    // the date posted - usually it's the updated time of the feed at Amazon so it's useless.
        'category'              => null,    // the category assigned to the product - most likely no value
    );
    
    
    /**
     * Stores the ASINs of parsed product links.
     */
    protected $arrParsedASINs = array();
    
    function __construct( $arrArgs=array() ) {
            
        parent::__construct();
        $this->setArguments( $arrArgs );
        $this->strUnitType = 'category';
        
    }
    
    /**
     * Allows setting arguments externally.
     * 
     * This can be used to reset the arguments and restart fetching.
     * However, SimplePie somehow does not renew the used urls so currently it's better to instantiate per set of urls.
     * 
     */
    public function setArguments( $arrArgs ) {

        $this->arrArgs              = $arrArgs + self::$arrStructure_Args + self::getItemFormatArray();
        $this->arrRSSURLs           = $this->getRSSURLsFromArguments( $this->arrArgs );
        $this->arrExcludingRSSURLs  = $this->getRSSURLsFromArguments( $this->arrArgs, 'categories_exclude' );

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
                ? $this->arrRSSURLs
                : $aRSSURLs            
        );
        if ( empty ( $aRSSURLs ) ) { return array(); }

        // Disable DOM related errors to be displayed.
        $_bDOMError = libxml_use_internal_errors( true );        
        
        $_aExcludingRSSURLs = $this->formatRSSURLs( $this->arrExcludingRSSURLs );
        $_oFeed_Exclude     = $this->getFeedObj( $_aExcludingRSSURLs );
        $this->updateBlackASINs( $_oFeed_Exclude );
        
        $_oFeed     = $this->getFeedObj( $aRSSURLs );                
        $_aProducts = $this->composeAssociativeArray( $_oFeed );

        // Revert the error message setting for DOM
        libxml_use_internal_errors( $_bDOMError );
        
        return $_aProducts;
    }
    
    /**
     * Formats the given RSS urls.
     * 
     * - Adds associate id.
     * - Adds the corresponding ad type's urls.
     * 
     *  [feed_type] => Array
     *      (
     *         [bestsellers] => 1        // 'bestsellers'
     *         [hotnewreleases] => 0    // 'new-releases'
     *         [moverandshakers] => 0    // 'movers-and-shakers'
     *         [toprated] => 0            // 'top-rated'
     *         [mostwished_for] => 0    // 'most-wished-for'
     *         [giftideas] => 0            // 'most-gifted'
     *      )        
     *              
     * @remark            The passed urls are assumed for the bestsellers'.
     * 
     */
    protected function formatRSSURLs( $aRSSURLs ) {
    
        $aRSSURLs       = is_array( $aRSSURLs ) ? $aRSSURLs : array( $aRSSURLs );
        $_aFormatedURLs = array();

        foreach( $aRSSURLs as $_sRSSURL ) {            
            foreach ( $this->arrArgs['feed_type'] as $_sSlug => $_bEnable ) {
                if ( ! $_bEnable ) { continue; }
                $_aFormatedURLs[] = $this->formatRSSURL( str_replace( "/rss/bestsellers/", "/rss/{$_sSlug}/", $_sRSSURL ) );
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
        $_sScheme = $this->fIsSSL ? "https" : $_aURLElems['scheme'];
        return add_query_arg( 
            array( 'tag' => $this->arrArgs['associate_id'] ), 
            "{$_sScheme}://{$_aURLElems['host']}{$_aURLElems['path']}"
        );
        
        
    }
    
    protected function getRSSURLsFromArguments( $aArgs, $sKey='categories' ) {
        
        $_aRSSURLs = array();
        foreach( $aArgs[ $sKey ] as $_aCategory ) {
            $_aRSSURLs[] = $_aCategory[ 'feed_url' ];
        }
        return $_aRSSURLs;        
        
    }
        
    protected function updateBlackASINs( $oFeed ) {
        foreach ( $oFeed->get_items( 0, 0 ) as $_oItem ) {
            $this->arrBlackListASINs[] = $this->getASIN( $_oItem->get_permalink() );
        }
    }
    
    /**
     * Composes and returns associative array from the fetched RSS data.
     * 
     */
    protected function composeAssociativeArray( $oFeed ) {

        $_aProducts = array();

        foreach ( $oFeed->get_items( 0, 0 ) as $oItem ) {
                        
            $_aProduct = array();
            
            // Load a DOM Object for description.
            // passing an empty string to the second parameter disables mb_language() function to be executed.
            $_oDoc = $this->oDOM->loadDOMFromHTMLElement( $oItem->get_description(), '' );    

            // The first depth div tag - If SimplePie is used outside of WordPress it should be the second depth which contains the description including images
            // Skip if the container node object could not be established. Sometimes this happens when unavailable feed is passed, such as Top Rated, which is not supported in some countries.
            $_oNodeDiv = $_oDoc->getElementsByTagName( 'div' )->item( 0 );
            if ( ! $_oNodeDiv ) { continue; }

            // ASIN - required to detect duplicated items.
            $_sPermalink        = $oItem->get_permalink();
            $_aProduct['ASIN']  = $this->getASIN( $_sPermalink );    
            if ( $this->isBlocked( $_aProduct['ASIN'], 'asin' ) ) { continue; }
            if ( $this->arrArgs['is_preview'] || ! $this->fNoDuplicate ) {
                $this->arrBlackListASINs[] = $_aProduct['ASIN'];    // for mush-ups.
            } else {
                $GLOBALS['arrBlackASINs'][] = $_aProduct['ASIN'];    
            }
            
            // Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
            $_aProduct['product_url'] = $this->formatProductLinkURL( $_sPermalink, $_aProduct['ASIN'] );

            // @todo I don't remember what this was for. Remove it.
            // $this->arrParsedASINs[ $_aProduct['product_url'] ] = $_aProduct['ASIN'];    
            
            // Title
            $_aProduct['raw_title'] = $oItem->get_title();
            $_aProduct['title']     = $this->sanitizeTitle( $_aProduct['raw_title'] );
            
            if ( $this->isBlocked( $_aProduct['title'], 'title' ) ) { continue; }
        
            // Description ( creates $htmldescription and $textdescription ) 
            $this->oDOM->removeNodeByTagAndClass( $_oNodeDiv, 'span', 'riRssTitle', 0 );    // remove the span tag containing the title
            $_aProduct['text_description'] = $this->getTextDescription( $_oNodeDiv );
            if ( $this->isBlocked( $_aProduct['text_description'], 'description' ) ) { continue; }

            // Images - img tags 
            $this->formatImages( 
                $_oDoc, 
                array( 
                    'alt'=> $_aProduct['title'], 
                    'title' => $_aProduct['text_description'] 
                ) 
            );
        
            // Thumbnail image
            $_aProduct['thumbnail_url'] = $this->getThumbnail( $_oDoc, $this->arrArgs['image_size'] );

            // Links - a tags
            $this->formatLinks( 
                $_oNodeDiv, 
                array( 
                    'rel'       => 'nofollow',
                    'target'    => '_blank',
                    'title'     => $_aProduct['title'] . " - " . $_aProduct['text_description'] 
                ),
                $_aProduct['ASIN']
            );
            $_aProduct['description']   = $this->getDescription( $_oNodeDiv );

            // Other elements - amazon does not provide the information for these but just in case.
            $_aProduct['category']      = ( $oCategory = $oItem->get_category() ) ? $oCategory->get_label() : '';    
            $_aProduct['author']        = ( $oAuthor = $oItem->get_author() ) ? $oAuthor->get_name() : '';
            $_aProduct['content']       = $oItem->get_content();
            $_aProduct['date']          = $oItem->get_date();
            
            // Format the item
            // Thumbnail
            $_aProduct['formatted_thumbnail'] = $this->_formatProductThumbnail( $_aProduct );
            $_aProduct['formed_thumbnail']    = $_aProduct['formatted_thumbnail'];  // backward compatibility
        
            // Title
            $_aProduct['formatted_title']     = $this->_formatProductTitle( $_aProduct );
            $_aProduct['formed_title']        = $_aProduct['formatted_title'];  // backward compatibility
            
            // Item        
            $_aProduct['formatted_item']      = $this->_formatProductOutput( $_aProduct );
            $_aProduct['formed_item']         = $_aProduct['formatted_item'];   // backward compatibility
            
            // Store the product output
            $_aProducts[] = $_aProduct + self::$arrStructure_Product;
            
            // Max Number of Items 
            if ( count( $_aProducts ) >= $this->arrArgs['count'] ) { break; }
                    
        }             
        
        return $_aProducts;
    }
        /**
         * Returns the formatted product HTML blcok.
         * @since       2.1.1
         */
        private function _formatProductOutput( array $aProduct ) {
            return str_replace( 
                array( "%href%", "%title_text%", "%description_text%", "%title%", "%image%", "%description%" ),
                array( $aProduct['product_url'], $aProduct['title'], $aProduct['text_description'], $aProduct['formatted_title'], $aProduct['formatted_thumbnail'], $aProduct['description'] ),
                $this->arrArgs['item_format'] 
            );
        }    
        /**
         * Returns the formatted product title HTML block.
         * @since       2.1.1
         */
        private function _formatProductTitle( array $aProduct ) {
            return  str_replace( 
                array( "%href%", "%title_text%", "%description_text%" ),
                array( $aProduct['product_url'], $aProduct['title'], $aProduct['text_description'] ),
                $this->arrArgs['title_format'] 
            );
        }    
        /**
         * Returns the formatted product thumbnail HTML block.
         * @since       2.1.1
         */
        private function _formatProductThumbnail( array $aProduct ) {
            return str_replace( 
                array( "%href%", "%title_text%", "%src%", "%max_width%", "%description_text%" ),
                array( $aProduct['product_url'], $aProduct['title'], $aProduct['thumbnail_url'], $this->arrArgs['image_size'], $aProduct['text_description'] ),
                $this->arrArgs['image_format'] 
            );
        }    
    
    /**
     * Converts the url scheme to https:// from http:// and uses the amazon's secure image server.
     */
    protected function respectSSLImages( $oDoc ) {        
        foreach ( $oDoc->getElementsByTagName( 'img' ) as $_nodeImg ) {
            $_nodeImg->attributes->getNamedItem( "src" )->value = $this->respectSSLImage(
                $_nodeImg->attributes->getNamedItem( "src" )->value
            );    
        }
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
        return force_balance_tags( $_sDescription );

    }
    
    /**
     * Retrieves the description part from the given dom node and strips the html tags.
     * 
     */
    protected function getTextDescription( $oNode ) {
        
        // Divide the string into arrays by <br> or <br />
        $_aDescription = preg_split( '/<br.*?\/?>/i', $this->oDOM->getInnerHTML( $oNode ) );        
        array_splice( $_aDescription, -2 );        // remove the last two elements    
        $_sHTMLDescription = implode( "&nbsp;", $_aDescription );
        return esc_attr( html_entity_decode( trim( strip_tags( $_sHTMLDescription ) ), ENT_QUOTES, $this->strCharEncoding ) );        
        
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
            $_sHref = $this->formatProductLinkURL( $_sHref, $sASIN );

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
        if ( $this->fIsSSL ) { 
            $this->respectSSLImages( $oDoc ); 
        }
        
        // For the Brazil and Mexiko locals, the element images in descriptions should be replaced as they don't load.
        if ( in_array( $this->arrArgs['country'], array( 'MX', 'BR' ) ) ) {
            $this->supportBrazilAndMexikoImages( $oNode );
        }
        
        // Modify attributes
        $this->oDOM->setAttributesByTagName( $oNode, 'img', $aAttributes );
        
    }
    
    protected function supportBrazilAndMexikoImages( $oNode ) {
    
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
                $_sImgURL = $this->setImageSize( $_sImgURL, $iImageSize );
                // $_sImgURL = preg_replace( '/(?<=_S)([LS])(\d+){3}(?=_)/i', 'S${2}'. $iImageSize . '', $_sImgURL );  // adjust the image size. _SL160_ or _SS160_
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
     */
    protected function getFeedObj( $aUrls, $iItem=10, $iCacheDuration=36000 ) {    // 60 seconds * 60 = 1 hour, 1800 = 30 minutes
        
        // Reuse the object that already exists. This conserves the memory usage.
        // $this->oFeed = isset( $this->oFeed ) ? $this->oFeed : new AmazonAutoLinks_SimplePie;
        // $oFeed = $this->oFeed; // 
        
        // For excluding sub categories, new instances need to be instantiated per set of uls as SimplePie somehow does not 
        // property output newly fetched items.
        $_oFeed = new AmazonAutoLinks_SimplePie();
        
        // Set sort type.        
        $_oFeed->set_sortorder( $this->arrArgs['sort'] );
        $_oFeed->set_charset_for_sort( $this->strCharEncoding );
        $_oFeed->set_keeprawtitle( $this->arrArgs['keep_raw_title'] );
        
        // Set urls
        $_oFeed->set_feed_url( $aUrls );    
        $_oFeed->set_item_limit( $iItem );    
        
        // This should be set after defining $urls
        $_oFeed->set_cache_duration( $iCacheDuration );    
        
        $_oFeed->set_stupidly_fast( true );
        
        // If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
        if ( 0 == $iCacheDuration ) {
            $_oFeed->setBackground( true );    // setting it true will be considered the background process; thus, it won't trigger the renewal event.
        }
        
        // set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
        $_oFeed->enable_order_by_date( true );    
        
        // $_oFeed->file_class = 'AmazonAutoLinks_SimplePie_File';    // this is assigned in the class definition already
        $_oFeed->init();     // will perform fetching the feeds.
        return $_oFeed;
        
    }        
    
}