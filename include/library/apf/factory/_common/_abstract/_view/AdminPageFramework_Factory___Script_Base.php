<?php
/*
 * Admin Page Framework v3.9.1b03 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

abstract class AmazonAutoLinks_AdminPageFramework_Factory___Script_Base extends AmazonAutoLinks_AdminPageFramework_FrameworkUtility {
    public $oMsg;
    public function __construct($oMsg=null)
    {
        if ($this->hasBeenCalled(get_class($this))) {
            return;
        }
        $this->oMsg = $oMsg ? $oMsg : AmazonAutoLinks_AdminPageFramework_Message::getInstance();
        $this->registerAction('customize_controls_print_footer_scripts', array( $this, '_replyToPrintScript' ));
        $this->registerAction('admin_print_footer_scripts', array( $this, '_replyToPrintScript' ));
        $this->registerAction('wp_print_footer_scripts', array( $this, '_replyToPrintScript' ));
        $this->construct();
        add_action('wp_enqueue_scripts', array( $this, 'load' ));
    }
    public function construct()
    {}
    public function load()
    {}
    public function _replyToPrintScript()
    {
        $_sScript = $this->getScript($this->oMsg);
        if (! $_sScript) {
            return;
        }
        echo "<script type='text/javascript' class='" . strtolower(get_class($this)) . "'>" . '/* <![CDATA[ */' . $_sScript . '/* ]]> */' . "</script>";
    }
    public static function getScript()
    {
        $_aParams = func_get_args() + array( null );
        $_oMsg = $_aParams[ 0 ];
        return "";
    }
}
