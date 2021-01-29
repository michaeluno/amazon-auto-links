<?php 
/**
	Admin Page Framework v3.8.26b01 by Michael Uno 
	Generated by PHP Class Files Script Generator <https://github.com/michaeluno/PHP-Class-Files-Script-Generator>
	<http://en.michaeluno.jp/amazon-auto-links>
	Copyright (c) 2013-2020, Michael Uno; Licensed under MIT <http://opensource.org/licenses/MIT> */
class AmazonAutoLinks_AdminPageFramework_Model__FormEmailHandler extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oFactory;
    public function __construct($oFactory) {
        $this->oFactory = $oFactory;
        if (!isset($_GET['apf_action'], $_GET['transient'])) {
            return;
        }
        if ('email' !== $_GET['apf_action']) {
            return;
        }
        ignore_user_abort(true);
        $this->registerAction('after_setup_theme', array($this, '_replyToSendFormEmail'));
    }
    static public $_bDoneEmail = false;
    public function _replyToSendFormEmail() {
        if (self::$_bDoneEmail) {
            return;
        }
        self::$_bDoneEmail = true;
        $_sTransient = $this->getElement($_GET, 'transient', '');
        if (!$_sTransient) {
            return;
        }
        $_sTransient = sanitize_text_field($_sTransient);
        $_aFormEmail = $this->getTransient($_sTransient);
        $this->deleteTransient($_sTransient);
        if (!is_array($_aFormEmail)) {
            return;
        }
        $_oEmail = new AmazonAutoLinks_AdminPageFramework_FormEmail($_aFormEmail['email_options'], $_aFormEmail['input'], $_aFormEmail['section_id']);
        $_bSent = $_oEmail->send();
        exit;
    }
    }
    