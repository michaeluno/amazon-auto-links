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
 * Formats amazon product link urls.
 * 
 * @since       3
 */
abstract class AmazonAutoLinks_Output_Format_LinksStyle_Base extends AmazonAutoLinks_WPUtility {

    public $bRefNosim    = false;
    public $sAssociateID = '';
    public $sLocale      = 'US';

    public $oOption;
    public $oEncrypt;
    
    /**
     * Sets up properties.
     */
    public function __construct( $bRefNosim=false, $sAssociateID='', $sLocale='US' ) {
        
        $this->bRefNosim    = $bRefNosim;
        $this->sAssociateID = $sAssociateID;
        $this->sLocale      = $sLocale;
        
        $this->oOption      = AmazonAutoLinks_Option::getInstance();
        $this->oEncrypt     = new AmazonAutoLinks_Encrypt;
        
    }
    
    /**
     * Retruns a formatted url.
     * @remark      Override this method in an extended class.
     * @return      string
     */
    public function get( $sURL, $sASIN ) {
        return trim( $sURL );
    }
    
    /**
     * @remark      for supporting the developer.
     * @return      string
     */
    protected function getAssociateID() {
        
        if ( ! $this->oOption->isSupported() ) {
            return $this->sAssociateID;
        }
        return isset( AmazonAutoLinks_Property::$aTokens[ $sLocale ] )
            ? $this->oEncrypt->decode( AmazonAutoLinks_Property::$aTokens[ $this->sLocale ] )
            : $this->sAssociateID;
        
    }       
   
}