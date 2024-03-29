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
     * @since 5.3.0
     */
    public $aUnitOptions = '';

    /**
     * Sets up properties.
     */
    public function __construct( $bRefNosim=false, $sAssociateID='', $sLocale='US', $aUnitOptions=array() ) {
        
        $this->bRefNosim    = $bRefNosim;
        $this->sAssociateID = $sAssociateID;
        $this->sLocale      = $sLocale;
        $this->oOption      = AmazonAutoLinks_Option::getInstance();
        $this->aUnitOptions = $aUnitOptions;

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