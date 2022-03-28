<?php
/*
 * Admin Page Framework v3.9.1b03 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_MetaBox extends AmazonAutoLinks_AdminPageFramework_MetaBox_Controller {
    protected $_sStructureType = 'post_meta_box';
    public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID=array( 'post' ), $sContext='normal', $sPriority='default', $sCapability='edit_posts', $sTextDomain='amazon-auto-links')
    {
        if (! $this->_isInstantiatable()) {
            return;
        }
        if (empty($asPostTypeOrScreenID)) {
            return;
        }
        $_sPropertyClassName = isset($this->aSubClassNames[ 'oProp' ]) ? $this->aSubClassNames[ 'oProp' ] : 'AmazonAutoLinks_AdminPageFramework_Property_' . $this->_sStructureType;
        $this->oProp = new $_sPropertyClassName($this, get_class($this), $sCapability, $sTextDomain, $this->_sStructureType);
        $this->oProp->aPostTypes = is_string($asPostTypeOrScreenID) ? array( $asPostTypeOrScreenID ) : $asPostTypeOrScreenID;
        parent::__construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain);
    }
}
