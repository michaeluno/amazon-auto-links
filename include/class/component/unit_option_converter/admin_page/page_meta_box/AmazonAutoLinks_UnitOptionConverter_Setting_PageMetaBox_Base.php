<?php
/**
 * Amazon Auto Links
 * 
 * http://en.michaeluno.jp/amazon auto links/
 * Copyright (c) 2013-2016 Michael Uno
 * 
 */
 
 
class AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Base extends AmazonAutoLinks_AdminPageFramework_PageMetaBox {
      
    public function start() {
        
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
        
    }
       
}