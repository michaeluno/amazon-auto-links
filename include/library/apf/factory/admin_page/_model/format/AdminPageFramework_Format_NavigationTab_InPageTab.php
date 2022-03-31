<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Format_NavigationTab_InPageTab extends AmazonAutoLinks_AdminPageFramework_Format_Base {
    public static $aStructure = array();
    public $aTab = array();
    public $aTabs = array();
    public $aArguments = array();
    public $oFactory = array();
    public function __construct()
    {
        $_aParameters = func_get_args() + array( $this->aTab, self::$aStructure, $this->aTabs, $this->aArguments, $this->oFactory, );
        $this->aTab = $_aParameters[ 0 ];
        self::$aStructure = $_aParameters[ 1 ];
        $this->aTabs = $_aParameters[ 2 ];
        $this->aArguments = $_aParameters[ 3 ];
        $this->oFactory = $_aParameters[ 4 ];
    }
    public function get()
    {
        $_aTab = $this->uniteArrays($this->aTab, array( 'capability' => 'manage_options', 'show_in_page_tab' => true, ));
        if (! $this->_isEnabled($_aTab)) {
            return array();
        }
        $_sSlug = $this->_getSlug($_aTab);
        $_aTab = array( 'slug' => $_sSlug, 'title' => $this->aTabs[ $_sSlug ][ 'title' ], 'href' => $_aTab[ 'disabled' ] ? null : esc_url($this->getElement($_aTab, 'url', $this->getQueryAdminURL(array( 'page' => $this->aArguments[ 'page_slug' ], 'tab' => $_sSlug, ), $this->oFactory->oProp->aDisallowedQueryKeys))), ) + $this->uniteArrays($_aTab, array( 'attributes' => array( 'data-tab-slug' => $_sSlug, ), ), self::$aStructure);
        return $_aTab;
    }
    private function _isEnabled($aTab)
    {
        return ! in_array(false, array( ( bool ) current_user_can($aTab[ 'capability' ]), ( bool ) $aTab[ 'show_in_page_tab' ], ( bool ) $aTab[ 'if' ], ));
    }
    private function _getSlug($aTab)
    {
        return isset($aTab[ 'parent_tab_slug' ], $this->aTabs[ $aTab[ 'parent_tab_slug' ] ]) ? $aTab[ 'parent_tab_slug' ] : $aTab[ 'tab_slug' ];
    }
}
