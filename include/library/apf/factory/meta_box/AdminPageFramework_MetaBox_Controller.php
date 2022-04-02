<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Controller extends AmazonAutoLinks_AdminPageFramework_MetaBox_View {
    public function setUp()
    {}
    public function enqueueStyles()
    {
        $_aParams = func_get_args() + array( array(), array(), array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'aPostTypes' => empty($_aParams[ 1 ]) ? $this->oProp->aPostTypes : $_aParams[ 1 ], ) + $_aParams[ 2 ], 'style');
    }
    public function enqueueStyle()
    {
        $_aParams = func_get_args() + array( '', array(), array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'aPostTypes' => empty($_aParams[ 1 ]) ? $this->oProp->aPostTypes : $_aParams[ 1 ], ) + $_aParams[ 2 ], 'style');
    }
    public function enqueueScripts()
    {
        $_aParams = func_get_args() + array( array(), array(), array() );
        return $this->oResource->_enqueueResourcesByType($_aParams[ 0 ], array( 'aPostTypes' => empty($_aParams[ 1 ]) ? $this->oProp->aPostTypes : $_aParams[ 1 ], ) + $_aParams[ 2 ], 'script');
    }
    public function enqueueScript()
    {
        $_aParams = func_get_args() + array( '', array(), array() );
        return $this->oResource->_addEnqueuingResourceByType($_aParams[ 0 ], array( 'aPostTypes' => empty($_aParams[ 1 ]) ? $this->oProp->aPostTypes : $_aParams[ 1 ], ) + $_aParams[ 2 ], 'script');
    }
}
