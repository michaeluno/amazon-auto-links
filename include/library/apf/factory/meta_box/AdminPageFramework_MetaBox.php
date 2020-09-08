<?php 
/**
	Admin Page Framework v3.8.22b04 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Router extends AmazonAutoLinks_AdminPageFramework_Factory {
    public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID = array('post'), $sContext = 'normal', $sPriority = 'default', $sCapability = 'edit_posts', $sTextDomain = 'amazon-auto-links') {
        parent::__construct($this->oProp);
        $this->oProp->sMetaBoxID = $sMetaBoxID ? $this->oUtil->sanitizeSlug($sMetaBoxID) : strtolower($this->oProp->sClassName);
        $this->oProp->sTitle = $sTitle;
        $this->oProp->sContext = $sContext;
        $this->oProp->sPriority = $sPriority;
        if (!$this->oProp->bIsAdmin) {
            return;
        }
        add_action('set_up_' . $this->oProp->sClassName, array($this, '_replyToCallLoadMethods'), 100);
        $this->oUtil->registerAction($this->oProp->bIsAdminAjax ? 'wp_loaded' : 'current_screen', array($this, '_replyToDetermineToLoad'));
    }
    public function _replyToCallLoadMethods() {
        $this->_load();
    }
    protected function _isInThePage() {
        if ($this->_isValidAjaxReferrer()) {
            return true;
        }
        if (!in_array($this->oProp->sPageNow, array('post.php', 'post-new.php'))) {
            return false;
        }
        if (!in_array($this->oUtil->getCurrentPostType(), $this->oProp->aPostTypes)) {
            return false;
        }
        return true;
    }
    protected function _isValidAjaxReferrer() {
        if (!$this->oProp->bIsAdminAjax) {
            return false;
        }
        $_aReferrer = parse_url($this->oProp->sAjaxReferrer) + array('query' => '', 'path' => '');
        parse_str($_aReferrer['query'], $_aQuery);
        $_sBaseName = basename($_aReferrer['path']);
        if (!in_array($_sBaseName, array('post.php', 'post-new.php'))) {
            return false;
        }
        $_iPost = $this->oUtil->getElement($_aQuery, array('post'), 0);
        $_sPostType = $this->oUtil->getElement($_aQuery, array('post_type'), '');
        $_sPostType = $_sPostType ? $_sPostType : get_post_type($_iPost);
        return in_array($_sPostType, $this->oProp->aPostTypes);
    }
    protected function _isInstantiatable() {
        return true;
    }
    }
    abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Model extends AmazonAutoLinks_AdminPageFramework_MetaBox_Router {
        public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID = array('post'), $sContext = 'normal', $sPriority = 'default', $sCapability = 'edit_posts', $sTextDomain = 'amazon-auto-links') {
            add_action('set_up_' . $this->oProp->sClassName, array($this, '_replyToSetUpHooks'));
            add_action('set_up_' . $this->oProp->sClassName, array($this, '_replyToSetUpValidationHooks'));
            parent::__construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain);
        }
        public function _replyToSetUpHooks($oFactory) {
            $this->oUtil->registerAction('add_meta_boxes', array($this, '_replyToRegisterMetaBoxes'));
        }
        public function _replyToSetUpValidationHooks($oScreen) {
            if ('attachment' === $oScreen->post_type && in_array('attachment', $this->oProp->aPostTypes)) {
                add_filter('wp_insert_attachment_data', array($this, '_replyToFilterSavingData'), 10, 2);
            } else {
                add_filter('wp_insert_post_data', array($this, '_replyToFilterSavingData'), 10, 2);
            }
        }
        public function _replyToRegisterMetaBoxes() {
            foreach ($this->oProp->aPostTypes as $_sPostType) {
                add_meta_box($this->oProp->sMetaBoxID, $this->oProp->sTitle, array($this, '_replyToPrintMetaBoxContents'), $_sPostType, $this->oProp->sContext, $this->oProp->sPriority, null);
            }
        }
        public function _replyToGetSavedFormData() {
            $_oMetaData = new AmazonAutoLinks_AdminPageFramework_MetaBox_Model___PostMeta($this->_getPostID(), $this->oForm->aFieldsets);
            $this->oProp->aOptions = $_oMetaData->get();
            return parent::_replyToGetSavedFormData();
        }
        private function _getPostID() {
            if (isset($GLOBALS['post']->ID)) {
                return $GLOBALS['post']->ID;
            }
            if (isset($_GET['post'])) {
                return $_GET['post'];
            }
            if (isset($_POST['post_ID'])) {
                return $_POST['post_ID'];
            }
            return 0;
        }
        public function _replyToFilterSavingData($aPostData, $aUnmodified) {
            if (!$this->_shouldProceedValidation($aUnmodified)) {
                return $aPostData;
            }
            $_aInputs = $this->oForm->getSubmittedData($_POST, true, false);
            $_aInputsRaw = $_aInputs;
            $_iPostID = $aUnmodified['ID'];
            $_aSavedMeta = $this->oUtil->getSavedPostMetaArray($_iPostID, array_keys($_aInputs));
            $_aInputs = $this->oUtil->addAndApplyFilters($this, "validation_{$this->oProp->sClassName}", call_user_func_array(array($this, 'validate'), array($_aInputs, $_aSavedMeta, $this)), $_aSavedMeta, $this);
            if ($this->hasFieldError()) {
                $this->setLastInputs($_aInputsRaw);
                $aPostData['post_status'] = 'pending';
                add_filter('redirect_post_location', array($this, '_replyToModifyRedirectPostLocation'));
            }
            $this->oForm->updateMetaDataByType($_iPostID, $_aInputs, $this->oForm->dropRepeatableElements($_aSavedMeta), $this->oForm->sStructureType);
            return $aPostData;
        }
        public function _replyToModifyRedirectPostLocation($sLocation) {
            remove_filter('redirect_post_location', array($this, __FUNCTION__));
            return add_query_arg(array('message' => 'apf_field_error', 'field_errors' => true), $sLocation);
        }
        private function _shouldProceedValidation(array $aUnmodified) {
            if ('auto-draft' === $aUnmodified['post_status']) {
                return false;
            }
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return false;
            }
            if (!isset($_POST[$this->oProp->sMetaBoxID])) {
                return false;
            }
            if (!wp_verify_nonce($_POST[$this->oProp->sMetaBoxID], $this->oProp->sMetaBoxID)) {
                return false;
            }
            if (!in_array($aUnmodified['post_type'], $this->oProp->aPostTypes)) {
                return false;
            }
            return current_user_can($this->oProp->sCapability, $aUnmodified['ID']);
        }
    }
    abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_View extends AmazonAutoLinks_AdminPageFramework_MetaBox_Model {
        public function _replyToPrintMetaBoxContents($oPost, $vArgs) {
            $_aOutput = array();
            $_aOutput[] = wp_nonce_field($this->oProp->sMetaBoxID, $this->oProp->sMetaBoxID, true, false);
            if (isset($this->oForm)) {
                $_aOutput[] = $this->oForm->get();
            }
            $this->oUtil->addAndDoActions($this, 'do_' . $this->oProp->sClassName, $this);
            echo $this->oUtil->addAndApplyFilters($this, "content_{$this->oProp->sClassName}", $this->content(implode(PHP_EOL, $_aOutput)));
        }
        public function content($sContent) {
            return $sContent;
        }
    }
    abstract class AmazonAutoLinks_AdminPageFramework_MetaBox_Controller extends AmazonAutoLinks_AdminPageFramework_MetaBox_View {
        public function setUp() {
        }
        public function enqueueStyles($aSRCs, $aPostTypes = array(), $aCustomArgs = array()) {
            return $this->oResource->_enqueueStyles($aSRCs, $aPostTypes, $aCustomArgs);
        }
        public function enqueueStyle($sSRC, $aPostTypes = array(), $aCustomArgs = array()) {
            return $this->oResource->_enqueueStyle($sSRC, $aPostTypes, $aCustomArgs);
        }
        public function enqueueScripts($aSRCs, $aPostTypes = array(), $aCustomArgs = array()) {
            return $this->oResource->_enqueueScripts($aSRCs, $aPostTypes, $aCustomArgs);
        }
        public function enqueueScript($sSRC, $aPostTypes = array(), $aCustomArgs = array()) {
            return $this->oResource->_enqueueScript($sSRC, $aPostTypes, $aCustomArgs);
        }
    }
    abstract class AmazonAutoLinks_AdminPageFramework_MetaBox extends AmazonAutoLinks_AdminPageFramework_MetaBox_Controller {
        protected $_sStructureType = 'post_meta_box';
        public function __construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID = array('post'), $sContext = 'normal', $sPriority = 'default', $sCapability = 'edit_posts', $sTextDomain = 'amazon-auto-links') {
            if (!$this->_isInstantiatable()) {
                return;
            }
            if (empty($asPostTypeOrScreenID)) {
                return;
            }
            $_sProprtyClassName = isset($this->aSubClassNames['oProp']) ? $this->aSubClassNames['oProp'] : 'AmazonAutoLinks_AdminPageFramework_Property_' . $this->_sStructureType;
            $this->oProp = new $_sProprtyClassName($this, get_class($this), $sCapability, $sTextDomain, $this->_sStructureType);
            $this->oProp->aPostTypes = is_string($asPostTypeOrScreenID) ? array($asPostTypeOrScreenID) : $asPostTypeOrScreenID;
            parent::__construct($sMetaBoxID, $sTitle, $asPostTypeOrScreenID, $sContext, $sPriority, $sCapability, $sTextDomain);
        }
    }
    