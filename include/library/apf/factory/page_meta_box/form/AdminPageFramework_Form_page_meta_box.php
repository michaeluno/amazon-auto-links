<?php
/*
 * Admin Page Framework v3.9.1b04 by Michael Uno
 * Compiled with Admin Page Framework Compiler <https://github.com/michaeluno/amazon-auto-links-compiler>
 * <https://en.michaeluno.jp/amazon-auto-links>
 * Copyright (c) 2013-2022, Michael Uno; Licensed under MIT <https://opensource.org/licenses/MIT>
 */

class AmazonAutoLinks_AdminPageFramework_Form_page_meta_box extends AmazonAutoLinks_AdminPageFramework_Form_post_meta_box {
    public $sStructureType = 'page_meta_box';
    public function construct()
    {
        add_filter('options_' . $this->aArguments[ 'caller_id' ], array( $this, '_replyToSanitizeSavedFormData' ), 5);
        parent::construct();
    }
    public function _replyToSanitizeSavedFormData($aSavedFormData)
    {
        return $this->castArrayContents($this->getDataStructureFromAddedFieldsets(), $aSavedFormData);
    }
}
