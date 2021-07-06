<?php
/**
 * Amazon Auto Links
 *
 * Generates links of Amazon products just coming out today. You just pick categories and they appear even in JavaScript disabled browsers.
 *
 * https://en.michaeluno.jp/amazon-auto-links/
 * Copyright (c) 2013-2021 Michael Uno
 */
 
 
class AmazonAutoLinks_UnitOptionConverter_Setting_PageMetaBox_Base extends AmazonAutoLinks_PageMetaBox_Base {
      
    public function start() {
        new AmazonAutoLinks_RevealerCustomFieldType( $this->oProp->sClassName );
    }

}