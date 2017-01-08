<?php
/**
 * A base class for unit classes, search, tag, and category.
 * 
 * Provides shared methods and properties for those classes.
 * 
 * @package         Amazon Auto Links
 * @copyright       Copyright (c) 2013, Michael Uno
 * @license         http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * 
 * @filter            aal_filter_template_path
 *         first parameter:    (string) template path
 *         second parameter:    (array) arguments(unit options) 
 * @filter            aal_filter_unit_output
 *         first parameter:    (string) unit output
 *         second parameter:    (array)    arguments(unit options)
 */

abstract class AmazonAutoLinks_Unit {

    function __construct() {
            
        $this->strCharEncoding = $GLOBALS['oAmazonAutoLinks_Option']->strCharEncoding;
        $this->fIsSSL = is_ssl();        
        $this->oOption = $GLOBALS['oAmazonAutoLinks_Option'];
        $this->oDOM = new AmazonAutoLinks_DOM;
            
        $this->setBlackWhiteLists();    
        
    }
    
    /**
     * The arrays contains the concatenation character(.) so it cannot be done in the declaration.
     */
    static public function getItemFormatArray() {
        
        return array(
            'item_format' => '%image%' . PHP_EOL    // since the 
                . '%title%' . PHP_EOL
                . '%description%',
            'image_format' => '<div class="amazon-product-thumbnail" style="max-width:%max_width%px;">' . PHP_EOL
                . '    <a href="%href%" title="%title_text%: %description_text%" rel="nofollow" target="_blank">' . PHP_EOL 
                . '        <img src="%src%" alt="%description_text%" />' . PHP_EOL
                . '    </a>' . PHP_EOL
                . '</div>',
            'title_format' => '<h5 class="amazon-product-title">' . PHP_EOL
                . '<a href="%href%" title="%title_text%: %description_text%" rel="nofollow" target="_blank">%title_text%</a>' . PHP_EOL 
                . '</h5>',    
        );
        
    }
    
    /**
     * Sets up black and white lists property array from the stored option values.
     * 
     */
    protected function setBlackWhiteLists() {
        
        $arrBlackWhiteLists = array();
        foreach( $this->oOption->arrOptions['aal_settings']['product_filters'] as $strSection => $arrSection ) {
            if ( $strSection == 'case_sensitive' ) {
                $this->fBWListCaseSensitive = $arrSection;
                continue;
            }
            if ( $strSection == 'no_duplicate' ) {
                $this->fNoDuplicate = $arrSection;
                continue;
            }
            if ( is_array( $arrSection ) ) {
                foreach( $arrSection as $strArea => $strUserInput ) {
                    $arrBlackWhiteLists[ $strSection ][ $strArea ] = AmazonAutoLinks_Utilities::convertStringToArray( $strUserInput, ',' );
                }
            } 
        }
        
        $this->arrBlackListASINs = $arrBlackWhiteLists['black_list']['asin'];
        $this->arrBlackListTitles = $arrBlackWhiteLists['black_list']['title'];
        $this->arrBlackListDescriptions = $arrBlackWhiteLists['black_list']['description'];                    
        $this->arrWhiteListASINs = $arrBlackWhiteLists['white_list']['asin'];
        $this->arrWhiteListTitles = $arrBlackWhiteLists['white_list']['title'];
        $this->arrWhiteListDescriptions = $arrBlackWhiteLists['white_list']['description'];        
        
    }
    
    /**
     * Checks whether the given item is blocked by the user.
     * 
     * 
     */
    protected function isBlocked( $strString, $strType ) {

        if ( ! is_string( $strString ) && ! is_integer( $strString ) ) { return false; }
    
        switch ( strtolower( $strType ) ) {
            default:
            case 'asin': 
                $arrBlackList = $this->arrArgs['is_preview'] ? $this->arrBlackListASINs : $GLOBALS['arrBlackASINs'];
                $arrWhiteList = $this->arrWhiteListASINs;
                break;
            case 'title':     
                $arrBlackList = $this->arrBlackListTitles;
                $arrWhiteList = $this->arrWhiteListTitles;
                break;
            case 'description':
                $arrBlackList = $this->arrBlackListDescriptions;
                $arrWhiteList = $this->arrWhiteListDescriptions;    
                break;
        }
    
        if ( ! empty( $arrWhiteList ) ) {            
            if ( ! $this->isWhiteListed( $strString, $arrWhiteList ) ) {
                return true;
            }
        }
        if ( $this->isBlackListed( $strString, $arrBlackList ) ) { 
            return true; 
        }
        return false;
        
    }
    
    /**
     * Checks whether the given string is white-listed.
     * 
     * This should not be performed if any white-list item is not set.
     * 
     */
    protected function isWhiteListed( $strString, $arrWhiteList ) {
        
        $strFunc = $this->fBWListCaseSensitive ? 'strpos' : 'stripos';
        
        foreach( $arrWhiteList as $strWhiteStringFindMe ) {            
            if ( call_user_func_array( $strFunc, array( $strString , $strWhiteStringFindMe ) ) !== false ) {
                return true;
            }
        }
                
        return false;
        
    }
    
    /**
     * Checks whether the given string is black-listed.
     * 
     */
    protected function isBlackListed( $strString, $arrBlackList ) {
        
        $strFunc = $this->fBWListCaseSensitive ? 'strpos' : 'stripos';
        foreach( $arrBlackList as $strBlackStringFindMe ) {            
            if ( call_user_func_array( $strFunc, array( $strString , $strBlackStringFindMe ) ) !== false ) {
                return true;        
            }
        }
                
        return false;
        
    }

    /**
     * Strips tags and truncates the given string.
     * 
     */
    protected function sanitizeDescription( $strDescription, $numMaxLength=null, $strReadMoreURL='' ) {
        
        $strDescription = strip_tags( $strDescription );
        
        // Title character length
        $numMaxLength = $numMaxLength ? $numMaxLength : $this->arrArgs['description_length'];
        if ( $numMaxLength == 0 ) { return ''; }
        
        $strDescription = ( $numMaxLength > 0 && AmazonAutoLinks_Utilities::getStringLength( $strDescription ) > $numMaxLength )
            ? esc_attr( AmazonAutoLinks_Utilities::getSubstring( $strDescription, 0, $numMaxLength ) ) . '...'
                . ( $strReadMoreURL 
                    ? " <a href='{$strReadMoreURL}' target='_blank' rel='nofollow'>" . __( 'read more', 'amazon-auto-links' ) . "</a>"
                    : ''
                )
            : esc_attr( $strDescription );
        
        return $strDescription;
        
        
    }
    
    /**
     * Strips HTML tags and sanitizes the product title.
     * 
     */
    protected function sanitizeTitle( $strTitle ) {

        $strTitle = strip_tags( $strTitle );

        // removes the heading numbering. e.g. #3: Product Name -> Product Name
        // Do not use "substr($strTitle, strpos($strTitle, ' '))" since some title contains double-quotes and they mess up html formats
        $strTitle = trim( preg_replace('/#\d+?:\s+?/i', '', $strTitle ) );
        
        // Title character length
        if ( $this->arrArgs['title_length'] == 0 ) {
            return '';
        }
        if ( $this->arrArgs['title_length'] > 0 && AmazonAutoLinks_Utilities::getStringLength( $strTitle ) > $this->arrArgs['title_length'] ) {            
            $strTitle = AmazonAutoLinks_Utilities::getSubstring( $strTitle, 0, $this->arrArgs['title_length'] ) . '...';
        }
        
        // return $strTitle;
        return esc_attr( $strTitle );

    }
        
    /**
     * Extracts ASIN from the given url. 
     * 
     * ASIN is a product ID consisting of 10 characters.
     * 
     * example regex patterns:
     *         /http:\/\/(?:www\.|)amazon\.com\/(?:gp\/product|[^\/]+\/dp|dp)\/([^\/]+)/
     *         "http://www.amazon.com/([\\w-]+/)?(dp|gp/product)/(\\w+/)?(\\w{10})"
     */
    protected function getASIN( $strURL ) {
    
        preg_match( '/(dp|gp|e)\/(.+\/)?(\w{10})(\/|$|\?)/i', $strURL, $arrMatches );    // \w{10} is the ASIN
        return isset( $arrMatches[ 3 ] ) ? $arrMatches[ 3 ] : "";    // if not found, it returns an empty string
        
    }
    
    /**
     * Returns the resized image url.
     */
    protected function setImageSize( $strImgURL, $numImageSize ) {
        
        // adjust the image size. _SL160_ or _SS160_
        return preg_replace( '/(?<=_S)([LS])(\d+){3}(?=_)/i', 'S${2}'. $numImageSize . '', $strImgURL );  
        
    }    
    
    /**
     * Returns the url using the amazon SSL image server.
     * 
     */
    protected function respectSSLImage( $strImgURL ) {
        return preg_replace(
            "/^http:\/\/.+?\//", 
            "https://images-na.ssl-images-amazon.com/", 
            $strImgURL
        );
    }
    
    /**
     * Formats the given url such as adding associate ID, ref=nosim, and link style.
     *  
     */
    protected function formatProductLinkURL( $strURL, $strASIN ) {

        $sStyledURL = $this->styleProductLink( 
            $strURL, 
            $strASIN, 
            $this->arrArgs['link_style'], 
            $this->arrArgs['ref_nosim'], 
            $this->arrArgs['associate_id'], 
            $this->arrArgs['country']
        );
        // return $sStyledURL;
        return esc_url( $sStyledURL );
            
    }
    
    /**
     * A helper function for the above formatProductLinkURL() method.
     * 
     * $numStyle should be 1 to 5 indicating the url style of the link.
     *
     */
    protected function styleProductLink( $strURL, $strASIN, $numStyle=1, $fRefNosim=false, $strAssociateID='', $strLocale='US' ) {

        switch ( $numStyle ) {
            default:
            case 1: // //www.amazon.[domain-suffix]/[product-name]/dp/[asin]/ref=[...]?tag=[associate-id]

                if ( ! empty( $fRefNosim ) ) // ref=nosim
                    $strURL = preg_replace( '/ref\=(.+?)(\?|$)/i', 'ref=nosim$2', $strURL );
                    
                // http://.../ref=pd_zg_rss_ts_bt_beauty_8?ie=UTF8&amp;tag=miunosoft-20 -> http://.../ref=pd_zg_rss_ts_bt_beauty_8?ie=UTF8&tag=miunosoft-20 ->                 
                $strURL = htmlspecialchars_decode( $strURL );
                return add_query_arg( array( 'tag' => $this->supportDeveloper( $strAssociateID, $strLocale ) ), $strURL );
                
            case 2: // http://www.amazon.[domain-suffix]/exec/obidos/ASIN/[asin]/[associate-id]/ref=[...]
                
                $arrURLelem = parse_url( $strURL );                
                return $arrURLelem['scheme'] . '://' . $arrURLelem['host'] 
                    . '/exec/obidos/ASIN/' . $strASIN 
                    . '/' . $this->supportDeveloper( $strAssociateID, $strLocale ) 
                    . ( empty( $fRefNosim ) ? '' : '/ref=nosim' );    // ref=nosim
                
            case 3:    // http://www.amazon.[domain-suffix]/gp/product/[asin]/?tag=[associate-id]&ref=[...]

                $arrURLelem = parse_url( $strURL );
                $arrQueries = array( 'tag' => $this->supportDeveloper( $strAssociateID, $strLocale ) );
                if ( $fRefNosim ) $arrQueries['ref'] = 'nosim';
                return add_query_arg( 
                    $arrQueries,
                    $arrURLelem['scheme'] . '://' . $arrURLelem['host'] . '/gp/product/' . $strASIN 
                );
                                
            case 4:    // http://www.amazon.[domain-suffix]/dp/ASIN/[asin]/ref=[...]?tag=[associate-id]

                $arrURLelem = parse_url( $strURL );
                return add_query_arg( 
                    array( 'tag' => $this->supportDeveloper( $strAssociateID, $strLocale ) ),
                    $arrURLelem['scheme'] . '://' . $arrURLelem['host'] . '/dp/ASIN/' . $strASIN . '/' . ( empty( $fRefNosim ) ? '' : 'ref=nosim' )
                );
                
            case 5:    // http://[yoursite]?[costom_query_key]=[ASIN]

                $strQueryKey = $GLOBALS['oAmazonAutoLinks_Option']->arrOptions['aal_settings']['query']['cloak'];
                $arrQueries = array( 
                    $strQueryKey => $strASIN,
                    'locale' => $strLocale,
                    'ref' => 'nosim',
                    'tag' => $this->supportDeveloper( $strAssociateID, $strLocale ),
                );
                if ( ! $fRefNosim ) unset( $arrQueries['ref'] );
                return add_query_arg( $arrQueries, site_url() );
                
        }

    }
    protected function supportDeveloper( $strAssociateID, $strLocale ) {
        
        $oEncrypt = new AmazonAutoLinks_Encrypt;
        $strDevID = isset( AmazonAutoLinks_Properties::$arrTokens[ $strLocale ] )
            ? $oEncrypt->decode( AmazonAutoLinks_Properties::$arrTokens[ $strLocale ] )
            : $strAssociateID;
        
        return $this->oOption->isSupported()
            ? $strDevID
            : $strAssociateID;
        
    }    
    
    /**
     * Finds the template path from the given arguments(unit options).
     * 
     * The keys that can determine the template path are template, template_id, template_path.
     * 
     * The template_id key is automatically assigned when creating a unit. If the template_path is explicitly set and the file exists, it will be used.
     * 
     * The template key is a user friendly one and it should point to the name of the template. If multiple names exist, the first item will be used.
     * 
     */
    protected function getTemplatePath( $arrArgs ) {
        
        if ( isset( $arrArgs[ 'template_path' ] ) && file_exists( $arrArgs[ 'template_path' ] ) )
            return $arrArgs[ 'template_path' ];
            
        $oTemplate = $GLOBALS['oAmazonAutoLinks_Templates'];
        if ( isset( $arrArgs[ 'template' ] ) && $arrArgs[ 'template' ] ) 
            foreach( $oTemplate->getActiveTemplates() as $arrTemplate ) 
                if ( strtolower( $arrTemplate[ 'strName' ] ) == strtolower( trim( $arrArgs[ 'template' ] ) ) )
                    return $arrTemplate['strTemplatePath'];
                            
        if ( isset( $arrArgs[ 'template_id' ] ) && $arrArgs[ 'template_id' ] )
            foreach( $oTemplate->getActiveTemplates() as $strID => $arrTemplate ) 
                if ( $strID == trim( $arrArgs[ 'template_id' ] ) )
                    return $arrTemplate[ 'strTemplatePath' ];        
        
        // Not found. In that case use the default one.
        $arrDefaultTemplate = $oTemplate->getPluginDefaultTemplate( $this->strUnitType );
        return $arrDefaultTemplate[ 'strTemplatePath' ];
    }
    
    /**
     * Gets the output of product links by specifying a template.
     * 
     */
    public function getOutput( $arrURLs=array(), $strTemplatePath=null ) {
        
        $arrOptions = $this->oOption->arrOptions; 
        $arrProducts = $this->fetch( $arrURLs );

        $strTemplatePath = apply_filters( 
            "aal_filter_template_path", 
            isset( $strTemplatePath ) ? $strTemplatePath : $this->getTemplatePath( $this->arrArgs ), 
            $this->arrArgs 
        );
        $strTemplatePath ? $strTemplatePath : AmazonAutoLinks_Commons::$strPluginDirPath . '/template/preview/template.php';
        
        // Capture the output buffer
        ob_start(); // start buffer
        $arrArgs = $this->arrArgs;    // this lets the template file to access the local $arrArgs variable.
        if ( file_exists( $strTemplatePath ) ) {
            include( $strTemplatePath );    // not include_once() as it may be called many times.
        } else {
            echo '<p>' . AmazonAutoLinks_Commons::Name . ': ' . __( 'the template could not be found. Try reselecting the template in the unit option page.', 'amazon-auto-links' ) . '</p>';
        }
        $strContent = ob_get_contents(); // assign the content buffer to a variable
        ob_end_clean(); // end and remove the buffer        
        
        return apply_filters( "aal_filter_unit_output", $strContent . $this->getCredit( $this->arrArgs['credit_link'] ), $arrArgs );
        
    }        
        /**
         * @return      string
         */
        protected function getCredit( $bEnabled=true ) {
            
            $_sQueryKey  = $GLOBALS[ 'oAmazonAutoLinks_Option' ]->arrOptions[ 'aal_settings' ][ 'query' ][ 'cloak' ];
            $_sVendorURL = site_url( "?{$_sQueryKey}=vendor" );
            return "<!-- Rendered with Amazon Auto Links by miunosoft -->"
                . ( $bEnabled
                    ? "<span class='amazon-auto-links-credit'>"
                            . "by <a href='" . esc_url( $_sVendorURL ) . "' rel='author' title='" . esc_attr( AmazonAutoLinks_Commons::$strPluginDescription ) . "'>"
                                . AmazonAutoLinks_Commons::Name
                            . "</a>"
                        . "</span>"
                    : "" 
                );
            
        }
        
    /**
     * Renders the product links.
     * 
     */
    public function render( $arrURLs=array() ) {
        echo $this->getOutput( $arrURLs );
    }

            
    // should be extended and must return an array.
    public function fetch( $arrURLs ) { return array(); }
    
}