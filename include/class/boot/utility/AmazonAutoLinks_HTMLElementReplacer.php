<?php
/**
    
    Replaces HTML elements
    
 * @package     Amazon Auto Links
 * @copyright   Copyright (c) 2013, Michael Uno
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        2.0.0

*/

class AmazonAutoLinks_HTMLElementReplacer {

    public $strImageQuery = 'amazon_auto_links_image';
    public $strLinkQuery = 'amazon_auto_links_link';        
    
    protected $strCharEncoding = '';    // stores the character set of the site.

    function __construct( $strCharEncoding=null ) {
    
        $this->strCharEncoding = $strCharEncoding ? $strCharEncoding : get_bloginfo( 'charset' );     
    
        $this->bImageCache = ( class_exists( 'DOMDocument' ) && function_exists( 'imagecreatefromstring' ) );
    
    }
                
    public function Perform( $strHTML ) {    
    
        // Performs replacements. 
        // replaces a tag's href values <a href="http://something"> -> <a href="http://siteurl?..._link=encodedstring">
        // replaces img tag's src values <img src="http://something" /> -> <img src="http://siteurl?..._link=encodedstring">
        
        if ( ! $this->bImageCache ) return $strHTML;    // if the server does not support necessary libraries, do not perform replacements.
        
        $strHTML = $this->ReplaceAHrefs( $strHTML, array( $this, 'ReplaceAHrefsCallback' ) ); 
        $strHTML = $this->ReplaceSRCs( $strHTML, array( $this, 'ReplaceSRCsCallback' ) );     // works for iframe and img tags.
        // $strHTML = $this->ReplaceIframeSRCs( $strHTML, array( $this, 'ReplaceIframeSRCsCallback' ) ); 
        
        return $strHTML;
        
    }
    public function ReplaceIframeSRCsCallback( $strSRC ) {

        return site_url() . "?{$this->strLinkQuery}=" . base64_encode( $strSRC );
        
    }
    public function ReplaceAHrefsCallback( $strHref ) {

        if ( stripos( $strHref, 'shareasale.com') !== false ) return null;    // returning null will discard the replacement.
            
        return site_url() . "?{$this->strLinkQuery}=" . base64_encode( $strHref );
        
    }
    public function ReplaceSRCsCallback( $strSRC ) {
        
        $strPath = parse_url( $strSRC, PHP_URL_PATH );
        $arrPathInfo = pathinfo( $strPath );

        // Iframe src values are also passed, - iframe url does not work with the redireced url so just return the given url.
        if ( ! isset(  $arrPathInfo['extension'] ) ) 
            return $strSRC;
            // return site_url() . "?{$this->strLinkQuery}=" . base64_encode( $strSRC );
                    
        // Only jpeg, jpg, png, and gif are supported. Otherwise, return the passed string, which does not perform replacement.
        if ( ! in_array( $arrPathInfo['extension'], array( 'jpeg', 'jpg', 'png', 'gif' ) ) ) 
            return $strSRC;
            // return site_url() . "?{$this->strLinkQuery}=" . base64_encode( $strSRC );
        
        return site_url() . "?{$this->strImageQuery}=" . base64_encode( $strSRC );
        
    }
    
    public function RemoveIDAttributes( $strHTML ) {    // since 1.1.1, used in the core class
        
        // $strPattern = '/\s\Qid\E=(["\'])(.*?)\1\s/i';    // '
        $strPattern = '/\s\Qid\E=(["\'])(.*?)\1(\s?)/si';    // '
        // return preg_replace( $strPattern, ' ', $strHTML );
        return preg_replace_callback(
            $strPattern,
            array( $this, 'ReturnSpace' ),
            $strHTML
        );
        
    }
    public function ReturnSpace( $arrMatches ) {    // since 1.1.1 - must be public as it is a callback method.
        
        // Callback for the above RemoveIDAttributes() method.
        if ( isset( $arrMatches[3] ) && empty( $arrMatches[3] ) )
            return '';        // if it's ending with >.
        return ' ';    // return a white-space.
        
    }
    public function GetAttributeReplacementArrayWithRegex( $strHTML, $strAttribute, $vReplaceCallbackFunc, $vParam=null ) {    // must be public as it is used by the instantiated objecct in the core class.

        // Make sure the string is long enough to be replaced with str_replace(); if the replacing string is too short,
        // it will match other unexpected block strings.
        // For more accurate performance, use preg_replace_callback()
        
        $arrReplacements = array( 
            'search' => array(), 
            'replace' => array(),
        );        
        
        $intCount = preg_match_all( '/\s\Q' . $strAttribute . '\E=(["\'])(.*?)\1\s?/i', $strHTML, $arrMatches );    //'
        
        $bIsCallable = is_callable( $vReplaceCallbackFunc );

        $i = 0;
        While ( $i < $intCount ) {
            
            $strAttr = $arrMatches[2][ $i++ ];
            $strReplace = $bIsCallable ? call_user_func_array( $vReplaceCallbackFunc , array( &$strAttr, $vParam ) ) : $strAttr;

            // if the callback function returns null explicitly, let it not add a replacement at all.
            if ( is_null( $strReplace ) ) continue;
            
            // Add the elements.
            $arrReplacements['replace'][] = $strReplace;
            $arrReplacements['search'][] = $strAttr;
            
        }
        
        return $arrReplacements;
        
    }
    protected function GetAttributeReplacementArrayWithDOM( $nodes, $strAttribute, $vReplaceCallbackFunc ) {
        
        $arrReplacements = array( 
            'search' => array(), 
            'replace' => array(),
        );
        
        foreach( $nodes as $node ){
            
            $strAttr = $node->getAttribute( $strAttribute );
            $strReplacement = is_callable( $vReplaceCallbackFunc ) ? call_user_func_array( $vReplaceCallbackFunc , array( &$strAttr ) ) : $strAttr;

// if ( $strAttribute == 'src' ) {
    // echo '<pre>' 
        // . $strReplacement . '<br />'
        // . $strAttr 
        // . '</pre>';
// }
            
            if ( $strAttr == $strReplacement ) continue;    // if the replacement is the same, no need to add it to the array.
            
            $arrReplacements['search'][] = $strAttr;
            $arrReplacements['replace'][] = $strReplacement;
                
        }            
                
        return $arrReplacements;
        
    }    
    protected function ReplaceSRCs( $strHTML, $vCallback ) {
        
        $arrReplacements = $this->GetAttributeReplacementArrayWithRegex( $strHTML, 'src', $vCallback );
        return str_replace( $arrReplacements['search'], $arrReplacements['replace'], $strHTML );

    }    
    protected function ReplaceAHrefs( $strHTML, $vCallback ) {

        $arrReplacements = $this->GetAttributeReplacementArrayWithRegex( $strHTML, 'href', $vCallback );
        return str_replace( $arrReplacements['search'], $arrReplacements['replace'], $strHTML );
        
    }
    protected function ReplaceAHrefsWithDOM( $strHTML, $vCallback ) {

        $bErrorFlag = libxml_use_internal_errors( true );
        $oDOM = $this->LoadDomFromHTML( $strHTML );
        $nodeAs = $oDOM->getElementsByTagName( 'a' );
        $arrReplacements = $this->GetAttributeReplacementArrayWithDOM( $nodeAs, 'href', $vCallback );
        $strHTML = str_replace( $arrReplacements['search'], $arrReplacements['replace'], $strHTML );
        libxml_use_internal_errors( $bErrorFlag );        
        return $strHTML;            
        
    }
    protected function ReplaceIframeSRCsWithDOM( $strHTML, $vCallback ) {
        
        $bErrorFlag = libxml_use_internal_errors( true );
        
        $oDOM = $this->LoadDomFromHTML( $strHTML );
        $nodeIframe = $oDOM->getElementsByTagName( 'iframe' );
        $arrReplacements = $this->GetAttributeReplacementArrayWithDOM( $nodeIframe, 'src', $vCallback );
        $strHTML = str_replace( $arrReplacements['search'], $arrReplacements['replace'], $strHTML );

        libxml_use_internal_errors( $bErrorFlag );
        
        return $strHTML;
        
    }
    protected function ReplaceIMGSRCsWithDOM( $strHTML, $vCallback ) {    
        
        $bErrorFlag = libxml_use_internal_errors( true );
        
        $oDOM = $this->LoadDomFromHTML( $strHTML );
        $nodeImgs = $oDOM->getElementsByTagName( 'img' );
        $arrReplacements = $this->GetAttributeReplacementArrayWithDOM( $nodeImgs, 'src', $vCallback );
        $strHTML = str_replace( $arrReplacements['search'], $arrReplacements['replace'], $strHTML );

        libxml_use_internal_errors( $bErrorFlag );
        
        return $strHTML;
        
    }
    public function LoadDomFromHTML( $strHTML ) {
        
        // $oDOM = new DOMDocument( '1.0', $this->strCharEncoding );
        $oDOM = new DOMDocument( '1.0' );
        // $oDOM->preserveWhiteSpace = false;
        // $oDOM->formatOutput = true;    
        // $strHTML = '<div>' . $strHTML . '</div>';        // this prevents later when using saveXML() from inserting the comment <!-- xml version .... -->
        $oDOM->loadhtml( $strHTML );
        return $oDOM;
        
    }    
    public function ReplaceLinks( $strHTML ) {    // since 1.1.0
        
        return $strHTML;
    }
    
}