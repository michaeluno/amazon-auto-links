<?php
/*
 * Admin Page Framework v3.9.1 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_View__PageMetaboxEnabler extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory)
    {
        $this->oFactory = $oFactory;
        add_action('admin_head', array( $this, '_replyToEnableMetaBox' ));
    }
    public function _replyToEnableMetaBox()
    {
        if (! $this->_isMetaBoxAdded()) {
            return;
        }
        $_sCurrentScreenID = $this->getCurrentScreenID();
        do_action("add_meta_boxes_{$_sCurrentScreenID}", null);
        do_action('add_meta_boxes', $_sCurrentScreenID, null);
        wp_enqueue_script('postbox');
        $_iColumns = $this->getAOrB($this->doesMetaBoxExist('side'), 2, 1);
        add_screen_option('layout_columns', array( 'max' => $_iColumns, 'default' => $_iColumns, ));
        wp_nonce_field('meta-box-order', 'meta-box-order-nonce', false);
        wp_nonce_field('closedpostboxes', 'closedpostboxesnonce', false);
        if (isset($GLOBALS[ 'page_hook' ])) {
            add_action("admin_footer-{$GLOBALS['page_hook']}", array( $this, '_replyToAddMetaboxScript' ));
        }
    }
    private function _isMetaBoxAdded()
    {
        $_aPageMetaBoxClasses = $this->getElementAsArray($GLOBALS, array( 'aAmazonAutoLinks_AdminPageFramework', 'aMetaBoxForPagesClasses' ));
        if (empty($_aPageMetaBoxClasses)) {
            return false;
        }
        $_sPageSlug = $this->getHTTPQueryGET('page', '');
        if (! $_sPageSlug) {
            return false;
        }
        foreach ($_aPageMetaBoxClasses as $_sClassName => $_oMetaBox) {
            if ($this->_isPageOfMetaBox($_sPageSlug, $_oMetaBox)) {
                return true;
            }
        }
        return false;
    }
    private function _isPageOfMetaBox($sPageSlug, $oMetaBox)
    {
        if (in_array($sPageSlug, $oMetaBox->oProp->aPageSlugs)) {
            return true;
        }
        if (! array_key_exists($sPageSlug, $oMetaBox->oProp->aPageSlugs)) {
            return false;
        }
        $_aTabs = $oMetaBox->oProp->aPageSlugs[ $sPageSlug ];
        $_sCurrentTabSlug = $this->oFactory->oProp->getCurrentTabSlug();
        return ($_sCurrentTabSlug && in_array($_sCurrentTabSlug, $_aTabs));
    }
    public function _replyToAddMetaboxScript()
    {
        $_bLoaded = $this->getElement($GLOBALS, array( 'aAmazonAutoLinks_AdminPageFramework', 'bAddedMetaBoxScript' ), false);
        if ($_bLoaded) {
            return;
        }
        $GLOBALS[ 'aAmazonAutoLinks_AdminPageFramework' ][ 'bAddedMetaBoxScript' ] = true;
        $_sScript = <<<JAVASCRIPTS
jQuery(document).ready(function(){postboxes.add_postbox_toggles(pagenow)})
JAVASCRIPTS;
        echo '<script class="amazon-auto-links-insert-metabox-script">' . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . '</script>';
    }
}
