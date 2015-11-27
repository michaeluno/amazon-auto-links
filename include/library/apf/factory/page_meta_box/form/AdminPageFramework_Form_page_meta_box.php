<?php
class AmazonAutoLinks_AdminPageFramework_Form_page_meta_box extends AmazonAutoLinks_AdminPageFramework_Form_post_meta_box {
    public $sStructureType = 'page_meta_box';
    public function construct() {
        add_filter('options_' . $this->aArguments['caller_id'], array($this, '_replyToSanitizeSavedFormData'), 5);
        parent::construct();
    }
    public function _replyToSanitizeSavedFormData($aSavedFormData) {
        return $this->castArrayContents($this->getDataStructureFromAddedFieldsets(), $aSavedFormData);
    }
}