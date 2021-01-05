<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * http://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */
 

/**
 * @since      3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base extends AmazonAutoLinks_PageMetaBox_Base {
    
    /**
     * Stores the section id for the unit default option dimension.
     */
    protected $_sSectionID = 'unit_default';
      
    public function start() {
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
    }

}