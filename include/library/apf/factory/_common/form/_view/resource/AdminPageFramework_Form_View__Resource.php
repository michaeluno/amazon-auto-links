<?php 
/**
	Admin Page Framework v3.8.21b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2019, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Form_View__Resource extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oForm;
    public function __construct($oForm) {
        $this->oForm = $oForm;
        if ($this->isDoingAjax()) {
            return;
        }
        if ($this->hasBeenCalled('resource_' . $oForm->aArguments['caller_id'])) {
            return;
        }
        $this->_setHooks();
    }
    private function _setHooks() {
        if (is_admin()) {
            $this->_setAdminHooks();
            return;
        }
        add_action('wp_enqueue_scripts', array($this, '_replyToEnqueueScripts'));
        add_action('wp_enqueue_scripts', array($this, '_replyToEnqueueStyles'));
        add_action(did_action('wp_print_styles') ? 'wp_print_footer_scripts' : 'wp_print_styles', array($this, '_replyToAddStyle'), 999);
        add_action('wp_footer', array($this, '_replyToEnqueueScripts'));
        add_action('wp_footer', array($this, '_replyToEnqueueStyles'));
        add_action('wp_print_footer_scripts', array($this, '_replyToAddStyle'), 999);
        add_action('wp_print_footer_scripts', array($this, '_replyToAddScript'), 999);
        new AmazonAutoLinks_AdminPageFramework_Form_View__Resource__Head($this->oForm, 'wp_head');
    }
    private function _setAdminHooks() {
        add_action('admin_enqueue_scripts', array($this, '_replyToEnqueueScripts'));
        add_action('admin_enqueue_scripts', array($this, '_replyToEnqueueStyles'));
        add_action(did_action('admin_print_styles') ? 'admin_print_footer_scripts' : 'admin_print_styles', array($this, '_replyToAddStyle'), 999);
        add_action('customize_controls_print_footer_scripts', array($this, '_replyToEnqueueScripts'));
        add_action('customize_controls_print_footer_scripts', array($this, '_replyToEnqueueStyles'));
        add_action('admin_footer', array($this, '_replyToEnqueueScripts'));
        add_action('admin_footer', array($this, '_replyToEnqueueStyles'));
        add_action('admin_print_footer_scripts', array($this, '_replyToAddStyle'), 999);
        add_action('admin_print_footer_scripts', array($this, '_replyToAddScript'), 999);
        new AmazonAutoLinks_AdminPageFramework_Form_View__Resource__Head($this->oForm, 'admin_head');
    }
    public function _replyToEnqueueScripts() {
        if (!$this->oForm->isInThePage()) {
            return;
        }
        foreach ($this->oForm->getResources('src_scripts') as $_isIndex => $_asEnqueue) {
            $this->_enqueueScript($_asEnqueue);
            $this->oForm->unsetResources(array('src_scripts', $_isIndex));
        }
    }
    static private $_aEnqueued = array();
    private function _enqueueScript($asEnqueue) {
        $_aEnqueueItem = $this->_getFormattedEnqueueScript($asEnqueue);
        if (isset(self::$_aEnqueued[$_aEnqueueItem['src']])) {
            return;
        }
        self::$_aEnqueued[$_aEnqueueItem['src']] = $_aEnqueueItem;
        wp_enqueue_script($_aEnqueueItem['handle_id'], $_aEnqueueItem['src'], $_aEnqueueItem['dependencies'], $_aEnqueueItem['version'], did_action('admin_body_class') ? true : $_aEnqueueItem['in_footer']);
        if ($_aEnqueueItem['translation']) {
            wp_localize_script($_aEnqueueItem['handle_id'], $_aEnqueueItem['handle_id'], $_aEnqueueItem['translation']);
        }
    }
    private function _getFormattedEnqueueScript($asEnqueue) {
        static $_iCallCount = 1;
        $_aEnqueueItem = $this->getAsArray($asEnqueue) + array('handle_id' => 'script_form_' . $this->oForm->aArguments['caller_id'] . '_' . $_iCallCount, 'src' => null, 'dependencies' => null, 'version' => null, 'in_footer' => false, 'translation' => null,);
        if (is_string($asEnqueue)) {
            $_aEnqueueItem['src'] = $asEnqueue;
        }
        $_aEnqueueItem['src'] = $this->getResolvedSRC($_aEnqueueItem['src']);
        $_iCallCount++;
        return $_aEnqueueItem;
    }
    public function _replyToEnqueueStyles() {
        if (!$this->oForm->isInThePage()) {
            return;
        }
        foreach ($this->oForm->getResources('src_styles') as $_isIndex => $_asEnqueueItem) {
            $this->_enqueueStyle($_asEnqueueItem);
            $this->oForm->unsetResources(array('src_styles', $_isIndex));
        }
    }
    private function _enqueueStyle($asEnqueue) {
        $_aEnqueueItem = $this->_getFormattedEnqueueStyle($asEnqueue);
        wp_enqueue_style($_aEnqueueItem['handle_id'], $_aEnqueueItem['src'], $_aEnqueueItem['dependencies'], $_aEnqueueItem['version'], $_aEnqueueItem['media']);
    }
    private function _getFormattedEnqueueStyle($asEnqueue) {
        static $_iCallCount = 1;
        $_aEnqueueItem = $this->getAsArray($asEnqueue) + array('handle_id' => 'style_form_' . $this->oForm->aArguments['caller_id'] . '_' . $_iCallCount, 'src' => null, 'dependencies' => null, 'version' => null, 'media' => null,);
        if (is_string($asEnqueue)) {
            $_aEnqueueItem['src'] = $asEnqueue;
        }
        $_aEnqueueItem['src'] = $this->getResolvedSRC($_aEnqueueItem['src']);
        $_iCallCount++;
        return $_aEnqueueItem;
    }
    public function _replyToAddStyle() {
        if (!$this->oForm->isInThePage()) {
            return;
        }
        $_sCSSRules = $this->_getFormattedInternalStyles($this->oForm->getResources('internal_styles'));
        $_sID = $this->sanitizeSlug(strtolower($this->oForm->aArguments['caller_id']));
        if ($_sCSSRules) {
            echo "<style type='text/css' id='internal-style-{$_sID}' class='amazon-auto-links-form-style'>" . $_sCSSRules . "</style>";
        }
        $_sIECSSRules = $this->_getFormattedInternalStyles($this->oForm->getResources('internal_styles_ie'));
        if ($_sIECSSRules) {
            echo "<!--[if IE]><style type='text/css' id='internal-style-ie-{$_sID}' class='amazon-auto-links-form-ie-style'>" . $_sIECSSRules . "</style><![endif]-->";
        }
        $this->oForm->setResources('internal_styles', array());
        $this->oForm->setResources('internal_styles_ie', array());
    }
    private function _getFormattedInternalStyles(array $aInternalStyles) {
        return implode(PHP_EOL, array_unique($aInternalStyles));
    }
    public function _replyToAddScript() {
        if (!$this->oForm->isInThePage()) {
            return;
        }
        $_sScript = implode(PHP_EOL, array_unique($this->oForm->getResources('internal_scripts')));
        if ($_sScript) {
            $_sID = $this->sanitizeSlug(strtolower($this->oForm->aArguments['caller_id']));
            echo "<script type='text/javascript' id='internal-script-{$_sID}' class='amazon-auto-links-form-script'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
        }
        $this->oForm->setResources('internal_scripts', array());
    }
    }
    