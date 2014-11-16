<?php
/**
 * Creates Amazon product links by category.
 * 
 * @package     	Amazon Auto Links
 * @copyright   	Copyright (c) 2013, Michael Uno
 * @license     	http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @filter			aal_filter_set_url			
 * 		first parameter:	feed url
 * 		second parameter:	ad type slug
 * @filter			aal_filter_description_node
 * 		first parameter:	the description node
 * 		second parameter:	the AmazonAutoLinks_Core object
 * @action			aal_action_output_rss
 * 		first parameter: 	the AmazonAutoLinks_Core object
 * 		second paramter:	the unit option array 
 */

abstract class AmazonAutoLinks_Unit_Category_ extends AmazonAutoLinks_Unit {

	public static $arrStructure_Args = array(
		'count' => 10,
		'column' => 4,
		'country' => 'US',
		'associate_id' => null,
		'image_size' => 160,
		'sort' => 'random',	// date, title, title_descending	
		'keep_raw_title' => false,	// this is for special sorting method.
		'feed_type' => array(
			'bestsellers' => true, 
			'new-releases' => false,
			'movers-and-shakers' => false,
			'top-rated' => false,
			'most-wished-for' => false,
			'most-gifted' => false,	
		),
		'ref_nosim' => false,
		'title_length' => -1,
		'link_style' => 1,
		'credit_link' => 1,
		'categories' => array(),	
		'categories_exclude' => array(),
		'title' => '',		// won't be used to fetch links. Used to create a unit.
		'template' => '',		// the template name - if multiple templates with a same name are registered, the first found item will be used.
		'template_id' => null,	// the template ID: md5( relative dir path )
		'template_path' => '',	// the template can be specified by the template path. If this is set, the 'template' key won't take effect.
		
		'is_preview' => false,	// used to decide whether the global ASIN black/white list should be used.
		
		// The below are retrieved separately
		// 'item_format' => '',
		// 'image_format' => '',
		// 'title_format' => '',
		
		'id'	=> null,	// the unit id
		'_labels'	=> array(),	// stores labels (plugin custom taxonomy)
	);

	public static $arrStructure_Product = array(
		'thumbnail_url'	=>	null,
		'ASIN'	=> null,
		'product_url' => null,
		'raw_title' => null,
		'title' => null,
		'description' => null,	// the formatted feed item description - some elements are removed 
		'text_description' => null,	// the non-html description
		
		// Useless items
		'content' => null,		// the raw feed item content 
		'author' => null,		// the author of the product - most likely no value
		'date' => null,			// the date posted - usually it's the updated time of the feed at Amazon so it's useless.
		'category' => null,		// the category assigned to the product - most likely no value
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

		$this->arrArgs = $arrArgs + self::$arrStructure_Args + self::getItemFormatArray();
		$this->arrRSSURLs = $this->getRSSURLsFromArguments( $this->arrArgs );
		$this->arrExcludingRSSURLs = $this->getRSSURLsFromArguments( $this->arrArgs, 'categories_exclude' );
// AmazonAutoLinks_Debug::logArray( $this->arrRSSURLs );		
	}
	

	
	/**
	 * Fetches and returns the associative array containing the output of product links.
	 * 
	 * If the first parameter is not given, 
	 * it will determine the RSS urls by the post IDs from the given arguments set in the constructor.
	 * 
	 * @return			array			The array contains product information. 
	 */
	public function fetch( $arrRSSURLs=array() ) {

		$arrRSSURLs = $this->formatRSSURLs( 
			empty( $arrRSSURLs )
				? $this->arrRSSURLs
				: $arrRSSURLs			
		);
		if ( empty ( $arrRSSURLs ) ) return array();

		// Disable DOM related errors to be displayed.
		$fDOMError = libxml_use_internal_errors( true );		
		
		$arrExcludingRSSURLs = $this->formatRSSURLs( $this->arrExcludingRSSURLs );
		$oFeed_Exclude = $this->getFeedObj( $arrExcludingRSSURLs );
		$this->updateBlackASINs( $oFeed_Exclude );
		
// if ( $this->oOption->isDebugMode() )
	// AmazonAutoLinks_Debug::dumpArray( $arrRSSURLs );		
			
		$oFeed = $this->getFeedObj( $arrRSSURLs );				
		$arrProducts = $this->composeAssociativeArray( $oFeed );

		// Revert the error message setting for DOM
		libxml_use_internal_errors( $fDOMError );
		
		return $arrProducts;
	}
	
	/**
	 * Formats the given RSS urls.
	 * 
	 * - Adds associate id.
	 * - Adds the corresponding ad type's urls.
	 * 
		// [feed_type] => Array
			// (
				// [bestsellers] => 1		// 'bestsellers'
				// [hotnewreleases] => 0	// 'new-releases'
				// [moverandshakers] => 0	// 'movers-and-shakers'
				// [toprated] => 0			// 'top-rated'
				// [mostwished_for] => 0	// 'most-wished-for'
				// [giftideas] => 0			// 'most-gifted'
			// )		
					
	 * @remark			The passed urls are assumed for the bestsellers'.
	 * 
	 */
	protected function formatRSSURLs( $arrRSSURLs ) {
	
		$arrRSSURLs = is_array( $arrRSSURLs ) ? $arrRSSURLs : array( $arrRSSURLs );
		$arrFormatedURLs = array();

		foreach( $arrRSSURLs as $strRSSURL ) {
			
			foreach ( $this->arrArgs['feed_type'] as $strSlug => $fEnable ) {
				
				if ( ! $fEnable ) continue;
				$arrFormatedURLs[] = $this->formatRSSURL( str_replace( "/rss/bestsellers/", "/rss/{$strSlug}/", $strRSSURL ) );
									
			}	
			
		}
		
		return array_unique( $arrFormatedURLs );
		
	}
	protected function formatRSSURL( $strRSSURL ) {
		
		$arrURLElems = parse_url( trim( $strRSSURL ) );
		
		// If the scheme is https, Amazon returns the contents formed with SSL such as image src urls with https://.
		// However, fetching feeds via SSL makes it somewhat slow. To resolve the speed issue, 
		// it might be worth considering fetching feeds as non-SSL and convert images to SSL-compatible when rendering.
		$strScheme = $this->fIsSSL ? "https" : $arrURLElems['scheme'];
		return add_query_arg( 
			array( 'tag' => $this->arrArgs['associate_id'] ), 
			"{$strScheme}://{$arrURLElems['host']}{$arrURLElems['path']}"
		);
		
		
	}
	
	protected function getRSSURLsFromArguments( $arrArgs, $strKey='categories' ) {
		
		$arrRSSURLs = array();
		foreach( $arrArgs[ $strKey ] as $arrCategory ) 
			$arrRSSURLs[] = $arrCategory[ 'feed_url' ];
		
		return $arrRSSURLs;		
		
	}
		
	protected function updateBlackASINs( $oFeed ) {
		
		foreach ( $oFeed->get_items( 0, 0 ) as $oItem ) 
			$this->arrBlackListASINs[] = $this->getASIN( $oItem->get_permalink() );
	
	}
	
	/**
	 * Composes and returns associative array from the fetched RSS data.
	 * 
	 */
	protected function composeAssociativeArray( $oFeed ) {

		$arrProducts = array();

		foreach ( $oFeed->get_items( 0, 0 ) as $oItem ) {
						
			$arrProduct = array();
			
			// Load a DOM Object for description
			$oDoc = $this->oDOM->loadDOMFromHTMLElement( $oItem->get_description(), '' );	// passing an empty string to the second parameter disables mb_language() function to be executed.

			// The first depth div tag - If SimplePie is used outside of WordPress it should be the second depth which contains the description including images
			$nodeDiv = $oDoc->getElementsByTagName( 'div' )->item( 0 );
			if ( ! $nodeDiv ) continue;		// sometimes this happens when unavailable feed is passed, such as Top Rated, which is not supported in some countries.

			// ASIN - required to detect duplicated items.
			$strPermalink = $oItem->get_permalink();
			$arrProduct['ASIN'] = $this->getASIN( $strPermalink );	
			if ( $this->isBlocked( $arrProduct['ASIN'], 'asin' ) ) continue;
			if ( $this->arrArgs['is_preview'] || ! $this->fNoDuplicate )
				$this->arrBlackListASINs[] = $arrProduct['ASIN'];	// for mush-ups.
			else 
				$GLOBALS['arrBlackASINs'][] = $arrProduct['ASIN'];	
			
			// Product Link (hyperlinked url) - ref=nosim, linkstyle, associate id etc.
			$arrProduct['product_url'] = $this->formatProductLinkURL( $strPermalink, $arrProduct['ASIN'] );
// forgot what this was for.			
// $this->arrParsedASINs[ $arrProduct['product_url'] ] = $arrProduct['ASIN'];	
			
			// Title
			$arrProduct['raw_title'] = $oItem->get_title();
			$arrProduct['title'] = $this->sanitizeTitle( $arrProduct['raw_title'] );
			// if ( ! $arrProduct['title'] ) continue;		// The user may set the title length to 0
			if ( $this->isBlocked( $arrProduct['title'], 'title' ) ) continue;
		
			// Description ( creates $htmldescription and $textdescription ) 
			$this->oDOM->removeNodeByTagAndClass( $nodeDiv, 'span', 'riRssTitle', 0 );	// remove the span tag containing the title
			$arrProduct['text_description'] = $this->getTextDescription( $nodeDiv );
			if ( $this->isBlocked( $arrProduct['text_description'], 'description' ) ) continue;

			// Images - img tags 
			$this->formatImages( $oDoc, array( 'alt'=> $arrProduct['title'], 'title' => $arrProduct['text_description'] ) );
		
			// Thumbnail image
			$arrProduct['thumbnail_url'] = $this->getThumbnail( $oDoc, $this->arrArgs['image_size'] );

			// Links - a tags
			$this->formatLinks( 
				$nodeDiv, 
				array( 
					'rel' => 'nofollow',
					'target' => '_blank',
					'title' => $arrProduct['title'] . " - " . $arrProduct['text_description'] 
				),
				$arrProduct['ASIN']
			);
			$arrProduct['description'] = $this->getDescription( $nodeDiv );

			// Other elements - amazon does not provide the information for these but just in case.
			$arrProduct['category'] = ( $oCategory = $oItem->get_category() ) ? $oCategory->get_label() : '';	
			$arrProduct['author'] = ( $oAuthor = $oItem->get_author() ) ? $oAuthor->get_name() : '';
			$arrProduct['content'] = $oItem->get_content();
			$arrProduct['date'] = $oItem->get_date();
			
			// Format the item
			// Thumbnail
			$arrProduct['formed_thumbnail'] = str_replace( 
				array( "%href%", "%title_text%", "%src%", "%max_width%", "%description_text%" ),
				array( $arrProduct['product_url'], $arrProduct['title'], $arrProduct['thumbnail_url'], $this->arrArgs['image_size'], $arrProduct['text_description'] ),
				$this->arrArgs['image_format'] 
			);
			// Title
			$arrProduct['formed_title'] = str_replace( 
				array( "%href%", "%title_text%", "%description_text%" ),
				array( $arrProduct['product_url'], $arrProduct['title'], $arrProduct['text_description'] ),
				$this->arrArgs['title_format'] 
			);
			// Item		
			$arrProduct['formed_item'] = str_replace( 
				array( "%href%", "%title_text%", "%description_text%", "%title%", "%image%", "%description%" ),
				array( $arrProduct['product_url'], $arrProduct['title'], $arrProduct['text_description'], $arrProduct['formed_title'], $arrProduct['formed_thumbnail'], $arrProduct['description'] ),
				$this->arrArgs['item_format'] 
			);
			
			// Store the product output
			$arrProducts[] = $arrProduct + self::$arrStructure_Product;
			
			// Max Number of Items 
			if ( count( $arrProducts ) >= $this->arrArgs['count'] ) break;
					
		} 			
		
		return $arrProducts;
	}
	
	/**
	 * Converts the url scheme to https:// from http:// and uses the amazon's secure image server.
	 */
	protected function respectSSLImages( $oDoc ) {
		
		foreach ( $oDoc->getElementsByTagName( 'img' ) as $nodeImg ) 
			$nodeImg->attributes->getNamedItem( "src" )->value = $this->respectSSLImage(
				$nodeImg->attributes->getNamedItem( "src" )->value
			);	
		
	}
		
	/**
	 * Retrieves the description part from the given dom node.
	 * 
	 */
	protected function getDescription( $oNode )  {

		$oNode = apply_filters( 'aal_filter_description_node', $oNode, $this );
		
		// Add markings to the text node which later gets converted to a whitespace because by itself elements don't have white spaces between each other.
		foreach( $oNode->childNodes as $oChildNode ) {
			if ( $oChildNode->nodeType == 3 ) {		// nodeType:3 TEXT_NODE
				$oChildNode->nodeValue = '[identical_replacement_string]' . $oChildNode->nodeValue . '[identical_replacement_string]';
			}
		}
		
		// getInnerHTML extracts intter html code, meaning the outer div tag will be stripped.
		$strDescription = str_replace( '[identical_replacement_string]', '<br />', $this->oDOM->getInnerHTML( $oNode ) );
		
		// Omit the text 'visit blah blah blah for more information'
		if ( preg_match( '/<span.+class=["\']price["\'].+span>/i', $strDescription ) ) {
		
			// $arrDescription = preg_split('/<span.+class=["\']price["\'].+span>\K/i', $strDescription);  // this works above PHP v5.2.4
			$arrDescription = preg_split( '/(<span.+class=["\']price["\'].+span>)\${0}/i', $strDescription, null, PREG_SPLIT_DELIM_CAPTURE );
			
		} else {
		
			// $arrDescription = preg_split('/<font.+color=["\']#990000["\'].+font>\K/i', $strDescription);	 // this works above PHP v5.2.4
			$arrDescription = preg_split( '/(<font.+color=["\']#990000["\'].+font>)\${0}/i', $strDescription, null, PREG_SPLIT_DELIM_CAPTURE );	// " (syntax fixer )
		}	
		$strDescription1 = isset( $arrDescription[0] ) ? $arrDescription[0] : '';
		$strDescription2 = isset( $arrDescription[1] ) ? $arrDescription[1] : '';
		$strDescription = $strDescription1 . $strDescription2;
		$arrDescription = preg_split('/<br.*?\/?>/i', $strDescription);		// devide the string into arrays by <br> or <br />	
		$strDescription = trim( implode( " ", $arrDescription ) );	// return them back to html text
		return force_balance_tags( $strDescription );

	}
	
	/**
	 * Retrieves the description part from the given dom node and strips the html tags.
	 * 
	 */
	protected function getTextDescription( $oNode ) {
		
		// Divide the string into arrays by <br> or <br />
		$arrDescription = preg_split( '/<br.*?\/?>/i', $this->oDOM->getInnerHTML( $oNode ) );		
		array_splice( $arrDescription, -2 );		// remove the last two elements	
		$strHTMLDescription = implode( "&nbsp;", $arrDescription );
		return esc_attr( html_entity_decode( trim( strip_tags( $strHTMLDescription ) ), ENT_QUOTES, $this->strCharEncoding ) );		
		
	}

	
	/**
	 * Formats the links in the given DOM node.
	 * 
	 */
	protected function formatLinks( $oNode, $arrAttributes=array(), $strASIN ) {

		$arrAttributes = ( ( array ) $arrAttributes ) + array(
			'rel' => 'nofollow',
			'title' => '',
		);
	
		foreach( $oNode->getElementsByTagName( 'a' ) as $nodeA ) {
			
			$strHref = $nodeA->getAttribute( 'href' );
			if ( empty( $strHref ) ) continue;
			$strHref = $this->formatProductLinkURL( $strHref, $strASIN );

			// Reported Issue: Warning: DOMElement::setAttribute() [domelement.setattribute]: string is not in UTF-8
			$bResult = @$nodeA->setAttribute( 'href', $strHref );
			
			// if (empty($bResult)) echo "error setting the url: " . $strHref;
			foreach( $arrAttributes as $strAttr => $strProperty )
				@$nodeA->setAttribute( $strAttr, $strProperty );
		
		}
	
	}
	
	protected function formatImages( $oNode, $arrAttributes=array() ) {
		
		// Take care of SSL image urls - in SSL enabled sites, if src-image urls use non-ssl protocol, some browsers show warnings.
		if ( $this->fIsSSL ) $this->respectSSLImages( $oDoc );
		
		// For the Brazil and Mexiko locals, the element images in descriptions should be replaced as they don't load.
		if ( in_array( $this->arrArgs['country'], array( 'MX', 'BR' ) ) )
			$this->supportBrazilAndMexikoImages( $oNode );
		
		// Modify attributes
		$this->oDOM->setAttributesByTagName( $oNode, 'img', $arrAttributes );
		
	}
	
	protected function supportBrazilAndMexikoImages( $oNode ) {
	
		foreach( $oNode->getElementsByTagName( 'img' ) as $oSelectedNode )  {
			
			$strImageURL = @$oSelectedNode->getAttribute( 'src' );
			$strImageURL = str_replace(
				array( "/images/G/32/detail/", "/images/G/33/detail/" ),	// find	
				"/images/G/01/detail/",	// replace
				$strImageURL	// source
			);	
			if ( $strImageURL )
				@$oSelectedNode->setAttribute( 'src', $strImageURL );
			
		}
		
	}
	
	protected function getThumbnail( $oDoc, $numImageSize ) {
			
		$strImgURL =""; 
		if ( $numImageSize > 0 ) {			
		
			$nodeImg = $oDoc->getElementsByTagName( 'img' )->item( 0 );
			if ( $nodeImg ) {
				$strImgURL = $nodeImg->attributes->getNamedItem( "src" )->value;
				$strImgURL = $this->setImageSize( $strImgURL, $numImageSize );
				// $strImgURL = preg_replace( '/(?<=_S)([LS])(\d+){3}(?=_)/i', 'S${2}'. $numImageSize . '', $strImgURL );  // adjust the image size. _SL160_ or _SS160_
			} 
			
		}
		// removes the div tag containing the image
		foreach ( $oDoc->getElementsByTagName( 'div' ) as $nodeDivFloat ) {
			if ( stripos( $nodeDivFloat->getAttribute( 'style' ), 'float' ) !== false ) {		// if the string 'float' is found 
				$nodeDivFloat->parentNode->removeChild( $nodeDivFloat );
				break;
			}
		}
		return $strImgURL;			
		
	}
	
	
	/**
	 * Sets up SimplePie object.
	 * 
	 */
	protected function getFeedObj( $arrUrls, $numItem=10, $numCacheDuration=36000 ) {	// 60 seconds * 60 = 1 hour, 1800 = 30 minutes
		
		// Reuse the object that already exists. This conserves the memory usage.
		// $this->oFeed = isset( $this->oFeed ) ? $this->oFeed : new AmazonAutoLinks_SimplePie;
		// $oFeed = $this->oFeed; // 
		
		// For excluding sub categories, new instances need to be instantiated per set of uls as SimplePie somehow does not 
		// property output newly fetched items.
		$oFeed = new AmazonAutoLinks_SimplePie();
		
		// Set sort type.		
		$oFeed->set_sortorder( $this->arrArgs['sort'] );
		$oFeed->set_charset_for_sort( $this->strCharEncoding );
		$oFeed->set_keeprawtitle( $this->arrArgs['keep_raw_title'] );
		
		// Set urls
		$oFeed->set_feed_url( $arrUrls );	
		$oFeed->set_item_limit( $numItem );	
		
		// This should be set after defining $urls
		$oFeed->set_cache_duration( $numCacheDuration );	
		
		$oFeed->set_stupidly_fast( true );
		
		// If the cache lifetime is explicitly set to 0, do not trigger the background renewal cache event
		if ( $numCacheDuration == 0 )
			$oFeed->setBackground( true );	// setting it true will be considered the background process; thus, it won't trigger the renewal event.
		
		// set_stupidly_fast() disables this internally so turn it on manually because it will trigger the custom sort method
		$oFeed->enable_order_by_date( true );	
		
		// $oFeed->file_class = 'AmazonAutoLinks_SimplePie_File';	// this is assigned in the class definition already
		$oFeed->init();	 // will perform fetching the feeds.
		return $oFeed;
		
	}		
	
}