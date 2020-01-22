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
 * Formats amazon product link urls.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_Output_Format_LinksStyle_Base extends AmazonAutoLinks_WPUtility {

    public $bRefNosim    = false;
    public $sAssociateID = '';
    public $sLocale      = 'US';

    public $oOption;
    
    /**
     * Sets up properties.
     */
    public function __construct( $bRefNosim=false, $sAssociateID='', $sLocale='US' ) {
        
        $this->bRefNosim    = $bRefNosim;
        $this->sAssociateID = $sAssociateID;
        $this->sLocale      = $sLocale;
        
        $this->oOption      = AmazonAutoLinks_Option::getInstance();
        
    }
    
    /**
     * Returns a formatted url.
     * @remark      Override this method in an extended class.
     * @return      string
     */
    public function get( $sURL, $sASIN, $sLanguageCode='', $sCurrency='' ) {
        return trim( $sURL );
    }
    
    /**
     * @remark      for supporting the developer.
     * @return      string
     * @since       3
     * @since       3.8.10      Removed supported tags.
     */
    protected function getAssociateID() {
        return $this->sAssociateID;
    }       
   
}