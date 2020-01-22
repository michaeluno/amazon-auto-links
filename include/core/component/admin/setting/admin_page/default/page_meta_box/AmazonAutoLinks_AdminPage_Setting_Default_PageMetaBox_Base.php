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
 * @since      3.4.0
 */
class AmazonAutoLinks_AdminPage_Setting_Default_PageMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
    
    /**
     * Stores the section id for the unit default option dimension.
     */
    protected $_sSectionID = 'unit_default';
      
    public function start() {
        
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
    }

    /**
     * Adds form fields.
     * @since       3.3.0
     */
    protected function _addFieldsByClasses( $aClassNames ) {
        foreach( $aClassNames as $_sClassName ) {
            $_oFields = new $_sClassName;
            foreach( $_oFields->get() as $_aField ) {
                $this->addSettingFields( $_aField );
            }
        }
    }
       
}
